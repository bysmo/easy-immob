<!DOCTYPE html>
<html lang="fr" x-data="{ dark: localStorage.getItem('dark') === 'true' }" :class="{ 'dark': dark }" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Introuvable | EasyImmob</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-full font-sans antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center relative overflow-hidden p-4">

    {{-- Background Decorative Glowing Orbs --}}
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/15 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-teal-500/10 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute top-10 right-10 w-80 h-80 bg-indigo-500/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-xl text-center z-10 relative space-y-8">

        {{-- Graphic / Illustration 404 Badge --}}
        <div class="relative inline-flex items-center justify-center">
            <div class="text-[120px] sm:text-[160px] font-black leading-none bg-gradient-to-br from-emerald-400 via-teal-300 to-indigo-400 bg-clip-text text-transparent opacity-20 select-none">
                404
            </div>
            
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-slate-900/90 border border-slate-800 backdrop-blur-xl shadow-2xl flex items-center justify-center group hover:scale-105 transition-transform duration-300">
                    <div class="relative">
                        <svg class="w-12 h-12 text-emerald-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="space-y-3">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Oups ! Cette clé n'ouvre aucune porte...
            </h1>
            <p class="text-slate-400 text-sm sm:text-base max-w-md mx-auto leading-relaxed">
                La page ou le bien immobilier que vous recherchez n'existe pas, a été déplacé ou est temporairement indisponible.
            </p>
        </div>

        {{-- Quick Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('dashboard') }}"
               class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2 transition-all duration-200 hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Retour au tableau de bord</span>
            </a>

            <a href="javascript:history.back()"
               class="w-full sm:w-auto px-6 py-3.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 font-semibold text-sm rounded-xl flex items-center justify-center gap-2 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Page précédente</span>
            </a>
        </div>

        {{-- Footer Brand Note --}}
        <div class="pt-8 border-t border-slate-800/60 text-xs text-slate-500 flex items-center justify-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>EasyImmob — Plateforme de Gestion Immobilière SaaS</span>
        </div>

    </div>

</body>
</html>
