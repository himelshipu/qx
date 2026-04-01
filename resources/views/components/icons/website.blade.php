@props(['class' => ''])

<svg 
    {{ $attributes->merge(['class' => $class]) }}
    fill="none" 
    stroke="currentColor" 
    viewBox="0 0 24 24"
>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.6 9h16.8M3.6 15h16.8M11.999 3c2.2 2.4 3.5 5.6 3.5 9s-1.3 6.6-3.5 9m0-18c-2.2 2.4-3.5 5.6-3.5 9s1.3 6.6 3.5 9" />
</svg>