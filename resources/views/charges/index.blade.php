<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cargos</h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6" x-data="chargeForm()">
                <h3 class="font-semibold text-gray-800 mb-4">Generar cargos mensuales</h3>
                <form action="{{ route('charges.generate') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="period" value="Periodo" />
                            <x-text-input id="period" name="period" type="month" class="mt-1 block w-full" value="{{ old('period', $period) }}" required />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="lease_id" value="Contrato (apartamento)" />
                            <select id="lease_id" name="lease_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($activeLeases as $lease)
                                    <option value="{{ $lease->id }}">{{ $lease->apartment?->displayName() }} — {{ $lease->tenant?->name }} (RD$ {{ number_format($lease->rent_amount, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Servicios variables (opcional)</span>
                            <button type="button" @click="addRow()" class="text-sm text-indigo-600 hover:underline">+ Agregar</button>
                        </div>
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="flex gap-2 mb-2">
                                <input type="text" :name="`services[${index}][concept]`" x-model="row.concept" placeholder="Concepto (ej. Agua)" class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="number" step="0.01" min="0" :name="`services[${index}][amount]`" x-model="row.amount" placeholder="Monto" class="w-24 sm:w-32 border-gray-300 rounded-md shadow-sm text-sm">
                                <button type="button" @click="removeRow(index)" class="px-3 text-red-600">✕</button>
                            </div>
                        </template>
                        <p x-show="rows.length === 0" class="text-sm text-gray-400">Sin servicios. Solo se generará la renta.</p>
                    </div>

                    <button class="w-full sm:w-auto sm:float-right px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Generar cargos</button>
                    <div class="clear-both"></div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="font-semibold text-gray-800">Cargos del periodo {{ $period }}</h3>
                    <form method="GET" class="flex items-center gap-2">
                        <input type="month" name="period" value="{{ $period }}" class="border-gray-300 rounded-md shadow-sm text-sm flex-1">
                        <button class="px-3 py-2 bg-gray-800 text-white text-sm rounded-md">Filtrar</button>
                    </form>
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
                                <th class="px-4 py-2 text-right">Saldo</th>
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($charges as $charge)
                                <tr>
                                    <td class="px-4 py-2" data-label="Apartamento">{{ $charge->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Inquilino">{{ $charge->tenant?->name ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Concepto">{{ $charge->concept }}</td>
                                    <td class="px-4 py-2 {{ $charge->isOverdue() ? 'text-red-600' : '' }}" data-label="Vence">{{ $charge->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="Monto">RD$ {{ number_format($charge->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right font-medium" data-label="Saldo">RD$ {{ number_format($charge->balance(), 2) }}</td>
                                    <td class="px-4 py-2" data-label="Estado">
                                        @php($colors = ['pending' => 'bg-gray-100 text-gray-600', 'partial' => 'bg-yellow-100 text-yellow-700', 'paid' => 'bg-green-100 text-green-700'])
                                        <span class="px-2 py-1 text-xs rounded-full {{ $colors[$charge->status] ?? 'bg-gray-100' }}">{{ $charge->status }}</span>
                                    </td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <form action="{{ route('charges.destroy', $charge) }}" method="POST" onsubmit="return confirm('¿Eliminar cargo?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">Sin cargos en este periodo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $charges->links() }}</div>
        </div>
    </div>

    @push('scripts')
    <script>
        function chargeForm() {
            return {
                rows: [],
                addRow() { this.rows.push({ concept: '', amount: '' }); },
                removeRow(index) { this.rows.splice(index, 1); },
            };
        }
    </script>
    @endpush
</x-app-layout>
