@php($methods = ['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'other' => 'Otro'])

<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-gray-800">{{ $charge->concept }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    @if ($charge->apartment)
                        <button type="button"
                                @click.prevent="$store.quickview.show(@js(route('quick.apartments', $charge->apartment)), 'Apartamento')"
                                class="text-indigo-600 hover:underline">{{ $charge->apartment->displayName() }}</button>
                    @endif
                    @if ($charge->tenant)
                        ·
                        <button type="button"
                                @click.prevent="$store.quickview.show(@js(route('quick.tenants', $charge->tenant)), 'Inquilino')"
                                class="text-indigo-600 hover:underline">{{ $charge->tenant->name }}</button>
                    @endif
                </div>
            </div>
            <x-status-badge :status="$charge->status" class="shrink-0" />
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div>
                <div class="text-gray-500">Periodo</div>
                <div class="font-medium">{{ $charge->period }}</div>
            </div>
            <div>
                <div class="text-gray-500">Vence</div>
                <div class="font-medium {{ $charge->isOverdue() ? 'text-red-600' : '' }}">{{ $charge->due_date->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-gray-500">Monto</div>
                <div class="font-medium">RD$ {{ number_format($charge->amount, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Saldo</div>
                <div class="font-medium {{ $charge->balance() > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($charge->balance(), 2) }}</div>
            </div>
        </div>

        @if ($charge->balance() > 0 && $charge->tenant)
            <div class="mt-4">
                @include('quick.partials.payment-form', ['tenant' => $charge->tenant, 'amount' => $charge->balance()])
            </div>
        @endif

        <div class="mt-4">
            <x-confirm-delete :action="route('charges.destroy', $charge)"
                title="Eliminar cargo"
                message="¿Seguro que deseas eliminar el cargo &quot;{{ $charge->concept }}&quot;? Esta acción no se puede deshacer.">Eliminar cargo</x-confirm-delete>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Pagos aplicados</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($charge->allocations as $allocation)
                    <tr class="cursor-pointer hover:bg-gray-50"
                        @click.prevent="$store.quickview.show(@js(route('quick.payments', $allocation->payment)), 'Pago')">
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">RD$ {{ number_format($allocation->amount, 2) }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $allocation->payment?->paid_at?->format('d/m/Y') }}
                                · {{ $methods[$allocation->payment?->method] ?? $allocation->payment?->method }}
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right text-xs text-gray-500 whitespace-nowrap">
                            {{ $allocation->payment?->receipt ? '#'.$allocation->payment->receipt->formattedNumber() : 'Sin recibo' }}
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-gray-400">Sin pagos aplicados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
