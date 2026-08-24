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
        Schema::create('kategori_aspeks', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('aspek');
            $table->text('dimensi')->nullable();
            $table->integer('bobot')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_aspeks');
    }
};
