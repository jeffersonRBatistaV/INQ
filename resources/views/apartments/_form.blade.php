@php($apartment = $apartment ?? null)
@php($buildingId = old('building_id', request('building_id', $apartment?->building_id)))

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label value="Código" />
        <div class="mt-1 block w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
            {{ $apartment?->code ?? 'Se generará automáticamente' }}
        </div>
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
                <option value="{{ $building->id }}" @selected((string) $buildingId === (string) $building->id)>{{ $building->name }}</option>
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
    <div class="md:col-span-2">
        @if ($apartment)
            @php($activeLease = $apartment->activeLease)
            @if ($activeLease)
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    <span class="font-semibold">Ocupado</span> por {{ $activeLease->tenant?->name ?? '—' }}
                    · Día de cobro: <strong>{{ $activeLease->billing_day }}</strong>
                    · <a href="{{ route('leases.edit', $activeLease) }}" class="underline">Ver contrato</a>
                </div>
            @else
                <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                    Disponible. El estado se marca <strong>ocupado</strong> automáticamente al crear un contrato activo.
                </div>
            @endif
        @else
            <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                El estado se marcará <strong>ocupado</strong> automáticamente cuando asignes un contrato con un inquilino.
            </div>
        @endif
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $apartment?->notes) }}</textarea>
    </div>
</div>
