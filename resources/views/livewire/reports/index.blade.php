<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Centre de reporting & bilan financier</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Statistiques d'encaissement, recouvrement et rapports comptables.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.owner-statements') }}">
                <x-button variant="secondary">👤 Relevés Propriétaires</x-button>
            </a>
            <a href="{{ route('reports.export.payments') }}">
                <x-button>📊 Exporter Encaissements (CSV)</x-button>
            </a>
        </div>
    </div>

    {{-- Filtres de période --}}
    <x-card>
        <div class="flex flex-col sm:flex-row gap-4 items-end">
            <div>
                <x-label for="startDate">Date de début</x-label>
                <x-input wire:model.live="startDate" type="date" id="startDate" />
            </div>

            <div>
                <x-label for="endDate">Date de fin</x-label>
                <x-input wire:model.live="endDate" type="date" id="endDate" />
            </div>

            <div class="text-sm text-gray-500 pb-2">
                Filtrer la période d'échéance des loyers.
            </div>
        </div>
    </x-card>

    {{-- Cartes d'indicateurs (KPIs) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card class="bg-blue-50/50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-800">
            <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">Loyers Attendus</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                {{ number_format($summary['expected_total'], 0, ',', ' ') }} FCFA
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ $summary['schedules_count'] }} échéance(s) au total</div>
        </x-card>

        <x-card class="bg-green-50/50 dark:bg-green-900/10 border-green-100 dark:border-green-800">
            <div class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Loyers Encaissés</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                {{ number_format($summary['collected_total'], 0, ',', ' ') }} FCFA
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ $summary['paid_schedules_count'] }} réglée(s) intégralement</div>
        </x-card>

        <x-card class="bg-red-50/50 dark:bg-red-900/10 border-red-100 dark:border-red-800">
            <div class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase">Solde Reste à Recouvrer</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                {{ number_format($summary['remaining_total'], 0, ',', ' ') }} FCFA
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ $summary['overdue_schedules_count'] }} échéance(s) en retard</div>
        </x-card>

        <x-card class="bg-amber-50/50 dark:bg-amber-900/10 border-amber-100 dark:border-amber-800">
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase">Taux de Recouvrement</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                {{ $summary['collection_rate'] }} %
            </div>
            <div class="text-xs text-gray-500 mt-1">Niveau d'encaissement période</div>
        </x-card>
    </div>
</div>
