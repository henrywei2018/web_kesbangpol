<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Galeri;


class GaleriShow extends Component
{
    public $galeri;

    public function mount($slug)
    {
        // Fetch the galeri record by slug
        $this->galeri = Galeri::where('slug', $slug)->firstOrFail();

    }

    public function render()
    {
        return view('livewire.galeri-show', [
            'galeri' => $this->galeri
        ]);
    }
}
