<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Lease;
use App\Models\Tenant;
use Illuminate\Http\Request;

class LeaseController extends Controller
{
    public function index()
    {
        $leases = Lease::with(['apartment', 'tenant'])->orderByDesc('start_date')->paginate(20);

        return view('leases.index', compact('leases'));
    }

    public function create()
    {
        $apartments = Apartment::orderBy('code')->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('leases.create', compact('apartments', 'tenants'));
    }

    public function store(Request $request)
    {
        $lease = Lease::create($this->validated($request));

        $lease->apartment->syncStatus();

        return redirect()->route('leases.index')->with('status', 'Contrato creado.');
    }

    public function edit(Lease $lease)
    {
        $apartments = Apartment::orderBy('code')->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('leases.edit', compact('lease', 'apartments', 'tenants'));
    }

    public function update(Request $request, Lease $lease)
    {
        $previousApartmentId = $lease->apartment_id;

        $lease->update($this->validated($request));

        $lease->load('apartment')->apartment->syncStatus();

        if ($previousApartmentId !== $lease->apartment_id) {
            Apartment::find($previousApartmentId)?->syncStatus();
        }

        return redirect()->route('leases.index')->with('status', 'Contrato actualizado.');
    }

    public function destroy(Lease $lease)
    {
        $apartment = $lease->apartment;

        $lease->delete();

        $apartment?->syncStatus();

        return redirect()->route('leases.index')->with('status', 'Contrato eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'apartment_id' => ['required', 'exists:apartments,id'],
            'tenant_id' => ['required', 'exists:tenants,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', ...($request->filled('start_date') ? ['after_or_equal:start_date'] : [])],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'billing_day' => ['required', 'integer', 'min:1', 'max:31'],
            'status' => ['required', 'in:active,ended'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
