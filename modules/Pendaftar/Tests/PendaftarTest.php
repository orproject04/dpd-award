<?php

namespace Modules\Pendaftar\Tests;

use App\Enums\Permission as PermissionEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravolt\Platform\Models\Permission;
use Laravolt\Platform\Models\Role;
use Modules\Pendaftar\Models\Pendaftar;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PendaftarTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @var User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_open_index_page(): void
    {
        $this->get(route('modules::pendaftar.index'))->assertStatus(200);
    }

    #[Test]
    public function it_can_open_create_page(): void
    {
        $this->get(route('modules::pendaftar.create'))->assertStatus(200);
    }

    #[Test]
    public function it_can_store_data(): void
    {
        $attributes = Pendaftar::factory()->raw();

        $this->post(route('modules::pendaftar.store'), $attributes)
            ->assertStatus(302)
            ->assertSessionDoesntHaveErrors();
    }

    #[Test]
    public function it_can_open_show_page(): void
    {
        $pendaftar = Pendaftar::factory()->create();

        $this->get(route('modules::pendaftar.show', $pendaftar))->assertStatus(200);
    }

    #[Test]
    public function it_can_render_show_page_with_evidence_and_bukti_dukung(): void
    {
        $tempDir = storage_path('app/private/pendaftar/test_show_evidence');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $pdfPath = 'pendaftar/test_show_evidence/doc.pdf';
        $fullPath = storage_path('app/private/' . $pdfPath);
        file_put_contents($fullPath, '%PDF-1.4 sample content');

        $pendaftar = Pendaftar::factory()->create([
            'nomor_registrasi' => 'TEST-SHOW-001',
            'ktp' => $pdfPath,
        ]);

        $pendaftar->kontribusi()->create([
            'judul' => 'Kontribusi Inovasi',
            'deskripsi' => 'Deskripsi inovasi',
            'dampak' => 'Dampak inovasi',
            'bukti_dukung' => [$pdfPath],
        ]);

        $pendaftar->penghargaan()->create([
            'uraian' => 'Penghargaan Nasional',
            'tahun' => '2025-01-01',
            'bukti_dukung' => [$pdfPath],
        ]);

        $response = $this->get(route('modules::pendaftar.show', $pendaftar));
        $response->assertStatus(200);
        $response->assertSee('doc.pdf');
        $response->assertSee('Layar Penuh');

        @unlink($fullPath);
        @rmdir($tempDir);
    }

    #[Test]
    public function it_can_open_edit_page(): void
    {
        $pendaftar = Pendaftar::factory()->create();

        $this->get(route('modules::pendaftar.edit', $pendaftar))->assertStatus(200);
    }

    #[Test]
    public function it_can_update_data(): void
    {
        $pendaftar = Pendaftar::factory()->create();
        $attributes = $pendaftar->toArray();
        $attributes['nomor_registrasi'] = 'Updated Nomor Registrasi';
        $attributes['kategori'] = 'Updated Kategori';

        $this->put(route('modules::pendaftar.update', $pendaftar), $attributes)
            ->assertStatus(302)
            ->assertSessionDoesntHaveErrors();
    }

    #[Test]
    public function it_can_delete_data(): void
    {
        $pendaftar = Pendaftar::factory()->create();

        $this->delete(route('modules::pendaftar.destroy', $pendaftar))->assertStatus(302);
    }

    #[Test]
    public function it_can_export_excel(): void
    {
        Pendaftar::factory()->count(3)->create();

        $response = $this->get(route('modules::pendaftar.export'));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('attachment; filename=Data_Pendaftar_DPD_', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function it_redirects_back_if_no_files_to_download(): void
    {
        Pendaftar::factory()->create(['ktp' => '', 'foto' => '']);

        $response = $this->get(route('modules::pendaftar.download-all-zip'));
        $response->assertStatus(302);
    }

    #[Test]
    public function it_can_download_zip_of_files(): void
    {
        $role = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $permission = Permission::firstOrCreate(['name' => '*']);
        $role->permissions()->syncWithoutDetaching([$permission->id]);
        $this->user->assignRole($role);

        $tempDir = storage_path('app/private/pendaftar/test_reg');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $ktpPath = 'pendaftar/test_reg/ktp.jpg';
        file_put_contents(storage_path('app/private/' . $ktpPath), 'fake ktp content');

        Pendaftar::factory()->create([
            'nomor_registrasi' => 'test_reg',
            'ktp' => $ktpPath,
            'foto' => ''
        ]);

        $response = $this->get(route('modules::pendaftar.download-all-zip'));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');

        @unlink(storage_path('app/private/' . $ktpPath));
        @rmdir($tempDir);
    }

    #[Test]
    public function it_can_serve_file_with_double_dots_in_filename(): void
    {
        $tempDir = storage_path('app/private/pendaftar/pendidikan/test_reg/penghargaan');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $filename = '1785212849_2019 -  Juara 3 lomba video Literasi Masyarakat, Kemendikbud..jpg';
        $relativeFilePath = 'pendaftar/pendidikan/test_reg/penghargaan/' . $filename;
        $fullPath = storage_path('app/private/' . $relativeFilePath);

        file_put_contents($fullPath, 'fake image content');

        $response = $this->get(route('modules::pendaftar.file', ['path' => $relativeFilePath]));
        $response->assertStatus(200);

        @unlink($fullPath);
        @rmdir($tempDir);
        @rmdir(dirname($tempDir));
        @rmdir(dirname(dirname($tempDir)));
    }

    #[Test]
    public function it_blocks_directory_traversal_in_serve_file(): void
    {
        $response = $this->get(route('modules::pendaftar.file', ['path' => 'pendaftar/../.env']));
        $response->assertStatus(403);
    }

    #[Test]
    public function it_blocks_ktp_file_access_if_user_lacks_permission(): void
    {
        $tempDir = storage_path('app/private/pendaftar/test_ktp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $ktpPath = 'pendaftar/test_ktp/ktp.jpg';
        file_put_contents(storage_path('app/private/' . $ktpPath), 'fake ktp content');

        Pendaftar::factory()->create([
            'nomor_registrasi' => 'test_ktp',
            'ktp' => $ktpPath,
        ]);

        $response = $this->get(route('modules::pendaftar.file', ['path' => $ktpPath]));
        $response->assertStatus(403);

        @unlink(storage_path('app/private/' . $ktpPath));
        @rmdir($tempDir);
    }

    #[Test]
    public function it_allows_ktp_file_access_if_user_has_permission(): void
    {
        $role = Role::firstOrCreate(['name' => 'KtpViewer']);
        $permission = Permission::firstOrCreate(['name' => PermissionEnum::KTP_VIEW]);
        $role->permissions()->syncWithoutDetaching([$permission->id]);
        $this->user->assignRole($role);

        $tempDir = storage_path('app/private/pendaftar/test_ktp_permitted');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $ktpPath = 'pendaftar/test_ktp_permitted/ktp.jpg';
        file_put_contents(storage_path('app/private/' . $ktpPath), 'fake ktp content');

        Pendaftar::factory()->create([
            'nomor_registrasi' => 'test_ktp_permitted',
            'ktp' => $ktpPath,
        ]);

        $response = $this->get(route('modules::pendaftar.file', ['path' => $ktpPath]));
        $response->assertStatus(200);

        @unlink(storage_path('app/private/' . $ktpPath));
        @rmdir($tempDir);
    }

    #[Test]
    public function it_preserves_query_parameters_in_backlink(): void
    {
        $pendaftar = Pendaftar::factory()->create();

        // 1. Visit index page with query parameters
        $this->get('/pendaftar?kategori=Diajukan&search=budi')->assertStatus(200);

        // 2. Open detail show page
        $response = $this->get(route('modules::pendaftar.show', $pendaftar));
        $response->assertStatus(200);

        // 3. Assert backlink contains the saved query parameters
        $response->assertSee('kategori=Diajukan', false);
        $response->assertSee('search=budi', false);
    }

    #[Test]
    public function it_can_download_template_keterangan(): void
    {
        $response = $this->get(route('modules::pendaftar.template-keterangan'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('attachment; filename=Template_Update_Catatan_Verifikator.xlsx', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function it_can_import_keterangan_excel(): void
    {
        $pendaftar = Pendaftar::factory()->create([
            'nomor_registrasi' => 'REG/TEST/001',
            'status' => 'Diajukan',
        ]);
        $riwayat = $pendaftar->riwayats()->create([
            'status' => 'Diajukan',
            'keterangan' => 'Keterangan lama',
        ]);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nomor Registrasi');
        $sheet->setCellValue('B1', 'Catatan Verifikator');
        $sheet->setCellValue('A2', 'REG/TEST/001');
        $sheet->setCellValue('B2', 'Catatan verifikasi baru dari Excel');

        $tempPath = storage_path('app/private/test_import_keterangan.xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new \Illuminate\Http\UploadedFile(
            $tempPath,
            'test_import_keterangan.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->post(route('modules::pendaftar.import-keterangan'), [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $riwayat->refresh();
        $this->assertEquals('Catatan verifikasi baru dari Excel', $riwayat->keterangan);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    #[Test]
    public function it_serves_file_with_streaming_headers_and_disposition(): void
    {
        $tempDir = storage_path('app/private/pendaftar/test_streaming');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $filePath = 'pendaftar/test_streaming/sample.pdf';
        $fullPath = storage_path('app/private/' . $filePath);
        file_put_contents($fullPath, '%PDF-1.4 test stream content');

        // Test 1: Preview (inline)
        $response = $this->get(route('modules::pendaftar.file', ['path' => $filePath]));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Accept-Ranges', 'bytes');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition') ?? '');

        // Test 2: Download (attachment)
        $dlResponse = $this->get(route('modules::pendaftar.file', ['path' => $filePath, 'download' => 1]));
        $dlResponse->assertStatus(200);
        $this->assertStringContainsString('attachment', $dlResponse->headers->get('Content-Disposition') ?? '');

        // Test 3: Range request (HTTP 206 Partial Content)
        $rangeResponse = $this->get(route('modules::pendaftar.file', ['path' => $filePath]), [
            'Range' => 'bytes=0-4',
        ]);
        $rangeResponse->assertStatus(206);
        $rangeResponse->assertHeader('Content-Range', 'bytes 0-4/' . filesize($fullPath));
        $rangeResponse->assertHeader('Content-Length', '5');

        @unlink($fullPath);
        @rmdir($tempDir);
    }

    #[Test]
    public function it_handles_large_video_range_request(): void
    {
        $videoPath = 'pendaftar/bukti_dukung/DPD-BP26-8305050272/MOTORPUSTAKA/WhatsApp Video 2026-09-09 at 20.20.05.mp4';
        $fullPath = storage_path('app/private/' . $videoPath);
        if (file_exists($fullPath)) {
            $response = $this->get(route('modules::pendaftar.file', ['path' => $videoPath]), [
                'Range' => 'bytes=0-1048575',
            ]);
            $response->assertStatus(206);
            $response->assertHeader('Content-Type', 'video/mp4');
            $response->assertHeader('Content-Range', 'bytes 0-1048575/' . filesize($fullPath));
            $response->assertHeader('Content-Length', '1048576');
            $response->assertHeader('Accept-Ranges', 'bytes');
        }
    }
}


