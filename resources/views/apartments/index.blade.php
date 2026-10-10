<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apartamentos</h2>
            <a href="{{ route('apartments.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                + Nuevo apartamento
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
                                <th class="px-4 py-2">Código</th>
                                <th class="px-4 py-2">Nombre</th>
                                <th class="px-4 py-2">Edificio</th>
                                <th class="px-4 py-2">Renta base</th>
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($apartments as $apartment)
                                <tr>
                                    <td class="px-4 py-2 font-medium" data-label="Código">{{ $apartment->code }}</td>
                                    <td class="px-4 py-2" data-label="Nombre">{{ $apartment->label ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Edificio">{{ $apartment->building?->name ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Renta base">RD$ {{ number_format($apartment->base_rent, 2) }}</td>
                                    <td class="px-4 py-2" data-label="Estado">
                                        @if ($apartment->status === 'occupied')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Ocupado</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Disponible</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <div class="flex justify-end gap-4">
                                            <a href="{{ route('apartments.edit', $apartment) }}" class="text-indigo-600 hover:underline">Editar</a>
                                            <x-confirm-delete :action="route('apartments.destroy', $apartment)"
                                                title="Eliminar apartamento"
                                                message="¿Seguro que deseas eliminar el apartamento {{ $apartment->code }}? Esta acción no se puede deshacer.">Eliminar</x-confirm-delete>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin apartamentos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $apartments->links() }}</div>
        </div>
    </div>
</x-app-layout>
