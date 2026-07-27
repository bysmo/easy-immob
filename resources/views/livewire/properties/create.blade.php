<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour au catalogue des biens</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Créer un bien immobilier</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Renseignez les caractéristiques du logement ou de l'espace commercial à ajouter.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Caractéristiques Principales -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="building" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Désignation du Bien & Propriétaire</h2>
                    <p class="text-xs text-slate-500">Informations générales de présentation et attribution du bailleur.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <x-label for="title" :required="true">Titre / Intitulé du bien</x-label>
                    <x-input wire:model="title" type="text" id="title" placeholder="Ex: Villa duplex 5 pièces avec piscine et garage" autofocus :error="$errors->first('title')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="owner_id" :required="true">Propriétaire (Bailleur)</x-label>
                        <x-select wire:model="owner_id" id="owner_id" icon="owners" :error="$errors->first('owner_id')">
                            <option value="">— Sélectionner un propriétaire —</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-label for="property_type_id" :required="true">Type de bien</x-label>
                        <x-select wire:model="property_type_id" id="property_type_id" icon="tag" :error="$errors->first('property_type_id')">
                            <option value="">— Sélectionner un type —</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Section 2 : Localisation -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                    <x-icon name="notifications" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Localisation & Adresse</h2>
                    <p class="text-xs text-slate-500">Adresse exacte du bien immobilier.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="city" :required="true">Ville</x-label>
                        <x-input wire:model="city" type="text" id="city" placeholder="Ex: Abidjan" :error="$errors->first('city')" />
                    </div>

                    <div>
                        <x-label for="neighborhood">Quartier / Secteur</x-label>
                        <x-input wire:model="neighborhood" type="text" id="neighborhood" placeholder="Ex: Cocody Riviera 3" :error="$errors->first('neighborhood')" />
                    </div>
                </div>

                <div>
                    <x-label for="address">Adresse précise / Lotissement</x-label>
                    <x-input wire:model="address" type="text" id="address" placeholder="Ex: Rue L84, Ilot 12, Lot 140" :error="$errors->first('address')" />
                </div>
            </div>
        </x-card>

        <!-- Section 3 : Spécifications techniques & Loyer -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="wallet" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Superficie, Pièces & Loyer</h2>
                    <p class="text-xs text-slate-500">Fixez le loyer de référence et les dimensions du logement.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <x-label for="surface_area">Surface habitable (m²)</x-label>
                        <x-input wire:model="surface_area" type="number" step="0.01" id="surface_area" placeholder="150" :error="$errors->first('surface_area')" />
                    </div>

                    <div>
                        <x-label for="bedrooms">Nombre de chambres</x-label>
                        <x-input wire:model="bedrooms" type="number" id="bedrooms" placeholder="3" :error="$errors->first('bedrooms')" />
                    </div>

                    <div>
                        <x-label for="bathrooms">Salles de bain</x-label>
                        <x-input wire:model="bathrooms" type="number" id="bathrooms" placeholder="2" :error="$errors->first('bathrooms')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="rent_amount" :required="true">Loyer mensuel proposé (FCFA)</x-label>
                        <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" placeholder="250000" icon="wallet" :error="$errors->first('rent_amount')" />
                    </div>

                    <div>
                        <x-label for="status" :required="true">Statut d'occupation initial</x-label>
                        <x-select wire:model="status" id="status" :error="$errors->first('status')">
                            @foreach($statusOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div>
                    <x-label for="description">Description complémentaire</x-label>
                    <textarea wire:model="description" id="description" rows="4"
                              class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm shadow-2xs p-3.5 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"
                              placeholder="Indiquez les équipements fournis, balcons, sécurité, charges incluses..."></textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </x-card>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('properties.index') }}">
                <x-button type="button" variant="secondary">Annuler</x-button>
            </a>
            <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-32">
                <span wire:loading.remove class="flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Enregistrer</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Création...</span>
                </span>
            </x-button>
        </div>

    </form>
</div>
