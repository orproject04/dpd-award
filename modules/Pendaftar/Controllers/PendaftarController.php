<?php

namespace Modules\Pendaftar\Controllers;

use App\Enums\Permission;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Pendaftar\Models\Pendaftar;
use Modules\Pendaftar\PendaftarTableView;
use Modules\Pendaftar\Requests\Store;
use Modules\Pendaftar\Requests\Update;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class PendaftarController extends Controller
{
    public function index()
    {
        return PendaftarTableView::make()->view('pendaftar::index')->showPerPage();
    }

    public function create(): View
    {
        /** @var view-string */
        $view = 'pendaftar::create';

        return view($view);
    }

    public function store(Store $request): RedirectResponse
    {
        Pendaftar::create($request->validated());

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar saved');
    }

    public function show(Pendaftar $pendaftar): View
    {
        /** @var view-string $view */
        $view = 'pendaftar::show';

        return view($view, compact('pendaftar'));
    }

    public function edit(Pendaftar $pendaftar): View
    {
        /** @var view-string $view */
        $view = 'pendaftar::edit';

        return view($view, compact('pendaftar'));
    }

    public function update(Update $request, Pendaftar $pendaftar): RedirectResponse
    {
        $pendaftar->update($request->validated());

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar updated');
    }

    public function destroy(Pendaftar $pendaftar): RedirectResponse
    {
        $pendaftar->delete();

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar deleted');
    }

    public function resendEmail(Pendaftar $pendaftar): RedirectResponse
    {
        try {
            $waktuSubmit = $pendaftar->created_at->format('d M Y, H.i');

            // Cast to App\Models\Pendaftar to avoid TypeError in Mailable
            $appPendaftar = \App\Models\Pendaftar::find($pendaftar->id);

            \Illuminate\Support\Facades\Mail::to($pendaftar->email)
                ->send(new \App\Mail\BuktiDaftarEmail($appPendaftar, $waktuSubmit));

            return back()->withSuccess('Email pendaftaran berhasil dikirim ulang ke ' . $pendaftar->email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal mengirim ulang email pendaftaran ke " . $pendaftar->email . ": " . $e->getMessage());
            return back()->withError('Gagal mengirim email: ' . $e->getMessage());
        }
    }

    public function serveFile(\Illuminate\Http\Request $request)
    {
        $path = $request->query('path');
        if (empty($path)) {
            abort(404);
        }

        // Replace backslashes with forward slashes for uniform checks
        $cleanPath = str_replace('\\', '/', $path);

        // Security check: prevent directory traversal and ensure it stays within pendaftar folder
        $segments = explode('/', $cleanPath);
        if (in_array('..', $segments, true) || in_array('.', $segments, true) || !str_starts_with($cleanPath, 'pendaftar/')) {
            abort(403, 'Unauthorized access.');
        }

        $fullPath = storage_path('app/private/' . $cleanPath);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404, 'File not found.');
        }

        $basePath = realpath(storage_path('app/private/pendaftar'));
        $realPath = realpath($fullPath);

        if ($basePath === false || $realPath === false || !str_starts_with($realPath, $basePath . DIRECTORY_SEPARATOR)) {
            abort(403, 'Unauthorized access.');
        }

        // ACL Check for KTP access
        $isKtpFile = Pendaftar::where('ktp', $cleanPath)
            ->orWhere('ktp', str_replace('/', '\\', $cleanPath))
            ->orWhere('ktp', str_replace('\\', '/', $cleanPath))
            ->exists()
            || str_starts_with(strtolower(basename($cleanPath)), 'ktp.')
            || str_starts_with(strtolower(basename($cleanPath)), 'ktp_');

        if ($isKtpFile) {
            $user = auth()->user();
            if (!$user || !($user->hasPermission('*') || $user->hasPermission(Permission::KTP_VIEW))) {
                abort(403, 'Anda tidak memiliki akses untuk melihat/mengunduh KTP.');
            }
        }

        if ($request->has('download')) {
            return response()->download($fullPath);
        }

        return response()->file($fullPath);
    }

    public function downloadAllFiles(Pendaftar $pendaftar)
    {
        $user = auth()->user();
        $hasKtpPermission = $user && ($user->hasPermission('*') || $user->hasPermission(Permission::KTP_VIEW));

        $files = [];

        // Add ktp if user has permission
        if ($hasKtpPermission) {
            $ktp = $pendaftar->getRawOriginal('ktp');
            if (!empty($ktp)) {
                $files['KTP_' . basename($ktp)] = storage_path('app/private/' . str_replace('\\', '/', $ktp));
            }
        }

        // Add foto
        $foto = $pendaftar->getRawOriginal('foto');
        if (!empty($foto)) {
            $files['Foto_' . basename($foto)] = storage_path('app/private/' . str_replace('\\', '/', $foto));
        }

        // Add kontribusi bukti_dukung (now a JSON array of file paths)
        foreach ($pendaftar->kontribusi as $index => $kontribusi) {
            $buktis = $kontribusi->bukti_dukung; // already decoded array via cast
            if (!empty($buktis) && is_array($buktis)) {
                foreach ($buktis as $fileIdx => $bukti) {
                    if (!empty($bukti)) {
                        $localName = 'Kontribusi_' . ($index + 1) . '_' . ($fileIdx + 1) . '_' . basename($bukti);
                        $files[$localName] = storage_path('app/private/' . str_replace('\\', '/', $bukti));
                    }
                }
            }
        }

        // Add penghargaan bukti_dukung (now a JSON array of file paths)
        foreach ($pendaftar->penghargaan as $index => $penghargaan) {
            $buktis = $penghargaan->bukti_dukung; // already decoded array via cast
            if (!empty($buktis) && is_array($buktis)) {
                foreach ($buktis as $fileIdx => $bukti) {
                    if (!empty($bukti)) {
                        $localName = 'Penghargaan_' . ($index + 1) . '_' . ($fileIdx + 1) . '_' . basename($bukti);
                        $files[$localName] = storage_path('app/private/' . str_replace('\\', '/', $bukti));
                    }
                }
            }
        }

        // Filter files that exist on disk
        $existingFiles = array_filter($files, function ($path) {
            return !empty($path) && file_exists($path) && is_file($path);
        });

        if (empty($existingFiles)) {
            return back()->withError('Tidak ada berkas yang dapat diunduh.');
        }

        $zipFileName = 'Berkas_Pendaftar_' . str_replace(' ', '_', $pendaftar->nama) . '_' . $pendaftar->nomor_registrasi . '.zip';
        $zipPath = storage_path('app/private/temp_' . \Illuminate\Support\Str::random(16) . '.zip');

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas ZIP.');
        }

        foreach ($existingFiles as $localName => $absolutePath) {
            $zip->addFile($absolutePath, $localName);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function updateStatus(Pendaftar $pendaftar, \Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        $newStatus = $validated['status'];
        $keterangan = $validated['keterangan'];

        if ($newStatus === $pendaftar->status) {
            // Hanya perbarui keterangan untuk status yang sama
            $latestRiwayat = $pendaftar->riwayats()->where('status', $newStatus)->latest()->first();
            if ($latestRiwayat) {
                $latestRiwayat->update(['keterangan' => $keterangan]);
            } else {
                $pendaftar->riwayats()->create([
                    'status' => $newStatus,
                    'keterangan' => $keterangan,
                ]);
            }
        } else {
            // Status berubah, simpan status sebelumnya dan buat riwayat baru
            $pendaftar->status_before = $pendaftar->status;
            $pendaftar->status = $newStatus;
            $pendaftar->save();

            $pendaftar->riwayats()->create([
                'status' => $newStatus,
                'keterangan' => $keterangan,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pendaftar berhasil diperbarui.',
                'status' => $pendaftar->status
            ]);
        }

        return back()->withSuccess('Status pendaftar berhasil diperbarui.');
    }

    public function updateFoto(Pendaftar $pendaftar, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:5120'
        ], [
            'foto.required' => 'Pilih file foto terlebih dahulu.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran file foto maksimal 5MB.'
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('pendaftar/' . $pendaftar->id, 'local');
            $pendaftar->update(['foto' => $path]);
            return back()->withSuccess('Foto pendaftar berhasil diperbarui.');
        }

        return back()->withError('Gagal mengunggah foto.');
    }

    public function updateKtp(Pendaftar $pendaftar, \Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user || !($user->hasPermission('*') || $user->hasPermission(Permission::KTP_VIEW))) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui KTP.');
        }

        $request->validate([
            'ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ], [
            'ktp.required' => 'Pilih file KTP terlebih dahulu.',
            'ktp.mimes' => 'File KTP harus berupa JPG, PNG, atau PDF.',
            'ktp.max' => 'Ukuran file KTP maksimal 5MB.'
        ]);

        if ($request->hasFile('ktp')) {
            $path = $request->file('ktp')->store('pendaftar/' . $pendaftar->id, 'local');
            $pendaftar->update(['ktp' => $path]);
            return back()->withSuccess('KTP pendaftar berhasil diperbarui.');
        }

        return back()->withError('Gagal mengunggah KTP.');
    }

    public function updateProvinsi(Pendaftar $pendaftar, \Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'provinsi' => 'nullable|string|max:255',
        ]);

        $pendaftar->update(['provinsi' => $validated['provinsi']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Provinsi pendaftar berhasil diperbarui.',
                'provinsi_with_wilayah' => $pendaftar->provinsi_with_wilayah
            ]);
        }

        return back()->withSuccess('Provinsi pendaftar berhasil diperbarui.');
    }

    protected function getFilteredQuery(\Illuminate\Http\Request $request)
    {
        $query = Pendaftar::query();

        $filters = [
            new \App\Http\Filters\KategoriFilter,
            new \App\Http\Filters\ProvinsiFilter,
            new \App\Http\Filters\StatusFilter,
        ];

        foreach ($filters as $filter) {
            $key = $filter->key();
            $value = $request->get($key);
            $query = $filter->apply($query, $value);
        }

        return $query
            ->autoSort()
            ->latest('created_at')
            ->autoSearch($request->get('search'));
    }

    public function exportExcel(\Illuminate\Http\Request $request)
    {
        $pendaftars = $this->getFilteredQuery($request)->with(['kontribusi', 'penghargaan', 'riwayats'])->get();

        $spreadsheet = new Spreadsheet();

        // ----------------------------------------------------
        // SHEET 1: PENDAFTAR
        // ----------------------------------------------------
        $sheetPendaftar = $spreadsheet->getActiveSheet();
        $sheetPendaftar->setTitle('Pendaftar');

        // Headers
        $headersPendaftar = [
            'No',
            'Nomor Registrasi',
            'Kategori',
            'Provinsi',
            'Sub Wilayah',
            'Nama Lengkap',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Pendidikan',
            'Alamat',
            'Nomor WA',
            'Email',
            'Status',
            'Keterangan',
            'Tanggal Registrasi'
        ];

        // Style helper
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'], // Soft blue
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataBorderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
        ];

        // Write headers for Pendaftar
        foreach ($headersPendaftar as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetPendaftar->setCellValue($colLetter . '1', $header);
        }
        $sheetPendaftar->getStyle('A1:P1')->applyFromArray($headerStyle);
        $sheetPendaftar->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        foreach ($pendaftars as $index => $pendaftar) {
            $sheetPendaftar->setCellValue('A' . $rowIdx, $index + 1);
            $sheetPendaftar->setCellValue('B' . $rowIdx, $pendaftar->nomor_registrasi);
            $sheetPendaftar->setCellValue('C' . $rowIdx, $pendaftar->kategori);
            $sheetPendaftar->setCellValue('D' . $rowIdx, $pendaftar->provinsi ?: '-');
            $sheetPendaftar->setCellValue('E' . $rowIdx, $pendaftar->wilayah ?: '-');
            $sheetPendaftar->setCellValue('F' . $rowIdx, $pendaftar->nama);
            $sheetPendaftar->setCellValue('G' . $rowIdx, $pendaftar->tempat_lahir);
            $sheetPendaftar->setCellValue('H' . $rowIdx, $pendaftar->tanggal_lahir);
            $sheetPendaftar->setCellValue('I' . $rowIdx, $pendaftar->jenis_kelamin);
            $sheetPendaftar->setCellValue('J' . $rowIdx, $pendaftar->pendidikan);
            $sheetPendaftar->setCellValue('K' . $rowIdx, $pendaftar->alamat);
            $sheetPendaftar->setCellValue('L' . $rowIdx, $pendaftar->nomor_wa);
            $sheetPendaftar->setCellValue('M' . $rowIdx, $pendaftar->email);
            $sheetPendaftar->setCellValue('N' . $rowIdx, $pendaftar->status ?? 'Diajukan');
            
            $keterangan = $pendaftar->riwayats->first()?->keterangan ?? '';
            $sheetPendaftar->setCellValue('O' . $rowIdx, $keterangan);
            $sheetPendaftar->setCellValue('P' . $rowIdx, $pendaftar->created_at->format('Y-m-d H:i:s'));

            // Apply light borders
            $sheetPendaftar->getStyle('A' . $rowIdx . ':P' . $rowIdx)->applyFromArray($dataBorderStyle);
            $rowIdx++;
        }

        // Auto-fit columns
        foreach (range(1, 16) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetPendaftar->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 2: KONTRIBUSI
        // ----------------------------------------------------
        $sheetKontribusi = $spreadsheet->createSheet();
        $sheetKontribusi->setTitle('Kontribusi');

        $headersKontribusi = [
            'No',
            'Nomor Registrasi Pendaftar',
            'Nama Pendaftar',
            'Judul Kontribusi',
            'Deskripsi',
            'Dampak',
            'Bukti Dukung'
        ];

        // Write headers for Kontribusi
        foreach ($headersKontribusi as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetKontribusi->setCellValue($colLetter . '1', $header);
        }
        $sheetKontribusi->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetKontribusi->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('E2EFDA'); // Soft green for Kontribusi
        $sheetKontribusi->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $noKontribusi = 1;
        foreach ($pendaftars as $pendaftar) {
            foreach ($pendaftar->kontribusi as $kontribusi) {
                $sheetKontribusi->setCellValue('A' . $rowIdx, $noKontribusi++);
                $sheetKontribusi->setCellValue('B' . $rowIdx, $pendaftar->nomor_registrasi);
                $sheetKontribusi->setCellValue('C' . $rowIdx, $pendaftar->nama);
                $sheetKontribusi->setCellValue('D' . $rowIdx, $kontribusi->judul);
                $sheetKontribusi->setCellValue('E' . $rowIdx, $kontribusi->deskripsi);
                $sheetKontribusi->setCellValue('F' . $rowIdx, $kontribusi->dampak);

                // Bukti dukung can be array or string
                $buktiText = '';
                $buktis = $kontribusi->bukti_dukung;
                if (is_array($buktis)) {
                    $urls = [];
                    foreach ($buktis as $bukti) {
                        if (!empty($bukti)) {
                            $urls[] = route('modules::pendaftar.file', ['path' => $bukti]);
                        }
                    }
                    $buktiText = implode("\n", $urls);
                } else if (!empty($buktis)) {
                    $buktiText = route('modules::pendaftar.file', ['path' => $buktis]);
                }
                $sheetKontribusi->setCellValue('G' . $rowIdx, $buktiText);
                $sheetKontribusi->getStyle('G' . $rowIdx)->getAlignment()->setWrapText(true);

                $sheetKontribusi->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);
                $rowIdx++;
            }
        }

        // Auto-fit columns for Kontribusi
        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetKontribusi->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 3: PENGHARGAAN
        // ----------------------------------------------------
        $sheetPenghargaan = $spreadsheet->createSheet();
        $sheetPenghargaan->setTitle('Penghargaan');

        $headersPenghargaan = [
            'No',
            'Nomor Registrasi Pendaftar',
            'Nama Pendaftar',
            'Uraian Penghargaan',
            'Tahun',
            'Bukti Dukung'
        ];

        // Write headers for Penghargaan
        foreach ($headersPenghargaan as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetPenghargaan->setCellValue($colLetter . '1', $header);
        }
        $sheetPenghargaan->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheetPenghargaan->getStyle('A1:F1')->getFill()->getStartColor()->setRGB('FFF2CC'); // Soft yellow for Penghargaan
        $sheetPenghargaan->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $noPenghargaan = 1;
        foreach ($pendaftars as $pendaftar) {
            foreach ($pendaftar->penghargaan as $penghargaan) {
                $sheetPenghargaan->setCellValue('A' . $rowIdx, $noPenghargaan++);
                $sheetPenghargaan->setCellValue('B' . $rowIdx, $pendaftar->nomor_registrasi);
                $sheetPenghargaan->setCellValue('C' . $rowIdx, $pendaftar->nama);
                $sheetPenghargaan->setCellValue('D' . $rowIdx, $penghargaan->uraian);
                $sheetPenghargaan->setCellValue('E' . $rowIdx, $penghargaan->tahun);

                $buktiText = '';
                $buktis = $penghargaan->bukti_dukung;
                if (is_array($buktis)) {
                    $urls = [];
                    foreach ($buktis as $bukti) {
                        if (!empty($bukti)) {
                            $urls[] = route('modules::pendaftar.file', ['path' => $bukti]);
                        }
                    }
                    $buktiText = implode("\n", $urls);
                } else if (!empty($buktis)) {
                    $buktiText = route('modules::pendaftar.file', ['path' => $buktis]);
                }
                $sheetPenghargaan->setCellValue('F' . $rowIdx, $buktiText);
                $sheetPenghargaan->getStyle('F' . $rowIdx)->getAlignment()->setWrapText(true);

                $sheetPenghargaan->getStyle('A' . $rowIdx . ':F' . $rowIdx)->applyFromArray($dataBorderStyle);
                $rowIdx++;
            }
        }

        // Auto-fit columns for Penghargaan
        foreach (range(1, 6) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetPenghargaan->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Set Sheet 1 as active at opening
        $spreadsheet->setActiveSheetIndex(0);

        // ----------------------------------------------------
        // AGGREGATE DATA FOR REKAP
        // ----------------------------------------------------
        $rekapProvinsi = [];
        $rekapWilayah = [];
        $rekapPendidikan = [];
        $rekapGender = [];
        $rekapUsia = [
            '< 25' => [],
            '25–34' => [],
            '35–44' => [],
            '45–54' => [],
            '55+' => []
        ];

        $kategories = [
            'Bidang Pendidikan',
            'Bidang Kesehatan',
            'Bidang Ketahanan Pangan',
            'Bidang Seni dan Budaya'
        ];

        foreach ($rekapUsia as $k => $v) {
            $rekapUsia[$k] = array_fill_keys($kategories, 0);
            $rekapUsia[$k]['Total'] = 0;
        }

        foreach ($pendaftars as $p) {
            $prov = (empty($p->provinsi) || $p->provinsi == '-') ? 'Tidak Diketahui' : $p->provinsi;
            $wil = (empty($p->wilayah) || $p->wilayah == '-') ? 'Tidak Diketahui' : $p->wilayah;
            $edu = (empty($p->pendidikan) || $p->pendidikan == '-') ? 'Tidak Diketahui' : $p->pendidikan;
            $gen = (empty($p->jenis_kelamin) || $p->jenis_kelamin == '-') ? 'Tidak Diketahui' : $p->jenis_kelamin;
            $kat = $p->kategori;

            if (!isset($rekapProvinsi[$prov])) {
                $rekapProvinsi[$prov] = array_fill_keys($kategories, 0);
                $rekapProvinsi[$prov]['Total'] = 0;
            }
            if (!isset($rekapWilayah[$wil])) {
                $rekapWilayah[$wil] = array_fill_keys($kategories, 0);
                $rekapWilayah[$wil]['Total'] = 0;
            }
            if (!isset($rekapPendidikan[$edu])) {
                $rekapPendidikan[$edu] = array_fill_keys($kategories, 0);
                $rekapPendidikan[$edu]['Total'] = 0;
            }
            if (!isset($rekapGender[$gen])) {
                $rekapGender[$gen] = array_fill_keys($kategories, 0);
                $rekapGender[$gen]['Total'] = 0;
            }

            // Usia grouping
            $ageGrp = 'Tidak Diketahui';
            if (!empty($p->tanggal_lahir)) {
                try {
                    $age = \Carbon\Carbon::parse($p->tanggal_lahir)->age;
                    if ($age < 25) $ageGrp = '< 25';
                    elseif ($age < 35) $ageGrp = '25–34';
                    elseif ($age < 45) $ageGrp = '35–44';
                    elseif ($age < 55) $ageGrp = '45–54';
                    else $ageGrp = '55+';
                } catch (\Exception $e) {
                    $ageGrp = 'Tidak Diketahui';
                }
            }

            if ($ageGrp === 'Tidak Diketahui' && !isset($rekapUsia['Tidak Diketahui'])) {
                $rekapUsia['Tidak Diketahui'] = array_fill_keys($kategories, 0);
                $rekapUsia['Tidak Diketahui']['Total'] = 0;
            }

            if (in_array($kat, $kategories)) {
                $rekapProvinsi[$prov][$kat]++;
                $rekapWilayah[$wil][$kat]++;
                $rekapPendidikan[$edu][$kat]++;
                $rekapGender[$gen][$kat]++;
                $rekapUsia[$ageGrp][$kat]++;
            }

            $rekapProvinsi[$prov]['Total']++;
            $rekapWilayah[$wil]['Total']++;
            $rekapPendidikan[$edu]['Total']++;
            $rekapGender[$gen]['Total']++;
            $rekapUsia[$ageGrp]['Total']++;
        }

        $eduOrder = ['SD/Sederajat','SMP/Sederajat','SMA/Sederajat','Diploma I','Diploma II','Diploma III','Diploma IV','Sarjana (S1)','Magister (S2)','Doktor (S3)'];
        uksort($rekapPendidikan, function($a, $b) use ($eduOrder) {
            $posA = array_search($a, $eduOrder);
            $posB = array_search($b, $eduOrder);
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            if ($posA == $posB) return $a <=> $b;
            return $posA <=> $posB;
        });

        $provOrder = [];
        foreach (\App\Models\Pendaftar::getProvinsiMap() as $wil => $provs) {
            $provOrder = array_merge($provOrder, $provs);
        }
        uksort($rekapProvinsi, function($a, $b) use ($provOrder) {
            $posA = array_search($a, $provOrder);
            $posB = array_search($b, $provOrder);
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            if ($posA == $posB) return $a <=> $b;
            return $posA <=> $posB;
        });

        ksort($rekapWilayah);
        ksort($rekapGender);

        // ----------------------------------------------------
        // SHEET 4: REKAP PROVINSI
        // ----------------------------------------------------
        $sheetRekapProv = $spreadsheet->createSheet();
        $sheetRekapProv->setTitle('Rekap Provinsi');

        $headersProv = ['No', 'Provinsi', 'Bidang Pendidikan', 'Bidang Kesehatan', 'Bidang Ketahanan Pangan', 'Bidang Seni dan Budaya', 'Total'];
        foreach ($headersProv as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetRekapProv->setCellValue($colLetter . '1', $header);
        }
        $sheetRekapProv->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetRekapProv->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('FFF2CC'); // Soft yellow
        $sheetRekapProv->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $no = 1;
        $grandTotalsProv = array_fill_keys($kategories, 0);
        $grandTotalsProv['Total'] = 0;

        foreach ($rekapProvinsi as $prov => $data) {
            $sheetRekapProv->setCellValue('A' . $rowIdx, $no++);
            $sheetRekapProv->setCellValue('B' . $rowIdx, $prov);
            $sheetRekapProv->setCellValue('C' . $rowIdx, $data['Bidang Pendidikan']);
            $sheetRekapProv->setCellValue('D' . $rowIdx, $data['Bidang Kesehatan']);
            $sheetRekapProv->setCellValue('E' . $rowIdx, $data['Bidang Ketahanan Pangan']);
            $sheetRekapProv->setCellValue('F' . $rowIdx, $data['Bidang Seni dan Budaya']);
            $sheetRekapProv->setCellValue('G' . $rowIdx, $data['Total']);

            $sheetRekapProv->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);

            foreach ($kategories as $k)
                $grandTotalsProv[$k] += $data[$k];
            $grandTotalsProv['Total'] += $data['Total'];
            $rowIdx++;
        }

        // Add Grand Total Row
        $sheetRekapProv->setCellValue('A' . $rowIdx, '');
        $sheetRekapProv->setCellValue('B' . $rowIdx, 'TOTAL KESELURUHAN');
        $sheetRekapProv->setCellValue('C' . $rowIdx, $grandTotalsProv['Bidang Pendidikan']);
        $sheetRekapProv->setCellValue('D' . $rowIdx, $grandTotalsProv['Bidang Kesehatan']);
        $sheetRekapProv->setCellValue('E' . $rowIdx, $grandTotalsProv['Bidang Ketahanan Pangan']);
        $sheetRekapProv->setCellValue('F' . $rowIdx, $grandTotalsProv['Bidang Seni dan Budaya']);
        $sheetRekapProv->setCellValue('G' . $rowIdx, $grandTotalsProv['Total']);
        $sheetRekapProv->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($headerStyle);
        $sheetRekapProv->getStyle('A' . $rowIdx . ':G' . $rowIdx)->getFill()->getStartColor()->setRGB('D9D9D9');

        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetRekapProv->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 5: REKAP SUB WILAYAH
        // ----------------------------------------------------
        $sheetRekapWil = $spreadsheet->createSheet();
        $sheetRekapWil->setTitle('Rekap Sub Wilayah');

        $headersWil = ['No', 'Sub Wilayah', 'Bidang Pendidikan', 'Bidang Kesehatan', 'Bidang Ketahanan Pangan', 'Bidang Seni dan Budaya', 'Total'];
        foreach ($headersWil as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetRekapWil->setCellValue($colLetter . '1', $header);
        }
        $sheetRekapWil->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetRekapWil->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('FCE4D6'); // Soft orange
        $sheetRekapWil->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $no = 1;
        $grandTotalsWil = array_fill_keys($kategories, 0);
        $grandTotalsWil['Total'] = 0;

        foreach ($rekapWilayah as $wil => $data) {
            $sheetRekapWil->setCellValue('A' . $rowIdx, $no++);
            $sheetRekapWil->setCellValue('B' . $rowIdx, $wil);
            $sheetRekapWil->setCellValue('C' . $rowIdx, $data['Bidang Pendidikan']);
            $sheetRekapWil->setCellValue('D' . $rowIdx, $data['Bidang Kesehatan']);
            $sheetRekapWil->setCellValue('E' . $rowIdx, $data['Bidang Ketahanan Pangan']);
            $sheetRekapWil->setCellValue('F' . $rowIdx, $data['Bidang Seni dan Budaya']);
            $sheetRekapWil->setCellValue('G' . $rowIdx, $data['Total']);

            $sheetRekapWil->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);

            foreach ($kategories as $k)
                $grandTotalsWil[$k] += $data[$k];
            $grandTotalsWil['Total'] += $data['Total'];
            $rowIdx++;
        }

        // Add Grand Total Row
        $sheetRekapWil->setCellValue('A' . $rowIdx, '');
        $sheetRekapWil->setCellValue('B' . $rowIdx, 'TOTAL KESELURUHAN');
        $sheetRekapWil->setCellValue('C' . $rowIdx, $grandTotalsWil['Bidang Pendidikan']);
        $sheetRekapWil->setCellValue('D' . $rowIdx, $grandTotalsWil['Bidang Kesehatan']);
        $sheetRekapWil->setCellValue('E' . $rowIdx, $grandTotalsWil['Bidang Ketahanan Pangan']);
        $sheetRekapWil->setCellValue('F' . $rowIdx, $grandTotalsWil['Bidang Seni dan Budaya']);
        $sheetRekapWil->setCellValue('G' . $rowIdx, $grandTotalsWil['Total']);
        $sheetRekapWil->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($headerStyle);
        $sheetRekapWil->getStyle('A' . $rowIdx . ':G' . $rowIdx)->getFill()->getStartColor()->setRGB('D9D9D9');

        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetRekapWil->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 6: REKAP PENDIDIKAN
        // ----------------------------------------------------
        $sheetRekapPendidikan = $spreadsheet->createSheet();
        $sheetRekapPendidikan->setTitle('Rekap Pendidikan');

        $headersEdu = ['No', 'Tingkat Pendidikan', 'Bidang Pendidikan', 'Bidang Kesehatan', 'Bidang Ketahanan Pangan', 'Bidang Seni dan Budaya', 'Total'];
        foreach ($headersEdu as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetRekapPendidikan->setCellValue($colLetter . '1', $header);
        }
        $sheetRekapPendidikan->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetRekapPendidikan->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('DDEBF7'); // Soft blue
        $sheetRekapPendidikan->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $no = 1;
        $grandTotalsEdu = array_fill_keys($kategories, 0);
        $grandTotalsEdu['Total'] = 0;

        foreach ($rekapPendidikan as $edu => $data) {
            $sheetRekapPendidikan->setCellValue('A' . $rowIdx, $no++);
            $sheetRekapPendidikan->setCellValue('B' . $rowIdx, $edu);
            $sheetRekapPendidikan->setCellValue('C' . $rowIdx, $data['Bidang Pendidikan']);
            $sheetRekapPendidikan->setCellValue('D' . $rowIdx, $data['Bidang Kesehatan']);
            $sheetRekapPendidikan->setCellValue('E' . $rowIdx, $data['Bidang Ketahanan Pangan']);
            $sheetRekapPendidikan->setCellValue('F' . $rowIdx, $data['Bidang Seni dan Budaya']);
            $sheetRekapPendidikan->setCellValue('G' . $rowIdx, $data['Total']);

            $sheetRekapPendidikan->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);

            foreach ($kategories as $k) $grandTotalsEdu[$k] += $data[$k];
            $grandTotalsEdu['Total'] += $data['Total'];
            $rowIdx++;
        }

        // Add Grand Total Row
        $sheetRekapPendidikan->setCellValue('A' . $rowIdx, '');
        $sheetRekapPendidikan->setCellValue('B' . $rowIdx, 'TOTAL KESELURUHAN');
        $sheetRekapPendidikan->setCellValue('C' . $rowIdx, $grandTotalsEdu['Bidang Pendidikan']);
        $sheetRekapPendidikan->setCellValue('D' . $rowIdx, $grandTotalsEdu['Bidang Kesehatan']);
        $sheetRekapPendidikan->setCellValue('E' . $rowIdx, $grandTotalsEdu['Bidang Ketahanan Pangan']);
        $sheetRekapPendidikan->setCellValue('F' . $rowIdx, $grandTotalsEdu['Bidang Seni dan Budaya']);
        $sheetRekapPendidikan->setCellValue('G' . $rowIdx, $grandTotalsEdu['Total']);
        $sheetRekapPendidikan->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($headerStyle);
        $sheetRekapPendidikan->getStyle('A' . $rowIdx . ':G' . $rowIdx)->getFill()->getStartColor()->setRGB('D9D9D9');

        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetRekapPendidikan->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 7: REKAP JENIS KELAMIN
        // ----------------------------------------------------
        $sheetRekapGender = $spreadsheet->createSheet();
        $sheetRekapGender->setTitle('Rekap Jenis Kelamin');

        $headersGender = ['No', 'Jenis Kelamin', 'Bidang Pendidikan', 'Bidang Kesehatan', 'Bidang Ketahanan Pangan', 'Bidang Seni dan Budaya', 'Total'];
        foreach ($headersGender as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetRekapGender->setCellValue($colLetter . '1', $header);
        }
        $sheetRekapGender->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetRekapGender->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('E2EFDA'); // Soft green
        $sheetRekapGender->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $no = 1;
        $grandTotalsGender = array_fill_keys($kategories, 0);
        $grandTotalsGender['Total'] = 0;

        foreach ($rekapGender as $gen => $data) {
            $sheetRekapGender->setCellValue('A' . $rowIdx, $no++);
            $sheetRekapGender->setCellValue('B' . $rowIdx, $gen);
            $sheetRekapGender->setCellValue('C' . $rowIdx, $data['Bidang Pendidikan']);
            $sheetRekapGender->setCellValue('D' . $rowIdx, $data['Bidang Kesehatan']);
            $sheetRekapGender->setCellValue('E' . $rowIdx, $data['Bidang Ketahanan Pangan']);
            $sheetRekapGender->setCellValue('F' . $rowIdx, $data['Bidang Seni dan Budaya']);
            $sheetRekapGender->setCellValue('G' . $rowIdx, $data['Total']);

            $sheetRekapGender->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);

            foreach ($kategories as $k) $grandTotalsGender[$k] += $data[$k];
            $grandTotalsGender['Total'] += $data['Total'];
            $rowIdx++;
        }

        // Add Grand Total Row
        $sheetRekapGender->setCellValue('A' . $rowIdx, '');
        $sheetRekapGender->setCellValue('B' . $rowIdx, 'TOTAL KESELURUHAN');
        $sheetRekapGender->setCellValue('C' . $rowIdx, $grandTotalsGender['Bidang Pendidikan']);
        $sheetRekapGender->setCellValue('D' . $rowIdx, $grandTotalsGender['Bidang Kesehatan']);
        $sheetRekapGender->setCellValue('E' . $rowIdx, $grandTotalsGender['Bidang Ketahanan Pangan']);
        $sheetRekapGender->setCellValue('F' . $rowIdx, $grandTotalsGender['Bidang Seni dan Budaya']);
        $sheetRekapGender->setCellValue('G' . $rowIdx, $grandTotalsGender['Total']);
        $sheetRekapGender->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($headerStyle);
        $sheetRekapGender->getStyle('A' . $rowIdx . ':G' . $rowIdx)->getFill()->getStartColor()->setRGB('D9D9D9');

        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetRekapGender->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 8: REKAP USIA
        // ----------------------------------------------------
        $sheetRekapUsia = $spreadsheet->createSheet();
        $sheetRekapUsia->setTitle('Rekap Usia');

        $headersUsia = ['No', 'Kelompok Usia', 'Bidang Pendidikan', 'Bidang Kesehatan', 'Bidang Ketahanan Pangan', 'Bidang Seni dan Budaya', 'Total'];
        foreach ($headersUsia as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheetRekapUsia->setCellValue($colLetter . '1', $header);
        }
        $sheetRekapUsia->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheetRekapUsia->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('FFF2CC'); // Soft yellow
        $sheetRekapUsia->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        $no = 1;
        $grandTotalsUsia = array_fill_keys($kategories, 0);
        $grandTotalsUsia['Total'] = 0;

        foreach ($rekapUsia as $age => $data) {
            $sheetRekapUsia->setCellValue('A' . $rowIdx, $no++);
            $sheetRekapUsia->setCellValue('B' . $rowIdx, $age);
            $sheetRekapUsia->setCellValue('C' . $rowIdx, $data['Bidang Pendidikan']);
            $sheetRekapUsia->setCellValue('D' . $rowIdx, $data['Bidang Kesehatan']);
            $sheetRekapUsia->setCellValue('E' . $rowIdx, $data['Bidang Ketahanan Pangan']);
            $sheetRekapUsia->setCellValue('F' . $rowIdx, $data['Bidang Seni dan Budaya']);
            $sheetRekapUsia->setCellValue('G' . $rowIdx, $data['Total']);

            $sheetRekapUsia->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($dataBorderStyle);

            foreach ($kategories as $k) $grandTotalsUsia[$k] += $data[$k];
            $grandTotalsUsia['Total'] += $data['Total'];
            $rowIdx++;
        }

        // Add Grand Total Row
        $sheetRekapUsia->setCellValue('A' . $rowIdx, '');
        $sheetRekapUsia->setCellValue('B' . $rowIdx, 'TOTAL KESELURUHAN');
        $sheetRekapUsia->setCellValue('C' . $rowIdx, $grandTotalsUsia['Bidang Pendidikan']);
        $sheetRekapUsia->setCellValue('D' . $rowIdx, $grandTotalsUsia['Bidang Kesehatan']);
        $sheetRekapUsia->setCellValue('E' . $rowIdx, $grandTotalsUsia['Bidang Ketahanan Pangan']);
        $sheetRekapUsia->setCellValue('F' . $rowIdx, $grandTotalsUsia['Bidang Seni dan Budaya']);
        $sheetRekapUsia->setCellValue('G' . $rowIdx, $grandTotalsUsia['Total']);
        $sheetRekapUsia->getStyle('A' . $rowIdx . ':G' . $rowIdx)->applyFromArray($headerStyle);
        $sheetRekapUsia->getStyle('A' . $rowIdx . ':G' . $rowIdx)->getFill()->getStartColor()->setRGB('D9D9D9');

        foreach (range(1, 7) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetRekapUsia->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Generate response
        $spreadsheet->setActiveSheetIndex(0);
        $fileName = 'Data_Pendaftar_DPD_' . date('Y-m-d_H-i-s') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function downloadAllZip(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $hasKtpPermission = $user && ($user->hasPermission('*') || $user->hasPermission(Permission::KTP_VIEW));

        $pendaftars = $this->getFilteredQuery($request)->with(['kontribusi', 'penghargaan'])->get();

        if ($pendaftars->isEmpty()) {
            return back()->withError('Tidak ada pendaftar yang memenuhi kriteria filter.');
        }

        // Check if there are any files at all
        $filesToZip = [];

        foreach ($pendaftars as $pendaftar) {
            // Folder name in zip
            $folderName = str_replace(['/', '\\', '?', '*', ':', '|', '"', '<', '>'], '_', $pendaftar->nomor_registrasi . ' - ' . $pendaftar->nama);

            // 1. Add KTP (only if user has KTP permission)
            if ($hasKtpPermission) {
                $ktp = $pendaftar->getRawOriginal('ktp');
                if (!empty($ktp)) {
                    $absolutePath = storage_path('app/private/' . str_replace('\\', '/', $ktp));
                    if (file_exists($absolutePath) && is_file($absolutePath)) {
                        $filesToZip[$folderName . '/KTP_' . basename($ktp)] = $absolutePath;
                    }
                }
            }

            // 2. Add Foto
            $foto = $pendaftar->getRawOriginal('foto');
            if (!empty($foto)) {
                $absolutePath = storage_path('app/private/' . str_replace('\\', '/', $foto));
                if (file_exists($absolutePath) && is_file($absolutePath)) {
                    $filesToZip[$folderName . '/Foto_' . basename($foto)] = $absolutePath;
                }
            }

            // 3. Add kontribusi bukti_dukung
            foreach ($pendaftar->kontribusi as $index => $kontribusi) {
                $buktis = $kontribusi->bukti_dukung;
                if (!empty($buktis) && is_array($buktis)) {
                    foreach ($buktis as $fileIdx => $bukti) {
                        if (!empty($bukti)) {
                            $absolutePath = storage_path('app/private/' . str_replace('\\', '/', $bukti));
                            if (file_exists($absolutePath) && is_file($absolutePath)) {
                                $localName = $folderName . '/Kontribusi_' . ($index + 1) . '_' . ($fileIdx + 1) . '_' . basename($bukti);
                                $filesToZip[$localName] = $absolutePath;
                            }
                        }
                    }
                }
            }

            // 4. Add penghargaan bukti_dukung
            foreach ($pendaftar->penghargaan as $index => $penghargaan) {
                $buktis = $penghargaan->bukti_dukung;
                if (!empty($buktis) && is_array($buktis)) {
                    foreach ($buktis as $fileIdx => $bukti) {
                        if (!empty($bukti)) {
                            $absolutePath = storage_path('app/private/' . str_replace('\\', '/', $bukti));
                            if (file_exists($absolutePath) && is_file($absolutePath)) {
                                $localName = $folderName . '/Penghargaan_' . ($index + 1) . '_' . ($fileIdx + 1) . '_' . basename($bukti);
                                $filesToZip[$localName] = $absolutePath;
                            }
                        }
                    }
                }
            }
        }

        if (empty($filesToZip)) {
            return back()->withError('Tidak ada berkas yang dapat diunduh.');
        }

        $zipFileName = 'Berkas_Pendaftar_Masal_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/private/temp_' . \Illuminate\Support\Str::random(16) . '.zip');

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas ZIP.');
        }

        foreach ($filesToZip as $localName => $absolutePath) {
            $zip->addFile($absolutePath, $localName);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function generateHistory()
    {
        $pendaftars = \App\Models\Pendaftar::all();
        $stages = [
            'Diajukan' => 0,
            'Lolos Verifikasi Berkas' => 1,
            'Lolos ke Tahap 50 Besar' => 2,
            'Lolos ke Tahap 10 Besar' => 3,
            'Lolos ke Tahap 3 Besar' => 4,
            'Lolos ke Tahap Wawancara' => 5,
            'Lolos ke Tahap Final' => 6,
        ];
        $stageNames = array_keys($stages);

        foreach ($pendaftars as $pendaftar) {
            // Hapus riwayat lama jika mau aman, atau biarkan. Kita asumsikan clear dulu.
            \App\Models\PendaftarRiwayat::where('pendaftar_id', $pendaftar->id)->delete();

            if ($pendaftar->status !== 'Tidak Lolos') {
                $currentRank = $stages[$pendaftar->status] ?? 0;
                for ($i = 0; $i <= $currentRank; $i++) {
                    \App\Models\PendaftarRiwayat::create([
                        'pendaftar_id' => $pendaftar->id,
                        'status' => $stageNames[$i],
                        'keterangan' => null,
                        'created_at' => $i === $currentRank ? $pendaftar->updated_at : $pendaftar->created_at->addSeconds($i),
                    ]);
                }
            } else {
                $beforeRank = $stages[$pendaftar->status_before] ?? 0;
                for ($i = 0; $i <= $beforeRank; $i++) {
                    \App\Models\PendaftarRiwayat::create([
                        'pendaftar_id' => $pendaftar->id,
                        'status' => $stageNames[$i],
                        'keterangan' => null,
                        'created_at' => $pendaftar->created_at->addSeconds($i),
                    ]);
                }
                \App\Models\PendaftarRiwayat::create([
                    'pendaftar_id' => $pendaftar->id,
                    'status' => 'Tidak Lolos',
                    'keterangan' => null,
                    'created_at' => $pendaftar->updated_at,
                ]);
            }
        }

        return back()->withSuccess('Riwayat pendaftar berhasil di-generate.');
    }

    public function updateRiwayatKeterangan(\Illuminate\Http\Request $request, $riwayatId)
    {
        $request->validate([
            'keterangan' => 'nullable|string'
        ]);

        $riwayat = \App\Models\PendaftarRiwayat::findOrFail($riwayatId);

        if ($riwayat->status === 'Diajukan') {
            return back()->withError('Keterangan untuk status Diajukan tidak dapat diubah.');
        }

        $riwayat->update([
            'keterangan' => $request->keterangan
        ]);

        return back()->withSuccess('Keterangan riwayat berhasil diperbarui.');
    }

    public function downloadTemplateKeterangan()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Catatan');

        $headers = ['Nomor Registrasi', 'Catatan Verifikator'];
        foreach ($headers as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add sample rows from existing pendaftar or dummy data
        $sampleData = [];
        $samplePendaftars = Pendaftar::latest()->take(2)->get();
        if ($samplePendaftars->isNotEmpty()) {
            foreach ($samplePendaftars as $sp) {
                $latestRiwayat = $sp->riwayats()->first();
                $sampleData[] = [
                    $sp->nomor_registrasi,
                    $latestRiwayat ? $latestRiwayat->keterangan : 'Berkas lengkap dan terverifikasi'
                ];
            }
        } else {
            $sampleData = [
                ['REG/2026/001', 'Berkas lengkap dan terverifikasi'],
                ['REG/2026/002', 'Surat rekomendasi belum ditandatangani'],
            ];
        }

        $dataBorderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
        ];

        foreach ($sampleData as $idx => $row) {
            $rowNum = $idx + 2;
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->applyFromArray($dataBorderStyle);
        }

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);

        $fileName = 'Template_Update_Catatan_Verifikator.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function importKeterangan(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return back()->withError('File Excel kosong atau tidak memiliki data.');
            }

            // Remove header row
            array_shift($rows);

            $updatedCount = 0;
            $notFoundNumbers = [];

            foreach ($rows as $row) {
                $nomorRegistrasi = isset($row[0]) ? trim((string)$row[0]) : '';
                $catatan = isset($row[1]) ? trim((string)$row[1]) : '';

                if (empty($nomorRegistrasi)) {
                    continue;
                }

                $pendaftar = Pendaftar::where('nomor_registrasi', $nomorRegistrasi)->first();

                if (!$pendaftar) {
                    $notFoundNumbers[] = $nomorRegistrasi;
                    continue;
                }

                $latestRiwayat = $pendaftar->riwayats()->first();

                if ($latestRiwayat) {
                    $latestRiwayat->update([
                        'keterangan' => $catatan !== '' ? $catatan : null,
                    ]);
                } else {
                    $pendaftar->riwayats()->create([
                        'status' => $pendaftar->status ?? 'Diajukan',
                        'keterangan' => $catatan !== '' ? $catatan : null,
                    ]);
                }

                $updatedCount++;
            }

            $message = "Berhasil mengupdate catatan verifikator untuk {$updatedCount} pendaftar.";
            if (!empty($notFoundNumbers)) {
                $message .= " (Tidak ditemukan: " . implode(', ', array_slice($notFoundNumbers, 0, 5));
                if (count($notFoundNumbers) > 5) {
                    $message .= " dan " . (count($notFoundNumbers) - 5) . " lainnya";
                }
                $message .= ")";
            }

            return back()->withSuccess($message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal mengimpor catatan verifikator: " . $e->getMessage());
            return back()->withError('Gagal mengolah file Excel: ' . $e->getMessage());
        }
    }
    // --- KONTRIBUSI & PENGHARGAAN ADMIN MANAGEMENT ---

    private function checkManagePermission()
    {
        $user = auth()->user();
        abort_if(!$user->hasPermission('*') && !$user->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE), 403, 'Unauthorized.');
    }

    public function storeKontribusi(\Illuminate\Http\Request $request, Pendaftar $pendaftar)
    {
        $this->checkManagePermission();
        $request->validate([
            'judul' => 'required|string|max:500',
            'deskripsi' => 'required|string',
            'dampak' => 'required|string',
            'bukti_dukung.*' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z|max:51200',
        ]);

        $buktiPaths = [];
        if ($request->hasFile('bukti_dukung')) {
            foreach ($request->file('bukti_dukung') as $file) {
                $buktiPaths[] = $file->store('pendaftar/' . $pendaftar->id);
            }
        }

        $pendaftar->kontribusi()->create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'dampak' => $request->dampak,
            'bukti_dukung' => $buktiPaths,
            'is_from_admin' => true,
        ]);

        return back()->withSuccess('Kontribusi berhasil ditambahkan.');
    }

    public function updateKontribusi(\Illuminate\Http\Request $request, Pendaftar $pendaftar, \App\Models\Kontribusi $kontribusi)
    {
        $this->checkManagePermission();
        abort_if(!$kontribusi->is_from_admin, 403, 'Anda tidak dapat mengedit data asli dari pendaftar.');

        $request->validate([
            'judul' => 'required|string|max:500',
            'deskripsi' => 'required|string',
            'dampak' => 'required|string',
            'bukti_dukung.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z|max:51200',
        ]);

        $buktiPaths = $kontribusi->bukti_dukung ?? [];
        if ($request->hasFile('bukti_dukung')) {
            foreach ($request->file('bukti_dukung') as $file) {
                $buktiPaths[] = $file->store('pendaftar/' . $pendaftar->id);
            }
        }

        $kontribusi->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'dampak' => $request->dampak,
            'bukti_dukung' => $buktiPaths,
        ]);

        return back()->withSuccess('Kontribusi berhasil diperbarui.');
    }

    public function destroyKontribusi(Pendaftar $pendaftar, \App\Models\Kontribusi $kontribusi)
    {
        $this->checkManagePermission();
        abort_if(!$kontribusi->is_from_admin, 403, 'Anda tidak dapat menghapus data asli dari pendaftar.');
        
        $kontribusi->delete();
        return back()->withSuccess('Kontribusi berhasil dihapus.');
    }

    public function storePenghargaan(\Illuminate\Http\Request $request, Pendaftar $pendaftar)
    {
        $this->checkManagePermission();
        $request->validate([
            'uraian' => 'required|string|max:500',
            'tahun' => 'required|string|max:4',
            'bukti_dukung.*' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z|max:51200',
        ]);

        $buktiPaths = [];
        if ($request->hasFile('bukti_dukung')) {
            foreach ($request->file('bukti_dukung') as $file) {
                $buktiPaths[] = $file->store('pendaftar/' . $pendaftar->id);
            }
        }

        $pendaftar->penghargaan()->create([
            'uraian' => $request->uraian,
            'tahun' => $request->tahun,
            'bukti_dukung' => $buktiPaths,
            'is_from_admin' => true,
        ]);

        return back()->withSuccess('Penghargaan berhasil ditambahkan.');
    }

    public function updatePenghargaan(\Illuminate\Http\Request $request, Pendaftar $pendaftar, \App\Models\Penghargaan $penghargaan)
    {
        $this->checkManagePermission();
        abort_if(!$penghargaan->is_from_admin, 403, 'Anda tidak dapat mengedit data asli dari pendaftar.');

        $request->validate([
            'uraian' => 'required|string|max:500',
            'tahun' => 'required|string|max:4',
            'bukti_dukung.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z|max:51200',
        ]);

        $buktiPaths = $penghargaan->bukti_dukung ?? [];
        if ($request->hasFile('bukti_dukung')) {
            foreach ($request->file('bukti_dukung') as $file) {
                $buktiPaths[] = $file->store('pendaftar/' . $pendaftar->id);
            }
        }

        $penghargaan->update([
            'uraian' => $request->uraian,
            'tahun' => $request->tahun,
            'bukti_dukung' => $buktiPaths,
        ]);

        return back()->withSuccess('Penghargaan berhasil diperbarui.');
    }

    public function destroyPenghargaan(Pendaftar $pendaftar, \App\Models\Penghargaan $penghargaan)
    {
        $this->checkManagePermission();
        abort_if(!$penghargaan->is_from_admin, 403, 'Anda tidak dapat menghapus data asli dari pendaftar.');
        
        $penghargaan->delete();
        return back()->withSuccess('Penghargaan berhasil dihapus.');
    }

    // --- PENILAIAN KERTAS KERJA ---

    public function kertasKerja(\Illuminate\Http\Request $request, Pendaftar $pendaftar)
    {
        $availableTahaps = [
            'Lolos Verifikasi Berkas',
            'Lolos ke Tahap 50 Besar',
            'Lolos ke Tahap 10 Besar',
            'Lolos ke Tahap 3 Besar',
            'Lolos ke Tahap Wawancara',
            'Lolos ke Tahap Final',
        ];

        $selectedTahap = $request->query('tahap', $pendaftar->status);
        if (!$selectedTahap || !str_starts_with($selectedTahap, 'Lolos')) {
            $selectedTahap = str_starts_with($pendaftar->status ?? '', 'Lolos') ? $pendaftar->status : 'Lolos Verifikasi Berkas';
        }

        if (!str_starts_with($selectedTahap ?? '', 'Lolos') && !str_starts_with($pendaftar->status ?? '', 'Lolos')) {
            return redirect()->route('modules::pendaftar.show', $pendaftar->id)
                ->with('error', 'Kertas Kerja Penilaian hanya berlaku untuk pendaftar dengan status Lolos.');
        }

        $pendaftar->load(['kontribusi', 'penghargaan', 'kertasKerja']);

        $aspeks = \App\Models\KategoriAspek::where('kategori', $pendaftar->kategori)
            ->orderBy('id', 'asc')
            ->get();

        $savedPenilaian = $pendaftar->kertasKerja
            ->filter(function ($kk) use ($selectedTahap, $pendaftar) {
                return $kk->tahap === $selectedTahap || (empty($kk->tahap) && $selectedTahap === $pendaftar->status);
            })
            ->keyBy('kategori_aspek_id');

        $items = $aspeks->map(function ($aspekItem) use ($savedPenilaian) {
            $saved = $savedPenilaian->get($aspekItem->id);

            return [
                'kategori_aspek_id' => $aspekItem->id,
                'aspek' => $aspekItem->aspek,
                'dimensi' => $aspekItem->dimensi,
                'bobot' => $aspekItem->bobot,
                'nilai' => $saved ? $saved->nilai : null,
                'total' => $saved ? $saved->total : null,
                'catatan_juri' => $saved ? $saved->catatan_juri : null,
                'tracking_media' => $saved ? $saved->tracking_media : null,
                'data_dukung' => $saved ? ($saved->data_dukung ?? []) : [],
            ];
        });

        $totalBobot = $items->sum('bobot');
        $totalNilaiAkhir = $items->sum(fn($i) => $i['total'] ?? 0);

        $savedTahapList = $pendaftar->kertasKerja->pluck('tahap')->filter()->unique()->values()->all();

        return view('pendaftar::kertas_kerja', compact(
            'pendaftar',
            'items',
            'totalBobot',
            'totalNilaiAkhir',
            'selectedTahap',
            'availableTahaps',
            'savedTahapList'
        ));
    }

    public function storeKertasKerja(\Illuminate\Http\Request $request, Pendaftar $pendaftar)
    {
        $selectedTahap = $request->input('tahap', $pendaftar->status);

        if (!str_starts_with($selectedTahap ?? '', 'Lolos') && !str_starts_with($pendaftar->status ?? '', 'Lolos')) {
            return redirect()->route('modules::pendaftar.show', $pendaftar->id)
                ->with('error', 'Kertas Kerja Penilaian hanya berlaku untuk pendaftar dengan status Lolos.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.kategori_aspek_id' => 'required|exists:kategori_aspeks,id',
            'items.*.nilai' => 'nullable|numeric|min:0|max:100',
            'items.*.catatan_juri' => 'nullable|string',
            'items.*.tracking_media' => 'nullable|string',
            'items.*.data_dukung' => 'nullable|array',
        ]);

        $user = auth()->user();

        foreach ($request->items as $itemData) {
            $kategoriAspek = \App\Models\KategoriAspek::find($itemData['kategori_aspek_id']);
            if (!$kategoriAspek) {
                continue;
            }

            $nilai = isset($itemData['nilai']) && $itemData['nilai'] !== '' ? (int)$itemData['nilai'] : null;
            $bobot = (int)$kategoriAspek->bobot;
            $total = !is_null($nilai) ? round(($nilai * $bobot) / 100, 2) : null;

            $dataDukung = [];
            if (!empty($itemData['data_dukung']) && is_array($itemData['data_dukung'])) {
                foreach ($itemData['data_dukung'] as $dd) {
                    if (!empty($dd['selected'])) {
                        $dataDukung[] = [
                            'kontribusi_id' => $dd['kontribusi_id'] ?? null,
                            'item_key' => $dd['item_key'] ?? null,
                            'title' => $dd['title'] ?? '',
                            'bukti' => $dd['bukti'] ?? '',
                            'catatan' => $dd['catatan'] ?? '',
                        ];
                    }
                }
            }

            \App\Models\PendaftarKertasKerja::updateOrCreate(
                [
                    'pendaftar_id' => $pendaftar->id,
                    'tahap' => $selectedTahap,
                    'kategori_aspek_id' => $kategoriAspek->id,
                ],
                [
                    'aspek' => $kategoriAspek->aspek,
                    'dimensi' => $kategoriAspek->dimensi,
                    'bobot' => $bobot,
                    'nilai' => $nilai,
                    'total' => $total,
                    'catatan_juri' => $itemData['catatan_juri'] ?? null,
                    'tracking_media' => $itemData['tracking_media'] ?? null,
                    'data_dukung' => $dataDukung,
                    'updated_by' => $user?->id,
                    'created_by' => $user?->id,
                ]
            );
        }

        if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'message' => "Kertas Kerja Penilaian ({$selectedTahap}) berhasil disimpan secara otomatis.",
                'tahap' => $selectedTahap,
                'timestamp' => date('H:i:s')
            ]);
        }

        return redirect()->route('modules::pendaftar.kertas-kerja', ['pendaftar' => $pendaftar->id, 'tahap' => $selectedTahap])
            ->with('success', "Kertas Kerja Penilaian ({$selectedTahap}) berhasil disimpan.");
    }

    public function exportKertasKerjaExcel(\Illuminate\Http\Request $request, Pendaftar $pendaftar)
    {
        $selectedTahap = $request->query('tahap', $pendaftar->status);

        if (!str_starts_with($selectedTahap ?? '', 'Lolos') && !str_starts_with($pendaftar->status ?? '', 'Lolos')) {
            return redirect()->route('modules::pendaftar.show', $pendaftar->id)
                ->with('error', 'Kertas Kerja Penilaian hanya berlaku untuk pendaftar dengan status Lolos.');
        }

        $pendaftar->load(['kontribusi', 'penghargaan', 'kertasKerja']);

        $aspeks = \App\Models\KategoriAspek::where('kategori', $pendaftar->kategori)
            ->orderBy('id', 'asc')
            ->get();

        $savedPenilaian = $pendaftar->kertasKerja
            ->filter(function ($kk) use ($selectedTahap, $pendaftar) {
                return $kk->tahap === $selectedTahap || (empty($kk->tahap) && $selectedTahap === $pendaftar->status);
            })
            ->keyBy('kategori_aspek_id');

        $items = $aspeks->map(function ($aspekItem) use ($savedPenilaian) {
            $saved = $savedPenilaian->get($aspekItem->id);

            return [
                'kategori_aspek_id' => $aspekItem->id,
                'aspek' => $aspekItem->aspek,
                'dimensi' => $aspekItem->dimensi,
                'bobot' => $aspekItem->bobot,
                'nilai' => $saved ? $saved->nilai : null,
                'total' => $saved ? $saved->total : null,
                'catatan_juri' => $saved ? $saved->catatan_juri : null,
                'tracking_media' => $saved ? $saved->tracking_media : null,
                'data_dukung' => $saved ? ($saved->data_dukung ?? []) : [],
            ];
        });

        $totalNilaiAkhir = $items->sum(fn($i) => $i['total'] ?? 0);

        // Helper to resolve absolute file path from relative storage path
        $resolvePhysicalPath = function ($path) {
            if (empty($path)) return null;
            if (file_exists($path)) return $path;

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->path($path);
            }
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                return \Illuminate\Support\Facades\Storage::disk('local')->path($path);
            }

            $storageAppPath = storage_path('app/' . $path);
            if (file_exists($storageAppPath)) return $storageAppPath;

            $storagePublicPath = storage_path('app/public/' . $path);
            if (file_exists($storagePublicPath)) return $storagePublicPath;

            $publicPath = public_path($path);
            if (file_exists($publicPath)) return $publicPath;

            return null;
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('FORM PENILAIAN');

        // 1. Title Rows
        $sheet->setCellValue('A1', 'FORM PENILAIAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // Subtitle (Kategori & Tahap Penilaian)
        $subTitle = 'Kategori ' . $pendaftar->kategori . ' | Tahap Penilaian: ' . $selectedTahap;
        $sheet->setCellValue('A3', $subTitle);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // 2. Participant & Nilai Akhir
        $sheet->setCellValue('A5', 'NAMA PESERTA');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A6', strtoupper($pendaftar->nama));
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);

        // Embed Foto Pendaftar if available
        $rawFoto = $pendaftar->getRawOriginal('foto') ?? $pendaftar->foto;
        $realFotoPath = $resolvePhysicalPath($rawFoto);
        if ($realFotoPath && in_array(strtolower(pathinfo($realFotoPath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            try {
                $drawingFoto = new Drawing();
                $drawingFoto->setName('Foto Pendaftar');
                $drawingFoto->setDescription('Foto ' . $pendaftar->nama);
                $drawingFoto->setPath($realFotoPath);
                $drawingFoto->setHeight(75);
                $drawingFoto->setCoordinates('G5');
                $drawingFoto->setOffsetX(5);
                $drawingFoto->setOffsetY(5);
                $drawingFoto->setWorksheet($sheet);
            } catch (\Throwable $e) {
                // Ignore corrupt image error
            }
        }

        $sheet->setCellValue('I5', 'NILAI AKHIR');
        $sheet->getStyle('I5')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('I6', number_format($totalNilaiAkhir, 2));
        $sheet->getStyle('I6')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('I6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7'); // Light green
        $sheet->getStyle('I6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 3. Table Headers at Row 8
        $headers = [
            'NO',
            'ASPEK',
            'DIMENSI',
            "NILAI\n(10-100)",
            'BOBOT',
            'TOTAL',
            'CATATAN JURI',
            'TRACKING MEDIA',
            'DATA DUKUNG'
        ];

        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($headers as $colIdx => $headerText) {
            $colLetter = $colLetters[$colIdx];
            $cellCoordinate = $colLetter . '8';
            $sheet->setCellValue($cellCoordinate, $headerText);

            $style = [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => ($colLetter === 'D') ? 'DCFCE7' : 'FEF3C7',
                    ],
                ],
            ];

            $sheet->getStyle($cellCoordinate)->applyFromArray($style);
        }
        $sheet->getRowDimension(8)->setRowHeight(28);

        // 4. Data Rows
        $row = 9;
        foreach ($items as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['aspek']);
            $sheet->setCellValue('C' . $row, $item['dimensi']);
            $sheet->setCellValue('D' . $row, $item['nilai'] !== null ? $item['nilai'] : '');
            $sheet->setCellValue('E' . $row, $item['bobot']);
            $sheet->setCellValue('F' . $row, $item['total'] !== null ? number_format($item['total'], 2) : '0');
            $sheet->setCellValue('G' . $row, $item['catatan_juri'] ?? '');

            // Tracking Media styling (Blue + Underline if URL)
            $trackingMedia = trim($item['tracking_media'] ?? '');
            $sheet->setCellValue('H' . $row, $trackingMedia);
            if (!empty($trackingMedia) && (str_starts_with($trackingMedia, 'http://') || str_starts_with($trackingMedia, 'https://'))) {
                $sheet->getStyle('H' . $row)->getFont()->setColor(new Color('0000FF'))->setUnderline(Font::UNDERLINE_SINGLE);
            }

            // Format & Embed Data Dukung cleanly using RichText
            $richText = new RichText();
            $drawingsInRow = [];
            $currentLineIdx = 0;
            $hasDataDukung = false;

            if (!empty($item['data_dukung']) && is_array($item['data_dukung'])) {
                foreach ($item['data_dukung'] as $ddIdx => $dd) {
                    $t = $dd['title'] ?? '';
                    $c = $dd['catatan'] ?? '';
                    $b = $dd['bukti'] ?? '';

                    if (!$t && !$b) continue;
                    $hasDataDukung = true;

                    // Header line for this data dukung item
                    $headerText = "• " . ($t ?: 'Bukti Dukung #' . ($ddIdx + 1));
                    $runHeader = $richText->createTextRun($headerText . "\n");
                    $runHeader->getFont()->setBold(true);
                    $headerLinesCount = max(1, (int)ceil(strlen($headerText) / 38));
                    $currentLineIdx += $headerLinesCount;

                    // Note line if exists
                    if ($c) {
                        $noteText = "   Catatan: \"" . $c . "\"";
                        $runNote = $richText->createTextRun($noteText . "\n");
                        $runNote->getFont()->setItalic(true)->setColor(new Color('555555'));
                        $noteLinesCount = max(1, (int)ceil(strlen($noteText) / 34));
                        $currentLineIdx += $noteLinesCount;
                    }

                    // Check file type
                    $realBuktiPath = $resolvePhysicalPath($b);
                    $ext = strtolower(pathinfo($realBuktiPath ?? $b, PATHINFO_EXTENSION));

                    if ($realBuktiPath && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        // Add 1 spacing line before image thumbnail for clean gap below Catatan
                        $richText->createTextRun("\n");
                        $currentLineIdx += 1;

                        // 18px per line height at 10pt font, 4px top margin
                        $offsetY = ($currentLineIdx * 18) + 4;

                        try {
                            $drawing = new Drawing();
                            $drawing->setName('Bukti - ' . ($t ?: 'Gambar'));
                            $drawing->setDescription($t ?: 'Gambar');
                            $drawing->setPath($realBuktiPath);
                            $drawing->setHeight(90); // 90px height
                            $drawing->setCoordinates('I' . $row);
                            $drawing->setOffsetX(15);
                            $drawing->setOffsetY($offsetY);
                            $drawing->setWorksheet($sheet);

                            $drawingsInRow[] = $drawing;
                        } catch (\Throwable $e) {
                            // Ignore drawing failure for corrupted file
                        }

                        // Add 5 blank lines (5 * 18px = 90px space) for drawing
                        $richText->createTextRun("\n\n\n\n\n");
                        $currentLineIdx += 5;
                    } elseif ($b) {
                        $fileUrl = route('modules::pendaftar.file', ['path' => $b]);
                        $linkText = "   " . $fileUrl;
                        
                        $runLink = $richText->createTextRun($linkText . "\n");
                        $runLink->getFont()->setColor(new Color('0000FF'))->setUnderline(Font::UNDERLINE_SINGLE);
                        
                        $linkLinesCount = max(1, (int)ceil(strlen($linkText) / 34));
                        $currentLineIdx += $linkLinesCount;
                    }

                    // Spacing between data dukung items
                    $richText->createTextRun("\n");
                    $currentLineIdx += 1;
                }
            }

            if ($hasDataDukung) {
                $sheet->setCellValue('I' . $row, $richText);
            } else {
                $sheet->setCellValue('I' . $row, '');
            }

            // Alignments & Styles
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

            // Column D (NILAI): light green fill, bold, center
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('D' . $row)->getFont()->setBold(true);
            $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');

            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

            // Border for entire row
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Calculate max lines across columns and set row height in points (1px = 0.75pt)
            $aspekLines = ceil(strlen($item['aspek'] ?? '') / 22);
            $dimensiLines = ceil(strlen($item['dimensi'] ?? '') / 42);
            $catatanJuriLines = ceil(strlen($item['catatan_juri'] ?? '') / 26);
            $trackingLines = ceil(strlen($item['tracking_media'] ?? '') / 26);

            $maxLinesInRow = max(1, $aspekLines, $dimensiLines, $catatanJuriLines, $trackingLines, $currentLineIdx);
            $rowHeightPx = ($maxLinesInRow * 18) + 12;
            $rowHeightPt = $rowHeightPx * 0.75; // Convert px to pt for PhpSpreadsheet row height

            $sheet->getRowDimension($row)->setRowHeight(max(35, $rowHeightPt));

            $row++;
        }

        // 5. Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('E' . $row, $items->sum('bobot'));
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('F' . $row, number_format($totalNilaiAkhir, 2));
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');

        $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 6. Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(48);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(45); // Enlarged to fit drawings

        // Download File
        $filename = 'Form_Penilaian_' . \Illuminate\Support\Str::slug($pendaftar->nama, '_') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
