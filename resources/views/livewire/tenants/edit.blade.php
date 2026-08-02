<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des locataires</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Modifier la fiche locataire</h1>
                <x-badge color="indigo" class="font-mono text-xs">{{ $tenant->reference }}</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Mettre à jour les informations et coordonnées de {{ $tenant->full_name }}.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2">
            @if ($tenant->isPortalActive())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Portail actif
                </span>
            @elseif ($tenant->hasPortalAccess())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    En attente d'activation
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Portail inactif
                </span>
            @endif

            @if ($tenant->email)
                <button type="button"
                        @click="$dispatch('open-confirm', {
                            title: @js($tenant->hasPortalAccess() ? "Renvoyer l'invitation portail" : "Envoyer l'invitation portail"),
                            message: @js("Voulez-vous envoyer un lien d'accès au portail locataire à {$tenant->email} ?"),
                            confirmText: @js("Envoyer l'invitation"),
                            variant: 'primary',
                            onConfirm: () => $wire.sendInvitation()
                        })"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold hover:bg-emerald-100 transition-colors cursor-pointer">
                    <x-icon name="notifications" class="w-4 h-4" />
                    <span>{{ $tenant->hasPortalAccess() ? 'Renvoyer l\'invitation' : 'Envoyer l\'invitation portail' }}</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
            <x-icon name="check" class="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-2">
            <x-icon name="alert" class="w-5 h-5 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($tenant->hasPortalAccess())
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-sm flex items-center gap-3">
            <x-icon name="lock" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
            <div>
                <span class="font-bold">Portail locataire attribué ou actif :</span> Les informations personnelles de ce locataire sont désormais gérées depuis son espace portail. Elles ne peuvent plus être modifiées ni supprimées par l'agence.
            </div>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Identité -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="user" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Identité du Locataire</h2>
                    <p class="text-xs text-slate-500">Informations d'état civil figurant sur le contrat de bail.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-label for="first_name" :required="true">Prénom</x-label>
                    <x-input wire:model="first_name" type="text" id="first_name" autofocus :disabled="$tenant->hasPortalAccess()" :error="$errors->first('first_name')" />
                </div>

                <div>
                    <x-label for="last_name" :required="true">Nom de famille</x-label>
                    <x-input wire:model="last_name" type="text" id="last_name" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('last_name')" />
                </div>

                <div>
                    <x-label for="profession">Profession / Fonction</x-label>
                    <x-input wire:model="profession" type="text" id="profession" placeholder="Ex: Secrétaire de Direction, Commerçant" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('profession')" />
                </div>

                <div>
                    <x-label for="nationality">Nationalité</x-label>
                    <x-input wire:model="nationality" type="text" id="nationality" placeholder="Ex: Burkinabè" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('nationality')" />
                </div>

                <div class="sm:col-span-2">
                    <x-label for="id_card_number">Pièce d'identité (N° & détails)</x-label>
                    <x-input wire:model="id_card_number" type="text" id="id_card_number" placeholder="Ex: CNIB N°B18203984 du 05/04/2023 par ONI/Ouaga" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('id_card_number')" />
                    <p class="text-[11px] text-slate-500 mt-1">Sert de référence pour les quittances et le contrat de bail.</p>
                </div>
            </div>
        </x-card>

        <!-- Section 2 : Contact & Urgence -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                    <x-icon name="notifications" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Coordonnées & Personne à Prévenir</h2>
                    <p class="text-xs text-slate-500">Pour l'envoi des avis d'échéance et quittances.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-label for="email">Adresse Email</x-label>
                    <x-input wire:model="email" type="email" id="email" icon="notifications" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('email')" />
                </div>

                <div>
                    <x-label for="phone" :required="true">Numéro de Téléphone principal</x-label>
                    <x-input wire:model="phone" type="text" id="phone" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('phone')" />
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-label for="address">Adresse de résidence précédente / Domicile</x-label>
                    <x-input wire:model="address" type="text" id="address" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('address')" />
                </div>

                <div>
                    <x-label for="emergency_contact">Contact d'urgence (Nom & Téléphone)</x-label>
                    <x-input wire:model="emergency_contact" type="text" id="emergency_contact" icon="user" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('emergency_contact')" />
                </div>
            </div>
        </x-card>

        <!-- Section 3 : Statut -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <x-icon name="cog" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Statut du dossier locataire</h2>
                    <p class="text-xs text-slate-500">Gérez la validité administrative du locataire.</p>
                </div>
            </div>

            <div class="max-w-xs">
                <x-label for="status" :required="true">Statut</x-label>
                <x-select wire:model="status" id="status" :disabled="$tenant->hasPortalAccess()" :error="$errors->first('status')">
                    <option value="active">Actif (En location / Éligible)</option>
                    <option value="inactive">Inactif (Parti / Archivé)</option>
                </x-select>
            </div>
        </x-card>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('tenants.index') }}">
                <x-button type="button" variant="secondary">Retour</x-button>
            </a>
            @unless($tenant->hasPortalAccess())
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
            @endunless
        </div>

    </form>
</div>
