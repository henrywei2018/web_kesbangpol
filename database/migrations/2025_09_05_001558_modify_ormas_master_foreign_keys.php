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
        Schema::table('ormas_master', function (Blueprint $table) {
            // Drop existing foreign keys with nullOnDelete
            $table->dropForeign(['skt_id']);
            $table->dropForeign(['skl_id']);
            
            // Recreate foreign keys WITHOUT nullOnDelete so Observer can handle deletion
            $table->foreign('skt_id')->references('id')->on('skts');
            $table->foreign('skl_id')->references('id')->on('skls');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ormas_master', function (Blueprint $table) {
            // Drop the modified foreign keys
            $table->dropForeign(['skt_id']);
            $table->dropForeign(['skl_id']);
            
            // Restore original foreign keys with nullOnDelete
            $table->foreign('skt_id')->references('id')->on('skts')->nullOnDelete();
            $table->foreign('skl_id')->references('id')->on('skls')->nullOnDelete();
        });
    }
};
