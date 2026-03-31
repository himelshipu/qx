@props(['class' => ''])
<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
>
    <rect x="4" y="4" width="12" height="16" rx="2" />
    <path d="M12 12h8" />
    <path d="M17 7l5 5-5 5" />
</svg>

