@php
    $cleanPath = str_replace('\\', '/', $file ?? '');
    $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
    $fileName = basename($cleanPath);
    $label = $label ?? null;

    $cat = match(true) {
        in_array($ext, ['pdf']) => 'pdf',
        in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) => 'image',
        in_array($ext, ['mp4', 'webm', 'ogg', 'mov']) => 'video',
        in_array($ext, ['docx', 'doc']) => 'word',
        in_array($ext, ['pptx', 'ppt']) => 'ppt',
        in_array($ext, ['xlsx', 'xls', 'csv']) => 'excel',
        in_array($ext, ['mp3', 'wav']) => 'audio',
        in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']) => 'archive',
        default => 'other',
    };

    $storageFullPath = storage_path('app/private/' . $cleanPath);
    $fileSize = 0;
    $formattedSize = '';
    if (file_exists($storageFullPath) && is_file($storageFullPath)) {
        $fileSize = filesize($storageFullPath);
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = $fileSize > 0 ? floor(log($fileSize, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        $bytes = $fileSize / pow(1024, $pow);
        $formattedSize = round($bytes, 2) . ' ' . $units[$pow];
    }

    $fileUrl = route('modules::pendaftar.file', ['path' => $cleanPath]);
    $downloadUrl = route('modules::pendaftar.file', ['path' => $cleanPath, 'download' => 1]);

    $itemData = [
        'name' => $fileName,
        'path' => $cleanPath,
        'url' => $fileUrl,
        'download_url' => $downloadUrl,
        'category' => $cat,
        'extension' => strtoupper($ext ?: 'FILE'),
        'size' => $fileSize,
        'formatted_size' => $formattedSize,
    ];
    $itemJson = json_encode($itemData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $catColors = [
        'pdf' => ['icon' => 'file pdf', 'color' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fecaca'],
        'image' => ['icon' => 'file image', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
        'video' => ['icon' => 'file video', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
        'word' => ['icon' => 'file word', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
        'ppt' => ['icon' => 'file powerpoint', 'color' => '#ea580c', 'bg' => '#fff7ed', 'border' => '#fed7aa'],
        'excel' => ['icon' => 'file excel', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
        'audio' => ['icon' => 'volume up', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
        'archive' => ['icon' => 'file archive', 'color' => '#475569', 'bg' => '#f8fafc', 'border' => '#e2e8f0'],
        'other' => ['icon' => 'file', 'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#e2e8f0'],
    ];
    $style = $catColors[$cat] ?? $catColors['other'];
@endphp

<div class="file-preview-card-wrapper" style="margin-bottom: 1.25rem;">
    @if ($label)
        <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.4rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
            {{ $label }}
        </div>
    @endif

    @if ($cat === 'image')
        {{-- Image Display --}}
        <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="padding: 0.6rem 0.85rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                    <i class="file image icon" style="color: #059669; margin: 0;"></i>
                    <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $fileName }}">
                        {{ $fileName }}
                    </span>
                    @if ($formattedSize)
                        <span style="font-size: 0.75rem; color: #64748b; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 4px; padding: 1px 6px;">{{ $formattedSize }}</span>
                    @endif
                </div>
                <div style="display: flex; gap: 0.35rem;">
                    <button type="button" class="ui mini teal button" onclick='openBdPreview({!! $itemJson !!})' title="Perbesar & Putar Gambar">
                        <i class="eye icon"></i> Pratinjau
                    </button>
                    <a href="{{ $downloadUrl }}" target="_blank" data-no-loader="true" class="ui mini basic blue button" title="Unduh Gambar">
                        <i class="download icon"></i> Unduh
                    </a>
                </div>
            </div>
            <div style="padding: 0.75rem; text-align: center; background: #f1f5f9;">
                <img src="{{ $fileUrl }}" class="bukti-img lightbox-trigger"
                    data-src="{{ $fileUrl }}"
                    alt="{{ $fileName }}"
                    style="max-width: 100%; max-height: 420px; border-radius: 6px; cursor: pointer; object-fit: contain; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            </div>
        </div>

    @elseif ($cat === 'pdf')
        {{-- PDF Display with Toolbar & Lazy Iframe --}}
        <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
            <div style="padding: 0.6rem 0.85rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                    <i class="file pdf icon" style="color: #ef4444; margin: 0; font-size: 1.15rem;"></i>
                    <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $fileName }}">
                        {{ $fileName }}
                    </span>
                    <span style="font-size: 0.75rem; color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 1px 6px; font-weight: 700;">PDF</span>
                    @if ($formattedSize)
                        <span style="font-size: 0.75rem; color: #64748b; background: #f1f5f9; border-radius: 4px; padding: 1px 6px;">{{ $formattedSize }}</span>
                    @endif
                </div>
                <div style="display: flex; gap: 0.35rem; align-items: center;">
                    <button type="button" class="ui mini teal button" onclick='openBdPreview({!! $itemJson !!})' title="Lihat di Modal Pratinjau Layar Penuh">
                        <i class="eye icon"></i> Layar Penuh
                    </button>
                    <a href="{{ $fileUrl }}" target="_blank" data-no-loader="true" class="ui mini basic button" title="Buka PDF di Tab Baru Browser">
                        <i class="external icon"></i> Tab Baru
                    </a>
                    <a href="{{ $downloadUrl }}" target="_blank" data-no-loader="true" class="ui mini basic blue button" title="Unduh Berkas PDF">
                        <i class="download icon"></i> Unduh
                    </a>
                </div>
            </div>
            <iframe src="{{ $fileUrl }}" loading="lazy" style="width: 100%; height: 500px; border: none; background: #f8fafc;" title="Pratinjau PDF {{ $fileName }}">
                <div style="padding: 2rem; text-align: center; color: #64748b;">
                    <p>Browser Anda tidak mendukung iframe PDF.</p>
                    <a href="{{ $fileUrl }}" target="_blank" class="ui mini primary button" data-no-loader="true"><i class="external icon"></i> Buka Dokumen</a>
                </div>
            </iframe>
        </div>

    @elseif (in_array($cat, ['word', 'ppt', 'video', 'audio']))
        {{-- Rich File Card with Preview & Download --}}
        <div style="border: 1px solid {{ $style['border'] }}; border-radius: 8px; padding: 0.85rem 1rem; background: {{ $style['bg'] }}; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #fff; border: 1px solid {{ $style['border'] }}; color: {{ $style['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                    <i class="{{ $style['icon'] }} icon" style="margin: 0;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b; max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $fileName }}">
                        {{ $fileName }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                        <span style="font-weight: 700; color: {{ $style['color'] }}; text-transform: uppercase;">{{ $ext }}</span>
                        @if ($formattedSize)
                            <span>&bull;</span>
                            <span>{{ $formattedSize }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.4rem; align-items: center;">
                <button type="button" class="ui mini teal button" onclick='openBdPreview({!! $itemJson !!})' title="Pratinjau langsung di website">
                    <i class="eye icon"></i> Lihat
                </button>
                <a href="{{ $fileUrl }}" target="_blank" data-no-loader="true" class="ui mini basic button" title="Buka di tab baru">
                    <i class="external icon"></i> Tab Baru
                </a>
                <a href="{{ $downloadUrl }}" target="_blank" data-no-loader="true" class="ui mini basic blue button" title="Unduh berkas">
                    <i class="download icon"></i> Unduh
                </a>
            </div>
        </div>

    @else
        {{-- Other / Archive File Card --}}
        <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem 1rem; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                    <i class="{{ $style['icon'] }} icon" style="margin: 0;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.9rem; color: #1e293b; max-width: 340px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $fileName }}">
                        {{ $fileName }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                        <span style="font-weight: 700; text-transform: uppercase;">{{ $ext }}</span>
                        @if ($formattedSize)
                            <span>&bull;</span>
                            <span>{{ $formattedSize }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.4rem; align-items: center;">
                <a href="{{ $downloadUrl }}" target="_blank" data-no-loader="true" class="ui mini blue button" title="Unduh berkas">
                    <i class="download icon"></i> Unduh Berkas
                </a>
            </div>
        </div>
    @endif
</div>

