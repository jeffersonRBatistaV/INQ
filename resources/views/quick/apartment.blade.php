@php($activeLease = $apartment->activeLease)

<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-gray-800 truncate">{{ $apartment->code }}</div>
                <div class="text-sm text-gray-500">{{ $apartment->label ?? 'Sin descripción' }}</div>
                @if ($apartment->building)
                    <button type="button"
                            @click.prevent="$store.quickview.show(@js(route('quick.buildings', $apartment->building)), 'Edificio')"
                            class="mt-1 text-sm text-indigo-600 hover:underline">{{ $apartment->building->name }}</button>
                @endif
                @if ($apartment->address)
                    <div class="text-sm text-gray-500 mt-1">{{ $apartment->address }}</div>
                @endif
            </div>
            <x-status-badge :status="$apartment->status" class="shrink-0" />
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div>
                <div class="text-gray-500">Renta base</div>
                <div class="font-medium">RD$ {{ number_format($apartment->base_rent, 2) }}</div>
            </div>
            <div>
                <div class="text-gray-500">Saldo pendiente</div>
                <div class="font-medium {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($balance, 2) }}</div>
            </div>
            @if ($activeLease)
                <div>
                    <div class="text-gray-500">Inquilino</div>
                    <button type="button"
                            @click.prevent="$store.quickview.show(@js(route('quick.tenants', $activeLease->tenant)), 'Inquilino')"
                            class="font-medium text-indigo-600 hover:underline">{{ $tenant?->name ?? '—' }}</button>
                </div>
                <div>
                    <div class="text-gray-500">Día de cobro</div>
                    <div class="font-medium">{{ $activeLease->billing_day }}</div>
                </div>
            @endif
        </div>

        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm">
            @if ($activeLease)
                <button type="button"
                        @click.prevent="$store.quickview.show(@js(route('quick.leases', $activeLease)), 'Contrato')"
                        class="text-indigo-600 hover:underline">Ver contrato</button>
            @endif
            <a href="{{ route('apartments.edit', $apartment) }}" class="text-indigo-600 hover:underline">Editar</a>
        </div>

        @if ($tenant)
            <div class="mt-4">
                @include('quick.partials.payment-form', ['tenant' => $tenant, 'amount' => $balance > 0 ? $balance : null])
            </div>
        @endif
    </div>

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Cargos recientes</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($charges as $charge)
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
                    <tr><td class="px-4 py-6 text-center text-gray-400">Sin cargos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenant)
        <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Últimos pagos del inquilino</div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="cursor-pointer hover:bg-gray-50"
                            @click.prevent="$store.quickview.show(@js(route('quick.payments', $payment)), 'Pago')">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-800">RD$ {{ number_format($payment->amount, 2) }}</div>
                                <div class="text-xs text-gray-400">{{ $payment->paid_at->format('d/m/Y') }} · {{ $payment->method }}</div>
                            </td>
                            <td class="px-4 py-2 text-right text-xs text-gray-500 whitespace-nowrap">
                                {{ $payment->receipt ? '#'.$payment->receipt->formattedNumber() : 'Sin recibo' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center text-gray-400">Sin pagos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
