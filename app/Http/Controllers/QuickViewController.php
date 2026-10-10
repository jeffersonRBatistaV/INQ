<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\Charge;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Tenant;

class QuickViewController extends Controller
{
    public function tenant(Tenant $tenant)
    {
        $tenant->load([
            'leases.apartment',
            'charges' => fn ($q) => $q->orderByDesc('due_date')->orderByDesc('id')->limit(12),
            'payments' => fn ($q) => $q->with('receipt')->orderByDesc('paid_at')->orderByDesc('id')->limit(6),
        ]);

        $balance = $this->balanceFor($tenant->charges());

        return view('quick.tenant', compact('tenant', 'balance'));
    }

    public function apartment(Apartment $apartment)
    {
        $apartment->load(['building', 'activeLease.tenant']);

        $tenant = $apartment->activeLease?->tenant;

        $charges = Charge::where('apartment_id', $apartment->id)
            ->orderByDesc('due_date')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $payments = $tenant
            ? Payment::with('receipt')->where('tenant_id', $tenant->id)
                ->orderByDesc('paid_at')->orderByDesc('id')->limit(6)->get()
            : collect();

        $balance = $this->balanceFor(Charge::where('apartment_id', $apartment->id));

        return view('quick.apartment', compact('apartment', 'tenant', 'charges', 'payments', 'balance'));
    }

    public function building(Building $building)
    {
        $building->load(['apartments' => fn ($q) => $q->orderBy('code'), 'apartments.activeLease.tenant']);

        $apartmentIds = $building->apartments->pluck('id');

        $expected = $apartmentIds->isEmpty()
            ? 0.0
            : (float) Lease::whereIn('apartment_id', $apartmentIds)->where('status', 'active')->sum('rent_amount');

        $balance = $apartmentIds->isEmpty()
            ? 0.0
            : $this->balanceFor(Charge::whereIn('apartment_id', $apartmentIds));

        $occupancy = [
            'total' => $building->apartments->count(),
            'occupied' => $building->apartments->where('status', 'occupied')->count(),
        ];

        return view('quick.building', compact('building', 'expected', 'balance', 'occupancy'));
    }

    public function lease(Lease $lease)
    {
        $lease->load([
            'apartment.building',
            'tenant',
            'charges' => fn ($q) => $q->orderByDesc('due_date')->orderByDesc('id')->limit(12),
        ]);

        $balance = $this->balanceFor($lease->charges());

        return view('quick.lease', compact('lease', 'balance'));
    }

    public function charge(Charge $charge)
    {
        $charge->load(['apartment', 'tenant', 'lease', 'allocations.payment']);

        return view('quick.charge', compact('charge'));
    }

    public function payment(Payment $payment)
    {
        $payment->load(['tenant', 'lease.apartment', 'allocations.charge', 'receipt']);

        return view('quick.payment', compact('payment'));
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Charge>  $query
     */
    private function balanceFor($query): float
    {
        $charges = $query->clone();
        $amount = (float) $charges->sum('amount');
        $paid = (float) $query->clone()->sum('paid_amount');

        return round($amount - $paid, 2);
    }
}
