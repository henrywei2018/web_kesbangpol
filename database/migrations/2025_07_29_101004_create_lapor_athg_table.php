<?php
// 1. Improved Migration - More focused and efficient
// database/migrations/xxxx_xx_xx_create_lapor_athg_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lapor_athg', function (Blueprint $table) {
            $table->id();
            $table->string('lapathg_id')->unique(); // Auto-generated ID
            $table->uuid('user_id'); // Changed from foreignId to uuid
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Core Report Information - Simplified
            $table->enum('bidang', [
                'ekonomi', 
                'budaya', 
                'politik', 
                'keamanan', 
                'lingkungan',
                'kesehatan'
            ]); // 6 core areas only
            
            $table->enum('jenis_athg', [
                'ancaman',
                'tantangan', 
                'hambatan',
                'gangguan'
            ]); // ATHG classification
            
            $table->string('perihal'); // Brief subject
            $table->date('tanggal'); // Date of incident
            
            // Location - Simplified
            $table->string('lokasi'); // Combined location field (Kota, Tempat)
            
            // Report Content - Streamlined  
            $table->text('deskripsi_singkat'); // Brief description (max 500 chars)
            $table->longText('detail_kejadian'); // Detailed incident description
            $table->text('sumber_informasi'); // Information source
            $table->text('dampak_potensial')->nullable(); // Potential impact
            
            // Reporter Info - Auto-filled from user profile
            $table->string('nama_pelapor');
            $table->string('kontak_pelapor'); // Phone/email
            
            // System fields
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->enum('status_athg', ['pending', 'verifikasi', 'investigasi', 'tindak_lanjut', 'selesai', 'ditolak'])
                  ->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status_athg']);
            $table->index(['bidang', 'jenis_athg']);
            $table->index(['tanggal', 'tingkat_urgensi']);
            $table->index('lapathg_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lapor_athg');
    }
};