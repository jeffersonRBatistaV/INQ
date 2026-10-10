@props(['action', 'title' => 'Confirmar eliminación', 'message' => 'Esta acción no se puede deshacer.'])

<form action="{{ $action }}" method="POST" x-data="{ open: false }" class="inline">
    @csrf
    @method('DELETE')

    <button type="button" @click="open = true" {{ $attributes->merge(['class' => 'text-red-600 hover:underline']) }}>
        {{ $slot }}
    </button>

    <div x-cloak x-show="open" @click.self="open = false" x-transition.opacity
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-gray-900/50 p-4" role="dialog" aria-modal="true" aria-label="{{ $title }}">
        <div x-show="open" @keydown.escape.window="open = false"
             x-transition
             class="w-full max-w-md bg-white rounded-xl shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>

            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button type="button" @click="open = false"
                        class="inline-flex justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</form>