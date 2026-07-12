<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftar;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function __invoke()
    {
        $kategoriCounts = Pendaftar::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();
        return view('landing', compact('kategoriCounts'));
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

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $pendaftar->nama,
                'status' => $pendaftar->status,
                'kategori' => $pendaftar->kategori,
            ]
        ]);
    }
}
