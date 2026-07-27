@props(['variant' => 'primary', 'type' => 'submit', 'size' => 'md'])

@php
$sizeClasses = match ($size) {
    'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
    'lg' => 'px-5 py-3 text-base rounded-xl gap-2.5',
    default => 'px-4 py-2 text-sm rounded-xl gap-2',
};

$variantClasses = match ($variant) {
    'primary' => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold shadow-xs hover:shadow-sm focus:ring-2 focus:ring-emerald-500/30 focus:outline-none dark:bg-emerald-600 dark:hover:bg-emerald-500',
    'secondary' => 'bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-medium border border-slate-200 shadow-2xs hover:border-slate-300 focus:ring-2 focus:ring-slate-400/20 focus:outline-none dark:bg-slate-800 dark:hover:bg-slate-700/80 dark:text-slate-200 dark:border-slate-700',
    'danger' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-semibold shadow-xs focus:ring-2 focus:ring-rose-500/30 focus:outline-none dark:bg-rose-600 dark:hover:bg-rose-500',
    'ghost' => 'bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium focus:outline-none',
    'outline' => 'bg-transparent hover:bg-emerald-50 dark:hover:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 font-medium border border-emerald-300 dark:border-emerald-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none',
    default => 'bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-xs focus:ring-2 focus:ring-emerald-500/30 focus:outline-none',
};
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "inline-flex items-center justify-center transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none $sizeClasses $variantClasses"]) }}>
    {{ $slot }}
</button>
