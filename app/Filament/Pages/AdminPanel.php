<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AdminPanel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-c-home';
    protected static ?string $title = 'Dashboard Admin';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.admin-panel';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public function mount(): void
    {
        // Redirect super-admin to their dashboard
        if (auth()->user()->hasRole('super-admin')) {
            redirect()->to('/admin')->send();
        }
    }
}