<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estado de cuenta — {{ $tenant->name }}</h2>
            <a href="{{ route('payments.create', ['tenant_id' => $tenant->id]) }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                Registrar pago
            </a>
        </div>
    </x-slot>

    @php($activeLease = $tenant->leases->firstWhere('status', 'active'))

    <div class="py-6 sm:py-8" x-data="{ tab: 'resumen' }">
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

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-100 px-2 sm:px-4">
                    <nav class="flex gap-1 overflow-x-auto" aria-label="Secciones">
                        @php($tabs = [
                            'resumen' => 'Resumen',
                            'cargos' => 'Cargos ('.$tenant->charges->count().')',
                            'pagos' => 'Pagos ('.$tenant->payments->count().')',
                            'contratos' => 'Contratos ('.$tenant->leases->count().')',
                        ])
                        @foreach ($tabs as $key => $label)
                            <button type="button" @click="tab = '{{ $key }}'"
                                    :class="tab === '{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                    class="whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 -mb-px">
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Resumen --}}
                <div x-show="tab === 'resumen'" class="p-4 space-y-4">
                    @if ($activeLease)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-500">Apartamento</div>
                                <button type="button"
                                        @click.prevent="$store.quickview.show(@js(route('quick.apartments', $activeLease->apartment)), 'Apartamento')"
                                        class="font-medium text-indigo-600 hover:underline">{{ $activeLease->apartment?->displayName() ?? '—' }}</button>
                            </div>
                            <div>
                                <div class="text-gray-500">Renta</div>
                                <div class="font-medium">RD$ {{ number_format($activeLease->rent_amount, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Día de cobro</div>
                                <div class="font-medium">{{ $activeLease->billing_day }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Desde</div>
                                <div class="font-medium">{{ $activeLease->start_date?->format('d/m/Y') ?? '—' }}</div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">Sin contrato activo.</p>
                    @endif

                    @if ($tenant->document || $tenant->notes)
                        <div class="text-sm text-gray-600 space-y-1 border-t border-gray-100 pt-4">
                            @if ($tenant->document)<div><span class="text-gray-500">Documento:</span> {{ $tenant->document }}</div>@endif
                            @if ($tenant->notes)<div><span class="text-gray-500">Notas:</span> {{ $tenant->notes }}</div>@endif
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm">
                        <a href="{{ route('tenants.edit', $tenant) }}" class="text-indigo-600 hover:underline">Editar inquilino</a>
                        @if ($activeLease)
                            <a href="{{ route('leases.edit', $activeLease) }}" class="text-indigo-600 hover:underline">Editar contrato</a>
                        @endif
                    </div>
                </div>

                {{-- Cargos --}}
                <div x-show="tab === 'cargos'" x-cloak class="p-4 sm:p-0">
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
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.charges', $charge)), 'Cargo')">
                                    <td class="px-4 py-2" data-label="Periodo">{{ $charge->period }}</td>
                                    <td class="px-4 py-2" data-label="Concepto">{{ $charge->concept }}</td>
                                    <td class="px-4 py-2" data-label="Vence">{{ $charge->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Monto">RD$ {{ number_format($charge->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right font-medium" data-label="Saldo">RD$ {{ number_format($charge->balance(), 2) }}</td>
                                    <td class="px-4 py-2" data-label="Estado"><x-status-badge :status="$charge->status" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin cargos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagos --}}
                <div x-show="tab === 'pagos'" x-cloak class="p-4 sm:p-0">
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
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.payments', $payment)), 'Pago')">
                                    <td class="px-4 py-2" data-label="Fecha">{{ $payment->paid_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2" data-label="Método">{{ ['cash' => 'Efectivo', 'transfer' => 'Transferencia', 'other' => 'Otro'][$payment->method] ?? $payment->method }}</td>
                                    <td class="px-4 py-2" data-label="Referencia">{{ $payment->reference ?? '—' }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Monto">RD$ {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Recibo">{{ $payment->receipt ? '#'.$payment->receipt->formattedNumber() : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin pagos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Contratos --}}
                <div x-show="tab === 'contratos'" x-cloak class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2">Inicio</th>
                                <th class="px-4 py-2 text-right">Renta</th>
                                <th class="px-4 py-2">Día cobro</th>
                                <th class="px-4 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tenant->leases as $lease)
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    @click.prevent="$store.quickview.show(@js(route('quick.leases', $lease)), 'Contrato')">
                                    <td class="px-4 py-2" data-label="Apartamento">{{ $lease->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Inicio">{{ $lease->start_date?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Renta">RD$ {{ number_format($lease->rent_amount, 2) }}</td>
                                    <td class="px-4 py-2" data-label="Día cobro">{{ $lease->billing_day }}</td>
                                    <td class="px-4 py-2" data-label="Estado"><x-status-badge :status="$lease->status" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin contratos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
