<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Connexion à votre espace</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Accédez au tableau de bord de gestion de votre agence.</p>
    </div>

    <form wire:submit="authenticate" class="space-y-4">
        <div>
            <x-label for="email" :required="true">Adresse Email</x-label>
            <x-input wire:model="email" type="email" id="email" placeholder="vous@agence.com" icon="notifications" autofocus :error="$errors->first('email')" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <x-label for="password" :required="true" class="!mb-0">Mot de passe</x-label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Mot de passe oublié ?
                </a>
            </div>
            <x-input wire:model="password" type="password" id="password" placeholder="••••••••" icon="shield" :error="$errors->first('password')" />
        </div>

        <div class="pt-2">
            <x-button variant="primary" class="w-full shadow-md shadow-emerald-600/20 py-3" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center justify-center gap-2">
                    <span>Se connecter</span>
                    <x-icon name="check" class="w-4 h-4" />
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Authentification...</span>
                </span>
            </x-button>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
        Pas encore d'agence inscrite ?
        <a href="{{ route('register') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
            Créer un compte agence
        </a>
    </div>
</div>
