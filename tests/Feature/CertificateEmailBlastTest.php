<?php

declare(strict_types=1);

use App\Enums\Permission as PermissionEnum;
use App\Mail\SertifikatEmail;
use App\Models\Pendaftar;
use App\Models\PendaftarEmailLog;
use App\Models\User;
use App\Services\SertifikatService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Laravolt\Platform\Models\Permission;
use Laravolt\Platform\Models\Role;

function makeBlastUser(array $permissionNames = ['*']): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'TestRole_' . md5(implode(',', $permissionNames))]);
    $permIds = [];
    foreach ($permissionNames as $name) {
        $perm = Permission::firstOrCreate(['name' => $name]);
        $permIds[] = $perm->id;
    }
    $role->permissions()->syncWithoutDetaching($permIds);
    $user->assignRole($role);

    return $user;
}

function makeBlastPendaftar(array $overrides = []): Pendaftar
{
    static $seq = 100;
    $seq++;

    return Pendaftar::create(array_merge([
        'nama' => 'Peserta Sertifikat ' . $seq,
        'nomor_registrasi' => sprintf('DPD-BK26-880101%04d', $seq),
        'kategori' => 'Bidang Kesehatan',
        'status' => 'Lolos ke Tahap 50 Besar',
        'status_before' => 'Lolos Verifikasi Berkas',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => 'Laki-Laki',
        'email' => "sertifikat{$seq}@example.com",
        'nomor_wa' => '08123456789',
        'alamat' => 'Jl. Pengujian No. 10',
        'pendidikan' => 'S1',
        'ktp' => 'ktp.jpg',
        'foto' => 'foto.jpg',
    ], $overrides));
}

function putBlastCertPdf(string $certRoot, string $folder, string $regId, string $content = '%PDF-1.4 sample'): string
{
    $dir = $certRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
    File::ensureDirectoryExists($dir);
    $path = $dir . DIRECTORY_SEPARATOR . $regId . '.pdf';
    file_put_contents($path, $content);

    return $path;
}

beforeEach(function (): void {
    $this->tempCertRoot = storage_path('app/private/test_cert_blast_' . bin2hex(random_bytes(6)));
    File::ensureDirectoryExists($this->tempCertRoot);
    $this->app->bind(SertifikatService::class, fn () => new SertifikatService($this->tempCertRoot));
});

afterEach(function (): void {
    if (isset($this->tempCertRoot) && is_dir($this->tempCertRoot)) {
        File::deleteDirectory($this->tempCertRoot);
    }
});

it('filters certificate email init accurately from dropdown Status and skips participants without pdf or valid email', function (): void {
    // Timeline publik masih Diajukan, namun admin tetap bisa memproses sertifikat sesuai status DB
    Config::set('laravolt.ui.timeline_saat_ini', 'Diajukan');

    $admin = makeBlastUser(['*']);
    $this->actingAs($admin);

    $p50WithPdf = makeBlastPendaftar(['status' => 'Lolos ke Tahap 50 Besar']);
    $p50NoPdf = makeBlastPendaftar(['status' => 'Lolos ke Tahap 50 Besar']);
    $p50BadEmail = makeBlastPendaftar(['status' => 'Lolos ke Tahap 50 Besar', 'email' => 'bukan-email']);
    $p10WithPdf = makeBlastPendaftar(['status' => 'Lolos ke Tahap 10 Besar', 'status_before' => 'Lolos ke Tahap 50 Besar']);

    putBlastCertPdf($this->tempCertRoot, '50_besar', $p50WithPdf->nomor_registrasi);
    putBlastCertPdf($this->tempCertRoot, '50_besar', $p50BadEmail->nomor_registrasi);
    putBlastCertPdf($this->tempCertRoot, '50_besar', $p10WithPdf->nomor_registrasi);
    putBlastCertPdf($this->tempCertRoot, '10_besar', $p10WithPdf->nomor_registrasi);

    // Filter dropdown Status = Lolos ke Tahap 50 Besar -> hanya $p50WithPdf yang memenuhi
    $this->getJson(route('modules::pendaftar.send-cert-email-init', ['status-filter' => 'Lolos ke Tahap 50 Besar']))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('status_filter_label', 'Lolos ke Tahap 50 Besar')
        ->assertJsonPath('total_with_certificate', 1)
        ->assertJsonPath('pending_count', 1)
        ->assertJsonPath('invalid_email_count', 1);

    // Filter dropdown Status kosong ("Semua Status") -> mengambil $p50WithPdf (50 Besar) dan $p10WithPdf (10 Besar)
    $this->getJson(route('modules::pendaftar.send-cert-email-init'))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('status_filter_label', 'Semua Status')
        ->assertJsonPath('total_with_certificate', 2)
        ->assertJsonPath('pending_count', 2);
});

