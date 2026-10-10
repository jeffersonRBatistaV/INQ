@php($lease = $lease ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="apartment_id" value="Apartamento" />
        <select id="apartment_id" name="apartment_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach ($apartments as $apartment)
                <option value="{{ $apartment->id }}" @selected(old('apartment_id', $lease?->apartment_id) == $apartment->id)>{{ $apartment->displayName() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('apartment_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="tenant_id" value="Inquilino" />
        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach ($tenants as $tenant)
                <option value="{{ $tenant->id }}" @selected(old('tenant_id', $lease?->tenant_id) == $tenant->id)>{{ $tenant->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="start_date" value="Inicio" />
        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', optional($lease?->start_date)->format('Y-m-d'))" required />
    </div>
    <div>
        <x-input-label for="end_date" value="Fin (opcional)" />
        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', optional($lease?->end_date)->format('Y-m-d'))" />
    </div>
    <div>
        <x-input-label for="rent_amount" value="Renta mensual (RD$)" />
        <x-text-input id="rent_amount" name="rent_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('rent_amount', $lease?->rent_amount ?? 0)" required />
    </div>
    <div>
        <x-input-label for="deposit_amount" value="Depósito (RD$)" />
        <x-text-input id="deposit_amount" name="deposit_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('deposit_amount', $lease?->deposit_amount ?? 0)" />
    </div>
    <div>
        <x-input-label for="billing_day" value="Día de cobro" />
        <x-text-input id="billing_day" name="billing_day" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('billing_day', $lease?->billing_day ?? 1)" required />
    </div>
    <div>
        <x-input-label for="status" value="Estado" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="active" @selected(old('status', $lease?->status) === 'active')>Activo</option>
            <option value="ended" @selected(old('status', $lease?->status) === 'ended')>Finalizado</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $lease?->notes) }}</textarea>
    </div>
</div>
