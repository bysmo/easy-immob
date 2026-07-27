<div class="max-w-3xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('properties.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">← Retour</a>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Modifier le bien <span class="font-mono text-base text-gray-500">({{ $property->reference }})</span>
        </h1>
    </div>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-label for="title">Titre / Désignation du bien</x-label>
                <x-input wire:model="title" type="text" id="title" autofocus />
                @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="owner_id">Propriétaire</x-label>
                    <select wire:model="owner_id" id="owner_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">— Sélectionner un propriétaire —</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }} ({{ $owner->reference }})</option>
                        @endforeach
                    </select>
                    @error('owner_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="property_type_id">Type de bien</x-label>
                    <select wire:model="property_type_id" id="property_type_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">— Sélectionner un type —</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('property_type_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="city">Ville</x-label>
                    <x-input wire:model="city" type="text" id="city" />
                    @error('city') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="neighborhood">Quartier</x-label>
                    <x-input wire:model="neighborhood" type="text" id="neighborhood" />
                    @error('neighborhood') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-label for="address">Adresse précise</x-label>
                <x-input wire:model="address" type="text" id="address" />
                @error('address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-label for="surface_area">Surface (m²)</x-label>
                    <x-input wire:model="surface_area" type="number" step="0.01" id="surface_area" />
                    @error('surface_area') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="bedrooms">Chambres</x-label>
                    <x-input wire:model="bedrooms" type="number" id="bedrooms" />
                    @error('bedrooms') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="bathrooms">Salles de bain</x-label>
                    <x-input wire:model="bathrooms" type="number" id="bathrooms" />
                    @error('bathrooms') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="rent_amount">Loyer mensuel demandé (FCFA)</x-label>
                    <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" />
                    @error('rent_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="status">Statut</x-label>
                    <select wire:model="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-label for="description">Description détaillée</x-label>
                <textarea wire:model="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>Sauvegarde...</span>
                </x-button>
                <a href="{{ route('properties.index') }}">
                    <x-button type="button" variant="secondary">Annuler</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
