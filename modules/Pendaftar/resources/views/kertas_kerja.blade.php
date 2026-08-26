<x-volt-app :title="'Kertas Kerja Penilaian - ' . $pendaftar->nama">
    @push('style')
        <style>
            .kk-container {
                font-family: inherit;
            }

            .kk-header-card {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                color: #0f172a;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .kk-user-info {
                display: flex;
                align-items: center;
                gap: 1.25rem;
            }

            .kk-user-avatar {
                width: 64px;
                height: 64px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #cbd5e1;
                background: #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                color: #64748b;
                flex-shrink: 0;
            }

            .kk-user-details h2 {
                margin: 0;
                font-size: 1.35rem;
                font-weight: 700;
                color: #0f172a;
            }

            .kk-user-details p {
                margin: 0.25rem 0 0 0;
                color: #475569;
                font-size: 0.9rem;
            }

            .kk-score-box {
                background: #f0f9ff;
                border: 1.5px solid #bae6fd;
                border-radius: 10px;
                padding: 0.75rem 1.25rem;
                text-align: right;
                min-width: 140px;
            }

            .kk-score-box .score-val {
                font-size: 2.2rem;
                font-weight: 800;
                line-height: 1;
                color: #0284c7;
            }

            .kk-score-box .score-lbl {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #0369a1;
                font-weight: 600;
                margin-top: 0.25rem;
            }

            .kk-table-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                overflow: hidden;
                margin-bottom: 2rem;
            }

            .kk-table {
                width: 100%;
                border-collapse: collapse;
            }

            .kk-table th {
                background: #f8fafc;
                color: #334155;
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                padding: 0.85rem 0.75rem;
                border-bottom: 2px solid #e2e8f0;
                vertical-align: middle;
            }

            .kk-table td {
                padding: 0.75rem;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: top;
                font-size: 0.9rem;
            }

            .kk-table td.cell-textarea {
                height: 1px;
                padding: 0.5rem;
            }

            .kk-textarea {
                width: 100%;
                height: 100%;
                min-height: 80px;
                font-size: 0.85rem;
                padding: 0.5rem;
                border: 1.5px solid #cbd5e1;
                border-radius: 6px;
                resize: vertical;
                box-sizing: border-box;
                font-family: inherit;
                color: #0f172a;
                background-color: #ffffff;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
            }

            .kk-textarea:focus {
                outline: none;
                border-color: #0284c7;
                box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            }

            .kk-table tr:hover td {
                background-color: #fafafa;
            }

            .nilai-input {
                width: 80px !important;
                text-align: center;
                font-weight: 700;
                font-size: 1.1rem;
                padding: 0.4rem !important;
                border-radius: 6px !important;
                border: 1.5px solid #cbd5e1 !important;
            }

            .nilai-input:focus {
                border-color: #0284c7 !important;
                box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
            }

            /* Hilangkan panah spinner pada input number */
            input[type=number]::-webkit-inner-spin-button, 
            input[type=number]::-webkit-outer-spin-button { 
                -webkit-appearance: none; 
                margin: 0; 
            }
            input[type=number] {
                -moz-appearance: textfield; /* Firefox */
            }

            .total-badge {
                display: inline-block;
                padding: 0.35rem 0.65rem;
                border-radius: 6px;
                font-weight: 700;
                font-size: 1rem;
                background: #e0f2fe;
                color: #0369a1;
                min-width: 60px;
                text-align: center;
            }

            .data-dukung-tag {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                padding: 0.4rem 0.6rem;
                margin-bottom: 0.4rem;
                font-size: 0.82rem;
                color: #334155;
            }

            .data-dukung-tag .title {
                font-weight: 600;
                color: #0f172a;
            }

            .data-dukung-tag .catatan {
                font-size: 0.78rem;
                color: #64748b;
                margin-top: 0.2rem;
                background: #ffffff;
                padding: 0.2rem 0.4rem;
                border-radius: 4px;
                border: 1px dashed #cbd5e1;
            }

            /* Modal Styles */
            .dd-modal {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: rgba(0, 0, 0, 0.65);
                backdrop-filter: blur(4px);
                align-items: center;
                justify-content: center;
            }

            .dd-modal.active {
                display: flex;
            }

            .dd-modal-content {
                background: #fff;
                width: 92%;
                max-width: 840px;
                max-height: 88vh;
                border-radius: 12px;
                display: flex;
                flex-direction: column;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
                overflow: hidden;
            }

            .dd-modal-header {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f8fafc;
            }

            .dd-modal-header h3 {
                margin: 0;
                font-size: 1.1rem;
                color: #0f172a;
            }

            .dd-modal-body {
                padding: 1.5rem;
                overflow-y: auto;
                flex: 1;
            }

            .dd-modal-footer {
                padding: 1rem 1.5rem;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: flex-end;
                gap: 0.5rem;
                background: #f8fafc;
            }

            .dd-item-card {
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                transition: all 0.2s ease;
                background: #fff;
            }

            .dd-item-card.selected {
                border-color: #0284c7 !important;
                background: #f0f9ff !important;
            }

            .dd-note-textarea {
                width: 100%;
                font-size: 0.85rem;
                padding: 0.5rem;
                border-radius: 6px;
                border: 1.5px solid #cbd5e1;
                resize: vertical;
                font-family: inherit;
                color: #0f172a;
                background: #ffffff;
                box-sizing: border-box;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
            }

            .dd-note-textarea:focus {
                outline: none;
                border-color: #0284c7;
                box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            }

            @media print {
                /* Sembunyikan elemen UI yang tidak perlu saat di-print/PDF */
                .ui.button, nav, header, footer, .sidebar, .autosave-status-container { 
                    display: none !important; 
                }
                body {
                    background: #fff !important;
                }
                .kk-header-card, .kk-table-card { 
                    box-shadow: none !important; 
                    border: 1px solid #cbd5e1 !important; 
                    margin: 0 !important;
                }
                .kk-table tr {
                    page-break-inside: avoid;
                }
                .kk-table tfoot {
                    display: table-row-group;
                }
                .kk-table th {
                    background-color: #f1f5f9 !important;
                    -webkit-print-color-adjust: exact;
                }
                .kk-textarea, .nilai-input {
                    border: none !important;
                    background: transparent !important;
                    resize: none !important;
                    box-shadow: none !important;
                    padding: 0 !important;
                }
                .total-badge {
                    border: 1px solid #0284c7;
                }
                @page { 
                    size: landscape; 
                    margin: 10mm; 
                }
            }
        </style>
    @endpush

    <div class="kk-container">

        @if (session('success'))
            <div class="ui success message m-b-2">
                <i class="close icon"></i>
                <div class="header">Berhasil</div>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Banner Header Pendaftar -->
        <div class="kk-header-card">
            <div class="kk-user-info">
                @if ($pendaftar->foto && file_exists($pendaftar->foto))
                    <img src="{{ route('modules::pendaftar.file', ['path' => $pendaftar->getRawOriginal('foto')]) }}"
                        class="kk-user-avatar" alt="Foto">
                @else
                    <div class="kk-user-avatar"><i class="user icon" style="margin:0"></i></div>
                @endif
                <div class="kk-user-details">
                    <h2>{{ $pendaftar->nama }}</h2>
                    <p style="margin-bottom: 0.5rem;">
                        <span class="ui label teal mini" style="margin-right: 0.5rem;">{{ $pendaftar->kategori }}</span>
                        <strong>No. Reg:</strong> {{ $pendaftar->nomor_registrasi }} |
                        <strong>Provinsi:</strong> {{ $pendaftar->provinsi_with_wilayah }}
                    </p>
                    {{-- <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.4rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: #0369a1;"><i class="icon tasks"></i>
                            Tahap Penilaian:</label>
                        <select
                            onchange="window.location.href='{{ route('modules::pendaftar.kertas-kerja', $pendaftar->id) }}?tahap=' + encodeURIComponent(this.value)"
                            class="ui dropdown selection compact"
                            style="font-size: 0.85rem; padding: 0.35rem 0.75rem; border-color: #0284c7; background: #f0f9ff; font-weight: 600;">
                            @foreach ($availableTahaps as $thp)
                                <option value="{{ $thp }}" {{ $selectedTahap === $thp ? 'selected' : '' }}>
                                    {{ $thp }} {{ in_array($thp, $savedTahapList) ? ' ✓' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div class="kk-score-box">
                    <div class="score-val" id="top-grand-total-display">{{ number_format($totalNilaiAkhir, 2) }}</div>
                    <div class="score-lbl">Nilai Akhir
                        {{-- ({{ $selectedTahap }}) --}}
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('modules::pendaftar.store-kertas-kerja', $pendaftar->id) }}" method="POST"
            id="kk-form">
            @csrf
            <input type="hidden" name="tahap" value="{{ $selectedTahap }}">

            <!-- Bar Tombol Aksi di Atas Tabel -->
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <a href="{{ route('modules::pendaftar.show', $pendaftar->id) }}" class="ui button basic">
                    <i class="icon arrow left"></i> Kembali ke Detail Pendaftar
                </a>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="autosave-status-container"
                        style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-right: 0.25rem;">
                        <span class="status-icon"></span>
                        <span class="status-text"></span>
                    </div>
                    <button type="button" class="ui button orange" onclick="window.print()">
                        <i class="icon file pdf outline"></i> Cetak PDF
                    </button>
                    <a href="{{ route('modules::pendaftar.export-kertas-kerja-excel', ['pendaftar' => $pendaftar->id, 'tahap' => $selectedTahap]) }}"
                        class="ui button green" data-no-loader="true" target="_blank">
                        <i class="icon file excel"></i> Cetak Excel
                    </a>
                    <button type="submit" class="ui button primary">
                        <i class="icon save"></i> Simpan Penilaian
                    </button>
                </div>
            </div>

            <div class="kk-table-card">
                <table class="kk-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th style="width: 200px;">Aspek</th>
                            <th>Dimensi</th>
                            <th style="width: 100px; text-align: center;">Nilai (10-100)</th>
                            <th style="width: 70px; text-align: center;">Bobot</th>
                            <th style="width: 90px; text-align: center;">Total</th>
                            <th style="width: 180px;">Catatan Juri</th>
                            <th style="width: 160px;">Tracking Media</th>
                            <th style="width: 220px;">Data Dukung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                            <tr>
                                <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][kategori_aspek_id]"
                                        value="{{ $item['kategori_aspek_id'] }}">
                                    <strong style="color: #0f172a;">{{ $item['aspek'] }}</strong>
                                </td>
                                <td style="color: #475569; white-space: pre-line; font-size: 0.85rem;">
                                    {{ $item['dimensi'] ?: '-' }}</td>
                                <td style="text-align: center;">
                                    <input type="number" name="items[{{ $index }}][nilai]"
                                        value="{{ $item['nilai'] }}" min="10" max="100"
                                        class="ui input nilai-input" data-index="{{ $index }}"
                                        data-bobot="{{ $item['bobot'] }}" placeholder="10-100"
                                        onwheel="this.blur()">
                                </td>
                                <td style="text-align: center;">
                                    <span class="ui badge basic label">{{ $item['bobot'] }}%</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="total-badge" id="total-display-{{ $index }}">
                                        {{ number_format($item['total'] ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="cell-textarea">
                                    <textarea name="items[{{ $index }}][catatan_juri]" class="kk-textarea" placeholder="Catatan juri...">{{ $item['catatan_juri'] }}</textarea>
                                </td>
                                <td class="cell-textarea">
                                    <textarea name="items[{{ $index }}][tracking_media]" class="kk-textarea" placeholder="Link / Catatan media...">{{ $item['tracking_media'] }}</textarea>
                                </td>
                                <td>
                                    <div id="dd-preview-container-{{ $index }}">
                                        @foreach ($item['data_dukung'] as $ddIndex => $dd)
                                            @php
                                                $ddItemKey = $dd['item_key'] ?? ($dd['kontribusi_id'] ?? '');
                                                $ddBukti = $dd['bukti'] ?? '';
                                                $ddExt = strtolower(pathinfo($ddBukti, PATHINFO_EXTENSION));
                                                $ddIsImage = in_array($ddExt, [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'webp',
                                                    'svg',
                                                ]);
                                                $ddIsPdf = $ddExt === 'pdf';
                                                $ddUrl = $ddBukti
                                                    ? route('modules::pendaftar.file', ['path' => $ddBukti])
                                                    : '';
                                            @endphp
                                            <div class="data-dukung-tag">
                                                <div class="title"><i class="icon file text grey"></i>
                                                    {{ $dd['title'] }}</div>
                                                @if ($ddUrl)
                                                    @if ($ddIsImage)
                                                        <a href="{{ $ddUrl }}" target="_blank">
                                                            <img src="{{ $ddUrl }}"
                                                                style="max-height: 80px; max-width: 100%; border-radius: 4px; margin: 0.3rem 0; display: block; border: 1px solid #cbd5e1; object-fit: contain;">
                                                        </a>
                                                    @elseif($ddIsPdf)
                                                        <a href="{{ $ddUrl }}" target="_blank"
                                                            class="ui label mini red basic"
                                                            style="margin: 0.3rem 0; display: inline-block;">
                                                            <i class="icon file pdf"></i> Lihat PDF
                                                        </a>
                                                    @else
                                                        <a href="{{ $ddUrl }}" target="_blank"
                                                            class="ui label mini basic"
                                                            style="margin: 0.3rem 0; display: inline-block;">
                                                            <i class="icon file"></i> Unduh File
                                                        </a>
                                                    @endif
                                                @endif
                                                @if (!empty($dd['catatan']))
                                                    <div class="catatan" style="white-space: pre-line;">
                                                        "{{ $dd['catatan'] }}"</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="ui button mini basic teal"
                                        style="margin-top: 0.3rem; width: 100%;"
                                        onclick="openDataDukungModal({{ $index }})">
                                        <i class="icon plus"></i> Pilih / Edit Data Dukung
                                    </button>

                                    <!-- Hidden Inputs Container for Data Dukung -->
                                    <div id="dd-inputs-container-{{ $index }}">
                                        @foreach ($item['data_dukung'] as $ddIndex => $dd)
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][selected]"
                                                value="1">
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][item_key]"
                                                value="{{ $dd['item_key'] ?? ($dd['kontribusi_id'] ?? '') }}">
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][kontribusi_id]"
                                                value="{{ $dd['kontribusi_id'] ?? '' }}">
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][title]"
                                                value="{{ $dd['title'] ?? '' }}">
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][bukti]"
                                                value="{{ $dd['bukti'] ?? '' }}">
                                            <input type="hidden"
                                                name="items[{{ $index }}][data_dukung][{{ $ddIndex }}][catatan]"
                                                value="{{ $dd['catatan'] ?? '' }}">
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="center aligned" style="padding: 2rem; color: #64748b;">
                                    Belum ada aspek penilaian untuk kategori {{ $pendaftar->kategori }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td colspan="4" style="text-align: right; font-size: 1rem; color: #0f172a;">TOTAL BOBOT
                                &
                                NILAI AKHIR:</td>
                            <td style="text-align: center; font-size: 1rem; color: #0284c7;">{{ $totalBobot }}%</td>
                            <td style="text-align: center;">
                                <span class="total-badge" id="grand-total-display"
                                    style="background: #0284c7; color: #fff; font-size: 1.1rem;">
                                    {{ number_format($totalNilaiAkhir, 2) }}
                                </span>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <a href="{{ route('modules::pendaftar.show', $pendaftar->id) }}" class="ui button large basic">
                    <i class="icon arrow left"></i> Kembali ke Detail Pendaftar
                </a>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="autosave-status-container"
                        style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-right: 0.25rem;">
                        <span class="status-icon"></span>
                        <span class="status-text"></span>
                    </div>
                    <button type="button" class="ui button large orange" onclick="window.print()">
                        <i class="icon file pdf outline"></i> Cetak PDF
                    </button>
                    <a href="{{ route('modules::pendaftar.export-kertas-kerja-excel', ['pendaftar' => $pendaftar->id, 'tahap' => $selectedTahap]) }}"
                        class="ui button large green" data-no-loader="true" target="_blank">
                        <i class="icon file excel"></i> Cetak Excel
                    </a>
                    <button type="submit" class="ui button large primary">
                        <i class="icon save"></i> Simpan Penilaian
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Selector Data Dukung -->
    <div class="dd-modal" id="ddModal">
        <div class="dd-modal-content">
            <div class="dd-modal-header">
                <h3><i class="icon folder open teal"></i> Pilih Data Dukung untuk Aspek: <span id="modal-aspek-title"
                        style="color: #0284c7;"></span></h3>
                <button type="button" class="ui icon button basic mini" onclick="closeDataDukungModal()"><i
                        class="icon close"></i></button>
            </div>
            <div class="dd-modal-body">
                <p style="color: #64748b; font-size: 0.88rem; margin-bottom: 1.25rem;">
                    Pilih kontribusi atau file bukti dukung spesifik di bawah ini yang relevan dengan aspek penilaian
                    ini, dan berikan catatan khusus untuk kontribusi terkait jika diperlukan:
                </p>

                <div id="modal-kontribusi-list">
                    @forelse($pendaftar->kontribusi as $kIndex => $kontribusi)
                        <div class="dd-kontribusi-card" id="modal-card-{{ $kontribusi->id }}"
                            style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 1.1rem; margin-bottom: 1.25rem; transition: all 0.2s ease;">

                            {{-- Header Kontribusi --}}
                            <div style="font-weight: 700; color: #0f172a; font-size: 1rem; margin-bottom: 0.35rem;">
                                <i class="icon folder open teal"></i> {{ $kontribusi->judul }}
                            </div>

                            @if (!empty($kontribusi->deskripsi))
                                <p
                                    style="color: #475569; font-size: 0.85rem; margin: 0 0 0.85rem 0; white-space: pre-line; background: #f8fafc; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                    {{ $kontribusi->deskripsi }}
                                </p>
                            @endif

                            {{-- File Selection List --}}
                            @if (!empty($kontribusi->bukti_dukung) && is_array($kontribusi->bukti_dukung) && count($kontribusi->bukti_dukung) > 0)
                                <div
                                    style="font-size: 0.78rem; font-weight: 700; color: #0369a1; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                                    Pilih Bukti Dukung Spesifik:
                                </div>
                                <div
                                    style="display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 0.85rem;">
                                    @foreach ($kontribusi->bukti_dukung as $bIndex => $bPath)
                                        @php
                                            $itemKey = $kontribusi->id . '_' . $bIndex;
                                            $fileUrl = route('modules::pendaftar.file', ['path' => $bPath]);
                                            $fileName = basename($bPath);
                                            $ext = strtolower(pathinfo($bPath, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                            $isPdf = $ext === 'pdf';
                                            $itemTitle =
                                                $kontribusi->judul .
                                                ' - Bukti #' .
                                                ($bIndex + 1) .
                                                ' (' .
                                                $fileName .
                                                ')';
                                        @endphp
                                        <div class="dd-file-item"
                                            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem;">
                                            <div style="display: flex; align-items: flex-start; gap: 0.65rem;">
                                                <input type="checkbox" class="dd-checkbox"
                                                    id="chk-{{ $itemKey }}" data-id="{{ $itemKey }}"
                                                    data-kontribusi-id="{{ $kontribusi->id }}"
                                                    data-title="{{ $itemTitle }}"
                                                    data-bukti="{{ $bPath }}"
                                                    data-bukti-url="{{ $fileUrl }}"
                                                    data-file-type="{{ $isImage ? 'image' : ($isPdf ? 'pdf' : 'file') }}"
                                                    onchange="updateKontribusiCardState('{{ $kontribusi->id }}')"
                                                    style="width: 18px; height: 18px; margin-top: 2px; cursor: pointer;">
                                                <div style="flex: 1;">
                                                    <label for="chk-{{ $itemKey }}"
                                                        style="font-weight: 600; color: #0f172a; cursor: pointer; font-size: 0.9rem; display: block;">
                                                        <i
                                                            class="icon file {{ $isImage ? 'image outline green' : ($isPdf ? 'pdf red' : 'text blue') }}"></i>
                                                        Bukti #{{ $bIndex + 1 }}: {{ $fileName }}
                                                    </label>

                                                    {{-- Image / PDF Preview --}}
                                                    @if ($isImage)
                                                        <div style="margin-top: 0.4rem;">
                                                            <a href="{{ $fileUrl }}" target="_blank"
                                                                title="Klik untuk lihat gambar penuh">
                                                                <img src="{{ $fileUrl }}"
                                                                    alt="{{ $fileName }}"
                                                                    style="max-height: 130px; max-width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; object-fit: contain; background: #ffffff; cursor: zoom-in; display: block;">
                                                            </a>
                                                        </div>
                                                    @elseif ($isPdf)
                                                        <div
                                                            style="margin-top: 0.4rem; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; background: #ffffff;">
                                                            <div
                                                                style="background: #fee2e2; color: #991b1b; padding: 0.3rem 0.6rem; font-size: 0.75rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center;">
                                                                <span><i class="icon file pdf"></i> PDF:
                                                                    {{ $fileName }}</span>
                                                                <a href="{{ $fileUrl }}" target="_blank"
                                                                    style="color: #991b1b; text-decoration: underline;"><i
                                                                        class="icon external link"></i> Buka Tab
                                                                    Baru</a>
                                                            </div>
                                                            <iframe src="{{ $fileUrl }}"
                                                                style="width: 100%; height: 150px; border: none;"></iframe>
                                                        </div>
                                                    @else
                                                        <div style="margin-top: 0.3rem;">
                                                            <a href="{{ $fileUrl }}" target="_blank"
                                                                class="ui label basic mini">
                                                                <i class="icon download"></i> Unduh File
                                                                {{ $fileName }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Catatan Per Bukti Dukung --}}
                                            <div style="margin-top: 0.75rem; background: #ffffff; padding: 0.6rem; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                                <label for="note-bukti-{{ $itemKey }}" style="font-size: 0.75rem; font-weight: 700; color: #334155; display: block; margin-bottom: 0.25rem;">
                                                    <i class="icon pencil text teal"></i> Catatan Juri untuk Bukti Dukung Ini:
                                                </label>
                                                <textarea class="dd-note-textarea" id="note-bukti-{{ $itemKey }}" rows="2"
                                                    placeholder="Tuliskan catatan khusus juri untuk bukti dukung ini..."></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- Kontribusi without attached files --}}
                                @php
                                    $itemKey = $kontribusi->id . '_main';
                                    $itemTitle = $kontribusi->judul;
                                @endphp
                                <div class="dd-file-item"
                                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.85rem;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <input type="checkbox" class="dd-checkbox" id="chk-{{ $itemKey }}"
                                            data-id="{{ $itemKey }}" data-kontribusi-id="{{ $kontribusi->id }}"
                                            data-title="{{ $itemTitle }}" data-bukti="" data-bukti-url=""
                                            data-file-type="text"
                                            onchange="updateKontribusiCardState('{{ $kontribusi->id }}')"
                                            style="width: 18px; height: 18px; cursor: pointer;">
                                        <label for="chk-{{ $itemKey }}"
                                            style="font-weight: 600; color: #0f172a; cursor: pointer; font-size: 0.9rem;">
                                            Pilih Kontribusi Ini Sebagai Data Dukung
                                        </label>
                                    </div>
                                    {{-- Catatan Per Kontribusi (Tanpa File) --}}
                                    <div style="margin-top: 0.75rem; background: #ffffff; padding: 0.6rem; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                        <label for="note-bukti-{{ $itemKey }}" style="font-size: 0.75rem; font-weight: 700; color: #334155; display: block; margin-bottom: 0.25rem;">
                                            <i class="icon pencil text teal"></i> Catatan Juri untuk Bukti Dukung Ini:
                                        </label>
                                        <textarea class="dd-note-textarea" id="note-bukti-{{ $itemKey }}" rows="2"
                                            placeholder="Tuliskan catatan khusus juri..."></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                            <i class="icon info circle large"></i><br>
                            Pendaftar belum mengisi data kontribusi.
                        </div>
                    @endforelse
                </div>

                <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 1.5rem 0;">

                {{-- Opsi Catatan Tambahan Bebas --}}
                <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 1.1rem; margin-bottom: 0.5rem;">
                    <label for="note-bebas" style="font-size: 0.95rem; font-weight: 700; color: #0f172a; display: block; margin-bottom: 0.5rem;">
                        <i class="icon comment alternate outline teal"></i> Catatan Lainnya untuk Aspek Ini:
                    </label>
                    <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 0.75rem;">
                        Gunakan kolom ini jika ada catatan wawancara atau fakta lain yang tidak bersumber dari lampiran file di atas.
                    </p>
                    <textarea class="dd-note-textarea" id="note-bebas" rows="3"
                        placeholder="Tuliskan catatan lainnya di sini..."></textarea>
                </div>
            </div>
            <div class="dd-modal-footer">
                <button type="button" class="ui button basic" onclick="closeDataDukungModal()">Batal</button>
                <button type="button" class="ui button primary" onclick="saveDataDukungModal()">Simpan Pilihan Data
                    Dukung</button>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            let currentAspectIndex = null;

            document.addEventListener('DOMContentLoaded', function() {
                // Auto calculate total on score input
                const scoreInputs = document.querySelectorAll('.nilai-input');
                scoreInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        calculateTotals();
                    });
                });

                calculateTotals();
            });

            function calculateTotals() {
                let grandTotal = 0;
                const scoreInputs = document.querySelectorAll('.nilai-input');

                scoreInputs.forEach(input => {
                    const index = input.getAttribute('data-index');
                    const bobot = parseFloat(input.getAttribute('data-bobot')) || 0;
                    let val = parseFloat(input.value);

                    if (!isNaN(val)) {
                        if (val > 100) val = 100;
                        if (val < 0) val = 0;
                        const rowTotal = (val * bobot) / 100;
                        document.getElementById('total-display-' + index).innerText = rowTotal.toFixed(2);
                        grandTotal += rowTotal;
                    } else {
                        document.getElementById('total-display-' + index).innerText = '0.00';
                    }
                });

                document.getElementById('grand-total-display').innerText = grandTotal.toFixed(2);
                document.getElementById('top-grand-total-display').innerText = grandTotal.toFixed(2);
            }

            function updateKontribusiCardState(kontribusiId) {
                const card = document.getElementById('modal-card-' + kontribusiId);
                if (!card) return;
                const checked = card.querySelectorAll('.dd-checkbox:checked').length > 0;
                if (checked) {
                    card.style.borderColor = '#0284c7';
                    card.style.backgroundColor = '#f0f9ff';
                } else {
                    card.style.borderColor = '#cbd5e1';
                    card.style.backgroundColor = '#ffffff';
                }
            }

            function openDataDukungModal(index) {
                currentAspectIndex = index;
                const aspekTitleInput = document.querySelector(`input[name="items[${index}][kategori_aspek_id]"]`);
                const rowAspekText = aspekTitleInput.closest('tr').querySelector('strong').innerText;

                document.getElementById('modal-aspek-title').innerText = rowAspekText;

                // Reset all checkboxes, note textareas, and card styles in modal
                document.querySelectorAll('.dd-checkbox').forEach(chk => chk.checked = false);
                document.querySelectorAll('.dd-note-textarea').forEach(note => note.value = '');
                document.querySelectorAll('.dd-kontribusi-card').forEach(card => {
                    card.style.borderColor = '#cbd5e1';
                    card.style.backgroundColor = '#ffffff';
                });

                // Reset custom note
                document.getElementById('note-bebas').value = '';

                // Read current hidden inputs for this aspect row
                const container = document.getElementById('dd-inputs-container-' + index);
                const hiddenInputs = container.querySelectorAll('input[name*="[item_key]"]');

                hiddenInputs.forEach((input, hIdx) => {
                    const itemKey = input.value;
                    const savedNoteInput = container.querySelector(
                        `input[name="items[${index}][data_dukung][${hIdx}][catatan]"]`
                    );

                    if (itemKey === 'custom') {
                        if (savedNoteInput && savedNoteInput.value) {
                            document.getElementById('note-bebas').value = savedNoteInput.value;
                        }
                        return;
                    }

                    const chk = document.getElementById('chk-' + itemKey);
                    if (chk) {
                        chk.checked = true;
                        const kontribusiId = chk.getAttribute('data-kontribusi-id');
                        updateKontribusiCardState(kontribusiId);

                        const noteTextarea = document.getElementById('note-bukti-' + itemKey);
                        if (noteTextarea && savedNoteInput && savedNoteInput.value) {
                            noteTextarea.value = savedNoteInput.value;
                        }
                    }
                });

                document.getElementById('ddModal').classList.add('active');
            }

            function closeDataDukungModal() {
                document.getElementById('ddModal').classList.remove('active');
                currentAspectIndex = null;
            }

            function saveDataDukungModal() {
                if (currentAspectIndex === null) return;

                const index = currentAspectIndex;
                const inputsContainer = document.getElementById('dd-inputs-container-' + index);
                const previewContainer = document.getElementById('dd-preview-container-' + index);

                inputsContainer.innerHTML = '';
                previewContainer.innerHTML = '';

                let ddIndex = 0;
                document.querySelectorAll('.dd-checkbox:checked').forEach(chk => {
                    const itemKey = chk.getAttribute('data-id');
                    const kontribusiId = chk.getAttribute('data-kontribusi-id');
                    const title = chk.getAttribute('data-title');
                    const bukti = chk.getAttribute('data-bukti');
                    const buktiUrl = chk.getAttribute('data-bukti-url');
                    const fileType = chk.getAttribute('data-file-type');

                    // Note per Bukti Dukung!
                    const note = document.getElementById('note-bukti-' + itemKey)?.value || '';

                    // Create hidden inputs
                    inputsContainer.innerHTML += `
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][selected]" value="1">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][item_key]" value="${escapeHtml(itemKey)}">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][kontribusi_id]" value="${escapeHtml(kontribusiId)}">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][title]" value="${escapeHtml(title)}">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][bukti]" value="${escapeHtml(bukti)}">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][catatan]" value="${escapeHtml(note)}">
                    `;

                    // Create preview tag in table cell
                    let previewMediaHtml = '';
                    if (buktiUrl) {
                        if (fileType === 'image') {
                            previewMediaHtml =
                                `<a href="${buktiUrl}" target="_blank"><img src="${buktiUrl}" style="max-height: 80px; max-width: 100%; border-radius: 4px; margin: 0.3rem 0; display: block; border: 1px solid #cbd5e1; object-fit: contain;"></a>`;
                        } else if (fileType === 'pdf') {
                            previewMediaHtml =
                                `<a href="${buktiUrl}" target="_blank" class="ui label mini red basic" style="margin: 0.3rem 0; display: inline-block;"><i class="icon file pdf"></i> Lihat PDF</a>`;
                        } else {
                            previewMediaHtml =
                                `<a href="${buktiUrl}" target="_blank" class="ui label mini basic" style="margin: 0.3rem 0; display: inline-block;"><i class="icon download"></i> Unduh File</a>`;
                        }
                    }

                    previewContainer.innerHTML += `
                        <div class="data-dukung-tag">
                            <div class="title"><i class="icon file text grey"></i> ${escapeHtml(title)}</div>
                            ${previewMediaHtml}
                            ${note ? `<div class="catatan" style="white-space: pre-line;">"${escapeHtml(note)}"</div>` : ''}
                        </div>
                    `;

                    ddIndex++;
                });

                // Tambahkan Catatan Bebas
                const noteBebas = document.getElementById('note-bebas').value;
                if (noteBebas.trim() !== '') {
                    inputsContainer.innerHTML += `
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][selected]" value="1">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][item_key]" value="custom">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][kontribusi_id]" value="">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][title]" value="Catatan Lainnya">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][bukti]" value="">
                        <input type="hidden" name="items[${index}][data_dukung][${ddIndex}][catatan]" value="${escapeHtml(noteBebas)}">
                    `;

                    previewContainer.innerHTML += `
                        <div class="data-dukung-tag">
                            <div class="title"><i class="icon comment alternate outline teal"></i> Catatan Lainnya</div>
                            <div class="catatan" style="white-space: pre-line;">"${escapeHtml(noteBebas)}"</div>
                        </div>
                    `;
                }

                closeDataDukungModal();
                triggerAutoSave();
            }

            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            /* ── Auto-Save System ─────────────────────────────────── */
            let autoSaveTimer = null;
            const AUTO_SAVE_DELAY = 1500; // 1.5 seconds delay

            document.addEventListener('DOMContentLoaded', function() {
                const kkForm = document.getElementById('kk-form');
                if (kkForm) {
                    kkForm.querySelectorAll('input, textarea, select').forEach(element => {
                        element.addEventListener('input', triggerAutoSave);
                        element.addEventListener('change', triggerAutoSave);
                    });
                }
            });

            function triggerAutoSave() {
                clearTimeout(autoSaveTimer);
                updateAutoSaveStatus('pending', 'Menulis...');
                autoSaveTimer = setTimeout(performAutoSave, AUTO_SAVE_DELAY);
            }

            function performAutoSave() {
                const form = document.getElementById('kk-form');
                if (!form) return;

                updateAutoSaveStatus('saving', 'Menyimpan...');

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error: ' + response.statusText);
                        return response.json();
                    })
                    .then(data => {
                        const nowStr = new Date().toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                        updateAutoSaveStatus('saved', 'Tersimpan ' + nowStr);
                    })
                    .catch(err => {
                        console.error('Autosave error:', err);
                        updateAutoSaveStatus('error', 'Gagal menyimpan otomatis');
                    });
            }

            function updateAutoSaveStatus(state, text) {
                const containers = document.querySelectorAll('.autosave-status-container');
                containers.forEach(container => {
                    const iconEl = container.querySelector('.status-icon');
                    const textEl = container.querySelector('.status-text');
                    if (!iconEl || !textEl) return;

                    if (state === 'pending') {
                        iconEl.innerHTML = '<i class="icon edit outline orange"></i>';
                        textEl.innerText = text;
                        textEl.style.color = '#ea580c';
                    } else if (state === 'saving') {
                        iconEl.innerHTML = '<i class="icon sync spinner loading blue"></i>';
                        textEl.innerText = text;
                        textEl.style.color = '#0284c7';
                    } else if (state === 'saved') {
                        iconEl.innerHTML = '<i class="icon check circle green"></i>';
                        textEl.innerText = text;
                        textEl.style.color = '#16a34a';
                    } else if (state === 'error') {
                        iconEl.innerHTML = '<i class="icon warning circle red"></i>';
                        textEl.innerText = text;
                        textEl.style.color = '#dc2626';
                    }
                });
            }
        </script>
    @endpush
</x-volt-app>
