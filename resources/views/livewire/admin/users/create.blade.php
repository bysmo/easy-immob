<div class="max-w-2xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">← Retour</a>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nouvel utilisateur</h1>
    </div>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <div>
                <x-label for="name">Nom complet</x-label>
                <x-input wire:model="name" type="text" id="name" placeholder="Marie Dubois" autofocus />
                @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="email">Email</x-label>
                <x-input wire:model="email" type="email" id="email" placeholder="marie@agence.com" />
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="role">Rôle</x-label>
                <select wire:model="role" id="role"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">— Choisir un rôle —</option>
                    @foreach($availableRoles as $roleName)
                        <option value="{{ $roleName }}">{{ $roleName }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <x-label for="password">Mot de passe temporaire</x-label>
                <x-input wire:model="password" type="password" id="password" />
                <p class="mt-1 text-xs text-gray-400">8 caractères minimum, majuscule et chiffre requis.</p>
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-label for="password_confirmation">Confirmer le mot de passe</x-label>
                <x-input wire:model="password_confirmation" type="password" id="password_confirmation" />
                @error('password_confirmation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Créer l'utilisateur</span>
                    <span wire:loading>Création…</span>
                </x-button>
                <a href="{{ route('admin.users.index') }}">
                    <x-button type="button" variant="secondary">Annuler</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
