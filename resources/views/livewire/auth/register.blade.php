<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Créer votre agence immobilière</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Démarrez gratuitement votre gestion locative en quelques clics.</p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <!-- Nom Agence -->
        <div>
            <x-label for="agency_name" :required="true">Nom de votre agence</x-label>
            <x-input wire:model="agency_name" type="text" id="agency_name" placeholder="Ex: Immobilier du Centre" icon="building" autofocus :error="$errors->first('agency_name')" />
        </div>

        <!-- Nom Administrateur -->
        <div>
            <x-label for="name" :required="true">Nom complet de l'administrateur</x-label>
            <x-input wire:model="name" type="text" id="name" placeholder="Ex: Jean Dupont" icon="user" :error="$errors->first('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-label for="email" :required="true">Email professionnel</x-label>
            <x-input wire:model="email" type="email" id="email" placeholder="vous@agence.com" icon="notifications" :error="$errors->first('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-label for="password" :required="true">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" placeholder="••••••••" icon="shield" :error="$errors->first('password')" />
            <p class="mt-1 text-[11px] text-slate-400">8 caractères minimum, une majuscule et un chiffre requis.</p>
        </div>

        <!-- Password Confirmation -->
        <div>
            <x-label for="password_confirmation" :required="true">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" placeholder="••••••••" icon="shield" :error="$errors->first('password_confirmation')" />
        </div>

        <div class="pt-2">
            <x-button variant="primary" class="w-full shadow-md shadow-emerald-600/20 py-3" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center justify-center gap-2">
                    <span>Créer mon agence</span>
                    <x-icon name="check" class="w-4 h-4" />
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Création de l'agence...</span>
                </span>
            </x-button>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500">
        Vous possédez déjà un compte ?
        <a href="{{ route('login') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
            Se connecter
        </a>
    </div>
</div>
