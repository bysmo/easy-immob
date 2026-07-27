<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="$watch('dark', v => localStorage.setItem('dark', v))" :class="{ 'dark': dark }" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob — Connexion' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden font-sans antialiased">
    
    <!-- Background Gradient Accents -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-lg shadow-emerald-600/30 mb-3">
                <x-icon name="building" class="w-7 h-7" />
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">EasyImmob</h1>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mt-0.5">Plateforme SaaS Immobilier</p>
        </div>

        <x-card class="shadow-xl shadow-slate-200/50 dark:shadow-none border-slate-200/80 dark:border-slate-800">
            {{ $slot }}
        </x-card>

        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} EasyImmob. Tous droits réservés.
        </div>
    </div>

    @livewireScripts
</body>
</html>
