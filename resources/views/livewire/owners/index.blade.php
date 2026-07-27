<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des propriétaires</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Liste des propriétaires enregistrés dans votre agence.</p>
        </div>
        @can('owners.create')
            <a href="{{ route('owners.create') }}">
                <x-button>+ Nouveau propriétaire</x-button>
            </a>
        @endcan
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Barre de recherche --}}
    <div class="max-w-sm">
        <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher par nom, email, réf..." />
    </div>

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 text-left">Référence</th>
                    <th class="px-5 py-3 text-left">Nom / Raison sociale</th>
                    <th class="px-5 py-3 text-left">Contact</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($owners as $owner)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $owner->reference }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $owner->full_name }}
                            @if($owner->company_name)
                                <span class="block text-xs text-gray-400 font-normal">Représentant: {{ $owner->first_name }} {{ $owner->last_name }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            <div>{{ $owner->email ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $owner->phone ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$owner->status === 'active' ? 'success' : 'muted'">
                                {{ $owner->status === 'active' ? 'Actif' : 'Inactif' }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @can('owners.update')
                                <a href="{{ route('owners.edit', $owner->id) }}"
                                   class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                    Modifier
                                </a>
                            @endcan
                            @can('owners.delete')
                                <button wire:click="delete({{ $owner->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir désactiver/supprimer ce propriétaire ?"
                                        class="text-red-600 dark:text-red-400 hover:underline text-xs font-medium">
                                    Supprimer
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            Aucun propriétaire trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($owners->hasPages())
        <div class="pt-2">
            {{ $owners->links() }}
        </div>
    @endif
</div>
