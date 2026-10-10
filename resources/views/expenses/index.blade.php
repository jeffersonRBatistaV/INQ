<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gastos</h2>
            <a href="{{ route('expenses.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                + Nuevo gasto
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="text-sm text-gray-500">Total de gastos registrados</div>
                <div class="text-2xl font-bold text-red-700">RD$ {{ number_format($total, 2) }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg sm:overflow-hidden">
                <div class="p-4 sm:p-0">
                    <table class="w-full rtable text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-2">Fecha</th>
                                <th class="px-4 py-2">Concepto</th>
                                <th class="px-4 py-2">Categoría</th>
                                <th class="px-4 py-2">Apartamento</th>
                                <th class="px-4 py-2 text-right">Monto</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td class="px-4 py-2" data-label="Fecha">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 font-medium" data-label="Concepto">{{ $expense->concept }}</td>
                                    <td class="px-4 py-2 mobile-hide">{{ $expense->category ?? '—' }}</td>
                                    <td class="px-4 py-2" data-label="Apartamento">{{ $expense->apartment?->displayName() ?? '—' }}</td>
                                    <td class="px-4 py-2 text-right font-medium" data-label="Monto">RD$ {{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-2 sm:text-right" data-label="">
                                        <div class="flex justify-end gap-4">
                                            <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:underline">Editar</a>
                                            <x-confirm-delete :action="route('expenses.destroy', $expense)"
                                                title="Eliminar gasto"
                                                message="¿Seguro que deseas eliminar el gasto &quot;{{ $expense->concept }}&quot;? Esta acción no se puede deshacer.">Eliminar</x-confirm-delete>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin gastos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $expenses->links() }}</div>
        </div>
    </div>
</x-app-layout>
