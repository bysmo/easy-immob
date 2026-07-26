<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="$watch('dark', v => localStorage.setItem('dark', v))" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <span class="text-2xl font-semibold text-primary-700 dark:text-primary-400">EasyImmob</span>
        </div>
        <x-card>
            {{ $slot }}
        </x-card>
    </div>
    @livewireScripts
</body>
</html>
