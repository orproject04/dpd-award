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
        Schema::create('beritas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sumber')->nullable();
            $table->string('tanggal')->nullable();
            $table->string('judul');
            $table->text('kutipan')->nullable();
            $table->string('gambar')->nullable();
            $table->string('tautan')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};
