<?php

namespace Tests\Unit\Models;

use App\Models\Pendaftar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PendaftarStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_status_before_takes_value_from_status()
    {
        $pendaftar = new Pendaftar([
            'nama' => 'John Doe',
            'nomor_registrasi' => 'DPD-TEST-001',
            'kategori' => 'Individu',
            'status' => 'Diajukan',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-Laki',
            'email' => 'john@example.com',
            'nomor_wa' => '08123456789',
            'alamat' => 'Jl. Test',
            'pendidikan' => 'S1',
            'ktp' => 'ktp.jpg',
            'foto' => 'foto.jpg',
        ]);
        $pendaftar->save();

        $this->assertEquals('Diajukan', $pendaftar->status_before);
    }

    public function test_get_display_status_returns_status_before_when_current_stage_matches_status_before()
    {
        Config::set('laravolt.ui.timeline_saat_ini', 'Diajukan');

        $pendaftar = new Pendaftar([
            'status' => 'Lolos Verifikasi Berkas',
            'status_before' => 'Diajukan',
        ]);

        // When current stage is still 'Diajukan' (same as status_before),
        // track status should show status_before ('Diajukan')
        $displayStatus = Pendaftar::getDisplayStatus($pendaftar);
        $this->assertEquals('Diajukan', $displayStatus);

        // Also test passing parameters directly
        $displayStatusParams = Pendaftar::getDisplayStatus('Lolos Verifikasi Berkas', 'Diajukan');
        $this->assertEquals('Diajukan', $displayStatusParams);
    }

    public function test_get_display_status_returns_actual_status_when_timeline_moves_to_next_stage()
    {
        Config::set('laravolt.ui.timeline_saat_ini', 'Lolos Verifikasi Berkas');

        $pendaftar = new Pendaftar([
            'status' => 'Lolos Verifikasi Berkas',
            'status_before' => 'Diajukan',
        ]);

        // When current stage reaches 'Lolos Verifikasi Berkas' (next stage),
        // track status should display the actual status ('Lolos Verifikasi Berkas')
        $displayStatus = Pendaftar::getDisplayStatus($pendaftar);
        $this->assertEquals('Lolos Verifikasi Berkas', $displayStatus);
    }

    public function test_get_display_status_with_tidak_lolos_and_stages()
    {
        Config::set('laravolt.ui.timeline_saat_ini', 'Diajukan');

        $pendaftar = new Pendaftar([
            'status' => 'Tidak Lolos',
            'status_before' => 'Diajukan',
        ]);

        // Still at 'Diajukan' stage -> shows 'Diajukan'
        $this->assertEquals('Diajukan', Pendaftar::getDisplayStatus($pendaftar));

        // When timeline advances to 'Lolos Verifikasi Berkas' -> shows 'Tidak Lolos'
        Config::set('laravolt.ui.timeline_saat_ini', 'Lolos Verifikasi Berkas');
        $this->assertEquals('Tidak Lolos', Pendaftar::getDisplayStatus($pendaftar));
    }
}
