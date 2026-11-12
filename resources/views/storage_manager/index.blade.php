<x-volt-app title="Manajemen Storage Pendaftar">
    <x-slot name="actions">
        <button type="button" class="ui teal button" onclick="openSmCreateFolderModal()">
            <i class="folder plus icon"></i> Buat Folder
        </button>
        <button type="button" class="ui primary button" onclick="openSmUploadModal()">
            <i class="cloud upload icon"></i> Unggah Berkas / Folder
        </button>
        <a href="{{ route('storage-manager.download-zip', ['path' => $currentPath]) }}"
            target="_blank" data-no-loader="true" class="ui basic blue button"
            title="Unduh seluruh isi folder saat ini sebagai arsip ZIP">
            <i class="file archive icon"></i> Unduh ZIP
        </a>
    </x-slot>

    @push('style')
        <style>
            .sm-stats-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.85rem;
                margin-bottom: 1.25rem;
            }
            @media (max-width: 1024px) {
                .sm-stats-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media (max-width: 576px) {
                .sm-stats-grid {
                    grid-template-columns: 1fr;
                }
            }
            .sm-stat-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 0.9rem 1.1rem;
                display: flex;
                align-items: center;
                gap: 0.85rem;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            }
            .sm-stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            .sm-stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            .sm-stat-value {
                font-size: 1.05rem;
                font-weight: 700;
                color: #0f172a;
                margin-top: 2px;
                word-break: break-all;
            }
            .sm-workspace {
                display: grid;
                grid-template-columns: 270px minmax(0, 1fr);
                gap: 1.25rem;
                align-items: start;
            }
            @media (max-width: 992px) {
                .sm-workspace {
                    grid-template-columns: 1fr;
                }
            }
            .sm-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
                overflow: hidden;
            }
            .sm-card-header {
                padding: 0.85rem 1.1rem;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                font-weight: 700;
                color: #1e293b;
                font-size: 0.92rem;
            }
            .sm-tree-scroll {
                max-height: 62vh;
                overflow-y: auto;
                padding: 0.6rem;
            }
            .sm-tree-ctrl-btn {
                width: 24px;
                height: 24px;
                border: 1px solid #cbd5e1;
                background: #fff;
                color: #475569;
                border-radius: 5px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                padding: 0;
                font-size: 0.78rem;
                transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
            }
            .sm-tree-ctrl-btn:hover {
                background: #f1f5f9;
                color: #0f766e;
                border-color: #94a3b8;
            }
            .sm-tree-row {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                border-radius: 7px;
                margin-bottom: 2px;
                padding-right: 0.45rem;
                transition: background 0.15s ease, color 0.15s ease;
            }
            .sm-tree-row.is-hidden-branch {
                display: none !important;
            }
            .sm-tree-row:hover {
                background: #f1f5f9;
            }
            .sm-tree-row.active {
                background: #0d9488;
                color: #fff;
            }
            .sm-tree-toggle {
                width: 20px;
                height: 24px;
                flex-shrink: 0;
                border: none;
                background: transparent;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: #64748b;
                border-radius: 4px;
                transition: background 0.15s ease, color 0.15s ease;
            }
            .sm-tree-toggle:hover {
                background: rgba(15, 23, 42, 0.08);
                color: #0f172a;
            }
            .sm-tree-toggle i {
                margin: 0 !important;
                font-size: 0.8rem;
                width: 14px;
                height: 14px;
                line-height: 1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.16s ease;
            }
            .sm-tree-toggle.is-expanded i {
                transform: rotate(90deg);
            }
            .sm-tree-row.active .sm-tree-toggle {
                color: rgba(255, 255, 255, 0.9);
            }
            .sm-tree-row.active .sm-tree-toggle:hover {
                background: rgba(255, 255, 255, 0.18);
                color: #fff;
            }
            .sm-tree-spacer {
                width: 20px;
                height: 24px;
                flex-shrink: 0;
            }
            .sm-tree-link {
                flex: 1;
                min-width: 0;
                display: flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.38rem 0.25rem;
                color: #334155;
                font-size: 0.84rem;
                text-decoration: none;
            }
            .sm-tree-row:hover .sm-tree-link {
                color: #0f172a;
            }
            .sm-tree-row.active .sm-tree-link {
                color: #fff;
                font-weight: 600;
            }
            .sm-tree-row.active .sm-tree-link i {
                color: #fff !important;
            }
            .sm-tree-name {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .sm-toolbar {
                padding: 0.9rem 1.1rem;
                border-bottom: 1px solid #e2e8f0;
                background: #fff;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
            }
            .sm-breadcrumb-bar {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.35rem;
                font-size: 0.88rem;
            }
            .sm-crumb {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.3rem 0.6rem;
                border-radius: 6px;
                background: #f1f5f9;
                color: #334155;
                text-decoration: none;
                font-weight: 600;
            }
            .sm-crumb:hover {
                background: #e2e8f0;
                color: #0f172a;
            }
            .sm-crumb.current {
                background: #eff6ff;
                color: #1d4ed8;
                border: 1px solid #bfdbfe;
            }
            .sm-bulk-bar {
                display: none;
                padding: 0.7rem 1.1rem;
                background: #f0fdfa;
                border-bottom: 1px solid #99f6e4;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.75rem;
            }
            .sm-bulk-bar.visible {
                display: flex;
            }
            .sm-table-wrap {
                overflow-x: auto;
            }
            .sm-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.88rem;
            }
            .sm-table thead th {
                background: #f8fafc;
                color: #475569;
                font-size: 0.76rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                padding: 0.75rem 0.9rem;
                border-bottom: 1px solid #e2e8f0;
                text-align: left;
                white-space: nowrap;
            }
            .sm-table tbody td {
                padding: 0.7rem 0.9rem;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
                color: #1e293b;
            }
            .sm-table tbody tr:hover {
                background: #f8fafc;
            }
            .sm-sort-link {
                color: inherit;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            }
            .sm-sort-link:hover {
                color: #0f172a;
            }
            .sm-item-cell {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                min-width: 240px;
            }
            .sm-item-icon {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                flex-shrink: 0;
            }
            .sm-item-title {
                font-weight: 600;
                color: #0f172a;
                text-decoration: none;
                word-break: break-word;
                cursor: pointer;
            }
            .sm-item-title:hover {
                color: #0d9488;
                text-decoration: underline;
            }
            .sm-item-sub {
                font-size: 0.76rem;
                color: #64748b;
                margin-top: 2px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.35rem;
            }
            .sm-ext-badge {
                display: inline-block;
                padding: 2px 7px;
                border-radius: 5px;
                font-size: 0.72rem;
                font-weight: 700;
                text-transform: uppercase;
            }
            .sm-actions-group {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 0.3rem;
                white-space: nowrap;
            }
            .sm-dropzone {
                border: 2px dashed #94a3b8;
                border-radius: 12px;
                padding: 2rem 1.25rem;
                text-align: center;
                background: #f8fafc;
                transition: border-color 0.2s ease, background 0.2s ease;
            }
            .sm-dropzone.dragover {
                border-color: #0d9488;
                background: #f0fdfa;
            }
            .sm-upload-queue {
                max-height: 220px;
                overflow-y: auto;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                margin-top: 0.85rem;
                display: none;
            }
            .sm-upload-queue-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.45rem 0.75rem;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.82rem;
                gap: 0.5rem;
            }
            .sm-upload-queue-item:last-child {
                border-bottom: none;
            }
            .sm-progress-wrap {
                margin-top: 0.9rem;
                display: none;
            }
            .sm-progress-bar-bg {
                width: 100%;
                height: 12px;
                background: #e2e8f0;
                border-radius: 999px;
                overflow: hidden;
            }
            .sm-progress-bar-fill {
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #0d9488, #2563eb);
                transition: width 0.15s ease;
            }
        </style>
    @endpush

    {{-- Flash & Validation Alerts --}}
    @if (session('success'))
        <div class="ui positive message" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="close icon" onclick="this.parentElement.remove()"></i>
            <div class="header">Berhasil</div>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="ui negative message" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="close icon" onclick="this.parentElement.remove()"></i>
            <div class="header">Terjadi Kesalahan</div>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="ui negative message" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="close icon" onclick="this.parentElement.remove()"></i>
            <div class="header">Validasi Gagal</div>
            <ul class="list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top Stats Summary --}}
    <div class="sm-stats-grid">
        <div class="sm-stat-card">
            <div class="sm-stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="hdd icon" style="margin: 0;"></i>
            </div>
            <div style="min-width: 0;">
                <div class="sm-stat-label">Lokasi Direktori</div>
                <div class="sm-stat-value" style="font-size: 0.85rem;" title="storage/app/private/pendaftar{{ $currentPath !== '' ? '/' . $currentPath : '' }}">
                    pendaftar{{ $currentPath !== '' ? '/' . $currentPath : ' (Root)' }}
                </div>
            </div>
        </div>
        <div class="sm-stat-card">
            <div class="sm-stat-icon" style="background: #fffbeb; color: #d97706;">
                <i class="folder open icon" style="margin: 0;"></i>
            </div>
            <div>
                <div class="sm-stat-label">Subfolder Tampil</div>
                <div class="sm-stat-value">{{ $stats['total_directories'] }} Folder</div>
            </div>
        </div>
        <div class="sm-stat-card">
            <div class="sm-stat-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="file alternate icon" style="margin: 0;"></i>
            </div>
            <div>
                <div class="sm-stat-label">Berkas Tampil</div>
                <div class="sm-stat-value">{{ $stats['total_files'] }} Berkas ({{ $stats['formatted_total_size'] }})</div>
            </div>
        </div>
        <div class="sm-stat-card">
            <div class="sm-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
                <i class="server icon" style="margin: 0;"></i>
            </div>
            <div>
                <div class="sm-stat-label">Sisa Disk / Maks Upload</div>
                <div class="sm-stat-value" style="font-size: 0.9rem;">{{ $diskFreeFormatted }} &bull; Maks {{ $maxUploadFormatted }}</div>
            </div>
        </div>
    </div>

    {{-- Main Workspace --}}
    <div class="sm-workspace">
        {{-- Left: Quick Folder Tree --}}
        <div class="sm-card">
            @php
                $totalFolders = count($allFolders);
                $rootHasChildren = $totalFolders > 0;
            @endphp
            <div class="sm-card-header">
                <span><i class="sitemap icon" style="color: #0d9488;"></i> Struktur Folder</span>
                <div style="display: flex; align-items: center; gap: 0.3rem;">
                    @if ($rootHasChildren)
                        <button type="button" class="sm-tree-ctrl-btn" onclick="expandAllSmTree()" title="Buka semua folder" aria-label="Buka semua folder">
                            <i class="plus square outline icon" style="margin: 0;"></i>
                        </button>
                        <button type="button" class="sm-tree-ctrl-btn" onclick="collapseAllSmTree()" title="Tutup semua subfolder" aria-label="Tutup semua subfolder">
                            <i class="minus square outline icon" style="margin: 0;"></i>
                        </button>
                    @endif
                    <span class="ui mini circular label">{{ $totalFolders }}</span>
                </div>
            </div>
            <div style="padding: 0.65rem 0.65rem 0.25rem 0.65rem;">
                <div class="ui mini icon input fluid">
                    <input type="text" id="sm-tree-filter" placeholder="Cari cepat folder..." oninput="filterSmTree(this.value)">
                    <i class="search icon"></i>
                </div>
            </div>
            <div class="sm-tree-scroll" id="sm-tree-list">
                <div class="sm-tree-row {{ $currentPath === '' ? 'active' : '' }}"
                    data-node-path=""
                    data-parent-path="__ROOT__"
                    data-tree-path="pendaftar root"
                    data-has-children="{{ $rootHasChildren ? '1' : '0' }}"
                    data-depth="0"
                    style="padding-left: 4px;">
                    @if ($rootHasChildren)
                        <button type="button" class="sm-tree-toggle is-expanded" aria-expanded="true" aria-label="Collapse/Expand Root" onclick="toggleSmTreeNode('')">
                            <i class="caret right icon"></i>
                        </button>
                    @else
                        <span class="sm-tree-spacer"></span>
                    @endif
                    <a href="{{ route('storage-manager.index') }}" class="sm-tree-link">
                        <i class="home icon" style="color: #0d9488; margin: 0;"></i>
                        <span class="sm-tree-name">pendaftar (Root)</span>
                    </a>
                </div>
                @foreach ($allFolders as $idx => $folder)
                    @php
                        $folderPath = $folder['path'];
                        $depth = (int) $folder['depth'];
                        $parentFolderRel = str_contains($folderPath, '/')
                            ? substr($folderPath, 0, (int) strrpos($folderPath, '/'))
                            : '';
                        $nextFolder = $allFolders[$idx + 1] ?? null;
                        $hasChildren = $nextFolder !== null && ((int) $nextFolder['depth'] > $depth);
                        $indent = max(0, ($depth - 1) * 14);
                        $isCurrent = $currentPath === $folderPath;
                        $isInActiveChain = $isCurrent || ($currentPath !== '' && str_starts_with($currentPath . '/', $folderPath . '/'));
                        $ssrVisible = ($depth === 1) || ($currentPath !== '' && ($parentFolderRel === '' || str_starts_with($currentPath . '/', $parentFolderRel . '/')));
                        $ssrExpanded = $hasChildren && $isInActiveChain;
                    @endphp
                    <div class="sm-tree-row {{ $isCurrent ? 'active' : '' }} {{ !$ssrVisible ? 'is-hidden-branch' : '' }}"
                        data-node-path="{{ $folderPath }}"
                        data-parent-path="{{ $parentFolderRel }}"
                        data-tree-path="{{ strtolower($folderPath) }}"
                        data-has-children="{{ $hasChildren ? '1' : '0' }}"
                        data-depth="{{ $depth }}"
                        style="padding-left: {{ 12 + $indent }}px;">
                        @if ($hasChildren)
                            <button type="button"
                                class="sm-tree-toggle {{ $ssrExpanded ? 'is-expanded' : '' }}"
                                aria-expanded="{{ $ssrExpanded ? 'true' : 'false' }}"
                                aria-label="Toggle folder {{ $folder['name'] }}"
                                onclick='toggleSmTreeNode(@json($folderPath))'>
                                <i class="caret right icon"></i>
                            </button>
                        @else
                            <span class="sm-tree-spacer"></span>
                        @endif
                        <a href="{{ route('storage-manager.index', ['path' => $folderPath]) }}"
                            class="sm-tree-link"
                            title="{{ $folderPath }}">
                            <i class="folder {{ ($isCurrent || $ssrExpanded) ? 'open' : '' }} icon sm-tree-folder-icon" style="color: #f59e0b; margin: 0;"></i>
                            <span class="sm-tree-name">{{ $folder['name'] }}</span>
                        </a>
                    </div>
                @endforeach
                <div id="sm-tree-empty" style="display: none; padding: 0.75rem; text-align: center; font-size: 0.82rem; color: #94a3b8;">
                    Tidak ada folder yang cocok.
                </div>
            </div>
        </div>

        {{-- Right: Main Directory Explorer --}}
        <div class="sm-card">
            {{-- Breadcrumb + Search Bar --}}
            <div class="sm-toolbar">
                <div class="sm-breadcrumb-bar">
                    @if ($parentPath !== null)
                        <a href="{{ route('storage-manager.index', $parentPath !== '' ? ['path' => $parentPath] : []) }}"
                            class="ui mini icon button" title="Kembali ke folder induk" style="margin-right: 0.25rem;">
                            <i class="level up alternate icon"></i>
                        </a>
                    @endif

                    @foreach ($breadcrumbs as $idx => $crumb)
                        @if ($idx > 0)
                            <i class="angle right icon" style="color: #94a3b8; margin: 0;"></i>
                        @endif
                        @php $isLast = $idx === count($breadcrumbs) - 1; @endphp
                        <a href="{{ route('storage-manager.index', $crumb['path'] !== '' ? ['path' => $crumb['path']] : []) }}"
                            class="sm-crumb {{ $isLast ? 'current' : '' }}">
                            @if ($idx === 0)
                                <i class="home icon" style="margin: 0;"></i>
                            @else
                                <i class="folder icon" style="margin: 0; color: {{ $isLast ? '#2563eb' : '#f59e0b' }};"></i>
                            @endif
                            <span>{{ $crumb['name'] }}</span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('storage-manager.index') }}" class="ui mini form" style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                    @if ($currentPath !== '')
                        <input type="hidden" name="path" value="{{ $currentPath }}">
                    @endif
                    <div class="ui mini icon input">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama folder / berkas...">
                        <i class="search icon"></i>
                    </div>
                    <select name="scope" class="ui mini dropdown" style="padding: 0.45rem 0.6rem; border: 1px solid rgba(34,36,38,.15); border-radius: 4px; background: #fff;">
                        <option value="current" {{ $scope === 'current' ? 'selected' : '' }}>Folder ini</option>
                        <option value="global" {{ $scope === 'global' ? 'selected' : '' }}>Semua subfolder</option>
                    </select>
                    <button type="submit" class="ui mini teal button">Cari</button>
                    @if ($search !== '')
                        <a href="{{ route('storage-manager.index', $currentPath !== '' ? ['path' => $currentPath] : []) }}" class="ui mini basic button">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Bulk Actions Toolbar --}}
            <div class="sm-bulk-bar" id="sm-bulk-bar">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: #0f766e;">
                    <i class="check square icon" style="margin: 0;"></i>
                    <span id="sm-selected-count">0 item dipilih</span>
                </div>
                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                    <button type="button" class="ui mini blue button" onclick="bulkDownloadSelected()">
                        <i class="file archive icon"></i> Unduh ZIP Terpilih
                    </button>
                    <button type="button" class="ui mini teal button" onclick="openSmMoveModalForSelected()">
                        <i class="arrows alternate icon"></i> Pindahkan
                    </button>
                    <button type="button" class="ui mini red button" onclick="bulkDeleteSelected()">
                        <i class="trash icon"></i> Hapus Terpilih
                    </button>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="sm-table-wrap">
                @php
                    $buildSortUrl = function (string $col) use ($currentPath, $search, $scope, $sort, $dir) {
                        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                        $params = array_filter([
                            'path' => $currentPath !== '' ? $currentPath : null,
                            'search' => $search !== '' ? $search : null,
                            'scope' => $search !== '' ? $scope : null,
                            'sort' => $col,
                            'dir' => $nextDir,
                        ], fn($v) => $v !== null);

                        return route('storage-manager.index', $params);
                    };

                    $catStyles = [
                        'folder' => ['icon' => 'folder', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'label' => 'Folder'],
                        'pdf' => ['icon' => 'file pdf', 'color' => '#ef4444', 'bg' => '#fef2f2', 'border' => '#fecaca', 'label' => 'PDF'],
                        'word' => ['icon' => 'file word', 'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe', 'label' => 'Word'],
                        'ppt' => ['icon' => 'file powerpoint', 'color' => '#ea580c', 'bg' => '#fff7ed', 'border' => '#fed7aa', 'label' => 'PPT'],
                        'excel' => ['icon' => 'file excel', 'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0', 'label' => 'Excel'],
                        'image' => ['icon' => 'file image', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0', 'label' => 'Gambar'],
                        'video' => ['icon' => 'file video', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#ddd6fe', 'label' => 'Video'],
                        'audio' => ['icon' => 'volume up', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a', 'label' => 'Audio'],
                        'archive' => ['icon' => 'file archive', 'color' => '#475569', 'bg' => '#f1f5f9', 'border' => '#cbd5e1', 'label' => 'Arsip'],
                        'text' => ['icon' => 'file alternate', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'border' => '#bae6fd', 'label' => 'Teks'],
                        'other' => ['icon' => 'file', 'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#e2e8f0', 'label' => 'Berkas'],
                    ];
                @endphp

                @if (count($items) > 0)
                    <table class="sm-table">
                        <thead>
                            <tr>
                                <th style="width: 38px; text-align: center;">
                                    <input type="checkbox" id="sm-check-all" onchange="toggleSelectAllSm(this)" title="Pilih semua">
                                </th>
                                <th>
                                    <a href="{{ $buildSortUrl('name') }}" class="sm-sort-link">
                                        <span>Nama</span>
                                        @if ($sort === 'name')
                                            <i class="sort {{ $dir === 'asc' ? 'up' : 'down' }} icon" style="margin: 0;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th style="width: 110px;">
                                    <a href="{{ $buildSortUrl('type') }}" class="sm-sort-link">
                                        <span>Tipe</span>
                                        @if ($sort === 'type')
                                            <i class="sort {{ $dir === 'asc' ? 'up' : 'down' }} icon" style="margin: 0;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th style="width: 130px;">
                                    <a href="{{ $buildSortUrl('size') }}" class="sm-sort-link">
                                        <span>Ukuran / Isi</span>
                                        @if ($sort === 'size')
                                            <i class="sort {{ $dir === 'asc' ? 'up' : 'down' }} icon" style="margin: 0;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th style="width: 165px;">
                                    <a href="{{ $buildSortUrl('modified') }}" class="sm-sort-link">
                                        <span>Terakhir Diubah</span>
                                        @if ($sort === 'modified')
                                            <i class="sort {{ $dir === 'asc' ? 'up' : 'down' }} icon" style="margin: 0;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th style="width: 210px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $cat = $item['category'] ?? 'other';
                                    $st = $catStyles[$cat] ?? $catStyles['other'];
                                    $isDir = $item['type'] === 'directory';
                                    $isZip = !$isDir && ($item['extension'] ?? '') === 'zip';
                                    $pendaftarInfo = $isDir ? ($pendaftarMap[$item['name']] ?? null) : null;

                                    $itemPreviewJson = !$isDir ? json_encode([
                                        'name' => $item['name'],
                                        'path' => $item['relative_path'],
                                        'url' => $item['url'],
                                        'download_url' => $item['download_url'],
                                        'category' => $cat,
                                        'extension' => strtoupper($item['extension'] ?: 'FILE'),
                                        'size' => $item['size'],
                                        'formatted_size' => $item['formatted_size'],
                                        'modified' => $item['modified'],
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : '{}';
                                @endphp
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="sm-row-check" value="{{ $item['relative_path'] }}" onchange="updateSmBulkBar()">
                                    </td>
                                    <td>
                                        <div class="sm-item-cell">
                                            <div class="sm-item-icon" style="background: {{ $st['bg'] }}; border: 1px solid {{ $st['border'] }}; color: {{ $st['color'] }};">
                                                <i class="{{ $st['icon'] }} icon" style="margin: 0;"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                @if ($isDir)
                                                    <a href="{{ route('storage-manager.index', ['path' => $item['relative_path']]) }}" class="sm-item-title">
                                                        {{ $item['name'] }}
                                                    </a>
                                                @else
                                                    <span class="sm-item-title" onclick='openSmPreview({!! $itemPreviewJson !!})'>
                                                        {{ $item['name'] }}
                                                    </span>
                                                @endif

                                                <div class="sm-item-sub">
                                                    @if ($pendaftarInfo)
                                                        <span class="ui mini teal basic label" style="padding: 2px 6px; font-weight: 600;">
                                                            <i class="user icon"></i> {{ $pendaftarInfo['nama'] }}
                                                        </span>
                                                    @endif
                                                    @if (!empty($item['parent_relative_path']) || $item['parent_relative_path'] === '')
                                                        <span>
                                                            Lokasi:
                                                            <a href="{{ route('storage-manager.index', $item['parent_relative_path'] !== '' ? ['path' => $item['parent_relative_path']] : []) }}" style="color: #0284c7;">
                                                                pendaftar{{ $item['parent_relative_path'] !== '' ? '/' . $item['parent_relative_path'] : '' }}
                                                            </a>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="sm-ext-badge" style="color: {{ $st['color'] }}; background: {{ $st['bg'] }}; border: 1px solid {{ $st['border'] }};">
                                            {{ $isDir ? 'FOLDER' : ($item['extension'] !== '' ? strtoupper($item['extension']) : $st['label']) }}
                                        </span>
                                    </td>
                                    <td style="font-weight: 500; color: #475569;">
                                        {{ $item['formatted_size'] }}
                                    </td>
                                    <td style="color: #64748b; font-size: 0.82rem;">
                                        {{ $item['modified'] }}
                                    </td>
                                    <td>
                                        <div class="sm-actions-group">
                                            @if ($isDir)
                                                <a href="{{ route('storage-manager.index', ['path' => $item['relative_path']]) }}"
                                                    class="ui mini icon basic teal button" title="Buka Folder">
                                                    <i class="folder open icon"></i>
                                                </a>
                                                <a href="{{ route('storage-manager.download-zip', ['path' => $item['relative_path']]) }}"
                                                    target="_blank" data-no-loader="true"
                                                    class="ui mini icon basic blue button" title="Unduh Folder sebagai ZIP">
                                                    <i class="download icon"></i>
                                                </a>
                                            @else
                                                <button type="button" class="ui mini icon teal button"
                                                    onclick='openSmPreview({!! $itemPreviewJson !!})' title="Pratinjau Berkas">
                                                    <i class="eye icon"></i>
                                                </button>
                                                <a href="{{ $item['download_url'] }}" target="_blank" data-no-loader="true"
                                                    class="ui mini icon basic blue button" title="Unduh Berkas">
                                                    <i class="download icon"></i>
                                                </a>
                                                @if ($isZip)
                                                    <button type="button" class="ui mini icon basic purple button"
                                                        data-path="{{ $item['relative_path'] }}"
                                                        data-name="{{ $item['name'] }}"
                                                        onclick="openSmExtractModal(this.dataset.path, this.dataset.name)"
                                                        title="Ekstrak Arsip ZIP">
                                                        <i class="box icon"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            <button type="button" class="ui mini icon basic button"
                                                data-path="{{ $item['relative_path'] }}"
                                                data-name="{{ $item['name'] }}"
                                                onclick="openSmRenameModal(this.dataset.path, this.dataset.name)"
                                                title="Ganti Nama">
                                                <i class="edit icon"></i>
                                            </button>

                                            <button type="button" class="ui mini icon basic button"
                                                data-path="{{ $item['relative_path'] }}"
                                                data-name="{{ $item['name'] }}"
                                                onclick="openSmMoveModal([this.dataset.path], this.dataset.name)"
                                                title="Pindahkan">
                                                <i class="arrows alternate icon"></i>
                                            </button>

                                            <button type="button" class="ui mini icon basic red button"
                                                data-path="{{ $item['relative_path'] }}"
                                                data-name="{{ $item['name'] }}"
                                                data-type="{{ $item['type'] }}"
                                                data-count="{{ $item['items_count'] ?? 0 }}"
                                                onclick="confirmSmDelete(this.dataset.path, this.dataset.name, this.dataset.type, parseInt(this.dataset.count || '0', 10))"
                                                title="Hapus">
                                                <i class="trash icon"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 3.5rem 1.5rem; color: #64748b;">
                        <div style="width: 68px; height: 68px; margin: 0 auto 1rem auto; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94a3b8;">
                            <i class="folder open outline icon" style="margin: 0;"></i>
                        </div>
                        @if ($search !== '')
                            <h3 style="margin: 0 0 0.4rem 0; color: #1e293b;">Tidak ada hasil untuk "{{ $search }}"</h3>
                            <p style="margin-bottom: 1.25rem;">Coba gunakan kata kunci lain atau ubah cakupan ke "Semua subfolder".</p>
                            <a href="{{ route('storage-manager.index', $currentPath !== '' ? ['path' => $currentPath] : []) }}" class="ui small basic button">
                                Reset Pencarian
                            </a>
                        @else
                            <h3 style="margin: 0 0 0.4rem 0; color: #1e293b;">Folder ini masih kosong</h3>
                            <p style="margin-bottom: 1.25rem;">Belum ada subfolder atau berkas di direktori <code>pendaftar{{ $currentPath !== '' ? '/' . $currentPath : '' }}</code>.</p>
                            <div style="display: flex; justify-content: center; gap: 0.6rem; flex-wrap: wrap;">
                                <button type="button" class="ui small teal button" onclick="openSmCreateFolderModal()">
                                    <i class="folder plus icon"></i> Buat Folder Baru
                                </button>
                                <button type="button" class="ui small primary button" onclick="openSmUploadModal()">
                                    <i class="cloud upload icon"></i> Unggah Berkas
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Hidden Action Forms for Delete & Bulk Delete --}}
    <form id="sm-single-delete-form" method="POST" action="{{ route('storage-manager.destroy') }}" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="item_path" id="sm-del-item-path">
        <input type="hidden" name="current_path" value="{{ $currentPath }}">
    </form>

    <form id="sm-bulk-delete-form" method="POST" action="{{ route('storage-manager.bulk-delete') }}" style="display: none;">
        @csrf
        <input type="hidden" name="current_path" value="{{ $currentPath }}">
        <div id="sm-bulk-delete-inputs"></div>
    </form>

    {{-- ═══════ Modal: Create Folder ═══════ --}}
    <div class="ui tiny modal" id="modal-sm-create-folder">
        <div class="header">
            <i class="folder plus icon" style="color: #0d9488;"></i> Buat Folder Baru
        </div>
        <div class="content">
            <form id="sm-create-folder-form" method="POST" action="{{ route('storage-manager.create-folder') }}" class="ui form">
                @csrf
                <input type="hidden" name="parent_path" value="{{ $currentPath }}">
                <div class="field">
                    <label>Lokasi Direktori Induk</label>
                    <input type="text" readonly value="pendaftar{{ $currentPath !== '' ? '/' . $currentPath : '' }}" style="background: #f8fafc;">
                </div>
                <div class="field required">
                    <label>Nama Folder</label>
                    <input type="text" name="folder_name" id="sm-create-folder-name" required
                        placeholder="Contoh: Dokumen_Tambahan atau bukti_dukung/DPD-BP26-001">
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                        Tips: Anda dapat memisahkan dengan tanda <code>/</code> untuk membuat subfolder bertingkat sekaligus.
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <button type="button" class="ui basic cancel button">Batal</button>
            <button type="submit" form="sm-create-folder-form" class="ui teal button">
                <i class="check icon"></i> Simpan Folder
            </button>
        </div>
    </div>

    {{-- ═══════ Modal: Upload Files / Folder ═══════ --}}
    <div class="ui small modal" id="modal-sm-upload">
        <div class="header">
            <i class="cloud upload icon" style="color: #2563eb;"></i> Unggah Berkas ke Storage
        </div>
        <div class="content">
            <div style="margin-bottom: 0.85rem; font-size: 0.86rem; color: #475569;">
                Tujuan: <strong><code>pendaftar{{ $currentPath !== '' ? '/' . $currentPath : '' }}</code></strong>
                &bull; Batas per berkas server: <strong>{{ $maxUploadFormatted }}</strong>
            </div>

            <div class="sm-dropzone" id="sm-dropzone">
                <i class="cloud upload icon huge" style="color: #64748b; margin-bottom: 0.5rem;"></i>
                <div style="font-weight: 700; font-size: 1rem; color: #1e293b; margin-bottom: 0.35rem;">
                    Tarik &amp; lepaskan berkas di sini
                </div>
                <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;">
                    Mendukung unggah banyak berkas sekaligus atau seluruh isi folder beserta subfoldernya.
                </div>
                <div style="display: flex; justify-content: center; gap: 0.6rem; flex-wrap: wrap;">
                    <button type="button" class="ui small primary button" onclick="document.getElementById('sm-file-input').click()">
                        <i class="file icon"></i> Pilih Banyak Berkas
                    </button>
                    <button type="button" class="ui small basic teal button" onclick="document.getElementById('sm-folder-input').click()">
                        <i class="folder open icon"></i> Pilih Folder (Beserta Subfolder)
                    </button>
                </div>
                <input type="file" id="sm-file-input" multiple style="display: none;" onchange="handleSmFileSelection(this.files, false)">
                <input type="file" id="sm-folder-input" webkitdirectory directory multiple style="display: none;" onchange="handleSmFileSelection(this.files, true)">
            </div>

            <div class="sm-upload-queue" id="sm-upload-queue"></div>

            <div class="ui checkbox" style="margin-top: 0.9rem;">
                <input type="checkbox" id="sm-upload-overwrite">
                <label for="sm-upload-overwrite" style="cursor: pointer; font-size: 0.86rem;">
                    Timpa berkas jika nama berkas sudah ada di folder tujuan
                </label>
            </div>

            <div class="sm-progress-wrap" id="sm-progress-wrap">
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
                    <span id="sm-progress-status">Mengunggah berkas...</span>
                    <span id="sm-progress-percent">0%</span>
                </div>
                <div class="sm-progress-bar-bg">
                    <div class="sm-progress-bar-fill" id="sm-progress-fill"></div>
                </div>
            </div>

            <div class="ui negative message" id="sm-upload-error" style="display: none; margin-top: 0.85rem; font-size: 0.85rem;"></div>
        </div>
        <div class="actions">
            <button type="button" class="ui basic button" id="sm-upload-cancel-btn" onclick="cancelSmUpload()">Tutup</button>
            <button type="button" class="ui primary button" id="sm-upload-submit-btn" onclick="startSmUpload()" disabled>
                <i class="upload icon"></i> Mulai Unggah
            </button>
        </div>
    </div>

    {{-- ═══════ Modal: Rename Item ═══════ --}}
    <div class="ui tiny modal" id="modal-sm-rename">
        <div class="header">
            <i class="edit icon" style="color: #0d9488;"></i> Ganti Nama Item
        </div>
        <div class="content">
            <form id="sm-rename-form" method="POST" action="{{ route('storage-manager.rename') }}" class="ui form">
                @csrf
                <input type="hidden" name="item_path" id="sm-rename-item-path">
                <div class="field required">
                    <label>Nama Baru</label>
                    <input type="text" name="new_name" id="sm-rename-new-name" required>
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.35rem;">
                        Pastikan ekstensi berkas tetap sesuai jika mengganti nama file.
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <button type="button" class="ui basic cancel button">Batal</button>
            <button type="submit" form="sm-rename-form" class="ui teal button">
                <i class="save icon"></i> Simpan Nama
            </button>
        </div>
    </div>

    {{-- ═══════ Modal: Move Items ═══════ --}}
    <div class="ui tiny modal" id="modal-sm-move">
        <div class="header">
            <i class="arrows alternate icon" style="color: #2563eb;"></i> Pindahkan Item
        </div>
        <div class="content">
            <form id="sm-move-form" method="POST" action="{{ route('storage-manager.move') }}" class="ui form">
                @csrf
                <input type="hidden" name="current_path" value="{{ $currentPath }}">
                <div id="sm-move-items-inputs"></div>

                <div style="margin-bottom: 0.85rem; padding: 0.65rem 0.85rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.85rem;">
                    Item dipindahkan: <strong id="sm-move-summary">-</strong>
                </div>

                <div class="field required">
                    <label>Folder Tujuan</label>
                    <select name="destination_path" id="sm-move-destination" style="width: 100%; padding: 0.55rem; border: 1px solid rgba(34,36,38,.15); border-radius: 5px;">
                        <option value="">pendaftar (Root)</option>
                        @foreach ($allFolders as $folder)
                            <option value="{{ $folder['path'] }}">
                                {{ str_repeat('— ', max(0, $folder['depth'] - 1)) }}{{ $folder['path'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="overwrite" value="1" id="sm-move-overwrite">
                        <label for="sm-move-overwrite" style="cursor: pointer;">Timpa berkas jika sudah ada di tujuan</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <button type="button" class="ui basic cancel button">Batal</button>
            <button type="submit" form="sm-move-form" class="ui primary button">
                <i class="check icon"></i> Pindahkan Sekarang
            </button>
        </div>
    </div>

    {{-- ═══════ Modal: Extract ZIP ═══════ --}}
    <div class="ui tiny modal" id="modal-sm-extract-zip">
        <div class="header">
            <i class="box icon" style="color: #7c3aed;"></i> Ekstrak Arsip ZIP
        </div>
        <div class="content">
            <form id="sm-extract-form" method="POST" action="{{ route('storage-manager.extract-zip') }}" class="ui form">
                @csrf
                <input type="hidden" name="item_path" id="sm-extract-item-path">
                <div style="margin-bottom: 0.9rem; font-size: 0.88rem;">
                    Berkas arsip: <strong id="sm-extract-filename">-</strong>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="create_subfolder" value="1" id="sm-extract-subfolder" checked>
                        <label for="sm-extract-subfolder" style="cursor: pointer;">
                            Ekstrak ke dalam subfolder baru sesuai nama ZIP
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <button type="button" class="ui basic cancel button">Batal</button>
            <button type="submit" form="sm-extract-form" class="ui purple button">
                <i class="expand arrows alternate icon"></i> Ekstrak Sekarang
            </button>
        </div>
    </div>

    {{-- ═══════ Modal: Pratinjau Berkas ═══════ --}}
    <div class="ui modal" id="modal-sm-preview" style="width: 94vw; max-width: 1250px; border-radius: 12px; overflow: hidden; background: #0f172a;">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.25rem; background: #0f172a; color: #fff; border-bottom: 1px solid #334155;">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden;">
                <div id="sm-modal-icon" style="width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; background: rgba(255,255,255,0.1); color: #38bdf8;">
                    <i class="file icon" style="margin:0;"></i>
                </div>
                <div style="overflow: hidden;">
                    <div id="sm-modal-title" style="font-weight: 700; font-size: 1.05rem; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 650px;">
                        Nama Berkas
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; display: flex; align-items: center; gap: 0.4rem; margin-top: 2px;">
                        <span id="sm-modal-ext" style="font-weight: 700; text-transform: uppercase;">PDF</span>
                        <span>&bull;</span>
                        <span id="sm-modal-size">0 KB</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <a id="sm-modal-btn-external" href="#" target="_blank" data-no-loader="true" class="ui mini basic inverted button">
                    <i class="external icon"></i> Tab Baru
                </a>
                <a id="sm-modal-btn-download" href="#" target="_blank" data-no-loader="true" class="ui mini blue button">
                    <i class="download icon"></i> Unduh
                </a>
                <button type="button" class="ui mini basic inverted icon button" onclick="closeSmPreviewModal()" title="Tutup">
                    <i class="close icon" style="margin: 0;"></i>
                </button>
            </div>
        </div>

        <div class="content" style="padding: 1rem; background: #0b1120; min-height: 480px; max-height: 82vh; overflow: auto; position: relative;">
            <div id="sm-modal-loader" style="display: none; position: absolute; inset: 0; background: rgba(15,23,42,0.88); z-index: 50; flex-direction: column; align-items: center; justify-content: center; color: #fff;">
                <div class="ui active centered inline loader large"></div>
                <p id="sm-modal-loader-text" style="margin-top: 1rem; font-size: 0.95rem; color: #cbd5e1;">Memuat pratinjau berkas...</p>
            </div>

            <div id="sm-view-pdf" class="sm-viewer-pane" style="display: none;">
                <iframe id="sm-frame-pdf" src="about:blank" style="width: 100%; height: 74vh; border: 1px solid #334155; border-radius: 8px; background: #fff;"></iframe>
            </div>

            <div id="sm-view-image" class="sm-viewer-pane" style="display: none; text-align: center; padding: 0.5rem 0;">
                <div style="margin-bottom: 0.75rem; display: flex; justify-content: center; gap: 0.5rem;">
                    <button type="button" class="ui mini basic inverted button" onclick="zoomSmImage(1.2)"><i class="search plus icon"></i> Perbesar</button>
                    <button type="button" class="ui mini basic inverted button" onclick="zoomSmImage(0.8)"><i class="search minus icon"></i> Perkecil</button>
                    <button type="button" class="ui mini basic inverted button" onclick="resetSmImage()"><i class="undo icon"></i> Reset</button>
                    <button type="button" class="ui mini basic inverted button" onclick="rotateSmImage()"><i class="redo icon"></i> Putar</button>
                </div>
                <div style="overflow: auto; max-height: 68vh; display: flex; align-items: center; justify-content: center;">
                    <img id="sm-img-element" src="" alt="Pratinjau Gambar" style="max-width: 100%; max-height: 65vh; object-fit: contain; transition: transform 0.2s ease; border-radius: 6px;">
                </div>
            </div>

            <div id="sm-view-video" class="sm-viewer-pane" style="display: none; text-align: center;">
                <video id="sm-video-element" controls playsinline style="max-width: 100%; max-height: 72vh; border-radius: 8px; background: #000;"></video>
            </div>

            <div id="sm-view-audio" class="sm-viewer-pane" style="display: none; text-align: center; padding: 3rem 1rem;">
                <audio id="sm-audio-element" controls style="width: 100%; max-width: 520px;"></audio>
            </div>

            <div id="sm-view-docx" class="sm-viewer-pane" style="display: none;">
                <div id="sm-docx-container" style="background: #fff; min-height: 400px; padding: 2rem; border-radius: 8px; overflow: auto; max-height: 72vh; color: #1e293b;"></div>
            </div>

            <div id="sm-view-pptx" class="sm-viewer-pane" style="display: none;">
                <div style="background: #fff; padding: 1.5rem; border-radius: 8px; min-height: 400px; max-height: 72vh; overflow-y: auto; color: #1e293b;">
                    <h3 id="sm-pptx-title" style="margin: 0 0 0.25rem 0; color: #9a3412;">Presentasi PowerPoint</h3>
                    <div id="sm-pptx-info" style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;"></div>
                    <div id="sm-pptx-slides-list" style="display: flex; flex-direction: column; gap: 0.85rem;"></div>
                </div>
            </div>

            <div id="sm-view-text" class="sm-viewer-pane" style="display: none;">
                <pre id="sm-text-content" style="background: #1e293b; color: #e2e8f0; padding: 1.25rem; border-radius: 8px; overflow: auto; max-height: 72vh; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.85rem; line-height: 1.55; margin: 0; white-space: pre-wrap; word-break: break-word;"></pre>
            </div>

            <div id="sm-view-fallback" class="sm-viewer-pane" style="display: none; text-align: center; padding: 3.5rem 1.5rem; color: #f8fafc;">
                <div style="width: 72px; height: 72px; border-radius: 12px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.25rem auto; color: #cbd5e1;">
                    <i class="file icon" style="margin: 0;"></i>
                </div>
                <h3 id="sm-fallback-name" style="margin: 0 0 0.5rem 0; color: #fff;">Nama Berkas</h3>
                <p id="sm-fallback-msg" style="color: #94a3b8; font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                    Pratinjau langsung tidak didukung untuk jenis berkas ini. Silakan unduh berkas untuk membukanya langsung.
                </p>
                <a id="sm-fallback-btn-download" href="#" target="_blank" data-no-loader="true" class="ui primary large button">
                    <i class="download icon"></i> Unduh Berkas
                </a>
            </div>
        </div>
    </div>

    @push('script')
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script src="{{ asset('js/docx-preview.min.js') }}"></script>
        <script>
            (function () {
                const csrfToken = @json(csrf_token());
                const uploadUrl = @json(route('storage-manager.upload'));
                const bulkDownloadBaseUrl = @json(route('storage-manager.bulk-download'));
                const currentPath = @json($currentPath);

                // Prevent Laravolt global page-loader from stalling on download links
                document.addEventListener('click', function (e) {
                    const link = e.target.closest('a[data-no-loader]');
                    if (!link) return;
                    e.stopImmediatePropagation();
                }, true);

                const treeStorageKey = 'sm_tree_expanded_v1';
                const treeRows = Array.from(document.querySelectorAll('#sm-tree-list .sm-tree-row'));
                const expandableNodes = new Set();
                const parentMap = {};

                treeRows.forEach(function (row) {
                    const nodePath = row.getAttribute('data-node-path') || '';
                    const parentPath = row.getAttribute('data-parent-path') || '';
                    parentMap[nodePath] = parentPath;
                    if (row.getAttribute('data-has-children') === '1') {
                        expandableNodes.add(nodePath);
                    }
                });

                function loadExpandedSet() {
                    const set = new Set();
                    let loadedFromStorage = false;
                    try {
                        const raw = window.sessionStorage.getItem(treeStorageKey);
                        if (raw !== null) {
                            const parsed = JSON.parse(raw);
                            if (Array.isArray(parsed)) {
                                loadedFromStorage = true;
                                parsed.forEach(function (p) {
                                    if (typeof p === 'string' && expandableNodes.has(p)) {
                                        set.add(p);
                                    }
                                });
                            }
                        }
                    } catch (e) {
                        // Ignore storage errors
                    }

                    if (!loadedFromStorage && expandableNodes.has('')) {
                        set.add('');
                    }

                    // Always ensure Root and active path's chain are expanded on navigation
                    if (currentPath !== '') {
                        if (expandableNodes.has('')) {
                            set.add('');
                        }
                        const parts = currentPath.split('/');
                        let acc = '';
                        parts.forEach(function (part) {
                            acc = acc === '' ? part : (acc + '/' + part);
                            if (expandableNodes.has(acc)) {
                                set.add(acc);
                            }
                        });
                    }

                    return set;
                }

                const expandedSet = loadExpandedSet();

                function saveExpandedSet() {
                    try {
                        window.sessionStorage.setItem(treeStorageKey, JSON.stringify(Array.from(expandedSet)));
                    } catch (e) {
                        // Ignore storage errors
                    }
                }

                function setRowToggleVisual(row, isExpanded) {
                    const toggleBtn = row.querySelector('.sm-tree-toggle');
                    if (toggleBtn) {
                        toggleBtn.classList.toggle('is-expanded', isExpanded);
                        toggleBtn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                    }
                    const folderIcon = row.querySelector('.sm-tree-folder-icon');
                    if (folderIcon) {
                        const isActiveRow = row.classList.contains('active');
                        folderIcon.className = 'folder' + ((isExpanded || isActiveRow) ? ' open' : '') + ' icon sm-tree-folder-icon';
                    }
                }

                function renderSmTree(query) {
                    const q = (query || '').toLowerCase().trim();
                    const emptyEl = document.getElementById('sm-tree-empty');

                    if (q !== '') {
                        const visibleInSearch = new Set(['']);
                        const expandInSearch = new Set();
                        let matchCount = 0;

                        treeRows.forEach(function (row) {
                            const nodePath = row.getAttribute('data-node-path') || '';
                            const searchText = row.getAttribute('data-tree-path') || '';
                            if (nodePath !== '' && searchText.indexOf(q) !== -1) {
                                matchCount++;
                                visibleInSearch.add(nodePath);
                                let currParent = parentMap[nodePath];
                                while (currParent !== undefined && currParent !== '__ROOT__') {
                                    visibleInSearch.add(currParent);
                                    expandInSearch.add(currParent);
                                    currParent = parentMap[currParent];
                                }
                            }
                        });

                        treeRows.forEach(function (row) {
                            const nodePath = row.getAttribute('data-node-path') || '';
                            const isVisible = (nodePath === '' || visibleInSearch.has(nodePath));
                            row.classList.toggle('is-hidden-branch', !isVisible);
                            if (row.getAttribute('data-has-children') === '1') {
                                setRowToggleVisual(row, expandInSearch.has(nodePath));
                            }
                        });

                        if (emptyEl) {
                            emptyEl.style.display = matchCount === 0 ? 'block' : 'none';
                        }
                        return;
                    }

                    if (emptyEl) {
                        emptyEl.style.display = 'none';
                    }

                    const childrenAllowed = {};
                    treeRows.forEach(function (row) {
                        const nodePath = row.getAttribute('data-node-path') || '';
                        const parentPath = row.getAttribute('data-parent-path') || '';
                        const hasChildren = row.getAttribute('data-has-children') === '1';

                        let isVisible = true;
                        if (parentPath !== '__ROOT__') {
                            isVisible = Boolean(childrenAllowed[parentPath]);
                        }

                        row.classList.toggle('is-hidden-branch', !isVisible);

                        const isExpanded = hasChildren && expandedSet.has(nodePath);
                        if (hasChildren) {
                            setRowToggleVisual(row, isExpanded);
                        }
                        childrenAllowed[nodePath] = isVisible && isExpanded;
                    });
                }

                renderSmTree('');

                window.toggleSmTreeNode = function (nodePath) {
                    const filterInput = document.getElementById('sm-tree-filter');
                    if (filterInput && filterInput.value.trim() !== '') {
                        filterInput.value = '';
                    }
                    if (!expandableNodes.has(nodePath)) {
                        return;
                    }
                    if (expandedSet.has(nodePath)) {
                        expandedSet.delete(nodePath);
                    } else {
                        expandedSet.add(nodePath);
                    }
                    saveExpandedSet();
                    renderSmTree('');
                };

                window.expandAllSmTree = function () {
                    const filterInput = document.getElementById('sm-tree-filter');
                    if (filterInput && filterInput.value.trim() !== '') {
                        filterInput.value = '';
                    }
                    expandableNodes.forEach(function (nodePath) {
                        expandedSet.add(nodePath);
                    });
                    saveExpandedSet();
                    renderSmTree('');
                };

                window.collapseAllSmTree = function () {
                    const filterInput = document.getElementById('sm-tree-filter');
                    if (filterInput && filterInput.value.trim() !== '') {
                        filterInput.value = '';
                    }
                    expandedSet.clear();
                    if (expandableNodes.has('')) {
                        expandedSet.add('');
                    }
                    saveExpandedSet();
                    renderSmTree('');
                };

                window.filterSmTree = function (query) {
                    renderSmTree(query);
                };

                function getSelectedPaths() {
                    const checked = [];
                    document.querySelectorAll('.sm-row-check:checked').forEach(function (cb) {
                        checked.push(cb.value);
                    });
                    return checked;
                }

                window.toggleSelectAllSm = function (master) {
                    document.querySelectorAll('.sm-row-check').forEach(function (cb) {
                        cb.checked = master.checked;
                    });
                    updateSmBulkBar();
                };

                window.updateSmBulkBar = function () {
                    const selected = getSelectedPaths();
                    const allBoxes = document.querySelectorAll('.sm-row-check');
                    const checkAll = document.getElementById('sm-check-all');
                    if (checkAll) {
                        checkAll.checked = allBoxes.length > 0 && selected.length === allBoxes.length;
                    }
                    const bar = document.getElementById('sm-bulk-bar');
                    const countEl = document.getElementById('sm-selected-count');
                    if (bar && countEl) {
                        if (selected.length > 0) {
                            countEl.textContent = selected.length + ' item dipilih';
                            bar.classList.add('visible');
                        } else {
                            bar.classList.remove('visible');
                        }
                    }
                };

                window.openSmCreateFolderModal = function () {
                    document.getElementById('sm-create-folder-name').value = '';
                    $('#modal-sm-create-folder').modal({ autofocus: true }).modal('show');
                };

                window.openSmRenameModal = function (path, currentName) {
                    document.getElementById('sm-rename-item-path').value = path;
                    document.getElementById('sm-rename-new-name').value = currentName;
                    $('#modal-sm-rename').modal({ autofocus: true }).modal('show');
                };

                window.openSmMoveModal = function (paths, summaryLabel) {
                    const inputsWrap = document.getElementById('sm-move-items-inputs');
                    inputsWrap.innerHTML = '';
                    paths.forEach(function (p) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'items[]';
                        input.value = p;
                        inputsWrap.appendChild(input);
                    });
                    document.getElementById('sm-move-summary').textContent = summaryLabel;
                    $('#modal-sm-move').modal({ autofocus: false }).modal('show');
                };

                window.openSmMoveModalForSelected = function () {
                    const selected = getSelectedPaths();
                    if (selected.length === 0) return;
                    openSmMoveModal(selected, selected.length + ' item terpilih');
                };

                window.openSmExtractModal = function (path, filename) {
                    document.getElementById('sm-extract-item-path').value = path;
                    document.getElementById('sm-extract-filename').textContent = filename;
                    $('#modal-sm-extract-zip').modal({ autofocus: false }).modal('show');
                };

                window.confirmSmDelete = function (path, name, type, itemsCount) {
                    let warningText = 'Berkas "' + name + '" akan dihapus permanen dari disk.';
                    if (type === 'directory') {
                        warningText = itemsCount > 0
                            ? 'Folder "' + name + '" berisi ' + itemsCount + ' item di dalamnya dan seluruh isinya akan dihapus permanen!'
                            : 'Folder "' + name + '" akan dihapus permanen.';
                    }

                    Swal.fire({
                        title: 'Hapus ' + (type === 'directory' ? 'Folder' : 'Berkas') + '?',
                        text: warningText,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus Permanen',
                        cancelButtonText: 'Batal'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            document.getElementById('sm-del-item-path').value = path;
                            document.getElementById('sm-single-delete-form').submit();
                        }
                    });
                };

                window.bulkDeleteSelected = function () {
                    const selected = getSelectedPaths();
                    if (selected.length === 0) return;

                    Swal.fire({
                        title: 'Hapus ' + selected.length + ' Item Terpilih?',
                        text: 'Seluruh berkas dan isi folder yang dipilih akan dihapus permanen dari disk dan tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus Semua Terpilih',
                        cancelButtonText: 'Batal'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            const container = document.getElementById('sm-bulk-delete-inputs');
                            container.innerHTML = '';
                            selected.forEach(function (path) {
                                const inp = document.createElement('input');
                                inp.type = 'hidden';
                                inp.name = 'items[]';
                                inp.value = path;
                                container.appendChild(inp);
                            });
                            document.getElementById('sm-bulk-delete-form').submit();
                        }
                    });
                };

                window.bulkDownloadSelected = function () {
                    const selected = getSelectedPaths();
                    if (selected.length === 0) return;
                    const params = new URLSearchParams();
                    selected.forEach(function (path) {
                        params.append('items[]', path);
                    });
                    window.open(bulkDownloadBaseUrl + '?' + params.toString(), '_blank');
                };

                // ── Multi-file & Folder Upload with Progress Bar ───────────────────────────
                let queuedFiles = [];
                let activeUploadXhr = null;

                function formatClientBytes(bytes) {
                    if (!bytes || bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                window.openSmUploadModal = function () {
                    queuedFiles = [];
                    activeUploadXhr = null;
                    document.getElementById('sm-file-input').value = '';
                    document.getElementById('sm-folder-input').value = '';
                    document.getElementById('sm-upload-error').style.display = 'none';
                    document.getElementById('sm-progress-wrap').style.display = 'none';
                    document.getElementById('sm-upload-submit-btn').disabled = true;
                    renderUploadQueue();

                    $('#modal-sm-upload').modal({
                        autofocus: false,
                        closable: false
                    }).modal('show');
                };

                window.handleSmFileSelection = function (fileList, isFolder) {
                    const arr = Array.from(fileList || []);
                    arr.forEach(function (f) {
                        const rel = isFolder && f.webkitRelativePath ? f.webkitRelativePath : f.name;
                        queuedFiles.push({ file: f, relativePath: rel });
                    });
                    renderUploadQueue();
                };

                function renderUploadQueue() {
                    const queueEl = document.getElementById('sm-upload-queue');
                    const submitBtn = document.getElementById('sm-upload-submit-btn');
                    if (queuedFiles.length === 0) {
                        queueEl.style.display = 'none';
                        queueEl.innerHTML = '';
                        submitBtn.disabled = true;
                        return;
                    }

                    queueEl.style.display = 'block';
                    submitBtn.disabled = false;
                    queueEl.innerHTML = '';

                    queuedFiles.forEach(function (entry, idx) {
                        const row = document.createElement('div');
                        row.className = 'sm-upload-queue-item';

                        const nameSpan = document.createElement('span');
                        nameSpan.style.cssText = 'overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;';
                        nameSpan.textContent = entry.relativePath;

                        const rightDiv = document.createElement('div');
                        rightDiv.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;';

                        const sizeSpan = document.createElement('span');
                        sizeSpan.style.color = '#64748b';
                        sizeSpan.textContent = formatClientBytes(entry.file.size);

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'ui mini icon basic button';
                        removeBtn.innerHTML = '<i class="times icon" style="margin:0;"></i>';
                        removeBtn.onclick = function () {
                            queuedFiles.splice(idx, 1);
                            renderUploadQueue();
                        };

                        rightDiv.appendChild(sizeSpan);
                        rightDiv.appendChild(removeBtn);
                        row.appendChild(nameSpan);
                        row.appendChild(rightDiv);
                        queueEl.appendChild(row);
                    });
                }

                // Drag & Drop support
                const dropzone = document.getElementById('sm-dropzone');
                if (dropzone) {
                    ['dragenter', 'dragover'].forEach(function (evt) {
                        dropzone.addEventListener(evt, function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.add('dragover');
                        });
                    });
                    ['dragleave', 'drop'].forEach(function (evt) {
                        dropzone.addEventListener(evt, function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.remove('dragover');
                        });
                    });
                    dropzone.addEventListener('drop', function (e) {
                        if (e.dataTransfer && e.dataTransfer.files) {
                            handleSmFileSelection(e.dataTransfer.files, false);
                        }
                    });
                }

                window.cancelSmUpload = function () {
                    if (activeUploadXhr) {
                        activeUploadXhr.abort();
                        activeUploadXhr = null;
                    }
                    $('#modal-sm-upload').modal('hide');
                };

                window.startSmUpload = function () {
                    if (queuedFiles.length === 0 || activeUploadXhr) return;

                    const formData = new FormData();
                    formData.append('target_path', currentPath);
                    formData.append('overwrite', document.getElementById('sm-upload-overwrite').checked ? '1' : '0');
                    queuedFiles.forEach(function (entry) {
                        formData.append('files[]', entry.file);
                        formData.append('relative_paths[]', entry.relativePath);
                    });

                    const errBox = document.getElementById('sm-upload-error');
                    const progWrap = document.getElementById('sm-progress-wrap');
                    const progStatus = document.getElementById('sm-progress-status');
                    const progPct = document.getElementById('sm-progress-percent');
                    const progFill = document.getElementById('sm-progress-fill');
                    const submitBtn = document.getElementById('sm-upload-submit-btn');

                    errBox.style.display = 'none';
                    progWrap.style.display = 'block';
                    progFill.style.width = '0%';
                    progPct.textContent = '0%';
                    progStatus.textContent = 'Mengunggah ' + queuedFiles.length + ' berkas...';
                    submitBtn.disabled = true;

                    const xhr = new XMLHttpRequest();
                    activeUploadXhr = xhr;
                    xhr.open('POST', uploadUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const pct = Math.round((e.loaded / e.total) * 100);
                            progFill.style.width = pct + '%';
                            progPct.textContent = pct + '%';
                            progStatus.textContent = 'Mengunggah (' + formatClientBytes(e.loaded) + ' / ' + formatClientBytes(e.total) + ')...';
                        }
                    });

                    xhr.addEventListener('load', function () {
                        activeUploadXhr = null;
                        if (xhr.status >= 200 && xhr.status < 300) {
                            progStatus.textContent = 'Selesai! Memuat ulang...';
                            window.location.reload();
                            return;
                        }

                        submitBtn.disabled = false;
                        let msg = 'Terjadi kesalahan saat mengunggah berkas (HTTP ' + xhr.status + ').';
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.message) {
                                msg = data.message;
                            } else if (data.errors) {
                                msg = Object.values(data.errors).flat().join(' ');
                            }
                        } catch (_) {}
                        errBox.textContent = msg;
                        errBox.style.display = 'block';
                    });

                    xhr.addEventListener('error', function () {
                        activeUploadXhr = null;
                        submitBtn.disabled = false;
                        errBox.textContent = 'Koneksi terputus saat mengunggah berkas.';
                        errBox.style.display = 'block';
                    });

                    xhr.send(formData);
                };

                // ── Preview Modal Controls ─────────────────────────────────────────────────
                let smImgScale = 1;
                let smImgRotate = 0;

                window.zoomSmImage = function (factor) {
                    smImgScale = Math.max(0.2, Math.min(smImgScale * factor, 5));
                    updateSmImageTransform();
                };
                window.rotateSmImage = function () {
                    smImgRotate = (smImgRotate + 90) % 360;
                    updateSmImageTransform();
                };
                window.resetSmImage = function () {
                    smImgScale = 1;
                    smImgRotate = 0;
                    updateSmImageTransform();
                };
                function updateSmImageTransform() {
                    const img = document.getElementById('sm-img-element');
                    if (img) img.style.transform = 'scale(' + smImgScale + ') rotate(' + smImgRotate + 'deg)';
                }

                function stopSmMedia() {
                    const video = document.getElementById('sm-video-element');
                    if (video) {
                        video.pause();
                        video.removeAttribute('src');
                        video.load();
                    }
                    const audio = document.getElementById('sm-audio-element');
                    if (audio) {
                        audio.pause();
                        audio.removeAttribute('src');
                        audio.load();
                    }
                    const frame = document.getElementById('sm-frame-pdf');
                    if (frame) frame.src = 'about:blank';
                    const docxContainer = document.getElementById('sm-docx-container');
                    if (docxContainer) docxContainer.innerHTML = '';
                    const textEl = document.getElementById('sm-text-content');
                    if (textEl) textEl.textContent = '';
                }

                window.closeSmPreviewModal = function () {
                    stopSmMedia();
                    $('#modal-sm-preview').modal('hide');
                };

                window.openSmPreview = function (file) {
                    if (!file) return;

                    document.getElementById('sm-modal-title').textContent = file.name;
                    document.getElementById('sm-modal-ext').textContent = file.extension || 'FILE';
                    document.getElementById('sm-modal-size').textContent = file.formatted_size || '';
                    document.getElementById('sm-modal-btn-external').href = file.url;
                    document.getElementById('sm-modal-btn-download').href = file.download_url;

                    document.querySelectorAll('.sm-viewer-pane').forEach(function (pane) {
                        pane.style.display = 'none';
                    });

                    const loader = document.getElementById('sm-modal-loader');
                    const loaderText = document.getElementById('sm-modal-loader-text');
                    loader.style.display = 'none';

                    $('#modal-sm-preview').modal({
                        autofocus: false,
                        observeChanges: true,
                        closable: true,
                        onHidden: function () {
                            stopSmMedia();
                        }
                    }).modal('show');

                    const cat = file.category;
                    const ext = (file.extension || '').toLowerCase();

                    if (cat === 'pdf') {
                        document.getElementById('sm-view-pdf').style.display = 'block';
                        loaderText.textContent = 'Memuat pratinjau PDF...';
                        loader.style.display = 'flex';
                        const frame = document.getElementById('sm-frame-pdf');
                        const timer = setTimeout(function () { loader.style.display = 'none'; }, 2500);
                        frame.onload = function () {
                            clearTimeout(timer);
                            loader.style.display = 'none';
                        };
                        frame.src = file.url;
                    } else if (cat === 'image') {
                        document.getElementById('sm-view-image').style.display = 'block';
                        document.getElementById('sm-img-element').src = file.url;
                        resetSmImage();
                    } else if (cat === 'video') {
                        document.getElementById('sm-view-video').style.display = 'block';
                        const video = document.getElementById('sm-video-element');
                        video.src = file.url;
                        video.load();
                    } else if (cat === 'audio') {
                        document.getElementById('sm-view-audio').style.display = 'block';
                        const audio = document.getElementById('sm-audio-element');
                        audio.src = file.url;
                        audio.load();
                    } else if (cat === 'word' && ext === 'docx') {
                        document.getElementById('sm-view-docx').style.display = 'block';
                        const container = document.getElementById('sm-docx-container');
                        container.innerHTML = '';
                        loaderText.textContent = 'Merender dokumen Word (.docx)...';
                        loader.style.display = 'flex';
                        fetch(file.url)
                            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.blob(); })
                            .then(function (blob) { return docx.renderAsync(blob, container); })
                            .then(function () { loader.style.display = 'none'; })
                            .catch(function (err) {
                                loader.style.display = 'none';
                                container.innerHTML = '<p style="color:#dc2626; text-align:center; padding:2rem;">Gagal memuat pratinjau DOCX: ' + err.message + '</p>';
                            });
                    } else if (cat === 'ppt' && ext === 'pptx') {
                        document.getElementById('sm-view-pptx').style.display = 'block';
                        document.getElementById('sm-pptx-title').textContent = file.name;
                        const slidesList = document.getElementById('sm-pptx-slides-list');
                        const pptxInfo = document.getElementById('sm-pptx-info');
                        slidesList.innerHTML = '';
                        pptxInfo.textContent = 'Membaca slide...';
                        loaderText.textContent = 'Membaca PowerPoint (.pptx)...';
                        loader.style.display = 'flex';
                        fetch(file.url)
                            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.arrayBuffer(); })
                            .then(async function (buf) {
                                const zip = await JSZip.loadAsync(buf);
                                const slideFiles = [];
                                zip.forEach(function (rel) {
                                    if (/^ppt\/slides\/slide[0-9]+\.xml$/i.test(rel)) slideFiles.push(rel);
                                });
                                slideFiles.sort(function (a, b) {
                                    return parseInt(a.match(/slide([0-9]+)\.xml/i)[1], 10) - parseInt(b.match(/slide([0-9]+)\.xml/i)[1], 10);
                                });
                                pptxInfo.textContent = 'Terdeteksi ' + slideFiles.length + ' slide';
                                const parser = new DOMParser();
                                for (let i = 0; i < slideFiles.length; i++) {
                                    const xml = await zip.file(slideFiles[i]).async('string');
                                    const doc = parser.parseFromString(xml, 'text/xml');
                                    const nodes = doc.getElementsByTagName('a:t');
                                    const lines = [];
                                    for (let t = 0; t < nodes.length; t++) {
                                        const v = (nodes[t].textContent || '').trim();
                                        if (v) lines.push(v);
                                    }
                                    const card = document.createElement('div');
                                    card.style.cssText = 'border: 1px solid #fed7aa; background: #fffdfa; border-radius: 8px; padding: 1rem;';
                                    const hdr = document.createElement('div');
                                    hdr.style.cssText = 'font-weight: 700; color: #ea580c; margin-bottom: 0.4rem;';
                                    hdr.textContent = 'Slide ' + (i + 1);
                                    const body = document.createElement('div');
                                    body.style.cssText = 'font-size: 0.88rem; color: #334155;';
                                    body.textContent = lines.length > 0 ? lines.join(' • ') : '(Slide visual / gambar)';
                                    card.appendChild(hdr);
                                    card.appendChild(body);
                                    slidesList.appendChild(card);
                                }
                                loader.style.display = 'none';
                            })
                            .catch(function (err) {
                                loader.style.display = 'none';
                                slidesList.innerHTML = '<p style="color:#dc2626;">Gagal membaca PPTX: ' + err.message + '</p>';
                            });
                    } else if (cat === 'text' && (!file.size || file.size <= 2 * 1024 * 1024)) {
                        document.getElementById('sm-view-text').style.display = 'block';
                        loaderText.textContent = 'Memuat teks berkas...';
                        loader.style.display = 'flex';
                        fetch(file.url)
                            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
                            .then(function (txt) {
                                loader.style.display = 'none';
                                document.getElementById('sm-text-content').textContent = txt;
                            })
                            .catch(function () {
                                loader.style.display = 'none';
                                showSmFallback(file);
                            });
                    } else {
                        showSmFallback(file);
                    }
                };

                function showSmFallback(file) {
                    document.querySelectorAll('.sm-viewer-pane').forEach(function (pane) {
                        pane.style.display = 'none';
                    });
                    document.getElementById('sm-view-fallback').style.display = 'block';
                    document.getElementById('sm-fallback-name').textContent = file.name;
                    document.getElementById('sm-fallback-btn-download').href = file.download_url;
                }
            })();
        </script>
    @endpush
</x-volt-app>

