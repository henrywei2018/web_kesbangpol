<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Remove old settings
        $this->migrator->delete('cms.profil_content');
        $this->migrator->delete('cms.visi_content');
        $this->migrator->delete('cms.misi_content');
        $this->migrator->delete('cms.tugas_content');
        $this->migrator->delete('cms.fungsi_content');
        $this->migrator->delete('cms.struktur_content');
        $this->migrator->delete('cms.struktur_image');
        
        // Add new structured settings
        // Profil Organisasi
        $this->migrator->add('cms.profil_subtitle', 'Badan Kesatuan Bangsa dan Politik');
        $this->migrator->add('cms.profil_description', 'Badan Kesatuan Bangsa dan Politik adalah perangkat pemerintah yang bertugas melaksanakan urusan pemerintah di bidang kesatuan bangsa dan politik dalam negeri dipimpin oleh Kepala Badan yang bertanggung jawab kepada Gubernur melalui Sekretaris Daerah.');
        $this->migrator->add('cms.profil_features', [
            'Merumuskan kebijakan teknis di bidang kesatuan bangsa dan politik.',
            'Melaksanakan kebijakan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan.',
            'Menyelenggarakan politik dalam negeri dan kehidupan demokrasi.',
            'Membina kerukunan antar suku, umat beragama, ras, dan golongan lainnya.',
            'Membina dan memberdayakan organisasi kemasyarakatan.',
            'Melaksanakan kewaspadaan nasional dan penanganan konflik sosial.'
        ]);
        $this->migrator->add('cms.profil_image', null);
        
        // Visi & Misi
        $this->migrator->add('cms.visi_content', 'TERWUJUDNYA PROVINSI KALIMANTAN UTARA YANG BERUBAH, MAJU DAN SEJAHTERA');
        $this->migrator->add('cms.misi_items', [
            'Mewujudkan Kalimantan Utara, yang aman, nyaman dan damai melalui penyelenggaraan pemerintahan yang baik.',
            'Mewujudkan sistem Pemerintahan provinsi yang ditopang oleh Tata Kelola Pemerintah Kabupaten/Kota sebagai pilar utama secara profesional, efisien, efektif, dan fokus pada sistem penganggaran yang berbasiskan kinerja.',
            'Mewujudkan pembangunan Sumber Daya Manusia yang sehat, cerdas, kreatif, inovatif, berakhlak mulia, produktif dan berdaya saing dengan berbasiskan Pendidikan wajib belajar 16 Tahun dan berwawasan.',
            'Mewujudkan pemanfaatan dan pengelolaan Sumber Daya Alam dengan nilai tambah tinggi dan berwawasan lingkungan yang berkelanjutan, secara efisien, terencana, menyeluruh, terarah, terpadu, dan bertahap dengan berbasiskan Ilmu Pengetahuan dan Teknologi.'
        ]);
        
        // Tugas & Fungsi
        $this->migrator->add('cms.tugas_description', 'Badan Kesatuan Bangsa dan Politik mempunyai tugas pokok membantu Bupati dalam penyelenggaraan pemerintahan daerah dibidang kesatuan bangsa dan politik.');
        $this->migrator->add('cms.fungsi_items', [
            'Perumusan kebijakan teknis di bidang kesatuan bangsa dan politik di wilayah kabupaten sesuai dengan ketentuan peraturan perundang-undangan.',
            'Pelaksanaan kebijakan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi.',
            'Pelaksanaan koordinasi di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi.',
            'Pelaksanaan evaluasi dan pelaporan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi.',
            'Pelaksanaan fasilitasi forum koordinasi pimpinan daerah.',
            'Pelaksanaan administrasi kesekretariatan badan kesatuan bangsa dan politik Provinsi.',
            'Pelaksanaan tugas lain yang diberikan oleh bupati sesuai dengan tugas dan fungsinya.'
        ]);
        $this->migrator->add('cms.tugas_fungsi_image', null);
        
        // Struktur Organisasi
        $this->migrator->add('cms.struktur_description', 'Struktur organisasi Badan Kesatuan Bangsa dan Politik Provinsi Kalimantan Utara.');
        $this->migrator->add('cms.struktur_chart_image', null);
    }
};
