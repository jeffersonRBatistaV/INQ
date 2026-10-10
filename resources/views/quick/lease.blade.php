<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-gray-800">Contrato</div>
                <div class="text-sm text-gray-500 mt-1">
                    <button type="button"
                            @click.prevent="$store.quickview.show(@js(route('quick.apartments', $lease->apartment)), 'Apartamento')"
                            class="text-indigo-600 hover:underline">{{ $lease->apartment?->displayName() ?? '—' }}</button>
                    ·
                    <button type="button"
                            @click.prevent="$store.quickview.show(@js(route('quick.tenants', $lease->tenant)), 'Inquilino')"
                            class="text-indigo-600 hover:underline">{{ $lease->tenant?->name ?? '—' }}</button>
                </div>
            </div>
            <x-status-badge :status="$lease->status" class="shrink-0" />
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div>
                <div class="text-gray-500">Renta</div>
                <div class="font-medium">RD$ {{ number_format($lease->rent_amount, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Depósito</div>
                <div class="font-medium">RD$ {{ number_format($lease->deposit_amount, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Día de cobro</div>
                <div class="font-medium">{{ $lease->billing_day }}</div>
            </div>
            <div>
                <div class="text-gray-500">Saldo pendiente</div>
                <div class="font-medium {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($balance, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Inicio</div>
                <div class="font-medium">{{ $lease->start_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-500">Fin</div>
                <div class="font-medium">{{ $lease->end_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm">
            <a href="{{ route('leases.edit', $lease) }}" class="text-indigo-600 hover:underline">Editar contrato</a>
            <a href="{{ route('charges.index') }}" class="text-gray-700 hover:underline">Generar cargos</a>
        </div>

        @if ($lease->tenant)
            <div class="mt-4">
                @include('quick.partials.payment-form', ['tenant' => $lease->tenant, 'amount' => $balance > 0 ? $balance : null])
            </div>
        @endif
    </div>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Cargos generados</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($lease->charges as $charge)
                    <tr class="cursor-pointer hover:bg-gray-50"
                        @click.prevent="$store.quickview.show(@js(route('quick.charges', $charge)), 'Cargo')">
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">{{ $charge->concept }}</div>
                            <div class="text-xs text-gray-400">{{ $charge->period }} · vence {{ $charge->due_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <div class="font-medium">RD$ {{ number_format($charge->balance(), 2) }}</div>
                            <x-status-badge :status="$charge->status" class="mt-1 inline-block" />
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-6 text-center text-gray-400">Sin cargos generados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
