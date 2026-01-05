<?php

namespace App\Livewire;

use Livewire\Component;

class ComponentFilterTahun extends Component
{
    public $tahun = '';
    
    public function mount()
    {
        $this->tahun = date('Y');
        $this->dispatch('global-filter-updated', ['tahun' => $this->tahun]);
    }

    public function updatedTahun()
    {
        $this->dispatch('global-filter-updated', ['tahun' => $this->tahun]);
    }

    public function render()
    {
        $years = range(date('Y'), 2020);
        return view('livewire.component-filter-tahun', ['years' => $years]);
    }
}