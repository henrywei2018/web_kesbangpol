<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\SKT;

class WidgetSKT extends Component
{
    public $totalskt;
    public $sktSelesai;
    public $sktProses;
    public $completionPercentage;

    public function mount()
    {
        $this->refreshData();
    }

    private function refreshData()
    {
        $this->totalskt = SKT::count();
        $this->sktSelesai = SKT::where('status', 'terbit')->count();

        $this->completionPercentage = $this->totalskt > 0
            ? round(($this->sktSelesai / $this->totalskt) * 100, 2)
            : 0;

        $this->sktProses = SKT::with('user')
            ->where('status', '!=', 'terbit')
            ->get();
    }

    public function render()
    {
        return view('livewire.widgets.widget-s-k-t', [
            'sktProses' => $this->sktProses,
        ]);
    }
}
