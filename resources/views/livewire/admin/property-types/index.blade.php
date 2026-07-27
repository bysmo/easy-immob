<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Types de Biens</h1>
                <x-badge color="purple">{{ $propertyTypes->total() }} catégories</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Référentiel des catégories de biens immobiliers de l'agence.</p>
        </div>

        <x-button wire:click="openCreateModal" variant="primary" class="shadow-md shadow-emerald-600/20">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nouveau type de bien</span>
        </x-button>
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

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher un type de bien..." :perPage="$perPage" :search="$search" />

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="name" :sortField="$sortField" :sortDirection="$sortDirection">Nom de la catégorie</x-datatable.th>
                        <x-datatable.th field="code" :sortField="$sortField" :sortDirection="$sortDirection">Code / Slug</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($propertyTypes as $type)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $type->name }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                {{ $type->code ?? \Illuminate\Support\Str::slug($type->name) }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$type->status === 'active' ? 'success' : 'muted'">
                                    {{ $type->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="openEditModal({{ $type->id }})" 
                                        class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                        title="Modifier">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                Aucun type de bien configuré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($propertyTypes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $propertyTypes->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Formulaire -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 space-y-4 shadow-2xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $editingTypeId ? 'Modifier le type' : 'Nouveau type de bien' }}
                </h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-label for="typeName">Nom de la catégorie</x-label>
                        <x-input id="typeName" type="text" wire:model="name" placeholder="ex: Appartement T3, Magasin..." required />
                        @error('name') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <x-label for="typeDescription">Description</x-label>
                        <x-input id="typeDescription" type="text" wire:model="description" placeholder="Optionnel..." />
                    </div>
                    <div>
                        <x-label for="typeStatus">Statut</x-label>
                        <select id="typeStatus" wire:model="status" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showModal', false)">Annuler</x-button>
                        <x-button type="submit" variant="primary">Enregistrer</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
