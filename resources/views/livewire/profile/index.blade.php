<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- En-tête de page -->
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Mon Profil & Sécurité</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Gérez vos informations personnelles, votre photo de profil et votre mot de passe d'accès.</p>
    </div>

    <!-- Section 1: Informations de profil & Avatar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Informations Personnelles</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mettez à jour vos identifiants de compte et vos coordonnées de contact.</p>
            </div>

            @if(session('success_profile'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
                    <x-icon name="check" class="w-4 h-4 text-emerald-600" />
                    <span>{{ session('success_profile') }}</span>
                </div>
            @endif

            <form wire:submit="updateProfileInformation" class="space-y-6">
                <!-- Avatar & Upload -->
                <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800">
                    <div class="relative group shrink-0">
                        @if($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" class="w-20 h-20 rounded-full object-cover border-2 border-emerald-500 shadow-md">
                        @elseif($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 shadow-md">
                        @else
                            <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-2xl shadow-inner">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <div wire:loading wire:target="avatar" class="absolute inset-0 bg-slate-900/60 rounded-full flex items-center justify-center text-white text-xs font-bold backdrop-blur-xs">
                            Chargement...
                        </div>
                    </div>

                    <div class="space-y-2 text-center sm:text-left flex-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Photo de profil (Avatar)</label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Format PNG, JPG ou WEBP. Taille max: 2 Mo.</p>
                        
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                            <label class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs cursor-pointer transition inline-flex items-center gap-2">
                                <x-icon name="upload" class="w-3.5 h-3.5" />
                                <span>Changer la photo</span>
                                <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                            </label>

                            @if($user->avatar_path)
                                <button type="button"
                                        @click="$dispatch('open-confirm', {
                                            title: 'Supprimer la photo de profil',
                                            message: 'Êtes-vous sûr de vouloir supprimer votre photo de profil ?',
                                            confirmText: 'Supprimer la photo',
                                            variant: 'danger',
                                            onConfirm: () => $wire.removeAvatar()
                                        })"
                                        class="px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-semibold transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                    <span>Supprimer</span>
                                </button>
                            @endif
                        </div>
                        @error('avatar') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-label for="name">Nom complet</x-label>
                        <x-input id="name" type="text" wire:model="name" required />
                        @error('name') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="email">Adresse e-mail</x-label>
                        <x-input id="email" type="email" wire:model="email" required />
                        @error('email') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="phone">Numéro de téléphone</x-label>
                        <x-input id="phone" type="text" wire:model="phone" placeholder="+225 07 00 00 00 00" />
                        @error('phone') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="submit" variant="primary">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Enregistrer les modifications</span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section 2: Paramètres Financiers de l'Agence (Masqué pour les bailleurs et locataires) -->
    @if(auth()->user()->agency_id && !auth()->user()->isOwner() && !auth()->user()->isTenant())
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Paramètres Financiers de l'Agence</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Définissez le taux de commission perçu sur les encaissements et la déclaration TVA.</p>
                </div>

                @if(session('success_agency'))
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600" />
                        <span>{{ session('success_agency') }}</span>
                    </div>
                @endif

                <form wire:submit="updateAgencySettings" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-label for="agency_commission_rate" :required="true">Taux de commission de l'agence (%)</x-label>
                            <x-input id="agency_commission_rate" type="number" step="0.1" min="0" max="100" wire:model="agency_commission_rate" placeholder="10.0" required />
                            <p class="text-[11px] text-slate-500 mt-1">Pourcentage d'honoraires prélevé par l'agence sur les loyers encaissés.</p>
                            @error('agency_commission_rate') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col justify-center">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-2">
                                <label class="relative flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model="agency_is_subject_to_tva" class="mt-1 w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Agence soumise à la TVA (18%)</span>
                                        <span class="text-[11px] text-slate-500 block">Cochez si votre agence collecte et reverse la TVA de 18% sur sa commission perçue.</span>
                                    </div>
                                </label>
                            </div>
                            @error('agency_is_subject_to_tva') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-button type="submit" variant="primary">
                            <x-icon name="check" class="w-4 h-4" />
                            <span>Enregistrer les paramètres de l'agence</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Section 3: Modification de mot de passe -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Sécurité & Mot de Passe</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Assurez-vous que votre compte utilise un mot de passe fort et unique.</p>
            </div>

            @if(session('success_password'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
                    <x-icon name="check" class="w-4 h-4 text-emerald-600" />
                    <span>{{ session('success_password') }}</span>
                </div>
            @endif

            <form wire:submit="updatePassword" class="space-y-4 max-w-md">
                <div>
                    <x-label for="current_password">Mot de passe actuel</x-label>
                    <x-input id="current_password" type="password" wire:model="current_password" required />
                    @error('current_password') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label for="password">Nouveau mot de passe</x-label>
                    <x-input id="password" type="password" wire:model="password" required />
                    @error('password') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label for="password_confirmation">Confirmer le nouveau mot de passe</x-label>
                    <x-input id="password_confirmation" type="password" wire:model="password_confirmation" required />
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="secondary">
                        <x-icon name="lock" class="w-4 h-4" />
                        <span>Mettre à jour le mot de passe</span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
