@props(['color' => 'gray', 'variant' => null])

@php
$effective = $variant ?? $color;

$classes = match ($effective) {
    'green', 'success', 'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-500/30',
    'red', 'danger', 'inactive', 'arrears' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-950/50 dark:text-rose-300 dark:ring-rose-500/30',
    'amber', 'warning', 'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/50 dark:text-amber-300 dark:ring-amber-500/30',
    'blue', 'info', 'primary' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-950/50 dark:text-sky-300 dark:ring-sky-500/30',
    'indigo', 'purple' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-950/50 dark:text-indigo-300 dark:ring-indigo-500/30',
    'muted' => 'bg-slate-100 text-slate-600 ring-slate-500/10 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
    default => 'bg-slate-100 text-slate-700 ring-slate-500/10 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset transition-all $classes"]) }}>
    {{ $slot }}
</span>
