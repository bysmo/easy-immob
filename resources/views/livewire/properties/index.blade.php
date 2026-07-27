<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des biens immobiliers</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Catalogue des biens gérés par votre agence.</p>
        </div>
        @can('properties.create')
            <a href="{{ route('properties.create') }}">
                <x-button>+ Nouveau bien</x-button>
            </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtres et Recherche --}}
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
        <div class="max-w-sm flex-1">
            <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher titre, ville, adresse, réf..." />
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
                    <th class="px-5 py-3 text-left">Référence</th>
                    <th class="px-5 py-3 text-left">Bien</th>
                    <th class="px-5 py-3 text-left">Propriétaire</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-left">Loyer</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($properties as $property)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $property->reference }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div>{{ $property->title }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $property->city }} @if($property->neighborhood)({{ $property->neighborhood }})@endif</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $property->owner?->full_name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $property->propertyType?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format((float) $property->rent_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$property->status->badgeColor()">
                                {{ $property->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @can('properties.update')
                                <a href="{{ route('properties.edit', $property->id) }}"
                                   class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                    Modifier
                                </a>
                            @endcan
                            @can('properties.delete')
                                <button wire:click="delete({{ $property->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce bien ?"
                                        class="text-red-600 dark:text-red-400 hover:underline text-xs font-medium">
                                    Supprimer
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                            Aucun bien immobilier trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($properties->hasPages())
        <div class="pt-2">
            {{ $properties->links() }}
        </div>
    @endif
</div>
