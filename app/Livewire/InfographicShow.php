<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Infographic;



class InfographicShow extends Component
{
    public $infographic;

    public function mount($slug)
    {
        $this->infographic = Infographic::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.infographic-show', [
            'infographic' => $this->infographic,
        ]);
    }
}