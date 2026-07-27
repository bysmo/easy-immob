@props([
    'placeholder' => 'Rechercher...',
    'perPage' => 15,
    'perPageOptions' => [10, 15, 25, 50, 100],
    'search' => '',
])

<div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs mb-4">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Gauche: Recherche et Filtres spécifiques -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
            <!-- Zone de recherche globale -->
            <div class="w-full sm:w-72 md:w-80">
                <x-input wire:model.live.debounce.300ms="search" 
                         type="search" 
                         icon="search" 
                         placeholder="{{ $placeholder }}" />
            </div>

            <!-- Filtres additionnels via le slot -->
            @if(isset($filters))
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    {{ $filters }}
                </div>
            @endif

            @if($search)
                <button wire:click="$set('search', '')" class="text-xs font-semibold text-rose-600 hover:underline flex items-center gap-1 shrink-0">
                    <x-icon name="x" class="w-3.5 h-3.5" />
                    Réinitialiser
                </button>
            @endif
        </div>

        <!-- Droite: Sélecteur du nombre de données à afficher (En haut à droite du tableau) -->
        <div class="flex items-center gap-2 self-end md:self-auto shrink-0">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Afficher :</span>
            <select wire:model.live="perPage" 
                    class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-bold py-1.5 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }} par page</option>
                @endforeach
            </select>
        </div>

    </div>
</div>
