@props(['color' => 'gray', 'variant' => null])

@php
// `variant` takes precedence over `color` for forward-compat
$effective = $variant ?? $color;

$classes = match ($effective) {
    'green', 'success' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/60 dark:text-primary-200',
    'red', 'danger'    => 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-200',
    'amber', 'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-200',
    'muted'            => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    default            => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
