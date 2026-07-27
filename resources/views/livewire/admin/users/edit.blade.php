<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des utilisateurs</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Modifier la fiche utilisateur</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Mettre à jour le rôle ou l'adresse email d'un membre de l'équipe.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Informations personnelles -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="user" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Identité & Rôle</h2>
                    <p class="text-xs text-slate-500">Profil du collaborateur et niveau de permission.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <x-label for="name" :required="true">Nom complet</x-label>
                    <x-input wire:model="name" type="text" id="name" autofocus icon="user" :error="$errors->first('name')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="email" :required="true">Adresse Email</x-label>
                        <x-input wire:model="email" type="email" id="email" icon="notifications" :error="$errors->first('email')" />
                    </div>

                    <div>
                        <x-label for="role" :required="true">Rôle Système</x-label>
                        <x-select wire:model="role" id="role" icon="admin" :error="$errors->first('role')">
                            <option value="">— Choisir un rôle —</option>
                            @foreach($availableRoles as $roleName)
                                <option value="{{ $roleName }}">{{ $roleName }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('admin.users.index') }}">
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
