
@props([
    'variant' => 'light',
    'size' => 'md',
    'color' => 'primary',
    'startIcon' => null,
    'endIcon' => null,
])

@php
    $baseStyles = 'inline-flex items-center px-2.5 py-1 justify-center gap-1 rounded-full font-medium capitalize';

    $sizeStyles = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
    ];

    $variants = [
        'light' => [
            'primary' => 'bg-blue-50 text-blue-500 dark:bg-blue-500/15 dark:text-blue-400',
            'success' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500',
            'error' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500',
            'warning' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400',
            'info' => 'bg-sky-50 text-sky-500 dark:bg-sky-500/15 dark:text-sky-500',
            'light' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80',
            'dark' => 'bg-gray-500 text-white dark:bg-white/5 dark:text-white',
        ],
        'solid' => [
            'primary' => 'bg-blue-500 text-white dark:text-white',
            'success' => 'bg-green-500 text-white dark:text-white',
            'error' => 'bg-red-500 text-white dark:text-white',
            'warning' => 'bg-yellow-500 text-white dark:text-white',
            'info' => 'bg-sky-500 text-white dark:text-white',
            'light' => 'bg-gray-400 dark:bg-white/5 text-white dark:text-white/80 py-4',
            'dark' => 'bg-gray-700 text-white dark:text-white',
        ],
        'custom' => [
            'primary' => 'bg-blue-500 dark:bg-blue-600 text-white dark:text-white shadow-md border border-blue-500/20 dark:border-blue-400/20',
            'success' => 'bg-green-500 dark:bg-green-600 text-white dark:text-white shadow-md border border-green-500/20 dark:border-green-400/20',
            'error' => 'bg-red-500 dark:bg-red-600 text-white dark:text-white shadow-md border border-red-500/20 dark:border-red-400/20',
            'warning' => 'bg-yellow-400 dark:bg-yellow-500 text-gray-900 dark:text-gray-900 shadow-md border border-yellow-400/30 dark:border-yellow-300/30',
            'info' => 'bg-sky-500 dark:bg-sky-600 text-white dark:text-white shadow-md border border-sky-500/20 dark:border-sky-400/20',
            'light' => 'bg-white dark:bg-white/5 text-gray-500 dark:text-white/80 shadow-md border border-gray-200 dark:border-gray-700',
            'dark' => 'bg-gray-700 dark:bg-gray-800 text-white dark:text-white shadow-md border border-gray-700 dark:border-gray-600',
        ],

    ];

    $sizeClass = $sizeStyles[$size] ?? $sizeStyles['md'];
    $colorStyles = $variants[$variant][$color] ?? $variants['light']['primary'];
@endphp

<span class="{{ $baseStyles }} {{ $sizeClass }} {{ $colorStyles }}" {{ $attributes }}>
    @if($startIcon)
        {!! $startIcon !!}
    @endif

    {{ $slot }}

    @if($endIcon)
        {!! $endIcon !!}
    @endif
</span>
