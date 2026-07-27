<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:text-primary-600">← Retour</a>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Relevés de compte propriétaires</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Calcul des encaissements par propriétaire, déduction des honoraires agence et net à reverser.</p>
        </div>

        @if($statement && $selectedOwnerId)
            <a href="{{ route('reports.owner-statements.print', ['ownerId' => $selectedOwnerId, 'fee' => $managementFeePercentage, 'period' => $period]) }}" target="_blank">
                <x-button>🖨️ Imprimer le relevé / PDF</x-button>
            </a>
        @endif
    </div>

    {{-- Filtres --}}
    <x-card>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <x-label for="selectedOwnerId">Propriétaire</x-label>
                <select wire:model.live="selectedOwnerId" id="selectedOwnerId"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">— Sélectionner un propriétaire —</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-label for="managementFeePercentage">Taux d'honoraires agence (%)</x-label>
                <x-input wire:model.live.debounce.300ms="managementFeePercentage" type="number" step="0.5" id="managementFeePercentage" />
            </div>

            <div>
                <x-label for="period">Période (AAAAMM ex: 2026-08)</x-label>
                <x-input wire:model.live.debounce.300ms="period" type="text" id="period" placeholder="Toutes les périodes" />
            </div>
        </div>
    </x-card>

    {{-- Synthèse Relevé --}}
    @if($statement)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-card>
                <div class="text-xs font-semibold text-gray-500 uppercase">Encaissements Bruts Perçus</div>
                <div class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                    {{ number_format($statement['total_collected'], 0, ',', ' ') }} FCFA
                </div>
            </x-card>

            <x-card>
                <div class="text-xs font-semibold text-gray-500 uppercase">Honoraires Agence ({{ $statement['management_fee_percentage'] }}%)</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                    {{ number_format($statement['management_fee_amount'], 0, ',', ' ') }} FCFA
                </div>
            </x-card>

            <x-card class="bg-primary-50/50 dark:bg-primary-900/10 border-primary-100 dark:border-primary-800">
                <div class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase">Montant Net à Reverser</div>
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                    {{ number_format($statement['net_payable'], 0, ',', ' ') }} FCFA
                </div>
            </x-card>
        </div>

        {{-- Détail des échéances du propriétaire --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-3 text-left">Période</th>
                        <th class="px-5 py-3 text-left">Bien</th>
                        <th class="px-5 py-3 text-left">Locataire</th>
                        <th class="px-5 py-3 text-left">Attendu</th>
                        <th class="px-5 py-3 text-left">Encaissements</th>
                        <th class="px-5 py-3 text-left">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($statement['schedules'] as $sched)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $sched->period }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $sched->lease?->property?->title }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $sched->lease?->tenant?->full_name }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ number_format((float)$sched->expected_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3 text-green-600 font-medium">{{ number_format((float)$sched->paid_amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3">
                                <x-badge :variant="$sched->status->badgeColor()">
                                    {{ $sched->status->label() }}
                                </x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                                Aucun encaissement pour ce propriétaire sur la période sélectionnée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
