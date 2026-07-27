<div x-data="{ 
        canInstall: false, 
        installed: false, 
        isIos: false, 
        dismissed: localStorage.getItem('pwa_banner_dismissed') === 'true',
        init() {
            // Check if already in standalone PWA mode
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                this.installed = true;
                return;
            }

            // Check iOS
            const userAgent = window.navigator.userAgent.toLowerCase();
            this.isIos = /iphone|ipad|ipod/.test(userAgent);

            // Listen for install prompt
            if (window.deferredPwaPrompt) {
                this.canInstall = true;
            }

            window.addEventListener('pwa-installable', () => {
                this.canInstall = true;
            });
        },
        async installPwa() {
            if (window.deferredPwaPrompt) {
                window.deferredPwaPrompt.prompt();
                const { outcome } = await window.deferredPwaPrompt.userChoice;
                if (outcome === 'accepted') {
                    this.installed = true;
                    this.canInstall = false;
                }
                window.deferredPwaPrompt = null;
            }
        },
        dismiss() {
            this.dismissed = true;
            localStorage.setItem('pwa_banner_dismissed', 'true');
        }
    }" 
    x-show="!installed && !dismissed && (canInstall || isIos)"
    x-transition
    class="mb-6 rounded-2xl bg-gradient-to-r from-emerald-900 via-slate-900 to-teal-950 p-4 sm:p-5 text-white shadow-xl shadow-emerald-950/20 border border-emerald-500/30 relative overflow-hidden">
    
    <!-- Ambient Glow Background -->
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shrink-0 text-emerald-400 shadow-inner">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-base text-white flex items-center gap-2">
                    📱 Installer l'application EasyImmob
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/30 text-emerald-300 font-semibold border border-emerald-400/20">Mobile</span>
                </h4>
                <p class="text-xs text-slate-300 mt-0.5">
                    Accédez directement à vos loyers et signalez vos incidents depuis l'écran d'accueil de votre téléphone.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <template x-if="canInstall">
                <button @click="installPwa()" 
                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-semibold text-xs transition shadow-md shadow-emerald-500/30 flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Installer maintenant
                </button>
            </template>

            <template x-if="!canInstall && isIos">
                <div class="text-[11px] text-emerald-300 bg-emerald-950/60 border border-emerald-500/30 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                    <span>Sur iPhone: Appuyez sur</span>
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <span>puis "Sur l'écran d'accueil"</span>
                </div>
            </template>

            <button @click="dismiss()" 
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition"
                    title="Masquer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
