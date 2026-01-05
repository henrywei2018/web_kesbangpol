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
        Schema::create('lapor_giat', function (Blueprint $table) {
            $table->id();
            $table->foreignuuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_ormas');
            $table->string('ketua_nama_lengkap');
            $table->string('nomor_handphone');
            $table->date('tanggal_kegiatan');
            $table->string('laporan_kegiatan_path')->nullable(); // PDF file path
            $table->json('images_paths')->nullable(); // Array of image paths
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('keterangan')->nullable(); // Admin notes
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('status');
            $table->index('tanggal_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapor_giat');
    }
};