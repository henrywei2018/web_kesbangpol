<?php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\Widget;

class WelcomeCard extends Widget
{
    protected static string $view = 'filament.public.widgets.welcome-card';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getViewData(): array
    {
        $user = auth()->user();
        
        return [
            'user' => $user,
            'greeting' => $this->getGreeting(),
        ];
    }
    
    private function getGreeting(): string
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Selamat pagi';
        } elseif ($hour < 17) {
            return 'Selamat siang';
        } elseif ($hour < 19) {
            return 'Selamat sore';
        } else {
            return 'Selamat malam';
        }
    }
}