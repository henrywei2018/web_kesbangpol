<?php

namespace App\Livewire;

use Filament\Pages\Page;

class PanelHome extends Page
{
	protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard'; // Change to Dashboard
    protected static ?string $slug = 'dashboard'; // Change to dashboard
    protected static ?int $navigationSort = -2;
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'livewire.panel-home';
}
