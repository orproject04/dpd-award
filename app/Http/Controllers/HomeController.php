<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\Kontribusi;
use App\Models\Penghargaan;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final class HomeController
{
    /**
     * Handle the incoming request and show the dashboard.
     */
    public function __invoke(\Illuminate\Http\Request $request): View
    {
        $availableKategories = Pendaftar::select('kategori')->whereNotNull('kategori')->where('kategori', '!=', '')->distinct()->pluck('kategori')->toArray();
        
        $desiredOrder = [
            'Bidang Pendidikan',
            'Bidang Kesehatan',
            'Bidang Ketahanan Pangan',
            'Bidang Seni dan Budaya'
        ];
        
        usort($availableKategories, function($a, $b) use ($desiredOrder) {
            $posA = array_search($a, $desiredOrder);
            $posB = array_search($b, $desiredOrder);
            $posA = $posA === false ? 999 : $posA;
            $posB = $posB === false ? 999 : $posB;
            return $posA <=> $posB;
        });

        $baseQuery = Pendaftar::query();

        // 1. General stats counts
        $totalPendaftar = (clone $baseQuery)->count();
        $totalKontribusi = Kontribusi::whereIn('pendaftar_id', (clone $baseQuery)->select('id'))->count();
        $totalPenghargaan = Penghargaan::whereIn('pendaftar_id', (clone $baseQuery)->select('id'))->count();

        // 2. Count by Status
        $statusCounts = (clone $baseQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 3. Count by Kategori (if unfiltered, shows all. if filtered, shows only the filtered one)
        $kategoriCounts = (clone $baseQuery)->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        // 4. Count by Gender (jenis_kelamin)
        $genderCounts = (clone $baseQuery)->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        // 5. Count by Pendidikan
        $pendidikanCounts = (clone $baseQuery)->select('pendidikan', DB::raw('count(*) as total'))
            ->groupBy('pendidikan')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // 6. Registration Trend (last 30 days)
        $trendData = (clone $baseQuery)->select(DB::raw("DATE(created_at) as date_only"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy(DB::raw("DATE(created_at)"), 'asc')
            ->get();

        $trendLabels = [];
        $trendValues = [];

        // Build continuous 30-day timeline to avoid missing days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        $dateMap = $trendData->pluck('total', 'date_only')->toArray();

        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $trendLabels[] = $date->format('d M');

            $total = 0;
            foreach ($dateMap as $dbDate => $val) {
                if (date('Y-m-d', strtotime((string)$dbDate)) === $formattedDate) {
                    $total = (int)$val;
                    break;
                }
            }
            $trendValues[] = $total;
        }

        // 7. Recent Registrants (latest 5)
        $recentPendaftar = (clone $baseQuery)->orderBy('created_at', 'desc')
            ->withCount(['kontribusi', 'penghargaan'])
            ->limit(10)
            ->get();

        // 8. Funnel / pipeline counts per stage
        $allStages = [
            'Tidak Lolos',
            'Diajukan',
            'Lolos Verifikasi Berkas',
            'Lolos ke Tahap 50 Besar',
            'Lolos ke Tahap 10 Besar',
            'Lolos ke Tahap 5 Besar',
            'Lolos ke Tahap Wawancara',
            'Lolos ke Tahap Final',
        ];
        $funnelCounts = [];
        foreach ($allStages as $stage) {
            $funnelCounts[$stage] = $statusCounts[$stage] ?? 0;
        }

        // 9. Conversion rate: finalist / total (avoid division by zero)
        $finalistCount = $statusCounts['Lolos ke Tahap Final'] ?? 0;
        $pendingCount  = $statusCounts['Diajukan'] ?? 0;
        $rejectedCount = $statusCounts['Tidak Lolos'] ?? 0;
        $conversionRate = $totalPendaftar > 0
            ? round(($finalistCount / $totalPendaftar) * 100, 1)
            : 0;

        // 10. New registrants today & this week
        $newToday = (clone $baseQuery)->whereDate('created_at', Carbon::today())->count();
        $newThisWeek = (clone $baseQuery)->where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        // 11. Age distribution (from tanggal_lahir)
        $ageGroups = [
            '< 25' => 0,
            '25–34' => 0,
            '35–44' => 0,
            '45–54' => 0,
            '55+' => 0,
        ];
        $pendaftarWithAge = (clone $baseQuery)->select('tanggal_lahir')->whereNotNull('tanggal_lahir')->get();
        foreach ($pendaftarWithAge as $p) {
            $age = Carbon::parse($p->tanggal_lahir)->age;
            if ($age < 25) $ageGroups['< 25']++;
            elseif ($age < 35) $ageGroups['25–34']++;
            elseif ($age < 45) $ageGroups['35–44']++;
            elseif ($age < 55) $ageGroups['45–54']++;
            else $ageGroups['55+']++;
        }

        // 12. Geographic distribution (Grouped by category for JS filtering)
        $geoProvinsi = [];
        $geoWilayah = [];
        $categoriesToFetch = array_merge(['Semua Kategori'], $availableKategories);

        foreach ($categoriesToFetch as $kat) {
            $q = clone $baseQuery;
            if ($kat !== 'Semua Kategori') {
                $q->where('kategori', $kat);
            }

            $allProvinsiCounts = [];
            foreach (\App\Models\Pendaftar::getProvinsiMap() as $wilayah => $provinsis) {
                foreach ($provinsis as $prov) {
                    $allProvinsiCounts[$prov] = 0;
                }
            }

            $dbProvinsiCounts = (clone $q)->select('provinsi', DB::raw('count(*) as total'))
                ->whereNotNull('provinsi')
                ->where('provinsi', '!=', '')
                ->groupBy('provinsi')
                ->pluck('total', 'provinsi')
                ->toArray();
                
            $provCounts = array_merge($allProvinsiCounts, $dbProvinsiCounts);

            $wilCounts = [];
            foreach (\App\Models\Pendaftar::getProvinsiMap() as $wilayah => $provinsis) {
                $wilCounts[$wilayah] = 0;
                foreach ($provinsis as $prov) {
                    if (isset($provCounts[$prov])) {
                        $wilCounts[$wilayah] += $provCounts[$prov];
                    }
                }
            }
            
            arsort($provCounts);
            
            $geoProvinsi[$kat] = $provCounts;
            $geoWilayah[$kat] = $wilCounts;
        }

        // Pass all stats to the dashboard view
        return view('home', compact(
            'availableKategories',
            'totalPendaftar',
            'totalKontribusi',
            'totalPenghargaan',
            'statusCounts',
            'kategoriCounts',
            'genderCounts',
            'pendidikanCounts',
            'trendLabels',
            'trendValues',
            'recentPendaftar',
            'funnelCounts',
            'finalistCount',
            'pendingCount',
            'rejectedCount',
            'conversionRate',
            'newToday',
            'newThisWeek',
            'ageGroups',
            'geoProvinsi',
            'geoWilayah'
        ));
    }

    /**
     * Clear the application cache.
     */
    public function clearCache(): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->user() || !auth()->user()->hasPermission('*')) {
            abort(403, 'Unauthorized action.');
        }

        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('responsecache:clear');

        try {
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Exception $e) {
            // ignore if not installed
        }

        try {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        } catch (\Throwable $e) {
            // ignore if not initialized
        }

        return redirect()->back()->withSuccess(__('Cache berhasil dibersihkan.'));
    }
}
