<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Livewire styles should be loaded first -->
    
    @include('components.layouts.partials.head')
    @livewireStyles
    
</head>
<body class="loading-overlay-showing" data-loading-overlay data-plugin-page-transition>

    <div class="body">
        <!-- Loading overlay (optional) -->
       
        @include('components.layouts.partials.loader')

        <!-- Conditional Header Inclusion -->
        @unless($excludeHeader ?? false)
            @include('components.layouts.partials.header')
        @endunless

        <!-- Main content section -->
        <div role="main" class="main">
            @yield('content')
        </div>

        <!-- Footer inclusion -->
        @include('components.layouts.partials.footer')
    </div>
    <!-- Main Scripts -->
    
    @unless($excludeScripts ?? false)
            @include('components.layouts.partials.scripts', ['siteKey' => $siteKey])
    @endunless
    @livewireScripts
    @stack('scripts')
    
    
</body>
</html>
