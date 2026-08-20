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
        Schema::table('kontribusi', function (Blueprint $table) {
            $table->boolean('is_from_admin')->default(false)->after('bukti_dukung');
        });

        Schema::table('penghargaan', function (Blueprint $table) {
            $table->boolean('is_from_admin')->default(false)->after('bukti_dukung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontribusi', function (Blueprint $table) {
            $table->dropColumn('is_from_admin');
        });

        Schema::table('penghargaan', function (Blueprint $table) {
            $table->dropColumn('is_from_admin');
        });
    }
};
