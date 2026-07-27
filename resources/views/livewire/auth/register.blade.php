<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Créer votre agence</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Commencez gratuitement. Aucune carte requise.</p>

    <form wire:submit="register" class="space-y-4">
        {{-- Agence --}}
        <div>
            <x-label for="agency_name">Nom de l'agence</x-label>
            <x-input wire:model="agency_name" type="text" id="agency_name" placeholder="Immobilier du Centre" autofocus />
            @error('agency_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Administrateur --}}
        <div>
            <x-label for="name">Votre nom complet</x-label>
            <x-input wire:model="name" type="text" id="name" placeholder="Jean Dupont" />
            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="email">Email</x-label>
            <x-input wire:model="email" type="email" id="email" placeholder="vous@agence.com" />
            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" />
            <p class="mt-1 text-xs text-gray-400">8 caractères minimum, majuscule et chiffre requis.</p>
            @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-label for="password_confirmation">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" />
            @error('password_confirmation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <x-button class="w-full" wire:loading.attr="disabled">
            <span wire:loading.remove>Créer mon agence</span>
            <span wire:loading>Création en cours…</span>
        </x-button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
        Déjà inscrit ?
        <a href="{{ route('login') }}" class="text-primary-700 dark:text-primary-400 font-medium hover:underline">Se connecter</a>
    </p>
</div>
