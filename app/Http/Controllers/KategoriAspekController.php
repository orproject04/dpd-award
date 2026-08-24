<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\KategoriAspek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriAspekController extends Controller
{
    /**
     * Check ACL Permission for Setting Aspek
     */
    protected function checkPermission(): void
    {
        $user = auth()->user();
        if (!$user || !($user->hasPermission('*') || $user->hasPermission(Permission::SETTING_ASPEK_MANAGE))) {
            abort(403, 'Anda tidak memiliki akses ke halaman Setting Aspek.');
        }
    }

    public function index(Request $request)
    {
        $this->checkPermission();

        $selectedKategori = $request->get('kategori');
        $search = $request->get('search');

        $query = KategoriAspek::query();

        if (!empty($selectedKategori)) {
            $query->where('kategori', $selectedKategori);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('aspek', 'like', "%{$search}%")
                  ->orWhere('dimensi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $aspeks = $query->orderBy('kategori')->orderBy('id', 'asc')->paginate(20)->withQueryString();

        // Rekap Bobot per Kategori
        $rekapBobot = KategoriAspek::select('kategori', DB::raw('SUM(bobot) as total_bobot'), DB::raw('COUNT(*) as total_aspek'))
            ->groupBy('kategori')
            ->get();

        $defaultKategories = KategoriAspek::defaultKategories();
        $totalDataCount = KategoriAspek::count();

        return view('kategori_aspek.index', compact('aspeks', 'rekapBobot', 'selectedKategori', 'search', 'defaultKategories', 'totalDataCount'));
    }

    public function create(Request $request)
    {
        $this->checkPermission();

        $defaultKategories = KategoriAspek::defaultKategories();
        $selectedKategori = $request->get('kategori', '');

        return view('kategori_aspek.create', compact('defaultKategories', 'selectedKategori'));
    }

    public function store(Request $request)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'aspek' => 'required|string|max:255',
            'dimensi' => 'nullable|string',
            'bobot' => 'required|integer|min:0',
        ], [
            'kategori.required' => 'Kategori wajib diisi.',
            'aspek.required' => 'Aspek wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.integer' => 'Bobot harus berupa angka bulat.',
            'bobot.min' => 'Bobot tidak boleh bernilai negatif.',
        ]);

        KategoriAspek::create($validated);

        return redirect()->route('kategori-aspek.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Setting Aspek berhasil ditambahkan');
    }

    public function edit(KategoriAspek $kategoriAspek)
    {
        $this->checkPermission();

        $defaultKategories = KategoriAspek::defaultKategories();

        return view('kategori_aspek.edit', compact('kategoriAspek', 'defaultKategories'));
    }

    public function update(Request $request, KategoriAspek $kategoriAspek)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'kategori' => 'required|string|max:255',
            'aspek' => 'required|string|max:255',
            'dimensi' => 'nullable|string',
            'bobot' => 'required|integer|min:0',
        ], [
            'kategori.required' => 'Kategori wajib diisi.',
            'aspek.required' => 'Aspek wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.integer' => 'Bobot harus berupa angka bulat.',
            'bobot.min' => 'Bobot tidak boleh bernilai negatif.',
        ]);

        $kategoriAspek->update($validated);

        return redirect()->route('kategori-aspek.index', ['kategori' => $validated['kategori']])
            ->with('success', 'Setting Aspek berhasil diperbarui');
    }

    public function destroy(KategoriAspek $kategoriAspek)
    {
        $this->checkPermission();

        $kategori = $kategoriAspek->kategori;
        $kategoriAspek->delete();

        return redirect()->route('kategori-aspek.index', ['kategori' => $kategori])
            ->with('success', 'Setting Aspek berhasil dihapus');
    }

    public function seedDefault(Request $request)
    {
        $this->checkPermission();

        if (KategoriAspek::count() > 0) {
            return redirect()->route('kategori-aspek.index')
                ->with('error', 'Seeder hanya dapat dijalankan jika tabel masih kosong.');
        }

        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'KategoriAspekSeeder',
            '--force' => true,
        ]);

        return redirect()->route('kategori-aspek.index')
            ->with('success', 'Data awal Aspek, Dimensi, & Bobot berhasil di-generate!');
    }
}