it('sends certificate pdf emails one-by-one, logs sent status, and skips already sent on next run', function (): void {
    Mail::fake();

    $admin = makeBlastUser(['*']);
    $this->actingAs($admin);

    $p1 = makeBlastPendaftar(['status' => 'Lolos Verifikasi Berkas', 'status_before' => 'Diajukan']);
    $p2 = makeBlastPendaftar(['status' => 'Lolos ke Tahap 3 Besar', 'status_before' => 'Lolos ke Tahap 10 Besar']);

    $pdf1 = putBlastCertPdf($this->tempCertRoot, 'verifikasi_berkas', $p1->nomor_registrasi, '%PDF-berkas-data');
    $pdf2 = putBlastCertPdf($this->tempCertRoot, '3_besar', $p2->nomor_registrasi, '%PDF-3besar-data');

    $init = $this->getJson(route('modules::pendaftar.send-cert-email-init'))
        ->assertOk()
        ->assertJsonPath('total_with_certificate', 2)
        ->assertJsonPath('pending_count', 2);

    $batchId = $init->json('batch_id');

    $this->postJson(route('modules::pendaftar.send-cert-email-process'), [
        'batch_id' => $batchId,
        'mode' => 'pending',
        'limit' => 0,
        'index' => 0,
    ])->assertOk()->assertJsonPath('success', true);

    $this->postJson(route('modules::pendaftar.send-cert-email-process'), [
        'batch_id' => $batchId,
        'mode' => 'pending',
        'limit' => 0,
        'index' => 1,
    ])->assertOk()->assertJsonPath('success', true);

    Mail::assertSent(SertifikatEmail::class, 2);
    Mail::assertSent(SertifikatEmail::class, function (SertifikatEmail $mail) use ($p1, $pdf1): bool {
        $html = $mail->render();

        return $mail->hasTo($p1->email)
            && $mail->pdfPath === realpath($pdf1)
            && $mail->certTitle === 'Sertifikat Peserta'
            && $mail->downloadName === 'Sertifikat_Peserta_' . $p1->nomor_registrasi . '.pdf'
            && str_contains($html, 'Sertifikat Peserta')
            && !str_contains($html, 'Sertifikat Lolos Verifikasi Berkas')
            && count($mail->attachments()) === 1;
    });
    Mail::assertSent(SertifikatEmail::class, function (SertifikatEmail $mail) use ($p2, $pdf2): bool {
        return $mail->hasTo($p2->email)
            && $mail->pdfPath === realpath($pdf2)
            && $mail->stageLabel === 'Tahap 3 Besar';
    });

    expect(PendaftarEmailLog::where('email_type', 'sertifikat')->where('status', 'sent')->count())->toBe(2);

    // Init ulang: harus terbaca sudah terkirim semua (pending = 0)
    $this->getJson(route('modules::pendaftar.send-cert-email-init'))
        ->assertOk()
        ->assertJsonPath('already_sent_count', 2)
        ->assertJsonPath('pending_count', 0);
});

it('logs smtp failures and signals quota_exceeded when gmail daily quota is reached', function (): void {
    $admin = makeBlastUser(['*']);
    $this->actingAs($admin);

    $p1 = makeBlastPendaftar(['status' => 'Lolos ke Tahap 50 Besar']);
    putBlastCertPdf($this->tempCertRoot, '50_besar', $p1->nomor_registrasi);

    $init = $this->getJson(route('modules::pendaftar.send-cert-email-init', ['status-filter' => 'Lolos ke Tahap 50 Besar']))
        ->assertOk();

    Mail::shouldReceive('to')
        ->once()
        ->with($p1->email)
        ->andThrow(new RuntimeException('550 5.4.5 Daily user sending quota exceeded.'));

    $this->postJson(route('modules::pendaftar.send-cert-email-process'), [
        'batch_id' => $init->json('batch_id'),
        'mode' => 'pending',
        'limit' => 0,
        'index' => 0,
    ])
        ->assertOk()
        ->assertJsonPath('success', false)
        ->assertJsonPath('quota_exceeded', true);

    $log = PendaftarEmailLog::where('pendaftar_id', $p1->id)->first();
    expect($log)->not->toBeNull()
        ->and($log->status)->toBe('failed')
        ->and($log->error_message)->toContain('Daily user sending quota exceeded');
});

