<?php

    namespace App\Settings;

    use Spatie\LaravelSettings\Settings;

    class WhatsAppSettings extends Settings
    {
        // Basic API Configuration
        public bool $enabled;
        public ?string $api_url;
        public ?string $token;
        
        // Admin phone numbers (existing)
        public ?string $admin_main;
        public ?string $admin_backup;
        public ?string $admin_skt;
        public ?string $admin_skl;
        public ?string $admin_ppid;
        public ?string $admin_athg;

        // Group WhatsApp IDs (NEW)
        public ?string $group_skt;
        public ?string $group_skl;
        public ?string $group_ppid;
        public ?string $group_athg;

        // User Templates (NEW - for public users)
        public ?string $user_template_skt;
        public ?string $user_template_skl;
        public ?string $user_template_ppid;
        public ?string $user_template_athg;

        // Admin Templates (NEW - for admin/group notifications)
        public ?string $admin_template_skt;
        public ?string $admin_template_skl;
        public ?string $admin_template_ppid;
        public ?string $admin_template_athg;

        public static function group(): string
        {
            return 'whatsapp';
        }

        public static function defaults(): array
        {
            return [
                // Basic Configuration
                'enabled' => false,
                'api_url' => 'https://api.fonnte.com/send',
                'token' => '',
                
                // Admin phones
                'admin_main' => null,
                'admin_backup' => null,
                'admin_skt' => null,
                'admin_skl' => null,
                'admin_ppid' => null,
                'admin_athg' => null,

                // Group IDs (NEW)
                'group_skt' => null,
                'group_skl' => null,
                'group_ppid' => null,
                'group_athg' => null,

                // User Templates (NEW)
                'user_template_skt' => '🔔 *Notifikasi SKT*

    Halo {nama_pemohon}!

    Permohonan SKT Anda telah diterima dan sedang diproses.

    📋 *Detail Permohonan:*
    • ID: {id}
    • Nama Ormas: {nama_ormas}
    • Jenis: {jenis_permohonan}
    • Tanggal: {tanggal_pengajuan}

    ✅ Tim kami akan segera memproses permohonan Anda.

    Terima kasih atas kepercayaan Anda! 🙏',

                'user_template_skl' => '🔔 *Notifikasi SKL*

    Halo {nama_pemohon}!

    Permohonan SKL Anda telah diterima dan sedang diproses.

    📋 *Detail Permohonan:*
    • ID: {id}
    • Nama Organisasi: {nama_organisasi}
    • Email: {email_organisasi}
    • Tanggal: {tanggal_pengajuan}

    ✅ Tim kami akan segera memproses permohonan Anda.

    Terima kasih atas kepercayaan Anda! 🙏',

                'user_template_ppid' => '🔔 *Notifikasi Permohonan Informasi Publik*

    Halo {nama_lengkap}!

    Permohonan informasi publik Anda telah diterima dan sedang diproses.

    📋 *Detail Permohonan:*
    • ID: {id}
    • Nama: {nama_lengkap}
    • Rincian: {rincian_informasi}
    • Tanggal: {tanggal_pengajuan}

    ✅ Tim kami akan segera memproses permohonan Anda sesuai dengan ketentuan yang berlaku.

    Terima kasih atas kepercayaan Anda! 🙏',

                'user_template_athg' => '🚨 *Notifikasi Laporan ATHG*

    Halo {nama_pelapor}!

    Laporan ATHG Anda telah diterima dan sedang ditinjau.

    📋 *Detail Laporan:*
    • ID: {lapathg_id}
    • Bidang: {bidang}
    • Jenis: {jenis_athg}
    • Tingkat Urgensi: {tingkat_urgensi}
    • Tanggal: {tanggal_pengajuan}

    🔒 Laporan Anda akan ditangani dengan kerahasiaan tinggi.

    ✅ Tim kami akan segera menindaklanjuti sesuai prosedur.

    Terima kasih atas partisipasi Anda! 🙏',

                // Admin Templates (NEW)
                'admin_template_skt' => '🔔 *NOTIFIKASI SKT - KESBANGPOL KALTARA*

    Ada pengajuan SKT yang perlu perhatian:

    📋 *Detail:*
    • ID: {id}
    • Nama Ormas: {nama_ormas}
    • Jenis: {jenis_permohonan}
    • Pemohon: {nama_pemohon}
    • Status: {status}
    • Tanggal: {tanggal}

    Silakan cek panel admin untuk detail lengkap.',

                'admin_template_skl' => '🔔 *NOTIFIKASI SKL - KESBANGPOL KALTARA*

    Ada pengajuan SKL yang perlu perhatian:

    📋 *Detail:*
    • ID: {id}
    • Nama Organisasi: {nama_organisasi}
    • Email: {email_organisasi}
    • Pemohon: {nama_pemohon}
    • Status: {status}
    • Tanggal: {tanggal}

    Silakan cek panel admin untuk detail lengkap.',

                'admin_template_ppid' => '🔔 *NOTIFIKASI PPID - KESBANGPOL KALTARA*

    Ada permohonan informasi publik yang perlu perhatian:

    📋 *Detail:*
    • ID: {id}
    • Nama Pemohon: {nama_lengkap}
    • Rincian: {rincian_informasi}
    • Status: {status}
    • Tanggal: {tanggal}

    Silakan cek panel admin untuk detail lengkap.',

                'admin_template_athg' => '🚨 *LAPORAN ATHG - POKUS KALTARA*

    Ada laporan ATHG yang perlu perhatian:

    📋 *Detail:*
    • ID Laporan: {id}
    • ID ATHG: {lapathg_id}
    • Bidang: {bidang}
    • Jenis ATHG: {jenis_athg}
    • Tingkat Urgensi: {tingkat_urgensi}
    • Status: {status}
    • Tanggal: {tanggal}

    🚨 *PERHATIAN: Informasi sensitif - Tangani sesuai prosedur*

    Silakan cek panel admin untuk detail lengkap.',
            ];
        }
    }