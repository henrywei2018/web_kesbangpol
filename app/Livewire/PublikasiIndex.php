<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Publikasi;

class PublikasiIndex extends Component
{
    public function render()
    {
        return view('livewire.publikasi-index');
    }
}
