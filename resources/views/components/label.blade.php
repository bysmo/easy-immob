@props(['required' => false])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-rose-500 font-bold ml-0.5">*</span>
    @endif
</label>
