<x-layouts.app title="{{ ucfirst($module) }} — Bientôt disponible">
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="text-6xl mb-4">🚧</div>
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-2">
            Module en cours de développement
        </h1>
        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
            Le module <strong>{{ $module }}</strong> sera disponible prochainement.
            Consultez la roadmap pour suivre l'avancement.
        </p>
        <a href="{{ route('dashboard') }}" class="mt-6">
            <x-button variant="secondary">← Retour au dashboard</x-button>
        </a>
    </div>
</x-layouts.app>
