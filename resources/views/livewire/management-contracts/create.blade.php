<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Nouveau Mandat de Gestion</h1>
            <p class="text-sm text-base-content/70">Établir un contrat de mandat de gestion entre le propriétaire et l'agence.</p>
        </div>
        <a href="{{ route('management-contracts.index') }}" class="btn btn-outline btn-sm">
            ← Retour à la liste
        </a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-4">
                <h2 class="card-title text-lg border-b pb-2">Informations Générales</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label font-semibold text-sm">Propriétaire Mandant <span class="text-error">*</span></label>
                        <select wire:model.live="owner_id" class="select select-bordered w-full @error('owner_id') select-error @enderror">
                            <option value="">-- Sélectionner un propriétaire --</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                            @endforeach
                        </select>
                        @error('owner_id') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">Référence du Mandat <span class="text-error">*</span></label>
                        <div class="join w-full">
                            <input type="text" wire:model="reference" class="input input-bordered join-item w-full @error('reference') input-error @enderror" />
                            <button type="button" wire:click="generateReference" class="btn btn-neutral join-item">Générer</button>
                        </div>
                        @error('reference') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">Intitulé du contrat</label>
                        <input type="text" wire:model="title" class="input input-bordered w-full" />
                        @error('title') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">Loyer prévisionnel estimé (FCFA)</label>
                        <input type="number" step="1000" wire:model="agreed_rent_amount" placeholder="Ex: 250000" class="input input-bordered w-full" />
                        @error('agreed_rent_amount') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-4">
                <h2 class="card-title text-lg border-b pb-2">Durée & Honoraires d'Agence</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="label font-semibold text-sm">Date de prise d'effet <span class="text-error">*</span></label>
                        <input type="date" wire:model="start_date" class="input input-bordered w-full @error('start_date') input-error @enderror" />
                        @error('start_date') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">Durée initiale (Mois) <span class="text-error">*</span></label>
                        <input type="number" min="1" wire:model="duration_months" class="input input-bordered w-full" />
                        @error('duration_months') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">Préavis de rupture (Mois)</label>
                        <input type="number" min="1" wire:model="notice_period_months" class="input input-bordered w-full" />
                        @error('notice_period_months') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="label font-semibold text-sm">Type de commission agence</label>
                        <select wire:model.live="commission_type" class="select select-bordered w-full">
                            <option value="percentage">Pourcentage sur les loyers (%)</option>
                            <option value="fixed">Montant forfaitaire fixe (FCFA)</option>
                        </select>
                    </div>

                    <div>
                        <label class="label font-semibold text-sm">
                            Valeur de la commission 
                            <span class="text-xs text-base-content/60">({{ $commission_type === 'percentage' ? '%' : 'FCFA' }})</span>
                        </label>
                        <input type="number" step="0.01" wire:model="commission_value" class="input input-bordered w-full @error('commission_value') input-error @enderror" />
                        @error('commission_value') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" wire:model="irf_paid_by_owner" class="checkbox checkbox-primary" />
                            <span class="label-text">L'Impôt sur le Revenu Foncier (IRF) est supporté par le mandant</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" wire:model="caution_kept_by_agency" class="checkbox checkbox-primary" />
                            <span class="label-text">L'Agence conserve la caution de garantie jusqu'à la fin du bail</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if($owner_id)
            <div class="card bg-base-100 shadow">
                <div class="card-body space-y-4">
                    <h2 class="card-title text-lg border-b pb-2">Biens Immobiliers associés à ce mandat</h2>
                    <p class="text-xs text-base-content/70">Cochez les biens appartement à ce propriétaire que vous souhaitez rattacher à ce mandat de gestion.</p>

                    @if($ownerProperties->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($ownerProperties as $prop)
                                <label class="flex items-center p-3 border rounded-lg hover:bg-base-200 cursor-pointer gap-3">
                                    <input type="checkbox" wire:model="selectedProperties" value="{{ $prop->id }}" class="checkbox checkbox-sm checkbox-primary" />
                                    <div>
                                        <div class="font-bold text-sm">{{ $prop->title }}</div>
                                        <div class="text-xs text-base-content/60">{{ $prop->reference }} - {{ $prop->address }}, {{ $prop->city }}</div>
                                        <div class="text-xs text-primary font-semibold">{{ number_format((float)$prop->rent_amount, 0, ',', ' ') }} FCFA / mois</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-sm">
                            Ce propriétaire n'a encore aucun bien enregistré. Vous pourrez lui attribuer un bien ultérieurement lors de la création ou modification du bien.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-4">
                <h2 class="card-title text-lg border-b pb-2">Règlement & Conditions Spéciales</h2>

                <div>
                    <label class="label font-semibold text-sm">Coordonnées bancaires du propriétaire (RIB / Compte de reversement)</label>
                    <textarea wire:model="payment_bank_details" rows="2" placeholder="Ex: Compte N° XXXX-XXXX chez Bank, Clé XX" class="textarea textarea-bordered w-full"></textarea>
                </div>

                <div>
                    <label class="label font-semibold text-sm">Clauses ou conditions particulières (optionnel)</label>
                    <textarea wire:model="terms_and_conditions" rows="3" placeholder="Insérer d'éventuelles clauses spécifiques agreed par les parties..." class="textarea textarea-bordered w-full"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('management-contracts.index') }}" class="btn btn-ghost">Annuler</a>
            <button type="submit" class="btn btn-primary">
                💾 Enregistrer & Générer le Mandat
            </button>
        </div>
    </form>
</div>
