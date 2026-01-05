<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('cms.profil_title', 'Profil Organisasi');
        $this->migrator->add('cms.profil_content', 'Konten profil organisasi akan diisi di sini...');
        
        $this->migrator->add('cms.visi_misi_title', 'Visi & Misi');
        $this->migrator->add('cms.visi_content', 'Visi organisasi...');
        $this->migrator->add('cms.misi_content', 'Misi organisasi...');
        
        $this->migrator->add('cms.tugas_fungsi_title', 'Tugas & Fungsi');
        $this->migrator->add('cms.tugas_content', 'Tugas organisasi...');
        $this->migrator->add('cms.fungsi_content', 'Fungsi organisasi...');
        
        $this->migrator->add('cms.struktur_organisasi_title', 'Struktur Organisasi');
        $this->migrator->add('cms.struktur_content', 'Struktur organisasi...');
        $this->migrator->add('cms.struktur_image', null);
    }
};
