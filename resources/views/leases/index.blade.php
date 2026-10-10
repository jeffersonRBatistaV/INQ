<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contratos</h2>
            <a href="{{ route('leases.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                + Nuevo contrato
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
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2">Inquilino</th>
                                <th class="px-4 py-2">Inicio</th>
                                <th class="px-4 py-2">Renta</th>
                                <th class="px-4 py-2">Día cobro</th>
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($leases as $lease)
                                <tr>
                                    <td class="px-4 py-2 font-medium" data-label="Apartamento">{{ $lease->apartment?->displayName() }}</td>
                                    <td class="px-4 py-2" data-label="Inquilino">{{ $lease->tenant?->name }}</td>
                                    <td class="px-4 py-2" data-label="Inicio">{{ $lease->start_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2" data-label="Renta">RD$ {{ number_format($lease->rent_amount, 2) }}</td>
                                    <td class="px-4 py-2" data-label="Día cobro">{{ $lease->billing_day }}</td>
                                    <td class="px-4 py-2" data-label="Estado">
                                        @if ($lease->status === 'active')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Activo</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Finalizado</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <div class="flex justify-end gap-4">
                                            <a href="{{ route('leases.edit', $lease) }}" class="text-indigo-600 hover:underline">Editar</a>
                                            <form action="{{ route('leases.destroy', $lease) }}" method="POST" onsubmit="return confirm('¿Eliminar contrato?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Sin contratos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $leases->links() }}</div>
        </div>
    </div>
</x-app-layout>
