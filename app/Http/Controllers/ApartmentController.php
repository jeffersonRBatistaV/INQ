<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Building;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::with(['building', 'activeLease.tenant'])->withCount('leases')->orderBy('code')->paginate(20);

        return view('apartments.index', compact('apartments'));
    }

    public function create()
    {
        $buildings = Building::orderBy('name')->get();

        return view('apartments.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Apartment::create($data);

        return redirect()->route('apartments.index')->with('status', 'Apartamento creado.');
    }

    public function edit(Apartment $apartment)
    {
        $buildings = Building::orderBy('name')->get();

        return view('apartments.edit', compact('apartment', 'buildings'));
    }

    public function update(Request $request, Apartment $apartment)
    {
        $data = $this->validated($request, $apartment);

        $apartment->update($data);

        return redirect()->route('apartments.index')->with('status', 'Apartamento actualizado.');
    }

    public function destroy(Apartment $apartment)
    {
        $apartment->delete();

        return redirect()->route('apartments.index')->with('status', 'Apartamento eliminado.');
    }

    private function validated(Request $request, ?Apartment $apartment = null): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:120'],
            'building_id' => ['nullable', 'exists:buildings,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'base_rent' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
