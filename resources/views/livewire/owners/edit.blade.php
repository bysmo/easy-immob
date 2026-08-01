<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header with Breadcrumb & Quick Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <a href="{{ route('owners.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-1 transition-colors">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la liste des bailleurs</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Fiche Bailleur — {{ $owner->full_name }}</h1>
                <x-badge color="indigo" class="font-mono text-xs">{{ $owner->reference }}</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Gestion des informations contractuelles, des biens et de l'historique financier des reversements.</p>
        </div>
        <div>
            <a href="{{ \Illuminate\Support\Facades\Route::has('owners.payouts.index') ? route('owners.payouts.index') : '#' }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold hover:bg-emerald-100 transition-colors">
                <x-icon name="rents" class="w-4 h-4" />
                <span>Gestion Globale des Reversements</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-2">
            <x-icon name="check" class="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- System d'onglets (Tabs) -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 space-x-4">
        <button wire:click="setTab('info')" class="py-3 px-4 text-xs font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'info' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <x-icon name="user" class="w-4 h-4" />
            <span>Identité & Coordonnées</span>
        </button>

        <button wire:click="setTab('contracts')" class="py-3 px-4 text-xs font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'contracts' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <x-icon name="document" class="w-4 h-4" />
            <span>Mandats de Gestion ({{ $contracts->count() }})</span>
        </button>

        <button wire:click="setTab('properties')" class="py-3 px-4 text-xs font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'properties' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <x-icon name="building" class="w-4 h-4" />
            <span>Biens rattachés ({{ $properties->count() }})</span>
        </button>

        <button wire:click="setTab('payouts')" class="py-3 px-4 text-xs font-bold border-b-2 transition-colors flex items-center gap-2 {{ $activeTab === 'payouts' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            <x-icon name="rents" class="w-4 h-4" />
            <span>Reversements & Factures ({{ $payouts->count() }})</span>
            @if($totalPending > 0)
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            @endif
        </button>
    </div>

    <!-- TAB 1 : Identité & Coordonnées -->
    @if($activeTab === 'info')
        <form wire:submit="save" class="space-y-6">
            <!-- Section 1 : Identité -->
            <x-card>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                        <x-icon name="user" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Identité du Bailleur</h2>
                        <p class="text-xs text-slate-500">Personne physique ou personne morale (entreprise/SCI).</p>
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

                <div class="mt-5">
                    <x-label for="company_name">Raison Sociale <span class="normal-case font-normal text-slate-400">(Facultatif pour une entreprise/SCI)</span></x-label>
                    <x-input wire:model="company_name" type="text" id="company_name" icon="building" :error="$errors->first('company_name')" />
                </div>
            </x-card>

            <!-- Section 2 : Contact & Localisation -->
            <x-card>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                        <x-icon name="notifications" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Coordonnées de Contact</h2>
                        <p class="text-xs text-slate-500">Informations de correspondance et de facturation.</p>
                    </div>
                </div>

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

                <div class="mt-5">
                    <x-label for="address">Adresse Résidentielle / Siège Social</x-label>
                    <x-input wire:model="address" type="text" id="address" :error="$errors->first('address')" />
                </div>
            </x-card>

            <!-- Section 3 : Statut & Configuration -->
            <x-card>
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                        <x-icon name="cog" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Statut du Compte</h2>
                        <p class="text-xs text-slate-500">Définissez l'état d'activité du bailleur.</p>
                    </div>
                </div>

                <div class="max-w-xs">
                    <x-label for="status" :required="true">Statut</x-label>
                    <x-select wire:model="status" id="status" :error="$errors->first('status')">
                        <option value="active">Actif (Délégation en cours)</option>
                        <option value="inactive">Inactif (Archivé)</option>
                    </x-select>
                </div>
            </x-card>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <a href="{{ route('owners.index') }}">
                    <x-button type="button" variant="secondary">Annuler</x-button>
                </a>
                <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-32">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Mettre à jour</span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span>Sauvegarde...</span>
                    </span>
                </x-button>
            </div>
        </form>
    @endif

    <!-- TAB 2 : Mandats de gestion -->
    @if($activeTab === 'contracts')
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Mandats de Gestion Confiés par {{ $owner->full_name }}</h3>
                <a href="{{ route('management-contracts.create', ['ownerId' => $owner->id]) }}">
                    <x-button variant="primary" class="text-xs">
                        <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                        <span>Nouveau Mandat de Gestion</span>
                    </x-button>
                </a>
            </div>

            <x-card class="!p-0 overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase font-bold text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3">Référence</th>
                            <th class="p-3">Titre</th>
                            <th class="p-3">Commission</th>
                            <th class="p-3">Biens Rattachés</th>
                            <th class="p-3">Période</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($contracts as $contract)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="p-3 font-bold text-slate-900 dark:text-white">
                                    <a href="{{ route('management-contracts.show', $contract->id) }}" class="text-emerald-600 hover:underline">
                                        {{ $contract->reference }}
                                    </a>
                                </td>
                                <td class="p-3">{{ $contract->title }}</td>
                                <td class="p-3 font-semibold text-slate-700 dark:text-slate-300">{{ $contract->formatted_commission }}</td>
                                <td class="p-3">
                                    @if($contract->properties->count() > 0)
                                        <span class="text-emerald-600 font-bold">{{ $contract->properties->count() }} bien(s)</span>
                                    @else
                                        <span class="text-slate-400 italic">Aucun bien</span>
                                    @endif
                                </td>
                                <td class="p-3 text-slate-500">
                                    {{ $contract->start_date?->format('d/m/Y') }} ({{ $contract->duration_months }} mois)
                                </td>
                                <td class="p-3">
                                    <span class="badge {{ $contract->status->badgeClass() }} text-[10px]">
                                        {{ $contract->status->label() }}
                                    </span>
                                </td>
                                <td class="p-3 text-right space-x-1">
                                    <a href="{{ route('management-contracts.show', $contract->id) }}" class="text-sky-600 hover:underline font-semibold" title="Voir">Consulter</a>
                                    <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank" class="text-emerald-600 hover:underline font-semibold ml-2" title="Imprimer">🖨️ PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Aucun mandat de gestion enregistré pour ce bailleur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    <!-- TAB 3 : Biens rattachés -->
    @if($activeTab === 'properties')
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Liste des Biens Immobiliers Appartenant à {{ $owner->full_name }}</h3>
                <a href="{{ route('properties.create') }}">
                    <x-button variant="secondary" class="text-xs">
                        <x-icon name="plus" class="w-3.5 h-3.5 mr-1" />
                        <span>Ajouter un bien</span>
                    </x-button>
                </a>
            </div>

            <x-card class="!p-0 overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase font-bold text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3">Référence</th>
                            <th class="p-3">Titre du Bien</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Localisation</th>
                            <th class="p-3 text-right">Loyer Mensuel HC</th>
                            <th class="p-3 text-center">Soumis IRF</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($properties as $prop)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="p-3 font-mono font-semibold text-emerald-600">{{ $prop->reference }}</td>
                                <td class="p-3 font-medium text-slate-900 dark:text-white">{{ $prop->title }}</td>
                                <td class="p-3 text-slate-500">{{ $prop->propertyType?->name ?? '—' }}</td>
                                <td class="p-3 text-slate-500">{{ $prop->city }} ({{ $prop->neighborhood }})</td>
                                <td class="p-3 text-right font-bold text-slate-900 dark:text-white">{{ number_format($prop->rent_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="p-3 text-center">
                                    @if($prop->is_subject_to_irf)
                                        <x-badge color="emerald" class="text-[10px]">Oui ({{ number_format($prop->irf_amount, 0, ',', ' ') }} FCFA)</x-badge>
                                    @else
                                        <x-badge color="slate" class="text-[10px]">Non</x-badge>
                                    @endif
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('properties.edit', $prop->id) }}" class="text-xs font-semibold text-emerald-600 hover:underline">Consulter</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">Aucun bien rattaché à ce bailleur pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    <!-- TAB 3 : Reversements & Factures -->
    @if($activeTab === 'payouts')
        <div class="space-y-6">
            <!-- Synthèse Financière du Bailleur -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-semibold">Total Net Dû au Bailleur</span>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalNet, 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span></p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-semibold text-emerald-600">Total Réglé (Payé)</span>
                    <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalPaid, 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span></p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
                    <span class="text-xs text-slate-500 uppercase font-semibold text-amber-600">En Attente de Règlement</span>
                    <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalPending, 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span></p>
                </div>
            </div>

            <!-- Liste des Factures de Reversement du Bailleur -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Factures & Décomptes de Reversement</h3>
                </div>

                <x-card class="!p-0 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase font-bold text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="p-3">Référence</th>
                                <th class="p-3">Période</th>
                                <th class="p-3">Mode Calcul</th>
                                <th class="p-3 text-right">Loyer Brut</th>
                                <th class="p-3 text-right">Com. Agence</th>
                                <th class="p-3 text-right">Net à Reverser</th>
                                <th class="p-3 text-right">Déjà Réglé</th>
                                <th class="p-3 text-center">Statut</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($payouts as $po)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="p-3 font-mono font-semibold text-emerald-600">{{ $po->reference }}</td>
                                    <td class="p-3 font-medium text-slate-800 dark:text-slate-200">{{ $po->period }}</td>
                                    <td class="p-3">
                                        <x-badge :color="$po->calculation_type->badgeColor()" class="text-[10px]">
                                            {{ $po->calculation_type->label() }}
                                        </x-badge>
                                    </td>
                                    <td class="p-3 text-right font-medium">{{ number_format($po->gross_amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 text-right text-slate-500">{{ number_format($po->commission_amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 text-right font-bold text-slate-900 dark:text-white">{{ number_format($po->net_amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 text-right font-semibold text-emerald-600">{{ number_format($po->paid_amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 text-center">
                                        <x-badge :color="$po->status->badgeColor()" class="text-[10px]">
                                            {{ $po->status->label() }}
                                        </x-badge>
                                    </td>
                                    <td class="p-3 text-right space-x-1">
                                        @if(! $po->is_fully_paid)
                                            <x-button wire:click="openSettlementModal({{ $po->id }})" variant="primary" class="!py-1 !px-2.5 text-xs">
                                                <span>Régler</span>
                                            </x-button>
                                        @endif
                                        <a href="{{ \Illuminate\Support\Facades\Route::has('owners.payouts.print') ? route('owners.payouts.print', $po->id) : '#' }}" target="_blank" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 transition" title="Imprimer le décompte">
                                            <x-icon name="reports" class="w-4 h-4" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-slate-400">Aucune facture de reversement calculée pour ce bailleur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-card>
            </div>

            <!-- Historique des Règlements Effectués -->
            @if($settlements->count() > 0)
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">Historique des Règlements Effectués au Bailleur</h3>
                    <x-card class="!p-0 overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase font-bold text-[10px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="p-3">Référence Règlement</th>
                                    <th class="p-3">Facture Décompte</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Moyen de Règlement</th>
                                    <th class="p-3">Réf Transaction</th>
                                    <th class="p-3 text-right">Montant Réglé</th>
                                    <th class="p-3 text-center">Justificatif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($settlements as $st)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                        <td class="p-3 font-mono font-bold text-emerald-600">{{ $st->reference }}</td>
                                        <td class="p-3 font-mono text-slate-600">{{ $st->ownerPayout?->reference }}</td>
                                        <td class="p-3 text-slate-700 dark:text-slate-300">{{ $st->payment_date?->format('d/m/Y') }}</td>
                                        <td class="p-3 font-medium">{{ $st->payment_method?->label() }}</td>
                                        <td class="p-3 font-mono text-slate-500">{{ $st->transaction_reference ?: '—' }}</td>
                                        <td class="p-3 text-right font-extrabold text-slate-900 dark:text-white">{{ number_format($st->amount, 0, ',', ' ') }} FCFA</td>
                                        <td class="p-3 text-center">
                                            @if($st->proof_document_url)
                                                <a href="{{ $st->proof_document_url }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:underline font-bold text-[11px]">
                                                    <x-icon name="reports" class="w-3.5 h-3.5" />
                                                    <span>Voir reçu</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-card>
                </div>
            @endif
        </div>
    @endif

    <!-- Modale de règlement rapide -->
    @if($showSettlementModal && $settlePayout)
        <div class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md" wire:click="$set('showSettlementModal', false)"></div>
            
            <div class="relative z-10 w-full max-w-xl transform rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl border border-slate-200/80 dark:border-slate-800 transition-all overflow-hidden p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="check" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Régler la facture {{ $settlePayout->reference }}</h3>
                            <p class="text-xs text-slate-500">Bailleur : {{ $owner->full_name }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showSettlementModal', false)" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="saveSettlement" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="settleDate" :required="true">Date de règlement</x-label>
                            <x-input wire:model="settleDate" type="date" id="settleDate" :error="$errors->first('settleDate')" />
                        </div>

                        <div>
                            <x-label for="settleAmount" :required="true">Montant (FCFA)</x-label>
                            <x-input wire:model="settleAmount" type="number" step="0.01" id="settleAmount" :error="$errors->first('settleAmount')" />
                        </div>
                    </div>

                    <div>
                        <x-label for="settleMethod" :required="true">Moyen de paiement</x-label>
                        <x-select wire:model="settleMethod" id="settleMethod" :error="$errors->first('settleMethod')">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    <div>
                        <x-label for="settleReference">Références de transaction</x-label>
                        <x-input wire:model="settleReference" type="text" id="settleReference" placeholder="Ex: TX-99823100293 / Bordereau #402" :error="$errors->first('settleReference')" />
                    </div>

                    <div>
                        <x-label for="settleProof">Pièce justificative (Reçu, Bordereau, Capture Mobile Money...)</x-label>
                        <input type="file" wire:model="settleProof" id="settleProof" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <div>
                        <x-label for="settleNotes">Commentaire / Remarques</x-label>
                        <textarea wire:model="settleNotes" id="settleNotes" rows="2" class="w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 text-xs focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <x-button type="button" variant="secondary" wire:click="$set('showSettlementModal', false)">Annuler</x-button>
                        <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-36">
                            <span wire:loading.remove>Valider le règlement</span>
                            <span wire:loading>Enregistrement...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
