<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\PermohonanInformasiPublik;

class PermohonanCount extends Widget
{
    protected static string $view = 'filament.widgets.permohonan-count';

    public $permohonanCount;
    public function mount()
    {
        $this->permohonanCount = PermohonanInformasiPublik::count();
    }

    protected array|string|int $columnSpan = 1;
}
