<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inquilinos</h2>
            <a href="{{ route('tenants.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                + Nuevo inquilino
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
                                <th class="px-4 py-2">Nombre</th>
                                <th class="px-4 py-2">Teléfono</th>
                                <th class="px-4 py-2">Correo</th>
                                <th class="px-4 py-2">Documento</th>
                                <th class="px-4 py-2">Contratos</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tenants as $tenant)
                                <tr>
                                    <td class="px-4 py-2 font-medium" data-label="Nombre">
                                        <button type="button"
                                                @click.prevent="$store.quickview.show(@js(route('quick.tenants', $tenant)), 'Inquilino')"
                                                class="text-gray-800 hover:text-indigo-600 hover:underline text-left">{{ $tenant->name }}</button>
                                    </td>
                                    <td class="px-4 py-2" data-label="Teléfono">{{ $tenant->phone ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Correo">{{ $tenant->email ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Documento">{{ $tenant->document ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Contratos">{{ $tenant->leases_count }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <div class="flex flex-wrap justify-end gap-4">
                                            <a href="{{ route('tenants.show', $tenant) }}" class="text-gray-700 hover:underline">Estado de cuenta</a>
                                            <a href="{{ route('tenants.edit', $tenant) }}" class="text-indigo-600 hover:underline">Editar</a>
                                            <x-confirm-delete :action="route('tenants.destroy', $tenant)"
                                                title="Eliminar inquilino"
                                                message="¿Seguro que deseas eliminar al inquilino {{ $tenant->name }} y sus datos? Esta acción no se puede deshacer.">Eliminar</x-confirm-delete>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin inquilinos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $tenants->links() }}</div>
        </div>
    </div>
</x-app-layout>
