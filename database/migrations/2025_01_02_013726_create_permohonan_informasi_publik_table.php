<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermohonanInformasiPublikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permohonan_informasi_publik', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->string('nomor_register', 100)->unique(); // Unique registration number
            $table->string('nik_no_identitas', 16)->unique(); // Unique NIK/identity number
            $table->string('ktp_path')->nullable(); // Path to uploaded KTP file
            $table->text('alamat'); // Address
            $table->string('no_telp', 20); // Phone number
            $table->string('pekerjaan', 100)->nullable(); // Occupation (nullable)
            $table->text('tujuan_penggunaan_informasi')->nullable();
            $table->text('rincian_informasi')->nullable();
            $table->enum('cara_memperoleh_informasi', ['Melihat', 'Membaca', 'Mendengarkan', 'Mencatat']);
            $table->enum('mendapatkan_salinan_informasi', ['Softcopy', 'Hardcopy']); // How the information will be delivered
            $table->enum('cara_mendapatkan_salinan', ['Mengambil Langsung', 'Faksimili', 'Email']); // Method to retrieve the copy

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permohonan_informasi_publik');
    }
}
