<?php
// resources/views/layouts/public-panel.blade.php

use App\Settings\GeneralSettings;

$settings = app(GeneralSettings::class);
$user = auth()->user();
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ request()->cookie('theme-mode', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Theme Settings -->
    @php
        $formattedTheme = [];
        foreach ($settings->site_theme as $key => $value) {
            if (str_starts_with($value, '#')) {
                $hex = ltrim($value, '#');
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $formattedTheme[$key] = "$r, $g, $b";
            } elseif (str_starts_with($value, 'rgb(')) {
                preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $value, $matches);
                if ($matches) {
                    $formattedTheme[$key] = "{$matches[1]}, {$matches[2]}, {$matches[3]}";
                }
            } else {
                $formattedTheme[$key] = $value;
            }
        }
    @endphp
    
    <meta name="theme-color" content="rgb({{ $formattedTheme['primary'] ?? '253, 29, 29' }})">
    <meta name="theme-settings" content="{{ json_encode($formattedTheme) }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($settings->site_favicon) }}">
    
    <title>{{ $settings->brand_name }} - Portal Publik</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Vite Assets -->
    <!--@vite(['resources/css/filament/public/theme.css', 'resources/js/app.js'])-->
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    @stack('styles')
    
    <style>
        :root {
            @foreach($formattedTheme as $key => $value)
            --theme-{{ $key }}: {{ $value }};
            @endforeach
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300 font-sans antialiased">
    <!-- Mobile Menu Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden" id="mobile-overlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar-theme" id="sidebar">
        <!-- Brand Header -->
        <div class="p-6 border-b border-gray-200 dark:border-slate-700 bg-theme-primary">
            <div class="flex items-center space-x-3">
                @if($settings->brand_logo)
                    <img src="{{ Storage::url($settings->brand_logo) }}" 
                         alt="{{ $settings->brand_name }}" 
                         class="w-10 h-10 object-contain bg-white rounded-lg p-1">
                @else
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i data-lucide="building-2" class="w-6 h-6 text-theme-primary"></i>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h1 class="text-white font-bold text-lg truncate">{{ $settings->brand_name }}</h1>
                    <p class="text-white/80 text-sm">Portal Publik</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            <a href="{{ route('filament.public.pages.dashboard') }}" 
               class="nav-theme-item {{ request()->routeIs('filament.public.pages.dashboard') ? 'active' : '' }}">
                <i data-lucide="home" class="w-5 h-5 mr-3"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('filament.public.resources.permohonan-informasi.index') }}" 
               class="nav-theme-item {{ request()->routeIs('filament.public.resources.permohonan-informasi-publiks.*') ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                <span>Permohonan Informasi</span>
            </a>
            
            <a href="{{ route('filament.public.resources.keberatan-informasi.index') }}" 
               class="nav-theme-item {{ request()->routeIs('filament.public.resources.keberatan-informasi-publiks.*') ? 'active' : '' }}">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                <span>Keberatan Informasi</span>
            </a>
            
            <hr class="my-4 border-gray-200 dark:border-slate-600">
            
            <a href="#" class="nav-theme-item">
                <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                <span>Profil Saya</span>
            </a>
            
            <a href="#" class="nav-theme-item">
                <i data-lucide="bell" class="w-5 h-5 mr-3"></i>
                <span>Notifikasi</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="ml-auto bg-theme-primary text-white text-xs px-2 py-1 rounded-full">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
            
            <a href="#" class="nav-theme-item">
                <i data-lucide="download" class="w-5 h-5 mr-3"></i>
                <span>Unduhan</span>
            </a>
            
            <a href="#" class="nav-theme-item">
                <i data-lucide="help-circle" class="w-5 h-5 mr-3"></i>
                <span>Bantuan</span>
            </a>
        </nav>
        
        <!-- User Profile -->
        <div class="p-4 border-t border-gray-200 dark:border-slate-700">
            <div class="flex items-center space-x-3 mb-3">
                @if($user->getFilamentAvatarUrl())
                    <img src="{{ $user->getFilamentAvatarUrl() }}" 
                         alt="{{ $user->getFilamentName() }}" 
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 bg-theme-primary rounded-full flex items-center justify-center">
                        <span class="text-white font-medium text-sm">
                            {{ strtoupper(substr($user->firstname ?? $user->username, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white truncate">
                        {{ $user->firstname ? $user->firstname . ' ' . $user->lastname : $user->username }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">Pengguna Publik</p>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button data-theme-toggle 
                        class="flex-1 px-3 py-2 text-sm bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors flex items-center justify-center">
                    <i data-lucide="sun" class="w-4 h-4 mr-2 hidden dark:block"></i>
                    <i data-lucide="moon" class="w-4 h-4 mr-2 block dark:hidden"></i>
                    <span class="hidden lg:inline">Theme</span>
                </button>
                
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-3 py-2 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center">
                        <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                        <span class="hidden lg:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-theme">
        <!-- Header -->
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button class="lg:hidden p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors" 
                            id="mobile-menu-toggle">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $pageTitle ?? 'Dashboard' }}
                        </h1>
                        @if(isset($pageSubtitle))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $pageSubtitle }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative hidden sm:block">
                        <input type="text" 
                               placeholder="Cari..." 
                               class="input-theme w-64"
                               id="global-search">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                            onclick="toggleNotifications()">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-theme-primary rounded-full animate-pulse"></span>
                        @endif
                    </button>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                onclick="toggleUserMenu()">
                            @if($user->getFilamentAvatarUrl())
                                <img src="{{ $user->getFilamentAvatarUrl() }}" 
                                     alt="{{ $user->getFilamentName() }}" 
                                     class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 bg-theme-primary rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        {{ strtoupper(substr($user->firstname ?? $user->username, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></i>
                        </button>
                        
                        <!-- User Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 hidden"
                             id="user-menu">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                                <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                Profil Saya
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">
                                <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                                Pengaturan
                            </a>
                            <hr class="my-1 border-gray-200 dark:border-slate-600">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="content-theme">
            {{ $slot }}
        </div>
    </main>
    
    <!-- Notification Panel -->
    <div class="fixed top-16 right-4 w-80 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-gray-200 dark:border-slate-700 hidden z-50"
         id="notification-panel">
        <div class="p-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications()->limit(5)->get() as $notification)
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-slate-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        {{ $notification->data['message'] ?? 'Pesan notifikasi' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                    <p class="text-sm">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>
        @if(auth()->user()->notifications->count() > 5)
            <div class="p-3 border-t border-gray-200 dark:border-slate-700">
                <a href="#" class="text-sm text-theme-primary hover:underline block text-center">
                    Lihat semua notifikasi
                </a>
            </div>
        @endif
    </div>
    
    <!-- Theme Toggle Floating Button (Mobile) -->
    <div class="fixed bottom-6 right-6 z-40 lg:hidden">
        <button data-theme-toggle 
                class="w-12 h-12 bg-theme-primary text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 flex items-center justify-center">
            <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
            <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
        </button>
    </div>
    
    @stack('modals')
    @stack('scripts')
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Theme toggle functionality
        document.addEventListener('click', function(event) {
            if (event.target.matches('[data-theme-toggle]') || event.target.closest('[data-theme-toggle]')) {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                
                // Save preference
                document.cookie = `theme-mode=${isDark ? 'dark' : 'light'}; path=/; max-age=31536000`;
                
                // Re-initialize icons
                setTimeout(() => lucide.createIcons(), 100);
            }
        });
        
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        
        mobileMenuToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-mobile-open');
            mobileOverlay.classList.toggle('hidden');
        });
        
        mobileOverlay?.addEventListener('click', function() {
            sidebar.classList.remove('sidebar-mobile-open');
            mobileOverlay.classList.add('hidden');
        });
        
        // Notifications toggle
        function toggleNotifications() {
            const panel = document.getElementById('notification-panel');
            panel.classList.toggle('hidden');
        }
        
        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const notificationPanel = document.getElementById('notification-panel');
            const userMenu = document.getElementById('user-menu');
            
            if (!event.target.closest('[onclick="toggleNotifications()"]') && !notificationPanel.contains(event.target)) {
                notificationPanel.classList.add('hidden');
            }
            
            if (!event.target.closest('[onclick="toggleUserMenu()"]') && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Global search functionality
        document.getElementById('global-search')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            if (query.length > 2) {
                // Implement search functionality here
                console.log('Searching for:', query);
            }
        });
        
        // Load saved theme
        const savedTheme = document.cookie.split('; ').find(row => row.startsWith('theme-mode='));
        if (savedTheme?.split('=')[1] === 'dark') {
            document.documentElement.classList.add('dark');
        }
        
        // Page load animations
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.card-theme, .stat-theme-card');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>