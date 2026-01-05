<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Update with actual original content from Blade templates
        
        // Profil Organisasi - Extract from profil-organisasi.blade.php
        $this->migrator->update('cms.profil_title', fn () => 'Profil Organisasi');
        $this->migrator->update('cms.profil_subtitle', fn () => 'Badan Kesatuan Bangsa dan Politik');
        $this->migrator->update('cms.profil_description', fn () => 'Badan Kesatuan Bangsa dan Politik adalah perangkat pemerintah yang bertugas melaksanakan urusan pemerintah di bidang kesatuan bangsa dan politik dalam negeri dipimpin oleh Kepala Badan yang bertanggung jawab kepada Gubernur melalui Sekretaris Daerah.');
        $this->migrator->update('cms.profil_features', fn () => [
            ['feature' => 'Merumuskan kebijakan teknis di bidang kesatuan bangsa dan politik.'],
            ['feature' => 'Melaksanakan kebijakan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan.'],
            ['feature' => 'Menyelenggarakan politik dalam negeri dan kehidupan demokrasi.'],
            ['feature' => 'Membina kerukunan antar suku, umat beragama, ras, dan golongan lainnya.'],
            ['feature' => 'Membina dan memberdayakan organisasi kemasyarakatan.'],
            ['feature' => 'Melaksanakan kewaspadaan nasional dan penanganan konflik sosial.']
        ]);
        
        // Visi & Misi - Extract from visi-misi.blade.php
        $this->migrator->update('cms.visi_misi_title', fn () => 'VISI & MISI');
        $this->migrator->update('cms.visi_content', fn () => 'TERWUJUDNYA PROVINSI KALIMANTAN UTARA YANG BERUBAH, MAJU DAN SEJAHTERA');
        $this->migrator->update('cms.misi_items', fn () => [
            ['misi' => 'Mewujudkan Kalimantan Utara, yang aman, nyaman dan damai melalui penyelenggaraan pemerintahan yang baik.'],
            ['misi' => 'Mewujudkan sistem Pemerintahan provinsi yang ditopang oleh Tata Kelola Pemerintah Kabupaten/Kota sebagai pilar utama secara profesional, efisien, efektif, dan fokus pada sistem penganggaran yang berbasiskan kinerja.'],
            ['misi' => 'Mewujudkan pembangunan Sumber Daya Manusia yang sehat, cerdas, kreatif, inovatif, berakhlak mulia, produktif dan berdaya saing dengan berbasiskan Pendidikan wajib belajar 16 Tahun dan berwawasan.'],
            ['misi' => 'Mewujudkan pemanfaatan dan pengelolaan Sumber Daya Alam dengan nilai tambah tinggi dan berwawasan lingkungan yang berkelanjutan, secara efisien, terencana, menyeluruh, terarah, terpadu, dan bertahap dengan berbasiskan Ilmu Pengetahuan dan Teknologi.'],
            ['misi' => 'Mewujudkan peningkatan pembangunan infrastruktur pedesaan, pedalaman, perkotaan, pesisir dan perbatasan untuk meningkatkan mobilisasi dan produktivitas daerah dalam rangka pemerataan pembangunan.'],
            ['misi' => 'Mewujudkan peningkatan ekonomi yang berdaya saing, mengurangi kesenjangan antar wilayah, serta meningkatkan ketahanan pangan dengan berorientasi pada kepentingan rakyat melalui sektor perdagangan, jasa, industri, pariwisata, dan pertanian dalam arti luas dengan pengembangan infrastruktur yang berkualitas dan merata serta meningkatkan konektivitas antar kabupaten/kota.'],
            ['misi' => 'Mewujudkan kualitas kerukunan kehidupan beragama dan etnis dengan berbagai latar belakang budaya dalam kerangka semangat Kebhinekaan di provinsi Kalimantan Utara.'],
            ['misi' => 'Mewujudkan ketahanan Energi dan pengembangan PLTA serta energi terbarukan dengan pemanfaatan potensi daerah.'],
            ['misi' => 'Mewujudkan peningkatan kualitas kesetaraan gender dan Milenial dalam pembangunan.'],
            ['misi' => 'Mewujudkan perlindungan dan pemberdayaan Koperasi dan UMKM.'],
            ['misi' => 'Meningkatkan kinerja Pembangunan dan Investasi Daerah dengan melibatkan Pengusaha dan investor Lokal serta Nasional.'],
            ['misi' => 'Memberi bantuan pengembangan sektor produktif dan potensi strategis di setiap desa dan kelurahan melalui Pengembangan Produk lokal masing-masing Kabupaten/Kota.'],
            ['misi' => 'Mewujudkan pembangunan yang berbasiskan RT/Komunitas dalam upaya gerakan membangun desa menata kota, serta memberi Bantuan Keuangan kepada Kabupaten/Kota sebagai pilar provinsi sesuai kemampuan APBD setiap Tahun.'],
            ['misi' => 'Mewujudkan Tanjung Selor menjadi DOB sebagai Ibu Kota Provinsi Kalimantan Utara serta beberapa DOB yang telah diusulkan yaitu; Kota Sebatik, Kabupaten Kabudaya, Kabupaten Kerayan, Kabupaten Apo Kayan.']
        ]);
        
        // Tugas & Fungsi - Extract from tugas-fungsi.blade.php
        $this->migrator->update('cms.tugas_fungsi_title', fn () => 'TUGAS & FUNGSI');
        $this->migrator->update('cms.tugas_description', fn () => 'Badan Kesatuan Bangsa dan Politik mempunyai tugas pokok membantu Bupati dalam penyelenggaraan pemerintahan daerah dibidang kesatuan bangsa dan politik.');
        $this->migrator->update('cms.fungsi_items', fn () => [
            ['fungsi' => 'Perumusan kebijakan teknis di bidang kesatuan bangsa dan politik di wilayah kabupaten sesuai dengan ketentuan peraturan perundang-undangan.'],
            ['fungsi' => 'Pelaksanaan kebijakan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi, pemeliharaan ketahan ekonomi, sosial dan budaya, pembinaan kerukunan antar suku, umat beragama, ras, dan golongan lainnya, pembinaan dan pemberdayaan organisasi kemasyarakatan, serta pelaksanaan kewaspadaan nasional dan penanganan konflik sosial di wilayah kabupaten sesuai dengan ketentuan peraturan perundang-undangan.'],
            ['fungsi' => 'Pelaksanaan koordinasi di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi, pemeliharaan ketahanan ekonomi, sosial dan budaya, pembinaan kerukunan antar suku, umat beragama, ras, dan golongan lainnya, fasilitasi organisasi kemasyarakatan, serta pelaksanaan kewaspadaan nasional dan penanganan konflik sosial di wilayah kabupaten sesuai dengan ketentuan peraturan perundang-undangan.'],
            ['fungsi' => 'Pelaksanaan evaluasi dan pelaporan di bidang pembinaan ideologi Pancasila dan wawasan kebangsaan, penyelenggaraan politik dalam negeri dan kehidupan demokrasi, pemeliharaan ketahan ekonomi, sosial dan budaya, pembinaan kerukunan antar suku, umat beragama, ras, dan golongan lainnya, pembinaan dan pemberdayaan organisasi kemasyarakatan, serta pelaksanaan kewaspadaan nasional dan penanganan konflik sosial di wilayah kabupaten sesuai dengan ketentuan peraturan perundang-undangan.'],
            ['fungsi' => 'Pelaksanaan fasilitasi forum koordinasi pimpinan daerah.'],
            ['fungsi' => 'Pelaksanaan administrasi kesekretariatan badan kesatuan bangsa dan politik Provinsi.'],
            ['fungsi' => 'Pelaksanaan tugas lain yang diberikan oleh bupati sesuai dengan tugas dan fungsinya.']
        ]);
        
        // Struktur Organisasi - Extract from struktur-organisasi.blade.php
        $this->migrator->update('cms.struktur_organisasi_title', fn () => 'Struktur Organisasi');
        $this->migrator->update('cms.struktur_description', fn () => 'Struktur organisasi Badan Kesatuan Bangsa dan Politik Provinsi Kalimantan Utara menggambarkan hierarki dan pembagian tugas dalam organisasi. Struktur ini terdiri dari dua bagian utama: bagan struktur organisasi dan profil kepegawaian.');
    }
};
