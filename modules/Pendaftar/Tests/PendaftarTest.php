<?php

namespace Modules\Pendaftar\Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
        $this->assertStringStartsWith('attachment; filename=Data_Pendaftar_Masal_', $response->headers->get('Content-Disposition'));
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
        $tempDir = storage_path('app/private/pendaftar/test_reg');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
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
}
