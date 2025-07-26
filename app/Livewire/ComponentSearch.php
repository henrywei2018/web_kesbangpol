<?php

namespace App\Livewire;

use Livewire\Component;

class ComponentSearch extends Component
{
    public $search = '';

    public function updatedSearch()
    {
        $this->dispatch('global-filter-updated', ['search' => $this->search]);
    }

    public function render()
    {
        return view('livewire.component-search');
    }
}