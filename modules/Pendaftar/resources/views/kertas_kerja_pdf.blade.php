<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kertas Kerja - {{ $pendaftar->nama }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .user-info {
            width: 100%;
        }
        .user-info td {
            vertical-align: middle;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .header-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }
        .score-box {
            text-align: right;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 8px 12px;
            display: inline-block;
            float: right;
        }
        .score-lbl {
            font-size: 9px;
            text-transform: uppercase;
            color: #0369a1;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .score-val {
            font-size: 20px;
            font-weight: bold;
            color: #0284c7;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        table.data-table tr {
            page-break-inside: avoid;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #0284c7; }
        .bg-light-blue { background-color: #e0f2fe; }
        .bg-blue { background-color: #0284c7; color: white; }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .badge-total {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #0284c7;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
        
        .bukti-item {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 6px;
        }
        .bukti-item:last-child {
            margin-bottom: 0;
        }
        .bukti-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 10px;
        }
        .bukti-catatan {
            font-style: italic;
            color: #64748b;
            font-size: 9px;
            margin-top: 4px;
            background: #ffffff;
            padding: 3px 5px;
            border-radius: 3px;
            border: 1px dashed #cbd5e1;
        }
        .bukti-img {
            max-width: 120px;
            max-height: 80px;
            margin-top: 4px;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
        }
        .link-file {
            font-size: 9px;
            color: #0369a1;
            text-decoration: underline;
            display: inline-block;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    @php
        $items = $aspeks->map(function ($aspekItem) use ($savedPenilaian) {
            $saved = $savedPenilaian->get($aspekItem->id);
            return [
                'aspek' => $aspekItem->aspek,
                'dimensi' => $aspekItem->dimensi,
                'bobot' => $aspekItem->bobot,
                'nilai' => $saved ? $saved->nilai : null,
                'total' => $saved ? $saved->total : 0,
                'catatan_juri' => $saved ? $saved->catatan_juri : null,
                'tracking_media' => $saved ? $saved->tracking_media : null,
                'data_dukung' => $saved ? ($saved->data_dukung ?? []) : [],
            ];
        });
        $totalNilaiAkhir = $items->sum('total');
        $totalBobot = $items->sum('bobot');
        
        // Resolve Avatar Path
        $rawFoto = $pendaftar->getRawOriginal('foto') ?? $pendaftar->foto;
        $realFotoPath = null;
        if ($rawFoto) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rawFoto)) {
                $realFotoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($rawFoto);
            } elseif (\Illuminate\Support\Facades\Storage::disk('local')->exists($rawFoto)) {
                $realFotoPath = \Illuminate\Support\Facades\Storage::disk('local')->path($rawFoto);
            } elseif (file_exists(storage_path('app/' . $rawFoto))) {
                $realFotoPath = storage_path('app/' . $rawFoto);
            } elseif (file_exists(storage_path('app/private/' . $rawFoto))) {
                $realFotoPath = storage_path('app/private/' . $rawFoto);
            }
        }
    @endphp

    <div class="header-card">
        <table class="user-info">
            <tr>
                @if($realFotoPath && file_exists($realFotoPath))
                <td width="10%">
                    <img src="file://{{ str_replace('\\', '/', $realFotoPath) }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1;">
                </td>
                @endif
                <td width="{{ $realFotoPath && file_exists($realFotoPath) ? '60%' : '70%' }}">
                    <div class="header-title">{{ strtoupper($pendaftar->nama) }}</div>
                    <div class="header-sub">
                        {{ $pendaftar->nomor_registrasi }} | Kategori {{ $pendaftar->kategori }}{{ !empty($pendaftar->provinsi) && $pendaftar->provinsi !== '-' ? ' | ' . $pendaftar->provinsi : '' }}
                    </div>
                    <div class="header-sub" style="font-weight: bold; color: #0284c7; margin-top: 6px;">
                        Tahap Penilaian: {{ $selectedTahap }}
                    </div>
                </td>
                <td width="30%">
                    <div class="score-box">
                        <div class="score-lbl">Nilai Akhir</div>
                        <div class="score-val">{{ number_format($totalNilaiAkhir, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="12%">ASPEK</th>
                <th width="15%">DIMENSI</th>
                <th width="8%">NILAI<br>(10-100)</th>
                <th width="6%">BOBOT</th>
                <th width="8%">TOTAL</th>
                <th width="15%">CATATAN JURI</th>
                <th width="13%">TRACKING MEDIA</th>
                <th width="20%">DATA DUKUNG</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $item['aspek'] }}</td>
                <td>{{ $item['dimensi'] }}</td>
                <td class="text-center font-bold">{{ $item['nilai'] !== null ? $item['nilai'] : '' }}</td>
                <td class="text-center"><span class="badge">{{ $item['bobot'] }}%</span></td>
                <td class="text-center font-bold text-blue">
                    <span class="badge-total">{{ number_format($item['total'], 2) }}</span>
                </td>
                <td>{{ $item['catatan_juri'] }}</td>
                <td>
                    @if($item['tracking_media'])
                        @if(str_starts_with($item['tracking_media'], 'http'))
                            <a href="{{ $item['tracking_media'] }}" style="color: blue; text-decoration: underline; word-break: break-all;">{{ $item['tracking_media'] }}</a>
                        @else
                            {{ $item['tracking_media'] }}
                        @endif
                    @endif
                </td>
                <td>
                    @if(!empty($item['data_dukung']) && is_array($item['data_dukung']))
                        @foreach($item['data_dukung'] as $dd)
                            @if(!empty($dd['title']) || !empty($dd['bukti']))
                            <div class="bukti-item">
                                <div class="bukti-title">&bull; {{ $dd['title'] ?? 'Bukti Dukung' }}</div>
                                
                                @if(!empty($dd['bukti']))
                                    @php
                                        $ext = strtolower(pathinfo($dd['bukti'], PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isPdf = $ext === 'pdf';
                                        $fileUrl = route('modules::pendaftar.file', ['path' => $dd['bukti']]);
                                        
                                        $physicalPath = null;
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dd['bukti'])) {
                                            $physicalPath = \Illuminate\Support\Facades\Storage::disk('public')->path($dd['bukti']);
                                        } elseif (\Illuminate\Support\Facades\Storage::disk('local')->exists($dd['bukti'])) {
                                            $physicalPath = \Illuminate\Support\Facades\Storage::disk('local')->path($dd['bukti']);
                                        } elseif (file_exists(storage_path('app/' . $dd['bukti']))) {
                                            $physicalPath = storage_path('app/' . $dd['bukti']);
                                        } elseif (file_exists(storage_path('app/private/' . $dd['bukti']))) {
                                            $physicalPath = storage_path('app/private/' . $dd['bukti']);
                                        }
                                    @endphp
                                    
                                    @if($isImage && $physicalPath && file_exists($physicalPath))
                                        <div><img src="file://{{ str_replace('\\', '/', $physicalPath) }}" class="bukti-img" alt="Bukti"></div>
                                    @else
                                        <div><a href="{{ $fileUrl }}" target="_blank" class="link-file">
                                            {{ $isPdf ? 'Lihat PDF' : 'Unduh File' }}
                                        </a></div>
                                    @endif
                                @endif
                                
                                @if(!empty($dd['catatan']))
                                    <div class="bukti-catatan">"{{ $dd['catatan'] }}"</div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc;">
                <td colspan="4" class="text-right font-bold" style="vertical-align: middle;">TOTAL BOBOT &amp; NILAI AKHIR:</td>
                <td class="text-center font-bold text-blue" style="vertical-align: middle;">{{ $totalBobot }}%</td>
                <td class="text-center font-bold" style="vertical-align: middle;">
                    <span class="badge-total bg-blue">{{ number_format($totalNilaiAkhir, 2) }}</span>
                </td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
