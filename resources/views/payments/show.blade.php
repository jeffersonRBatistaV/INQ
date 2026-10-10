<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pago #{{ $payment->id }}</h2>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                @if ($payment->receipt)
                    <a href="{{ route('receipts.download', $payment) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900">
                        Descargar recibo PDF
                    </a>
                @endif
                <a href="{{ route('payments.edit', $payment) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                    Editar
                </a>
                <x-confirm-delete :action="route('payments.destroy', $payment)" title="Anular pago" message="Se revertirán los cargos aplicados y se eliminará el recibo emitido. Esta acción no se puede deshacer." button-class="w-full sm:w-auto justify-center items-center px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 inline-flex">Eliminar</x-confirm-delete>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4 text-sm">
                <div><div class="text-gray-500">Inquilino</div><div class="font-medium">{{ $payment->tenant?->name ?? '—' }}</div></div>
                <div><div class="text-gray-500">Apartamento</div><div class="font-medium">{{ $payment->lease?->apartment?->displayName() ?? '—' }}</div></div>
                <div><div class="text-gray-500">Fecha</div><div class="font-medium">{{ $payment->paid_at->format('d/m/Y') }}</div></div>
                <div><div class="text-gray-500">Método</div><div class="font-medium">{{ $payment->method }}</div></div>
                <div><div class="text-gray-500">Referencia</div><div class="font-medium">{{ $payment->reference ?? '—' }}</div></div>
                <div><div class="text-gray-500">Monto</div><div class="font-bold text-green-700">RD$ {{ number_format($payment->amount, 2) }}</div></div>
                <div class="col-span-2"><div class="text-gray-500">Recibo</div><div class="font-medium">{{ $payment->receipt ? '#'.$payment->receipt->formattedNumber().' ('.$payment->receipt->issued_at->format('d/m/Y H:i').')' : 'Sin recibo' }}</div></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100 font-semibold text-gray-800">Aplicado a cargos</div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-2">Periodo</th>
                            <th class="px-4 py-2">Concepto</th>
                            <th class="px-4 py-2 text-right">Aplicado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($payment->allocations as $allocation)
                            <tr>
                                <td class="px-4 py-2">{{ $allocation->charge?->period }}</td>
                                <td class="px-4 py-2">{{ $allocation->charge?->concept }}</td>
                                <td class="px-4 py-2 text-right">RD$ {{ number_format($allocation->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Sin cargos aplicados (posible saldo a favor).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
