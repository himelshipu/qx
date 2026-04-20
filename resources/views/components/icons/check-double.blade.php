@props(['class' => 'w-4 h-4'])

<svg 
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
>
    <polyline points="18 6 9 17 4 12"></polyline>
    <polyline points="23 6 14 17 9 12"></polyline>
</svg>