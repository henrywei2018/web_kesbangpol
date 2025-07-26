<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\AgendaSidang;

class AgendaCount extends Widget
{
    protected static string $view = 'filament.widgets.agenda-count';

    public $agendaCount;
    public function mount()
    {
        $this->agendaCount = AgendaSidang::count();
    }

    protected array|string|int $columnSpan = 1;
}
