<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des contrats de location</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Liste et suivi des baux enregistrés.</p>
        </div>
        @can('leases.create')
            <a href="{{ route('leases.create') }}">
                <x-button>+ Nouveau contrat</x-button>
            </a>
        @endcan
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
            <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher par réf, bien, locataire..." />
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
                    <th class="px-5 py-3 text-left">Locataire</th>
                    <th class="px-5 py-3 text-left">Période</th>
                    <th class="px-5 py-3 text-left">Total Mensuel</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($leases as $lease)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $lease->reference }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <div>{{ $lease->property?->title ?? '—' }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $lease->property?->city }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            {{ $lease->tenant?->full_name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">
                            Du {{ $lease->start_date?->format('d/m/Y') }} au {{ $lease->end_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ number_format($lease->total_monthly_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$lease->status->badgeColor()">
                                {{ $lease->status->label() }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            <a href="{{ route('leases.show', $lease->id) }}"
                               class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                Consulter
                            </a>

                            @if($lease->status->value === 'draft' || $lease->status->value === 'pending_signature')
                                @can('leases.update')
                                    <button wire:click="activate({{ $lease->id }})"
                                            wire:confirm="Activer ce contrat ? Le bien passera à 'Occupé' et les échéances seront créées."
                                            class="text-green-600 dark:text-green-400 hover:underline text-xs font-medium">
                                        Activer
                                    </button>
                                @endcan
                            @elseif($lease->status->value === 'active')
                                @can('leases.update')
                                    <button wire:click="terminate({{ $lease->id }})"
                                            wire:confirm="Résilier ce contrat ?"
                                            class="text-red-600 dark:text-red-400 hover:underline text-xs font-medium">
                                        Résilier
                                    </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                            Aucun contrat de location trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($leases->hasPages())
        <div class="pt-2">
            {{ $leases->links() }}
        </div>
    @endif
</div>
