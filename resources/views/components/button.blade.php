@props(['variant' => 'primary', 'type' => 'submit'])

@php
$classes = match ($variant) {
    'primary' => 'bg-primary-600 hover:bg-primary-700 text-white',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600',
    default => 'bg-primary-600 hover:bg-primary-700 text-white',
};
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed $classes"]) }}>
    {{ $slot }}
</button>
