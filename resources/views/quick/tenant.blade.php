@php($activeLease = $tenant->leases->firstWhere('status', 'active'))

<div class="p-4 space-y-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-semibold text-gray-800 truncate">{{ $tenant->name }}</div>
                <div class="text-sm text-gray-500 space-y-0.5 mt-1">
                    @if ($tenant->phone)<div>{{ $tenant->phone }}</div>@endif
                    @if ($tenant->email)<div class="break-all">{{ $tenant->email }}</div>@endif
                    @if ($tenant->document)<div>{{ $tenant->document }}</div>@endif
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs text-gray-500">Saldo</div>
                <div class="text-lg font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">RD$ {{ number_format($balance, 2) }}</div>
            </div>
        </div>

        <div class="mt-4">
            @include('quick.partials.payment-form', ['tenant' => $tenant])
        </div>

        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm">
            <a href="{{ route('tenants.show', $tenant) }}" class="text-gray-700 hover:underline">Estado de cuenta completo</a>
            <a href="{{ route('tenants.edit', $tenant) }}" class="text-indigo-600 hover:underline">Editar</a>
        </div>
    </div>

    @if ($activeLease)
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <div class="text-sm font-semibold text-gray-700 mb-2">Contrato activo</div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <div class="text-gray-500">Apartamento</div>
                    <button type="button"
                            @click.prevent="$store.quickview.show(@js(route('quick.apartments', $activeLease->apartment)), 'Apartamento')"
                            class="font-medium text-indigo-600 hover:underline text-left">
                        {{ $activeLease->apartment?->displayName() ?? '—' }}
                    </button>
                </div>
                <div>
                    <div class="text-gray-500">Día de cobro</div>
                    <div class="font-medium">{{ $activeLease->billing_day }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Renta</div>
                    <div class="font-medium">RD$ {{ number_format($activeLease->rent_amount, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Desde</div>
                    <div class="font-medium">{{ $activeLease->start_date?->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>
            <button type="button"
                    @click.prevent="$store.quickview.show(@js(route('quick.leases', $activeLease)), 'Contrato')"
                    class="mt-3 text-sm text-indigo-600 hover:underline">Ver contrato</button>
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Cargos recientes</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($tenant->charges as $charge)
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

    <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Últimos pagos</div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @forelse ($tenant->payments as $payment)
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
</div>
