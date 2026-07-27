<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Suivi des loyers & encaissements</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Échéances de loyers mensuelles et enregistrement des paiements.</p>
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
            <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher par période (2026-08), bien, locataire..." />
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
                    <th class="px-5 py-3 text-left">Période</th>
                    <th class="px-5 py-3 text-left">Locataire & Bien</th>
                    <th class="px-5 py-3 text-left">Date limite</th>
                    <th class="px-5 py-3 text-left">Attendu</th>
                    <th class="px-5 py-3 text-left">Payé</th>
                    <th class="px-5 py-3 text-left">Reste à payer</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($schedules as $sched)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $sched->period }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div>{{ $sched->lease?->tenant?->full_name }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $sched->lease?->property?->title }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $sched->due_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format((float)$sched->expected_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 text-green-600 font-medium">
                            {{ number_format((float)$sched->paid_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 font-medium text-red-600">
                            {{ number_format((float)$sched->remaining_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$sched->status->badgeColor()">
                                {{ $sched->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @if((float)$sched->remaining_amount > 0)
                                @can('rents.record-payment')
                                    <button wire:click="openPaymentModal({{ $sched->id }})"
                                            class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                        Payer
                                    </button>
                                @endcan
                            @endif

                            @if($sched->status->value === 'paid')
                                <a href="{{ route('rents.receipt.print', $sched->id) }}" target="_blank"
                                   class="text-green-600 dark:text-green-400 hover:underline text-xs font-medium">
                                    📄 Quittance
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                            Aucune échéance de loyer trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($schedules->hasPages())
        <div class="pt-2">
            {{ $schedules->links() }}
        </div>
    @endif

    {{-- Modal Enregistrer un paiement --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Enregistrer un règlement
                </h3>

                <form wire:submit="recordPayment" class="space-y-4">
                    <div>
                        <x-label for="amount">Montant réglé (FCFA)</x-label>
                        <x-input wire:model="amount" type="number" step="100" id="amount" autofocus />
                        @error('amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="payment_date">Date du règlement</x-label>
                        <x-input wire:model="payment_date" type="date" id="payment_date" />
                        @error('payment_date') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="payment_method">Mode de règlement</x-label>
                        <select wire:model="payment_method" id="payment_method"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm['value'] }}">{{ $pm['label'] }}</option>
                            @endforeach
                        </select>
                        @error('payment_method') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="notes">Notes / Référence du virement (facultatif)</x-label>
                        <x-input wire:model="notes" type="text" id="notes" placeholder="Réf virement, N° chèque..." />
                        @error('notes') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showPaymentModal', false)">
                            Annuler
                        </x-button>
                        <x-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Valider le paiement</span>
                            <span wire:loading>Enregistrement...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
