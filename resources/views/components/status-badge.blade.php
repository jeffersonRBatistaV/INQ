@props(['status'])

@php
    $map = [
        'pending' => ['bg-gray-100 text-gray-600', 'Pendiente'],
        'partial' => ['bg-yellow-100 text-yellow-700', 'Parcial'],
        'paid' => ['bg-green-100 text-green-700', 'Pagado'],
        'active' => ['bg-green-100 text-green-700', 'Activo'],
        'ended' => ['bg-gray-100 text-gray-600', 'Finalizado'],
        'available' => ['bg-gray-100 text-gray-600', 'Disponible'],
        'occupied' => ['bg-green-100 text-green-700', 'Ocupado'],
    ];

    [$class, $label] = $map[$status] ?? ['bg-gray-100 text-gray-600', $status];
@endphp

<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs rounded-full '.$class]) }}>{{ $label }}</span>
