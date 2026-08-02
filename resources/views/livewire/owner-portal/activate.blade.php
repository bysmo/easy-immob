<div class="space-y-8">
    {{-- =========================================================== --}}
    {{-- ACTIVATION COMPTE BAILLEUR                                  --}}
    {{-- =========================================================== --}}
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-teal-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 p-4">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 shadow-xl shadow-emerald-600/20 mb-4">
                    <x-icon name="building" class="w-8 h-8 text-white" />
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Activation de votre compte</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Portail Bailleur — EasyImmob</p>
            </div>

            {{-- Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl p-8 space-y-6">

                <div class="text-center">
                    <p class="text-slate-600 dark:text-slate-300 text-sm">
                        Bienvenue <strong class="text-slate-900 dark:text-white">{{ $user->name }}</strong> ! 
                        Créez votre mot de passe pour accéder à votre espace bailleur.
                    </p>
                </div>

                <form wire:submit="activate" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mot de passe
                        </label>
                        <input type="password"
                               wire:model="password"
                               placeholder="Minimum 8 caractères avec chiffres"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" />
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Confirmer le mot de passe
                        </label>
                        <input type="password"
                               wire:model="password_confirmation"
                               placeholder="Répétez le mot de passe"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" />
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-6 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-md shadow-emerald-600/20 hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center gap-2"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed">
                        <span wire:loading.remove>Activer mon compte</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Activation en cours…
                        </span>
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-slate-400 mt-4">
                Ce lien est valable 72 heures. Contactez votre agence si le lien a expiré.
            </p>
        </div>
    </div>
</div>
