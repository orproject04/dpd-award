<?php

namespace Modules\Pendaftar\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Pendaftar\Models\Pendaftar;
use Modules\Pendaftar\PendaftarTableView;
use Modules\Pendaftar\Requests\Store;
use Modules\Pendaftar\Requests\Update;
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

    public function serveFile(\Illuminate\Http\Request $request)
    {
        $path = $request->query('path');
        if (empty($path)) {
            abort(404);
        }

        // Replace backslashes with forward slashes for uniform checks
        $cleanPath = str_replace('\\', '/', $path);

        // Security check: prevent directory traversal and ensure it stays within pendaftar folder
        if (str_contains($cleanPath, '..') || !str_starts_with($cleanPath, 'pendaftar/')) {
            abort(403, 'Unauthorized access.');
        }

        $fullPath = storage_path('app/private/' . $cleanPath);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404, 'File not found.');
        }

        if ($request->has('download')) {
            return response()->download($fullPath);
        }

        return response()->file($fullPath);
    }

    public function downloadAllFiles(Pendaftar $pendaftar)
    {
        $files = [];

        // Add ktp
        $ktp = $pendaftar->getRawOriginal('ktp');
        if (!empty($ktp)) {
            $files['KTP_' . basename($ktp)] = storage_path('app/private/' . str_replace('\\', '/', $ktp));
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
        $zipPath = tempnam(sys_get_temp_dir(), 'zip');

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
        ]);

        $pendaftar->update([
            'status' => $validated['status'],
        ]);

        return back()->withSuccess('Status pendaftar berhasil diperbarui.');
    }

    protected function getFilteredQuery(\Illuminate\Http\Request $request)
    {
        $query = Pendaftar::query();

        $filters = [
            new \App\Http\Filters\KategoriFilter,
            new \App\Http\Filters\StatusFilter,
        ];

        foreach ($filters as $filter) {
            $key = $filter->key();
            $value = $request->get($key);
            $query = $filter->apply($query, $value);
        }

        return $query
            ->autoSort()
            ->latest('updated_at')
            ->autoSearch($request->get('search'));
    }

    public function exportExcel(\Illuminate\Http\Request $request)
    {
        $pendaftars = $this->getFilteredQuery($request)->with(['kontribusi', 'penghargaan'])->get();

        $spreadsheet = new Spreadsheet();

        // ----------------------------------------------------
        // SHEET 1: PENDAFTAR
        // ----------------------------------------------------
        $sheetPendaftar = $spreadsheet->getActiveSheet();
        $sheetPendaftar->setTitle('Pendaftar');

        // Headers
        $headersPendaftar = [
            'No', 'Nomor Registrasi', 'Kategori', 'Nama Lengkap', 'Tempat Lahir', 
            'Tanggal Lahir', 'Jenis Kelamin', 'Pendidikan', 'Alamat', 'Nomor WA', 
            'Email', 'Status', 'Tanggal Registrasi'
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
        $sheetPendaftar->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheetPendaftar->getRowDimension(1)->setRowHeight(25);

        $rowIdx = 2;
        foreach ($pendaftars as $index => $pendaftar) {
            $sheetPendaftar->setCellValue('A' . $rowIdx, $index + 1);
            $sheetPendaftar->setCellValue('B' . $rowIdx, $pendaftar->nomor_registrasi);
            $sheetPendaftar->setCellValue('C' . $rowIdx, $pendaftar->kategori);
            $sheetPendaftar->setCellValue('D' . $rowIdx, $pendaftar->nama);
            $sheetPendaftar->setCellValue('E' . $rowIdx, $pendaftar->tempat_lahir);
            $sheetPendaftar->setCellValue('F' . $rowIdx, $pendaftar->tanggal_lahir);
            $sheetPendaftar->setCellValue('G' . $rowIdx, $pendaftar->jenis_kelamin);
            $sheetPendaftar->setCellValue('H' . $rowIdx, $pendaftar->pendidikan);
            $sheetPendaftar->setCellValue('I' . $rowIdx, $pendaftar->alamat);
            $sheetPendaftar->setCellValue('J' . $rowIdx, $pendaftar->nomor_wa);
            $sheetPendaftar->setCellValue('K' . $rowIdx, $pendaftar->email);
            $sheetPendaftar->setCellValue('L' . $rowIdx, $pendaftar->status ?? 'Diajukan');
            $sheetPendaftar->setCellValue('M' . $rowIdx, $pendaftar->created_at->format('Y-m-d H:i:s'));

            // Apply light borders
            $sheetPendaftar->getStyle('A' . $rowIdx . ':M' . $rowIdx)->applyFromArray($dataBorderStyle);
            $rowIdx++;
        }

        // Auto-fit columns
        foreach (range(1, 13) as $colIdx) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheetPendaftar->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // ----------------------------------------------------
        // SHEET 2: KONTRIBUSI
        // ----------------------------------------------------
        $sheetKontribusi = $spreadsheet->createSheet();
        $sheetKontribusi->setTitle('Kontribusi');

        $headersKontribusi = [
            'No', 'Nomor Registrasi Pendaftar', 'Nama Pendaftar', 'Judul Kontribusi', 'Deskripsi', 'Dampak', 'Bukti Dukung'
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
            'No', 'Nomor Registrasi Pendaftar', 'Nama Pendaftar', 'Uraian Penghargaan', 'Tahun', 'Bukti Dukung'
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

        // Serve file as download
        $fileName = 'Data_Pendaftar_Masal_' . date('Ymd_His') . '.xlsx';
        
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
        $pendaftars = $this->getFilteredQuery($request)->with(['kontribusi', 'penghargaan'])->get();

        if ($pendaftars->isEmpty()) {
            return back()->withError('Tidak ada pendaftar yang memenuhi kriteria filter.');
        }

        // Check if there are any files at all
        $filesToZip = [];

        foreach ($pendaftars as $pendaftar) {
            // Folder name in zip
            $folderName = str_replace(['/', '\\', '?', '*', ':', '|', '"', '<', '>'], '_', $pendaftar->nomor_registrasi . ' - ' . $pendaftar->nama);

            // 1. Add KTP
            $ktp = $pendaftar->getRawOriginal('ktp');
            if (!empty($ktp)) {
                $absolutePath = storage_path('app/private/' . str_replace('\\', '/', $ktp));
                if (file_exists($absolutePath) && is_file($absolutePath)) {
                    $filesToZip[$folderName . '/KTP_' . basename($ktp)] = $absolutePath;
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
        $zipPath = tempnam(sys_get_temp_dir(), 'zip');

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
}
