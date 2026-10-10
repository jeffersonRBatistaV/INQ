<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;

class LandlordController extends Controller
{
    public function index()
    {
        $landlords = User::where('role', 'landlord')
            ->withCount(['apartments', 'tenants'])
            ->orderByDesc('created_at')
            ->paginate(25);

        $totals = [
            'landlords' => User::where('role', 'landlord')->count(),
            'apartments' => Apartment::withoutGlobalScopes()->count(),
            'tenants' => Tenant::withoutGlobalScopes()->count(),
            'payments' => (float) Payment::withoutGlobalScopes()->sum('amount'),
        ];

        return view('admin.landlords.index', compact('landlords', 'totals'));
    }

    public function toggle(User $landlord)
    {
        abort_unless($landlord->role === 'landlord', 404);

        $landlord->suspended_at = $landlord->isSuspended() ? null : now();
        $landlord->save();

        return back()->with('status', $landlord->isSuspended()
            ? 'Cuenta suspendida.'
            : 'Cuenta reactivada.');
    }
}
