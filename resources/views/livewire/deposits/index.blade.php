<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des cautions & dépôts de garantie</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Suivez la réception, les retenues motivées et la restitution des cautions.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
        <div class="max-w-sm flex-1">
            <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher par bien, locataire, réf contrat..." />
        </div>
        <div>
            <select wire:model.live="statusFilter"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tous les statuts</option>
                @foreach($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 text-left">Contrat / Bien</th>
                    <th class="px-5 py-3 text-left">Locataire</th>
                    <th class="px-5 py-3 text-left">Caution prévue</th>
                    <th class="px-5 py-3 text-left">Reçue</th>
                    <th class="px-5 py-3 text-left">Retenue (Motif)</th>
                    <th class="px-5 py-3 text-left">Restituée</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($deposits as $deposit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div class="font-mono text-xs text-gray-400">{{ $deposit->lease?->reference }}</div>
                            <div>{{ $deposit->lease?->property?->title }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $deposit->lease?->tenant?->full_name }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format((float)$deposit->expected_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 text-green-600 font-medium">
                            {{ number_format((float)$deposit->received_amount, 0, ',', ' ') }} FCFA
                            @if($deposit->received_at)
                                <div class="text-xs text-gray-400 font-normal">le {{ $deposit->received_at->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-red-600">
                            @if((float)$deposit->retained_amount > 0)
                                <div class="font-medium">{{ number_format((float)$deposit->retained_amount, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-gray-400 italic font-normal" title="{{ $deposit->retention_reason }}">
                                    "{{ Str::limit($deposit->retention_reason, 20) }}"
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            @if((float)$deposit->refunded_amount > 0)
                                <div class="font-medium">{{ number_format((float)$deposit->refunded_amount, 0, ',', ' ') }} FCFA</div>
                                @if($deposit->refunded_at)
                                    <div class="text-xs text-gray-400">le {{ $deposit->refunded_at->format('d/m/Y') }}</div>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$deposit->status->badgeColor()">
                                {{ $deposit->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @if($deposit->status->value === 'pending')
                                @can('deposits.manage')
                                    <button wire:click="openReceiveModal({{ $deposit->id }})"
                                            class="text-green-600 dark:text-green-400 hover:underline text-xs font-medium">
                                        Encaisser
                                    </button>
                                @endcan
                            @elseif($deposit->status->value === 'held')
                                @can('deposits.manage')
                                    <button wire:click="openRefundModal({{ $deposit->id }})"
                                            class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                        Restituer / Clôturer
                                    </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                            Aucune caution répertoriée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Encaisser la caution --}}
    @if($showReceiveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Encaisser la caution
                </h3>

                <form wire:submit="processReceive" class="space-y-4">
                    <div>
                        <x-label for="receive_amount">Montant encaissé (FCFA)</x-label>
                        <x-input wire:model="receive_amount" type="number" step="1000" id="receive_amount" autofocus />
                        @error('receive_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="received_at">Date de réception</x-label>
                        <x-input wire:model="received_at" type="date" id="received_at" />
                        @error('received_at') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showReceiveModal', false)">
                            Annuler
                        </x-button>
                        <x-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Confirmer l'encaissement</span>
                            <span wire:loading>Enregistrement...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Restituer / Clôturer la caution --}}
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Restitution ou retenue de la caution
                </h3>

                <form wire:submit="processRefund" class="space-y-4">
                    <div>
                        <x-label for="refunded_amount">Montant restitué au locataire (FCFA)</x-label>
                        <x-input wire:model="refunded_amount" type="number" step="1000" id="refunded_amount" autofocus />
                        @error('refunded_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="retained_amount">Montant retenu par le bailleur (FCFA)</x-label>
                        <x-input wire:model="retained_amount" type="number" step="1000" id="retained_amount" />
                        @error('retained_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="retention_reason">Motif de la retenue (Obligatoire si retenue > 0)</x-label>
                        <x-input wire:model="retention_reason" type="text" id="retention_reason" placeholder="Ex: Réparation peinture dégradée, facture eau impayée..." />
                        @error('retention_reason') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="refunded_at">Date de la restitution</x-label>
                        <x-input wire:model="refunded_at" type="date" id="refunded_at" />
                        @error('refunded_at') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showRefundModal', false)">
                            Annuler
                        </x-button>
                        <x-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Valider la restitution</span>
                            <span wire:loading>Traitement...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
