@php($tenant = $tenant ?? null)

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <x-input-label for="name" value="Nombre completo" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $tenant?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="document" value="Cédula / documento" />
        <x-text-input id="document" name="document" class="mt-1 block w-full" :value="old('document', $tenant?->document)" />
    </div>
    <div>
        <x-input-label for="phone" value="Teléfono" />
        <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $tenant?->phone)" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="email" value="Correo electrónico" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $tenant?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="notes" value="Notas" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $tenant?->notes) }}</textarea>
    </div>
</div>
