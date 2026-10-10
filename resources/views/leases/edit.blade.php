<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar contrato</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('partials.flash')
            <form action="{{ route('leases.update', $lease) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf @method('PUT')
                @include('leases._form')
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('leases.index') }}" class="px-4 py-2.5 text-sm text-gray-600 text-center">Cancelar</a>
                    <button class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
