<div class="max-w-4xl mx-auto space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Incident {{ $incident->reference }}</h1>
                <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $incident->status->badgeClass() }}">
                    {{ $incident->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Signalé le {{ $incident->created_at->format('d/m/Y à H:i') }} par {{ $incident->tenant?->full_name }}</p>
        </div>

        <a href="{{ route('incidents.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold transition flex items-center gap-1.5 self-start sm:self-auto">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Retour aux incidents</span>
        </a>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Incident Details (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Details Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $incident->title }}</h2>
                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $incident->description }}
                    </p>
                </div>

                <!-- Note vocale Audio -->
                @if ($incident->audio_path)
                    <div class="p-4 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 space-y-2">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-semibold text-xs">
                            <x-icon name="bell" class="w-4 h-4" />
                            <span>Note vocale audio transmise par le locataire</span>
                        </div>
                        <audio controls class="w-full mt-2">
                            <source src="{{ asset('storage/' . $incident->audio_path) }}">
                            Votre navigateur ne supporte pas la lecture audio.
                        </audio>
                    </div>
                @endif

                <!-- Photos transmises par le locataire -->
                @if ($incident->photos && count($incident->photos) > 0)
                    <div class="space-y-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Photos de l'incident</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($incident->photos as $photo)
                                <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="group relative rounded-xl overflow-hidden aspect-square border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800">
                                    <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Vidéos transmises -->
                @if ($incident->videos && count($incident->videos) > 0)
                    <div class="space-y-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Vidéos transmises</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($incident->videos as $video)
                                <video controls class="w-full rounded-xl border border-slate-200 dark:border-slate-800">
                                    <source src="{{ asset('storage/' . $video) }}">
                                </video>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Travaux / Résolution effectués par l'Agence -->
            @if ($incident->repair_details)
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-indigo-200/80 dark:border-indigo-900/60 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider">
                            Rapport d'intervention de l'agence
                        </h3>
                        @if ($incident->resolved_at)
                            <span class="text-xs text-slate-500">Traité le {{ $incident->resolved_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $incident->repair_details }}
                    </p>

                    <!-- Coût de la réparation (VISIBLE UNIQUEMENT PAR L'AGENCE) -->
                    @if (!auth()->user()->isTenant())
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">Coût de la réparation (Agence uniquement) :</span>
                            <span class="text-sm font-bold font-mono text-emerald-600 dark:text-emerald-400">
                                {{ number_format((float) $incident->repair_cost, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Confirmation Finale du Locataire (si clôturé) -->
            @if ($incident->tenant_confirmation_photo || $incident->tenant_confirmation_note)
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/60 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                            Confirmation de réparation du locataire
                        </h3>
                        @if ($incident->closed_at)
                            <span class="text-xs text-slate-500">Clôturé le {{ $incident->closed_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    
                    @if ($incident->tenant_confirmation_note)
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            "{{ $incident->tenant_confirmation_note }}"
                        </p>
                    @endif

                    @if ($incident->tenant_confirmation_photo)
                        <div class="mt-2">
                            <span class="text-xs font-semibold text-slate-400 block mb-1">Photo de confirmation finale :</span>
                            <a href="{{ asset('storage/' . $incident->tenant_confirmation_photo) }}" target="_blank" class="inline-block rounded-xl overflow-hidden border border-slate-200 max-w-xs">
                                <img src="{{ asset('storage/' . $incident->tenant_confirmation_photo) }}" class="max-h-48 object-cover">
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Formulaire Prise en charge / Traitement Agence -->
            @if (!auth()->user()->isTenant() && in_array($incident->status->value, ['reported', 'in_progress']))
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Traitement de l'incident (Agence)</h3>

                    @if ($incident->status->value === 'reported')
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                            <span class="text-xs text-slate-600 dark:text-slate-400">Cet incident est nouveau. Marquez-le en cours pour informer le locataire.</span>
                            <x-button variant="secondary" wire:click="takeInCharge" class="text-xs">
                                Prendre en charge
                            </x-button>
                        </div>
                    @endif

                    <!-- Traitement & Saisie du coût -->
                    <form wire:submit="resolve" class="space-y-4 pt-2">
                        <div>
                            <x-label for="repair_details" :required="true">Comment la réparation a-t-elle été effectuée ?</x-label>
                            <textarea wire:model="repair_details" id="repair_details" rows="3" placeholder="Description des interventions réalisées, prestataire, matériel remplacé..." class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"></textarea>
                            @error('repair_details') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-label for="repair_cost" :required="true">Coût de la réparation (Visible uniquement par l'agence - FCFA)</x-label>
                            <x-input wire:model="repair_cost" type="number" step="1" id="repair_cost" placeholder="Ex: 25000" icon="wallet" :error="$errors->first('repair_cost')" />
                        </div>

                        <div class="flex justify-end">
                            <x-button variant="primary" class="shadow-md shadow-emerald-600/20 text-xs">
                                Marquer comme traité par l'agence
                            </x-button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Formulaire Confirmation Locataire -->
            @if (auth()->user()->isTenant() && $incident->status->value === 'resolved')
                <div class="bg-gradient-to-tr from-emerald-900 to-teal-800 text-white p-6 rounded-2xl shadow-md space-y-4">
                    <div>
                        <h3 class="text-lg font-bold">Confirmer la réparation de votre logement</h3>
                        <p class="text-xs text-emerald-200 mt-1">L'agence a indiqué avoir traité cet incident. Veuillez envoyer une photo de confirmation pour clôturer l'incident.</p>
                    </div>

                    <form wire:submit="confirmResolution" class="space-y-4">
                        <div>
                            <label for="confirmation_photo" class="block text-xs font-semibold mb-1 text-emerald-100">Photo de confirmation (Obligatoire)</label>
                            <input type="file" wire:model="confirmation_photo" id="confirmation_photo" accept="image/*" class="text-xs text-white file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-emerald-800 hover:file:bg-emerald-100 cursor-pointer" />
                            @error('confirmation_photo') <p class="mt-1 text-xs text-amber-300 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tenant_confirmation_note" class="block text-xs font-semibold mb-1 text-emerald-100">Vos remarques / commentaires (Optionnel)</label>
                            <textarea wire:model="tenant_confirmation_note" id="tenant_confirmation_note" rows="2" placeholder="Tout est parfait, merci !" class="w-full px-3 py-2 text-sm rounded-xl bg-emerald-950/60 border border-emerald-700 text-white focus:outline-hidden"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-emerald-900 font-bold text-xs hover:bg-emerald-50 transition shadow-sm">
                                Confirmer & Clôturer l'incident
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        <!-- Sidebar Info (1 col) -->
        <div class="space-y-6">
            
            <!-- Informations Bien & Bail -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informations complémentaires</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block">Bien immobilier :</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $incident->property?->title }}</span>
                        <p class="text-slate-500">{{ $incident->property?->address }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 block">Locataire :</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $incident->tenant?->full_name }}</span>
                        <p class="text-slate-500">{{ $incident->tenant?->phone }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 block">Niveau d'urgence :</span>
                        <span class="font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">{{ $incident->priority }}</span>
                    </div>

                    @if ($incident->lease)
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block">Contrat de bail :</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $incident->lease->reference }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
