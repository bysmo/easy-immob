<div class="max-w-6xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour au catalogue des biens</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $property->title }}</h1>
                <x-badge :color="$property->status->badgeColor()">{{ $property->status->label() }}</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                <x-icon name="notifications" class="w-3.5 h-3.5 text-emerald-500" />
                <span>{{ $property->address }}, {{ $property->neighborhood ? $property->neighborhood . ', ' : '' }}{{ $property->city }}</span>
                <span class="font-mono text-slate-400">• {{ $property->reference }}</span>
            </p>
        </div>

        <!-- Action CTAs -->
        <div class="flex items-center gap-2">
            <x-button type="button" variant="secondary" wire:click="openChatModal">
                <x-icon name="notifications" class="w-4 h-4 mr-1.5 text-emerald-600" />
                <span>Chatter avec l'agence</span>
            </x-button>

            <x-button type="button" variant="primary" wire:click="openDraftLeaseModal">
                <x-icon name="check" class="w-4 h-4 mr-1.5" />
                <span>Conclure un brouillon de bail</span>
            </x-button>
        </div>
    </div>

    <!-- Layout Grid: Main Media & Specs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column Left / Main: Photos & Videos Gallery -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Photos Gallery avec Lightbox & Zoom Plein Écran -->
            <x-card class="!p-4"
                x-data="{
                    lightboxOpen: false,
                    activeIndex: @entangle('activePhotoIndex'),
                    zoomLevel: 1,
                    panX: 0,
                    panY: 0,
                    isDragging: false,
                    startX: 0,
                    startY: 0,
                    photos: {{ json_encode($property->photo_list) }},
                    
                    zoomIn() {
                        if (this.zoomLevel < 4) this.zoomLevel = parseFloat((this.zoomLevel + 0.5).toFixed(1));
                    },
                    zoomOut() {
                        if (this.zoomLevel > 1) {
                            this.zoomLevel = parseFloat((this.zoomLevel - 0.5).toFixed(1));
                            if (this.zoomLevel === 1) { this.panX = 0; this.panY = 0; }
                        }
                    },
                    resetZoom() {
                        this.zoomLevel = 1;
                        this.panX = 0;
                        this.panY = 0;
                    },
                    toggleZoom() {
                        if (this.zoomLevel > 1) this.resetZoom();
                        else this.zoomLevel = 2;
                    },
                    nextPhoto() {
                        this.resetZoom();
                        this.activeIndex = (this.activeIndex + 1) % this.photos.length;
                    },
                    prevPhoto() {
                        this.resetZoom();
                        this.activeIndex = (this.activeIndex - 1 + this.photos.length) % this.photos.length;
                    },
                    openLightbox(idx = null) {
                        if (idx !== null) this.activeIndex = idx;
                        this.resetZoom();
                        this.lightboxOpen = true;
                    },
                    closeLightbox() {
                        this.lightboxOpen = false;
                        this.resetZoom();
                    }
                }"
                @keydown.window.escape="closeLightbox()"
                @keydown.window.arrow-left="if (lightboxOpen) prevPhoto()"
                @keydown.window.arrow-right="if (lightboxOpen) nextPhoto()"
            >
                <div class="space-y-3">
                    <!-- Image Principale avec Survol & Action Zoom -->
                    <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-900 shadow-md group cursor-pointer"
                         @click="openLightbox(activeIndex)">
                        <img :src="photos[activeIndex] || photos[0]" class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                        
                        <!-- Overlay de Contrôles & Agrandissement -->
                        <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-between p-4 pointer-events-none">
                            <!-- Fleche Gauche -->
                            <button type="button" @click.stop="prevPhoto()" class="p-2.5 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white backdrop-blur-md transition pointer-events-auto shadow-lg">
                                <x-icon name="arrow-left" class="w-5 h-5" />
                            </button>
                            
                            <!-- Bouton Agrandir -->
                            <div class="px-4 py-2 rounded-xl bg-slate-900/80 backdrop-blur-md text-white font-medium text-xs flex items-center gap-2 pointer-events-auto shadow-lg hover:bg-emerald-600 transition">
                                <x-icon name="arrows-pointing-out" class="w-4 h-4" />
                                <span>Agrandir / Zoomer</span>
                            </div>
                            
                            <!-- Fleche Droite -->
                            <button type="button" @click.stop="nextPhoto()" class="p-2.5 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white backdrop-blur-md transition pointer-events-auto shadow-lg">
                                <x-icon name="arrow-right" class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Badge Compteur de Photos -->
                        <div class="absolute bottom-3 right-3 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-xs font-mono">
                            Photo <span x-text="activeIndex + 1"></span> / {{ count($property->photo_list) }}
                        </div>
                    </div>

                    <!-- Bandeau de Miniatures -->
                    @if(count($property->photo_list) > 1)
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                            <template x-for="(photo, idx) in photos" :key="idx">
                                <button type="button" @click="activeIndex = idx"
                                        class="w-20 h-14 rounded-lg overflow-hidden border-2 shrink-0 transition"
                                        :class="activeIndex === idx ? 'border-emerald-500 scale-95 ring-2 ring-emerald-500/30' : 'border-transparent opacity-70 hover:opacity-100'">
                                    <img :src="photo" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    @endif
                </div>

                <!-- Modal Lightbox Plein Écran Haute Résolution & Zoom Pan -->
                <template x-teleport="body">
                    <div x-show="lightboxOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col select-none overflow-hidden"
                         style="display: none;">

                        <!-- Toolbar Supérieure Lightbox -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/90 text-white shrink-0">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold truncate max-w-md">{{ $property->title }}</span>
                                <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-mono text-xs">
                                    Photo <span x-text="activeIndex + 1"></span> / <span x-text="photos.length"></span>
                                </span>
                            </div>

                            <!-- Outils de Zoom & Actions -->
                            <div class="flex items-center gap-3">
                                <!-- Pourcentage de Zoom -->
                                <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-emerald-400 font-mono text-xs font-bold"
                                      x-text="Math.round(zoomLevel * 100) + '%'"></span>

                                <!-- Zoom Out -->
                                <button type="button" @click="zoomOut()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition text-sm font-bold flex items-center justify-center" title="Zoom arrière">
                                    -
                                </button>

                                <!-- Reset Zoom -->
                                <button type="button" @click="resetZoom()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition text-xs font-medium" title="Réinitialiser le zoom">
                                    100%
                                </button>

                                <!-- Zoom In -->
                                <button type="button" @click="zoomIn()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition text-sm font-bold flex items-center justify-center" title="Zoom avant">
                                    +
                                </button>

                                <div class="h-5 w-px bg-slate-800 mx-1"></div>

                                <!-- Télécharger Photo -->
                                <a :href="photos[activeIndex]" download target="_blank" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition" title="Télécharger l'image">
                                    <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                </a>

                                <!-- Fermer (Échap) -->
                                <button type="button" @click="closeLightbox()" class="p-2 rounded-lg bg-rose-600/80 hover:bg-rose-600 text-white transition" title="Fermer (Échap)">
                                    <x-icon name="x-mark" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Zone d'affichage de l'image (Support Molette & Pan au glissement) -->
                        <div class="relative flex-1 flex items-center justify-center p-4 overflow-hidden"
                             :class="zoomLevel > 1 ? (isDragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-default'"
                             @wheel.prevent="$event.deltaY < 0 ? zoomIn() : zoomOut()"
                             @mousedown="if (zoomLevel > 1) { isDragging = true; startX = $event.clientX - panX; startY = $event.clientY - panY; }"
                             @mousemove="if (isDragging) { panX = $event.clientX - startX; panY = $event.clientY - startY; }"
                             @mouseup="isDragging = false"
                             @mouseleave="isDragging = false"
                             @dblclick="toggleZoom()">

                            <!-- Navigation Précédent -->
                            <button type="button" @click.stop="prevPhoto()"
                                    class="absolute left-6 z-10 p-3 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white backdrop-blur-md shadow-2xl transition border border-slate-800">
                                <x-icon name="arrow-left" class="w-6 h-6" />
                            </button>

                            <!-- Image Zoomée -->
                            <img :src="photos[activeIndex]"
                                 class="max-w-full max-h-full object-contain transition-transform duration-100 ease-out select-none shadow-2xl rounded-lg"
                                 :style="`transform: scale(${zoomLevel}) translate(${panX / zoomLevel}px, ${panY / zoomLevel}px)`"
                                 draggable="false">

                            <!-- Navigation Suivant -->
                            <button type="button" @click.stop="nextPhoto()"
                                    class="absolute right-6 z-10 p-3 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white backdrop-blur-md shadow-2xl transition border border-slate-800">
                                <x-icon name="arrow-right" class="w-6 h-6" />
                            </button>
                        </div>

                        <!-- Galerie de Miniatures Inférieure -->
                        <div class="px-6 py-3 border-t border-slate-800 bg-slate-900/90 shrink-0 flex items-center justify-center gap-2 overflow-x-auto scrollbar-thin">
                            <template x-for="(photo, idx) in photos" :key="idx">
                                <button type="button" @click="activeIndex = idx; resetZoom();"
                                        class="w-16 h-12 rounded-lg overflow-hidden border-2 shrink-0 transition"
                                        :class="activeIndex === idx ? 'border-emerald-500 scale-105 ring-2 ring-emerald-500/50' : 'border-slate-800 opacity-60 hover:opacity-100'">
                                    <img :src="photo" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </x-card>

            <!-- Video Player Section (3 Videos Max) -->
            @php $videoList = $property->video_list; @endphp
            @if(count($videoList) > 0)
                <x-card class="!p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Vidéos de présentation (Visite virtuelle)</h3>
                                <p class="text-xs text-slate-500">{{ count($videoList) }} vidéo(s) disponible(s) (Maximum 3 autorisées)</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                            {{ count($videoList) }}/3 vidéos
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($videoList as $vIndex => $videoUrl)
                            <div class="space-y-2">
                                <span class="text-xs font-bold text-purple-600 dark:text-purple-400 block">Vidéo #{{ $vIndex + 1 }}</span>
                                <div class="rounded-xl overflow-hidden bg-slate-900 aspect-video shadow-md border border-slate-200 dark:border-slate-800 flex items-center justify-center text-white">
                                    @if(str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be'))
                                        @php
                                            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match);
                                            $youtubeId = $match[1] ?? null;
                                        @endphp
                                        @if($youtubeId)
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}" frameborder="0" allowfullscreen></iframe>
                                        @else
                                            <a href="{{ $videoUrl }}" target="_blank" class="text-xs hover:underline flex items-center gap-2">
                                                <span>Regarder la vidéo sur YouTube</span>
                                            </a>
                                        @endif
                                    @elseif(str_ends_with(strtolower($videoUrl), '.mp4') || str_ends_with(strtolower($videoUrl), '.webm'))
                                        <video controls class="w-full h-full">
                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                            Votre navigateur ne supporte pas le lecteur vidéo.
                                        </video>
                                    @else
                                        <div class="p-6 text-center space-y-2">
                                            <p class="text-xs text-slate-300">Vidéo de présentation externe :</p>
                                            <a href="{{ $videoUrl }}" target="_blank" class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow">
                                                <span>Visionner la vidéo</span>
                                                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            <!-- Property Description -->
            <x-card class="!p-6 space-y-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Description & Équipements</h3>
                <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">
                    {{ $property->description ?: 'Aucune description détaillée renseignée pour ce bien.' }}
                </div>
            </x-card>
        </div>

        <!-- Column Right: Pricing, Specs & Geolocation Card -->
        <div class="space-y-6">

            <!-- Pricing & Key Specs Card -->
            <x-card class="!p-6 space-y-5 border-emerald-500/30">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Loyer Mensuel</span>
                    <div class="text-2xl font-extrabold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA/mois</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-slate-400 block text-[10px]">Type de bien</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->propertyType?->name ?? 'Standard' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-slate-400 block text-[10px]">Surface habitable</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->surface_area ? number_format((float)$property->surface_area, 0) . ' m²' : '-' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-slate-400 block text-[10px]">Chambres</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->bedrooms ?? '-' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="text-slate-400 block text-[10px]">Salles de bain</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $property->bathrooms ?? '-' }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <x-button type="button" variant="primary" wire:click="openDraftLeaseModal" class="w-full justify-center">
                        <x-icon name="check" class="w-4 h-4 mr-1.5" />
                        <span>Conclure un brouillon de bail</span>
                    </x-button>
                    <x-button type="button" variant="secondary" wire:click="openChatModal" class="w-full justify-center">
                        <x-icon name="notifications" class="w-4 h-4 mr-1.5 text-emerald-600" />
                        <span>Chatter avec l'agence</span>
                    </x-button>
                </div>
            </x-card>

            <!-- Geolocation & Location Card -->
            <x-card class="!p-5 space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                        <x-icon name="notifications" class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Géolocalisation du bien</h3>
                        <p class="text-[11px] text-slate-500">Coordonnées cartographiques</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Adresse complète :</span>
                        <p class="font-medium text-slate-800 dark:text-slate-200 mt-0.5">
                            {{ $property->address }}, {{ $property->neighborhood ? $property->neighborhood . ', ' : '' }}{{ $property->city }}
                        </p>
                    </div>

                    @if($property->latitude && $property->longitude)
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 font-mono text-[11px]">
                            <div>
                                <span class="text-slate-400 block text-[9px]">Lat :</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($property->latitude, 6) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px]">Long :</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($property->longitude, 6) }}</span>
                            </div>
                        </div>
                    @endif

                    @if($property->google_maps_url)
                        <a href="{{ $property->google_maps_url }}" target="_blank"
                           class="w-full py-2.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/50 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 font-semibold text-xs flex items-center justify-center gap-1.5 transition">
                            <x-icon name="notifications" class="w-4 h-4" />
                            <span>Ouvrir dans Google Maps</span>
                        </a>
                    @elseif($property->latitude && $property->longitude)
                        <a href="https://maps.google.com/?q={{ $property->latitude }},{{ $property->longitude }}" target="_blank"
                           class="w-full py-2.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/50 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 font-semibold text-xs flex items-center justify-center gap-1.5 transition">
                            <x-icon name="notifications" class="w-4 h-4" />
                            <span>Afficher sur la carte</span>
                        </a>
                    @endif
                </div>
            </x-card>

            <!-- Landlord / Agency Info Card -->
            <x-card class="!p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Agence en charge</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center font-bold">
                        <x-icon name="building" class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $property->agency?->name ?? 'Agence Immobilier' }}</h4>
                        <p class="text-xs text-slate-500">{{ $property->agency?->email ?? 'Gestionnaire agréé' }}</p>
                    </div>
                </div>
            </x-card>

        </div>
    </div>

    <!-- Modal 1: Chatter avec l'agence -->
    @if($showChatModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="notifications" class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Envoyer un message à l'agence</h3>
                    </div>
                    <button type="button" wire:click="$set('showChatModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-xs text-slate-500">Posez vos questions sur le bien <strong>{{ $property->title }}</strong> à l'agence immobilière.</p>
                    <div>
                        <x-label for="initialMessage" :required="true">Votre message</x-label>
                        <textarea wire:model="initialMessage" id="initialMessage" rows="4"
                                  class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs p-3 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                        @error('initialMessage') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-button type="button" variant="secondary" wire:click="$set('showChatModal', false)">Annuler</x-button>
                    <x-button type="button" variant="primary" wire:click="startChat">Démarrer la discussion</x-button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal 2: Conclure un brouillon de contrat de bail -->
    @if($showDraftLeaseModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-xl rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Conclure un brouillon de contrat de bail</h3>
                            <p class="text-xs text-slate-500">Initiation rapide de bail pour le bien : {{ $property->title }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showDraftLeaseModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label for="selected_tenant_id" :required="true">Locataire signataire</x-label>
                        <x-select wire:model="selected_tenant_id" id="selected_tenant_id" icon="tenants">
                            <option value="">— Sélectionner le locataire —</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->reference }}) — {{ $t->phone }}</option>
                            @endforeach
                        </x-select>
                        @error('selected_tenant_id') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="start_date" :required="true">Date de prise d'effet proposée</x-label>
                            <x-input wire:model="start_date" type="date" id="start_date" :error="$errors->first('start_date')" />
                        </div>

                        <div>
                            <x-label for="duration_months" :required="true">Durée du bail (mois)</x-label>
                            <x-input wire:model="duration_months" type="number" min="1" id="duration_months" placeholder="12" :error="$errors->first('duration_months')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="rent_amount" :required="true">Loyer mensuel convenu (FCFA)</x-label>
                            <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" icon="wallet" :error="$errors->first('rent_amount')" />
                        </div>

                        <div>
                            <x-label for="deposit_amount" :required="true">Dépôt de garantie / Caution (FCFA)</x-label>
                            <x-input wire:model="deposit_amount" type="number" step="1000" id="deposit_amount" icon="wallet" :error="$errors->first('deposit_amount')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <x-button type="button" variant="secondary" wire:click="$set('showDraftLeaseModal', false)">Annuler</x-button>
                    <x-button type="button" variant="primary" wire:click="createDraftLease">Générer le brouillon de bail</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
