<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftar;
use Illuminate\Support\Facades\DB;

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

    public function track(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string|max:50',
        ]);

        $pendaftar = Pendaftar::where('nomor_registrasi', $request->reg_id)->first();

        if (!$pendaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi tidak ditemukan.'
            ], 404);
        }

        $displayStatus = Pendaftar::getDisplayStatus($pendaftar);

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $pendaftar->nama,
                'status' => $displayStatus,
                'kategori' => $pendaftar->kategori,
            ]
        ]);
    }
}
