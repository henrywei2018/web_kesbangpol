<?php

namespace App\Livewire\Widgets;

use Livewire\Component;
use App\Models\SKL;

class WidgetSKL extends Component
{
    public $totalskl;
    public $sklSelesai;
    public $sklProses;
    public $completionPercentage;

    public function mount()
    {
        $this->refreshData();
    }

    private function refreshData()
    {
        $this->totalskl = SKL::count();
        $this->sklSelesai = SKL::where('status', 'terbit')->count();

        $this->completionPercentage = $this->totalskl > 0
            ? round(($this->sklSelesai / $this->totalskl) * 100, 2)
            : 0;

        // Simplified version using your model method
        $this->sklProses = SKL::with('user')
            ->where('status', '!=', 'terbit')
            ->get();

    }

    public function render()
    {
        return view('livewire.widgets.widget-s-k-l', [
            'sklProses' => $this->sklProses,
        ]);
    }
}
