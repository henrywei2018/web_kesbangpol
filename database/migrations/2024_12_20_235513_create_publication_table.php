<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->foreignId('category_id')
                  ->constrained('publication_categories')
                  ->onDelete('cascade');
            $table->foreignId('subcategory_id')
                  ->nullable()
                  ->constrained('publication_subcategories')
                  ->onDelete('set null');
            $table->date('publication_date');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('publications');
    }
};