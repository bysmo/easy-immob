<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('leases.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des contrats</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Nouveau contrat de location</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Établissez un nouveau bail entre un propriétaire et un locataire.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        <!-- Section 1 : Parties au Contrat -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="building" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Bien & Locataire</h2>
                    <p class="text-xs text-slate-500">Sélection du bien disponible et de l'occupant titulaire.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="property_id" :required="true">Bien immobilier à louer</x-label>
                        <x-select wire:model.live="property_id" id="property_id" icon="building" :error="$errors->first('property_id')">
                            <option value="">— Choisir un bien disponible —</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->title }} ({{ $property->reference }}) - {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} FCFA</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-label for="tenant_id" :required="true">Locataire titulaire</x-label>
                        <x-select wire:model="tenant_id" id="tenant_id" icon="user" :error="$errors->first('tenant_id')">
                            <option value="">— Choisir un locataire —</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->full_name }} ({{ $tenant->reference }})</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <!-- Saisie directe du Code Locataire -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-3">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">
                        Ou rechercher un locataire par son Code Locataire (ex: LOC-849201) :
                    </span>
                    <div class="flex gap-2">
                        <x-input wire:model="tenant_code_input" type="text" placeholder="Entrez le code LOC-XXXXXX" icon="user" class="uppercase" />
                        <button type="button" wire:click="searchTenantByCode" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shrink-0 transition shadow-sm">
                            Rechercher & Rattacher
                        </button>
                    </div>
                    @if ($tenant_code_message)
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">{{ $tenant_code_message }}</p>
                    @endif
                    @if ($tenant_code_error)
                        <p class="text-xs text-rose-500 font-semibold">{{ $tenant_code_error }}</p>
                    @endif
                </div>

                <div>
                    <x-label for="template_id">Modèle de contrat généré <span class="normal-case font-normal text-slate-400">(Facultatif)</span></x-label>
                    <x-select wire:model="template_id" id="template_id" icon="document" :error="$errors->first('template_id')">
                        <option value="">— Aucun modèle (Brouillon simple) —</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
        </x-card>

        <!-- Section 2 : Durée du Bail -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                    <x-icon name="reports" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Période d'Occupation</h2>
                    <p class="text-xs text-slate-500">Dates de prise d'effet et d'échéance du bail.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <x-label for="start_date" :required="true">Date de prise d'effet (Début)</x-label>
                    <x-input wire:model="start_date" type="date" id="start_date" :error="$errors->first('start_date')" />
                </div>

                <div>
                    <x-label for="end_date" :required="true">Date d'échéance (Fin)</x-label>
                    <x-input wire:model="end_date" type="date" id="end_date" :error="$errors->first('end_date')" />
                </div>
            </div>
        </x-card>

        <!-- Section 3 : Modalités Financières -->
        <x-card>
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="wallet" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Montants & Échéances de Loyer</h2>
                    <p class="text-xs text-slate-500">Fixez le loyer, les charges et la caution.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <x-label for="rent_amount" :required="true">Loyer mensuel HC (FCFA)</x-label>
                        <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" icon="wallet" :error="$errors->first('rent_amount')" />
                    </div>

                    <div>
                        <x-label for="charges_amount">Charges mensuelles (FCFA)</x-label>
                        <x-input wire:model="charges_amount" type="number" step="1000" id="charges_amount" :error="$errors->first('charges_amount')" />
                    </div>

                    <div>
                        <x-label for="payment_due_day" :required="true">Jour d'échéance (1-31)</x-label>
                        <x-input wire:model="payment_due_day" type="number" min="1" max="31" id="payment_due_day" :error="$errors->first('payment_due_day')" />
                    </div>
                </div>

                <div>
                    <x-label for="deposit_amount">Dépôt de garantie / Caution (FCFA)</x-label>
                    <x-input wire:model="deposit_amount" type="number" step="1000" id="deposit_amount" icon="shield" :error="$errors->first('deposit_amount')" />
                </div>
            </div>
        </x-card>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('leases.index') }}">
                <x-button type="button" variant="secondary">Annuler</x-button>
            </a>
            <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-40">
                <span wire:loading.remove class="flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Créer le contrat (Brouillon)</span>
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
