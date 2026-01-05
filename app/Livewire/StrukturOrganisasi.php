<?php

namespace App\Livewire;

use App\Settings\CMSSettings;
use Livewire\Component;
use App\Models\Pegawai;

class StrukturOrganisasi extends Component
{
    public function render()
    {   
        $settings = app(CMSSettings::class);
        $pegawai = Pegawai::all();
        
        return view('livewire.struktur-organisasi', [
            'title' => $settings->struktur_organisasi_title,
            'description' => $settings->struktur_description,
            'struktur_chart_image' => $settings->struktur_chart_image,
            'pegawai' => $pegawai,
        ]);
    }
}
