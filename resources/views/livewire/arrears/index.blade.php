<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Recouvrement & Gestion des impayés</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Détection automatique et dossiers de relance des loyers en retard.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
        <div class="max-w-sm flex-1">
            <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher locataire, bien..." />
        </div>
        <div>
            <select wire:model.live="severityFilter"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Toutes les sévérités</option>
                @foreach($severityOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
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
                    <th class="px-5 py-3 text-left">Locataire & Bien</th>
                    <th class="px-5 py-3 text-left">Période</th>
                    <th class="px-5 py-3 text-left">Loyer dû</th>
                    <th class="px-5 py-3 text-left">Montant restant</th>
                    <th class="px-5 py-3 text-left">Retard depuis le</th>
                    <th class="px-5 py-3 text-left">Sévérité</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($arrears as $arrear)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div>{{ $arrear->tenant?->full_name }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $arrear->lease?->property?->title }}</div>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                            {{ $arrear->rentSchedule?->period }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format((float)$arrear->amount_due, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 font-medium text-red-600">
                            {{ number_format((float)$arrear->remaining_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $arrear->first_overdue_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$arrear->severity->badgeColor()">
                                {{ $arrear->severity->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$arrear->status->badgeColor()">
                                {{ $arrear->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            <a href="{{ route('arrears.show', $arrear->id) }}"
                               class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                Consulter le dossier
                            </a>
                            @if($arrear->status->value === 'open')
                                @can('arrears.manage')
                                    <button wire:click="sendReminder({{ $arrear->id }})"
                                            wire:confirm="Envoyer une relance par e-mail au locataire ?"
                                            class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">
                                        📩 Relancer
                                    </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                            Aucun impayé en cours.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($arrears->hasPages())
        <div class="pt-2">
            {{ $arrears->links() }}
        </div>
    @endif
</div>
