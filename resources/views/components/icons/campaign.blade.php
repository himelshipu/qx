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
    <path d="M3 11h18a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-7l-2 3-2-3H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2z"></path>
    <path d="M6 11V8a6 6 0 0 1 12 0v3"></path>
</svg>