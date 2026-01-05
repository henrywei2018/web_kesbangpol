<?php

namespace App\Providers\Filament;


use App\Livewire\MyProfileExtended;
use App\Settings\GeneralSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Widgets\AdminOverviewWidget;
use App\Filament\Widgets\AdminStatusWidget;
use App\Filament\Widgets\AdminRecentActivityWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->authPasswordBroker('users') 
            ->favicon(fn (GeneralSettings $settings) => Storage::url($settings->site_favicon))
            ->brandName(fn (GeneralSettings $settings) => $settings->brand_name)
            ->brandLogo(function (GeneralSettings $settings) {
                // Check if dark mode is active
                $isDarkMode = request()->cookie('theme-mode') === 'dark';
                
                if ($isDarkMode) {
                    // Use dark mode logo if set, otherwise fallback to regular logo
                    return Storage::url($settings->brand_logo_white ?? $settings->brand_logo);
                }
                
                return Storage::url($settings->brand_logo);
            })
            ->brandLogoHeight(fn (GeneralSettings $settings) => $settings->brand_logoHeight)
            ->colors(fn (GeneralSettings $settings) => $settings->site_theme)
            ->databaseNotifications()->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            
            ->pages([
                AdminDashboard::class,
            ])
            ->homeUrl(fn () => url('/admin/dashboard-admin'))
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Aplikasi')
                     ->icon('heroicon-c-tv'),
                NavigationGroup::make()
                     ->label('POKUS KALTARA')
                     ->icon('heroicon-c-tv'),
                NavigationGroup::make()
                     ->label('Layanan')
                     ->icon('heroicon-o-briefcase'),
                NavigationGroup::make()
                     ->label('PPID')
                     ->icon('heroicon-o-briefcase'),
                NavigationGroup::make()
                     ->label('Media')
                     ->icon('heroicon-c-photo'),
                NavigationGroup::make()
                    ->label('Blog')
                    ->icon('heroicon-o-pencil'),
                NavigationGroup::make()
                    ->label('Publikasi')
                    ->icon('heroicon-o-pencil'),
                NavigationGroup::make()
                    ->label('Data Umum')
                    ->icon('heroicon-c-building-office-2'),
                NavigationGroup::make()
                    ->label('Kelola Akun')
                    ->icon('heroicon-c-users'),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-c-wrench-screwdriver')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Log Aplikasi')
                    ->icon('heroicon-c-cog-6-tooth'),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                AdminOverviewWidget::class,
                AdminStatusWidget::class,
                AdminRecentActivityWidget::class,
            ])
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
            ->plugins([
                \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 2,
                        'sm' => 1
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                \Jeffgreco13\FilamentBreezy\BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Settings',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->myProfileComponents([
                        'personal_info' => MyProfileExtended::class,
                    ]),
            ]);
    }
}
