<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Configuración de mora</h2>
        <p class="mt-1 text-sm text-gray-600">
            Define cómo se calculan los recargos por pagos atrasados. La mora se aplica al generar cargos nuevos.
        </p>
    </header>

    @if (session('status') === 'mora-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="mt-3 text-sm text-green-600">
            Configuración guardada.
        </p>
    @endif

    <form method="patch" action="{{ route('profile.mora') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="mora_type" value="Tipo de mora" />
            <select id="mora_type" name="mora_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="none" @selected(old('mora_type', $user->mora_type) === 'none')>Sin mora</option>
                <option value="fixed" @selected(old('mora_type', $user->mora_type) === 'fixed')>Monto fijo por periodo vencido</option>
                <option value="percent" @selected(old('mora_type', $user->mora_type) === 'percent')>Porcentaje del monto vencido</option>
                <option value="daily" @selected(old('mora_type', $user->mora_type) === 'daily')>Monto fijo por día de atraso</option>
            </select>
            <x-input-error :messages="$errors->get('mora_type')" class="mt-2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="mora_value" value="Valor (RD$ o %)" />
                <x-text-input id="mora_value" name="mora_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('mora_value', $user->mora_value)" />
                <x-input-error :messages="$errors->get('mora_value')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="grace_days" value="Días de gracia" />
                <x-text-input id="grace_days" name="grace_days" type="number" min="0" max="90" class="mt-1 block w-full" :value="old('grace_days', $user->grace_days)" />
                <x-input-error :messages="$errors->get('grace_days')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="currency" value="Moneda" />
            <x-text-input id="currency" name="currency" class="mt-1 block w-full" :value="old('currency', $user->currency)" />
            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>
        </div>
    </form>
</section>
