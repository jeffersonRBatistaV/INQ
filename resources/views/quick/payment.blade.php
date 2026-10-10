@php($methods = ['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'other' => 'Otro'])

<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-gray-800">Pago #{{ $payment->id }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    @if ($payment->tenant)
                        <button type="button"
                                @click.prevent="$store.quickview.show(@js(route('quick.tenants', $payment->tenant)), 'Inquilino')"
                                class="text-indigo-600 hover:underline">{{ $payment->tenant->name }}</button>
                    @endif
                    @if ($payment->lease?->apartment)
                        ·
                        <button type="button"
                                @click.prevent="$store.quickview.show(@js(route('quick.apartments', $payment->lease->apartment)), 'Apartamento')"
                                class="text-indigo-600 hover:underline">{{ $payment->lease->apartment->displayName() }}</button>
                    @endif
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs text-gray-500">Monto</div>
                <div class="text-lg font-bold text-green-700">RD$ {{ number_format($payment->amount, 2) }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div>
                <div class="text-gray-500">Fecha</div>
                <div class="font-medium">{{ $payment->paid_at->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-gray-500">Método</div>
                <div class="font-medium">{{ $methods[$payment->method] ?? $payment->method }}</div>
            </div>
            <div>
                <div class="text-gray-500">Referencia</div>
                <div class="font-medium">{{ $payment->reference ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-500">Recibo</div>
                <div class="font-medium">
                    @if ($payment->receipt)
                        #{{ $payment->receipt->formattedNumber() }}
                    @else
                        Sin recibo
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm">
            @if ($payment->receipt)
                <a href="{{ route('receipts.download', $payment) }}" class="text-gray-700 hover:underline">Descargar recibo PDF</a>
            @endif
            <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:underline">Editar</a>
        </div>

        <div class="mt-4">
            <x-confirm-delete :action="route('payments.destroy', $payment)"
                title="Anular pago"
                message="Se revertirán los cargos aplicados y se eliminará el recibo emitido. Esta acción no se puede deshacer.">Anular pago</x-confirm-delete>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Aplicado a cargos</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($payment->allocations as $allocation)
                    <tr class="cursor-pointer hover:bg-gray-50"
                        @click.prevent="$store.quickview.show(@js(route('quick.charges', $allocation->charge)), 'Cargo')">
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">{{ $allocation->charge?->concept ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $allocation->charge?->period }}</div>
                        </td>
                        <td class="px-4 py-2 text-right font-medium whitespace-nowrap">RD$ {{ number_format($allocation->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-gray-400">Sin cargos aplicados (posible saldo a favor).</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
