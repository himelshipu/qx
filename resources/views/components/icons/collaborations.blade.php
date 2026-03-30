@props(['class' => 'w-4 h-4'])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
>
    <path d="M8.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"></path>
    <path d="M15.5 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"></path>
    <path d="M2.5 20a6 6 0 0 1 12 0"></path>
    <path d="M13 20a4.5 4.5 0 0 1 8.5-2"></path>
</svg>
