@props(['color' => 'gray'])

@php
$classes = match ($color) {
    'green' => 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-100',
    'red' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-100',
    'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-100',
    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
