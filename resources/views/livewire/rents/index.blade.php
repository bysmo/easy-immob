<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Loyers & Quittances</h1>
                <x-badge color="emerald">{{ $schedules->total() }} échéances</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Suivi des échéances de loyer, encaissements et quittances.</p>
        </div>

        <a href="{{ route('reports.export.payments') }}">
            <x-button variant="secondary" size="sm">
                <x-icon name="download" class="w-4 h-4" />
                <span>Exporter l'historique CSV</span>
            </x-button>
        </a>
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
    @if(session('error'))
        <div class="rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 p-4 text-sm text-rose-800 dark:text-rose-200 flex items-center justify-between shadow-2xs">
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher par période, locataire, bien..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les statuts</option>
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
                        <x-datatable.th field="period" :sortField="$sortField" :sortDirection="$sortDirection">Période</x-datatable.th>
                        <x-datatable.th>Locataire & Bien</x-datatable.th>
                        <x-datatable.th field="due_date" :sortField="$sortField" :sortDirection="$sortDirection">Date Échéance</x-datatable.th>
                        <x-datatable.th field="expected_amount" :sortField="$sortField" :sortDirection="$sortDirection">Loyer Dû</x-datatable.th>
                        <x-datatable.th field="paid_amount" :sortField="$sortField" :sortDirection="$sortDirection">Montant Payé</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ $schedule->period }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $schedule->lease?->tenant?->full_name ?? 'Inconnu' }}
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    {{ $schedule->lease?->property?->title ?? 'Bien inconnu' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $schedule->due_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format((float)$schedule->expected_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format((float)$schedule->paid_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$schedule->status?->badgeColor() ?? 'muted'">
                                    {{ $schedule->status?->label() ?? '—' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($schedule->status?->value !== 'paid')
                                        <button wire:click="openPaymentModal({{ $schedule->id }})" 
                                                class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1">
                                            <x-icon name="plus" class="w-3.5 h-3.5" />
                                            <span>Enregistrer un paiement</span>
                                        </button>
                                    @else
                                        <a href="{{ route('rents.receipt.print', $schedule->id) }}" 
                                           target="_blank"
                                           class="px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition inline-flex items-center gap-1">
                                            <x-icon name="printer" class="w-3.5 h-3.5" />
                                            <span>Quittance</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Aucune échéance de loyer trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Enregistrement de Paiement -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-5 shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Encaisser un loyer</h3>
                    <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="recordPayment" class="space-y-4">
                    <div>
                        <x-label for="amount">Montant (FCFA)</x-label>
                        <x-input id="amount" type="number" step="1" wire:model="amount" required />
                        @error('amount') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="payment_date">Date du règlement</x-label>
                        <x-input id="payment_date" type="date" wire:model="payment_date" required />
                        @error('payment_date') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="payment_method">Mode de règlement</x-label>
                        <select id="payment_method" wire:model="payment_method" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-sm font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm['value'] }}">{{ $pm['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-label for="notes">Notes / Numéro de virement/chèque</x-label>
                        <x-input id="notes" type="text" wire:model="notes" placeholder="Optionnel..." />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showPaymentModal', false)">Annuler</x-button>
                        <x-button type="submit" variant="primary">Confirmer l'encaissement</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
