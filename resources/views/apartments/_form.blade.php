@php($apartment = $apartment ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="code" value="Código" />
        <x-text-input id="code" name="code" class="mt-1 block w-full" :value="old('code', $apartment?->code)" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="label" value="Nombre / descripción" />
        <x-text-input id="label" name="label" class="mt-1 block w-full" :value="old('label', $apartment?->label)" />
    </div>
    <div>
        <x-input-label for="building_id" value="Edificio" />
        <select id="building_id" name="building_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">— Sin edificio —</option>
            @foreach ($buildings as $building)
                <option value="{{ $building->id }}" @selected(old('building_id', $apartment?->building_id) == $building->id)>{{ $building->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="base_rent" value="Renta base (RD$)" />
        <x-text-input id="base_rent" name="base_rent" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('base_rent', $apartment?->base_rent ?? 0)" required />
        <x-input-error :messages="$errors->get('base_rent')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="address" value="Dirección" />
        <x-text-input id="address" name="address" class="mt-1 block w-full" :value="old('address', $apartment?->address)" />
    </div>
    <div>
        <x-input-label for="status" value="Estado" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="available" @selected(old('status', $apartment?->status) === 'available')>Disponible</option>
            <option value="occupied" @selected(old('status', $apartment?->status) === 'occupied')>Ocupado</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $apartment?->notes) }}</textarea>
    </div>
</div>
