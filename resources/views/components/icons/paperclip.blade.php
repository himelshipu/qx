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
    <path d="M13.5 3H12H8C6.9 3 6 3.9 6 5v14c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V8.5L13.5 3z"></path>
    <path d="M12 3v5h5"></path>
</svg>