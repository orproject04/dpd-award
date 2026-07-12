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
    public function __invoke(): View
    {
        // 1. General stats counts
        $totalPendaftar = Pendaftar::count();
        $totalKontribusi = Kontribusi::count();
        $totalPenghargaan = Penghargaan::count();

        // 2. Count by Status
        $statusCounts = Pendaftar::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // 3. Count by Kategori
        $kategoriCounts = Pendaftar::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        // 4. Count by Gender (jenis_kelamin)
        $genderCounts = Pendaftar::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        // 5. Count by Pendidikan
        $pendidikanCounts = Pendaftar::select('pendidikan', DB::raw('count(*) as total'))
            ->groupBy('pendidikan')
            ->pluck('total', 'pendidikan')
            ->toArray();

        // 6. Registration Trend (last 30 days)
        $trendData = Pendaftar::select(DB::raw("DATE(created_at) as date_only"), DB::raw('count(*) as total'))
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
        $recentPendaftar = Pendaftar::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 8. Funnel / pipeline counts per stage
        $allStages = [
            'Diajukan',
            'Lolos Verifikasi Berkas',
            'Lolos Penilaian Tahap 1',
            'Lolos Penilaian Tahap 2',
            'Lolos Penilaian Tahap 3',
            'Lolos Tahap Wawancara',
            'Lolos Tahap Final',
            'Tidak Lolos',
        ];
        $funnelCounts = [];
        foreach ($allStages as $stage) {
            $funnelCounts[$stage] = $statusCounts[$stage] ?? 0;
        }

        // 9. Conversion rate: finalist / total (avoid division by zero)
        $finalistCount = $statusCounts['Lolos Tahap Final'] ?? 0;
        $pendingCount  = $statusCounts['Diajukan'] ?? 0;
        $rejectedCount = $statusCounts['Tidak Lolos'] ?? 0;
        $conversionRate = $totalPendaftar > 0
            ? round(($finalistCount / $totalPendaftar) * 100, 1)
            : 0;

        // 10. New registrants today & this week
        $newToday = Pendaftar::whereDate('created_at', Carbon::today())->count();
        $newThisWeek = Pendaftar::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        // 11. Age distribution (from tanggal_lahir)
        $ageGroups = [
            '< 25' => 0,
            '25–34' => 0,
            '35–44' => 0,
            '45–54' => 0,
            '55+' => 0,
        ];
        $pendaftarWithAge = Pendaftar::select('tanggal_lahir')->whereNotNull('tanggal_lahir')->get();
        foreach ($pendaftarWithAge as $p) {
            $age = Carbon::parse($p->tanggal_lahir)->age;
            if ($age < 25) $ageGroups['< 25']++;
            elseif ($age < 35) $ageGroups['25–34']++;
            elseif ($age < 45) $ageGroups['35–44']++;
            elseif ($age < 55) $ageGroups['45–54']++;
            else $ageGroups['55+']++;
        }

        // Pass all stats to the dashboard view
        return view('home', compact(
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
            'ageGroups'
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

        return redirect()->route('dashboard')->withSuccess(__('Cache berhasil dibersihkan.'));
    }
}
