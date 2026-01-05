<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PublicPanel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard Public';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.public-panel';
    
    // Define a different navigation group
    protected static ?string $navigationGroup = 'User Area';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('public');
    }

    protected function authorizeAccess(): void
    {
        // Prevent direct URL access
        $this->authorize(function(): bool {
            return auth()->user()->hasRole('public');
        });
    }
}