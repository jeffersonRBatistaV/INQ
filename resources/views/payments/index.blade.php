<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pagos</h2>
            <a href="{{ route('payments.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                + Registrar pago
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg sm:overflow-hidden">
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Fecha</th>
                                <th class="px-4 py-2">Inquilino</th>
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2">Método</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                                <th class="px-4 py-2">Recibo</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-4 py-2" data-label="Fecha">{{ $payment->paid_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 font-medium" data-label="Inquilino">
                                        @if ($payment->tenant)
                                            <button type="button"
                                                    @click.prevent="$store.quickview.show(@js(route('quick.tenants', $payment->tenant)), 'Inquilino')"
                                                    class="font-medium text-gray-800 hover:text-indigo-600 hover:underline text-left">{{ $payment->tenant->name }}</button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 mobile-hide">{{ $payment->lease?->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $payment->method }}</td>
                                    <td class="px-4 py-2 text-right font-medium" data-label="Monto">RD$ {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2" data-label="Recibo">{{ $payment->receipt ? '#'.$payment->receipt->formattedNumber() : '—' }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <div class="flex flex-col sm:flex-row sm:justify-end gap-2">
                                            <button type="button"
                                                    @click.prevent="$store.quickview.show(@js(route('quick.payments', $payment)), 'Pago')"
                                                    class="text-indigo-600 hover:underline">Ver</button>
                                            <a href="{{ route('payments.edit', $payment) }}" class="text-gray-600 hover:underline">Editar</a>
                                            <x-confirm-delete :action="route('payments.destroy', $payment)" title="Anular pago" message="Se revertirán los cargos aplicados y se eliminará el recibo emitido. Esta acción no se puede deshacer.">Eliminar</x-confirm-delete>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Sin pagos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $payments->links() }}</div>
        </div>
    </div>
</x-app-layout>
