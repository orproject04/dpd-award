<x-volt-app :title="'Detail Pendaftar'">
    <x-volt-backlink url="{{ session('pendaftar_index_url', route('modules::pendaftar.index')) }}" />

    @php
        $statuses = [
            'Tidak Lolos',
            'Diajukan',
            'Lolos Verifikasi Berkas',
            'Lolos ke Tahap 50 Besar',
            'Lolos ke Tahap 10 Besar',
            'Lolos ke Tahap 3 Besar',
            'Lolos ke Tahap Wawancara',
            'Lolos ke Tahap Final',
        ];

        $statusColor = match ($pendaftar->status) {
            'Tidak Lolos' => 'red',
            'Diajukan' => 'blue',
            'Lolos Verifikasi Berkas' => 'yellow',
            'Lolos ke Tahap 50 Besar' => 'yellow',
            'Lolos ke Tahap 10 Besar' => 'yellow',
            'Lolos ke Tahap 3 Besar' => 'yellow',
            'Lolos ke Tahap Wawancara' => 'purple',
            'Lolos ke Tahap Final' => 'teal',
            default => 'grey',
        };

        $isImage = function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
        };

        $isPdf = function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return $ext === 'pdf';
        };

        $fotoRaw = $pendaftar->getRawOriginal('foto');
        $ktpRaw = $pendaftar->getRawOriginal('ktp');

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
                --accent:
                    {{ $themeColor }}
                ;
                --accent-hover:
                    {{ hexToRgba(config('laravolt.ui.color'), 1.0) }}
                ;
                --accent-light:
                    {{ $themeColorLight }}
                ;
                --radius: 10px;
            }

            input[type=number]::-webkit-inner-spin-button, 
            input[type=number]::-webkit-outer-spin-button { 
                -webkit-appearance: none; 
                margin: 0; 
            }
            input[type=number] {
                -moz-appearance: textfield;
            }
            /* ─── Section Cards ─────────────────────────────────────── */
            .show-card {
                background: #fff;
                border: 1px solid #e8edf2;
                border-radius: var(--radius);
                box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
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

            .custom-file-upload {
                border: 2px dashed #cbd5e1;
                border-radius: 8px;
                padding: 2rem 1.5rem;
                text-align: center;
                background: #fdfdfd;
                cursor: pointer !important;
                transition: all 0.3s;
                position: relative;
                display: block;
                margin-top: 0.5rem;
            }
            .custom-file-upload:hover {
                border-color: #dcb340;
                background: #fffdf5;
            }
            .custom-file-upload input[type="file"] {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor: pointer !important;
                z-index: 10;
            }
            .custom-file-upload .upload-icon-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: #fff;
                border: 1px solid #dcb340;
                color: #dcb340;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem auto;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .custom-file-upload .upload-text {
                font-weight: 700;
                color: #1e293b;
                font-size: 0.95rem;
            }
            .custom-file-upload .upload-hint {
                font-size: 0.8rem;
                color: #94a3b8;
                margin-top: 0.25rem;
            }
            .ui.form .required.field > .custom-file-upload:after {
                display: none !important;
            }
            .file-preview-container {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-top: 1rem;
            }
            .file-preview-card {
                display: flex;
                align-items: center;
                padding: 0.75rem;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                width: calc(50% - 0.5rem);
                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                position: relative;
            }
            .file-preview-card .file-icon {
                width: 48px;
                height: 48px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #10b981;
                color: #fff;
                margin-right: 1rem;
                overflow: hidden;
            }
            .file-preview-card .file-icon img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .file-preview-card .file-info {
                flex: 1;
                overflow: hidden;
            }
            .file-preview-card .file-name {
                font-size: 0.85rem;
                font-weight: 700;
                color: #1e293b;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .file-preview-card .file-meta {
                font-size: 0.7rem;
                color: #64748b;
                margin-top: 0.2rem;
                text-transform: uppercase;
            }
            .file-preview-card .file-remove {
                cursor: pointer;
                color: #94a3b8;
                padding: 0.5rem;
                transition: color 0.2s;
            }
            .file-preview-card .file-remove:hover {
                color: #ef4444;
            }

            .show-card-header .card-icon {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--accent);
                border-radius: 8px;
                color: #fff;
                font-size: 1rem;
                flex-shrink: 0;
            }

            .show-card-header h3 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 700;
                color: #1a2035;
            }

            .show-card-body {
                padding: 1.25rem;
            }

            /* ─── Profile Table ───────────────────────────────────── */
            .profile-table {
                width: 100%;
                border-collapse: collapse;
            }

            .profile-table tr {
                border-bottom: 1px solid #f1f5f9;
            }

            .profile-table tr:last-child {
                border-bottom: none;
            }

            .profile-table td {
                padding: .65rem .75rem;
                font-size: 1rem;
                vertical-align: top;
            }

            .profile-table td:first-child {
                width: 38%;
                color: #64748b;
                font-weight: 500;
                white-space: nowrap;
            }

            .profile-table td:last-child {
                color: #1a2035;
                font-weight: 600;
            }

            /* ─── File Attachment Block ──────────────────────────── */
            .file-block {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .file-block:last-child {
                margin-bottom: 0;
            }

            .file-block-label {
                font-size: .9rem;
                font-weight: 700;
                color: #64748b;
                letter-spacing: .06em;
                margin-bottom: .65rem;
            }

            .file-img-preview {
                width: 100%;
                border-radius: 6px;
                max-height: 200px;
                object-fit: contain;
                background: #f0f4f8;
                border: 1px solid #e2e8f0;
                cursor: zoom-in;
                transition: transform .2s, box-shadow .2s;
            }

            .file-img-preview:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 16px rgba(0, 0, 0, .15);
            }

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

            .status-dot.blue {
                background-color: #3b82f6;
                box-shadow: 0 0 6px rgba(59, 130, 246, 0.6);
            }

            .status-dot.yellow {
                background-color: #eab308;
                box-shadow: 0 0 6px rgba(234, 179, 8, 0.6);
            }

            .status-dot.teal {
                background-color: #14b8a6;
                box-shadow: 0 0 6px rgba(20, 184, 166, 0.6);
            }

            .status-dot.red {
                background-color: #ef4444;
                box-shadow: 0 0 6px rgba(239, 68, 68, 0.6);
            }

            .status-dot.grey {
                background-color: #64748b;
                box-shadow: 0 0 6px rgba(100, 116, 139, 0.6);
            }

            .status-dot.purple {
                background-color: #8b5cf6;
                box-shadow: 0 0 6px rgba(139, 92, 246, 0.6);
            }

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

            .accordion-item:last-child {
                margin-bottom: 0;
            }

            .accordion-trigger {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: .85rem 1rem;
                background: #f8fafc;
                border: none;
                cursor: pointer;
                font-size: .92rem;
                font-weight: 700;
                color: #1a2035;
                text-align: left;
                transition: background .15s;
            }

            .accordion-trigger:hover {
                background: var(--accent-light);
            }

            .accordion-trigger .acc-chevron {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
                transition: transform .25s ease;
                color: #94a3b8;
            }

            .accordion-trigger.open .acc-chevron {
                transform: rotate(180deg);
            }

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
            .detail-field {
                margin-bottom: 1rem;
            }

            .detail-field:last-child {
                margin-bottom: 0;
            }

            .detail-field-label {
                font-size: 0.9rem;
                font-weight: 700;
                color: #64748b;
                letter-spacing: .06em;
                margin-bottom: .3rem;
            }

            .detail-field-value {
                font-size: 1rem;
                color: #1a2035;
                white-space: pre-line;
                line-height: 1.6;
            }

            .detail-field-divider {
                border: none;
                border-top: 1px solid #f1f5f9;
                margin: 1rem 0;
            }

            /* ─── Bukti Dukung inside accordion ─────────────────── */
            .bukti-img {
                width: 100%;
                border-radius: 6px;
                max-height: 220px;
                object-fit: contain;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                cursor: zoom-in;
                transition: transform .2s, box-shadow .2s;
                display: block;
            }

            .bukti-img:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 16px rgba(0, 0, 0, .15);
            }

            /* ─── Lightbox Overlay ─────────────────────────────── */
            #img-lightbox {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 99998;
                background: rgba(0, 0, 0, .85);
                backdrop-filter: blur(4px);
                align-items: center;
                justify-content: center;
                cursor: zoom-out;
            }

            #img-lightbox.active {
                display: flex;
            }

            #img-lightbox img {
                max-width: 90vw;
                max-height: 90vh;
                border-radius: 8px;
                box-shadow: 0 8px 40px rgba(0, 0, 0, .6);
                object-fit: contain;
            }

            #img-lightbox-close {
                position: absolute;
                top: 1.25rem;
                right: 1.5rem;
                color: #fff;
                font-size: 2rem;
                cursor: pointer;
                line-height: 1;
                background: rgba(255, 255, 255, .1);
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background .15s;
            }

            #img-lightbox-close:hover {
                background: rgba(255, 255, 255, .25);
            }

            /* ─── Section divider ─────────────────────────────── */
            .show-divider {
                border: none;
                border-top: 1px solid #e2e8f0;
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
                            <tr>
                                <td>Nomor Registrasi</td>
                                <td>{{ $pendaftar->nomor_registrasi }}</td>
                            </tr>
                            <tr>
                                <td>Kategori</td>
                                <td>{{ $pendaftar->kategori }}</td>
                            </tr>
                            <tr>
                                <td>Provinsi</td>
                                <td>
                                    <div id="provinsi-display"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>{{ $pendaftar->provinsi_with_wilayah }}</span>
                                        <button type="button" class="ui mini button basic"
                                            onclick="document.getElementById('provinsi-form-container').style.display='block'; document.getElementById('provinsi-display').style.display='none';">Edit</button>
                                    </div>
                                    <div id="provinsi-form-container" style="display: none;">
                                        <form id="provinsi-form"
                                            action="{{ route('modules::pendaftar.update-provinsi', $pendaftar->id) }}"
                                            method="POST" class="ui form mini">
                                            @csrf
                                            <div style="display: flex; gap: 8px; width: 100%; align-items: stretch;">
                                                <div style="flex-grow: 1;">
                                                    <select name="provinsi" class="ui fluid search dropdown">
                                                        @foreach (\App\Models\Pendaftar::getProvinsiList() as $val => $label)
                                                            <option value="{{ $val }}" {{ $pendaftar->provinsi == $val ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="display: flex; gap: 4px; flex-shrink: 0;">
                                                    <button type="submit" class="ui mini button primary"
                                                        style="margin: 0;">Simpan</button>
                                                    <button type="button" class="ui mini button basic"
                                                        style="margin: 0;"
                                                        onclick="document.getElementById('provinsi-form-container').style.display='none'; document.getElementById('provinsi-display').style.display='flex'; $(this).closest('form').find('.ui.dropdown').dropdown('restore defaults');">Batal</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>{{ $pendaftar->nama }}</td>
                            </tr>
                            <tr>
                                <td>Tempat, Tanggal Lahir</td>
                                <td>{{ $pendaftar->tempat_lahir }},
                                    {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td>{{ $pendaftar->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <td>Pendidikan</td>
                                <td>{{ $pendaftar->pendidikan }}</td>
                            </tr>
                            <tr>
                                <td>Alamat Lengkap</td>
                                <td>{{ $pendaftar->alamat }}</td>
                            </tr>
                            <tr>
                                <td>Nomor WhatsApp</td>
                                <td>
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                        style="color: var(--accent); font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <i class="whatsapp icon"></i> {{ $pendaftar->nomor_wa }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat Email</td>
                                <td>{{ $pendaftar->email }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Registrasi</td>
                                <td>{{ $pendaftar->created_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- ── Kontribusi / Inovasi ─────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <div class="card-icon"><i class="lightbulb outline icon" style="margin:0"></i></div>
                            <h3 style="margin:0">Kontribusi / Inovasi</h3>
                        </div>
                        @if(auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                            <button type="button" class="ui mini blue button" onclick="$('#modal-add-kontribusi').modal('show')">
                                <i class="plus icon"></i> Tambah
                            </button>
                        @endif
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="accordion-content" id="kontribusi-{{ $index }}">
                                    <div class="accordion-content-inner">
                                        <div class="detail-field">
                                            <div class="detail-field-label">Judul Inovasi / Kontribusi</div>
                                            <div class="detail-field-value" style="font-weight:700;">
                                                {{ $kontribusi->judul }}
                                            </div>
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
                                        @if (!empty($kontribusi->bukti_dukung) && is_array($kontribusi->bukti_dukung) && count($kontribusi->bukti_dukung) > 0)
                                            <hr class="detail-field-divider">
                                            <div class="detail-field">
                                                <div class="detail-field-label">Bukti Dukung (Evidence)</div>
                                                <div style="margin-top: .5rem;">
                                                    @foreach ($kontribusi->bukti_dukung as $fileIdx => $buktiFile)
                                                        @if (!empty($buktiFile))
                                                            @php $buktiFileUrl = route('modules::pendaftar.file', ['path' => $buktiFile]); @endphp
                                                            <div style="margin-bottom: 1.25rem;">
                                                                @if (count($kontribusi->bukti_dukung) > 1)
                                                                    <div
                                                                        style="font-size:.75rem; color:#94a3b8; margin-bottom:.3rem; font-weight:700; ">
                                                                        Berkas {{ $fileIdx + 1 }}</div>
                                                                @endif
                                                                @if ($isImage($buktiFile))
                                                                    <img src="{{ $buktiFileUrl }}" class="bukti-img lightbox-trigger"
                                                                        data-src="{{ $buktiFileUrl }}"
                                                                        alt="Bukti Kontribusi {{ $index + 1 }} - {{ $fileIdx + 1 }}">
                                                                @elseif($isPdf($buktiFile))
                                                                    <object
                                                                        data="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}"
                                                                        type="application/pdf"
                                                                        style="width: 100%; height: 500px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                                                        <iframe
                                                                            src="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}"
                                                                            style="width: 100%; height: 500px; border: none;">
                                                                            <p>Browser Anda tidak mendukung pratinjau
                                                                                PDF.</p>
                                                                        </iframe>
                                                                    </object>
                                                                @endif
                                                                <x-volt-link-button
                                                                    url="{{ route('modules::pendaftar.file', ['path' => $buktiFile, 'download' => 1]) }}"
                                                                    icon="download" class="basic blue"
                                                                    style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                                    target="_blank" data-no-loader="true">
                                                                    Unduh{{ count($kontribusi->bukti_dukung) > 1 ? ' Berkas ' . ($fileIdx + 1) : ' Berkas Bukti' }}
                                                                </x-volt-link-button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($kontribusi->is_from_admin && (auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE)))
                                            <hr class="detail-field-divider">
                                            <div style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                                <button type="button" class="ui mini orange button" onclick="$('#modal-edit-kontribusi-{{ $kontribusi->id }}').modal('show')">
                                                    <i class="edit icon"></i> Edit
                                                </button>
                                                <form id="form-delete-kontribusi-{{ $kontribusi->id }}" action="{{ route('modules::pendaftar.destroy-kontribusi', ['pendaftar' => $pendaftar->id, 'kontribusi' => $kontribusi->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="ui mini red button" onclick="confirmDelete('form-delete-kontribusi-{{ $kontribusi->id }}', 'Apakah Anda yakin ingin menghapus kontribusi ini?')">
                                                        <i class="trash icon"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            {{-- MODAL EDIT KONTRIBUSI --}}
                                            <div class="ui modal small" id="modal-edit-kontribusi-{{ $kontribusi->id }}" style="text-align: left;">
                                                <i class="close icon"></i>
                                                <div class="header">Edit Kontribusi / Inovasi</div>
                                                <div class="content">
                                                    <form id="form-edit-kontribusi-{{ $kontribusi->id }}" action="{{ route('modules::pendaftar.update-kontribusi', ['pendaftar' => $pendaftar->id, 'kontribusi' => $kontribusi->id]) }}" method="POST" enctype="multipart/form-data" class="ui form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="field required">
                                                            <label>Judul</label>
                                                            <input type="text" name="judul" value="{{ $kontribusi->judul }}" maxlength="500" required>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Deskripsi</label>
                                                            <textarea name="deskripsi" required>{{ $kontribusi->deskripsi }}</textarea>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Dampak</label>
                                                            <textarea name="dampak" required>{{ $kontribusi->dampak }}</textarea>
                                                        </div>
                                                        <div class="field">
                                                            <label>Bukti Dukung (Ganti File - Opsional)</label>
                                                            <div class="custom-file-upload" >
                                                                <input type="file" name="bukti_dukung[]" multiple accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" onchange="handleFileChange(this)">
                                                                <div class="upload-icon-circle">
                                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="upload-text">Klik atau seret file ke sini</div>
                                                                <div class="upload-hint">Format JPG, PNG, PDF, DOC, XLS, PPT, ZIP</div>
                                                            </div>
                                                            <small style="display:block; margin-top:.5rem;">Biarkan kosong jika tidak ingin mengubah file bukti dukung.</small>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="actions">
                                                    <div class="ui cancel button">Batal</div>
                                                    <button type="submit" form="form-edit-kontribusi-{{ $kontribusi->id }}" class="ui primary button">Simpan Perubahan</button>
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
                
                {{-- MODAL ADD KONTRIBUSI --}}
                @if(auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                    <div class="ui modal small" id="modal-add-kontribusi">
                        <i class="close icon"></i>
                        <div class="header">Tambah Kontribusi / Inovasi</div>
                        <div class="content">
                            <form id="form-add-kontribusi" action="{{ route('modules::pendaftar.store-kontribusi', ['pendaftar' => $pendaftar->id]) }}" method="POST" enctype="multipart/form-data" class="ui form">
                                @csrf
                                <div class="field required">
                                    <label>Judul</label>
                                    <input type="text" name="judul" maxlength="500" required>
                                </div>
                                <div class="field required">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" required></textarea>
                                </div>
                                <div class="field required">
                                    <label>Dampak</label>
                                    <textarea name="dampak" required></textarea>
                                </div>
                                <div class="field required">
                                    <label>Bukti Dukung (Wajib)</label>
                                    <div class="custom-file-upload" >
                                        <input type="file" name="bukti_dukung[]" multiple required accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" onchange="handleFileChange(this)">
                                        <div class="upload-icon-circle">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                        </div>
                                        <div class="upload-text">Klik atau seret file ke sini</div>
                                        <div class="upload-hint">Format JPG, PNG, PDF, DOC, XLS, PPT, ZIP</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="actions">
                            <div class="ui cancel button">Batal</div>
                            <button type="submit" form="form-add-kontribusi" class="ui primary button">Tambah Kontribusi</button>
                        </div>
                    </div>
                @endif

                {{-- ── Penghargaan ───────────────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <div class="card-icon"><i class="trophy icon" style="margin:0"></i></div>
                            <h3 style="margin:0">Penghargaan</h3>
                        </div>
                        @if(auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                            <button type="button" class="ui mini blue button" onclick="$('#modal-add-penghargaan').modal('show')">
                                <i class="plus icon"></i> Tambah
                            </button>
                        @endif
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
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
                                                {{ $penghargaan->tahun }}
                                            </div>
                                        </div>
                                        @if (!empty($penghargaan->bukti_dukung) && is_array($penghargaan->bukti_dukung) && count($penghargaan->bukti_dukung) > 0)
                                            <hr class="detail-field-divider">
                                            <div class="detail-field">
                                                <div class="detail-field-label">Bukti Dukung (Evidence)</div>
                                                <div style="margin-top: .5rem;">
                                                    @foreach ($penghargaan->bukti_dukung as $fileIdx => $buktiFile)
                                                        @if (!empty($buktiFile))
                                                            @php $buktiFileUrl = route('modules::pendaftar.file', ['path' => $buktiFile]); @endphp
                                                            <div style="margin-bottom: 1.25rem;">
                                                                @if (count($penghargaan->bukti_dukung) > 1)
                                                                    <div
                                                                        style="font-size:.75rem; color:#94a3b8; margin-bottom:.3rem; font-weight:700; ">
                                                                        Berkas {{ $fileIdx + 1 }}</div>
                                                                @endif
                                                                @if ($isImage($buktiFile))
                                                                    <img src="{{ $buktiFileUrl }}" class="bukti-img lightbox-trigger"
                                                                        data-src="{{ $buktiFileUrl }}"
                                                                        alt="Bukti Penghargaan {{ $index + 1 }} - {{ $fileIdx + 1 }}">
                                                                @elseif($isPdf($buktiFile))
                                                                    <object
                                                                        data="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}"
                                                                        type="application/pdf"
                                                                        style="width: 100%; height: 500px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                                                        <iframe
                                                                            src="{{ route('modules::pendaftar.file', ['path' => $buktiFile], false) }}"
                                                                            style="width: 100%; height: 500px; border: none;">
                                                                            <p>Browser Anda tidak mendukung pratinjau
                                                                                PDF.</p>
                                                                        </iframe>
                                                                    </object>
                                                                @endif
                                                                <x-volt-link-button
                                                                    url="{{ route('modules::pendaftar.file', ['path' => $buktiFile, 'download' => 1]) }}"
                                                                    icon="download" class="basic blue"
                                                                    style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                                                    target="_blank" data-no-loader="true">
                                                                    Unduh{{ count($penghargaan->bukti_dukung) > 1 ? ' Berkas ' . ($fileIdx + 1) : ' Berkas Bukti' }}
                                                                </x-volt-link-button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($penghargaan->is_from_admin && (auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE)))
                                            <hr class="detail-field-divider">
                                            <div style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                                <button type="button" class="ui mini orange button" onclick="$('#modal-edit-penghargaan-{{ $penghargaan->id }}').modal('show')">
                                                    <i class="edit icon"></i> Edit
                                                </button>
                                                <form id="form-delete-penghargaan-{{ $penghargaan->id }}" action="{{ route('modules::pendaftar.destroy-penghargaan', ['pendaftar' => $pendaftar->id, 'penghargaan' => $penghargaan->id]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="ui mini red button" onclick="confirmDelete('form-delete-penghargaan-{{ $penghargaan->id }}', 'Apakah Anda yakin ingin menghapus penghargaan ini?')">
                                                        <i class="trash icon"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            {{-- MODAL EDIT PENGHARGAAN --}}
                                            <div class="ui modal small" id="modal-edit-penghargaan-{{ $penghargaan->id }}" style="text-align: left;">
                                                <i class="close icon"></i>
                                                <div class="header">Edit Penghargaan</div>
                                                <div class="content">
                                                    <form id="form-edit-penghargaan-{{ $penghargaan->id }}" action="{{ route('modules::pendaftar.update-penghargaan', ['pendaftar' => $pendaftar->id, 'penghargaan' => $penghargaan->id]) }}" method="POST" enctype="multipart/form-data" class="ui form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="field required">
                                                            <label>Uraian Penghargaan</label>
                                                            <input type="text" name="uraian" value="{{ $penghargaan->uraian }}" maxlength="500" required>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Tahun</label>
                                                            <input type="number" name="tahun" value="{{ $penghargaan->tahun }}" min="1900" max="{{ date('Y') }}" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                                                        </div>
                                                        <div class="field">
                                                            <label>Bukti Dukung (Ganti File - Opsional)</label>
                                                            <div class="custom-file-upload" >
                                                                <input type="file" name="bukti_dukung[]" multiple accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" onchange="handleFileChange(this)">
                                                                <div class="upload-icon-circle">
                                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                                    </svg>
                                                                </div>
                                                                <div class="upload-text">Klik atau seret file ke sini</div>
                                                                <div class="upload-hint">Format JPG, PNG, PDF, DOC, XLS, PPT, ZIP</div>
                                                            </div>
                                                            <small style="display:block; margin-top:.5rem;">Biarkan kosong jika tidak ingin mengubah file bukti dukung.</small>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="actions">
                                                    <div class="ui cancel button">Batal</div>
                                                    <button type="submit" form="form-edit-penghargaan-{{ $penghargaan->id }}" class="ui primary button">Simpan Perubahan</button>
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
                
                {{-- MODAL ADD PENGHARGAAN --}}
                @if(auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                    <div class="ui modal small" id="modal-add-penghargaan">
                        <i class="close icon"></i>
                        <div class="header">Tambah Penghargaan</div>
                        <div class="content">
                            <form id="form-add-penghargaan" action="{{ route('modules::pendaftar.store-penghargaan', ['pendaftar' => $pendaftar->id]) }}" method="POST" enctype="multipart/form-data" class="ui form">
                                @csrf
                                <div class="field required">
                                    <label>Uraian Penghargaan</label>
                                    <input type="text" name="uraian" maxlength="500" required>
                                </div>
                                <div class="field required">
                                    <label>Tahun</label>
                                    <input type="number" name="tahun" min="1900" max="{{ date('Y') }}" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                                </div>
                                <div class="field required">
                                    <label>Bukti Dukung (Wajib)</label>
                                    <div class="custom-file-upload" >
                                        <input type="file" name="bukti_dukung[]" multiple required accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" onchange="handleFileChange(this)">
                                        <div class="upload-icon-circle">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                        </div>
                                        <div class="upload-text">Klik atau seret file ke sini</div>
                                        <div class="upload-hint">Format JPG, PNG, PDF, DOC, XLS, PPT, ZIP</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="actions">
                            <div class="ui cancel button">Batal</div>
                            <button type="submit" form="form-add-penghargaan" class="ui primary button">Tambah Penghargaan</button>
                        </div>
                    </div>
                @endif

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
                            <div
                                style="font-size:1rem; font-weight:700;  letter-spacing:.08em; color:#94a3b8; margin-bottom:.5rem;">
                                Status Saat Ini</div>
                            <div class="ui label {{ $statusColor }} large" style="font-weight: 700;">
                                {{ $pendaftar->status ?? 'Diajukan' }}
                            </div>
                        </div>

                        <hr class="show-divider">

                        {{-- Update status form --}}
                        <div x-data="{ showModal: false }">
                            <form class="status-update-form" x-ref="statusForm"
                                action="{{ route('modules::pendaftar.update-status', $pendaftar->id) }}" method="POST">
                                @csrf
                                <div
                                    style="font-size:1rem; font-weight:700; color:#64748b;  letter-spacing:.06em; margin-bottom:.5rem;">
                                    Ubah Status Pendaftar</div>

                                <input type="hidden" name="status" id="status-input"
                                    value="{{ $pendaftar->status ?? 'Diajukan' }}">
                                <div class="custom-dropdown" id="status-dropdown">
                                    <div class="custom-dropdown-trigger">
                                        <div>
                                            <span class="status-dot {{ $statusColor }}"></span>
                                            <span class="status-text">{{ $pendaftar->status ?? 'Diajukan' }}</span>
                                        </div>
                                        <i class="chevron down icon"></i>
                                    </div>
                                    <div class="custom-dropdown-menu">
                                        @foreach ($statuses as $status)
                                            @php
                                                $optColor = match ($status) {
                                                    'Tidak Lolos' => 'red',
                                                    'Diajukan' => 'blue',
                                                    'Lolos Verifikasi Berkas' => 'yellow',
                                                    'Lolos ke Tahap 50 Besar' => 'yellow',
                                                    'Lolos ke Tahap 10 Besar' => 'yellow',
                                                    'Lolos ke Tahap 3 Besar' => 'yellow',
                                                    'Lolos ke Tahap Wawancara' => 'purple',
                                                    'Lolos ke Tahap Final' => 'teal',
                                                    default => 'grey',
                                                };
                                            @endphp
                                            <div class="custom-dropdown-item {{ ($pendaftar->status ?? 'Diajukan') === $status ? 'active' : '' }}"
                                                data-value="{{ $status }}" data-color="{{ $optColor }}">
                                                <span class="status-dot {{ $optColor }}"></span>
                                                <span class="item-text">{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <x-volt-button type="button" icon="save" class="primary fluid"
                                    @click="showModal = true">
                                    Perbarui Status
                                </x-volt-button>

                                {{-- Modal Keterangan --}}
                                <div x-show="showModal"
                                    style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; background: rgba(0,0,0,0.6);"
                                    x-transition>
                                    <div @click.away="showModal = false"
                                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 12px; width: 450px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                                        <h3
                                            style="margin-top: 0; margin-bottom: 0.5rem; font-size: 1.25rem; color: #1e293b;">
                                            <i class="edit icon"></i> Keterangan</h3>

                                        <div class="ui form">
                                            <div class="field required">
                                                <label>Keterangan</label>
                                                <textarea x-ref="keteranganInput" name="keterangan" rows="4" required
                                                    placeholder="Contoh: Lolos berkas administrasi dan dapat lanjut ke tahap berikutnya..."></textarea>
                                            </div>
                                        </div>

                                        <div
                                            style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                                            <button type="button" @click="showModal = false"
                                                class="ui button basic">Batal</button>
                                            <button type="button"
                                                @click="if($refs.keteranganInput.reportValidity()) $refs.statusForm.submit()"
                                                class="ui button primary"><i class="save icon"></i> Simpan
                                                Status</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <hr class="show-divider">

                        {{-- Download all ZIP --}}
                        <x-volt-link-button url="{{ route('modules::pendaftar.download-all', $pendaftar->id) }}"
                            icon="archive" class="blue fluid" target="_blank" data-no-loader="true">
                            Unduh Semua Berkas (ZIP)
                        </x-volt-link-button>

                        <hr class="show-divider">

                        {{-- Resend Email --}}
                        {{-- Resend Email --}}
                        <form id="resend-email-form"
                            action="{{ route('modules::pendaftar.resend-email', $pendaftar->id) }}" method="POST">
                            @csrf
                            <x-volt-button type="button" icon="envelope" class="green fluid"
                                onclick="confirmResendEmail()">
                                Kirim Ulang Email Bukti Pendaftaran
                            </x-volt-button>
                        </form>
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
                            @if (!empty($fotoRaw))
                                @if ($isImage($fotoRaw))
                                    <img src="{{ route('modules::pendaftar.file', ['path' => $fotoRaw]) }}"
                                        class="file-img-preview lightbox-trigger"
                                        data-src="{{ route('modules::pendaftar.file', ['path' => $fotoRaw]) }}"
                                        alt="Foto Pendaftar">
                                @else
                                    <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                        <i class="file alternate outline icon large"></i><br>Berkas Non-Gambar
                                    </div>
                                @endif
                                <x-volt-link-button
                                    url="{{ route('modules::pendaftar.file', ['path' => $fotoRaw, 'download' => 1]) }}"
                                    icon="download" class="basic blue"
                                    style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                    target="_blank" data-no-loader="true">
                                    Unduh Foto
                                </x-volt-link-button>
                            @else
                                <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                    <i class="image outline icon"></i> Foto tidak diunggah
                                </div>
                            @endif
                            <form action="{{ route('modules::pendaftar.update-foto', $pendaftar->id) }}" method="POST"
                                enctype="multipart/form-data" style="margin-top: .6rem;">
                                @csrf
                                <label class="ui basic button small fluid"
                                    style="display: flex; align-items: center; justify-content: center; gap: .4rem;">
                                    <i class="upload icon"></i> Unggah / Ganti Foto
                                    <input type="file" name="foto" style="display: none;"
                                        onchange="showLoading(); this.form.submit();" accept="image/*">
                                </label>
                            </form>
                        </div>

                        {{-- KTP --}}
                        @php
                            $user = auth()->user();
                            $canViewKtp =
                                $user &&
                                ($user->hasPermission('*') || $user->hasPermission(\App\Enums\Permission::KTP_VIEW));
                        @endphp
                        @if ($canViewKtp)
                            <div class="file-block">
                                <div class="file-block-label"><i class="id card outline icon"></i> KTP Pendaftar</div>

                                @if (!empty($ktpRaw))
                                    @if ($isImage($ktpRaw))
                                        <img src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw]) }}"
                                            class="file-img-preview lightbox-trigger"
                                            data-src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw]) }}"
                                            alt="KTP Pendaftar">
                                    @elseif($isPdf($ktpRaw))
                                        <object data="{{ route('modules::pendaftar.file', ['path' => $ktpRaw], false) }}"
                                            type="application/pdf"
                                            style="width: 100%; height: 300px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 0.5rem;">
                                            <iframe src="{{ route('modules::pendaftar.file', ['path' => $ktpRaw], false) }}"
                                                style="width: 100%; height: 300px; border: none;">
                                                <p>Browser Anda tidak mendukung pratinjau PDF.</p>
                                            </iframe>
                                        </object>
                                    @else
                                        <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                            <i class="file alternate outline icon large"></i><br>Berkas Lainnya
                                        </div>
                                    @endif
                                    <x-volt-link-button
                                        url="{{ route('modules::pendaftar.file', ['path' => $ktpRaw, 'download' => 1]) }}"
                                        icon="download" class="basic blue"
                                        style="margin-top: .6rem; width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;"
                                        target="_blank" data-no-loader="true">
                                        Unduh KTP
                                    </x-volt-link-button>
                                @else
                                    <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                        <i class="id card outline icon"></i> KTP tidak diunggah
                                    </div>
                                @endif
                                <form action="{{ route('modules::pendaftar.update-ktp', $pendaftar->id) }}" method="POST"
                                    enctype="multipart/form-data" style="margin-top: .6rem;">
                                    @csrf
                                    <label class="ui basic button small fluid"
                                        style="display: flex; align-items: center; justify-content: center; gap: .4rem;">
                                        <i class="upload icon"></i> Unggah / Ganti KTP
                                        <input type="file" name="ktp" style="display: none;"
                                            onchange="showLoading(); this.form.submit();" accept=".jpg,.jpeg,.png,.pdf">
                                    </label>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>{{-- /show-card (Lampiran Berkas Utama) --}}

                {{-- ── Riwayat Status ──────────────────────────── --}}
                <div class="show-card mt-4">
                    <div class="show-card-header">
                        <div class="card-icon"><i class="history icon" style="margin:0"></i></div>
                        <h3>Riwayat Status</h3>
                    </div>
                    <div class="show-card-body"
                        style="padding: 1.2rem; display: flex; flex-direction: column; gap: 1.2rem;">
                        @foreach ($pendaftar->riwayats ?? [] as $riwayat)
                            @php
                                $optColor = match ($riwayat->status) {
                                    'Tidak Lolos' => 'red',
                                    'Diajukan' => 'blue',
                                    'Lolos Verifikasi Berkas' => 'yellow',
                                    'Lolos ke Tahap 50 Besar' => 'yellow',
                                    'Lolos ke Tahap 10 Besar' => 'yellow',
                                    'Lolos ke Tahap 3 Besar' => 'yellow',
                                    'Lolos ke Tahap Wawancara' => 'purple',
                                    'Lolos ke Tahap Final' => 'teal',
                                    default => 'grey',
                                };
                                $textColor = match ($optColor) {
                                    'red' => '#ef4444',
                                    'blue' => '#3b82f6',
                                    'yellow' => '#d97706', // slightly darker yellow for text readability
                                    'teal' => '#14b8a6',
                                    'purple' => '#a855f7',
                                    default => '#64748b'
                                };
                            @endphp
                            <div
                                style="border-left: 2px solid #e2e8f0; padding-left: 1.75rem; position: relative; padding-bottom: 0.5rem;">
                                <div class="status-dot {{ $optColor }}"
                                    style="position: absolute; left: -9px; top: 2px; width: 14px; height: 14px; margin: 0; box-shadow: 0 0 0 3px #fff;">
                                </div>
                                <div
                                    style="font-size: 15px; font-weight: 700; color: {{ $textColor }}; margin-bottom: 0.4rem;">
                                    {{ $riwayat->status }}</div>
                                <div
                                    style="font-size: 12.5px; color: #475569; margin-bottom: 0.75rem; display: inline-flex; align-items: center; background: #f1f5f9; padding: 0.3rem 0.7rem; border-radius: 6px; font-weight: 500;">
                                    <i class="calendar alternate icon" style="margin-right: 0.4rem; color: #64748b;"></i>
                                    {{ $riwayat->created_at->format('d M Y, H:i') }}
                                </div>

                                @if ($riwayat->status !== 'Diajukan')
                                    <div x-data="{ showForm: false }" style="margin-top: 0.5rem;">
                                        <button type="button" x-show="!showForm" @click="showForm = true"
                                            class="ui button small basic"
                                            style="padding: 0.6rem 1rem; font-size: 12.5px; margin-bottom: 0.5rem;">
                                            <i class="edit icon"></i>
                                            {{ $riwayat->keterangan ? 'Ubah Keterangan' : 'Tambah Keterangan' }}
                                        </button>

                                        <form action="{{ route('pendaftar.riwayat.update-keterangan', $riwayat->id) }}"
                                            method="POST" x-show="showForm" style="display: none;">
                                            @csrf
                                            <div class="ui form">
                                                <div class="field" style="margin-bottom: 0.6rem;">
                                                    <textarea name="keterangan" rows="2"
                                                        style="font-size: 14px; padding: 0.75rem; line-height: 1.5;"
                                                        placeholder="Tambahkan keterangan opsional...">{{ $riwayat->keterangan }}</textarea>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button type="submit" class="ui button small primary"
                                                        style="padding: 0.6rem 1.2rem; font-size: 13px;"><i
                                                            class="save icon"></i> Simpan</button>
                                                    <button type="button" @click="showForm = false"
                                                        class="ui button small basic"
                                                        style="padding: 0.6rem 1.2rem; font-size: 13px;">Batal</button>
                                                </div>
                                            </div>
                                        </form>

                                        @if($riwayat->keterangan)
                                            <div x-show="!showForm"
                                                style="font-size: 14px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem; border-radius: 8px; color: #334155; line-height: 1.5;">
                                                {{ $riwayat->keterangan }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if($riwayat->keterangan)
                                        <div
                                            style="font-size: 14px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem; border-radius: 8px; margin-top: 0.5rem; color: #334155; line-height: 1.5;">
                                            {{ $riwayat->keterangan }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                        @if(empty($pendaftar->riwayats) || $pendaftar->riwayats->isEmpty())
                            <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 1rem 0;">
                                Belum ada riwayat</div>
                        @endif
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
                        var content = document.getElementById(targetId);
                        var isOpen = content.classList.contains('open');

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
                setTimeout(function () {
                    lightboxImg.src = '';
                }, 300);
            }

            function confirmResendEmail() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Kirim Ulang Email?',
                        text: "Apakah Anda yakin ingin mengirim ulang Email Bukti Pendaftaran ke pendaftar ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1b6e4c',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Kirim Ulang!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('resend-email-form').submit();
                        }
                    });
                } else {
                    if (confirm("Apakah Anda yakin ingin mengirim ulang Email Bukti Pendaftaran ke pendaftar ini?")) {
                        document.getElementById('resend-email-form').submit();
                    }
                }
            }

            // --- AJAX Form Handlers for inline edits to avoid history pollution ---
            function handleAjaxForm(formId, onSuccess) {
                var form = document.getElementById(formId);
                if (!form) return;

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (typeof showLoading === 'function') showLoading();

                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (typeof hideLoading === 'function') hideLoading();
                            if (data.success) {
                                if (onSuccess) onSuccess(data);
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert(data.message);
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                                } else {
                                    alert(data.message || 'Terjadi kesalahan.');
                                }
                            }
                        })
                        .catch(err => {
                            if (typeof hideLoading === 'function') hideLoading();
                            console.error(err);
                            alert('Gagal menyimpan data.');
                        });
                });
            }

            // 1. Provinsi Form
            handleAjaxForm('provinsi-form', function (data) {
                document.querySelector('#provinsi-display span').textContent = data.provinsi_with_wilayah;
                document.getElementById('provinsi-form-container').style.display = 'none';
                document.getElementById('provinsi-display').style.display = 'flex';
            });

            // 2. Status Form (it has class status-form, let's grab it)
            var statusForm = document.querySelector('.status-form');
            if (statusForm) {
                statusForm.id = 'status-form'; // assign id dynamically if it doesn't have one
                handleAjaxForm('status-form'); // UI is already updated by the custom dropdown click
            }

            window.confirmDelete = function(formId, message) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(formId).submit();
                        }
                    });
                } else {
                    if (confirm(message)) {
                        document.getElementById(formId).submit();
                    }
                }
            };

            window.handleFileChange = function(input) {
                if (!input.accumulatedFiles) {
                    input.accumulatedFiles = new DataTransfer();
                }
                
                if (input.files && input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        let isDuplicate = false;
                        for (let j = 0; j < input.accumulatedFiles.files.length; j++) {
                            if (input.accumulatedFiles.files[j].name === input.files[i].name && 
                                input.accumulatedFiles.files[j].size === input.files[i].size) {
                                isDuplicate = true;
                                break;
                            }
                        }
                        if (!isDuplicate) {
                            input.accumulatedFiles.items.add(input.files[i]);
                        }
                    }
                }
                
                input.files = input.accumulatedFiles.files;
                window.renderFilePreview(input);
            };

            window.removeFile = function(btn, index) {
                const container = btn.closest('.file-preview-container');
                const form = container.closest('form');
                const input = form.querySelector('input[type="file"]');
                if (!input || !input.accumulatedFiles) return;
                
                const dt = new DataTransfer();
                const files = input.accumulatedFiles.files;
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                input.accumulatedFiles = dt;
                input.files = dt.files;
                window.renderFilePreview(input);
            };

            window.renderFilePreview = function(input) {
                let container = input.closest('.field').querySelector('.file-preview-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'file-preview-container';
                    input.closest('.field').appendChild(container);
                }
                
                container.innerHTML = '';
                const files = input.files;
                if (files && files.length > 0) {
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                        const sizeKB = (file.size / 1024).toFixed(1) + ' KB';
                        const sizeStr = file.size > 1024 * 1024 ? sizeMB : sizeKB;
                        
                        let iconHtml = '<i class="file alternate outline icon large" style="margin:0;"></i>';
                        let bgColor = '#10b981'; 
                        
                        if (file.type.startsWith('image/')) {
                            const objUrl = URL.createObjectURL(file);
                            iconHtml = `<img src="${objUrl}" alt="Preview" onload="URL.revokeObjectURL(this.src)">`;
                            bgColor = 'transparent';
                        } else if (file.type === 'application/pdf') {
                            iconHtml = '<i class="file pdf outline icon large" style="margin:0;"></i>';
                            bgColor = '#ef4444'; 
                        } else if (file.name.match(/\.(doc|docx)$/i)) {
                            iconHtml = '<i class="file word outline icon large" style="margin:0;"></i>';
                            bgColor = '#3b82f6'; 
                        }
                        
                        const card = document.createElement('div');
                        card.className = 'file-preview-card';
                        card.innerHTML = `
                            <div class="file-icon" style="background: ${bgColor}">${iconHtml}</div>
                            <div class="file-info">
                                <div class="file-name" title="${file.name}">${file.name}</div>
                                <div class="file-meta">${file.name.split('.').pop()} , ${sizeStr}</div>
                            </div>
                            <div class="file-remove" onclick="removeFile(this, ${i})">
                                <i class="times icon"></i>
                            </div>
                        `;
                        container.appendChild(card);
                    }
                }
            };

            document.addEventListener("wheel", function(event){
                if(document.activeElement.type === "number"){
                    event.preventDefault();
                }
            }, { passive: false });
        </script>
    @endpush

</x-volt-app>
