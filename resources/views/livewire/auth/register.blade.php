<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Créer votre compte EasyImmob</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Choisissez votre profil pour commencer.</p>
    </div>

    <!-- Selector Tabs -->
    <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700">
        <button type="button" 
                wire:click="setAccountType('agency')" 
                class="py-2.5 px-3 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 {{ $account_type === 'agency' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-800 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <x-icon name="building" class="w-4 h-4" />
            <span>Agence immobilière</span>
        </button>

        <button type="button" 
                wire:click="setAccountType('tenant')" 
                class="py-2.5 px-3 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-2 {{ $account_type === 'tenant' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-800 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
            <x-icon name="user" class="w-4 h-4" />
            <span>Citoyen / Locataire</span>
        </button>
    </div>

    <form wire:submit="register" class="space-y-4">
        @if ($account_type === 'agency')
            <!-- Agency Fields -->
            <div>
                <x-label for="agency_name" :required="true">Nom de votre agence</x-label>
                <x-input wire:model="agency_name" type="text" id="agency_name" placeholder="Ex: Immobilier du Centre" icon="building" autofocus :error="$errors->first('agency_name')" />
            </div>

            <div>
                <x-label for="name" :required="true">Nom complet de l'administrateur</x-label>
                <x-input wire:model="name" type="text" id="name" placeholder="Ex: Jean Dupont" icon="user" :error="$errors->first('name')" />
            </div>
        @else
            <!-- Tenant Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="last_name" :required="true">Nom</x-label>
                    <x-input wire:model="last_name" type="text" id="last_name" placeholder="Ex: Sawadogo" icon="user" autofocus :error="$errors->first('last_name')" />
                </div>
                <div>
                    <x-label for="first_name" :required="true">Prénom(s)</x-label>
                    <x-input wire:model="first_name" type="text" id="first_name" placeholder="Ex: Paul" icon="user" :error="$errors->first('first_name')" />
                </div>
            </div>

            <div>
                <x-label for="phone">Numéro de téléphone</x-label>
                <x-input wire:model="phone" type="text" id="phone" placeholder="+226 70 00 00 00" icon="user" :error="$errors->first('phone')" />
            </div>
        @endif

        <!-- Shared Fields -->
        <div>
            <x-label for="email" :required="true">Adresse email</x-label>
            <x-input wire:model="email" type="email" id="email" placeholder="vous@exemple.com" icon="notifications" :error="$errors->first('email')" />
        </div>

        <div>
            <x-label for="password" :required="true">Mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" placeholder="••••••••" icon="shield" :error="$errors->first('password')" />
            <p class="mt-1 text-[11px] text-slate-400">8 caractères minimum.</p>
        </div>

        <div>
            <x-label for="password_confirmation" :required="true">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" placeholder="••••••••" icon="shield" :error="$errors->first('password_confirmation')" />
        </div>

        <div class="pt-2">
            <x-button variant="primary" class="w-full shadow-md shadow-emerald-600/20 py-3" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center justify-center gap-2">
                    <span>{{ $account_type === 'agency' ? 'Créer mon agence' : 'Créer mon compte Locataire' }}</span>
                    <x-icon name="check" class="w-4 h-4" />
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Création du compte...</span>
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
