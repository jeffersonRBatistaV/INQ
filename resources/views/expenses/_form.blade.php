@php($expense = $expense ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <x-input-label for="concept" value="Concepto" />
        <x-text-input id="concept" name="concept" class="mt-1 block w-full" :value="old('concept', $expense?->concept)" required />
        <x-input-error :messages="$errors->get('concept')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="amount" value="Monto (RD$)" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $expense?->amount ?? 0)" required />
    </div>
    <div>
        <x-input-label for="expense_date" value="Fecha" />
        <x-text-input id="expense_date" name="expense_date" type="date" class="mt-1 block w-full" :value="old('expense_date', optional($expense?->expense_date)->format('Y-m-d') ?? now()->toDateString())" required />
    </div>
    <div>
        <x-input-label for="category" value="Categoría" />
        <x-text-input id="category" name="category" class="mt-1 block w-full" :value="old('category', $expense?->category)" />
    </div>
    <div>
        <x-input-label for="apartment_id" value="Apartamento (opcional)" />
        <select id="apartment_id" name="apartment_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">— General —</option>
            @foreach ($apartments as $apartment)
                <option value="{{ $apartment->id }}" @selected(old('apartment_id', $expense?->apartment_id) == $apartment->id)>{{ $apartment->displayName() }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $expense?->notes) }}</textarea>
    </div>
</div>
