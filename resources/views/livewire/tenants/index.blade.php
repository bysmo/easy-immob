<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des locataires</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Liste des locataires gérés par votre agence.</p>
        </div>
        @can('tenants.create')
            <a href="{{ route('tenants.create') }}">
                <x-button>+ Nouveau locataire</x-button>
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
        <x-input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher nom, email, tél, réf..." />
    </div>

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 text-left">Référence</th>
                    <th class="px-5 py-3 text-left">Nom & Prénom</th>
                    <th class="px-5 py-3 text-left">Contact</th>
                    <th class="px-5 py-3 text-left">Contact d'urgence</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $tenant->reference }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $tenant->full_name }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                            <div>{{ $tenant->email ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $tenant->phone ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $tenant->emergency_contact ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$tenant->status === 'active' ? 'success' : 'muted'">
                                {{ $tenant->status === 'active' ? 'Actif' : 'Inactif' }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @can('tenants.update')
                                <a href="{{ route('tenants.edit', $tenant->id) }}"
                                   class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                    Modifier
                                </a>
                            @endcan
                            @can('tenants.delete')
                                <button wire:click="delete({{ $tenant->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir désactiver/supprimer ce locataire ?"
                                        class="text-red-600 dark:text-red-400 hover:underline text-xs font-medium">
                                    Supprimer
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            Aucun locataire trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tenants->hasPages())
        <div class="pt-2">
            {{ $tenants->links() }}
        </div>
    @endif
</div>
