<?php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.public.widgets.quick-actions';
    
    protected int | string | array $columnSpan = 'full';
}