<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- En-tête de page -->
    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.saas-dashboard') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">&larr; Dashboard SaaS</a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Configuration Mails SMTP (Super Admin SaaS)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Gérez le serveur SMTP global utilisé par la plateforme SaaS pour l'expédition de tous les emails système, invitations bailleurs et notifications clients.</p>
        </div>
    </div>

    <!-- Alert Succès -->
    @if(session('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
            <x-icon name="check" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Alert Erreur -->
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-800 dark:text-rose-300 flex items-center gap-3">
            <x-icon name="alert" class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">

        <!-- Card: Paramètres Serveur & Authentification SMTP Platform -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <x-icon name="notifications" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            <span>Serveur d'Envoi SMTP Global (Plateforme EasyImmob)</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ces identifiants SMTP seront utilisés par défaut pour délivrer l'ensemble des mails générés par la plateforme.</p>
                    </div>
                    <div>
                        <button type="button"
                                wire:click="testMailConnection"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-xs font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition cursor-pointer">
                            <x-icon name="notifications" class="w-4 h-4" />
                            <span wire:loading.remove wire:target="testMailConnection">Tester la connexion SMTP</span>
                            <span wire:loading wire:target="testMailConnection">Test en cours...</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <x-label for="mail_mailer">Driver Mail</x-label>
                        <x-select id="mail_mailer" wire:model="mail_mailer">
                            <option value="smtp">SMTP (Recommandé)</option>
                            <option value="sendmail">Sendmail</option>
                            <option value="log">Log (Mode Test local)</option>
                        </x-select>
                        @error('mail_mailer') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <x-label for="mail_host">Hôte / Serveur SMTP</x-label>
                        <x-input id="mail_host" type="text" wire:model="mail_host" placeholder="Ex: smtp.sendgrid.net ou mail.easyimmob.com" />
                        @error('mail_host') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_port">Port SMTP</x-label>
                        <x-input id="mail_port" type="number" wire:model="mail_port" placeholder="587" />
                        <p class="text-[11px] text-slate-500 mt-1">Ex: 587 (TLS), 465 (SSL), 25.</p>
                        @error('mail_port') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_encryption">Chiffrement / Sécurité</x-label>
                        <x-select id="mail_encryption" wire:model="mail_encryption">
                            <option value="tls">TLS (Recommandé - Port 587)</option>
                            <option value="ssl">SSL (Port 465)</option>
                            <option value="none">Aucun (Non sécurisé)</option>
                        </x-select>
                        @error('mail_encryption') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_username">Nom d'utilisateur SMTP</x-label>
                        <x-input id="mail_username" type="text" wire:model="mail_username" placeholder="apikey ou smtp_username" />
                        @error('mail_username') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_password">Mot de passe SMTP / Clé d'API</x-label>
                        <x-input id="mail_password" type="password" wire:model="mail_password" placeholder="••••••••••••" />
                        @error('mail_password') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_from_address">Adresse d'expéditeur système (From Email)</x-label>
                        <x-input id="mail_from_address" type="email" wire:model="mail_from_address" placeholder="notifications@easyimmob.com" />
                        @error('mail_from_address') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label for="mail_from_name">Nom d'expéditeur affiché (From Name)</x-label>
                        <x-input id="mail_from_name" type="text" wire:model="mail_from_name" placeholder="EasyImmob SaaS" />
                        @error('mail_from_name') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton d'enregistrement général -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-button type="submit" variant="primary" class="!px-6 !py-3 text-sm">
                <x-icon name="check" class="w-4 h-4" />
                <span wire:loading.remove wire:target="save">Enregistrer la configuration SMTP</span>
                <span wire:loading wire:target="save">Enregistrement en cours...</span>
            </x-button>
        </div>
    </form>
</div>
