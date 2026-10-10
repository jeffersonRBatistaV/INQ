<?php

namespace App\Services;

use App\Models\Charge;
use App\Models\Lease;
use App\Models\User;
use Carbon\Carbon;

class ChargeGenerator
{
    /**
     * Generate the recurring charges (rent + optional services) for a lease and period.
     *
     * @param  array<int, array{concept: string, amount: float|int|string}>  $services
     * @return array{rent: bool, services: int, mora: int}
     */
    public function generateForLease(Lease $lease, string $period, array $services = []): array
    {
        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $day = min((int) $lease->billing_day, $start->daysInMonth);
        $dueDate = $start->copy()->day($day);

        $result = ['rent' => false, 'services' => 0, 'mora' => 0];

        $rentExists = Charge::where('lease_id', $lease->id)
            ->where('period', $period)
            ->where('type', Charge::TYPE_RENT)
            ->exists();

        if (! $rentExists) {
            $this->createCharge($lease, $period, [
                'type' => Charge::TYPE_RENT,
                'concept' => 'Renta '.$period,
                'amount' => (float) $lease->rent_amount,
                'due_date' => $dueDate,
            ]);
            $result['rent'] = true;
        }

        foreach ($services as $service) {
            $amount = (float) ($service['amount'] ?? 0);
            $concept = trim((string) ($service['concept'] ?? ''));

            if ($amount <= 0 || $concept === '') {
                continue;
            }

            $this->createCharge($lease, $period, [
                'type' => Charge::TYPE_SERVICE,
                'concept' => $concept,
                'amount' => $amount,
                'due_date' => $dueDate,
            ]);
            $result['services']++;
        }

        $result['mora'] = $this->applyMora($lease, $period, $dueDate);

        return $result;
    }

    /**
     * Create mora charges for overdue rent periods according to the landlord config.
     */
    public function applyMora(Lease $lease, string $currentPeriod, Carbon $currentDueDate): int
    {
        $landlord = $lease->landlord ?? User::find($lease->landlord_id);

        if (! $landlord || $landlord->mora_type === 'none') {
            return 0;
        }

        $created = 0;

        $overdueRent = Charge::where('lease_id', $lease->id)
            ->where('type', Charge::TYPE_RENT)
            ->where('status', '!=', Charge::STATUS_PAID)
            ->where('period', '<', $currentPeriod)
            ->get();

        foreach ($overdueRent as $charge) {
            $balance = $charge->balance();

            if ($balance <= 0) {
                continue;
            }

            $grace = (int) $landlord->grace_days;
            $daysLate = $charge->due_date->copy()->addDays($grace)->diffInDays(now(), false);

            if ($daysLate <= 0) {
                continue;
            }

            $amount = $this->moraAmount($landlord, $balance, $daysLate);

            if ($amount <= 0) {
                continue;
            }

            $concept = 'Mora '.$charge->period;

            $exists = Charge::where('lease_id', $lease->id)
                ->where('period', $currentPeriod)
                ->where('type', Charge::TYPE_MORA)
                ->where('concept', $concept)
                ->exists();

            if ($exists) {
                continue;
            }

            $this->createCharge($lease, $currentPeriod, [
                'type' => Charge::TYPE_MORA,
                'concept' => $concept,
                'amount' => $amount,
                'due_date' => $currentDueDate,
            ]);
            $created++;
        }

        return $created;
    }

    public function moraAmount(User $landlord, float $balance, int $daysLate): float
    {
        return match ($landlord->mora_type) {
            'fixed' => (float) $landlord->mora_value,
            'percent' => round($balance * ((float) $landlord->mora_value / 100), 2),
            'daily' => round((float) $landlord->mora_value * $daysLate, 2),
            default => 0.0,
        };
    }

    private function createCharge(Lease $lease, string $period, array $attributes): Charge
    {
        return Charge::create([
            'landlord_id' => $lease->landlord_id,
            'lease_id' => $lease->id,
            'apartment_id' => $lease->apartment_id,
            'tenant_id' => $lease->tenant_id,
            'period' => $period,
            'paid_amount' => 0,
            'status' => Charge::STATUS_PENDING,
            ...$attributes,
        ]);
    }
}
