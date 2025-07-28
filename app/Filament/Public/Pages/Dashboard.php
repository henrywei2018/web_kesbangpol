<?php

namespace App\Filament\Public\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Public\Widgets\StatsOverview;
use App\Filament\Public\Widgets\WelcomeCard;
use App\Filament\Public\Widgets\RecentActivity;
use App\Filament\Public\Widgets\QuickActions;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    // Use the custom view if you want, or let Filament handle it with widgets
    // protected static string $view = 'filament.public.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            WelcomeCard::class,
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            QuickActions::class,
            RecentActivity::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        return 'Dashboard Publik';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola permohonan dan keberatan informasi publik Anda.';
    }
}