<?php

declare(strict_types=1);

use App\Enums\Permission as PermissionEnum;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Laravolt\Platform\Models\Permission;
use Laravolt\Platform\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Helper membuat user dengan permission tertentu (tanpa role tambahan jika $permissionNames kosong).
 *
 * @param array<int, string> $permissionNames
 */
function createUserWithPermissions(array $permissionNames = []): User
{
    $user = User::factory()->create();

    if (!empty($permissionNames)) {
        $role = Role::firstOrCreate(['name' => 'Role_' . implode('_', $permissionNames)]);
        $permissionIds = [];
        foreach ($permissionNames as $name) {
            $perm = Permission::firstOrCreate(['name' => $name]);
            $permissionIds[] = $perm->id;
        }
        $role->permissions()->syncWithoutDetaching($permissionIds);
        $user->assignRole($role);
    }

    return $user;
}

/**
 * Helper membaca isi BinaryFileResponse dan menjadwalkan pembersihan file temporary.
 */
function captureBinaryResponseContent(BinaryFileResponse $response, array &$tempFilesToClean): string
{
    $filePath = $response->getFile()->getPathname();
    $tempFilesToClean[] = $filePath;

    ob_start();
    $response->sendContent();

    return (string) ob_get_clean();
}

beforeEach(function (): void {
    $this->rootAbs = storage_path('app/private/pendaftar');
    File::ensureDirectoryExists($this->rootAbs);

    $this->testFolder = 'test_sm_' . bin2hex(random_bytes(6));
    $this->testAbs = $this->rootAbs . DIRECTORY_SEPARATOR . $this->testFolder;
    File::ensureDirectoryExists($this->testAbs);

    $this->tempFilesToClean = [];
});

afterEach(function (): void {
    gc_collect_cycles();

    if (isset($this->testAbs) && is_dir($this->testAbs)) {
        File::deleteDirectory($this->testAbs);
    }

    foreach ($this->tempFilesToClean ?? [] as $path) {
        if (is_string($path) && str_contains(basename($path), 'temp_sm_') && file_exists($path)) {
            @unlink($path);
        }
    }
});

afterAll(function (): void {
    gc_collect_cycles();
    $rootAbs = storage_path('app/private/pendaftar');
    foreach (glob($rootAbs . DIRECTORY_SEPARATOR . 'test_sm_*') ?: [] as $dir) {
        if (is_dir($dir)) {
            File::deleteDirectory($dir);
        }
    }
});

// =========================================================================
// 1. AUTENTIKASI & ACL (STORAGE_MANAGE / *)
// =========================================================================

test('guest tidak dapat mengakses menu dan endpoint storage manager', function (): void {
    $this->get(route('storage-manager.index'))
        ->assertRedirect(route('auth::login.show'));

    $this->post(route('storage-manager.create-folder'), [
        'folder_name' => 'forbidden',
    ])->assertRedirect(route('auth::login.show'));
});

test('user tanpa permission STORAGE_MANAGE atau * mendapat 403 di semua endpoint dan tidak melihat menu sidebar', function (): void {
    $user = createUserWithPermissions([PermissionEnum::KTP_VIEW]);
    $this->actingAs($user);

    File::put($this->testAbs . DIRECTORY_SEPARATOR . 'sample.txt', 'hello');
    $zipPath = $this->testAbs . DIRECTORY_SEPARATOR . 'pack.zip';
    $zip = new ZipArchive();
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('a.txt', 'content');
    $zip->close();

    // Sidebar di dashboard tidak boleh menampilkan menu Manajemen Storage
    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertDontSee('Manajemen Storage');

    $fileRel = $this->testFolder . '/sample.txt';
    $zipRel = $this->testFolder . '/pack.zip';

    $this->get(route('storage-manager.index'))->assertStatus(403);
    $this->get(route('storage-manager.file', ['path' => $fileRel]))->assertStatus(403);
    $this->get(route('storage-manager.download-zip', ['path' => $this->testFolder]))->assertStatus(403);
    $this->get(route('storage-manager.bulk-download', ['items' => [$fileRel]]))->assertStatus(403);
    $this->post(route('storage-manager.create-folder'), [
        'parent_path' => $this->testFolder,
        'folder_name' => 'abc',
    ])->assertStatus(403);
    $this->post(route('storage-manager.upload'), [
        'target_path' => $this->testFolder,
        'files' => [UploadedFile::fake()->create('doc.pdf', 10)],
    ])->assertStatus(403);
    $this->post(route('storage-manager.rename'), [
        'item_path' => $fileRel,
        'new_name' => 'renamed.txt',
    ])->assertStatus(403);
    $this->post(route('storage-manager.move'), [
        'item_path' => $fileRel,
        'destination_path' => '',
    ])->assertStatus(403);
    $this->post(route('storage-manager.extract-zip'), [
        'item_path' => $zipRel,
    ])->assertStatus(403);
    $this->delete(route('storage-manager.destroy'), [
        'item_path' => $fileRel,
    ])->assertStatus(403);
    $this->post(route('storage-manager.bulk-delete'), [
        'items' => [$fileRel],
    ])->assertStatus(403);
});

