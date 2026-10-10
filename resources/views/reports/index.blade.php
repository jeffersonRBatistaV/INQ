<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Morosidad actual</div>
                    <div class="text-2xl font-bold text-red-700">RD$ {{ number_format($morosidad['total'], 2) }}</div>
                    <div class="text-xs text-gray-400">{{ $morosidad['count'] }} cargos vencidos</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Ocupación</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $occupancy['occupied'] }} / {{ $occupancy['occupied'] + $occupancy['available'] }}</div>
                    <div class="text-xs text-gray-400">{{ $occupancy['available'] }} disponibles</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Cumplimiento promedio</div>
                    <div class="text-2xl font-bold text-green-700">{{ $ranking->avg('rate') ? number_format($ranking->avg('rate'), 1) : 0 }}%</div>
                    <div class="text-xs text-gray-400">Últimos 12 meses</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Ingresos vs esperado / flujo de caja</h3>
                <canvas id="cashflowChart" height="90"></canvas>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Ocupación</h3>
                    <canvas id="occupancyChart" height="160"></canvas>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 font-semibold text-gray-800">Ranking de cumplimiento</div>
                    <div class="overflow-x-auto max-h-80 overflow-y-auto">
                        <table class="w-full rtable text-sm">
                            <thead class="bg-gray-50 text-gray-500 text-left">
                                <tr>
                                    <th class="px-4 py-2">Apartamento</th>
                                    <th class="px-4 py-2 text-right">Cobrado</th>
                                    <th class="px-4 py-2 text-right">%</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($ranking as $row)
                                    <tr>
                                        <td class="px-4 py-2" data-label="Apartamento">{{ $row['apartment'] }}</td>
                                        <td class="px-4 py-2 text-right" data-label="Cobrado">RD$ {{ number_format($row['paid'], 2) }}</td>
                                        <td class="px-4 py-2 text-right font-medium" data-label="%">{{ number_format($row['rate'], 1) }}%</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Sin datos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($labels);
        const series = @json($series);
        const occupancy = @json($occupancy);

        new Chart(document.getElementById('cashflowChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Esperado', data: series.expected, backgroundColor: '#c7d2fe' },
                    { label: 'Cobrado', data: series.collected, backgroundColor: '#4f46e5' },
                    { label: 'Gastos', data: series.expenses, backgroundColor: '#f87171' },
                    { label: 'Flujo neto', type: 'line', data: series.net, borderColor: '#16a34a', backgroundColor: '#16a34a', tension: 0.3 },
                ],
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
        });

        new Chart(document.getElementById('occupancyChart'), {
            type: 'doughnut',
            data: {
                labels: ['Ocupados', 'Disponibles'],
                datasets: [{ data: [occupancy.occupied, occupancy.available], backgroundColor: ['#4f46e5', '#e5e7eb'] }],
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
        });
    </script>
    @endpush
</x-app-layout>
