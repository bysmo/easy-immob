<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Mot de passe oublié</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Saisissez votre adresse email pour recevoir un lien de réinitialisation.</p>
    </div>

    @if($sent)
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-200 flex items-start gap-3">
            <x-icon name="check" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
            <div>
                Un lien de réinitialisation a été transmis à votre adresse email si un compte y est associé.
            </div>
        </div>
        
        <div class="pt-2 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la connexion</span>
            </a>
        </div>
    @else
        <form wire:submit="sendLink" class="space-y-4">
            <div>
                <x-label for="email" :required="true">Adresse Email</x-label>
                <x-input wire:model="email" type="email" id="email" placeholder="vous@agence.com" icon="notifications" autofocus :error="$errors->first('email')" />
            </div>

            <div class="pt-2">
                <x-button variant="primary" class="w-full shadow-md shadow-emerald-600/20 py-3" wire:loading.attr="disabled">
                    <span wire:loading.remove class="flex items-center justify-center gap-2">
                        <span>Envoyer le lien</span>
                        <x-icon name="notifications" class="w-4 h-4" />
                    </span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>Envoi en cours...</span>
                    </span>
                </x-button>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                <span>Retour à la page de connexion</span>
            </a>
        </div>
    @endif
</div>
