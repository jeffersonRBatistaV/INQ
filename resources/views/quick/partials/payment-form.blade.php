@php($amount = $amount ?? null)
@php($label = $label ?? 'Registrar pago')

<details class="group">
    <summary class="inline-flex w-full justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 cursor-pointer list-none">
        {{ $label }}
    </summary>

    <form action="{{ route('payments.store') }}" method="POST" class="mt-3 grid grid-cols-2 gap-3 rounded-lg border border-gray-200 bg-white p-4">
        @csrf
        <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">

        <div>
            <x-input-label for="qv_amount" value="Monto (RD$)" />
            <x-text-input id="qv_amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount', $amount)" required />
        </div>
        <div>
            <x-input-label for="qv_paid_at" value="Fecha" />
            <x-text-input id="qv_paid_at" name="paid_at" type="date" class="mt-1 block w-full" :value="old('paid_at', now()->toDateString())" required />
        </div>
        <div>
            <x-input-label for="qv_method" value="Método" />
            <select id="qv_method" name="method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="cash" @selected(old('method') === 'cash')>Efectivo</option>
                <option value="transfer" @selected(old('method') === 'transfer')>Transferencia</option>
                <option value="other" @selected(old('method') === 'other')>Otro</option>
            </select>
        </div>
        <div>
            <x-input-label for="qv_reference" value="Referencia" />
            <x-text-input id="qv_reference" name="reference" class="mt-1 block w-full" :value="old('reference')" />
        </div>
        <div class="col-span-2">
            <button class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                Registrar y emitir recibo
            </button>
        </div>
    </form>
</details>