test('user dengan permission STORAGE_MANAGE dapat mengakses menu sidebar dan halaman storage manager', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::put($this->testAbs . DIRECTORY_SEPARATOR . 'dokumen_uji.pdf', '%PDF-1.4 test');

    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Manajemen Storage');

    $this->get(route('storage-manager.index'))
        ->assertStatus(200)
        ->assertSee('Manajemen Storage Pendaftar')
        ->assertSee($this->testFolder);

    $this->get(route('storage-manager.index', ['path' => $this->testFolder]))
        ->assertStatus(200)
        ->assertSee('dokumen_uji.pdf');
});

test('user dengan permission wildcard * dapat mengakses menu sidebar dan halaman storage manager', function (): void {
    $user = createUserWithPermissions(['*']);
    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Manajemen Storage');

    $this->get(route('storage-manager.index', ['path' => $this->testFolder]))
        ->assertStatus(200)
        ->assertSee('Manajemen Storage Pendaftar');
});

// =========================================================================
// 2. PENCARIAN & SORTING
// =========================================================================

test('pencarian saat ini dan pencarian global berfungsi akurat', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::ensureDirectoryExists($this->testAbs . DIRECTORY_SEPARATOR . 'dalam');
    File::put($this->testAbs . DIRECTORY_SEPARATOR . 'berkas_akar.txt', 'root');
    File::put($this->testAbs . DIRECTORY_SEPARATOR . 'dalam' . DIRECTORY_SEPARATOR . 'rahasia_nested.txt', 'nested');

    // Pencarian scope current hanya menemukan yang ada di folder saat ini
    $this->get(route('storage-manager.index', [
        'path' => $this->testFolder,
        'search' => 'rahasia_nested',
        'scope' => 'current',
    ]))
        ->assertStatus(200)
        ->assertDontSee('dalam/rahasia_nested.txt');

    // Pencarian scope global menemukan file di subfolder
    $this->get(route('storage-manager.index', [
        'path' => $this->testFolder,
        'search' => 'rahasia_nested',
        'scope' => 'global',
    ]))
        ->assertStatus(200)
        ->assertSee('rahasia_nested.txt');
});

// =========================================================================
// 3. MANAJEMEN FOLDER & UPLOAD FILE / FOLDER
// =========================================================================

