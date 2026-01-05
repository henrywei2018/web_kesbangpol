<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mt_status', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('status', 50); // Status name (e.g., Pending, Approved)
            $table->text('deskripsi_status')->nullable(); // Optional description

            // Polymorphic relationship fields
            $table->unsignedBigInteger('layanan_id'); // The ID of the layanan being referenced
            $table->string('layanan_type'); // The class name of the layanan model (e.g., PermohonanInformasiPublik)

            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mt_status');
    }
}
