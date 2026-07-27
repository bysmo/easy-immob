<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Modèles de contrats de location</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gérez les modèles type réutilisables avec variables automatiques.</p>
        </div>
        <x-button wire:click="openCreateModal">+ Nouveau modèle</x-button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3 text-left">Nom du modèle</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">Version</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($templates as $tpl)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $tpl->name }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $tpl->description ?? '—' }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">v{{ $tpl->version }}</td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$tpl->status === 'active' ? 'success' : 'muted'">
                                {{ $tpl->status === 'active' ? 'Actif' : 'Inactif' }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button wire:click="openEditModal({{ $tpl->id }})"
                                    class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                Modifier
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            Aucun modèle de contrat configuré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $editingTemplateId ? 'Modifier le modèle' : 'Nouveau modèle de contrat' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-label for="name">Nom du modèle</x-label>
                        <x-input wire:model="name" type="text" id="name" placeholder="Ex: Contrat de bail à usage d'habitation" autofocus />
                        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="description">Description (facultatif)</x-label>
                        <x-input wire:model="description" type="text" id="description" placeholder="Courte note explicative..." />
                        @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="content">Contenu du modèle avec balises</x-label>
                        <div class="text-xs text-gray-500 mb-1">
                            Variables disponibles: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{tenant_name}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{owner_name}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{property_address}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{rent_amount}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{charges_amount}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{total_amount}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{deposit_amount}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{start_date}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{end_date}}</code>, <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">@{{payment_due_day}}</code>.
                        </div>
                        <textarea wire:model="content" id="content" rows="10"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm font-mono shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        @error('content') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="status">Statut</x-label>
                        <select wire:model="status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button type="button" variant="secondary" wire:click="$set('showModal', false)">
                            Annuler
                        </x-button>
                        <x-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Enregistrer</span>
                            <span wire:loading>Sauvegarde...</span>
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