test('dapat membuat folder tunggal dan subfolder bertingkat serta menolak duplikasi', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    $this->postJson(route('storage-manager.create-folder'), [
        'parent_path' => $this->testFolder,
        'folder_name' => 'bukti_baru/sub_a/sub_b',
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(is_dir($this->testAbs . '/bukti_baru/sub_a/sub_b'))->toBeTrue();

    // Membuat folder yang sama kembali ditolak (422)
    $this->postJson(route('storage-manager.create-folder'), [
        'parent_path' => $this->testFolder,
        'folder_name' => 'bukti_baru',
    ])
        ->assertStatus(422)
        ->assertJson(['success' => false]);
});

test('dapat mengunggah beberapa berkas dan struktur folder serta menangani konflik nama', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    $file1 = UploadedFile::fake()->create('laporan.pdf', 20, 'application/pdf');
    $file2 = UploadedFile::fake()->create('foto.jpg', 15, 'image/jpeg');

    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('storage-manager.upload'), [
            'target_path' => $this->testFolder,
            'files' => [$file1, $file2],
            'relative_paths' => ['laporan.pdf', 'album/liburan/foto.jpg'],
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(is_file($this->testAbs . '/laporan.pdf'))->toBeTrue();
    expect(is_file($this->testAbs . '/album/liburan/foto.jpg'))->toBeTrue();

    // Unggah ulang tanpa overwrite membuat nama unik (misal laporan (1).pdf)
    $duplicateFile = UploadedFile::fake()->create('laporan.pdf', 10, 'application/pdf');
    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('storage-manager.upload'), [
            'target_path' => $this->testFolder,
            'files' => [$duplicateFile],
            'overwrite' => '0',
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(is_file($this->testAbs . '/laporan (1).pdf'))->toBeTrue();
});

// =========================================================================
// 4. PREVIEW, RANGE STREAMING, DOWNLOAD SINGLE, FOLDER ZIP & BULK ZIP
// =========================================================================

test('dapat preview inline, partial range streaming, dan download berkas melalui LargeFileResponse', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    $filePath = $this->testAbs . DIRECTORY_SEPARATOR . 'catatan.txt';
    File::put($filePath, 'ABCDEFGHIJ0123456789');
    $relPath = $this->testFolder . '/catatan.txt';

    // 1. Preview inline
    $inlineResponse = $this->get(route('storage-manager.file', ['path' => $relPath]));
    $inlineResponse->assertStatus(200);
    expect($inlineResponse->headers->get('Content-Disposition'))->toContain('inline');
    expect(captureBinaryResponseContent($inlineResponse->baseResponse, $this->tempFilesToClean))
        ->toBe('ABCDEFGHIJ0123456789');

    // 2. Download sebagai attachment dengan nama kustom
    $downloadResponse = $this->get(route('storage-manager.file', [
        'path' => $relPath,
        'download' => 1,
        'name' => 'Unduhan_Kustom.txt',
    ]));
    $downloadResponse->assertStatus(200);
    expect($downloadResponse->headers->get('Content-Disposition'))
        ->toContain('attachment')
        ->toContain('Unduhan_Kustom.txt');

    // 3. HTTP Byte-Range Request (206 Partial Content)
    $rangeResponse = $this->withHeaders(['Range' => 'bytes=0-4'])
        ->get(route('storage-manager.file', ['path' => $relPath]));
    $rangeResponse->assertStatus(206);
    expect($rangeResponse->headers->get('Content-Range'))->toBe('bytes 0-4/20');
    expect(captureBinaryResponseContent($rangeResponse->baseResponse, $this->tempFilesToClean))
        ->toBe('ABCDE');
});

test('dapat mengunduh folder sebagai arsip ZIP dan bulk download dengan deduplikasi jalur', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::ensureDirectoryExists($this->testAbs . '/sub1');
    File::ensureDirectoryExists($this->testAbs . '/sub2');
    File::ensureDirectoryExists($this->testAbs . '/kosong');
    File::put($this->testAbs . '/sub1/sama.txt', 'dari sub1');
    File::put($this->testAbs . '/sub2/sama.txt', 'dari sub2');

    // 1. Download satu folder sebagai ZIP
    $zipResponse = $this->get(route('storage-manager.download-zip', ['path' => $this->testFolder]));
    $zipResponse->assertStatus(200);
    $downloadedZipPath = $zipResponse->baseResponse->getFile()->getPathname();
    $this->tempFilesToClean[] = $downloadedZipPath;

    $zip = new ZipArchive();
    expect($zip->open($downloadedZipPath))->toBeTrue();
    expect($zip->getFromName('sub1/sama.txt'))->toBe('dari sub1');
    expect($zip->getFromName('sub2/sama.txt'))->toBe('dari sub2');
    $zip->close();
    captureBinaryResponseContent($zipResponse->baseResponse, $this->tempFilesToClean);

    // 2. Bulk download dari dua parent berbeda menjaga path relatif agar tidak menimpa nama yang sama
    $bulkResponse = $this->get(route('storage-manager.bulk-download', [
        'items' => [
            $this->testFolder . '/sub1/sama.txt',
            $this->testFolder . '/sub2/sama.txt',
        ],
    ]));
    $bulkResponse->assertStatus(200);
    $bulkZipPath = $bulkResponse->baseResponse->getFile()->getPathname();
    $this->tempFilesToClean[] = $bulkZipPath;

    $bulkZip = new ZipArchive();
    expect($bulkZip->open($bulkZipPath))->toBeTrue();
    expect($bulkZip->getFromName($this->testFolder . '/sub1/sama.txt'))->toBe('dari sub1');
    expect($bulkZip->getFromName($this->testFolder . '/sub2/sama.txt'))->toBe('dari sub2');
    $bulkZip->close();
    captureBinaryResponseContent($bulkResponse->baseResponse, $this->tempFilesToClean);
});

