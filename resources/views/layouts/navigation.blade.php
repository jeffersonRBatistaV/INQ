<nav x-data="{ open: false }" x-effect="document.body.classList.toggle('overflow-hidden', open)" class="bg-white border-b border-gray-100 sticky top-0 z-40 pt-safe">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <button type="button" @click="open = true"
                        aria-label="Abrir menú"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ auth()->user()->isSuperadmin() ? route('admin.dashboard') : route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    <span class="font-semibold text-gray-800 hidden sm:inline">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <span class="hidden sm:block text-sm text-gray-600 truncate max-w-[12rem]">{{ Auth::user()->name }}</span>
                <div class="h-9 w-9 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-semibold">
                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay --}}
    <div x-cloak x-show="open" x-transition.opacity.duration.200ms @click="open = false"
         class="fixed inset-0 bg-gray-900/50 z-40"></div>

    {{-- Drawer --}}
    <aside x-cloak x-show="open"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           @keydown.escape.window="open = false"
           class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85%] bg-white shadow-xl flex flex-col pt-safe pb-safe">
        <div class="flex items-center justify-between px-4 h-16 border-b border-gray-100">
            <div class="min-w-0">
                <div class="font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
            </div>
            <button type="button" @click="open = false" aria-label="Cerrar menú"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-2">
            @if (auth()->user()->isSuperadmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    Panel
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Inicio</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('apartments.index')" :active="request()->routeIs('apartments.*')">Apartamentos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('buildings.index')" :active="request()->routeIs('buildings.*')">Edificios</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">Inquilinos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leases.index')" :active="request()->routeIs('leases.*')">Contratos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('charges.index')" :active="request()->routeIs('charges.*')">Cargos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">Pagos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">Gastos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">Reportes</x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-gray-100 py-2">
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                Perfil y configuración
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        @click.prevent="$el.closest('form').submit()">
                    Cerrar sesión
                </x-responsive-nav-link>
            </form>
        </div>
    </aside>
</nav>
