<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::latest()->paginate(10);
        return view('berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('berita.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sumber' => 'nullable|string|max:255',
            'tanggal' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'kutipan' => 'nullable|string',
            'tautan' => 'nullable|string|max:255',
            'status_aktif' => 'boolean',
            'gambar' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $validated['status_aktif'] = $request->has('status_aktif');

        Berita::create($validated);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit(Berita $berita)
    {
        return view('berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'sumber' => 'nullable|string|max:255',
            'tanggal' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'kutipan' => 'nullable|string',
            'tautan' => 'nullable|string|max:255',
            'status_aktif' => 'boolean',
            'gambar' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $validated['status_aktif'] = $request->has('status_aktif');

        $berita->update($validated);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy(Berita $berita)
    {
        $berita->delete();
        return redirect()->route('berita.index')->with('success', 'Berita berhasil dihapus');
    }
}
