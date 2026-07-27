<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Nouveau mot de passe</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Choisissez un mot de passe sécurisé.</p>

    <form wire:submit="resetPassword" class="space-y-4">
        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" readonly />
            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Nouveau mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" autofocus />
            <p class="mt-1 text-xs text-gray-400">8 caractères minimum, majuscule et chiffre requis.</p>
            @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" />
            @error('password_confirmation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <x-button class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove>Réinitialiser le mot de passe</span>
            <span wire:loading>Traitement…</span>
        </x-button>
    </form>
</div>
