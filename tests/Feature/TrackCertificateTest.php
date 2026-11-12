<?php

declare(strict_types=1);

use App\Models\Pendaftar;
use App\Models\PendaftarRiwayat;
use App\Services\SertifikatService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

function makePendaftarForTrack(array $overrides = []): Pendaftar
{
    static $seq = 1;
    $seq++;

    return Pendaftar::create(array_merge([
        'nama' => 'Peserta Uji ' . $seq,
        'nomor_registrasi' => sprintf('DPD-BK26-990101%04d', $seq),
        'kategori' => 'Kesehatan',
        'status' => 'Diajukan',
        'status_before' => 'Diajukan',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'Laki-Laki',
        'email' => "peserta{$seq}@example.com",
        'nomor_wa' => '08123456789',
        'alamat' => 'Jl. Pengujian No. 1',
        'pendidikan' => 'S1',
        'ktp' => 'ktp.jpg',
        'foto' => 'foto.jpg',
    ], $overrides));
}

function writeFakeCertificatePdf(string $certRoot, string $folder, string $filename, string $content = '%PDF-1.4 fake'): string
{
    $dir = $certRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
    File::ensureDirectoryExists($dir);
    $path = $dir . DIRECTORY_SEPARATOR . $filename;
    file_put_contents($path, $content);

    return $path;
}

beforeEach(function (): void {
    $this->tempCertRoot = storage_path('app/private/test_sertifikat_' . bin2hex(random_bytes(6)));
    File::ensureDirectoryExists($this->tempCertRoot);

    $this->app->bind(SertifikatService::class, fn () => new SertifikatService($this->tempCertRoot));
});

afterEach(function (): void {
    if (isset($this->tempCertRoot) && is_dir($this->tempCertRoot)) {
        File::deleteDirectory($this->tempCertRoot);
    }
});

it('recognizes stage folder names smartly without false positives', function (): void {
    $service = new SertifikatService($this->tempCertRoot);

    expect($service->classifyStageFolder('verifikasi_berkas'))->toBe(1)
        ->and($service->classifyStageFolder('Lolos Verifikasi Berkas'))->toBe(1)
        ->and($service->classifyStageFolder('50_besar'))->toBe(2)
        ->and($service->classifyStageFolder('50 Besar'))->toBe(2)
        ->and($service->classifyStageFolder('10-besar'))->toBe(3)
        ->and($service->classifyStageFolder('3_besar'))->toBe(4)
        ->and($service->classifyStageFolder('penjurian_3_besar'))->toBe(5)
        ->and($service->classifyStageFolder('verifikasi_lapangan'))->toBe(5)
        ->and($service->classifyStageFolder('final'))->toBe(6)
        ->and($service->classifyStageFolder('13_besar'))->toBeNull()
        ->and($service->classifyStageFolder('draft_internal'))->toBeNull();
});

it('returns only eligible certificates matching participant stage and timeline', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 50 Besar');

    $pendaftar50 = makePendaftarForTrack([
        'status' => 'Lolos ke Tahap 50 Besar',
        'status_before' => 'Lolos Verifikasi Berkas',
    ]);
    writeFakeCertificatePdf($this->tempCertRoot, 'verifikasi_berkas', $pendaftar50->nomor_registrasi . '.pdf', '%PDF-berkas');
    writeFakeCertificatePdf($this->tempCertRoot, '50_besar', $pendaftar50->nomor_registrasi . '.pdf', '%PDF-50besar');
    // File di folder 10_besar tidak boleh muncul untuk peserta 50 besar
    writeFakeCertificatePdf($this->tempCertRoot, '10_besar', $pendaftar50->nomor_registrasi . '.pdf', '%PDF-10besar');

    $response = $this->postJson(route('track'), [
        'reg_id' => strtolower($pendaftar50->nomor_registrasi),
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.status', 'Lolos ke Tahap 50 Besar')
        ->assertJsonCount(2, 'data.certificates')
        ->assertJsonPath('data.certificates.0.stage_key', '50_besar')
        ->assertJsonPath('data.certificates.1.stage_key', 'verifikasi_berkas');
});

it('hides future stage certificates before timeline announcement date', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 50 Besar');

    $pendaftar10 = makePendaftarForTrack([
        'status' => 'Lolos ke Tahap 10 Besar',
        'status_before' => 'Lolos ke Tahap 50 Besar',
    ]);
    writeFakeCertificatePdf($this->tempCertRoot, '50_besar', $pendaftar10->nomor_registrasi . '.pdf');
    writeFakeCertificatePdf($this->tempCertRoot, '10_besar', $pendaftar10->nomor_registrasi . '.pdf');

    $response = $this->postJson(route('track'), [
        'reg_id' => $pendaftar10->nomor_registrasi,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'Lolos ke Tahap 50 Besar')
        ->assertJsonCount(1, 'data.certificates')
        ->assertJsonPath('data.certificates.0.stage_key', '50_besar');

    // Setelah timeline masuk ke 10 Besar, kedua sertifikat muncul
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 10 Besar');

    $this->postJson(route('track'), ['reg_id' => $pendaftar10->nomor_registrasi])
        ->assertOk()
        ->assertJsonPath('data.status', 'Lolos ke Tahap 10 Besar')
        ->assertJsonCount(2, 'data.certificates')
        ->assertJsonPath('data.certificates.0.stage_key', '10_besar')
        ->assertJsonPath('data.certificates.1.stage_key', '50_besar');
});

it('keeps certificate access for stages passed before Tidak Lolos', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 3 Besar');

    // Gugur saat seleksi 10 besar setelah lolos 50 besar
    $gugur10 = makePendaftarForTrack([
        'status' => 'Tidak Lolos',
        'status_before' => 'Lolos ke Tahap 50 Besar',
    ]);
    writeFakeCertificatePdf($this->tempCertRoot, 'verifikasi_berkas', $gugur10->nomor_registrasi . '.pdf');
    writeFakeCertificatePdf($this->tempCertRoot, '50_besar', $gugur10->nomor_registrasi . '.pdf');
    writeFakeCertificatePdf($this->tempCertRoot, '10_besar', $gugur10->nomor_registrasi . '.pdf');

    $this->postJson(route('track'), ['reg_id' => $gugur10->nomor_registrasi])
        ->assertOk()
        ->assertJsonPath('data.status', 'Tidak Lolos Tahap 10 Besar')
        ->assertJsonCount(2, 'data.certificates')
        ->assertJsonPath('data.certificates.0.stage_key', '50_besar')
        ->assertJsonPath('data.certificates.1.stage_key', 'verifikasi_berkas');

    // Gugur langsung pada verifikasi berkas -> tidak berhak sertifikat apa pun
    $gugurBerkas = makePendaftarForTrack([
        'status' => 'Tidak Lolos',
        'status_before' => 'Diajukan',
    ]);
    writeFakeCertificatePdf($this->tempCertRoot, 'verifikasi_berkas', $gugurBerkas->nomor_registrasi . '.pdf');

    $this->postJson(route('track'), ['reg_id' => $gugurBerkas->nomor_registrasi])
        ->assertOk()
        ->assertJsonPath('data.status', 'Tidak Lolos Tahap Verifikasi Berkas')
        ->assertJsonCount(0, 'data.certificates');
});

