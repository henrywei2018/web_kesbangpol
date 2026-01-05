<?php

namespace App\Livewire;

use App\Settings\CMSSettings;
use Livewire\Component;

class TugasFungsi extends Component
{
    public function render()
    {
        $settings = app(CMSSettings::class);
        
        return view('livewire.tugas-fungsi', [
            'title' => $settings->tugas_fungsi_title,
            'tugas_description' => $settings->tugas_description,
            'fungsi_items' => $settings->fungsi_items,
            'image' => $settings->tugas_fungsi_image,
        ]);
    }
}
