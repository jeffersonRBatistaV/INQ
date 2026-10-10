<?php

namespace App\Services;

use App\Models\Charge;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class PaymentRecorder
{
    /**
     * Register a payment and allocate it to the tenant's open charges (oldest first).
     *
     * @param  array<string, mixed>  $data
     */
    public function record(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create([
                'landlord_id' => $data['landlord_id'] ?? null,
                'tenant_id' => $data['tenant_id'] ?? null,
                'lease_id' => $data['lease_id'] ?? null,
                'amount' => $data['amount'],
                'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->allocate($payment);

            return $payment->fresh(['allocations.charge', 'tenant', 'lease.apartment']);
        });
    }

    /**
     * Update a payment and re-allocate its amount to the tenant's open charges.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $this->reverseAllocations($payment);

            $payment->update([
                'tenant_id' => $data['tenant_id'] ?? $payment->tenant_id,
                'lease_id' => $data['lease_id'] ?? null,
                'amount' => $data['amount'],
                'method' => $data['method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            $this->allocate($payment);

            $payment->receipt?->update(['total' => $payment->amount]);

            return $payment->fresh(['allocations.charge', 'tenant', 'lease.apartment', 'receipt']);
        });
    }

    /**
     * Void a payment: reverse its allocations and delete it (cascades the receipt).
     */
    public function void(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $this->reverseAllocations($payment);

            $payment->delete();
        });
    }

    /**
     * Subtract the payment's allocations back from their charges and remove them.
     */
    public function reverseAllocations(Payment $payment): void
    {
        foreach ($payment->allocations()->with('charge')->get() as $allocation) {
            $charge = $allocation->charge;

            if ($charge) {
                $charge->paid_amount = round(max(0, (float) $charge->paid_amount - (float) $allocation->amount), 2);
                $charge->refreshStatus();
            }

            $allocation->delete();
        }
    }

    public function allocate(Payment $payment): void
    {
        $remaining = round((float) $payment->amount, 2);

        if ($remaining <= 0) {
            return;
        }

        $charges = Charge::query()
            ->when($payment->tenant_id, fn ($q) => $q->where('tenant_id', $payment->tenant_id))
            ->where('status', '!=', Charge::STATUS_PAID)
            ->orderBy('due_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($charges as $charge) {
            if ($remaining <= 0) {
                break;
            }

            $need = $charge->balance();

            if ($need <= 0) {
                continue;
            }

            $apply = min($remaining, $need);

            $charge->paid_amount = round((float) $charge->paid_amount + $apply, 2);
            $charge->refreshStatus();

            $payment->allocations()->create([
                'charge_id' => $charge->id,
                'amount' => $apply,
            ]);

            $remaining = round($remaining - $apply, 2);
        }
    }

    public function tenantBalance(Tenant $tenant): float
    {
        $charges = (float) $tenant->charges()->sum('amount');
        $paid = (float) $tenant->charges()->sum('paid_amount');

        return round($charges - $paid, 2);
    }
}
