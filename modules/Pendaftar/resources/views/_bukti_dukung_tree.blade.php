@php
    $level = $level ?? 0;
    $parentId = $parentId ?? 'root';
@endphp

<div class="bd-tree-list {{ $level > 0 ? 'bd-tree-nested' : '' }}" data-level="{{ $level }}">
    @foreach ($items as $index => $item)
        @php
            $itemId = 'node-' . $level . '-' . $index . '-' . \Illuminate\Support\Str::slug($item['name'] ?? 'item');
        @endphp

        @if ($item['type'] === 'directory')
            <div class="bd-folder-item" data-folder-name="{{ strtolower($item['name']) }}" data-type="directory">
                <div class="bd-folder-header" onclick="toggleBdFolder('{{ $itemId }}')" tabindex="0"
                    onkeypress="if(event.key==='Enter') toggleBdFolder('{{ $itemId }}')">
                    <div class="bd-folder-left">
                        <i class="chevron right icon bd-chevron" id="chevron-{{ $itemId }}"></i>
                        <i class="folder icon bd-folder-icon" id="folder-icon-{{ $itemId }}"></i>
                        <span class="bd-folder-title">{{ $item['name'] }}</span>
                    </div>
                    <div class="bd-folder-right">
                        <span class="bd-badge-count">{{ $item['items_count'] ?? count($item['children'] ?? []) }} item</span>
                    </div>
                </div>

                <div class="bd-folder-content" id="content-{{ $itemId }}" style="display: none;">
                    @if (!empty($item['children']) && count($item['children']) > 0)
                        @include('pendaftar::_bukti_dukung_tree', [
                            'items' => $item['children'],
                            'level' => $level + 1,
                            'parentId' => $itemId
                        ])
                    @else
                        <div class="bd-empty-subfolder">
                            <i class="info circle icon"></i> Folder kosong
                        </div>
                    @endif
                </div>
            </div>
        @else
            @php
                $cat = $item['category'] ?? 'other';
                $ext = strtoupper($item['extension'] ?? 'FILE');
                $catColors = [
                    'pdf' => ['icon' => 'file pdf', 'color' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fecaca'],
                    'word' => ['icon' => 'file word', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
                    'ppt' => ['icon' => 'file powerpoint', 'color' => '#ea580c', 'bg' => '#fff7ed', 'border' => '#fed7aa'],
                    'excel' => ['icon' => 'file excel', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
                    'image' => ['icon' => 'file image', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
                    'video' => ['icon' => 'file video', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe'],
                    'audio' => ['icon' => 'file audio', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
                    'archive' => ['icon' => 'file archive', 'color' => '#475569', 'bg' => '#f8fafc', 'border' => '#e2e8f0'],
                    'other' => ['icon' => 'file', 'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#e2e8f0'],
                ];
                $style = $catColors[$cat] ?? $catColors['other'];
                $itemJson = json_encode([
                    'name' => $item['name'],
                    'path' => $item['storage_path'],
                    'url' => $item['url'],
                    'download_url' => $item['download_url'],
                    'category' => $cat,
                    'extension' => $ext,
                    'size' => $item['size'],
                    'formatted_size' => $item['formatted_size'],
                    'modified' => $item['modified'],
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            @endphp

            <div class="bd-file-item" data-category="{{ $cat }}" data-filename="{{ strtolower($item['name']) }}" data-type="file">
                <div class="bd-file-left">
                    <div class="bd-file-icon-box" style="background: {{ $style['bg'] }}; border: 1px solid {{ $style['border'] }}; color: {{ $style['color'] }};">
                        <i class="{{ $style['icon'] }} icon" style="margin: 0;"></i>
                    </div>
                    <div class="bd-file-meta">
                        <div class="bd-file-name" title="{{ $item['name'] }}" onclick='openBdPreview({!! $itemJson !!})'>
                            {{ $item['name'] }}
                        </div>
                        <div class="bd-file-info">
                            <span class="bd-badge-ext" style="color: {{ $style['color'] }}; background: {{ $style['bg'] }}; border: 1px solid {{ $style['border'] }};">{{ $ext }}</span>
                            <span class="bd-info-sep">&bull;</span>
                            <span class="bd-file-size">{{ $item['formatted_size'] }}</span>
                            <span class="bd-info-sep">&bull;</span>
                            <span class="bd-file-date">{{ $item['modified'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="bd-file-actions">
                    <button type="button" class="ui mini teal button bd-btn-preview" onclick='openBdPreview({!! $itemJson !!})' title="Pratinjau langsung di website">
                        <i class="eye icon"></i> Lihat
                    </button>
                    <a href="{{ $item['download_url'] }}" class="ui mini basic icon button bd-btn-download" target="_blank" data-no-loader="true" title="Unduh berkas">
                        <i class="download icon"></i>
                    </a>
                </div>
            </div>
        @endif
    @endforeach
</div>

