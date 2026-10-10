<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Charge;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $landlord = $request->user();
        $period = $request->query('period', now()->format('Y-m'));

        $overdue = Charge::query()
            ->with(['apartment', 'tenant'])
            ->where('status', '!=', Charge::STATUS_PAID)
            ->whereDate('due_date', '<', now()->toDateString())
            ->orderBy('due_date')
            ->get();

        $upcoming = Charge::query()
            ->with(['apartment', 'tenant'])
            ->where('status', '!=', Charge::STATUS_PAID)
            ->whereDate('due_date', '>=', now()->toDateString())
            ->orderBy('due_date')
            ->limit(15)
            ->get();

        $stats = [
            'apartments' => Apartment::count(),
            'occupied' => Apartment::where('status', 'occupied')->count(),
            'tenants' => \App\Models\Tenant::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'expected' => (float) Charge::where('period', $period)->sum('amount'),
            'collected' => (float) Payment::whereBetween('paid_at', [
                $period.'-01',
                now()->parse($period.'-01')->endOfMonth()->toDateString(),
            ])->sum('amount'),
            'overdue_total' => round($overdue->sum(fn ($c) => $c->balance()), 2),
        ];

        return view('dashboard', compact('overdue', 'upcoming', 'stats', 'period'));
    }
}
