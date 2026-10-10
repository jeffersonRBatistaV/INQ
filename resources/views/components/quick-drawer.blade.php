<div x-data
     x-effect="document.body.classList.toggle('overflow-hidden', $store.quickview.open)"
     @keydown.escape.window="$store.quickview.close()"
     role="dialog" aria-modal="true" aria-label="Detalle">

    {{-- Overlay --}}
    <div x-cloak x-show="$store.quickview.open"
         x-transition.opacity.duration.200ms
         @click="$store.quickview.close()"
         class="fixed inset-0 bg-gray-900/50 z-50"></div>

    {{-- Panel --}}
    <aside x-cloak x-show="$store.quickview.open"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed inset-y-0 right-0 z-[60] w-full sm:max-w-md bg-gray-50 shadow-xl flex flex-col pt-safe pb-safe">
        <div class="flex items-center justify-between gap-3 px-4 h-16 border-b border-gray-100 bg-white shrink-0">
            <div class="font-semibold text-gray-800 truncate" x-text="$store.quickview.title || 'Detalle'"></div>
            <button type="button" @click="$store.quickview.close()" aria-label="Cerrar"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto safe-bottom">
            <template x-if="$store.quickview.loading">
                <div class="p-6 space-y-3 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded"></div>
                    <div class="h-32 bg-gray-200 rounded"></div>
                </div>
            </template>

            <div x-show="! $store.quickview.loading" x-html="$store.quickview.content"></div>
        </div>
    </aside>
</div>
