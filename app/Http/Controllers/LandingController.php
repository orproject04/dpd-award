<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Services\SertifikatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Pendaftar\Responses\LargeFileResponse;

class LandingController extends Controller
{
    public function __invoke()
    {
        $kategoriCounts = \Illuminate\Support\Facades\Cache::remember('landing_kategori_counts', 3600, function () {
            return Pendaftar::select('kategori', DB::raw('count(*) as total'))
                ->groupBy('kategori')
                ->pluck('total', 'kategori')
                ->toArray();
        });

        $beritaTerkini = \App\Models\Berita::where('status_aktif', true)->latest()->take(3)->get();

        $response = response()->view('landing', compact('kategoriCounts', 'beritaTerkini'));
        $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=3600');
        return $response;
    }

    public function berita()
    {
        $beritaList = \App\Models\Berita::where('status_aktif', true)->latest()->paginate(9);
        return view('berita-list', compact('beritaList'));
    }

    public function track(Request $request, SertifikatService $sertifikatService)
    {
        $request->validate([
            'reg_id' => 'required|string|max:50',
        ]);

        $pendaftar = $this->findPendaftarByRegId((string) $request->reg_id);

        if (!$pendaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi tidak ditemukan.'
            ], 404);
        }

        $displayStatus = Pendaftar::getDisplayStatus($pendaftar);
        $certificates = $sertifikatService->getAvailableCertificates($pendaftar);

        return response()->json([
            'success' => true,
            'data' => [
                'nomor_registrasi' => $pendaftar->nomor_registrasi,
                'nama' => $pendaftar->nama,
                'status' => $displayStatus,
                'kategori' => $pendaftar->kategori,
                'certificates' => $certificates,
            ]
        ]);
    }

    /**
     * Menyajikan file PDF sertifikat secara aman melalui URL bertanda tangan sementara.
     */
    public function trackCertificate(Request $request, SertifikatService $sertifikatService)
    {
        if (!$request->hasValidRelativeSignature()) {
            abort(403, 'Tautan sertifikat tidak valid atau telah kedaluwarsa. Silakan lakukan pelacakan ulang.');
        }

        $regId = trim((string) $request->query('reg_id', ''));
        $tahap = trim((string) $request->query('tahap', ''));
        if ($regId === '' || $tahap === '') {
            abort(404);
        }

        $pendaftar = $this->findPendaftarByRegId($regId);
        if (!$pendaftar) {
            abort(404, 'Data pendaftar tidak ditemukan.');
        }

        $resolved = $sertifikatService->resolveCertificateFileForStage($pendaftar, $tahap);
        if ($resolved === null) {
            abort(404, 'Sertifikat tidak ditemukan atau belum tersedia.');
        }

        return LargeFileResponse::make(
            $resolved['path'],
            $request,
            $request->boolean('download'),
            $resolved['download_name']
        );
    }

    protected function findPendaftarByRegId(string $regId): ?Pendaftar
    {
        $trimmed = trim($regId);
        if ($trimmed === '') {
            return null;
        }

        return Pendaftar::with('riwayats')
            ->whereIn('nomor_registrasi', array_values(array_unique([$trimmed, strtoupper($trimmed)])))
            ->first();
    }
}
