@props(['error' => null])

<input {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm']) }}>
@if ($error)
    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
@endif
