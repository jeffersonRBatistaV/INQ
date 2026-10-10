<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::withCount('apartments')->orderBy('name')->paginate(20);

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        Building::create($this->validated($request));

        return redirect()->route('buildings.index')->with('status', 'Edificio creado.');
    }

    public function edit(Building $building)
    {
        return view('buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $building->update($this->validated($request));

        return redirect()->route('buildings.index')->with('status', 'Edificio actualizado.');
    }

    public function destroy(Building $building)
    {
        if ($building->apartments()->exists()) {
            return redirect()->route('buildings.index')
                ->with('error', 'No se puede eliminar el edificio: tiene apartamentos asociados.');
        }

        $building->delete();

        return redirect()->route('buildings.index')->with('status', 'Edificio eliminado.');
    }

    private function validated(Request $request, ?Building $building = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}