<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['leases'])->orderBy('name')->paginate(20);

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        Tenant::create($this->validated($request));

        return redirect()->route('tenants.index')->with('status', 'Inquilino creado.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['leases.apartment', 'charges' => fn ($q) => $q->orderByDesc('due_date'), 'payments' => fn ($q) => $q->orderByDesc('paid_at')]);

        $balance = round((float) $tenant->charges->sum('amount') - (float) $tenant->charges->sum('paid_amount'), 2);

        return view('tenants.show', compact('tenant', 'balance'));
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($this->validated($request));

        return redirect()->route('tenants.index')->with('status', 'Inquilino actualizado.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('tenants.index')->with('status', 'Inquilino eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'document' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
