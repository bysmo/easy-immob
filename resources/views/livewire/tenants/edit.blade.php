<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
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
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Identité -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="tenants" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Identité du Locataire</h2>
                    <p class="text-xs text-slate-500">Nom et prénom du titulaire du bail.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-label for="first_name" :required="true">Prénom</x-label>
                    <x-input wire:model="first_name" type="text" id="first_name" autofocus :error="$errors->first('first_name')" />
                </div>

                <div>
                    <x-label for="last_name" :required="true">Nom de famille</x-label>
                    <x-input wire:model="last_name" type="text" id="last_name" :error="$errors->first('last_name')" />
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
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Coordonnées de Contact</h2>
                    <p class="text-xs text-slate-500">Moyens de joindre le locataire et garants.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="email">Adresse Email</x-label>
                        <x-input wire:model="email" type="email" id="email" icon="notifications" :error="$errors->first('email')" />
                    </div>

                    <div>
                        <x-label for="phone">Numéro de Téléphone</x-label>
                        <x-input wire:model="phone" type="text" id="phone" :error="$errors->first('phone')" />
                    </div>
                </div>

                <div>
                    <x-label for="address">Adresse de résidence actuelle</x-label>
                    <x-input wire:model="address" type="text" id="address" :error="$errors->first('address')" />
                </div>

                <div>
                    <x-label for="emergency_contact">Contact en cas d'urgence / Personne à prévenir</x-label>
                    <x-input wire:model="emergency_contact" type="text" id="emergency_contact" icon="user" :error="$errors->first('emergency_contact')" />
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
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Statut du Dossier</h2>
                    <p class="text-xs text-slate-500">État d'activité du locataire.</p>
                </div>
            </div>

            <div class="max-w-xs">
                <x-label for="status" :required="true">Statut</x-label>
                <x-select wire:model="status" id="status" :error="$errors->first('status')">
                    <option value="active">Actif (Dossier validé)</option>
                    <option value="inactive">Inactif (Archivé)</option>
                </x-select>
            </div>
        </x-card>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('tenants.index') }}">
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
</div>
