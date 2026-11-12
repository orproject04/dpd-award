<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $certTitle }} - DPDRI Awards 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3ecdd;
            color: #10131a;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e8ddc4;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(11, 42, 91, .05);
        }

        .header-title {
            color: #1b6e4c;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .main-title {
            font-size: 26px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 24px;
            color: #10131a;
        }

        .main-title span {
            color: #b8860b;
        }

        .card {
            background-color: #faf8f3;
            border: 1px solid #e8ddc4;
            border-radius: 14px;
            padding: 22px;
            margin: 20px 0;
            text-align: left;
        }

        .row-label {
            font-size: 11px;
            color: #8a7f66;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .row-value {
            font-size: 15px;
            color: #10131a;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            background-color: #e6f4ee;
            color: #1b6e4c;
            border: 1px solid #b7e0cd;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .info {
            font-size: 14px;
            color: #4b5262;
            line-height: 1.65;
            margin: 0 0 14px 0;
        }

        .attachment-note {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 13px;
            padding: 12px 16px;
            border-radius: 10px;
            margin-top: 18px;
            line-height: 1.5;
        }

        .footer {
            font-size: 13px;
            color: #8a7f66;
            margin-top: 26px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-title">SERTIFIKAT PENGHARGAAN</div>
        <h1 class="main-title">DPDRI <span>AWARDS 2026</span></h1>

        <p class="info">
            Yth. Bapak/Ibu <strong>{{ $pendaftar->nama }}</strong>,<br>
            Selamat dan terima kasih atas partisipasi serta dedikasi Anda dalam ajang
            <strong>DPDRI Awards 2026</strong> pada kategori <strong>{{ $pendaftar->kategori }}</strong>.
        </p>

        <div class="card">
            <div class="row-label">Nomor Registrasi</div>
            <div class="row-value">{{ $pendaftar->nomor_registrasi }}</div>

            <div class="row-label">Nama Peserta</div>
            <div class="row-value">{{ $pendaftar->nama }}</div>

            <div class="row-label">Kategori</div>
            <div class="row-value">{{ $pendaftar->kategori }}</div>

            <div class="row-label">Capaian Sertifikat</div>
            <div><span class="badge">{{ $certTitle }}</span></div>
        </div>

        <div class="attachment-note">
            <strong>Lampiran Sertifikat PDF:</strong> File sertifikat resmi Anda telah kami lampirkan pada email ini.
            Anda juga dapat mengunduhnya kapan saja melalui fitur <strong>Lacak Pendaftaran</strong> di situs resmi kami.
        </div>

        <p class="footer">
            Hormat kami,<br>
            <strong>Panitia DPDRI Awards 2026</strong>
        </p>
    </div>
</body>

</html>

