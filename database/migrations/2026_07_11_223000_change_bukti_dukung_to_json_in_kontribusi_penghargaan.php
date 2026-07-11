<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Converts bukti_dukung in kontribusi & penghargaan tables from string to JSON.
     * Existing values are wrapped into a JSON array for backward compatibility.
     */
    public function up(): void
    {
        // Migrate kontribusi.bukti_dukung → JSON array
        DB::table('kontribusi')->get()->each(function ($row) {
            $current = $row->bukti_dukung;
            if (empty($current)) return;

            // Already a JSON array? skip.
            $decoded = json_decode($current, true);
            if (is_array($decoded)) return;

            // Wrap the existing string value into a single-element array
            DB::table('kontribusi')
                ->where('id', $row->id)
                ->update(['bukti_dukung' => json_encode([$current])]);
        });

        DB::statement('ALTER TABLE kontribusi ALTER COLUMN bukti_dukung TYPE json USING bukti_dukung::json');

        // Migrate penghargaan.bukti_dukung → JSON array
        DB::table('penghargaan')->get()->each(function ($row) {
            $current = $row->bukti_dukung;
            if (empty($current)) return;

            $decoded = json_decode($current, true);
            if (is_array($decoded)) return;

            DB::table('penghargaan')
                ->where('id', $row->id)
                ->update(['bukti_dukung' => json_encode([$current])]);
        });

        DB::statement('ALTER TABLE penghargaan ALTER COLUMN bukti_dukung TYPE json USING bukti_dukung::json');
    }

    /**
     * Reverse the migrations.
     * Flattens the JSON array back to the first element string.
     */
    public function down(): void
    {
        DB::table('kontribusi')->get()->each(function ($row) {
            $current = $row->bukti_dukung;
            if (empty($current)) return;

            $decoded = json_decode($current, true);
            $flat = is_array($decoded) ? ($decoded[0] ?? '') : $current;

            DB::table('kontribusi')
                ->where('id', $row->id)
                ->update(['bukti_dukung' => $flat]);
        });

        DB::statement("ALTER TABLE kontribusi ALTER COLUMN bukti_dukung TYPE varchar(500) USING (bukti_dukung#>>'{0}')");

        DB::table('penghargaan')->get()->each(function ($row) {
            $current = $row->bukti_dukung;
            if (empty($current)) return;

            $decoded = json_decode($current, true);
            $flat = is_array($decoded) ? ($decoded[0] ?? '') : $current;

            DB::table('penghargaan')
                ->where('id', $row->id)
                ->update(['bukti_dukung' => $flat]);
        });

        DB::statement("ALTER TABLE penghargaan ALTER COLUMN bukti_dukung TYPE varchar(500) USING (bukti_dukung#>>'{0}')");
    }
};