it('falls back to riwayats when status_before is empty or Tidak Lolos', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 50 Besar');

    $pendaftar = makePendaftarForTrack([
        'status' => 'Tidak Lolos',
        'status_before' => 'Tidak Lolos',
    ]);
    PendaftarRiwayat::create([
        'pendaftar_id' => $pendaftar->id,
        'status' => 'Lolos Verifikasi Berkas',
    ]);
    PendaftarRiwayat::create([
        'pendaftar_id' => $pendaftar->id,
        'status' => 'Tidak Lolos',
    ]);

    writeFakeCertificatePdf($this->tempCertRoot, 'verifikasi_berkas', $pendaftar->nomor_registrasi . '.pdf');

    $this->postJson(route('track'), ['reg_id' => $pendaftar->nomor_registrasi])
        ->assertOk()
        ->assertJsonCount(1, 'data.certificates')
        ->assertJsonPath('data.certificates.0.stage_key', 'verifikasi_berkas');
});

it('finds pdf placed inside a category subfolder and serves signed view and download', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos ke Tahap 3 Besar');

    $pendaftar3 = makePendaftarForTrack([
        'status' => 'Lolos ke Tahap 3 Besar',
        'status_before' => 'Lolos ke Tahap 10 Besar',
    ]);
    writeFakeCertificatePdf(
        $this->tempCertRoot,
        '3_besar/kesehatan',
        strtolower($pendaftar3->nomor_registrasi) . '.pdf',
        '%PDF-3besar-content'
    );

    $trackResp = $this->postJson(route('track'), ['reg_id' => $pendaftar3->nomor_registrasi])
        ->assertOk()
        ->assertJsonCount(1, 'data.certificates');

    $viewUrl = $trackResp->json('data.certificates.0.view_url');
    $downloadUrl = $trackResp->json('data.certificates.0.download_url');

    $viewResp = $this->get($viewUrl)->assertOk();
    expect($viewResp->headers->get('Content-Type'))->toBe('application/pdf')
        ->and($viewResp->headers->get('Content-Disposition'))->toContain('inline');

    $dlResp = $this->get($downloadUrl)->assertOk();
    expect($dlResp->headers->get('Content-Type'))->toBe('application/pdf')
        ->and($dlResp->headers->get('Content-Disposition'))->toContain('attachment')
        ->and($dlResp->headers->get('Content-Disposition'))->toContain('Sertifikat_3_Besar_' . $pendaftar3->nomor_registrasi . '.pdf');
});

it('rejects unsigned or unauthorized stage certificate requests', function (): void {
    Config::set('laravolt.ui.timeline_saat_ini', 'Lolos Verifikasi Berkas');

    $pendaftar = makePendaftarForTrack([
        'status' => 'Lolos Verifikasi Berkas',
        'status_before' => 'Diajukan',
    ]);
    writeFakeCertificatePdf($this->tempCertRoot, '50_besar', $pendaftar->nomor_registrasi . '.pdf');

    // Tanpa signature yang sah -> 403
    $this->get('/track/certificate?reg_id=' . $pendaftar->nomor_registrasi . '&tahap=50_besar')
        ->assertStatus(403);

    // Signature valid tapi peserta tidak berhak atas tahap 50_besar -> 404
    $forgedStageUrl = URL::temporarySignedRoute('track.certificate', now()->addHour(), [
        'reg_id' => $pendaftar->nomor_registrasi,
        'tahap' => '50_besar',
    ], absolute: false);

    $this->get($forgedStageUrl)->assertStatus(404);
});

