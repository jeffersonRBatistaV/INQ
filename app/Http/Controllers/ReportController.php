<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Charge;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $months = collect(range(11, 0))
            ->map(fn ($i) => now()->copy()->subMonths($i)->format('Y-m'));

        $expected = Charge::query()
            ->selectRaw('period, SUM(amount) as total')
            ->groupBy('period')
            ->pluck('total', 'period');

        $collected = Payment::query()->get()
            ->groupBy(fn (Payment $p) => $p->paid_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $expenses = Expense::query()->get()
            ->groupBy(fn (Expense $e) => $e->expense_date->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $labels = $months->map(fn ($m) => $m)->values();

        $series = [
            'expected' => $months->map(fn ($m) => round((float) ($expected[$m] ?? 0), 2))->values(),
            'collected' => $months->map(fn ($m) => round((float) ($collected[$m] ?? 0), 2))->values(),
            'expenses' => $months->map(fn ($m) => round((float) ($expenses[$m] ?? 0), 2))->values(),
        ];

        $series['net'] = $series['collected']->map(fn ($v, $i) => round($v - $series['expenses'][$i], 2))->values();

        $overdueCharges = Charge::query()
            ->where('status', '!=', Charge::STATUS_PAID)
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        $morosidad = [
            'count' => $overdueCharges->count(),
            'total' => round($overdueCharges->sum(fn ($c) => $c->balance()), 2),
        ];

        $occupancy = [
            'available' => Apartment::where('status', 'available')->count(),
            'occupied' => Apartment::where('status', 'occupied')->count(),
        ];

        $ranking = Charge::query()
            ->selectRaw('apartment_id, SUM(amount) as charged, SUM(paid_amount) as paid')
            ->groupBy('apartment_id')
            ->with('apartment')
            ->get()
            ->map(function ($row) {
                $charged = (float) $row->charged;

                return [
                    'apartment' => $row->apartment?->displayName() ?? 'N/D',
                    'charged' => round($charged, 2),
                    'paid' => round((float) $row->paid, 2),
                    'rate' => $charged > 0 ? round(((float) $row->paid / $charged) * 100, 1) : 0.0,
                ];
            })
            ->sortByDesc('rate')
            ->values();

        return view('reports.index', compact('labels', 'series', 'morosidad', 'occupancy', 'ranking'));
    }
}
