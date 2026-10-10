@php($available = $occupancy['total'] - $occupancy['occupied'])

<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="text-lg font-semibold text-gray-800">{{ $building->name }}</div>
        @if ($building->address)
            <div class="text-sm text-gray-500 mt-1">{{ $building->address }}</div>
        @endif
        @if ($building->notes)
            <div class="text-sm text-gray-500 mt-1">{{ $building->notes }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div>
                <div class="text-gray-500">Apartamentos</div>
                <div class="font-medium">{{ $occupancy['total'] }}</div>
            </div>
            <div>
                <div class="text-gray-500">Ocupación</div>
                <div class="font-medium">{{ $occupancy['occupied'] }} ocupados · {{ $available }} libres</div>
            </div>
            <div>
                <div class="text-gray-500">Renta esperada (activos)</div>
                <div class="font-medium">RD$ {{ number_format($expected, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Saldo pendiente</div>
                <div class="font-medium {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($balance, 2) }}</div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm">
            <a href="{{ route('apartments.create', ['building_id' => $building->id]) }}" class="text-gray-700 hover:underline">Agregar apartamento</a>
            <a href="{{ route('buildings.edit', $building) }}" class="text-indigo-600 hover:underline">Editar</a>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Apartamentos</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($building->apartments as $apartment)
                    <tr class="cursor-pointer hover:bg-gray-50"
                        @click.prevent="$store.quickview.show(@js(route('quick.apartments', $apartment)), 'Apartamento')">
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">{{ $apartment->code }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $apartment->activeLease?->tenant?->name ?? 'Sin inquilino' }}
                                @if ($apartment->activeLease) · día {{ $apartment->activeLease->billing_day }}@endif
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <x-status-badge :status="$apartment->status" />
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-gray-400">Sin apartamentos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
