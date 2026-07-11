<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function __invoke()
    {
        return view('landing');
    }

    public function track(Request $request)
    {
        $request->validate([
            'reg_id' => 'required|string|max:50',
        ]);

        $pendaftar = \App\Models\Pendaftar::where('nomor_registrasi', $request->reg_id)->first();

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
