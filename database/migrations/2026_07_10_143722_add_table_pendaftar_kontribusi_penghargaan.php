<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_registrasi', 20);
            $table->string('kategori', 20)->index();
            $table->string('nama', 255);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir')->index();
            $table->string('jenis_kelamin', 15)->index();
            $table->string('pendidikan', 15)->index();
            $table->text('alamat');
            $table->string('nomor_wa', 15);
            $table->string('email', 50);
            $table->string('ktp', 500);
            $table->string('foto', 500);
            $table->timestamps();
        });

        Schema::create('kontribusi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftar_id')->index()->constrained('pendaftar')->cascadeOnDelete();
            $table->string('judul', 500);
            $table->text('deskripsi');
            $table->text('dampak');
            $table->string('bukti_dukung', 500);
            $table->timestamps();
        });

        Schema::create('penghargaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftar_id')->index()->constrained('pendaftar')->cascadeOnDelete();
            $table->string('uraian', 500);
            $table->date('tahun');
            $table->string('bukti_dukung', 500);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghargaan');
        Schema::dropIfExists('kontribusi');
        Schema::dropIfExists('pendaftar');
    }
};
