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
    <circle cx="12" cy="12" r="10"></circle>
    <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
    <line x1="9" y1="9" x2="9.01" y2="9"></line>
    <line x1="15" y1="9" x2="15.01" y2="9"></line>
    <line x1="17" y1="5" x2="7" y2="5"></line>
    <line x1="12" y1="2" x2="12" y2="8"></line>
</svg>