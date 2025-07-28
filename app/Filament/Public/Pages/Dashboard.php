<?php

namespace App\Filament\Public\Pages;

use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.public.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            // Add public user specific widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets for public users
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard Publik';
    }

    public function getHeading(): string
    {
        $user = auth()->user();
        return "Selamat datang, {$user->firstname}!";
    }

    public function getSubheading(): ?string
    {
        return 'Panel untuk pengguna publik - akses layanan dan informasi yang tersedia.';
    }
}