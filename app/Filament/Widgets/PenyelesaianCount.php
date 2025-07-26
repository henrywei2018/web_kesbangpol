<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\PenyelesaianSengketaInformasi;

class PenyelesaianCount extends Widget
{
    protected static string $view = 'filament.widgets.penyelesaian-count';
    public $penyelesaianCount;
    public function mount()
    {
        $this->penyelesaianCount = PenyelesaianSengketaInformasi::count();
    }

    protected array|string|int $columnSpan = 1;
}
