<?php

namespace App\Providers;

use App\Livewire\MyProfileExtended;
use App\Settings\GeneralSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PublicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('public')
            ->path('panel')
            ->authGuard('web')
            ->authPasswordBroker('users')
            
            // Branding - same as admin but can be customized
            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName(fn (GeneralSettings $settings) => $settings->brand_name . ' - Portal Publik')
            ->brandLogo(fn (GeneralSettings $settings) => Storage::url($settings->brand_logo))
            ->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)
            ->viteTheme('resources/css/filament/public/theme.css')
            // Features - customized for public panel
            ->databaseNotifications()->databaseNotificationsPolling('60s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            
            // Discovery paths
            ->discoverResources(in: app_path('Filament/Public/Resources'), for: 'App\\Filament\\Public\\Resources')
            ->discoverPages(in: app_path('Filament/Public/Pages'), for: 'App\\Filament\\Public\\Pages')
            ->discoverWidgets(in: app_path('Filament/Public/Widgets'), for: 'App\\Filament\\Public\\Widgets')
            
            // Use our custom dashboard
            ->pages([
                \App\Filament\Public\Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            
            // Middleware stack
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            // Auth features - disabled since we have custom auth
            ->login(false)
            ->registration(false)
            ->passwordReset(false)
            ->emailVerification(false)
            
            // Profile management enabled
            ->profile()
            
            // Navigation items
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Edit Profile')
                    ->url('/panel/profile')
                    ->icon('heroicon-o-user'),
                'logout' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout')
                    ->url('/logout')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            
            // Plugins - simplified for public panel
            ->plugins([
                // FilamentBreezy for profile management
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Account',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->myProfileComponents([
                        'personal_info' => MyProfileExtended::class,
                    ]),
            ]);
    }
}