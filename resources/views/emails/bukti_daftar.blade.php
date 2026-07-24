<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran DPDRI Awards 2026</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3ecdd;
            color: #10131a;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e8ddc4;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(11, 42, 91, .05);
        }

        .header-title {
            color: #1b6e4c;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .main-title {
            font-size: 28px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .main-title span {
            color: #b8860b;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e8ddc4;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .label {
            font-size: 12px;
            color: #8a7f66;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .reg-id {
            font-size: 26px;
            font-weight: bold;
            color: #b8860b;
            margin: 10px 0;
            letter-spacing: 1px;
        }

        .info {
            font-size: 14px;
            color: #4b5262;
            margin-bottom: 5px;
        }

        .date {
            font-size: 12px;
            color: #6b7280;
            margin-top: 15px;
        }

        .footer {
            font-size: 13px;
            color: #8a7f66;
            margin-top: 30px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-title">BUKTI PENDAFTARAN</div>
        <h1 class="main-title">DPDRI <span>AWARDS 2026</span></h1>

        <p class="info" style="margin-bottom: 25px;">Terima kasih <strong>{{ $pendaftar->nama }}</strong> telah
            berpartisipasi. Pendaftaran Anda pada <strong>{{ $pendaftar->kategori }}</strong> telah kami terima.</p>

        <div class="card">
            <div class="label">NOMOR REGISTRASI</div>
            <div class="reg-id">{{ $pendaftar->nomor_registrasi }}</div>
            <div class="date">Disubmit pada: {{ $waktuSubmit }} WIB</div>
        </div>

        <p class="footer">Simpan email ini sebagai bukti pendaftaran yang sah.<br>Pendaftaran Anda akan
            diverifikasi oleh tim.<br><br>Terima Kasih,<br><strong>Panitia DPDRI Awards 2026</strong></p>
    </div>
</body>

</html>