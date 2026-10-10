<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar pago #{{ $payment->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('partials.flash')
            <form action="{{ route('payments.update', $payment) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="tenant_id" value="Inquilino" />
                        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" @selected(old('tenant_id', $payment->tenant_id) == $tenant->id)>{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="amount" value="Monto (RD$)" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount', $payment->amount)" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="paid_at" value="Fecha" />
                        <x-text-input id="paid_at" name="paid_at" type="date" class="mt-1 block w-full" :value="old('paid_at', optional($payment->paid_at)->format('Y-m-d'))" required />
                    </div>
                    <div>
                        <x-input-label for="method" value="Método" />
                        <select id="method" name="method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="cash" @selected(old('method', $payment->method) === 'cash')>Efectivo</option>
                            <option value="transfer" @selected(old('method', $payment->method) === 'transfer')>Transferencia</option>
                            <option value="other" @selected(old('method', $payment->method) === 'other')>Otro</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="reference" value="Referencia (opcional)" />
                        <x-text-input id="reference" name="reference" class="mt-1 block w-full" :value="old('reference', $payment->reference)" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="notes" value="Notas" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Al guardar, se revertirá la aplicación actual del pago y se reaplicará al monto nuevo sobre los cargos pendientes más antiguos. El recibo se recalcula con el nuevo total.</p>
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('payments.show', $payment) }}" class="px-4 py-2.5 text-sm text-gray-600 text-center">Cancelar</a>
                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
