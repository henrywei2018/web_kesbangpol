<?php

namespace App\View\Components;

use App\Settings\GeneralSettings;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Storage;

class ThemeProvider extends Component
{
    public $settings;
    public $themeSettings;
    public $formattedTheme;

    public function __construct()
    {
        $this->settings = app(GeneralSettings::class);
        $this->themeSettings = $this->settings->site_theme;
        $this->formattedTheme = $this->formatThemeColors();
    }

    /**
     * Format theme colors for JavaScript consumption
     */
    private function formatThemeColors()
    {
        $formatted = [];
        
        foreach ($this->themeSettings as $key => $value) {
            if (str_starts_with($value, '#')) {
                // Convert hex to RGB
                $hex = ltrim($value, '#');
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $formatted[$key] = "$r, $g, $b";
            } elseif (str_starts_with($value, 'rgb(')) {
                // Extract RGB values
                preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $value, $matches);
                if ($matches) {
                    $formatted[$key] = "{$matches[1]}, {$matches[2]}, {$matches[3]}";
                }
            } else {
                $formatted[$key] = $value;
            }
        }
        
        return $formatted;
    }

    public function render()
    {
        return view('components.theme-provider');
    }
}