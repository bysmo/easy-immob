<div>
    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Mot de passe oublié</h1>

    @if($sent)
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-sm text-green-700 dark:text-green-300">
            Si un compte est associé à cet email, un lien de réinitialisation a été envoyé.
        </div>
        <p class="mt-4 text-center text-sm">
            <a href="{{ route('login') }}" class="text-primary-700 dark:text-primary-400 font-medium hover:underline">← Retour à la connexion</a>
        </p>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Saisissez votre email et nous vous enverrons un lien de réinitialisation.
        </p>

        <form wire:submit="sendLink" class="space-y-4">
            <div>
                <x-label for="email">Email</x-label>
                <x-input wire:model="email" type="email" id="email" placeholder="vous@agence.com" autofocus />
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <x-button class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Envoyer le lien</span>
                <span wire:loading>Envoi…</span>
            </x-button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-primary-700 dark:text-primary-400 font-medium hover:underline">← Retour à la connexion</a>
        </p>
    @endif
</div>
