<div class="space-y-5">
    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Types de biens immobiliers</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Référentiel des catégories de biens pour votre agence.</p>
        </div>
        <x-button wire:click="openCreateModal">+ Nouveau type de bien</x-button>
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
                    <th class="px-5 py-3 text-left">Nom</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($propertyTypes as $type)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $type->name }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $type->description ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$type->status === 'active' ? 'success' : 'muted'">
                                {{ $type->status === 'active' ? 'Actif' : 'Inactif' }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button wire:click="openEditModal({{ $type->id }})"
                                    class="text-primary-600 dark:text-primary-400 hover:underline text-xs font-medium">
                                Modifier
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-gray-400">
                            Aucun type de bien configuré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $editingTypeId ? 'Modifier le type de bien' : 'Nouveau type de bien' }}
                </h3>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-label for="name">Nom du type</x-label>
                        <x-input wire:model="name" type="text" id="name" placeholder="Ex: Appartement, Villa..." autofocus />
                        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="description">Description (facultatif)</x-label>
                        <x-input wire:model="description" type="text" id="description" placeholder="Description courte..." />
                        @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
