<div class="max-w-2xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('tenants.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">← Retour</a>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Modifier locataire <span class="font-mono text-base text-gray-500">({{ $tenant->reference }})</span>
        </h1>
    </div>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="first_name">Prénom</x-label>
                    <x-input wire:model="first_name" type="text" id="first_name" autofocus />
                    @error('first_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="last_name">Nom</x-label>
                    <x-input wire:model="last_name" type="text" id="last_name" />
                    @error('last_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="email">Email</x-label>
                    <x-input wire:model="email" type="email" id="email" />
                    @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="phone">Téléphone</x-label>
                    <x-input wire:model="phone" type="text" id="phone" />
                    @error('phone') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-label for="address">Adresse actuelle</x-label>
                <x-input wire:model="address" type="text" id="address" />
                @error('address') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="emergency_contact">Contact en cas d'urgence</x-label>
                <x-input wire:model="emergency_contact" type="text" id="emergency_contact" />
                @error('emergency_contact') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Enregistrer</span>
                    <span wire:loading>Sauvegarde...</span>
                </x-button>
                <a href="{{ route('tenants.index') }}">
                    <x-button type="button" variant="secondary">Annuler</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
