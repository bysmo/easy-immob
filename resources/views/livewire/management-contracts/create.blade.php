<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('management-contracts.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des mandats</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Nouveau Mandat de Gestion</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Établir un contrat de mandat de gestion entre le propriétaire et l'agence.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Informations Générales -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="user" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Informations Générales & Mandant</h2>
                    <p class="text-xs text-slate-500">Identification du propriétaire et désignation du contrat.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="owner_id" :required="true">Propriétaire Mandant</x-label>
                        <x-select wire:model.live="owner_id" id="owner_id" icon="owners" :error="$errors->first('owner_id')">
                            <option value="">— Sélectionner un propriétaire —</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-label for="reference" :required="true">Référence du Mandat</x-label>
                        <div class="flex gap-2">
                            <x-input wire:model="reference" type="text" id="reference" :error="$errors->first('reference')" class="font-mono text-xs font-bold" />
                            <button type="button" wire:click="generateReference" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition-colors shrink-0">
                                Générer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="title" :required="true">Intitulé du contrat</x-label>
                        <x-input wire:model="title" type="text" id="title" placeholder="Ex: Mandat de Gestion Immobilière Exclusif" :error="$errors->first('title')" />
                    </div>

                    <div>
                        <x-label for="agreed_rent_amount">Loyer prévisionnel estimé (FCFA)</x-label>
                        <x-input wire:model="agreed_rent_amount" type="number" step="1000" id="agreed_rent_amount" placeholder="Ex: 250000" :error="$errors->first('agreed_rent_amount')" />
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Section 2 : Durée & Honoraires d'Agence -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="rents" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Durée & Honoraires d'Agence</h2>
                    <p class="text-xs text-slate-500">Fixation de la commission agence, de la durée et des conditions fiscales.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <x-label for="start_date" :required="true">Date de prise d'effet</x-label>
                        <x-input wire:model="start_date" type="date" id="start_date" :error="$errors->first('start_date')" />
                    </div>

                    <div>
                        <x-label for="duration_months" :required="true">Durée initiale (Mois)</x-label>
                        <x-input wire:model="duration_months" type="number" min="1" id="duration_months" :error="$errors->first('duration_months')" />
                    </div>

                    <div>
                        <x-label for="notice_period_months" :required="true">Préavis de rupture (Mois)</x-label>
                        <x-input wire:model="notice_period_months" type="number" min="1" id="notice_period_months" :error="$errors->first('notice_period_months')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    <div>
                        <x-label for="commission_type" :required="true">Type de commission agence</x-label>
                        <x-select wire:model.live="commission_type" id="commission_type">
                            <option value="percentage">Pourcentage sur les loyers (%)</option>
                            <option value="fixed">Montant forfaitaire fixe (FCFA)</option>
                        </x-select>
                    </div>

                    <div>
                        <x-label for="commission_value" :required="true">
                            Valeur de la commission ({{ $commission_type === 'percentage' ? '%' : 'FCFA' }})
                        </x-label>
                        <x-input wire:model="commission_value" type="number" step="0.01" id="commission_value" :error="$errors->first('commission_value')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer hover:bg-slate-100/50 transition-colors">
                        <input type="checkbox" wire:model="irf_paid_by_owner" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">L'Impôt sur le Revenu Foncier (IRF) est supporté par le mandant</span>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer hover:bg-slate-100/50 transition-colors">
                        <input type="checkbox" wire:model="caution_kept_by_agency" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">L'Agence conserve la caution de garantie jusqu'à la fin du bail</span>
                    </label>
                </div>
            </div>
        </x-card>

        @if($owner_id)
            <!-- Section 3 : Biens Immobiliers associés -->
            <x-card>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <x-icon name="building" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Biens Immobiliers à Rattacher</h2>
                        <p class="text-xs text-slate-500">Sélectionnez les biens appartenant à ce propriétaire rattachés à ce mandat.</p>
                    </div>
                </div>

                @if($ownerProperties->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($ownerProperties as $prop)
                            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-emerald-500 cursor-pointer transition-all">
                                <input type="checkbox" wire:model="selectedProperties" value="{{ $prop->id }}" class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4" />
                                <div>
                                    <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $prop->title }}</div>
                                    <div class="text-xs text-slate-400">{{ $prop->reference }} — {{ $prop->address }}, {{ $prop->city }}</div>
                                    <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format((float)$prop->rent_amount, 0, ',', ' ') }} FCFA / mois</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs text-slate-500">
                        Ce propriétaire n'a encore aucun bien enregistré. Vous pourrez lui attribuer un bien ultérieurement lors de la création ou modification du bien.
                    </div>
                @endif
            </x-card>
        @endif

        <!-- Section 4 : Règlement & Conditions Spéciales -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="document" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Reversement & Conditions Spéciales</h2>
                    <p class="text-xs text-slate-500">Coordonnées de règlement et clauses spécifiques du contrat.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <x-label for="payment_bank_details">Coordonnées bancaires du propriétaire (RIB / Compte de reversement)</x-label>
                    <textarea wire:model="payment_bank_details" id="payment_bank_details" rows="2" placeholder="Ex: Compte N° CI092 01001 123456789012 34 chez BOA Burkina" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs p-3 focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <x-label for="terms_and_conditions">Clauses ou conditions particulières (optionnel)</x-label>
                    <textarea wire:model="terms_and_conditions" id="terms_and_conditions" rows="3" placeholder="Insérer d'éventuelles clauses particulières négociées..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs p-3 focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
            </div>
        </x-card>

        <!-- Action Bar Footer -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('management-contracts.index') }}">
                <x-button type="button" variant="secondary">Annuler</x-button>
            </a>
            <x-button type="submit" variant="primary" class="shadow-md shadow-emerald-600/20">
                <x-icon name="check" class="w-4 h-4" />
                <span>Enregistrer & Générer le Mandat</span>
            </x-button>
        </div>
    </form>
</div>
