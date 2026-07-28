<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Rechercher une maison / un bien</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Explorez les offres de logements disponibles avec critères ajustables, photos, vidéos et géolocalisation.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                {{ $properties->total() }} bien(s) trouvé(s)
            </span>
        </div>
    </div>

    <!-- Filters Section -->
    <x-card class="!p-5">
        <div class="space-y-4">
            <!-- Row 1: Search & Main Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search term -->
                <div class="lg:col-span-2">
                    <x-label for="search">Mot-clé / Titre / Quartier</x-label>
                    <x-input wire:model.live.debounce.300ms="search" type="search" id="search" icon="search" placeholder="Ex: Duplex Cocody, Appartement 3 pièces..." />
                </div>

                <!-- Property Type -->
                <div>
                    <x-label for="property_type_id">Type de bien</x-label>
                    <x-select wire:model.live="property_type_id" id="property_type_id" icon="tag">
                        <option value="">Tous les types</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <!-- City -->
                <div>
                    <x-label for="city">Ville</x-label>
                    <x-select wire:model.live="city" id="city" icon="notifications">
                        <option value="">Toutes les villes</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- Row 2: Price & Room Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <x-label for="min_price">Loyer Min (FCFA)</x-label>
                    <x-input wire:model.live.debounce.400ms="min_price" type="number" step="10000" placeholder="50 000" />
                </div>
                <div>
                    <x-label for="max_price">Loyer Max (FCFA)</x-label>
                    <x-input wire:model.live.debounce.400ms="max_price" type="number" step="10000" placeholder="500 000" />
                </div>
                <div>
                    <x-label for="min_bedrooms">Chambres min</x-label>
                    <x-input wire:model.live="min_bedrooms" type="number" min="0" placeholder="1, 2, 3..." />
                </div>
                <div>
                    <x-label for="min_surface">Surface min (m²)</x-label>
                    <x-input wire:model.live.debounce.400ms="min_surface" type="number" min="0" placeholder="50" />
                </div>
                <div class="flex items-end gap-2">
                    <x-button type="button" variant="secondary" wire:click="resetFilters" class="w-full">
                        Réinitialiser
                    </x-button>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($properties as $property)
            <div class="group rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-200 flex flex-col">
                <!-- Cover Photo -->
                <div class="relative aspect-video bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    @php $photoList = $property->photo_list; @endphp
                    <img src="{{ $photoList[0] }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                    <!-- Badges -->
                    <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-bold">
                            {{ $property->propertyType?->name ?? 'Logement' }}
                        </span>
                        @if(count($property->video_list) > 0)
                            <span class="px-2 py-1 rounded-full bg-purple-600/90 backdrop-blur-md text-white text-[10px] font-bold flex items-center gap-1">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span>{{ count($property->video_list) }} vidéo(s)</span>
                            </span>
                        @endif
                    </div>

                    <div class="absolute top-3 right-3">
                        <x-badge :color="$property->status->badgeColor()" class="shadow-sm">
                            {{ $property->status->label() }}
                        </x-badge>
                    </div>

                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white drop-shadow-md">
                        <span class="text-xs font-semibold flex items-center gap-1">
                            <x-icon name="notifications" class="w-3.5 h-3.5 text-emerald-400" />
                            <span>{{ $property->city }} {{ $property->neighborhood ? '• ' . $property->neighborhood : '' }}</span>
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                {{ $property->title }}
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">
                            {{ $property->description ?: 'Aucune description disponible pour le moment.' }}
                        </p>
                    </div>

                    <!-- Specs List -->
                    <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-center text-xs border border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Chambres</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->bedrooms ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Douches</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->bathrooms ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Surface</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->surface_area ? number_format((float)$property->surface_area, 0) . ' m²' : '-' }}</span>
                        </div>
                    </div>

                    <!-- Card Footer: Rent Amount & Action -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Loyer demandé</span>
                            <span class="text-lg font-extrabold font-mono text-emerald-600 dark:text-emerald-400">
                                {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} <span class="text-xs font-normal">FCFA/mois</span>
                            </span>
                        </div>

                        <a href="{{ route('catalog.show', $property->id) }}"
                           class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                            <span>Détails</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                    <x-icon name="building" class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Aucun bien ne correspond à vos critères</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Essayez d'élargir votre recherche en modifiant le loyer, la localisation ou les caractéristiques.</p>
                <x-button type="button" variant="secondary" wire:click="resetFilters">
                    Réinitialiser tous les filtres
                </x-button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4">
        {{ $properties->links() }}
    </div>
</div>
