<?php

namespace Modules\Pendaftar\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Pendaftar\Models\Pendaftar;
use Modules\Pendaftar\PendaftarTableView;
use Modules\Pendaftar\Requests\Store;
use Modules\Pendaftar\Requests\Update;

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
}
