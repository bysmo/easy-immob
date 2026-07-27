<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Modèles de Contrats de Location</h1>
                <x-badge color="indigo">{{ count($templates) }} configurés</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modèles d'actes juridiques et de baux d'habitation avec injection automatique de variables.</p>
        </div>

        <x-button variant="primary" wire:click="openCreateModal" class="shadow-md shadow-emerald-600/20">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nouveau modèle</span>
        </x-button>
    </div>

    <!-- Flash Notifications -->
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

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Nom du Modèle</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Version</th>
                        <th class="px-6 py-3.5">Statut</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($templates as $tpl)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-icon name="lease-templates" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                <span>{{ $tpl->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400">
                                {{ $tpl->description ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-semibold">v{{ $tpl->version }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$tpl->status === 'active' ? 'success' : 'muted'">
                                    {{ $tpl->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="openEditModal({{ $tpl->id }})" 
                                        class="inline-flex items-center gap-1 p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors text-xs font-semibold">
                                    <x-icon name="edit" class="w-4 h-4" />
                                    <span>Modifier</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucun modèle de contrat configuré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Create / Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 space-y-5 max-h-[90vh] overflow-y-auto scrollbar-thin">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $editingTemplateId ? 'Modifier le modèle de contrat' : 'Nouveau modèle de contrat' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-label for="name" :required="true">Nom du modèle</x-label>
                        <x-input wire:model="name" type="text" id="name" placeholder="Ex: Contrat de bail d'habitation standard" icon="lease-templates" autofocus :error="$errors->first('name')" />
                    </div>

                    <div>
                        <x-label for="description">Description courte</x-label>
                        <x-input wire:model="description" type="text" id="description" placeholder="Ex: Usage d'habitation principale..." :error="$errors->first('description')" />
                    </div>

                    <div>
                        <x-label for="content" :required="true">Contenu avec injection dynamique de variables</x-label>
                        <div class="text-[11px] text-slate-500 mb-1.5 flex flex-wrap gap-1">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">Variables :</span>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{tenant_name}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{owner_name}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{property_address}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{rent_amount}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{charges_amount}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{total_amount}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{start_date}}</code>
                            <code class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded">@{{end_date}}</code>
                        </div>
                        <textarea wire:model="content" id="content" rows="10"
                                  class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-mono shadow-2xs p-3.5 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                        @error('content') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="status" :required="true">Statut</x-label>
                        <x-select wire:model="status" id="status" :error="$errors->first('status')">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </x-select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3">
                        <x-button type="button" variant="secondary" wire:click="$set('showModal', false)">
                            Annuler
                        </x-button>
                        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Enregistrer</span>
                            <span wire:loading>Sauvegarde...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
