<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penghargaan', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
        Schema::table('penghargaan', function (Blueprint $table) {
            $table->integer('tahun')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penghargaan', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
        Schema::table('penghargaan', function (Blueprint $table) {
            $table->date('tahun')->nullable();
        });
    }
};
