<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Apartamentos</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['apartments'] }}</div>
                    <div class="text-xs text-gray-400">{{ $stats['occupied'] }} ocupados</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Inquilinos</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['tenants'] }}</div>
                    <div class="text-xs text-gray-400">{{ $stats['active_leases'] }} contratos activos</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Cobrado {{ $period }}</div>
                    <div class="text-2xl font-bold text-green-700">RD$ {{ number_format($stats['collected'], 2) }}</div>
                    <div class="text-xs text-gray-400">Esperado: RD$ {{ number_format($stats['expected'], 2) }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">En mora</div>
                    <div class="text-2xl font-bold text-red-700">RD$ {{ number_format($stats['overdue_total'], 2) }}</div>
                    <div class="text-xs text-gray-400">{{ $overdue->count() }} cargos vencidos</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Lista de cobro — vencidos ({{ $overdue->count() }})</h3>
                    <a href="{{ route('charges.index') }}" class="text-sm text-indigo-600 hover:underline">Ver cargos</a>
                </div>
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2">Inquilino</th>
                                <th class="px-4 py-2">Concepto</th>
                                <th class="px-4 py-2">Vence</th>
                                <th class="px-4 py-2 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($overdue as $charge)
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.charges', $charge)), 'Cargo')">
                                    <td class="px-4 py-2" data-label="Apartamento">{{ $charge->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Inquilino">{{ $charge->tenant?->name ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Concepto">{{ $charge->concept }}</td>
                                    <td class="px-4 py-2 text-red-600" data-label="Vence">{{ $charge->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 text-right font-medium" data-label="Saldo">RD$ {{ number_format($charge->balance(), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin cargos vencidos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Próximos vencimientos</h3>
                </div>
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2">Inquilino</th>
                                <th class="px-4 py-2">Concepto</th>
                                <th class="px-4 py-2">Vence</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($upcoming as $charge)
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.charges', $charge)), 'Cargo')">
                                    <td class="px-4 py-2" data-label="Apartamento">{{ $charge->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Inquilino">{{ $charge->tenant?->name ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Concepto">{{ $charge->concept }}</td>
                                    <td class="px-4 py-2" data-label="Vence">{{ $charge->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 text-right font-medium" data-label="Monto">RD$ {{ number_format($charge->balance(), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin vencimientos próximos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Pagos recientes</h3>
                    <a href="{{ route('payments.index') }}" class="text-sm text-indigo-600 hover:underline">Ver todos</a>
                </div>
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Fecha</th>
                                <th class="px-4 py-2">Inquilino</th>
                                <th class="px-4 py-2 mobile-hide">Método</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                                <th class="px-4 py-2">Recibo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentPayments as $payment)
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.payments', $payment)), 'Pago')">
                                    <td class="px-4 py-2" data-label="Fecha">{{ $payment->paid_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2" data-label="Inquilino">{{ $payment->tenant?->name ?? '—' }}</td>
                                    <td class="px-4 py-2 mobile-hide" data-label="Método">
                                        {{ ['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'other' => 'Otro'][$payment->method] ?? $payment->method }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-medium" data-label="Monto">RD$ {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2" data-label="Recibo">{{ $payment->receipt ? '#'.$payment->receipt->formattedNumber() : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin pagos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
