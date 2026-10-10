<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estado de cuenta — {{ $tenant->name }}</h2>
            <a href="{{ route('payments.create', ['tenant_id' => $tenant->id]) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                Registrar pago
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Saldo pendiente</div>
                    <div class="text-2xl font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($balance, 2) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Teléfono</div>
                    <div class="text-lg font-medium">{{ $tenant->phone ?? '—' }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Correo</div>
                    <div class="text-lg font-medium break-all">{{ $tenant->email ?? '—' }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-100 font-semibold text-gray-800">Cargos</div>
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Periodo</th>
                                <th class="px-4 py-2">Concepto</th>
                                <th class="px-4 py-2">Vence</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                                <th class="px-4 py-2 text-right">Saldo</th>
                                <th class="px-4 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tenant->charges as $charge)
                                <tr>
                                    <td class="px-4 py-2" data-label="Periodo">{{ $charge->period }}</td>
                                    <td class="px-4 py-2" data-label="Concepto">{{ $charge->concept }}</td>
                                    <td class="px-4 py-2" data-label="Vence">{{ $charge->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Monto">RD$ {{ number_format($charge->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right font-medium" data-label="Saldo">RD$ {{ number_format($charge->balance(), 2) }}</td>
                                    <td class="px-4 py-2" data-label="Estado">
                                        @php($colors = ['pending' => 'bg-gray-100 text-gray-600', 'partial' => 'bg-yellow-100 text-yellow-700', 'paid' => 'bg-green-100 text-green-700'])
                                        <span class="px-2 py-1 text-xs rounded-full {{ $colors[$charge->status] ?? 'bg-gray-100' }}">{{ $charge->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin cargos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-100 font-semibold text-gray-800">Pagos</div>
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Fecha</th>
                                <th class="px-4 py-2">Método</th>
                                <th class="px-4 py-2">Referencia</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                                <th class="px-4 py-2 text-right">Recibo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tenant->payments as $payment)
                                <tr>
                                    <td class="px-4 py-2" data-label="Fecha">{{ $payment->paid_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2" data-label="Método">{{ $payment->method }}</td>
                                    <td class="px-4 py-2" data-label="Referencia">{{ $payment->reference ?? '—' }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Monto">RD$ {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Recibo">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin pagos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
