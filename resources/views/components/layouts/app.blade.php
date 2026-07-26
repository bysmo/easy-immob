<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true', sidebarOpen: false }" x-init="$watch('dark', v => localStorage.setItem('dark', v))" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex">
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center px-6 text-xl font-semibold text-primary-700 dark:text-primary-400">
                EasyImmob
            </div>
            <nav class="px-3 space-y-1">
                @foreach (\App\Support\Navigation\SidebarMenu::items() as $item)
                    <a href="{{ \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route'], $item['params']) : '#' }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-gray-700">
                        <span>{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 lg:ml-64">
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6">
                <button class="lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Ouvrir le menu">☰</button>
                <input type="search" disabled placeholder="Recherche (bientôt disponible)"
                       class="hidden md:block w-72 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm text-gray-400">
                <div class="flex items-center gap-4">
                    <button @click="dark = !dark" aria-label="Basculer le thème">🌓</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 dark:text-gray-300 hover:text-red-600">
                            {{ auth()->user()->name }} — Déconnexion
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
