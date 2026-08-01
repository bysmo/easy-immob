<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour au catalogue des biens</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Modifier le bien immobilier</h1>
                <x-badge color="indigo" class="font-mono text-xs">{{ $property->reference }}</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Mettre à jour les informations, la géolocalisation et les médias de ce bien.</p>
        </div>
    </div>

    <!-- Banner Suivi d'entretien réel -->
    <div class="p-4 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-md">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Coût d'entretien réel cumulé sur ce bien</span>
            <p class="text-2xl font-bold font-mono text-emerald-400 mt-0.5">
                {{ number_format((float) $property->total_maintenance_cost, 0, ',', ' ') }} FCFA
            </p>
        </div>
        <a href="{{ route('incidents.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5 self-start sm:self-auto">
            <x-icon name="bell" class="w-4 h-4 text-emerald-400" />
            <span>Voir l'historique des réparations</span>
        </a>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Caractéristiques Principales -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="building" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Désignation du Bien & Bailleur</h2>
                    <p class="text-xs text-slate-500">Informations générales de présentation et attribution du bailleur.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <x-label for="title" :required="true">Titre / Intitulé du bien</x-label>
                    <x-input wire:model="title" type="text" id="title" autofocus :error="$errors->first('title')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <x-label for="owner_id" :required="true">Bailleur</x-label>
                        <x-select wire:model.live="owner_id" id="owner_id" icon="owners" :error="$errors->first('owner_id')">
                            <option value="">— Sélectionner un bailleur —</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-label for="management_contract_id">Mandat de Gestion</x-label>
                        <x-select wire:model="management_contract_id" id="management_contract_id" :error="$errors->first('management_contract_id')">
                            <option value="">— Aucun ou sélectionner un mandat —</option>
                            @foreach($managementContracts as $contract)
                                <option value="{{ $contract->id }}">{{ $contract->reference }} (Prise d'effet: {{ $contract->start_date?->format('d/m/Y') }})</option>
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

        <!-- Section 2 : Localisation & Géolocalisation -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                    <x-icon name="notifications" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Adresse & Géolocalisation</h2>
                    <p class="text-xs text-slate-500">Adresse exacte, coordonnées GPS et lien Google Maps du bien.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="city" :required="true">Ville</x-label>
                        <x-input wire:model="city" type="text" id="city" :error="$errors->first('city')" />
                    </div>

                    <div>
                        <x-label for="neighborhood">Quartier / Secteur</x-label>
                        <x-input wire:model="neighborhood" type="text" id="neighborhood" :error="$errors->first('neighborhood')" />
                    </div>
                </div>

                <div>
                    <x-label for="address">Adresse précise / Lotissement</x-label>
                    <x-input wire:model="address" type="text" id="address" :error="$errors->first('address')" />
                </div>

                <!-- GPS & Maps -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-2">
                    <div>
                        <x-label for="latitude">Latitude GPS</x-label>
                        <x-input wire:model="latitude" type="number" step="0.00000001" id="latitude" placeholder="5.3599517" :error="$errors->first('latitude')" />
                    </div>
                    <div>
                        <x-label for="longitude">Longitude GPS</x-label>
                        <x-input wire:model="longitude" type="number" step="0.00000001" id="longitude" placeholder="-4.0082563" :error="$errors->first('longitude')" />
                    </div>
                    <div>
                        <x-label for="google_maps_url">Lien Google Maps</x-label>
                        <x-input wire:model="google_maps_url" type="url" id="google_maps_url" placeholder="https://maps.google.com/..." :error="$errors->first('google_maps_url')" />
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Section 3 : Médias du Bien (Photos 10 max & Vidéos 3 max) -->
        <x-card>
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                        <x-icon name="reports" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Photos & Vidéos du bien</h2>
                        <p class="text-xs text-slate-500">Chargement binaire (fichiers) ou lien URL. (Max 10 photos, Max 3 vidéos).</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold">
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        Photos : {{ count($photos) }}/10 max
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                        Vidéos : {{ count($videos) }}/3 max
                    </span>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Section Photos -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <x-label class="mb-0 font-bold">Photos de présentation (Max 10 photos)</x-label>
                        <span class="text-xs text-slate-500">Fichier binaire ou Lien URL</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                        <!-- Option 1: Upload binaire -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Charger une image (Fichier local)</label>
                            <div class="flex items-center gap-2">
                                <input type="file" wire:model="photo_file" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300" :disabled="count($photos) >= 10">
                                <x-button type="button" variant="secondary" wire:click="uploadPhotoFile" :disabled="count($photos) >= 10" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="uploadPhotoFile">Charger</span>
                                    <span wire:loading wire:target="uploadPhotoFile">...</span>
                                </x-button>
                            </div>
                            @error('photo_file') <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Option 2: Lien URL -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Ajouter par URL externe</label>
                            <div class="flex items-center gap-2">
                                <x-input wire:model="new_photo_url" placeholder="https://example.com/photo.jpg" class="flex-1 !py-1.5" :disabled="count($photos) >= 10" />
                                <x-button type="button" variant="secondary" wire:click="addPhotoUrl" :disabled="count($photos) >= 10">
                                    Ajouter
                                </x-button>
                            </div>
                            @error('new_photo_url') <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @error('photos') <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                    <!-- Photos Grid Preview -->
                    @if(count($photos) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4">
                            @foreach($photos as $index => $photo)
                                <div class="relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 aspect-video">
                                    <img src="{{ $photo }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-slate-900/80 text-white text-[9px] font-mono">#{{ $index + 1 }}</span>
                                    <button type="button" wire:click="removePhoto({{ $index }})"
                                            class="absolute top-1 right-1 p-1 bg-rose-600 text-white rounded-full opacity-90 hover:opacity-100 transition shadow">
                                        <x-icon name="x-mark" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Section Vidéos (3 max) -->
                <div class="pt-5 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <x-label class="mb-0 font-bold">Vidéos de présentation (Max 3 vidéos)</x-label>
                        <span class="text-xs text-slate-500">Fichier binaire (MP4, WEBM...) ou URL YouTube/Vimeo</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                        <!-- Option 1: Upload binaire vidéo -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Charger une vidéo (Fichier local)</label>
                            <div class="flex items-center gap-2">
                                <input type="file" wire:model="video_file" accept="video/*" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-950 dark:file:text-purple-300" :disabled="count($videos) >= 3">
                                <x-button type="button" variant="secondary" wire:click="uploadVideoFile" :disabled="count($videos) >= 3" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="uploadVideoFile">Charger</span>
                                    <span wire:loading wire:target="uploadVideoFile">...</span>
                                </x-button>
                            </div>
                            @error('video_file') <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <!-- Option 2: Lien URL vidéo -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Ajouter par URL (YouTube, Vimeo...)</label>
                            <div class="flex items-center gap-2">
                                <x-input wire:model="new_video_url" placeholder="https://www.youtube.com/watch?v=..." class="flex-1 !py-1.5" :disabled="count($videos) >= 3" />
                                <x-button type="button" variant="secondary" wire:click="addVideoUrl" :disabled="count($videos) >= 3">
                                    Ajouter
                                </x-button>
                            </div>
                            @error('new_video_url') <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @error('videos') <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror

                    <!-- Vidéos List Preview -->
                    @if(count($videos) > 0)
                        <div class="space-y-2 mt-4">
                            @foreach($videos as $index => $video)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="font-bold text-purple-600 dark:text-purple-400">Vidéo #{{ $index + 1 }}</span>
                                        <span class="truncate text-slate-600 dark:text-slate-400">{{ $video }}</span>
                                    </div>
                                    <button type="button" wire:click="removeVideo({{ $index }})" class="text-rose-600 hover:text-rose-700 font-medium ml-2">
                                        Supprimer
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Section 4 : Spécifications techniques & Loyer -->
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
                        <x-input wire:model="surface_area" type="number" step="0.01" id="surface_area" :error="$errors->first('surface_area')" />
                    </div>

                    <div>
                        <x-label for="bedrooms">Nombre de chambres</x-label>
                        <x-input wire:model="bedrooms" type="number" id="bedrooms" :error="$errors->first('bedrooms')" />
                    </div>

                    <div>
                        <x-label for="bathrooms">Salles de bain</x-label>
                        <x-input wire:model="bathrooms" type="number" id="bathrooms" :error="$errors->first('bathrooms')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <x-label for="rent_amount" :required="true" class="mb-0">Loyer mensuel proposé (FCFA)</x-label>
                            <button type="button" wire:click="openIncreaseModal" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                <x-icon name="arrow-up" class="w-3 h-3" />
                                <span>Augmenter / Réviser</span>
                            </button>
                        </div>
                        <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" icon="wallet" :error="$errors->first('rent_amount')" />
                    </div>

                    <div>
                        <x-label for="status" :required="true">Statut d'occupation</x-label>
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
                              class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm shadow-2xs p-3.5 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                    @error('description') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </x-card>

        <!-- Section 4.5 : Fiscalité IRF, Commission Agence & Récapitulatif Financier -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <x-icon name="document" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Fiscalité (IRF) & Commission Agence</h2>
                    <p class="text-xs text-slate-500">Paramétrez l'IRF (Burkina Faso) et la part de commission spécifique à ce bien.</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Grille d'options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Option IRF -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-center">
                        <label class="relative flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="is_subject_to_irf" class="mt-1 w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Soumis à l'IRF (Impôt sur le Revenu Foncier - BF)</span>
                                <span class="text-[11px] text-slate-500 block">Applique le barème par tranches (18% jusqu'à 100 000 FCFA + 25% au-delà).</span>
                            </div>
                        </label>
                    </div>

                    <!-- Option Commission Agence -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3">
                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Mode de commission de l'agence :</span>
                        <div class="flex items-center gap-4 text-xs font-medium">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="agency_fee_type" value="percentage" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Pourcentage (%)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="agency_fee_type" value="fixed" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Forfait fixe (FCFA)</span>
                            </label>
                        </div>

                        <div>
                            <x-label for="agency_fee_value">
                                @if($agency_fee_type === 'percentage')
                                    Taux de commission spécifique (%) <span class="normal-case font-normal text-slate-400">(Laissez vide pour utiliser le taux agence de {{ auth()->user()->agency?->commission_rate ?? 10 }}%)</span>
                                @else
                                    Montant du forfait mensuel (FCFA)
                                @endif
                            </x-label>
                            <x-input wire:model.live="agency_fee_value" type="number" step="{{ $agency_fee_type === 'percentage' ? '0.1' : '1000' }}" id="agency_fee_value" placeholder="{{ $agency_fee_type === 'percentage' ? '10.0' : '25000' }}" />
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif Financier Prévisionnel -->
                @php
                    $computedRent = (float) ($rent_amount ?? 0);
                    $computedIrf  = 0.0;
                    if ($is_subject_to_irf && $computedRent > 0) {
                        $computedIrf = $computedRent <= 100000
                            ? round($computedRent * 0.18, 2)
                            : round((100000 * 0.18) + (($computedRent - 100000) * 0.25), 2);
                    }
                    $computedFee = 0.0;
                    if ($agency_fee_type === 'fixed') {
                        $computedFee = (float) ($agency_fee_value ?? 0);
                    } else {
                        $rate = $agency_fee_value !== null ? (float) $agency_fee_value : (float) (auth()->user()->agency?->commission_rate ?? 10.0);
                        $computedFee = round(($computedRent * $rate) / 100, 2);
                    }
                    $computedNet = max(0, round($computedRent - $computedIrf - $computedFee, 2));
                @endphp

                <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white space-y-4 shadow-md">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400">Récapitulatif Financier Prévisionnel</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                        <div class="p-3 rounded-xl bg-white/10 border border-white/10">
                            <span class="text-slate-300 block text-[11px]">Loyer Mensuel Total (HC)</span>
                            <span class="font-mono font-bold text-sm text-white block mt-0.5">{{ number_format($computedRent, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <div class="p-3 rounded-xl bg-white/10 border border-white/10">
                            <span class="text-slate-300 block text-[11px]">
                                Impôt IRF (BF) {{ $is_subject_to_irf ? '(Appliqué)' : '(Exonéré)' }}
                            </span>
                            <span class="font-mono font-bold text-sm {{ $is_subject_to_irf ? 'text-amber-300' : 'text-slate-400' }} block mt-0.5">
                                {{ number_format($computedIrf, 0, ',', ' ') }} FCFA
                            </span>
                        </div>

                        <div class="p-3 rounded-xl bg-white/10 border border-white/10">
                            <span class="text-slate-300 block text-[11px]">Commission Agence</span>
                            <span class="font-mono font-bold text-sm text-sky-300 block mt-0.5">{{ number_format($computedFee, 0, ',', ' ') }} FCFA</span>
                        </div>

                        <div class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30">
                            <span class="text-emerald-200 block text-[11px] font-bold">Revenu Net Bailleur</span>
                            <span class="font-mono font-extrabold text-base text-emerald-300 block mt-0.5">{{ number_format($computedNet, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Section 5 : Historique des révisions et augmentations de loyer -->
        <x-card>
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                        <x-icon name="chart-bar" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Historique des Montants de Loyer</h2>
                        <p class="text-xs text-slate-500">Traçabilité des augmentations et révisions antérieures avec motifs.</p>
                    </div>
                </div>
                <button type="button" wire:click="openIncreaseModal" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition flex items-center gap-1.5">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Nouvelle révision</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Date d'effet</th>
                            <th class="px-4 py-3">Ancien loyer</th>
                            <th class="px-4 py-3">Nouveau loyer</th>
                            <th class="px-4 py-3">Écart</th>
                            <th class="px-4 py-3">Raison / Motif</th>
                            <th class="px-4 py-3">Auteur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($property->rentHistories as $history)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="px-4 py-3 font-mono font-medium text-slate-700 dark:text-slate-300">
                                    {{ $history->effective_date?->format('d/m/Y') ?? $history->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ number_format((float)$history->old_rent_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ number_format((float)$history->new_rent_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold {{ $history->change_amount >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600' }}">
                                    {{ $history->change_amount >= 0 ? '+' : '' }}{{ number_format((float)$history->change_amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                    {{ $history->reason }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $history->user?->name ?? 'Système' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                    Aucune révision enregistrée pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                    <span>Mettre à jour</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Sauvegarde...</span>
                </span>
            </x-button>
        </div>

    </form>

    <!-- Modal d'Augmentation / Révision de Loyer -->
    @if($showIncreaseModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="wallet" class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Révision du loyer du bien</h3>
                    </div>
                    <button type="button" wire:click="closeIncreaseModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 text-xs flex justify-between items-center">
                        <span class="text-slate-500">Loyer actuel :</span>
                        <span class="font-bold font-mono text-slate-900 dark:text-white text-sm">
                            {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} FCFA
                        </span>
                    </div>

                    <div>
                        <x-label for="new_rent_amount" :required="true">Nouveau montant du loyer (FCFA)</x-label>
                        <x-input wire:model="new_rent_amount" type="number" step="1000" id="new_rent_amount" icon="wallet" :error="$errors->first('new_rent_amount')" />
                    </div>

                    <div>
                        <x-label for="effective_date" :required="true">Date d'effet de l'augmentation</x-label>
                        <x-input wire:model="effective_date" type="date" id="effective_date" :error="$errors->first('effective_date')" />
                    </div>

                    <div>
                        <x-label for="increase_reason" :required="true">Raison / Motif de l'augmentation</x-label>
                        <textarea wire:model="increase_reason" id="increase_reason" rows="3" placeholder="Ex: Révision annuelle légale, travaux de rénovation, etc."
                                  class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs shadow-2xs p-3 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                        @error('increase_reason') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input wire:model="update_active_lease" type="checkbox" id="update_active_lease" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <x-label for="update_active_lease" class="mb-0 text-xs font-medium cursor-pointer">
                            Appliquer également au contrat de bail actif s'il existe et notifier le locataire
                        </x-label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="closeIncreaseModal" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="button" wire:click="increaseRent" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition flex items-center gap-1.5">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Enregistrer l'augmentation</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
