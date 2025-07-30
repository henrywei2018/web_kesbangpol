<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ormas_master', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // === IDENTITAS ORGANISASI ===
            $table->string('nomor_registrasi')->unique(); // Auto-generated: ORM-KALTARA-YYYY-NNNN
            $table->string('nama_ormas');
            $table->string('nama_singkatan_ormas')->nullable();
            
            // === STATUS ADMINISTRASI ===
            $table->enum('status_administrasi', [
                'belum_selesai', 
                'selesai'
            ])->default('belum_selesai');
            
            $table->text('keterangan_status')->nullable();
            $table->timestamp('tanggal_selesai_administrasi')->nullable();
            
            // === REFERENSI SKT/SKL ===
            $table->enum('sumber_registrasi', ['skt', 'skl']); // Dari mana ORMAS didaftarkan
            
            $table->foreignId('skt_id')->nullable()->constrained('skts')->nullOnDelete();
            $table->foreignId('skl_id')->nullable()->constrained('skls')->nullOnDelete(); // Uncomment jika ada tabel skls
            
            // === DATA DASAR (Copy dari SKT) ===
            $table->string('tempat_pendirian')->nullable();
            $table->date('tanggal_pendirian')->nullable();
            $table->string('bidang_kegiatan')->nullable();
            $table->enum('ciri_khusus', [
                'Keagamaan',
                'Kewanitaan', 
                'Kepemudaan',
                'Kesamaan Profesi',
                'Kesamaan Kegiatan',
                'Kesamaan Bidang',
                'Mitra K/L'
            ])->nullable();
            $table->text('tujuan_ormas')->nullable();
            
            // === ALAMAT (Copy dari SKT) ===
            $table->text('alamat_sekretariat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kab_kota')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('nomor_handphone')->nullable();
            $table->string('nomor_fax')->nullable();
            $table->string('email')->nullable();
            
            // === DATA LEGAL (Copy dari SKT) ===
            $table->string('nomor_akta_notaris')->nullable();
            $table->date('tanggal_akta_notaris')->nullable();
            $table->string('jenis_akta', 255)->nullable();
            $table->string('nomor_npwp')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening_bank')->nullable();
            
            // === STRUKTUR ORGANISASI (Copy dari SKT) ===
            $table->string('ketua_nama_lengkap')->nullable();
            $table->string('ketua_nik')->nullable();
            $table->date('ketua_masa_bakti_akhir')->nullable();
            
            $table->string('sekretaris_nama_lengkap')->nullable();
            $table->string('sekretaris_nik')->nullable();
            $table->date('sekretaris_masa_bakti_akhir')->nullable();
            
            $table->string('bendahara_nama_lengkap')->nullable();
            $table->string('bendahara_nik')->nullable();
            $table->date('bendahara_masa_bakti_akhir')->nullable();
            
            // === DATA PENDIRI/PEMBINA (Copy dari SKT) ===
            $table->json('nama_pendiri')->nullable();
            $table->json('nik_pendiri')->nullable();
            $table->json('nama_pembina')->nullable();
            $table->json('nik_pembina')->nullable();
            $table->json('nama_penasihat')->nullable();
            $table->json('nik_penasihat')->nullable();
            
            // === TRACKING ===
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('first_registered_at'); // Kapan pertama kali masuk master
            $table->timestamp('last_updated_from_source_at')->nullable(); // Kapan terakhir update dari SKT/SKL
            
            $table->timestamps();
            $table->softDeletes();
            
            // === INDEXES ===
            $table->index(['status_administrasi']);
            $table->index(['sumber_registrasi']);
            $table->index(['nama_ormas']);
            $table->index(['skt_id']);
            $table->index(['skl_id']);
            $table->index(['nomor_registrasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ormas_master');
    }
};