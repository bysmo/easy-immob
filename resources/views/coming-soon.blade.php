<x-layouts.app title="{{ ucfirst($module) }} — Bientôt disponible">
    <div class="flex flex-col items-center justify-center py-20 text-center max-w-md mx-auto">
        <div class="w-16 h-16 rounded-3xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900 flex items-center justify-center mb-6 shadow-md shadow-emerald-600/10">
            <x-icon name="cog" class="w-8 h-8 animate-spin" style="animation-duration: 8s;" />
        </div>
        <x-badge color="indigo" class="mb-3">Module en cours de déploiement</x-badge>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-2">
            Module {{ ucfirst($module) }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
            Cette fonctionnalité est en cours de développement actif. Elle sera mise en service lors des prochaines étapes de déploiement de l'application EasyImmob.
        </p>
        <a href="{{ route('dashboard') }}">
            <x-button variant="secondary" size="sm">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span>Retour au tableau de bord</span>
            </x-button>
        </a>
    </div>
</x-layouts.app>
