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
}


