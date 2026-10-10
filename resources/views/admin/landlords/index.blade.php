<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de superadmin</h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Arrendadores</div>
                    <div class="text-2xl font-bold">{{ $totals['landlords'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Apartamentos</div>
                    <div class="text-2xl font-bold">{{ $totals['apartments'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Inquilinos</div>
                    <div class="text-2xl font-bold">{{ $totals['tenants'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-500">Pagos (RD$)</div>
                    <div class="text-2xl font-bold text-green-700">{{ number_format($totals['payments'], 2) }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg sm:overflow-hidden">
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Arrendador</th>
                                <th class="px-4 py-2">Correo</th>
                                <th class="px-4 py-2">Apartamentos</th>
                                <th class="px-4 py-2">Inquilinos</th>
                                <th class="px-4 py-2">Registrado</th>
                                <th class="px-4 py-2">Estado</th>
                                <th class="px-4 py-2 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($landlords as $landlord)
                                <tr>
                                    <td class="px-4 py-2 font-medium" data-label="Arrendador">{{ $landlord->name }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $landlord->email }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $landlord->apartments_count }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $landlord->tenants_count }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $landlord->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2" data-label="Estado">
                                        @if ($landlord->isSuspended())
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Suspendido</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Activo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <form action="{{ route('admin.landlords.toggle', $landlord) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="text-indigo-600 hover:underline">{{ $landlord->isSuspended() ? 'Reactivar' : 'Suspender' }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Sin arrendadores registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $landlords->links() }}</div>
        </div>
    </div>
</x-app-layout>
