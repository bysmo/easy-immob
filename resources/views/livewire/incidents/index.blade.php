<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Incidents & Réparations</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Suivi et prise en charge des demandes d'intervention et réparations.</p>
        </div>

        @if (auth()->user()->isTenant() || auth()->user()->can('incidents.create'))
            <a href="{{ route('incidents.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 self-start sm:self-auto">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Signaler un incident</span>
            </a>
        @endif
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex flex-col md:flex-row gap-4 items-center justify-between shadow-xs">
        <div class="w-full md:w-80">
            <x-input type="search" wire:model.live.debounce.300ms="search" placeholder="Rechercher réf, titre, bien, locataire..." icon="search" class="!py-2" />
        </div>

        <div class="w-full md:w-auto flex items-center gap-3">
            <select wire:model.live="statusFilter" class="w-full md:w-48 px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                <option value="">Tous les statuts</option>
                <option value="reported">Signalé</option>
                <option value="in_progress">En cours de traitement</option>
                <option value="resolved">Traité par l'agence</option>
                <option value="closed">Clôturé & Confirmé</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4">Référence</th>
                        <th class="py-3.5 px-4">Intitulé</th>
                        <th class="py-3.5 px-4">Bien concerné</th>
                        @if (!auth()->user()->isTenant())
                            <th class="py-3.5 px-4">Locataire</th>
                        @endif
                        <th class="py-3.5 px-4">Priorité</th>
                        <th class="py-3.5 px-4">Statut</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/60 dark:divide-slate-800 text-xs">
                    @forelse ($incidents as $incident)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-900 dark:text-white">
                                {{ $incident->reference }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate font-medium text-slate-800 dark:text-slate-200">
                                {{ $incident->title }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                {{ $incident->property?->title ?? '—' }}
                            </td>
                            @if (!auth()->user()->isTenant())
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                    {{ $incident->tenant?->full_name ?? '—' }}
                                </td>
                            @endif
                            <td class="py-3.5 px-4">
                                @php
                                    $pClasses = match($incident->priority) {
                                        'urgent' => 'bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border-rose-200',
                                        'high'   => 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border-amber-200',
                                        'medium' => 'bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border-blue-200',
                                        default  => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border uppercase tracking-wider {{ $pClasses }}">
                                    {{ ucfirst($incident->priority) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $incident->status->badgeClass() }}">
                                    {{ $incident->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('incidents.show', $incident->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition text-xs">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                    <span>Consulter</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isTenant() ? 6 : 7 }}" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-icon name="alert" class="w-8 h-8 text-slate-300 dark:text-slate-600" />
                                    <p class="font-medium text-slate-600 dark:text-slate-400">Aucun incident enregistré.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($incidents->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
