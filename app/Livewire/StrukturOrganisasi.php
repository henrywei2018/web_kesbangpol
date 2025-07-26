<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pegawai;

class StrukturOrganisasi extends Component
{
    public function render()
    {   $pegawai = Pegawai::all();
        return view('livewire.struktur-organisasi', compact('pegawai',));
    }
}
