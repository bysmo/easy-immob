<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Cautions & Dépôts de Garantie</h1>
                <x-badge color="amber">{{ $deposits->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Suivi de l'encaissement et de la restitution des garanties locatives.</p>
        </div>
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
    <x-datatable.controls placeholder="Rechercher par locataire, bien, réf contrat..." :perPage="$perPage" :search="$search">
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
                        <x-datatable.th>Contrat / Locataire & Bien</x-datatable.th>
                        <x-datatable.th field="expected_amount" :sortField="$sortField" :sortDirection="$sortDirection">Montant Attendu</x-datatable.th>
                        <x-datatable.th field="received_amount" :sortField="$sortField" :sortDirection="$sortDirection">Montant Reçu</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $deposit->lease?->tenant?->full_name ?? 'Inconnu' }}
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    {{ $deposit->lease?->property?->title ?? 'Bien inconnu' }} (Réf: {{ $deposit->lease?->reference }})
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                {{ number_format((float)$deposit->expected_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format((float)$deposit->received_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$deposit->status->badgeColor()">
                                    {{ $deposit->status->label() }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($deposit->status->value === 'pending')
                                        <button wire:click="openReceiveModal({{ $deposit->id }})"
                                                class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition">
                                            Réceptionner
                                        </button>
                                    @elseif($deposit->status->value === 'held')
                                        <button wire:click="openRefundModal({{ $deposit->id }})"
                                                class="px-2.5 py-1 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-2xs transition">
                                            Restituer / Clôturer
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucune garantie ou caution enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deposits->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>

    <!-- Modals (Reception / Restitution) -->
    @if($showReceiveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Enregistrer la réception de la caution</h3>
                <form wire:submit="processReceive" class="space-y-4">
                    <div>
                        <x-label for="receive_amount">Montant encaissé (FCFA)</x-label>
                        <x-input id="receive_amount" type="number" wire:model="receive_amount" required />
                    </div>
                    <div>
                        <x-label for="received_at">Date de réception</x-label>
                        <x-input id="received_at" type="date" wire:model="received_at" required />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showReceiveModal', false)">Annuler</x-button>
                        <x-button type="submit" variant="primary">Confirmer</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
