<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- En-tête de page -->
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Informations & Paramètres de l'Agence</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configurez l'identité de votre agence, votre logo officiel pour les imprimés, ainsi que le régime fiscal (TVA) et le taux de commission.</p>
    </div>

    <!-- Alert Succès -->
    @if(session('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <x-icon name="check" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">

        <!-- Card 1: Logo & Identité Visuelle -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-icon name="building" class="w-5 h-5 text-emerald-600" />
                        <span>Logo Officiel de l'Agence</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Le logo sera directement utilisé en haut de vos documents officiels (Quittances, Baux, Reversements et Relevés).</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800">
                    <div class="relative group shrink-0">
                        @if($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="w-28 h-28 rounded-xl object-contain bg-white dark:bg-slate-900 p-2 border-2 border-emerald-500 shadow-md">
                        @elseif($existingLogoUrl)
                            <img src="{{ $existingLogoUrl }}" class="w-28 h-28 rounded-xl object-contain bg-white dark:bg-slate-900 p-2 border-2 border-slate-200 dark:border-slate-700 shadow-md">
                        @else
                            <div class="w-28 h-28 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center font-semibold text-xs p-2 text-center">
                                <x-icon name="building" class="w-8 h-8 mb-1" />
                                <span>Aucun logo</span>
                            </div>
                        @endif

                        <div wire:loading wire:target="logo" class="absolute inset-0 bg-slate-900/60 rounded-xl flex items-center justify-center text-white text-xs font-bold backdrop-blur-xs">
                            Chargement...
                        </div>
                    </div>

                    <div class="space-y-2 text-center sm:text-left flex-1">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Téléverser le logo officiel</label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Formats acceptés : PNG, JPG, WEBP. Taille maximale : 2 Mo.</p>
                        
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-2">
                            <label class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs cursor-pointer transition inline-flex items-center gap-2">
                                <x-icon name="upload" class="w-4 h-4" />
                                <span>{{ $existingLogoUrl || $logo ? 'Remplacer le logo' : 'Sélectionner une image' }}</span>
                                <input type="file" wire:model="logo" accept="image/*" class="hidden">
                            </label>

                            @if($existingLogoUrl)
                                <button type="button"
                                        @click="$dispatch('open-confirm', {
                                            title: 'Supprimer le logo de l\'agence',
                                            message: 'Êtes-vous sûr de vouloir supprimer le logo officiel de votre agence ? Les futurs imprimés seront générés sans logo.',
                                            confirmText: 'Supprimer le logo',
                                            variant: 'danger',
                                            onConfirm: () => $wire.removeLogo()
                                        })"
                                        class="px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-semibold transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <x-icon name="trash" class="w-4 h-4" />
                                    <span>Supprimer le logo</span>
                                </button>
                            @endif
                        </div>
                        @error('logo') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Coordonnées & Identité Légale -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Identité & Coordonnées Officielles</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ces informations figureront sur l'en-tête et le pied de page des documents d'impression.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="name" :required="true">Nom commercial de l'agence</x-label>
                        <x-input id="name" type="text" wire:model="name" placeholder="Ex: Horizon Immobilier" required />
                        @error('name') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="legal_name">Raison sociale (Nom légal)</x-label>
                        <x-input id="legal_name" type="text" wire:model="legal_name" placeholder="Ex: SARL Horizon West Africa" />
                        @error('legal_name') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="email" :required="true">Adresse email professionnelle</x-label>
                        <x-input id="email" type="email" wire:model="email" placeholder="contact@horizon-immo.com" required />
                        @error('email') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="phone">Numéro de téléphone</x-label>
                        <x-input id="phone" type="text" wire:model="phone" placeholder="+225 27 22 00 00 00 / 07 00 00 00 00" />
                        @error('phone') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <x-label for="address">Adresse du siège social</x-label>
                        <x-input id="address" type="text" wire:model="address" placeholder="Abidjan, Cocody Riviera 3, Bd de France" />
                        @error('address') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <x-label for="nif_rccm">Identifiant Fiscal / N° RCCM / IFU</x-label>
                        <x-input id="nif_rccm" type="text" wire:model="nif_rccm" placeholder="Ex: RCCM CI-ABJ-2024-B-99887 | NIF 1928374 A" />
                        <p class="text-[11px] text-slate-500 mt-1">Numéro d'immatriculation légale figurant sur les reçus et factures.</p>
                        @error('nif_rccm') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Fiscalité, TVA & Commission Agence -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Paramètres Financiers & Fiscalité (TVA)</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Réglez l'assujettissement à la TVA et les honoraires par défaut appliqués sur la gestion locative.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- TVA Toggle & Taux -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-4">
                        <label class="relative flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="is_subject_to_tva" class="mt-1 w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Assujettissement à la TVA</span>
                                <span class="text-[11px] text-slate-500 block">Cochez si votre agence est soumise et collecte la TVA sur ses prestations de gestion.</span>
                            </div>
                        </label>

                        @if($is_subject_to_tva)
                            <div class="pt-2 border-t border-slate-200/80 dark:border-slate-700">
                                <x-label for="tva_rate" :required="true">Taux de TVA (%)</x-label>
                                <div class="relative mt-1">
                                    <x-input id="tva_rate" type="number" step="0.01" min="0" max="100" wire:model="tva_rate" placeholder="18.00" required class="pr-8" />
                                    <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">Taux standard habituellement de 18%.</p>
                                @error('tva_rate') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-300">
                                ℹ️ Agence non assujettie à la TVA. Vos prestations seront mentionnées HT / TVA non applicable.
                            </div>
                        @endif
                    </div>

                    <!-- Taux de commission agence -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 space-y-4">
                        <div>
                            <x-label for="commission_rate" :required="true">Taux de commission agence par défaut (%)</x-label>
                            <div class="relative mt-1">
                                <x-input id="commission_rate" type="number" step="0.1" min="0" max="100" wire:model="commission_rate" placeholder="10.0" required class="pr-8" />
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Pourcentage par défaut prélevé sur les encaissements pour le calcul du reversement bailleur.</p>
                            @error('commission_rate') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton d'enregistrement général -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-button type="submit" variant="primary" class="!px-6 !py-3 text-sm">
                <x-icon name="check" class="w-4 h-4" />
                <span wire:loading.remove wire:target="save">Enregistrer les modifications</span>
                <span wire:loading wire:target="save">Enregistrement en cours...</span>
            </x-button>
        </div>
    </form>
</div>
