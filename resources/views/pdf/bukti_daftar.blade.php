<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran - {{ $pendaftar->nomor_registrasi }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #10131a;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header .subtitle {
            color: #1b6e4c;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .header h1 {
            font-size: 20pt;
            font-weight: 900;
            margin: 0;
            color: #10131a;
        }

        .header h1 span {
            color: #b8860b;
        }

        .reg-box {
            border: 1px solid #e8ddc4;
            background-color: #ffffff;
            border-radius: 10px;
            text-align: center;
            padding: 10px;
            margin: 15px auto;
            width: 320px;
        }

        .reg-label {
            font-size: 7pt;
            color: #8a7f66;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .reg-number {
            font-size: 14pt;
            font-weight: bold;
            color: #b8860b;
            margin: 4px 0;
        }

        .reg-time {
            font-size: 7pt;
            color: #6b7280;
        }

        .main-card {
            border: 1px solid #e8ddc4;
            border-radius: 12px;
            padding: 20px;
            background-color: #ffffff;
        }

        .success-banner {
            background-color: #edf5f0;
            border: 1px solid #cce2d6;
            color: #1b6e4c;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section-panel {
            border: 1px solid #e8ddc4;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            background-color: #fcfbf7;
            padding: 10px 15px;
            border-bottom: 1px solid #e8ddc4;
            color: #1b6e4c;
            font-weight: bold;
            font-size: 10pt;
        }

        .section-body {
            padding: 15px;
            background-color: #ffffff;
        }

        table.grid-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.grid-table td {
            vertical-align: top;
            padding-bottom: 15px;
            width: 50%;
        }

        .field-label {
            font-size: 7pt;
            color: #8a7f66;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .field-value {
            font-size: 9.5pt;
            color: #10131a;
            font-weight: 500;
        }

        /* Dokumen Pribadi style */
        .doc-box {
            border: 1px solid #e8ddc4;
            background-color: #fdfcf9;
            border-radius: 6px;
            padding: 10px;
            display: inline-block;
            width: 45%;
            margin-right: 2%;
        }

        .doc-title {
            font-size: 7pt;
            color: #8a7f66;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .doc-value {
            font-size: 8.5pt;
            font-weight: bold;
            color: #10131a;
        }

        /* Capaian & Inovasi */
        .item-box {
            border: 1px solid #e8ddc4;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .item-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #f0eae1;
            padding-bottom: 5px;
        }

        .empty-text {
            font-size: 9pt;
            color: #8a7f66;
            font-style: italic;
            text-align: center;
            padding: 10px;
        }

        .checkbox-area {
            padding: 15px 5px 0 5px;
            font-size: 8.5pt;
            color: #4b5262;
            margin-top: 10px;
        }

        .checkbox-area strong {
            color: #1b6e4c;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="subtitle">BUKTI PENDAFTARAN</div>
        <h1>DPDRI <span>AWARDS 2026</span></h1>

        <div class="reg-box">
            <div class="reg-label">NOMOR REGISTRASI</div>
            <div class="reg-number">{{ $pendaftar->nomor_registrasi }}</div>
            <div class="reg-time">Diajukan pada: {{ $waktuSubmit }}</div>
        </div>
    </div>

    <div class="main-card">

        <div class="success-banner">
            Kategori Pilihan: {{ $pendaftar->kategori }}
        </div>

        <!-- Informasi Personal -->
        <div class="section-panel">
            <div class="section-header">Informasi Personal</div>
            <div class="section-body">
                <table class="grid-table">
                    <tr>
                        <td>
                            <div class="field-label">Kategori</div>
                            <div class="field-value">{{ $pendaftar->kategori }}</div>
                        </td>
                        <td>
                            <div class="field-label">Nama Lengkap</div>
                            <div class="field-value">{{ $pendaftar->nama }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="field-label">Tempat Lahir</div>
                            <div class="field-value">{{ $pendaftar->tempat_lahir }}</div>
                        </td>
                        <td>
                            <div class="field-label">Tanggal Lahir</div>
                            <div class="field-value">{{ $pendaftar->tanggal_lahir }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="field-label">Jenis Kelamin</div>
                            <div class="field-value">{{ $pendaftar->jenis_kelamin }}</div>
                        </td>
                        <td>
                            <div class="field-label">Pendidikan</div>
                            <div class="field-value">{{ $pendaftar->pendidikan }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="field-label">Alamat</div>
                            <div class="field-value">{{ $pendaftar->alamat }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="field-label">Nomor WhatsApp</div>
                            <div class="field-value">{{ $pendaftar->nomor_wa }}</div>
                        </td>
                        <td>
                            <div class="field-label">Alamat Email</div>
                            <div class="field-value">{{ $pendaftar->email }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Capaian & Inovasi -->
        <div class="section-panel">
            <div class="section-header">Capaian & Inovasi</div>
            <div class="section-body">
                @if(isset($pendaftar->kontribusi) && count($pendaftar->kontribusi) > 0)
                    @foreach($pendaftar->kontribusi as $index => $kontribusi)
                        <div class="item-box">
                            <div class="item-title">{{ $index + 1 }}. {{ $kontribusi->judul }}</div>
                            <table class="grid-table" style="margin-bottom: 0;">
                                <tr>
                                    <td colspan="2">
                                        <div class="field-label">Deskripsi Singkat</div>
                                        <div class="field-value">{{ $kontribusi->deskripsi }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-bottom: 0;">
                                        <div class="field-label">Dampak & Pencapaian</div>
                                        <div class="field-value">{{ $kontribusi->dampak }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                @else
                    <div class="empty-text">Belum ada data capaian.</div>
                @endif
            </div>
        </div>

        <!-- Penghargaan -->
        <div class="section-panel">
            <div class="section-header">Penghargaan</div>
            <div class="section-body">
                @if(isset($pendaftar->penghargaan) && count($pendaftar->penghargaan) > 0)
                    @foreach($pendaftar->penghargaan as $index => $penghargaan)
                        <div class="item-box">
                            <div class="item-title">{{ $index + 1 }}. {{ $penghargaan->nama_penghargaan }}</div>
                            <table class="grid-table" style="margin-bottom: 0;">
                                <tr>
                                    <td>
                                        <div class="field-label">Tingkat</div>
                                        <div class="field-value">{{ $penghargaan->tingkat }}</div>
                                    </td>
                                    <td>
                                        <div class="field-label">Tahun</div>
                                        <div class="field-value">{{ $penghargaan->tahun }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-bottom: 0;">
                                        <div class="field-label">Pemberi Penghargaan</div>
                                        <div class="field-value">{{ $penghargaan->pemberi }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                @else
                    <div class="empty-text">Belum ada data penghargaan.</div>
                @endif
            </div>
        </div>

        <div class="checkbox-area">
            <span
                style="display:inline-block; border: 1px solid #1b6e4c; background-color: #1b6e4c; width: 14px; height: 14px; text-align: center; line-height: 14px; margin-right: 5px; color: #ffffff; font-weight: bold; font-family: Arial, sans-serif; border-radius: 2px;">V</span>
            Saya menyatakan bahwa seluruh data yang diisi adalah benar dan menyetujui <strong>syarat &
                ketentuan</strong> DPDRI AWARDS 2026.
        </div>

    </div>
</body>

</html>