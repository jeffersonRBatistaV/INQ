<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 8px; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: bold; color: #4f46e5; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .total { font-size: 15px; font-weight: bold; }
        .box { border: 1px solid #ddd; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">RECIBO DE PAGO</div>
        <div class="muted">{{ $landlord->name }} &mdash; {{ $landlord->email }}</div>
        <div class="muted">Rep&uacute;blica Dominicana &mdash; Moneda: {{ $landlord->currency }}</div>
    </div>

    <table>
        <tr>
            <th style="width: 30%">Recibo No.</th>
            <td>{{ str_pad($receipt->number, 6, '0', STR_PAD_LEFT) }}</td>
            <th style="width: 20%">Fecha</th>
            <td>{{ $receipt->issued_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Inquilino</th>
            <td>{{ $payment->tenant?->name ?? 'N/D' }}</td>
            <th>Apartamento</th>
            <td>{{ $payment->lease?->apartment?->displayName() ?? 'N/D' }}</td>
        </tr>
        <tr>
            <th>M&eacute;todo</th>
            <td>{{ $payment->method }}</td>
            <th>Referencia</th>
            <td>{{ $payment->reference ?? 'N/D' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Concepto</th>
                <th class="right">Monto aplicado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payment->allocations as $allocation)
                <tr>
                    <td>{{ $allocation->charge?->period }}</td>
                    <td>{{ $allocation->charge?->concept }}</td>
                    <td class="right">RD$ {{ number_format($allocation->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Pago acreditado (sin cargos pendientes).</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="box">
        <div class="total right">Total recibido: RD$ {{ number_format($payment->amount, 2) }}</div>
    </div>

    <p class="muted" style="margin-top: 30px;">
        Gracias por su pago. Documento generado electr&oacute;nicamente.
    </p>
</body>
</html>
