@props(['class' => 'w-5 h-5'])

<svg 
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24" 
    fill="none" 
    xmlns="http://www.w3.org/2000/svg"
>
    <path 
        d="M3 3h2l2.4 12.2a2 2 0 002 1.6h7.8a2 2 0 002-1.6L21 7H7" 
        stroke="currentColor" 
        stroke-width="1.6" 
        stroke-linecap="round" 
        stroke-linejoin="round"
    />

    <circle cx="18"  stroke="currentColor" stroke-width="1.6"/>
    <circle cx="18"   stroke="currentColor" stroke-width="1.6"/>
</svg>