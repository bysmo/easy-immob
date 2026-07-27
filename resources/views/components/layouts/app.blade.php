<!DOCTYPE html>
<html lang="fr" 
      x-data="{ 
          dark: localStorage.getItem('dark') === 'true', 
          sidebarOpen: false, 
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' 
      }" 
      x-init="$watch('dark', v => localStorage.setItem('dark', v)); $watch('sidebarCollapsed', v => localStorage.setItem('sidebarCollapsed', v))" 
      :class="{ 'dark': dark }" 
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EasyImmob — Gestion Immobilière SaaS' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <!-- PWA Manifest & Mobile Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#059669">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('PWA ServiceWorker registered:', reg.scope))
                    .catch(err => console.error('PWA ServiceWorker registration failed:', err));
            });
        }

        window.deferredPwaPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPwaPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-installable'));
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-full font-sans antialiased selection:bg-emerald-500 selection:text-white">
    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar Menu -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800 transform transition-all duration-200 ease-in-out lg:translate-x-0 flex flex-col justify-between"
               :class="[
                   sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                   sidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
                   'w-72'
               ]">
            
            <div class="flex-1 overflow-y-auto px-4 py-5 scrollbar-thin">
                <!-- Brand Header & Collapse Toggle -->
                <div class="flex items-center justify-between px-2 mb-6" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-600/20 shrink-0">
                            <x-icon name="building" class="w-6 h-6" />
                        </div>
                        <div :class="sidebarCollapsed ? 'lg:hidden' : ''">
                            <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white block leading-tight">EasyImmob</span>
                            <span class="text-[10px] font-semibold tracking-wider text-emerald-600 dark:text-emerald-400 uppercase block">Gestion Locative</span>
                        </div>
                    </div>

                    <!-- Desktop Collapse Switch -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" 
                            class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            :title="sidebarCollapsed ? 'Agrandir le menu' : 'Réduire le menu'">
                        <template x-if="!sidebarCollapsed">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </template>
                        <template x-if="sidebarCollapsed">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </template>
                    </button>
                </div>

                <!-- Navigation Groups -->
                <nav class="space-y-6">
                    @foreach (\App\Support\Navigation\SidebarMenu::groupedItems() as $group)
                        <div>
                            <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500"
                                 :class="sidebarCollapsed ? 'lg:hidden' : ''">
                                {{ $group['section'] }}
                            </div>
                            <div class="space-y-1">
                                @foreach ($group['items'] as $item)
                                    @php
                                        $isActive = \Illuminate\Support\Facades\Route::currentRouteName() === $item['route'];
                                        $url = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route'], $item['params']) : '#';
                                    @endphp
                                    <a href="{{ $url }}"
                                       title="{{ $item['label'] }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group relative {{ $isActive ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-slate-200' }}"
                                       :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                                        
                                        @if($isActive)
                                            <span class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-emerald-600 dark:bg-emerald-400 rounded-r-full"></span>
                                        @endif

                                        <x-icon :name="$item['icon']" class="w-4 h-4 shrink-0 {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }}" />
                                        
                                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ $item['label'] }}</span>

                                        @if(!$isActive && str_contains($item['route'], 'index') && !in_array($item['icon'], ['dashboard', 'reports', 'admin']))
                                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700 opacity-0 group-hover:opacity-100 transition-opacity"
                                                  :class="sidebarCollapsed ? 'lg:hidden' : ''"></span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                <div class="flex items-center justify-between" :class="sidebarCollapsed ? 'lg:flex-col lg:gap-3' : ''">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 min-w-0 hover:opacity-80 transition group"
                       title="Mon profil & sécurité">
                        @if(auth()->user()?->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover border border-emerald-500 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                            <p class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                {{ auth()->user()?->name ?? 'Utilisateur' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                {{ auth()->user()?->email }}
                            </p>
                        </div>
                    </a>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('profile.edit') }}" 
                           title="Paramètres de profil"
                           class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors"
                           :class="sidebarCollapsed ? 'lg:hidden' : ''">
                            <x-icon name="settings" class="w-4 h-4" />
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    title="Se déconnecter"
                                    class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                <x-icon name="logout" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-200 ease-in-out"
             :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'">
            
            <!-- Header Top Bar -->
            <header class="sticky top-0 z-30 h-16 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Ouvrir le menu">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Header Search Bar -->
                    <div class="hidden sm:flex items-center gap-2">
                        <x-input type="search" 
                                 disabled 
                                 icon="search" 
                                 placeholder="Rechercher propriétaire, bien, locataire..." 
                                 class="w-64 md:w-80 !py-1.5 opacity-80" />
                    </div>
                </div>

                <!-- Header Actions & Controls -->
                <div class="flex items-center gap-3">
                    
                    <!-- Agency Badge -->
                    @if(auth()->user()?->agency)
                        <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ auth()->user()->agency->name }}</span>
                        </div>
                    @endif

                    <!-- Dark/Light Mode Switcher -->
                    <button @click="dark = !dark" 
                            class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="Basculer le thème">
                        <template x-if="!dark">
                            <x-icon name="moon" class="w-5 h-5 text-slate-600" />
                        </template>
                        <template x-if="dark">
                            <x-icon name="sun" class="w-5 h-5 text-amber-400" />
                        </template>
                    </button>

                    <!-- Notifications Bell Button -->
                    <a href="{{ route('notifications.index') }}" 
                       class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative">
                        <x-icon name="bell" class="w-5 h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full"></span>
                    </a>

                    <!-- User Profile Quick Link -->
                    <a href="{{ route('profile.edit') }}" 
                       class="p-1 rounded-full hover:ring-2 hover:ring-emerald-500 transition shrink-0"
                       title="Mon profil">
                        @if(auth()->user()?->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                        @else
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Page Main Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="py-4 px-6 text-center text-xs text-slate-400 border-t border-slate-200/50 dark:border-slate-800/50 mt-auto">
                EasyImmob &copy; {{ date('Y') }} &mdash; Solution SaaS de gestion immobilière moderne
            </footer>
        </div>
    </div>
    @livewireScripts
</body>
</html>
