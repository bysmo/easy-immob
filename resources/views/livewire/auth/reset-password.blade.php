<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Nouveau mot de passe</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Choisissez un mot de passe sécurisé pour réinitialiser votre accès.</p>
    </div>

    <form wire:submit="resetPassword" class="space-y-4">
        <div>
            <x-label for="email">Adresse Email</x-label>
            <x-input wire:model="email" type="email" id="email" icon="notifications" readonly />
        </div>

        <div>
            <x-label for="password" :required="true">Nouveau mot de passe</x-label>
            <x-input wire:model="password" type="password" id="password" placeholder="••••••••" icon="shield" autofocus :error="$errors->first('password')" />
            <p class="mt-1 text-[11px] text-slate-400">8 caractères minimum, une majuscule et un chiffre requis.</p>
        </div>

        <div>
            <x-label for="password_confirmation" :required="true">Confirmer le mot de passe</x-label>
            <x-input wire:model="password_confirmation" type="password" id="password_confirmation" placeholder="••••••••" icon="shield" :error="$errors->first('password_confirmation')" />
        </div>

        <div class="pt-2">
            <x-button variant="primary" class="w-full shadow-md shadow-emerald-600/20 py-3" wire:loading.attr="disabled">
                <span wire:loading.remove class="flex items-center justify-center gap-2">
                    <span>Réinitialiser le mot de passe</span>
                    <x-icon name="check" class="w-4 h-4" />
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>Mise à jour...</span>
                </span>
            </x-button>
        </div>
    </form>
</div>
