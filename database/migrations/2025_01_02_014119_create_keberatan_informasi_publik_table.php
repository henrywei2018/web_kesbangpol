<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keberatan_informasi', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permohonan_id')->constrained('permohonan_informasi_publik')->onDelete('cascade');
            $table->string('nomor_registrasi')->unique();
            $table->string('nik_no_identitas', 16)->unique();
            $table->string('no_telp');
            $table->string('pekerjaan');
            $table->text('alamat');
            $table->text('rincian_informasi');
            $table->text('tujuan_keberatan');
            $table->text('alasan_keberatan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keberatan_informasi');
    }
};