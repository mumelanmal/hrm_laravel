@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold bg-zinc-100 text-indigo-600 dark:bg-zinc-800 dark:text-white'
            : 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-zinc-700 hover:text-indigo-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
