<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $statusMap = [
            // Old names -> New names
            'Verifikasi Berkas' => 'Diajukan',
            'Lolos 50 Besar' => 'Lolos ke Tahap 50 Besar',
            'Lolos 10 Besar' => 'Lolos ke Tahap 10 Besar',
            'Lolos 5 Besar' => 'Lolos ke Tahap 5 Besar',
            'Tahap Wawancara' => 'Lolos ke Tahap Wawancara',
            'Tahap Final' => 'Lolos ke Tahap Final',

            'Lolos Penilaian Tahap 1' => 'Lolos ke Tahap 50 Besar',
            'Lolos Pengumuman 50 Besar' => 'Lolos ke Tahap 50 Besar',
            'Lolos Penilaian Tahap 2' => 'Lolos ke Tahap 10 Besar',
            'Lolos Pengumuman 10 Besar' => 'Lolos ke Tahap 10 Besar',
            'Lolos Penilaian Tahap 3' => 'Lolos ke Tahap 5 Besar',
            'Lolos Pengumuman 5 Besar' => 'Lolos ke Tahap 5 Besar',
            'Lolos Tahap Wawancara' => 'Lolos ke Tahap Wawancara',
            'Lolos Tahap Final' => 'Lolos ke Tahap Final',
        ];

        foreach ($statusMap as $old => $new) {
            DB::table('pendaftar')
                ->where('status', $old)
                ->update(['status' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
