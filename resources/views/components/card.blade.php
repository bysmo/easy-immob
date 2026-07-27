@props(['padding' => true])

@php
$paddingClass = $padding ? 'p-6 sm:p-7' : '';
@endphp

<div {{ $attributes->merge(['class' => "bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 shadow-xs hover:shadow-sm transition-all duration-200 $paddingClass"]) }}>
    {{ $slot }}
</div>
