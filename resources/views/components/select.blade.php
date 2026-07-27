@props(['error' => null, 'icon' => null])

@php
$baseClasses = 'block w-full rounded-xl border text-sm transition-all duration-150 shadow-2xs px-3.5 py-2.5 outline-none disabled:opacity-60 disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed appearance-none pr-10 cursor-pointer';

$stateClasses = $error
    ? 'border-rose-300 dark:border-rose-700 bg-rose-50/30 dark:bg-rose-950/20 text-rose-900 dark:text-rose-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20'
    : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 hover:border-slate-400 dark:hover:border-slate-600';

$paddingClasses = $icon ? 'pl-10' : '';
@endphp

<div class="relative w-full">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
            <x-icon :name="$icon" class="w-4 h-4" />
        </div>
    @endif

    <select {{ $attributes->merge(['class' => "$baseClasses $stateClasses $paddingClasses"]) }}>
        {{ $slot }}
    </select>

    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
        <x-icon name="chevron-down" class="w-4 h-4" />
    </div>

    @if ($error)
        <p class="mt-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 flex items-center gap-1">
            <x-icon name="alert" class="w-3.5 h-3.5" />
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