it('enforces permissions for restricted users and category-scoped admins', function (): void {
    $restricted = makeBlastUser([PermissionEnum::PENILAIAN_VIEW_TERBATAS]);
    $this->actingAs($restricted)
        ->getJson(route('modules::pendaftar.send-cert-email-init'))
        ->assertStatus(403);

    $kesehatanAdmin = makeBlastUser([PermissionEnum::KESEHATAN_MANAGE]);
    $pKes = makeBlastPendaftar(['kategori' => 'Bidang Kesehatan', 'status' => 'Lolos ke Tahap 50 Besar']);
    $pPend = makeBlastPendaftar(['kategori' => 'Bidang Pendidikan', 'status' => 'Lolos ke Tahap 50 Besar']);

    putBlastCertPdf($this->tempCertRoot, '50_besar', $pKes->nomor_registrasi);
    putBlastCertPdf($this->tempCertRoot, '50_besar', $pPend->nomor_registrasi);

    $this->actingAs($kesehatanAdmin)
        ->getJson(route('modules::pendaftar.send-cert-email-init'))
        ->assertOk()
        ->assertJsonPath('total_with_certificate', 1);
});

it('renders send email button, email sertifikat filter dropdown, and dedicated Email Sertifikat column on pendaftar index', function (): void {
    $admin = makeBlastUser(['*']);
    $this->actingAs($admin);

    $pSent = makeBlastPendaftar(['nama' => 'Peserta Terkirim', 'status' => 'Lolos ke Tahap 50 Besar']);
    $pFailed = makeBlastPendaftar(['nama' => 'Peserta Gagal', 'status' => 'Lolos ke Tahap 50 Besar']);
    $pUnsent = makeBlastPendaftar(['nama' => 'Peserta Belum', 'status' => 'Lolos ke Tahap 50 Besar']);

    // Tambah log tahap lama (verifikasi_berkas) pada pUnsent untuk memastikan filter per tahap tetap akurat
    PendaftarEmailLog::create([
        'pendaftar_id' => $pUnsent->id,
        'email_type' => 'sertifikat',
        'stage_key' => 'verifikasi_berkas',
        'stage_label' => 'Peserta',
        'email' => $pUnsent->email,
        'status' => 'sent',
        'sent_by' => $admin->id,
        'sent_at' => now(),
    ]);

    PendaftarEmailLog::create([
        'pendaftar_id' => $pSent->id,
        'email_type' => 'sertifikat',
        'stage_key' => '50_besar',
        'stage_label' => 'Tahap 50 Besar',
        'email' => $pSent->email,
        'status' => 'sent',
        'sent_by' => $admin->id,
        'sent_at' => now(),
    ]);

    PendaftarEmailLog::create([
        'pendaftar_id' => $pFailed->id,
        'email_type' => 'sertifikat',
        'stage_key' => '50_besar',
        'stage_label' => 'Tahap 50 Besar',
        'email' => $pFailed->email,
        'status' => 'failed',
        'error_message' => 'Mailbox unavailable',
        'sent_by' => $admin->id,
    ]);

    $response = $this->get(route('modules::pendaftar.index'))
        ->assertOk()
        ->assertSee('Kirim Email Sertifikat')
        ->assertDontSee('openCertificateEmailLogs')
        ->assertSee('Semua Email Sertifikat')
        ->assertSee('Terkirim')
        ->assertSee('Gagal');

    // Urutan header kolom di <thead>: Email -> Email Sertifikat -> Nomor Registrasi
    $html = $response->getContent();
    preg_match('/<thead[^>]*>(.*?)<\/thead>/is', $html, $theadMatch);
    $thead = $theadMatch[1] ?? '';
    $posEmail = strpos($thead, 'Email');
    $posEmailCert = strpos($thead, 'Email Sertifikat');
    $posReg = strpos($thead, 'Nomor Registrasi');
    expect($thead)->not->toBeEmpty()
        ->and($posEmail)->not->toBeFalse()
        ->and($posEmailCert)->not->toBeFalse()
        ->and($posReg)->not->toBeFalse()
        ->and($posEmailCert)->toBeGreaterThan($posEmail)
        ->and($posReg)->toBeGreaterThan($posEmailCert);

    // Filter dropdown Email Sertifikat + Status = Lolos ke Tahap 50 Besar
    $this->get(route('modules::pendaftar.index', [
        'status-filter' => 'Lolos ke Tahap 50 Besar',
        'email-sertifikat-filter' => 'sent',
    ]))
        ->assertOk()
        ->assertSee('Peserta Terkirim')
        ->assertDontSee('Peserta Gagal')
        ->assertDontSee('Peserta Belum');

    $this->get(route('modules::pendaftar.index', [
        'status-filter' => 'Lolos ke Tahap 50 Besar',
        'email-sertifikat-filter' => 'unsent',
    ]))
        ->assertOk()
        ->assertDontSee('Peserta Terkirim')
        ->assertSee('Peserta Gagal')
        ->assertSee('Peserta Belum');

    $this->get(route('modules::pendaftar.index', [
        'status-filter' => 'Lolos ke Tahap 50 Besar',
        'email-sertifikat-filter' => 'failed',
    ]))
        ->assertOk()
        ->assertDontSee('Peserta Terkirim')
        ->assertSee('Peserta Gagal')
        ->assertDontSee('Peserta Belum');
});

