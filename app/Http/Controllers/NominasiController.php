<?php

namespace App\Http\Controllers;

use App\Models\Kontribusi;
use App\Models\Pendaftar;
use App\Models\Penghargaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NominasiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'namaNominee' => 'required|string|max:255',
            'wilayah' => 'required|string|max:50',
            'tanggalLahir' => 'required|date',
            'jenisKelamin' => 'required|string|max:15',
            'pendidikan' => 'required|string|max:15',
            'alamat' => 'required|string',
            'telp' => 'required|string|max:15',
            'email' => 'required|email|max:50',
            'ktp' => 'required|file',
            'foto' => 'nullable|file',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate Registration Number
            $kodeKategori = [
                'pendidikan' => 'BP',
                'kesehatan' => 'BK',
                'pangan' => 'BKP',
                'budaya' => 'BSB',
            ];

            $kategoriCode = $kodeKategori[$request->kategori] ?? 'XX';
            $tglLahir = date('ymd', strtotime($request->tanggalLahir));

            // Get the latest number suffix for this specific prefix (e.g., DPD-BSB26-991003)
            // The requirement says: XXXX adalah incement 1 dari setiap pendaftar
            // This means it's a global increment or category specific. Let's make it a global increment of all registrations to be safe and robust, or count total + 1.
            $count = Pendaftar::count() + 1;
            $increment = str_pad($count, 4, '0', STR_PAD_LEFT);

            $nomorRegistrasi = "DPD-{$kategoriCode}26-{$tglLahir}{$increment}";

            // 2. Handle File Uploads for KTP & Foto
            $ktpPath = null;
            $fotoPath = null;

            $basePath = "{$request->kategori}/{$nomorRegistrasi}";

            if ($request->hasFile('ktp')) {
                $ktp = $request->file('ktp');
                $ktpPath = $ktp->storeAs("pendaftar/{$basePath}", 'ktp.' . $ktp->extension());
            }

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoPath = $foto->storeAs("pendaftar/{$basePath}", 'foto.' . $foto->extension());
            }

            // 3. Save Pendaftar
            $namaKategoriFull = [
                'pendidikan' => 'Bidang Pendidikan',
                'kesehatan' => 'Bidang Kesehatan',
                'pangan' => 'Bidang Ketahanan Pangan',
                'budaya' => 'Bidang Seni dan Budaya',
            ];
            $kategoriFull = $namaKategoriFull[$request->kategori] ?? $request->kategori;

            $pendaftar = Pendaftar::create([
                'nomor_registrasi' => $nomorRegistrasi,
                'kategori' => $kategoriFull,
                'nama' => $request->namaNominee,
                'tempat_lahir' => $request->wilayah,
                'tanggal_lahir' => $request->tanggalLahir,
                'jenis_kelamin' => $request->jenisKelamin,
                'pendidikan' => $request->pendidikan,
                'alamat' => $request->alamat,
                'nomor_wa' => $request->telp,
                'email' => $request->email,
                'ktp' => $ktpPath,
                'foto' => $fotoPath ?? '',
                'status' => 'Diajukan',
            ]);

            // 4. Handle Kontribusi
            if ($request->has('capaianList')) {
                foreach ($request->capaianList as $index => $capaian) {
                    $evidencePaths = [];
                    // Process files for this kontribusi
                    $filesKey = "capaianFiles_{$index}";
                    if ($request->hasFile($filesKey)) {
                        foreach ($request->file($filesKey) as $file) {
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs("pendaftar/{$basePath}/kontribusi", $filename);
                            $evidencePaths[] = $path;
                        }
                    }

                    Kontribusi::create([
                        'pendaftar_id' => $pendaftar->id,
                        'judul' => $capaian['judul'] ?? '',
                        'deskripsi' => $capaian['deskripsi'] ?? '',
                        'dampak' => $capaian['dampak'] ?? '',
                        'bukti_dukung' => $evidencePaths,
                    ]);
                }
            }

            // 5. Handle Penghargaan
            if ($request->has('penghargaanList')) {
                foreach ($request->penghargaanList as $index => $penghargaan) {
                    // Check if it's not empty
                    if (empty($penghargaan['nama']) && empty($penghargaan['tahun'])) {
                        continue;
                    }

                    $evidencePaths = [];
                    $filesKey = "penghargaanFiles_{$index}";
                    if ($request->hasFile($filesKey)) {
                        foreach ($request->file($filesKey) as $file) {
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $path = $file->storeAs("pendaftar/{$basePath}/penghargaan", $filename);
                            $evidencePaths[] = $path;
                        }
                    }

                    Penghargaan::create([
                        'pendaftar_id' => $pendaftar->id,
                        'uraian' => $penghargaan['nama'] ?? '',
                        'tahun' => (isset($penghargaan['tahun']) && is_numeric($penghargaan['tahun'])) ? (int)$penghargaan['tahun'] : (int)date('Y'),
                        'bukti_dukung' => $evidencePaths,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'regId' => $nomorRegistrasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Nominasi Store: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
