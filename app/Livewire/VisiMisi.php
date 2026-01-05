<?php

namespace App\Livewire;

use App\Settings\CMSSettings;
use Livewire\Component;

class VisiMisi extends Component
{
    public function render()
    {
        $settings = app(CMSSettings::class);
        
        return view('livewire.visi-misi', [
            'title' => $settings->visi_misi_title,
            'visi_content' => $settings->visi_content,
            'misi_items' => $settings->misi_items,
        ]);
    }
}
