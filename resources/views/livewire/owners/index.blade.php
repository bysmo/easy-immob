<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Bailleurs</h1>
                <x-badge color="indigo">{{ $owners->total() }} au total</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gérez l'ensemble des bailleurs et mandataires enregistrés dans l'agence.</p>
        </div>

        <div class="flex items-center gap-2">
            @can('owners.create')
                <!-- Import Button -->
                <button wire:click="openImportModal"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                    <x-icon name="upload" class="w-4 h-4 text-emerald-500" />
                    <span>Importer CSV/Excel</span>
                </button>

                <a href="{{ route('owners.create') }}">
                    <x-button variant="primary" class="shadow-md shadow-emerald-600/20">
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Nouveau bailleur</span>
                    </x-button>
                </a>
            @endcan
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 p-4 text-sm text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <x-icon name="check" class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- DataTables Controls Top Bar (Filters & Top-Right perPage selector) -->
    <x-datatable.controls placeholder="Rechercher par nom, email, référence..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les statuts</option>
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
            </select>
        </x-slot:filters>
    </x-datatable.controls>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="reference" :sortField="$sortField" :sortDirection="$sortDirection">Référence</x-datatable.th>
                        <x-datatable.th field="first_name" :sortField="$sortField" :sortDirection="$sortDirection">Bailleur</x-datatable.th>
                        <x-datatable.th field="email" :sortField="$sortField" :sortDirection="$sortDirection">Coordonnées</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th>Portail</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($owners as $owner)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                    {{ $owner->reference }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 shrink-0">
                                        {{ strtoupper(substr($owner->first_name, 0, 1) . substr($owner->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $owner->full_name }}
                                        </div>
                                        @if($owner->company_name)
                                            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                                <x-icon name="building" class="w-3 h-3" />
                                                <span>{{ $owner->company_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="text-slate-800 dark:text-slate-200 font-semibold">{{ $owner->email ?? '—' }}</div>
                                <div class="text-slate-400 mt-0.5">{{ $owner->phone ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$owner->status === 'active' ? 'success' : 'muted'">
                                    {{ $owner->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4">
                                @if ($owner->hasPortalAccess())
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Portail actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-medium border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Portail inactif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('owners.update')
                                        @if($owner->email)
                                            <button type="button"
                                                    @click="$dispatch('open-confirm', {
                                                        title: @js($owner->hasPortalAccess() ? "Renvoyer l'invitation portail" : "Envoyer l'invitation portail"),
                                                        message: @js("Voulez-vous envoyer un lien d'accès au portail bailleur à {$owner->email} ?"),
                                                        confirmText: @js("Envoyer l'invitation"),
                                                        variant: 'primary',
                                                        onConfirm: () => $wire.sendInvitation({{ $owner->id }})
                                                    })"
                                                    class="p-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors cursor-pointer"
                                                    title="{{ $owner->hasPortalAccess() ? 'Renvoyer l\'invitation portail' : 'Envoyer l\'invitation portail' }}">
                                                <x-icon name="notifications" class="w-4 h-4" />
                                            </button>
                                        @endif

                                        <a href="{{ route('owners.edit', $owner->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                           title="Modifier">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </a>
                                    @endcan

                                    @can('owners.delete')
                                        <button type="button"
                                                @click="$dispatch('open-confirm', {
                                                    title: 'Supprimer le bailleur',
                                                    message: 'Êtes-vous sûr de vouloir supprimer le bailleur {{ $owner->full_name }} ({{ $owner->reference }}) ? Cette action est irréversible.',
                                                    confirmText: 'Supprimer le bailleur',
                                                    variant: 'danger',
                                                    onConfirm: () => $wire.delete({{ $owner->id }})
                                                })"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                title="Supprimer">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucun bailleur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $owners->links() }}
            </div>
        @endif
    </div>

    <!-- ======================================================= -->
    <!-- MODAL IMPORT CSV / EXCEL                                 -->
    <!-- ======================================================= -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeImportModal"></div>

        <!-- Modal Panel -->
        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/30">
                        <x-icon name="upload" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Importer des Bailleurs</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">CSV ou Excel (.xlsx)</p>
                    </div>
                </div>
                <button wire:click="closeImportModal"
                        class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-5">

                {{-- Résultat import --}}
                @if($importedCount !== null)
                    <div class="rounded-xl border {{ count($importErrors) > 0 ? 'border-amber-200 bg-amber-50 dark:border-amber-800/60 dark:bg-amber-950/30' : 'border-emerald-200 bg-emerald-50 dark:border-emerald-800/60 dark:bg-emerald-950/30' }} p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <x-icon name="check" class="w-5 h-5 {{ count($importErrors) > 0 ? 'text-amber-500' : 'text-emerald-500' }}" />
                            <span class="font-semibold text-sm {{ count($importErrors) > 0 ? 'text-amber-800 dark:text-amber-200' : 'text-emerald-800 dark:text-emerald-200' }}">
                                {{ $importedCount }} bailleur(s) importé(s) avec succès
                                @if(count($importErrors) > 0)
                                    — {{ count($importErrors) }} ligne(s) ignorée(s)
                                @endif
                            </span>
                        </div>
                        @if(count($importErrors) > 0)
                            <div class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-1 scrollbar-thin">
                                @foreach($importErrors as $err)
                                    <div class="text-xs bg-white dark:bg-slate-800 rounded-lg p-2 border border-amber-100 dark:border-amber-800/40">
                                        <span class="font-bold text-amber-700 dark:text-amber-400">Ligne {{ $err['row'] }} – {{ $err['name'] }}</span>
                                        <ul class="mt-1 list-disc list-inside text-slate-600 dark:text-slate-400">
                                            @foreach($err['errors'] as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Télécharger le modèle --}}
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-4 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Télécharger le modèle</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Colonnes : prenom, nom, societe, email, telephone, adresse, statut</p>
                    </div>
                    <a href="{{ asset('templates/import-bailleurs.csv') }}" download
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/40 transition-colors shadow-sm">
                        <x-icon name="download" class="w-4 h-4" />
                        <span>Modèle CSV</span>
                    </a>
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Sélectionner le fichier <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-emerald-400 dark:hover:border-emerald-500 transition-colors cursor-pointer"
                         onclick="document.getElementById('import-owners-file').click()">
                        <x-icon name="upload" class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                        @if($importFile)
                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ $importFile->getClientOriginalName() }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ round($importFile->getSize() / 1024, 1) }} Ko</p>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-400">Glissez-déposez ou <span class="text-emerald-600 font-medium">parcourez</span></p>
                            <p class="text-xs text-slate-400 mt-1">CSV, XLSX, XLS — Max 5 Mo</p>
                        @endif
                        <input type="file" id="import-owners-file" wire:model="importFile"
                               accept=".csv,.xlsx,.xls" class="sr-only">
                    </div>
                    @error('importFile')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                            <x-icon name="alert" class="w-3 h-3" />
                            {{ $message }}
                        </p>
                    @enderror
                    <div wire:loading wire:target="importFile" class="mt-2 text-xs text-slate-400 flex items-center gap-1.5">
                        <svg class="animate-spin w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Chargement du fichier...
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 flex items-center justify-end gap-3">
                <button wire:click="closeImportModal"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Fermer
                </button>
                <button wire:click="importOwners"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/25 transition-all">
                    <span wire:loading.remove wire:target="importOwners">
                        <x-icon name="upload" class="w-4 h-4 inline -mt-0.5 mr-1" />
                        Lancer l'import
                    </span>
                    <span wire:loading wire:target="importOwners" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Importation en cours...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
