<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Contrats de Location</h1>
                <x-badge color="teal">{{ $leases->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Baux locatifs en cours, brouillons et contrats résiliés.</p>
        </div>

        @can('leases.create')
            <a href="{{ route('leases.create') }}">
                <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Créer un contrat</span>
                </x-button>
            </a>
        @endcan
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 p-4 text-sm text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <x-icon name="check" class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher par référence, bien, locataire..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les statuts</option>
                <option value="expiring_soon">⏰ Arrivant à échéance (≤ 60 jours)</option>
                @foreach($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-datatable.controls>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="reference" :sortField="$sortField" :sortDirection="$sortDirection">Référence</x-datatable.th>
                        <x-datatable.th>Bien & Locataire</x-datatable.th>
                        <x-datatable.th field="start_date" :sortField="$sortField" :sortDirection="$sortDirection">Période du bail</x-datatable.th>
                        <x-datatable.th field="rent_amount" :sortField="$sortField" :sortDirection="$sortDirection">Loyer mensuel</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($leases as $lease)
                        @php
                            $isExpiringSoon = $lease->status?->value === 'active' && $lease->end_date && $lease->end_date->diffInDays(now(), false) >= -60;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $isExpiringSoon ? 'bg-amber-50/30 dark:bg-amber-950/20' : '' }}">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $lease->reference }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $lease->property?->title ?? 'Bien inconnu' }}
                                </div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                    Locataire: {{ $lease->tenant?->full_name ?? 'Inconnu' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                <div>Du {{ $lease->start_date?->format('d/m/Y') }}</div>
                                <div class="{{ $isExpiringSoon ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-400' }}">
                                    Au {{ $lease->end_date ? $lease->end_date->format('d/m/Y') : 'Indéterminée' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format((float)$lease->rent_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 space-y-1">
                                <x-badge :variant="$lease->status?->badgeColor() ?? 'muted'">
                                    {{ $lease->status?->label() ?? '—' }}
                                </x-badge>
                                @if($isExpiringSoon)
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        Échéance proche
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('leases.show', $lease->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                       title="Détails">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>

                                    @if($lease->status?->value === 'draft')
                                        <button type="button"
                                                @click="$dispatch('open-confirm', {
                                                    title: 'Activer le contrat de location',
                                                    message: 'Voulez-vous activer le bail {{ $lease->reference }} ? Le bien passera au statut Occupé et l\'échéancier sera généré.',
                                                    confirmText: 'Activer le contrat',
                                                    variant: 'success',
                                                    onConfirm: () => $wire.activate({{ $lease->id }})
                                                })"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-200 dark:hover:bg-emerald-900 transition cursor-pointer">
                                            Activer
                                        </button>
                                    @endif

                                    @if(in_array($lease->status?->value, ['active', 'expired']))
                                        <!-- Actions d'échéance et relance -->
                                        <button type="button" wire:click="notifyTenant({{ $lease->id }})"
                                                class="px-2 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-semibold hover:bg-sky-100 transition"
                                                title="Relancer le locataire par notification">
                                            Relancer Locataire
                                        </button>
                                        <button type="button" wire:click="notifyAgency({{ $lease->id }})"
                                                class="px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-xs font-semibold hover:bg-indigo-100 transition"
                                                title="Alerter l'agence par notification">
                                            Alerte Agence
                                        </button>
                                        <button type="button" wire:click="openRenewModal({{ $lease->id }})"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition"
                                                title="Renouveler ce contrat de bail">
                                            Renouveler
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Aucun contrat de location trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $leases->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de Renouvellement de Bail -->
    @if($showRenewModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-900 p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <x-icon name="document-text" class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Renouvellement du Contrat de Bail</h3>
                    </div>
                    <button type="button" wire:click="closeRenewModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label for="new_end_date" :required="true">Nouvelle date d'échéance (Fin de contrat)</x-label>
                        <x-input wire:model="new_end_date" type="date" id="new_end_date" :error="$errors->first('new_end_date')" />
                    </div>

                    <div>
                        <x-label for="new_rent_amount" :required="true">Loyer mensuel du bail (FCFA)</x-label>
                        <x-input wire:model="new_rent_amount" type="number" step="1000" id="new_rent_amount" icon="wallet" :error="$errors->first('new_rent_amount')" />
                        <p class="text-[11px] text-slate-500 mt-1">Si le montant diffère de l'ancien loyer, une entrée d'historique de révision sera automatiquement créée.</p>
                    </div>

                    <div>
                        <x-label for="renewal_notes">Notes / Observations de renouvellement</x-label>
                        <textarea wire:model="renewal_notes" id="renewal_notes" rows="3" placeholder="Ex: Renouvellement d'un an avec l'accord des deux parties..."
                                  class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs shadow-2xs p-3 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                        @error('renewal_notes') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" wire:click="closeRenewModal" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition">
                        Annuler
                    </button>
                    <button type="button" wire:click="renewLease" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition flex items-center gap-1.5">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Valider le renouvellement</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
