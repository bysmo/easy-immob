<div x-data="{
        open: false,
        title: '',
        message: '',
        confirmText: 'Confirmer',
        cancelText: 'Annuler',
        variant: 'primary', // 'primary', 'danger', 'success', 'warning'
        callback: null,

        init() {
            window.addEventListener('open-confirm', (e) => {
                const data = e.detail;
                this.title = data.title || 'Confirmation requise';
                this.message = data.message || 'Êtes-vous sûr de vouloir effectuer cette action ?';
                this.confirmText = data.confirmText || 'Confirmer';
                this.cancelText = data.cancelText || 'Annuler';
                this.variant = data.variant || 'primary';
                this.callback = data.onConfirm || null;
                this.open = true;
            });
        },
        confirm() {
            if (typeof this.callback === 'function') {
                this.callback();
            }
            this.open = false;
        },
        cancel() {
            this.open = false;
        }
    }"
    x-show="open"
    x-on:keydown.escape.window="cancel()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cancel()"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-md"></div>

    <!-- Modal Box -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl p-6 text-slate-900 dark:text-white overflow-hidden">

        <!-- Ambient Glow Badge Header -->
        <div class="flex items-start gap-4">
            <!-- Dynamic Icon Badge based on Variant -->
            <template x-if="variant === 'danger'">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </template>

            <template x-if="variant === 'success'">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </template>

            <template x-if="variant === 'warning'">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800 flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </template>

            <template x-if="variant === 'primary' || !['danger', 'success', 'warning'].includes(variant)">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-950/80 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800 flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </template>

            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-snug" x-text="title"></h3>
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 leading-relaxed" x-text="message"></p>
            </div>
        </div>

        <!-- Action Buttons Footer -->
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-end gap-3">
            <button type="button"
                    @click="cancel()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer">
                <span x-text="cancelText">Annuler</span>
            </button>

            <template x-if="variant === 'danger'">
                <button type="button"
                        @click="confirm()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-semibold text-xs transition shadow-md shadow-rose-600/20 cursor-pointer">
                    <span x-text="confirmText">Confirmer</span>
                </button>
            </template>

            <template x-if="variant === 'success'">
                <button type="button"
                        @click="confirm()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs transition shadow-md shadow-emerald-600/20 cursor-pointer">
                    <span x-text="confirmText">Confirmer</span>
                </button>
            </template>

            <template x-if="variant === 'warning'">
                <button type="button"
                        @click="confirm()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-semibold text-xs transition shadow-md shadow-amber-600/20 cursor-pointer">
                    <span x-text="confirmText">Confirmer</span>
                </button>
            </template>

            <template x-if="variant === 'primary' || !['danger', 'success', 'warning'].includes(variant)">
                <button type="button"
                        @click="confirm()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white font-semibold text-xs transition shadow-md shadow-teal-600/20 cursor-pointer">
                    <span x-text="confirmText">Confirmer</span>
                </button>
            </template>
        </div>
    </div>
</div>
