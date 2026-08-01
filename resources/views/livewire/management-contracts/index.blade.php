<div>
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Mandats de Gestion Immobilière</h1>
            <p class="text-sm text-base-content/70">Gérez les contrats de mandat confiés par vos propriétaires à l'agence.</p>
        </div>
        <div>
            <a href="{{ route('management-contracts.create') }}" class="btn btn-primary gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau Mandat de Gestion
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="label text-xs font-semibold">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Référence, titre, propriétaire..." class="input input-bordered w-full input-sm" />
                </div>
                <div>
                    <label class="label text-xs font-semibold">Filtrer par statut</label>
                    <select wire:model.live="statusFilter" class="select select-bordered w-full select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="draft">Brouillon</option>
                        <option value="active">Actif</option>
                        <option value="expired">Expiré</option>
                        <option value="terminated">Résilié</option>
                    </select>
                </div>
                <div>
                    <label class="label text-xs font-semibold">Filtrer par propriétaire</label>
                    <select wire:model.live="ownerFilter" class="select select-bordered w-full select-sm">
                        <option value="">Tous les propriétaires</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card bg-base-100 shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Propriétaire (Mandant)</th>
                        <th>Commission Agence</th>
                        <th>Biens gérés</th>
                        <th>Période d'effet</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr class="hover">
                            <td class="font-bold">
                                <a href="{{ route('management-contracts.show', $contract->id) }}" class="text-primary hover:underline">
                                    {{ $contract->reference }}
                                </a>
                            </td>
                            <td>
                                <div class="font-medium">{{ $contract->owner?->full_name }}</div>
                                <div class="text-xs text-base-content/60">{{ $contract->owner?->phone }}</div>
                            </td>
                            <td>
                                <span class="badge badge-outline font-semibold">
                                    {{ $contract->formatted_commission }}
                                </span>
                            </td>
                            <td>
                                @if($contract->properties->count() > 0)
                                    <div class="text-sm font-semibold text-success">
                                        {{ $contract->properties->count() }} bien(s) lié(s)
                                    </div>
                                    <div class="text-xs text-base-content/60 max-w-xs truncate">
                                        {{ $contract->properties->pluck('title')->implode(', ') }}
                                    </div>
                                @else
                                    <span class="text-xs text-base-content/40 italic">Aucun bien rattaché</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-xs">
                                    <div>Début : <span class="font-medium">{{ $contract->start_date?->format('d/m/Y') }}</span></div>
                                    <div class="text-base-content/60">Durée : {{ $contract->duration_months }} mois</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $contract->status->badgeClass() }}">
                                    {{ $contract->status->label() }}
                                </span>
                            </td>
                            <td class="text-right space-x-1">
                                <a href="{{ route('management-contracts.show', $contract->id) }}" class="btn btn-xs btn-ghost text-info" title="Consulter">
                                    👁️
                                </a>
                                <a href="{{ route('management-contracts.print', $contract->id) }}" target="_blank" class="btn btn-xs btn-ghost text-success" title="Imprimer le Mandat PDF">
                                    🖨️
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                Aucun mandat de gestion trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>
