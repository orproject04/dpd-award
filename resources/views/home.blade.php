<x-volt-app title="Dashboard">
    @php
        $themeColor = config('laravolt.ui.color');
        $themeColorRgba = hexToRgba($themeColor, 0.9);
        $themeColorLight = hexToRgba($themeColor, 0.12);

        // Map status names to Fomantic UI label colors
        $statusLabels = [
            'Diajukan' => 'blue',
            'Lolos Verifikasi Berkas' => 'orange',
            'Lolos Pengumuman 50 Besar' => 'yellow',
            'Lolos Pengumuman 10 Besar' => 'yellow',
            'Lolos Pengumuman 5 Besar' => 'yellow',
            'Lolos Tahap Wawancara' => 'purple',
            'Lolos Tahap Final' => 'teal',
            'Tidak Lolos' => 'red',
        ];

        // Kategori icon map
        $kategoriIcons = [
            'Pendidikan' => ['icon' => 'graduation cap', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
            'Kesehatan' => ['icon' => 'heartbeat', 'color' => '#ec4899', 'bg' => '#fdf2f8'],
            'Pangan' => ['icon' => 'leaf', 'color' => '#10b981', 'bg' => '#ecfdf5'],
            'Budaya' => ['icon' => 'theater masks', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
        ];

        // Default avatar source
        $defaultAvatarSrc = '';
        $defaultPath = resource_path('images/avatar.png');
        if (file_exists($defaultPath) && is_file($defaultPath)) {
            $type = pathinfo($defaultPath, PATHINFO_EXTENSION);
            $fileData = file_get_contents($defaultPath);
            $defaultAvatarSrc = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
        }
    @endphp

    {{-- Fonts & Chart.js --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --font-head: 'Outfit', sans-serif;
            --accent: #00b5ad;
            --accent-soft: rgba(0, 181, 173, .12);
            --surface: #ffffff;
            --bg: #f4f6fb;
            --border: rgba(0, 0, 0, .07);
            --text-main: #1a202c;
            --text-muted: #718096;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-card: 0 2px 20px -4px rgba(0, 0, 0, .08), 0 1px 4px rgba(0, 0, 0, .04);
            --shadow-hover: 0 8px 32px -6px rgba(0, 0, 0, .14);
            --transition: all .25s cubic-bezier(.4, 0, .2, 1);
        }

        .db {
            font-family: var(--font-body);
            color: var(--text-main);
        }

        .db-head {
            font-family: var(--font-head);
        }

        .card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-hover);
        }

        .hero-banner {
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, #0f2027 0%, #203a43 45%, #2c5364 100%);
            position: relative;
            overflow: hidden;
            padding: 36px 40px;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 181, 173, .25) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-orb {
            position: absolute;
            bottom: -60px;
            left: 30%;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, .04) 0%, transparent 70%);
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            border: 1px solid var(--border);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            background: var(--stat-accent, var(--accent));
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: var(--transition);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(8deg);
        }

        .stat-number {
            font-family: var(--font-head);
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1px;
        }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: var(--text-muted);
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 20px;
        }

        .sec-title {
            font-family: var(--font-head);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .sec-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .funnel-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .funnel-item:hover {
            background: #f7f8fc;
        }

        .funnel-bar-wrap {
            flex: 1;
            height: 8px;
            background: #f0f2f7;
            border-radius: 20px;
            overflow: hidden;
        }

        .funnel-bar {
            height: 100%;
            border-radius: 20px;
            transition: width .8s cubic-bezier(.4, 0, .2, 1);
        }

        .funnel-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #f4f5f8;
            transition: var(--transition);
        }

        .modern-table tbody tr:last-child {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background: #f9fafb;
        }

        .modern-table tbody td {
            padding: 13px 14px;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .avatar-initials {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            flex-shrink: 0;
            letter-spacing: .5px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .ring-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .ring-wrap svg {
            transform: rotate(-90deg);
        }

        .ring-val {
            position: absolute;
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--text-main);
        }

        .tab-group {
            display: flex;
            gap: 2px;
            background: #f0f2f7;
            padding: 4px;
            border-radius: var(--radius-sm);
        }

        .tab-btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-muted);
            border: none;
            background: none;
        }

        .tab-btn.active {
            background: var(--surface);
            color: var(--text-main);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
        }

        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            text-decoration: none !important;
            border: 1px solid var(--border);
            transition: var(--transition);
            color: var(--text-main) !important;
        }

        .quick-action:hover {
            border-color: var(--accent);
            background: var(--accent-soft);
            transform: translateX(3px);
        }

        .quick-action-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .finalist-card {
            padding: 14px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--transition);
        }

        .finalist-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(0, 181, 173, .12);
        }

        .scroll-thin::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .scroll-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scroll-thin::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .scroll-thin::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            color: rgba(255, 255, 255, .85);
            font-weight: 500;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-in {
            animation: slideUp .5s cubic-bezier(.4, 0, .2, 1) both;
        }

        .anim-delay-1 {
            animation-delay: .08s;
        }

        .anim-delay-2 {
            animation-delay: .16s;
        }

        .anim-delay-3 {
            animation-delay: .24s;
        }

        .anim-delay-4 {
            animation-delay: .32s;
        }

        .anim-delay-5 {
            animation-delay: .40s;
        }
    </style>

    <div class="db -mt-4">

        {{-- ═══════════════ HERO BANNER ═══════════════ --}}
        <div class="hero-banner mb-8 anim-in">
            <div class="hero-orb"></div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5"
                style="position:relative;z-index:1">
                <div>
                    <div class="date-chip mb-4">
                        <i class="calendar alternate icon"></i>
                        {{ Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>
                    <h1 class="db-head"
                        style="font-size:2rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-0.5px;">
                        Selamat datang, <span style="color:#4dd9d5;">{{ auth()->user()->name }}</span> 👋
                    </h1>
                    <p style="color:rgba(255,255,255,.65);margin-top:8px;font-size:14px;">
                        Berikut ringkasan terkini program <strong style="color:rgba(255,255,255,.9)">DPDRI
                            <i>AWARDS</i></strong>.
                        Pantau seluruh progres seleksi dari satu tempat.
                    </p>
                </div>
                <div class="flex gap-4 flex-shrink-0">
                    <div
                        style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:16px 22px;text-align:center;min-width:90px;">
                        <div class="db-head" style="font-size:1.6rem;font-weight:800;color:#fff;">{{ $newToday }}
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,.6);font-weight:600;margin-top:2px;">Pendaftar
                            hari ini
                        </div>
                    </div>
                    <div
                        style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:16px 22px;text-align:center;min-width:90px;">
                        <div class="db-head" style="font-size:1.6rem;font-weight:800;color:#fff;">{{ $newThisWeek }}
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,.6);font-weight:600;margin-top:2px;">Pendaftar
                            minggu ini</div>
                    </div>
                    <div
                        style="background:rgba(0,181,173,.2);border:1px solid rgba(0,181,173,.4);border-radius:14px;padding:16px 22px;text-align:center;min-width:90px;">
                        @php
                            $reviewedCount = $totalPendaftar - $pendingCount;
                            $reviewRate = $totalPendaftar > 0 ? round(($reviewedCount / $totalPendaftar) * 100, 1) : 0;
                        @endphp
                        <div class="db-head" style="font-size:1.6rem;font-weight:800;color:#4dd9d5;">
                            {{ $reviewRate }}%</div>
                        <div style="font-size:11px;color:rgba(255,255,255,.6);font-weight:600;margin-top:2px;">Ditinjau
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ KPI CARDS (5) ═══════════════ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
            <div class="stat-card anim-in anim-delay-1" style="--stat-accent:#00b5ad;">
                <div class="flex items-start justify-between">
                    <div class="stat-icon" style="background:#e6faf9;color:#00b5ad;"><i class="users icon"></i></div>
                    <span class="stat-badge" style="background:#e6faf9;color:#00b5ad;"><i class="arrow up icon"
                            style="font-size:9px;"></i> Aktif</span>
                </div>
                <div>
                    <div class="stat-number" id="cnt-total">{{ number_format($totalPendaftar) }}</div>
                    <div class="stat-label mt-1">Total Pendaftar</div>
                </div>
            </div>

            <div class="stat-card anim-in anim-delay-2" style="--stat-accent:#3b82f6;">
                <div class="flex items-start justify-between">
                    <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="graduation cap icon"></i>
                    </div>
                    <span class="stat-badge" style="background:#eff6ff;color:#3b82f6;"><i class="chart pie icon"
                            style="font-size:9px;"></i>
                        {{ $totalPendaftar > 0 ? round((($kategoriCounts['Bidang Pendidikan'] ?? 0) / $totalPendaftar) * 100) : 0 }}%</span>
                </div>
                <div>
                    <div class="stat-number" style="color:#3b82f6;" id="cnt-pendidikan">
                        {{ number_format($kategoriCounts['Bidang Pendidikan'] ?? 0) }}
                    </div>
                    <div class="stat-label mt-1">Bidang Pendidikan</div>
                </div>
            </div>

            <div class="stat-card anim-in anim-delay-3" style="--stat-accent:#ec4899;">
                <div class="flex items-start justify-between">
                    <div class="stat-icon" style="background:#fdf2f8;color:#ec4899;"><i class="heartbeat icon"></i>
                    </div>
                    <span class="stat-badge" style="background:#fdf2f8;color:#ec4899;"><i class="chart pie icon"
                            style="font-size:9px;"></i>
                        {{ $totalPendaftar > 0 ? round((($kategoriCounts['Bidang Kesehatan'] ?? 0) / $totalPendaftar) * 100) : 0 }}%</span>
                </div>
                <div>
                    <div class="stat-number" style="color:#ec4899;" id="cnt-kesehatan">
                        {{ number_format($kategoriCounts['Bidang Kesehatan'] ?? 0) }}</div>
                    <div class="stat-label mt-1">Bidang Kesehatan</div>
                </div>
            </div>

            <div class="stat-card anim-in anim-delay-4" style="--stat-accent:#10b981;">
                <div class="flex items-start justify-between">
                    <div class="stat-icon" style="background:#ecfdf5;color:#10b981;"><i class="leaf icon"></i>
                    </div>
                    <span class="stat-badge" style="background:#ecfdf5;color:#10b981;"><i class="chart pie icon"
                            style="font-size:9px;"></i>
                        {{ $totalPendaftar > 0 ? round((($kategoriCounts['Bidang Ketahanan Pangan'] ?? 0) / $totalPendaftar) * 100) : 0 }}%</span>
                </div>
                <div>
                    <div class="stat-number" style="color:#10b981;" id="cnt-pangan">
                        {{ number_format($kategoriCounts['Bidang Ketahanan Pangan'] ?? 0) }}</div>
                    <div class="stat-label mt-1">Bidang Ketahanan Pangan</div>
                </div>
            </div>

            <div class="stat-card anim-in anim-delay-5" style="--stat-accent:#f59e0b;">
                <div class="flex items-start justify-between">
                    <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;"><i
                            class="theater masks icon"></i>
                    </div>
                    <span class="stat-badge" style="background:#fffbeb;color:#f59e0b;"><i class="chart pie icon"
                            style="font-size:9px;"></i>
                        {{ $totalPendaftar > 0 ? round((($kategoriCounts['Bidang Seni dan Budaya'] ?? 0) / $totalPendaftar) * 100) : 0 }}%</span>
                </div>
                <div>
                    <div class="stat-number" style="color:#f59e0b;" id="cnt-budaya">
                        {{ number_format($kategoriCounts['Bidang Seni dan Budaya'] ?? 0) }}</div>
                    <div class="stat-label mt-1">Bidang Seni dan Budaya</div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ ROW 2: Trend + Conversion Ring ═══════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-6">
            <div class="card p-3 lg:col-span-2 anim-in anim-delay-2">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <div class="sec-title">Tren Pendaftaran Harian</div>
                        <div class="sec-sub">Jumlah pendaftar baru 30 hari terakhir</div>
                    </div>
                    <span
                        style="background:#f0f2f7;color:#718096;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;">30
                        Hari</span>
                </div>
                <div style="position:relative;height:240px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="card p-3 anim-in anim-delay-3">
                <div class="sec-title">Progres Review Berkas</div>
                <div class="sec-sub mb-5">Persentase berkas yang telah ditinjau</div>
                <div class="flex flex-col items-center gap-4">
                    @php
                        $reviewedCount = $totalPendaftar - $pendingCount;
                        $reviewRate = $totalPendaftar > 0 ? round(($reviewedCount / $totalPendaftar) * 100, 1) : 0;
                        $r = 68;
                        $circ = 2 * pi() * $r;
                        $dash = ($reviewRate / 100) * $circ;
                        $gap = $circ - $dash;
                    @endphp
                    <div class="ring-wrap">
                        <svg width="180" height="180" viewBox="0 0 180 180">
                            <circle cx="90" cy="90" r="{{ $r }}" fill="none"
                                stroke="#f0f2f7" stroke-width="18" />
                            <circle cx="90" cy="90" r="{{ $r }}" fill="none"
                                stroke="#00b5ad" stroke-width="18"
                                stroke-dasharray="{{ round($dash, 2) }} {{ round($gap, 2) }}"
                                stroke-linecap="round" />
                        </svg>
                        <div class="ring-val">{{ $reviewRate }}%</div>
                    </div>
                    <div class="w-full space-y-3" style="font-size:13px;">
                        <div class="flex justify-between items-center">
                            <span style="color:var(--text-muted)"><i class="folder open icon"
                                    style="color:#3b82f6;margin-right:6px;"></i>Total Berkas</span>
                            <strong>{{ number_format($totalPendaftar) }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:var(--text-muted)"><i class="check circle icon"
                                    style="color:#059669;margin-right:6px;"></i>Sudah Ditinjau</span>
                            <strong style="color:#059669;">{{ number_format($reviewedCount) }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:var(--text-muted)"><i class="clock icon"
                                    style="color:#f59e0b;margin-right:6px;"></i>Belum Ditinjau</span>
                            <strong style="color:#d97706;">{{ number_format($pendingCount) }}</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color:var(--text-muted)"><i class="trophy icon"
                                    style="color:#8b5cf6;margin-right:6px;"></i>Lolos Final</span>
                            <strong style="color:#8b5cf6;">{{ number_format($finalistCount) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ ROW 3: Category + Funnel + Demographics ═══════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-6">

            {{-- Category Doughnut --}}
            <div class="card p-3 anim-in anim-delay-1">
                <div class="sec-title">Distribusi Kategori</div>
                <div class="sec-sub mb-4">Proporsi peserta per bidang penghargaan</div>
                <div style="position:relative;height:200px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @php
                        $catColors = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'];
                        $ci = 0;
                    @endphp
                    @foreach ($kategoriCounts as $katName => $katCount)
                        @php $pct = $totalPendaftar > 0 ? round(($katCount / $totalPendaftar) * 100, 1) : 0; @endphp
                        <div class="flex items-center justify-between" style="font-size:12.5px;">
                            <div class="flex items-center gap-2">
                                <div
                                    style="width:10px;height:10px;border-radius:3px;background:{{ $catColors[$ci % 5] }};flex-shrink:0;">
                                </div>
                                <span
                                    style="color:var(--text-muted);">{{ Str::limit(str_replace('Bidang ', '', $katName), 20) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span style="font-weight:700;">{{ $katCount }}</span>
                                <span style="color:var(--text-muted);">({{ $pct }}%)</span>
                            </div>
                        </div>
                        @php $ci++; @endphp
                    @endforeach
                    @if (empty($kategoriCounts))
                        <p style="font-size:12px;color:var(--text-muted);text-align:center;">Belum ada data</p>
                    @endif
                </div>
            </div>

            {{-- Tingkat Pendidikan --}}
            <div class="card p-3 anim-in anim-delay-2">
                <div class="sec-title">Tingkat Pendidikan</div>
                <div class="sec-sub mb-4">Profil jenjang pendidikan peserta</div>
                @php
                    $eduOrder = [
                        'SMA/Sederajat',
                        'Diploma I',
                        'Diploma II',
                        'Diploma III',
                        'Diploma IV',
                        'Sarjana (S1)',
                        'Magister (S2)',
                        'Doktor (S3)',
                    ];
                    $maxEdu = max(array_merge([1], array_values($pendidikanCounts)));
                @endphp
                <div class="space-y-1">
                    @foreach ($eduOrder as $edu)
                        @php $eduVal = $pendidikanCounts[$edu] ?? 0; @endphp
                        @if ($eduVal >= 0)
                            <div class="flex items-center gap-2 mb-2" style="font-size:11.5px;">
                                <span
                                    style="min-width:100px;color:var(--text-muted);white-space:nowrap;">{{ $edu }}</span>
                                <div style="flex:1;height:6px;background:#f0f2f7;border-radius:6px;overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ round(($eduVal / $maxEdu) * 100) }}%;background:#6366f1;border-radius:6px;transition:width .8s;">
                                    </div>
                                </div>
                                <span
                                    style="min-width:18px;text-align:right;font-weight:700;">{{ $eduVal }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Demographics: Gender + Age --}}
            <div class="card p-3 anim-in anim-delay-3">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="sec-title">Demografi Pendaftar</div>
                        <div class="sec-sub">Profil umum peserta</div>
                    </div>
                    <div class="tab-group">
                        <button class="tab-btn active" onclick="switchDemoTab('gender', this)">Gender</button>
                        <button class="tab-btn" onclick="switchDemoTab('age', this)">Usia</button>
                    </div>
                </div>

                <div id="tab-gender">
                    <div
                        style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
                        <canvas id="genderChart"></canvas>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        @php $gColors = ['Laki-laki' => '#60a5fa', 'Perempuan' => '#f472b6']; @endphp
                        @foreach ($genderCounts as $g => $gc)
                            @php $gPct = $totalPendaftar > 0 ? round(($gc / $totalPendaftar) * 100, 1) : 0; @endphp
                            <div style="background:#f9fafb;border-radius:10px;padding:10px 12px;text-align:center;">
                                <div
                                    style="width:12px;height:12px;border-radius:3px;background:{{ $gColors[$g] ?? '#94a3b8' }};margin:0 auto 4px;">
                                </div>
                                <div style="font-weight:800;font-size:1.1rem;font-family:var(--font-head);">
                                    {{ $gc }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $g }}
                                    ({{ $gPct }}%)
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="tab-age" style="display:none;">
                    <div style="position:relative;height:180px;">
                        <canvas id="ageChart"></canvas>
                    </div>
                    <div class="mt-3 space-y-1">
                        @foreach ($ageGroups as $ageKey => $ageVal)
                            @php $agePct = $totalPendaftar > 0 ? round(($ageVal / max(1, $totalPendaftar)) * 100, 1) : 0; @endphp
                            <div class="flex items-center gap-2" style="font-size:12px;">
                                <span style="min-width:48px;color:var(--text-muted);">{{ $ageKey }}</span>
                                <div style="flex:1;height:6px;background:#f0f2f7;border-radius:10px;overflow:hidden;">
                                    <div
                                        style="height:100%;width:{{ $agePct }}%;background:#6366f1;border-radius:10px;">
                                    </div>
                                </div>
                                <span
                                    style="min-width:24px;text-align:right;font-weight:700;">{{ $ageVal }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ ROW 4: Status Chart + Quick Actions ═══════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-6">
            <div class="card p-3 lg:col-span-3 anim-in anim-delay-1">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="sec-title">Status Seleksi Peserta</div>
                        <div class="sec-sub">Distribusi peserta di setiap tahap penilaian</div>
                    </div>
                </div>
                <div style="position:relative;height:260px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="card p-3 anim-in anim-delay-2">
                <div class="sec-title mb-4">Aksi Cepat</div>
                <div class="space-y-3">
                    <a href="{{ route('modules::pendaftar.index') }}" class="quick-action">
                        <div class="quick-action-icon" style="background:#e6faf9;color:#00b5ad;"><i
                                class="list ul icon"></i></div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Daftar Pendaftar</div>
                            <div style="font-size:11px;color:var(--text-muted);">Kelola semua peserta</div>
                        </div>
                    </a>
                    <a href="{{ route('modules::pendaftar.index') }}" class="quick-action">
                        <div class="quick-action-icon" style="background:#fffbeb;color:#d97706;"><i
                                class="clipboard check icon"></i></div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Verifikasi Berkas</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $pendingCount }} menunggu</div>
                        </div>
                    </a>
                    <a href="{{ route('modules::pendaftar.index') }}" class="quick-action">
                        <div class="quick-action-icon" style="background:#ecfdf5;color:#059669;"><i
                                class="trophy icon"></i></div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">Finalis</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $finalistCount }} kandidat</div>
                        </div>
                    </a>
                    <button onclick="window.location.reload()" class="quick-action"
                        style="cursor:pointer;border:none;background:none;width:100%;font-family:inherit;text-align:left;">
                        <div class="quick-action-icon" style="background:#eef2ff;color:#4f46e5;"><i
                                class="sync alternate icon"></i></div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--text-main);">Refresh Data</div>
                            <div style="font-size:11px;color:var(--text-muted);">Perbarui semua statistik</div>
                        </div>
                    </button>
                    @if (auth()->user()->hasPermission('*'))
                        <form action="{{ route('clear-cache') }}" method="POST" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" class="quick-action"
                                style="cursor:pointer;border:none;background:none;width:100%;font-family:inherit;text-align:left;">
                                <div class="quick-action-icon" style="background:#fef2f2;color:#dc2626;"><i
                                        class="trash alternate icon"></i></div>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:var(--text-main);">Clear Cache
                                    </div>
                                    <div style="font-size:11px;color:var(--text-muted);">Bersihkan cache aplikasi</div>
                                </div>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════════════ ROW 5: Recent Table ═══════════════ --}}
        <div class="mb-6">
            <div class="card p-3 anim-in anim-delay-1">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <div class="sec-title">Pendaftar Terbaru</div>
                        <div class="sec-sub">10 peserta terakhir yang mengirimkan berkas</div>
                    </div>
                    <a href="{{ route('modules::pendaftar.index') }}"
                        style="font-size:12px;font-weight:700;color:#00b5ad;display:flex;align-items:center;gap:4px;text-decoration:none;">
                        Lihat Semua <i class="arrow right icon"></i>
                    </a>
                </div>
                <div class="overflow-x-auto scroll-thin">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Peserta</th>
                                <th>Kategori</th>
                                <th>No. Registrasi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPendaftar as $item)
                                @php
                                    $pillStyle = match ($item->status) {
                                        'Diajukan' => 'background:#eff6ff;color:#1d4ed8;',
                                        'Tidak Lolos' => 'background:#fef2f2;color:#dc2626;',
                                        'Lolos Tahap Final' => 'background:#ecfdf5;color:#059669;',
                                        'Lolos Tahap Wawancara' => 'background:#f5f3ff;color:#7c3aed;',
                                        default => 'background:#fffbeb;color:#d97706;',
                                    };
                                    $avatarPalette = ['#00b5ad', '#3b82f6', '#8b5cf6', '#ec4899', '#f97316'];
                                    $avatarBg = $avatarPalette[ord($item->nama[0]) % 5];
                                    $katKey = collect($kategoriIcons)
                                        ->keys()
                                        ->first(fn($k) => str_contains($item->kategori, $k));
                                    $katMeta = $katKey ? $kategoriIcons[$katKey] : null;

                                    $itemPath = $item->getFotoAttribute();
                                    $itemAvatarSrc = $defaultAvatarSrc;
                                    if (!empty($itemPath) && file_exists($itemPath) && is_file($itemPath)) {
                                        $type = pathinfo($itemPath, PATHINFO_EXTENSION);
                                        $fileData = file_get_contents($itemPath);
                                        $itemAvatarSrc = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <img src="{{ $itemAvatarSrc }}" alt="{{ $item->nama }}"
                                                style="width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                                            <div>
                                                <div style="font-weight:700;font-size:13px;">{{ $item->nama }}</div>
                                                <div style="font-size:11px;color:var(--text-muted);">
                                                    {{ $item->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($katMeta)
                                            <span
                                                style="color:{{ $katMeta['color'] }};font-weight:600;font-size:13px;">
                                                <i class="{{ $katMeta['icon'] }} icon"
                                                    style="margin-right:4px;"></i>{{ $katKey }}
                                            </span>
                                        @else
                                            <span
                                                style="color:var(--text-muted);font-size:13px;">{{ $item->kategori }}</span>
                                        @endif
                                    </td>
                                    <td style="font-family:monospace;font-size:12px;color:var(--text-muted);">
                                        {{ $item->nomor_registrasi }}</td>
                                    <td style="font-size:12px;color:var(--text-muted);white-space:nowrap;">
                                        {{ $item->created_at->format('d M Y') }}</td>
                                    <td><span class="status-pill"
                                            style="{{ $pillStyle }}">{{ $item->status }}</span></td>
                                    <td style="text-align:center;">
                                        <a href="{{ route('modules::pendaftar.show', $item->id) }}"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#f0f2f7;color:#4a5568;transition:all .2s;text-decoration:none;"
                                            onmouseover="this.style.background='#e6faf9';this.style.color='#00b5ad';"
                                            onmouseout="this.style.background='#f0f2f7';this.style.color='#4a5568';">
                                            <i class="eye icon" style="font-size:13px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="inbox icon"
                                            style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4;"></i>
                                        Belum ada pendaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end .db --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.color = '#94a3b8';

            // ── 1. Trend Line Chart ──────────────────────────────────
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const grad = trendCtx.createLinearGradient(0, 0, 0, 240);
            grad.addColorStop(0, 'rgba(0,181,173,.25)');
            grad.addColorStop(1, 'rgba(0,181,173,.0)');

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [{
                        label: 'Pendaftaran Baru',
                        data: {!! json_encode($trendValues) !!},
                        borderColor: '#00b5ad',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#00b5ad',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        fill: true,
                        backgroundColor: grad,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1a202c',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: {
                                weight: '700'
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.05)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxTicksLimit: 8,
                                callback: function(val, idx) {
                                    return idx % 5 === 0 ? this.getLabelForValue(val) : '';
                                }
                            }
                        }
                    }
                }
            });

            // ── 2. Category Doughnut ──────────────────────────────────
            const rawCat = {!! json_encode($kategoriCounts) !!};
            let catLabels = [],
                catValues = [];
            for (const [k, v] of Object.entries(rawCat)) {
                catLabels.push(k.replace('Bidang ', ''));
                catValues.push(v);
            }
            if (!catLabels.length) {
                catLabels = ['Pendidikan', 'Kesehatan', 'Pangan', 'Budaya'];
                catValues = [0, 0, 0, 0];
            }

            new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catValues,
                        backgroundColor: ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '50%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ── 3. Gender Doughnut ────────────────────────────────────
            const rawG = {!! json_encode($genderCounts) !!};
            const gLabels = Object.keys(rawG).length ? Object.keys(rawG) : ['Laki-laki', 'Perempuan'];
            const gVals = Object.keys(rawG).length ? Object.values(rawG) : [0, 0];
            new Chart(document.getElementById('genderChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: gLabels,
                    datasets: [{
                        data: gVals,
                        backgroundColor: ['#60a5fa', '#f472b6'],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // ── 4. Age Bar ────────────────────────────────────────────
            const ageData = {!! json_encode($ageGroups) !!};
            new Chart(document.getElementById('ageChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(ageData),
                    datasets: [{
                        data: Object.values(ageData),
                        backgroundColor: ['#818cf8', '#6366f1', '#4f46e5', '#4338ca', '#3730a3'],
                        borderRadius: 6,
                        barThickness: 28
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ── 5. Status Bar Chart ───────────────────────────────────
            const rawStatus = {!! json_encode($statusCounts) !!};
            const stages = [
                'Diajukan', 'Lolos Verifikasi Berkas', 'Lolos Pengumuman 50 Besar',
                'Lolos Pengumuman 10 Besar', 'Lolos Pengumuman 5 Besar',
                'Lolos Tahap Wawancara', 'Lolos Tahap Final', 'Tidak Lolos'
            ];
            const stageBg = stages.map(s => {
                if (s === 'Tidak Lolos') return '#ef4444';
                if (s === 'Lolos Tahap Final') return '#10b981';
                if (s === 'Diajukan') return '#3b82f6';
                if (s.includes('Wawancara')) return '#8b5cf6';
                return '#f59e0b';
            });
            new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: stages.map(s => s.replace('Lolos ', '').replace('Tahap ', '')),
                    datasets: [{
                        label: 'Jumlah Peserta',
                        data: stages.map(s => rawStatus[s] ?? 0),
                        backgroundColor: stageBg,
                        borderRadius: 8,
                        barThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1a202c',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: {
                                weight: '700'
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(0,0,0,.04)'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

        }); // end DOMContentLoaded

        // ── Tab switcher ──────────────────────────────────────────
        function switchDemoTab(name, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-gender').style.display = name === 'gender' ? '' : 'none';
            document.getElementById('tab-age').style.display = name === 'age' ? '' : 'none';
        }
    </script>
</x-volt-app>
