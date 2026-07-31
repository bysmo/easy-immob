<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-1">
                <x-icon name="owners" class="w-4 h-4" />
                <span>Gestion Financière Bailleurs</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Reversement des fonds aux bailleurs</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Calcul des décomptes de reversement, suivi des factures et règlement des fonds.</p>
        </div>
        <div>
            <x-button wire:click="openCalculationModal" variant="primary" class="shadow-lg shadow-emerald-600/20">
                <x-icon name="plus" class="w-4 h-4 mr-2" />
                <span>Calculer les reversements</span>
            </x-button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 text-amber-800 dark:text-amber-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="bell" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
                <span>{{ session('warning') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-800 dark:text-rose-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="close" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Cartes KPI Financières -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1 : Loyers Bruts -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Loyers Calculés</span>
                <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <x-icon name="rents" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">
                {{ number_format($stats['total_gross'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Période {{ $selectedPeriod ?: 'Toutes' }}</p>
        </div>

        <!-- KPI 2 : Commissions Agence -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Commissions Agence</span>
                <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <x-icon name="building" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">
                {{ number_format($stats['total_commission'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Frais de gestion déduits</p>
        </div>

        <!-- KPI 3 : Montant Réglé -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Reversements Réglés</span>
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                {{ number_format($stats['total_paid'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Payés aux bailleurs</p>
        </div>

        <!-- KPI 4 : En Attente -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">En Attente de Règlement</span>
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <x-icon name="bell" class="w-4 h-4" />
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                {{ number_format($stats['total_pending'], 0, ',', ' ') }} <span class="text-xs font-normal text-slate-400">FCFA</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Factures à régler</p>
        </div>
    </div>

    <!-- Filtres de recherche -->
    <x-card class="!p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Recherche Bailleur -->
                <div>
                    <x-input wire:model.live.debounce.300ms="searchOwner" type="search" placeholder="Rechercher par nom, référence..." icon="search" class="!py-2 text-xs" />
                </div>

                <!-- Filtre Période -->
                <div>
                    <x-input wire:model.live="selectedPeriod" type="month" class="!py-2 text-xs" />
                </div>

                <!-- Filtre Statut -->
                <div>
                    <x-select wire:model.live="selectedStatus" class="!py-2 text-xs">
                        <option value="all">Tous les statuts</option>
                        <option value="pending">À régler (En attente)</option>
                        <option value="partially_paid">Partiellement réglé</option>
                        <option value="paid">Réglé</option>
                        <option value="cancelled">Annulé</option>
                    </x-select>
                </div>
            </div>

            @if(!empty($selectedPeriod) || $selectedStatus !== 'all' || !empty($searchOwner))
                <button wire:click="$set('selectedPeriod', ''); $set('selectedStatus', 'all'); $set('searchOwner', '')" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline shrink-0 flex items-center gap-1">
                    <x-icon name="close" class="w-3.5 h-3.5" />
                    <span>Réinitialiser les filtres</span>
                </button>
            @endif
        </div>
    </x-card>

    <!-- Tableau des Factures de Reversement -->
    <x-card class="!p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Référence</th>
                        <th class="py-3.5 px-4">Bailleur</th>
                        <th class="py-3.5 px-4">Période</th>
                        <th class="py-3.5 px-4">Mode de calcul</th>
                        <th class="py-3.5 px-4 text-right">Loyer Brut</th>
                        <th class="py-3.5 px-4 text-right">Com. Agence</th>
                        <th class="py-3.5 px-4 text-right">Net à Reverser</th>
                        <th class="py-3.5 px-4 text-right">Déjà Réglé</th>
                        <th class="py-3.5 px-4 text-center">Statut</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse ($payouts as $payout)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ $payout->reference }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">
                                <a href="{{ route('owners.edit', $payout->owner_id) }}" class="hover:underline hover:text-emerald-600">
                                    {{ $payout->owner?->full_name }}
                                </a>
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $payout->owner?->reference }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                {{ $payout->period }}
                            </td>
                            <td class="py-3 px-4">
                                @if($payout->calculation_type === \App\Domain\Owner\Enums\OwnerPayoutCalculationType::Collected)
                                    <x-badge color="emerald" class="text-[10px]">
                                        Encaissements réels
                                    </x-badge>
                                @else
                                    <x-badge color="amber" class="text-[10px]">
                                        Loyers attendus
                                    </x-badge>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700 dark:text-slate-300">
                                {{ number_format($payout->gross_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500">
                                {{ number_format($payout->commission_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-white">
                                {{ number_format($payout->net_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($payout->paid_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3 px-4 text-center">
                                <x-badge :color="$payout->status->badgeColor()" class="text-[10px]">
                                    {{ $payout->status->label() }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <!-- Action : Régler la facture -->
                                @if(! $payout->is_fully_paid)
                                    <x-button wire:click="openSettlementModal({{ $payout->id }})" variant="primary" class="!py-1 !px-2.5 text-xs">
                                        <x-icon name="check" class="w-3.5 h-3.5 mr-1" />
                                        <span>Régler</span>
                                    </x-button>
                                @endif

                                <!-- Action : Détails -->
                                <x-button wire:click="openDetailsModal({{ $payout->id }})" variant="secondary" class="!py-1 !px-2.5 text-xs">
                                    <span>Détails</span>
                                </x-button>

                                <!-- Action : Imprimer décompte -->
                                <a href="{{ route('owners.payouts.print', $payout->id) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Imprimer la facture">
                                    <x-icon name="reports" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <x-icon name="rents" class="w-10 h-10 mx-auto mb-2 text-slate-300 dark:text-slate-700" />
                                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Aucune facture de reversement trouvée.</p>
                                <p class="text-xs text-slate-400 mt-1">Cliquer sur "Calculer les reversements" ci-dessus pour lancer le calcul de la période.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $payouts->links() }}
            </div>
        @endif
    </x-card>

    <!-- Modale 1 : Calcul des Reversements -->
    @if($showCalculationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md" wire:click="$set('showCalculationModal', false)"></div>
            
            <div class="relative z-10 w-full max-w-xl transform rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl border border-slate-200/80 dark:border-slate-800 transition-all overflow-hidden p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="plus" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Calculer les reversements</h3>
                            <p class="text-xs text-slate-500">Génération automatique des factures de reversement agence.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showCalculationModal', false)" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="runCalculation" class="space-y-5">
                    <!-- Champ Période -->
                    <div>
                        <x-label for="calcPeriod" :required="true">Période concernée</x-label>
                        <x-input wire:model="calcPeriod" type="month" id="calcPeriod" :error="$errors->first('calcPeriod')" />
                        <p class="text-[11px] text-slate-400 mt-1">Sélectionnez le mois et l'année du reversement (ex: 2026-07).</p>
                    </div>

                    <!-- Mode de Calcul -->
                    <div>
                        <x-label :required="true">Mode de calcul des montants dus</x-label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                            <!-- Option A : Encaissements Réels -->
                            <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition-all {{ $calcType === 'collected' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Loyers réellement encaissés</span>
                                    <input type="radio" wire:model.live="calcType" value="collected" class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Reverse uniquement ce que l'agence a effectivement perçu des locataires sur la période.</p>
                            </label>

                            <!-- Option B : Loyers Attendus -->
                            <label class="relative flex flex-col p-4 rounded-2xl border cursor-pointer transition-all {{ $calcType === 'expected' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Loyers attendus</span>
                                    <input type="radio" wire:model.live="calcType" value="expected" class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">L'agence avance le reversement complet au bailleur et recouvre plus tard chez le locataire.</p>
                            </label>
                        </div>
                    </div>

                    <!-- Filtre Bailleur spécifique (Facultatif) -->
                    <div>
                        <x-label for="calcOwnerId">Bailleur concerné <span class="normal-case font-normal text-slate-400">(Facultatif)</span></x-label>
                        <x-select wire:model="calcOwnerId" id="calcOwnerId">
                            <option value="">Tous les bailleurs actifs</option>
                            @foreach($owners as $ow)
                                <option value="{{ $ow->id }}">{{ $ow->full_name }} ({{ $ow->reference }})</option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <x-button type="button" variant="secondary" wire:click="$set('showCalculationModal', false)">Annuler</x-button>
                        <x-button type="submit" variant="primary" wire:loading.attr="disabled" class="min-w-36">
                            <span wire:loading.remove>Lancer le calcul</span>
                            <span wire:loading>Calcul en cours...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modale 2 : Régler la Facture Bailleur -->
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
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Régler la facture bailleur</h3>
                            <p class="text-xs text-slate-500">Règlement du décompte {{ $settlePayout->reference }} pour {{ $settlePayout->owner?->full_name }}.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showSettlementModal', false)" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Récapitulatif montant -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500">Montant Net Total :</span>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($settlePayout->net_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-500">Solde Restant à Régler :</span>
                        <p class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($settlePayout->remaining_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <form wire:submit="saveSettlement" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Date de Règlement -->
                        <div>
                            <x-label for="settleDate" :required="true">Date de règlement</x-label>
                            <x-input wire:model="settleDate" type="date" id="settleDate" :error="$errors->first('settleDate')" />
                        </div>

                        <!-- Montant -->
                        <div>
                            <x-label for="settleAmount" :required="true">Montant (FCFA)</x-label>
                            <x-input wire:model="settleAmount" type="number" step="0.01" id="settleAmount" :error="$errors->first('settleAmount')" />
                        </div>
                    </div>

                    <!-- Moyen de Paiement -->
                    <div>
                        <x-label for="settleMethod" :required="true">Moyen de paiement</x-label>
                        <x-select wire:model="settleMethod" id="settleMethod" :error="$errors->first('settleMethod')">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->value }}">{{ $pm->label() }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    <!-- Références de transaction -->
                    <div>
                        <x-label for="settleReference">Références de transaction <span class="normal-case font-normal text-slate-400">(N° virement, bordereau, tx ID Mobile Money...)</span></x-label>
                        <x-input wire:model="settleReference" type="text" id="settleReference" placeholder="Ex: TX-99823100293 / Bordereau #402" :error="$errors->first('settleReference')" />
                    </div>

                    <!-- Upload Pièce Justificative -->
                    <div>
                        <x-label for="settleProof">Pièce justificative (Reçu, Bordereau, Capture Mobile Money...)</x-label>
                        <input type="file" wire:model="settleProof" id="settleProof" accept="image/*,application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-300">
                        @error('settleProof') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror

                        @if ($settleProof)
                            <div class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <x-icon name="check" class="w-3.5 h-3.5" />
                                <span>Fichier prêt à être téléversé : {{ $settleProof->getClientOriginalName() }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Commentaire -->
                    <div>
                        <x-label for="settleNotes">Commentaire / Remarques</x-label>
                        <textarea wire:model="settleNotes" id="settleNotes" rows="2" class="w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Informations complémentaires..."></textarea>
                    </div>

                    <!-- Actions -->
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

    <!-- Modale 3 : Détails des Loyers Inclus -->
    @if($showDetailsModal && $detailPayout)
        <div class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20 flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-md" wire:click="$set('showDetailsModal', false)"></div>
            
            <div class="relative z-10 w-full max-w-3xl transform rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl border border-slate-200/80 dark:border-slate-800 transition-all overflow-hidden p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Détails du Décompte {{ $detailPayout->reference }}</h3>
                        <p class="text-xs text-slate-500">Bailleur : {{ $detailPayout->owner?->full_name }} — Période : {{ $detailPayout->period }}</p>
                    </div>
                    <button wire:click="$set('showDetailsModal', false)" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Informations globales -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl text-xs">
                    <div>
                        <span class="text-slate-400 block">Mode de calcul :</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $detailPayout->calculation_type->label() }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Montant Brut :</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($detailPayout->gross_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Commission Agence :</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($detailPayout->commission_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Net à Reverser :</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($detailPayout->net_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <!-- Tableau des Lignes / Biens -->
                <div class="border rounded-2xl overflow-hidden border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800 font-bold uppercase text-[10px] text-slate-500">
                            <tr>
                                <th class="p-3">Bien Immobiliers</th>
                                <th class="p-3 text-right">Loyer Brut</th>
                                <th class="p-3 text-right">Commission</th>
                                <th class="p-3 text-right">IRF</th>
                                <th class="p-3 text-right">Net</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($detailPayout->items as $item)
                                <tr>
                                    <td class="p-3 font-medium text-slate-900 dark:text-white">
                                        {{ $item->property?->title }}
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $item->property?->reference }}</span>
                                    </td>
                                    <td class="p-3 text-right text-slate-700 dark:text-slate-300 font-medium">
                                        {{ number_format($item->gross_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="p-3 text-right text-slate-500">
                                        {{ number_format($item->commission_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="p-3 text-right text-slate-500">
                                        {{ number_format($item->irf_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="p-3 text-right font-bold text-slate-900 dark:text-white">
                                        {{ number_format($item->net_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Historique des règlements s'il y en a -->
                @if($detailPayout->settlements->count() > 0)
                    <div>
                        <h4 class="text-xs font-bold uppercase text-slate-500 mb-2">Historique des Règlements Effectués</h4>
                        <div class="border rounded-2xl overflow-hidden border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 dark:bg-slate-800 font-bold uppercase text-[10px] text-slate-500">
                                    <tr>
                                        <th class="p-3">Référence</th>
                                        <th class="p-3">Date</th>
                                        <th class="p-3">Moyen</th>
                                        <th class="p-3">Réf Tx</th>
                                        <th class="p-3 text-right">Montant</th>
                                        <th class="p-3 text-center">Reçu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($detailPayout->settlements as $st)
                                        <tr>
                                            <td class="p-3 font-mono font-bold text-emerald-600">{{ $st->reference }}</td>
                                            <td class="p-3 text-slate-600">{{ $st->payment_date?->format('d/m/Y') }}</td>
                                            <td class="p-3">{{ $st->payment_method?->label() }}</td>
                                            <td class="p-3 font-mono text-slate-500">{{ $st->transaction_reference ?: '—' }}</td>
                                            <td class="p-3 text-right font-bold text-slate-900 dark:text-white">{{ number_format($st->amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="p-3 text-center">
                                                @if($st->proof_document_url)
                                                    <a href="{{ $st->proof_document_url }}" target="_blank" class="text-emerald-600 hover:underline font-semibold text-[11px]">
                                                        Voir PJ
                                                    </a>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('owners.payouts.print', $detailPayout->id) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                        <x-icon name="reports" class="w-4 h-4" />
                        <span>Imprimer le décompte</span>
                    </a>
                    <x-button type="button" variant="secondary" wire:click="$set('showDetailsModal', false)">Fermer</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
