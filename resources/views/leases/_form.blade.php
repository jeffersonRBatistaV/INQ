@php($lease = $lease ?? null)
@php($isCreate = ! $lease)
@php($rents = $apartments->mapWithKeys(fn ($apartment) => [$apartment->id => (float) $apartment->base_rent]))

<div
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="{
        apartmentId: {{ Js::from((string) old('apartment_id', $lease?->apartment_id ?? '')) }},
        rents: {{ Js::from($rents) }},
        rent: {{ Js::from(old('rent_amount', $lease?->rent_amount)) }},
        deposit: {{ Js::from(old('deposit_amount', $lease?->deposit_amount)) }},
        isCreate: {{ $isCreate ? 'true' : 'false' }},
        baseRent() {
            return this.rents[this.apartmentId] ?? 0;
        },
        suggestedDeposit() {
            const value = parseFloat(this.rent);
            return (value > 0 ? value : this.baseRent()) * 2;
        },
        onApartmentChange() {
            if (! this.isCreate) return;
            this.rent = this.baseRent();
            this.deposit = this.baseRent() * 2;
        },
    }"
    x-init="if (isCreate) { if (! rent) rent = baseRent(); if (! deposit) deposit = rent * 2; }"
>
    <div>
        <x-input-label for="apartment_id" value="Apartamento" />
        <select id="apartment_id" name="apartment_id" x-model="apartmentId" x-on:change="onApartmentChange()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
        <x-input-label for="start_date" value="Inicio (opcional)" />
        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', optional($lease?->start_date)->format('Y-m-d'))" />
    </div>
    <div>
        <x-input-label for="end_date" value="Fin (opcional)" />
        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', optional($lease?->end_date)->format('Y-m-d'))" />
    </div>
    <div>
        <x-input-label for="rent_amount" value="Renta mensual (RD$)" />
        <x-text-input id="rent_amount" name="rent_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" x-model="rent" x-bind:placeholder="baseRent()" required />
        <x-input-error :messages="$errors->get('rent_amount')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="deposit_amount" value="Depósito (RD$)" />
        <x-text-input id="deposit_amount" name="deposit_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" x-model="deposit" x-bind:placeholder="suggestedDeposit()" />
        <p class="mt-1 text-xs text-gray-500" x-text="'Sugerencia: 2 meses = RD$ ' + suggestedDeposit().toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></p>
        <x-input-error :messages="$errors->get('deposit_amount')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="billing_day" value="Día de cobro" />
        <x-text-input id="billing_day" name="billing_day" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('billing_day', $lease?->billing_day ?? 1)" required />
        <p class="mt-1 text-xs text-gray-500">Día del mes en que vence el pago del inquilino. Los cargos se generan con esta fecha.</p>
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
