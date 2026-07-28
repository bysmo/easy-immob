<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour au catalogue des biens</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Créer un bien immobilier</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Renseignez les caractéristiques, la géolocalisation et les médias du bien (photos & 3 vidéos max).</p>
        </div>
    </div>

    @if($hasReachedLimit || $errors->has('quota'))
        <div class="p-5 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-rose-800 dark:text-rose-200">
            <div class="flex items-start gap-3">
                <x-icon name="bell" class="w-6 h-6 text-rose-600 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-bold">Quota de biens atteint pour votre agence !</h3>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5">
                        {{ $errors->first('quota') ?: 'Vous avez atteint le nombre maximal de biens autorisés par votre forfait d\'abonnement actuel.' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('subscription.index') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs shrink-0 transition">
                Mettre à niveau mon abonnement &rarr;
            </a>
        </div>
    @endif

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

        <!-- Section 2 : Localisation et Carte -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                    <x-icon name="notifications" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Adresse & Géolocalisation</h2>
                    <p class="text-xs text-slate-500">Adresse précise et coordonnées GPS / Lien carte Google Maps.</p>
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
                        <x-label for="google_maps_url">Lien Google Maps / GPS</x-label>
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
