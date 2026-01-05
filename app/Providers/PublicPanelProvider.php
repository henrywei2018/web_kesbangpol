<?php

namespace App\Providers;

use App\Livewire\MyProfileExtended;
use App\Settings\GeneralSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class PublicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('public')
            ->path('panel')
            ->authGuard('web')
            ->authPasswordBroker('users')
            
            // Branding
            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName(fn (GeneralSettings $settings) => $settings->brand_name . ' - Portal Publik')
            ->brandLogo(fn (GeneralSettings $settings) => Storage::url($settings->brand_logo))
            ->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)
            
            // Features
            ->databaseNotifications()->databaseNotificationsPolling('60s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/public/theme.css')
            
            // Discovery
            ->discoverResources(in: app_path('Filament/Public/Resources'), for: 'App\\Filament\\Public\\Resources')
            ->discoverPages(in: app_path('Filament/Public/Pages'), for: 'App\\Filament\\Public\\Pages')
            ->discoverWidgets(in: app_path('Filament/Public/Widgets'), for: 'App\\Filament\\Public\\Widgets')
            
            // Dashboard
            ->pages([
                \App\Filament\Public\Pages\Dashboard::class,
                \App\Filament\Public\Pages\OrmasDashboard::class,
            ])
            
            // Navigation Groups
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('POKUS KALTARA')
                    ->icon('heroicon-o-building-office'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('PPID')
                    ->icon('heroicon-o-document-text'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Layanan')
                    ->icon('heroicon-o-briefcase'),
            ])
            
            // Middleware
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            // Disable default auth pages (we have custom ones)
            ->login(false)
            ->registration(false)
            ->passwordReset(false)
            ->emailVerification(false)
            
            // Enable plugins for enhanced functionality
            ->plugins([
                // Filament Breezy for enhanced profile management
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Profile',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->myProfileComponents([
                        'personal_info' => MyProfileExtended::class,
                    ])
            ])
            
            // User menu with enhanced options
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('My Profile')
                    ->url('/panel/my-profile')
                    ->icon('heroicon-o-user-circle'),
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout')
                    ->url('/logout')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ]);
    }
    
    public function boot()
    {
        // Register layout component
        Blade::component('layouts.public-panel', \App\View\Components\Layouts\PublicPanel::class);
    }
}