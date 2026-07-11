<x-volt-app :title="'Detail Pendaftar'">
    <x-volt-backlink url="{{ route('modules::pendaftar.index') }}"/>

    @php
        $statuses = [
            'Diajukan',
            'Lolos Verifikasi Berkas',
            'Lolos Penilaian Tahap 1',
            'Lolos Penilaian Tahap 2',
            'Lolos Penilaian Tahap 3',
            'Lolos Tahap Wawancara',
            'Lolos Tahap Final',
            'Tidak Lolos'
        ];

        $statusColor = match ($pendaftar->status) {
            'Diajukan'               => 'blue',
            'Lolos Verifikasi Berkas'=> 'yellow',
            'Lolos Penilaian Tahap 1'=> 'yellow',
            'Lolos Penilaian Tahap 2'=> 'yellow',
            'Lolos Penilaian Tahap 3'=> 'yellow',
            'Lolos Tahap Wawancara'  => 'yellow',
            'Lolos Tahap Final'      => 'teal',
            'Tidak Lolos'            => 'red',
            default                  => 'grey'
        };

        $isImage = function($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        };

        $isPdf = function($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return $ext === 'pdf';
        };

        $fotoRaw = $pendaftar->getRawOriginal('foto');
        $ktpRaw  = $pendaftar->getRawOriginal('ktp');

        $themeColor = hexToRgba(config('laravolt.ui.color'), 0.9);
        $themeColorLight = hexToRgba(config('laravolt.ui.color'), 0.1);

        // Process WhatsApp Link
        $waNumber = preg_replace('/[^0-9]/', '', $pendaftar->nomor_wa);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
    @endphp

    @push('style')
    <style>
        /* ─── Page Chrome ─────────────────────────────────────── */
        .show-page {
            --accent: {{ $themeColor }};
            --accent-hover: {{ hexToRgba(config('laravolt.ui.color'), 1.0) }};
            --accent-light: {{ $themeColorLight }};
            --radius: 10px;
        }

        /* ─── Section Cards ─────────────────────────────────────── */
        .show-card {
            background: #fff;
            border: 1px solid #e8edf2;
            border-radius: var(--radius);
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            margin-bottom: 1.5rem;
        }
        .show-card-header {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            border-bottom: 1px solid #e8edf2;
            border-top-left-radius: calc(var(--radius) - 1px);
            border-top-right-radius: calc(var(--radius) - 1px);
        }
        .show-card-header .card-icon {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            background: var(--accent); border-radius: 8px; color: #fff;
            font-size: 1rem; flex-shrink: 0;
        }
        .show-card-header h3 {
            margin: 0; font-size: 1.1rem; font-weight: 700; color: #1a2035;
        }
        .show-card-body { padding: 1.25rem; }

        /* ─── Profile Table ───────────────────────────────────── */
        .profile-table { width: 100%; border-collapse: collapse; }
        .profile-table tr { border-bottom: 1px solid #f1f5f9; }
        .profile-table tr:last-child { border-bottom: none; }
        .profile-table td { padding: .65rem .75rem; font-size: 1rem; vertical-align: top; }
        .profile-table td:first-child {
            width: 38%; color: #64748b; font-weight: 500; white-space: nowrap;
        }
        .profile-table td:last-child { color: #1a2035; font-weight: 600; }

        /* ─── File Attachment Block ──────────────────────────── */
        .file-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .file-block:last-child { margin-bottom: 0; }
        .file-block-label {
            font-size: .9rem; font-weight: 700; color: #64748b;
            letter-spacing: .06em; margin-bottom: .65rem;
        }
        .file-img-preview {
            width: 100%; border-radius: 6px; max-height: 200px;
            object-fit: contain; background: #f0f4f8;
            border: 1px solid #e2e8f0;
            cursor: zoom-in;
            transition: transform .2s, box-shadow .2s;
        }
        .file-img-preview:hover { transform: scale(1.02); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

        /* ─── Custom Premium Dropdown ────────────────────────── */
        .custom-dropdown {
            position: relative;
            width: 100%;
            margin-bottom: 1rem;
        }
        .custom-dropdown-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.75rem 1rem;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 01rem;
            font-weight: 600;
            color: #1a2035;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .custom-dropdown-trigger:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .custom-dropdown.active .custom-dropdown-trigger {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .custom-dropdown-trigger .chevron.icon {
            font-size: 0.8rem;
            color: #64748b;
            transition: transform 0.2s ease;
            margin: 0;
        }
        .custom-dropdown.active .custom-dropdown-trigger .chevron.icon {
            transform: rotate(180deg);
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.75rem;
            flex-shrink: 0;
            vertical-align: middle;
        }
        .status-dot.blue { background-color: #3b82f6; box-shadow: 0 0 6px rgba(59,130,246,0.6); }
        .status-dot.yellow { background-color: #eab308; box-shadow: 0 0 6px rgba(234,179,8,0.6); }
        .status-dot.teal { background-color: #14b8a6; box-shadow: 0 0 6px rgba(20,184,166,0.6); }
        .status-dot.red { background-color: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.6); }
        .status-dot.grey { background-color: #64748b; box-shadow: 0 0 6px rgba(100,116,139,0.6); }

        .custom-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            z-index: 50;
            max-height: 260px;
            overflow-y: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .custom-dropdown.active .custom-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .custom-dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            font-size: 01rem;
            color: #334155;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .custom-dropdown-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .custom-dropdown-item.active {
            background-color: #f8fafc;
            color: var(--accent);
            font-weight: 600;
        }
        .custom-dropdown-item.active .status-dot {
            transform: scale(1.2);
        }

        .custom-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        .custom-dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-dropdown-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ─── Accordion ──────────────────────────────────────── */
        .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: .75rem;
            overflow: hidden;
        }
        .accordion-item:last-child { margin-bottom: 0; }
        .accordion-trigger {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; padding: .85rem 1rem;
            background: #f8fafc; border: none; cursor: pointer;
            font-size: .92rem; font-weight: 700; color: #1a2035;
            text-align: left; transition: background .15s;
        }
        .accordion-trigger:hover { background: var(--accent-light); }
        .accordion-trigger .acc-chevron {
            width: 20px; height: 20px; flex-shrink: 0;
            transition: transform .25s ease;
            color: #94a3b8;
        }
        .accordion-trigger.open .acc-chevron { transform: rotate(180deg); }

        .accordion-content {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.3s ease-out;
            background: #fff;
        }
        .accordion-content.open {
            grid-template-rows: 1fr;
        }
        .accordion-content-inner {
            overflow: hidden;
            padding: 0 1rem;
            transition: padding 0.3s ease-out;
        }
        .accordion-content.open .accordion-content-inner {
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        /* ─── Kontribusi / Penghargaan Field Grid ────────────── */
        .detail-field { margin-bottom: 1rem; }
        .detail-field:last-child { margin-bottom: 0; }
        .detail-field-label {
            font-size: 0.9rem; font-weight: 700; color: #64748b;
            letter-spacing: .06em; margin-bottom: .3rem;
        }
        .detail-field-value {
            font-size: 1rem; color: #1a2035; white-space: pre-line;
            line-height: 1.6;
        }
        .detail-field-divider { border: none; border-top: 1px solid #f1f5f9; margin: 1rem 0; }

        /* ─── Bukti Dukung inside accordion ─────────────────── */
        .bukti-img {
            width: 100%; border-radius: 6px; max-height: 220px;
            object-fit: contain; background: #f8fafc;
            border: 1px solid #e2e8f0;
            cursor: zoom-in;
            transition: transform .2s, box-shadow .2s;
            display: block;
        }
        .bukti-img:hover { transform: scale(1.02); box-shadow: 0 4px 16px rgba(0,0,0,.15); }

        /* ─── Lightbox Overlay ─────────────────────────────── */
        #img-lightbox {
            display: none;
            position: fixed; inset: 0; z-index: 99998;
            background: rgba(0,0,0,.85);
            backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
            cursor: zoom-out;
        }
        #img-lightbox.active { display: flex; }
        #img-lightbox img {
            max-width: 90vw; max-height: 90vh;
            border-radius: 8px; box-shadow: 0 8px 40px rgba(0,0,0,.6);
            object-fit: contain;
        }
        #img-lightbox-close {
            position: absolute; top: 1.25rem; right: 1.5rem;
            color: #fff; font-size: 2rem; cursor: pointer; line-height: 1;
            background: rgba(255,255,255,.1); border-radius: 50%;
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        #img-lightbox-close:hover { background: rgba(255,255,255,.25); }

        /* ─── Section divider ─────────────────────────────── */
        .show-divider {
            border: none; border-top: 1px solid #e2e8f0;
            margin: 1rem 0;
        }
    </style>
    @endpush

    <div class="show-page">
        <div class="ui grid stackable" style="margin-top: .5rem;">

            {{-- ═══════════════════════ KOLOM KIRI ═══════════════════════ --}}
            <div class="eleven wide column">

                {{-- ── Informasi Profil ─────────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="user icon" style="margin:0"></i></div>
                        <h3>Informasi Profil Pendaftar</h3>
                    </div>
                    <div class="show-card-body" style="padding: 0;">
                        <table class="profile-table">
                            <tr><td>Nomor Registrasi</td><td>{{ $pendaftar->nomor_registrasi }}</td></tr>
                            <tr><td>Kategori</td><td>{{ $pendaftar->kategori }}</td></tr>
                            <tr><td>Nama Lengkap</td><td>{{ $pendaftar->nama }}</td></tr>
                            <tr><td>Tempat, Tanggal Lahir</td><td>{{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td></tr>
                            <tr><td>Jenis Kelamin</td><td>{{ $pendaftar->jenis_kelamin }}</td></tr>
                            <tr><td>Pendidikan</td><td>{{ $pendaftar->pendidikan }}</td></tr>
                            <tr><td>Alamat Lengkap</td><td>{{ $pendaftar->alamat }}</td></tr>
                            <tr>
                                <td>Nomor WhatsApp</td>
                                <td>
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" style="color: var(--accent); font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <i class="whatsapp icon"></i> {{ $pendaftar->nomor_wa }}
                                    </a>
                                </td>
                            </tr>
                            <tr><td>Alamat Email</td><td>{{ $pendaftar->email }}</td></tr>
                            <tr><td>Tanggal Registrasi</td><td>{{ $pendaftar->created_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB</td></tr>
                        </table>
                    </div>
                </div>

                {{-- ── Kontribusi / Inovasi ─────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="lightbulb outline icon" style="margin:0"></i></div>
                        <h3>Kontribusi / Inovasi</h3>
                    </div>
                    <div class="show-card-body">
                        @forelse($pendaftar->kontribusi as $index => $kontribusi)
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" data-acc="kontribusi-{{ $index }}">
                                    <span>
                                        <span style="color: var(--accent); margin-right:.4rem;">#{{ $index + 1 }}</span>
                                        {{ $kontribusi->judul }}
                                    </span>
                                    <svg class="acc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="accordion-content" id="kontribusi-{{ $index }}">
                                    <div class="accordion-content-inner">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Judul Inovasi / Kontribusi</div>
                                            <div class="detail-field-value" style="font-weight:700;">{{ $kontribusi->judul }}</div>
                                        </div>
                                        <hr class="detail-field-divider">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Deskripsi</div>
                                            <div class="detail-field-value">{{ $kontribusi->deskripsi }}</div>
                                        </div>
                                        <hr class="detail-field-divider">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Dampak &amp; Pencapaian</div>
                                            <div class="detail-field-value">{{ $kontribusi->dampak }}</div>
                                        </div>
                                        @if(!empty($kontribusi->bukti_dukung) && is_array($kontribusi->bukti_dukung) && count($kontribusi->bukti_dukung) > 0)
                                            <hr class="detail-field-divider">
                                            <div class="detail-field">
                                                <div class="detail-field-label">Bukti Dukung (Evidence)</div>
                                                <div style="margin-top: .5rem;">
                                                    @foreach($kontribusi->bukti_dukung as $fileIdx => $buktiFile)
                                                        @if(!empty($buktiFile))
                                                            @php $buktiFileUrl = route('modules::pendaftar.file', ['path' => $buktiFile]); @endphp
                                                            <div style="margin-bottom: 1.25rem;">
                                                                @if(count($kontribusi->bukti_dukung) > 1)
                                                                    <div style="font-size:.75rem; color:#94a3b8; margin-bottom:.3rem; font-weight:700; ">Berkas {{ $fileIdx + 1 }}</div>
                                                                @endif
                                                                @if($isImage($buktiFile))
                                                                    <img src="{{ $buktiFileUrl }}"
                                                                         class="bukti-img lightbox-trigger"
                                                                         data-src="{{ $buktiFileUrl }}"
                                                                         alt="Bukti Kontribusi {{ $index + 1 }} - {{ $fileIdx + 1 }}">
                                                                @elseif($isPdf($buktiFile))
                                                                    <object data="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}" type="application/pdf" style="width: 100%; height: 500px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                                                        <iframe src="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}" style="width: 100%; height: 500px; border: none;">
                                                                            <p>Browser Anda tidak mendukung pratinjau PDF.</p>
                                                                        </iframe>
                                                                    </object>
                                                                @endif
                                                                <x-volt-link-button url="{{ route('modules::pendaftar.file', ['path' => $buktiFile, 'download' => 1]) }}"
                                                                                     icon="download"
                                                                                     class="basic blue"
                                                                                     style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                                                     target="_blank"
                                                                                     data-no-loader="true">
                                                                    Unduh{{ count($kontribusi->bukti_dukung) > 1 ? ' Berkas ' . ($fileIdx + 1) : ' Berkas Bukti' }}
                                                                </x-volt-link-button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; padding: 2rem; color: #94a3b8;">
                                <i class="info circle icon large"></i>
                                <p style="margin-top:.5rem;">Tidak ada data kontribusi yang terdaftar.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ── Penghargaan ───────────────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="trophy icon" style="margin:0"></i></div>
                        <h3>Penghargaan</h3>
                    </div>
                    <div class="show-card-body">
                        @forelse($pendaftar->penghargaan as $index => $penghargaan)
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" data-acc="penghargaan-{{ $index }}">
                                    <span>
                                        <span style="color: var(--accent); margin-right:.4rem;">#{{ $index + 1 }}</span>
                                        {{ \Illuminate\Support\Str::limit($penghargaan->uraian, 60) }}
                                    </span>
                                    <svg class="acc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="accordion-content" id="penghargaan-{{ $index }}">
                                    <div class="accordion-content-inner">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Uraian Penghargaan</div>
                                            <div class="detail-field-value">{{ $penghargaan->uraian }}</div>
                                        </div>
                                        <hr class="detail-field-divider">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Tahun</div>
                                            <div class="detail-field-value" style="font-weight:700; font-size:1.05rem;">
                                                {{ \Carbon\Carbon::parse($penghargaan->tahun)->format('Y') }}
                                            </div>
                                        </div>
                                        @if(!empty($penghargaan->bukti_dukung) && is_array($penghargaan->bukti_dukung) && count($penghargaan->bukti_dukung) > 0)
                                            <hr class="detail-field-divider">
                                            <div class="detail-field">
                                                <div class="detail-field-label">Bukti Dukung (Evidence)</div>
                                                <div style="margin-top: .5rem;">
                                                    @foreach($penghargaan->bukti_dukung as $fileIdx => $buktiFile)
                                                        @if(!empty($buktiFile))
                                                            @php $buktiFileUrl = route('modules::pendaftar.file', ['path' => $buktiFile]); @endphp
                                                            <div style="margin-bottom: 1.25rem;">
                                                                @if(count($penghargaan->bukti_dukung) > 1)
                                                                    <div style="font-size:.75rem; color:#94a3b8; margin-bottom:.3rem; font-weight:700; ">Berkas {{ $fileIdx + 1 }}</div>
                                                                @endif
                                                                @if($isImage($buktiFile))
                                                                    <img src="{{ $buktiFileUrl }}"
                                                                         class="bukti-img lightbox-trigger"
                                                                         data-src="{{ $buktiFileUrl }}"
                                                                         alt="Bukti Penghargaan {{ $index + 1 }} - {{ $fileIdx + 1 }}">
                                                                @elseif($isPdf($buktiFile))
                                                                    <object data="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}" type="application/pdf" style="width: 100%; height: 500px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                                                        <iframe src="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}" style="width: 100%; height: 500px; border: none;">
                                                                            <p>Browser Anda tidak mendukung pratinjau PDF.</p>
                                                                        </iframe>
                                                                    </object>
                                                                @endif
                                                                <x-volt-link-button url="{{ route('modules::pendaftar.file', ['path' => $buktiFile, 'download' => 1]) }}"
                                                                                     icon="download"
                                                                                     class="basic blue"
                                                                                     style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                                                     target="_blank"
                                                                                     data-no-loader="true">
                                                                    Unduh{{ count($penghargaan->bukti_dukung) > 1 ? ' Berkas ' . ($fileIdx + 1) : ' Berkas Bukti' }}
                                                                </x-volt-link-button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align:center; padding: 2rem; color: #94a3b8;">
                                <i class="info circle icon large"></i>
                                <p style="margin-top:.5rem;">Tidak ada data penghargaan yang terdaftar.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- /left column --}}

            {{-- ═══════════════════════ KOLOM KANAN ══════════════════════ --}}
            <div class="five wide column">

                {{-- ── Status & Administrasi ──────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="tasks icon" style="margin:0"></i></div>
                        <h3>Status &amp; Administrasi</h3>
                    </div>
                    <div class="show-card-body">
                        {{-- Current status badge --}}
                        <div style="text-align:center; margin-bottom:1.25rem;">
                            <div style="font-size:1rem; font-weight:700;  letter-spacing:.08em; color:#94a3b8; margin-bottom:.5rem;">Status Saat Ini</div>
                            <div class="ui label {{ $statusColor }} large" style="font-weight: 700;">{{ $pendaftar->status ?? 'Diajukan' }}</div>
                        </div>

                        <hr class="show-divider">

                        {{-- Update status form --}}
                        <form class="status-form" action="{{ route('modules::pendaftar.update-status', $pendaftar->id) }}" method="POST">
                            @csrf
                            <div style="font-size:1rem; font-weight:700; color:#64748b;  letter-spacing:.06em; margin-bottom:.5rem;">Ubah Status Pendaftar</div>
                            
                            <input type="hidden" name="status" id="status-input" value="{{ $pendaftar->status ?? 'Diajukan' }}">
                            <div class="custom-dropdown" id="status-dropdown">
                                <div class="custom-dropdown-trigger">
                                    <div>
                                        <span class="status-dot {{ $statusColor }}"></span>
                                        <span class="status-text">{{ $pendaftar->status ?? 'Diajukan' }}</span>
                                    </div>
                                    <i class="chevron down icon"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    @foreach($statuses as $status)
                                        @php
                                            $optColor = match ($status) {
                                                'Diajukan'               => 'blue',
                                                'Lolos Verifikasi Berkas'=> 'yellow',
                                                'Lolos Penilaian Tahap 1'=> 'yellow',
                                                'Lolos Penilaian Tahap 2'=> 'yellow',
                                                'Lolos Penilaian Tahap 3'=> 'yellow',
                                                'Lolos Tahap Wawancara'  => 'yellow',
                                                'Lolos Tahap Final'      => 'teal',
                                                'Tidak Lolos'            => 'red',
                                                default                  => 'grey'
                                            };
                                        @endphp
                                        <div class="custom-dropdown-item {{ ($pendaftar->status ?? 'Diajukan') === $status ? 'active' : '' }}" data-value="{{ $status }}" data-color="{{ $optColor }}">
                                            <span class="status-dot {{ $optColor }}"></span>
                                            <span class="item-text">{{ $status }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <x-volt-button type="submit" icon="save" class="primary fluid">
                                Perbarui Status
                            </x-volt-button>
                        </form>

                        <hr class="show-divider">

                        {{-- Download all ZIP --}}
                        <x-volt-link-button url="{{ route('modules::pendaftar.download-all', $pendaftar->id) }}"
                                             icon="archive"
                                             class="blue fluid"
                                             target="_blank"
                                             data-no-loader="true">
                            Unduh Semua Berkas (ZIP)
                        </x-volt-link-button>
                    </div>
                </div>

                {{-- ── Lampiran Berkas Utama ──────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="paperclip icon" style="margin:0"></i></div>
                        <h3>Lampiran Berkas Utama</h3>
                    </div>
                    <div class="show-card-body">

                        {{-- Foto --}}
                        <div class="file-block">
                            <div class="file-block-label"><i class="camera icon"></i> Foto Diri</div>
                            @if(!empty($fotoRaw))
                                @if($isImage($fotoRaw))
                                    <img src="{{ route('modules::pendaftar.file', ['path' => $fotoRaw]) }}"
                                         class="file-img-preview lightbox-trigger"
                                         data-src="{{ route('modules::pendaftar.file', ['path' => $fotoRaw]) }}"
                                         alt="Foto Pendaftar">
                                @else
                                    <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                        <i class="file alternate outline icon large"></i><br>Berkas Non-Gambar
                                    </div>
                                @endif
                                <x-volt-link-button url="{{ route('modules::pendaftar.file', ['path' => $fotoRaw, 'download' => 1]) }}"
                                                     icon="download"
                                                     class="basic blue"
                                                     style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                     target="_blank"
                                                     data-no-loader="true">
                                    Unduh Foto
                                </x-volt-link-button>
                            @else
                                <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                    <i class="image outline icon"></i> Foto tidak diunggah
                                </div>
                            @endif
                        </div>

                        {{-- KTP --}}
                        <div class="file-block">
                            <div class="file-block-label"><i class="id card outline icon"></i> KTP Pendaftar</div>
                            @if(!empty($ktpRaw))
                                @if($isImage($ktpRaw))
                                    <img src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw]) }}"
                                         class="file-img-preview lightbox-trigger"
                                         data-src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw]) }}"
                                         alt="KTP Pendaftar">
                                @elseif($isPdf($ktpRaw))
                                    <object data="{{ route('modules::pendaftar.file', ['path' => $ktpRaw], false) }}" type="application/pdf" style="width: 100%; height: 300px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                        <iframe src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw], false) }}" style="width: 100%; height: 300px; border: none;">
                                            <p>Browser Anda tidak mendukung pratinjau PDF.</p>
                                        </iframe>
                                    </object>
                                @else
                                    <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                        <i class="file alternate outline icon large"></i><br>Berkas Lainnya
                                    </div>
                                @endif
                                <x-volt-link-button url="{{ route('modules::pendaftar.file', ['path' => $ktpRaw, 'download' => 1]) }}"
                                                     icon="download"
                                                     class="basic blue"
                                                     style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                     target="_blank"
                                                     data-no-loader="true">
                                    Unduh KTP
                                </x-volt-link-button>
                            @else
                                <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                    <i class="id card outline icon"></i> KTP tidak diunggah
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>{{-- /right column --}}
        </div>{{-- /grid --}}
    </div>{{-- /show-page --}}

    {{-- ═══════ Image Lightbox ═══════ --}}
    <div id="img-lightbox">
        <div id="img-lightbox-close" onclick="closeLightbox()">&#x2715;</div>
        <img id="img-lightbox-img" src="" alt="Preview">
    </div>

    @push('script')
    <script>
    (function () {
        /* ── Accordion ─────────────────────────────────────── */
        document.querySelectorAll('.accordion-trigger').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-acc');
                var content  = document.getElementById(targetId);
                var isOpen   = content.classList.contains('open');

                /* Close all open accordions */
                document.querySelectorAll('.accordion-content.open').forEach(function (el) {
                    el.classList.remove('open');
                });
                document.querySelectorAll('.accordion-trigger.open').forEach(function (el) {
                    el.classList.remove('open');
                });

                /* Open clicked (toggle) */
                if (!isOpen) {
                    content.classList.add('open');
                    btn.classList.add('open');
                }
            });
        });

        /* ── Image Lightbox ────────────────────────────────── */
        var lightbox = document.getElementById('img-lightbox');
        var lightboxImg = document.getElementById('img-lightbox-img');

        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('.lightbox-trigger');
            if (!trigger) return;
            e.preventDefault();
            var src = trigger.getAttribute('data-src') || trigger.src;
            lightboxImg.src = src;
            lightbox.classList.add('active');
        });

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox || e.target === lightboxImg) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });

        /* ── Prevent loader on download links ─────────────── */
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-no-loader]');
            if (!link) return;
            /* stop the page-loader that base.blade.php attaches */
            e.stopImmediatePropagation();
        }, true); /* capture phase so we run before the loader listener */

        /* ── Custom Dropdown Interaction ───────────────────── */
        var dropdown = document.getElementById('status-dropdown');
        if (dropdown) {
            var trigger = dropdown.querySelector('.custom-dropdown-trigger');
            var input = document.getElementById('status-input');
            var triggerDot = trigger.querySelector('.status-dot');
            var triggerText = trigger.querySelector('.status-text');

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            dropdown.querySelectorAll('.custom-dropdown-item').forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var val = item.getAttribute('data-value');
                    var color = item.getAttribute('data-color');
                    var text = item.querySelector('.item-text').textContent;

                    // Update hidden input
                    input.value = val;

                    // Update trigger text & dot color
                    triggerText.textContent = text;
                    triggerDot.className = 'status-dot ' + color;

                    // Set active item class
                    dropdown.querySelectorAll('.custom-dropdown-item').forEach(function (i) {
                        i.classList.remove('active');
                    });
                    item.classList.add('active');

                    // Close menu
                    dropdown.classList.remove('active');
                });
            });

            // Close when clicking outside
            document.addEventListener('click', function () {
                dropdown.classList.remove('active');
            });
        }
    }());

    function closeLightbox() {
        var lightbox = document.getElementById('img-lightbox');
        var lightboxImg = document.getElementById('img-lightbox-img');
        lightbox.classList.remove('active');
        setTimeout(function () { lightboxImg.src = ''; }, 300);
    }
    </script>
    @endpush

</x-volt-app>
