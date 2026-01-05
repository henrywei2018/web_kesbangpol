<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ request()->cookie('theme-mode', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="rgb({{ $formattedTheme['primary'] ?? '253, 29, 29' }})">
    
    <!-- Theme Settings for JavaScript -->
    <meta name="theme-settings" content="{{ json_encode($formattedTheme) }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($settings->site_favicon) }}">
    
    <title>{{ $settings->brand_name }} - Portal Publik</title>
    
    <!-- Theme CSS -->
    <link href="{{ asset('css/filament/public/public-panel-theme.css') }}" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/filament/public/theme.css', 'resources/js/app.js'])
    
    @stack('styles')
    
    <style>
        :root {
            @foreach($formattedTheme as $key => $value)
            --theme-{{ $key }}: {{ $value }};
            @endforeach
        }
    </style>
</head>
<body class="font-sans antialiased">
    {{ $slot }}
    
    <!-- Theme Controller -->
    <script src="{{ asset('js/theme-controller.js') }}"></script>
    
    @stack('scripts')
</body>
</html>