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
        Schema::create('pendaftar_kertas_kerja', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftar_id')->index()->constrained('pendaftar')->cascadeOnDelete();
            $table->foreignId('kategori_aspek_id')->nullable()->index()->constrained('kategori_aspeks')->nullOnDelete();
            $table->string('aspek', 255);
            $table->text('dimensi')->nullable();
            $table->integer('bobot')->default(0);
            $table->integer('nilai')->nullable();
            $table->decimal('total', 8, 2)->nullable();
            $table->text('catatan_juri')->nullable();
            $table->text('tracking_media')->nullable();
            $table->json('data_dukung')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftar_kertas_kerja');
    }
};
