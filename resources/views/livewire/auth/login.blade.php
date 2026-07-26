<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Connexion</h1>

    <form wire:submit="authenticate" class="space-y-4">
        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" autofocus />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" />
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="text-primary-700 dark:text-primary-400">Mot de passe oublié ?</a>
            <a href="{{ route('register') }}" class="text-primary-700 dark:text-primary-400">Créer une agence</a>
        </div>

        <x-button class="w-full">Se connecter</x-button>
    </form>
</div>
