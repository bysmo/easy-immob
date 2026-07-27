@props([
    'field' => '',
    'sortField' => '',
    'sortDirection' => 'asc',
    'align' => 'left',
])

@php
    $isSorted = $sortField === $field;
    $alignClass = match($align) {
        'center' => 'text-center justify-center',
        'right'  => 'text-right justify-end',
        default  => 'text-left justify-start',
    };
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 select-none']) }}>
    @if($field)
        <button type="button" 
                wire:click="sortBy('{{ $field }}')" 
                class="inline-flex items-center gap-1.5 font-bold hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors w-full {{ $alignClass }}">
            <span>{{ $slot }}</span>
            <span class="inline-flex flex-col text-[9px] leading-none shrink-0">
                @if($isSorted)
                    @if($sortDirection === 'asc')
                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">▲</span>
                    @else
                        <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">▼</span>
                    @endif
                @else
                    <span class="text-slate-300 dark:text-slate-600 opacity-60">▲▼</span>
                @endif
            </span>
        </button>
    @else
        <div class="{{ $alignClass }}">
            {{ $slot }}
        </div>
    @endif
</th>
