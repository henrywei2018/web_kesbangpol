<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CMSSettings extends Settings
{
    // Profil Organisasi
    public string $profil_title;
    public string $profil_subtitle;
    public string $profil_description;
    public array $profil_features;
    public ?string $profil_image;
    
    // Visi & Misi
    public string $visi_misi_title;
    public string $visi_content;
    public array $misi_items;
    
    // Tugas & Fungsi
    public string $tugas_fungsi_title;
    public string $tugas_description;
    public array $fungsi_items;
    public ?string $tugas_fungsi_image;
    
    // Struktur Organisasi
    public string $struktur_organisasi_title;
    public string $struktur_description;
    public ?string $struktur_chart_image;

    public static function group(): string
    {
        return 'cms';
    }
}