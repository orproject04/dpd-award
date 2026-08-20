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
        $kategories = [
            'Bidang Pendidikan',
            'Bidang Kesehatan',
            'Bidang Ketahanan Pangan',
            'Bidang Seni dan Budaya'
        ];

        foreach ($pendaftars as $p) {
            $prov = (empty($p->provinsi) || $p->provinsi == '-') ? 'Tidak Diketahui' : $p->provinsi;
            $wil = (empty($p->wilayah) || $p->wilayah == '-') ? 'Tidak Diketahui' : $p->wilayah;
            $kat = $p->kategori;

            if (!isset($rekapProvinsi[$prov])) {
                $rekapProvinsi[$prov] = array_fill_keys($kategories, 0);
                $rekapProvinsi[$prov]['Total'] = 0;
            }
            if (!isset($rekapWilayah[$wil])) {
                $rekapWilayah[$wil] = array_fill_keys($kategories, 0);
                $rekapWilayah[$wil]['Total'] = 0;
            }

            if (in_array($kat, $kategories)) {
                $rekapProvinsi[$prov][$kat]++;
                $rekapWilayah[$wil][$kat]++;
            }

            $rekapProvinsi[$prov]['Total']++;
            $rekapWilayah[$wil]['Total']++;
        }

        ksort($rekapProvinsi);
        ksort($rekapWilayah);

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
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
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
}
