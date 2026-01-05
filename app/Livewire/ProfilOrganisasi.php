<?php

namespace App\Livewire;

use App\Settings\CMSSettings;
use Livewire\Component;

class ProfilOrganisasi extends Component
{
    public function render()
    {
        $settings = app(CMSSettings::class);
        
        return view('livewire.profil-organisasi', [
            'title' => $settings->profil_title,
            'subtitle' => $settings->profil_subtitle,
            'description' => $settings->profil_description,
            'features' => $settings->profil_features,
            'image' => $settings->profil_image,
        ]);
    }
}