// =========================================================================
// 5. RENAME, MOVE, EKSTRAK ZIP (ZIPSLIP PROTECTION), HAPUS & BULK HAPUS
// =========================================================================

test('dapat rename, move, dan menolak pemindahan folder ke dalam dirinya sendiri', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::ensureDirectoryExists($this->testAbs . '/sumber/anak');
    File::ensureDirectoryExists($this->testAbs . '/tujuan');
    File::put($this->testAbs . '/lama.txt', 'isi berkas');

    // Rename file
    $this->postJson(route('storage-manager.rename'), [
        'item_path' => $this->testFolder . '/lama.txt',
        'new_name' => 'baru.txt',
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(is_file($this->testAbs . '/baru.txt'))->toBeTrue();
    expect(file_exists($this->testAbs . '/lama.txt'))->toBeFalse();

    // Move file ke folder tujuan
    $this->postJson(route('storage-manager.move'), [
        'item_path' => $this->testFolder . '/baru.txt',
        'destination_path' => $this->testFolder . '/tujuan',
        'current_path' => $this->testFolder,
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(is_file($this->testAbs . '/tujuan/baru.txt'))->toBeTrue();

    // Tidak boleh memindahkan folder sumber ke dalam subfoldernya sendiri
    $this->postJson(route('storage-manager.move'), [
        'item_path' => $this->testFolder . '/sumber',
        'destination_path' => $this->testFolder . '/sumber/anak',
    ])
        ->assertStatus(422)
        ->assertJson(['success' => false]);
});

test('ekstraksi ZIP mengekstrak berkas valid serta memblokir ZipSlip dan ekstensi berbahaya', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    $zipAbs = $this->testAbs . '/paket_uji.zip';
    $zip = new ZipArchive();
    expect($zip->open($zipAbs, ZipArchive::CREATE | ZipArchive::OVERWRITE))->toBeTrue();
    $zip->addFromString('materi/panduan.txt', 'Panduan resmi');
    $zip->addFromString('../kabur_zipslip.txt', 'Tidak boleh keluar');
    $zip->addFromString('backdoor.php', '<?php system($_GET["c"]);');
    $zip->addFromString('.htaccess', 'Allow from all');
    $zip->close();

    $this->postJson(route('storage-manager.extract-zip'), [
        'item_path' => $this->testFolder . '/paket_uji.zip',
        'create_subfolder' => '1',
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    $extractedBase = $this->testAbs . '/paket_uji';
    expect(is_file($extractedBase . '/materi/panduan.txt'))->toBeTrue();
    expect(File::get($extractedBase . '/materi/panduan.txt'))->toBe('Panduan resmi');

    // Berkas berbahaya dan path traversal tidak boleh terekstrak
    expect(file_exists($this->testAbs . '/kabur_zipslip.txt'))->toBeFalse();
    expect(file_exists($this->rootAbs . '/kabur_zipslip.txt'))->toBeFalse();
    expect(file_exists($extractedBase . '/backdoor.php'))->toBeFalse();
    expect(file_exists($extractedBase . '/.htaccess'))->toBeFalse();
});

test('dapat menghapus item tunggal dan beberapa item sekaligus (bulk delete)', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::ensureDirectoryExists($this->testAbs . '/folder_hapus/isi');
    File::put($this->testAbs . '/folder_hapus/isi/a.txt', 'a');
    File::put($this->testAbs . '/hapus1.txt', '1');
    File::put($this->testAbs . '/hapus2.txt', '2');

    // Hapus tunggal (rekursif folder)
    $this->deleteJson(route('storage-manager.destroy'), [
        'item_path' => $this->testFolder . '/folder_hapus',
        'current_path' => $this->testFolder,
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(file_exists($this->testAbs . '/folder_hapus'))->toBeFalse();

    // Bulk delete
    $this->postJson(route('storage-manager.bulk-delete'), [
        'items' => [
            $this->testFolder . '/hapus1.txt',
            $this->testFolder . '/hapus2.txt',
        ],
        'current_path' => $this->testFolder,
    ])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(file_exists($this->testAbs . '/hapus1.txt'))->toBeFalse();
    expect(file_exists($this->testAbs . '/hapus2.txt'))->toBeFalse();
});

// =========================================================================
// 6. KEAMANAN: PATH TRAVERSAL, ROOT PROTECTION & BLOCKED EXTENSIONS
// =========================================================================

test('memblokir path traversal, modifikasi root, serta unggah/rename berkas berbahaya', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::put($this->testAbs . '/aman.txt', 'aman');

    // Path traversal ditolak 403
    $this->get(route('storage-manager.index', ['path' => '../']))->assertStatus(403);
    $this->get(route('storage-manager.file', ['path' => '../../.env']))->assertStatus(403);
    $this->get(route('storage-manager.file', ['path' => $this->testFolder . '/../../.env']))->assertStatus(403);

    // Tidak boleh menghapus atau merename root direktori ('' -> 422 validasi required, '/' -> 403 root protection)
    $this->deleteJson(route('storage-manager.destroy'), [
        'item_path' => '/',
    ])->assertStatus(403);

    $this->deleteJson(route('storage-manager.destroy'), [
        'item_path' => '',
    ])->assertStatus(422);

    $this->postJson(route('storage-manager.rename'), [
        'item_path' => '/',
        'new_name' => 'bukan_root',
    ])->assertStatus(403);

    $this->postJson(route('storage-manager.bulk-delete'), [
        'items' => ['/'],
    ])->assertStatus(403);

    // Upload berkas PHP / executable / dotfile ditolak 422
    $phpFile = UploadedFile::fake()->create('exploit.php', 5);
    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('storage-manager.upload'), [
            'target_path' => $this->testFolder,
            'files' => [$phpFile],
        ])
        ->assertStatus(422);

    expect(file_exists($this->testAbs . '/exploit.php'))->toBeFalse();

    $dotFile = UploadedFile::fake()->create('.env', 1);
    $this->withHeaders(['Accept' => 'application/json'])
        ->post(route('storage-manager.upload'), [
            'target_path' => $this->testFolder,
            'files' => [$dotFile],
        ])
        ->assertStatus(422);

    // Rename menjadi ekstensi berbahaya (.php) ditolak 422
    $this->postJson(route('storage-manager.rename'), [
        'item_path' => $this->testFolder . '/aman.txt',
        'new_name' => 'aman.php',
    ])->assertStatus(422);

    expect(is_file($this->testAbs . '/aman.txt'))->toBeTrue();
    expect(file_exists($this->testAbs . '/aman.php'))->toBeFalse();
});

test('struktur folder pada sidebar mendukung collapse dan expand bertingkat', function (): void {
    $user = createUserWithPermissions([PermissionEnum::STORAGE_MANAGE]);
    $this->actingAs($user);

    File::ensureDirectoryExists($this->testAbs . '/Cabang_A/Daun_1');
    File::ensureDirectoryExists($this->testAbs . '/Cabang_B');

    // Saat berada di root, cabang bersarang tetap ter-render dengan atribut hierarki & fungsi toggle
    $rootResponse = $this->get(route('storage-manager.index'));
    $rootResponse->assertStatus(200)
        ->assertSee('expandAllSmTree()')
        ->assertSee('collapseAllSmTree()')
        ->assertSee('data-node-path="' . $this->testFolder . '"', false)
        ->assertSee('data-has-children="1"', false)
        ->assertSee('data-node-path="' . $this->testFolder . '/Cabang_A/Daun_1"', false)
        ->assertSee('data-parent-path="' . $this->testFolder . '/Cabang_A"', false);

    // Saat membuka Cabang_A/Daun_1, cabang induk Cabang_A ter-expand otomatis
    $branchResponse = $this->get(route('storage-manager.index', [
        'path' => $this->testFolder . '/Cabang_A/Daun_1',
    ]));
    $branchResponse->assertStatus(200);
    $html = $branchResponse->getContent();

    expect($html)->toContain('aria-label="Toggle folder Cabang_A"');
    expect($html)->toContain('data-node-path="' . $this->testFolder . '/Cabang_A/Daun_1"');
});

