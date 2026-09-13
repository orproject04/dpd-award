<x-volt-app :title="'Detail Pendaftar'">
    @php
        $hasRestrictedView =
            auth()->user()->hasPermission(\App\Enums\Permission::PENILAIAN_VIEW_TERBATAS) &&
            !auth()->user()->hasPermission('*');
    @endphp
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

        $reqPerm = \App\Enums\Permission::getCategoryManagePermission($pendaftar->kategori);
        $canManage = auth()->user()->hasPermission('*') || ($reqPerm && auth()->user()->hasPermission($reqPerm));
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
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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

            .ui.form .required.field>.custom-file-upload:after {
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
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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

            /* ─── Bukti Dukung Tambahan Explorer ──────────────────────── */
            .bd-stats-bar {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                padding: 0.75rem 1rem;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                margin-bottom: 1rem;
            }

            .bd-stat-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                font-size: 0.85rem;
                color: #334155;
            }

            .bd-stat-pill .icon {
                margin: 0;
            }

            .bd-stat-pill .text-blue { color: #0284c7; }
            .bd-stat-pill .text-amber { color: #d97706; }
            .bd-stat-pill .text-slate { color: #64748b; }

            .bd-controls-bar {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            .bd-search-box {
                position: relative;
                flex: 1;
                min-width: 240px;
            }

            .bd-search-box input {
                width: 100%;
                padding: 0.55rem 2.25rem 0.55rem 2.25rem;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                font-size: 0.875rem;
                outline: none;
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .bd-search-box input:focus {
                border-color: #0284c7;
                box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
            }

            .bd-search-box .search.icon {
                position: absolute;
                left: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                pointer-events: none;
                margin: 0;
            }

            #bd-search-clear {
                position: absolute;
                right: 0.6rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #94a3b8;
                font-size: 1.1rem;
                cursor: pointer;
                padding: 0 0.3rem;
            }

            #bd-search-clear:hover {
                color: #ef4444;
            }

            .bd-filter-tabs {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
            }

            .bd-tab-btn {
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                padding: 0.45rem 0.75rem;
                font-size: 0.8rem;
                font-weight: 600;
                color: #475569;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                transition: all 0.2s;
            }

            .bd-tab-btn:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            .bd-tab-btn.active {
                background: #0284c7;
                border-color: #0284c7;
                color: #fff;
            }

            .bd-tab-btn .bd-tab-count {
                background: rgba(0,0,0,0.08);
                padding: 1px 6px;
                border-radius: 10px;
                font-size: 0.7rem;
            }

            .bd-tab-btn.active .bd-tab-count {
                background: rgba(255,255,255,0.25);
                color: #fff;
            }

            .bd-tree-container {
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background: #fff;
                padding: 0.5rem;
                max-height: 700px;
                overflow-y: auto;
            }

            .bd-tree-list {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .bd-tree-nested {
                margin-left: 1.5rem;
                padding-left: 0.75rem;
                border-left: 2px dashed #cbd5e1;
                margin-top: 0.35rem;
                margin-bottom: 0.35rem;
            }

            .bd-folder-item {
                border-radius: 6px;
                transition: background 0.15s;
            }

            .bd-folder-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.55rem 0.75rem;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                cursor: pointer;
                user-select: none;
                transition: all 0.15s;
            }

            .bd-folder-header:hover {
                background: #f1f5f9;
                border-color: #cbd5e1;
            }

            .bd-folder-left {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .bd-chevron {
                font-size: 0.85rem !important;
                color: #64748b;
                transition: transform 0.2s ease;
                margin: 0 !important;
            }

            .bd-chevron.down {
                transform: rotate(90deg);
            }

            .bd-folder-icon {
                color: #f59e0b !important;
                font-size: 1.15rem !important;
                margin: 0 !important;
            }

            .bd-folder-title {
                font-weight: 700;
                color: #1e293b;
                font-size: 0.9rem;
            }

            .bd-badge-count {
                font-size: 0.75rem;
                color: #64748b;
                background: #e2e8f0;
                padding: 2px 8px;
                border-radius: 12px;
                font-weight: 600;
            }

            .bd-empty-subfolder {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
                color: #94a3b8;
                font-style: italic;
            }

            .bd-file-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.5rem 0.75rem;
                background: #fff;
                border: 1px solid #f1f5f9;
                border-radius: 6px;
                transition: all 0.15s;
            }

            .bd-file-item:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .bd-file-left {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                min-width: 0;
                flex: 1;
            }

            .bd-file-icon-box {
                width: 36px;
                height: 36px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                flex-shrink: 0;
            }

            .bd-file-meta {
                min-width: 0;
                flex: 1;
            }

            .bd-file-name {
                font-weight: 600;
                color: #1e293b;
                font-size: 0.875rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                cursor: pointer;
            }

            .bd-file-name:hover {
                color: #0284c7;
                text-decoration: underline;
            }

            .bd-file-info {
                display: flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.75rem;
                color: #64748b;
                margin-top: 0.15rem;
            }

            .bd-badge-ext {
                font-size: 0.65rem;
                font-weight: 700;
                padding: 1px 5px;
                border-radius: 4px;
                letter-spacing: 0.04em;
            }

            .bd-info-sep {
                color: #cbd5e1;
            }

            .bd-file-actions {
                display: flex;
                align-items: center;
                gap: 0.35rem;
                flex-shrink: 0;
                margin-left: 0.75rem;
            }

            .bd-empty-state {
                text-align: center;
                padding: 3rem 1.5rem;
                background: #f8fafc;
                border: 2px dashed #cbd5e1;
                border-radius: 8px;
            }

            .bd-empty-icon {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #e2e8f0;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
                margin: 0 auto 0.75rem auto;
            }

            /* DOCX rendered styling */
            #bd-docx-container .docx-wrapper {
                background: transparent !important;
                padding: 0 !important;
            }
            #bd-docx-container section.docx {
                box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
                margin-bottom: 2rem !important;
                max-width: 100% !important;
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
                                        @if ($canManage && !$hasRestrictedView)
                                            <button type="button" class="ui mini button basic"
                                                onclick="document.getElementById('provinsi-form-container').style.display='block'; document.getElementById('provinsi-display').style.display='none';">Edit</button>
                                        @endif
                                    </div>
                                    @if ($canManage && !$hasRestrictedView)
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
                                    @endif
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
                    <div class="show-card-header"
                        style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <div class="card-icon"><i class="lightbulb icon" style="margin:0"></i></div>
                            <h3 style="margin:0">Kontribusi / Inovasi</h3>
                        </div>
                        @if (
                                $canManage &&
                                (auth()->user()->hasPermission('*') ||
                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                            )
                            <button type="button" class="ui mini blue button"
                                onclick="$('#modal-add-kontribusi').modal('show')">
                                <i class="plus icon"></i> Tambah
                            </button>
                        @endif
                    </div>
                    <div class="show-card-body">
                        @forelse($pendaftar->kontribusi as $index => $kontribusi)
                            <div class="accordion-item">
                                <div class="accordion-trigger" data-acc="kontribusi-{{ $index }}" tabindex="0"
                                    onkeypress="if(event.key==='Enter') this.click();">
                                    <span>
                                        <span style="color: var(--accent); margin-right:.4rem;">#{{ $index + 1 }}</span>
                                        {{ $kontribusi->judul }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        @if (
                                                $kontribusi->is_from_admin &&
                                                $canManage &&
                                                (auth()->user()->hasPermission('*') ||
                                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                                            )
                                            <button type="button" class="ui mini icon button orange"
                                                style="margin:0; padding: 0.4rem 0.5rem;"
                                                onclick="event.stopPropagation(); $('#modal-edit-kontribusi-{{ $kontribusi->id }}').modal('show')"
                                                title="Edit">
                                                <i class="edit icon" style="margin:0;"></i>
                                            </button>
                                            <form id="form-delete-kontribusi-{{ $kontribusi->id }}"
                                                action="{{ route('modules::pendaftar.destroy-kontribusi', ['pendaftar' => $pendaftar->id, 'kontribusi' => $kontribusi->id]) }}"
                                                method="POST" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="ui mini icon button red"
                                                    style="margin:0; padding: 0.4rem 0.5rem;"
                                                    onclick="event.stopPropagation(); confirmDelete('form-delete-kontribusi-{{ $kontribusi->id }}', 'Apakah Anda yakin ingin menghapus kontribusi ini?')"
                                                    title="Hapus">
                                                    <i class="trash icon" style="margin:0;"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <svg class="acc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            style="margin-left: 0.5rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
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
                                                            @include('pendaftar::_file_preview_card', [
                                                                'file' => $buktiFile,
                                                                'label' => count($kontribusi->bukti_dukung) > 1 ? 'Berkas ' . ($fileIdx + 1) : null,
                                                            ])
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if (
                                                $kontribusi->is_from_admin &&
                                                $canManage &&
                                                (auth()->user()->hasPermission('*') ||
                                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                                            )
                                            {{-- MODAL EDIT KONTRIBUSI --}}
                                            <div class="ui modal small" id="modal-edit-kontribusi-{{ $kontribusi->id }}"
                                                style="text-align: left;">
                                                <i class="close icon"></i>
                                                <div class="header">Edit Kontribusi / Inovasi</div>
                                                <div class="content">
                                                    <form id="form-edit-kontribusi-{{ $kontribusi->id }}"
                                                        action="{{ route('modules::pendaftar.update-kontribusi', ['pendaftar' => $pendaftar->id, 'kontribusi' => $kontribusi->id]) }}"
                                                        method="POST" enctype="multipart/form-data" class="ui form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="field required">
                                                            <label>Judul</label>
                                                            <input type="text" name="judul" value="{{ $kontribusi->judul }}"
                                                                maxlength="500" required>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Deskripsi</label>
                                                            <textarea name="deskripsi"
                                                                required>{{ $kontribusi->deskripsi }}</textarea>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Dampak</label>
                                                            <textarea name="dampak"
                                                                required>{{ $kontribusi->dampak }}</textarea>
                                                        </div>
                                                        <div class="field">
                                                            <label>Bukti Dukung (Ganti File - Opsional)</label>
                                                            <div class="custom-file-upload">
                                                                <input type="file" name="bukti_dukung[]" multiple
                                                                    accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                                                    onchange="handleFileChange(this)">
                                                                <div class="upload-icon-circle">
                                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                        width="28" height="28">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="upload-text">Klik atau seret file ke sini
                                                                </div>
                                                                <div class="upload-hint">Format JPG, PNG, PDF, DOC,
                                                                    XLS, PPT, ZIP</div>
                                                            </div>
                                                            <small style="display:block; margin-top:.5rem;">Biarkan
                                                                kosong jika tidak ingin mengubah file bukti
                                                                dukung.</small>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="actions">
                                                    <div class="ui cancel button">Batal</div>
                                                    <button type="submit" form="form-edit-kontribusi-{{ $kontribusi->id }}"
                                                        class="ui primary button">Simpan Perubahan</button>
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
                @if (
                        auth()->user()->hasPermission('*') ||
                        auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE)
                    )
                    <div class="ui modal small" id="modal-add-kontribusi">
                        <i class="close icon"></i>
                        <div class="header">Tambah Kontribusi / Inovasi</div>
                        <div class="content">
                            <form id="form-add-kontribusi"
                                action="{{ route('modules::pendaftar.store-kontribusi', ['pendaftar' => $pendaftar->id]) }}"
                                method="POST" enctype="multipart/form-data" class="ui form">
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
                                    <div class="custom-file-upload">
                                        <input type="file" name="bukti_dukung[]" multiple required
                                            accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                            onchange="handleFileChange(this)">
                                        <div class="upload-icon-circle">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28"
                                                height="28">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                </path>
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
                            <button type="submit" form="form-add-kontribusi" class="ui primary button">Tambah
                                Kontribusi</button>
                        </div>
                    </div>
                @endif

                {{-- ── Penghargaan ───────────────────────────────────────── --}}
                <div class="show-card">
                    <div class="show-card-header"
                        style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <div class="card-icon"><i class="trophy icon" style="margin:0"></i></div>
                            <h3 style="margin:0">Penghargaan</h3>
                        </div>
                        @if (
                                $canManage &&
                                (auth()->user()->hasPermission('*') ||
                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                            )
                            <button type="button" class="ui mini blue button"
                                onclick="$('#modal-add-penghargaan').modal('show')">
                                <i class="plus icon"></i> Tambah
                            </button>
                        @endif
                    </div>
                    <div class="show-card-body">
                        @forelse($pendaftar->penghargaan as $index => $penghargaan)
                            <div class="accordion-item">
                                <div class="accordion-trigger" data-acc="penghargaan-{{ $index }}" tabindex="0"
                                    onkeypress="if(event.key==='Enter') this.click();">
                                    <span>
                                        <span style="color: var(--accent); margin-right:.4rem;">#{{ $index + 1 }}</span>
                                        {{ \Illuminate\Support\Str::limit($penghargaan->uraian, 60) }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        @if (
                                                $penghargaan->is_from_admin &&
                                                $canManage &&
                                                (auth()->user()->hasPermission('*') ||
                                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                                            )
                                            <button type="button" class="ui mini icon button orange"
                                                style="margin:0; padding: 0.4rem 0.5rem;"
                                                onclick="event.stopPropagation(); $('#modal-edit-penghargaan-{{ $penghargaan->id }}').modal('show')"
                                                title="Edit">
                                                <i class="edit icon" style="margin:0;"></i>
                                            </button>
                                            <form id="form-delete-penghargaan-{{ $penghargaan->id }}"
                                                action="{{ route('modules::pendaftar.destroy-penghargaan', ['pendaftar' => $pendaftar->id, 'penghargaan' => $penghargaan->id]) }}"
                                                method="POST" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="ui mini icon button red"
                                                    style="margin:0; padding: 0.4rem 0.5rem;"
                                                    onclick="event.stopPropagation(); confirmDelete('form-delete-penghargaan-{{ $penghargaan->id }}', 'Apakah Anda yakin ingin menghapus penghargaan ini?')"
                                                    title="Hapus">
                                                    <i class="trash icon" style="margin:0;"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <svg class="acc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            style="margin-left: 0.5rem;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
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
                                                            @include('pendaftar::_file_preview_card', [
                                                                'file' => $buktiFile,
                                                                'label' => count($penghargaan->bukti_dukung) > 1 ? 'Berkas ' . ($fileIdx + 1) : null,
                                                            ])
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif



                                        @if (
                                                $penghargaan->is_from_admin &&
                                                $canManage &&
                                                (auth()->user()->hasPermission('*') ||
                                                    auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE))
                                            )
                                            {{-- MODAL EDIT PENGHARGAAN --}}
                                            <div class="ui modal small" id="modal-edit-penghargaan-{{ $penghargaan->id }}"
                                                style="text-align: left;">
                                                <i class="close icon"></i>
                                                <div class="header">Edit Penghargaan</div>
                                                <div class="content">
                                                    <form id="form-edit-penghargaan-{{ $penghargaan->id }}"
                                                        action="{{ route('modules::pendaftar.update-penghargaan', ['pendaftar' => $pendaftar->id, 'penghargaan' => $penghargaan->id]) }}"
                                                        method="POST" enctype="multipart/form-data" class="ui form">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="field required">
                                                            <label>Uraian Penghargaan</label>
                                                            <input type="text" name="uraian" value="{{ $penghargaan->uraian }}"
                                                                maxlength="500" required>
                                                        </div>
                                                        <div class="field required">
                                                            <label>Tahun</label>
                                                            <input type="number" name="tahun" value="{{ $penghargaan->tahun }}"
                                                                min="1900" max="{{ date('Y') }}"
                                                                oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);"
                                                                required>
                                                        </div>
                                                        <div class="field">
                                                            <label>Bukti Dukung (Ganti File - Opsional)</label>
                                                            <div class="custom-file-upload">
                                                                <input type="file" name="bukti_dukung[]" multiple
                                                                    accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                                                    onchange="handleFileChange(this)">
                                                                <div class="upload-icon-circle">
                                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                                        width="28" height="28">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                                <div class="upload-text">Klik atau seret file ke sini
                                                                </div>
                                                                <div class="upload-hint">Format JPG, PNG, PDF, DOC,
                                                                    XLS, PPT, ZIP</div>
                                                            </div>
                                                            <small style="display:block; margin-top:.5rem;">Biarkan
                                                                kosong jika tidak ingin mengubah file bukti
                                                                dukung.</small>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="actions">
                                                    <div class="ui cancel button">Batal</div>
                                                    <button type="submit" form="form-edit-penghargaan-{{ $penghargaan->id }}"
                                                        class="ui primary button">Simpan Perubahan</button>
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
                @if (
                        auth()->user()->hasPermission('*') ||
                        auth()->user()->hasPermission(\App\Enums\Permission::KONTRIBUSI_PENGHARGAAN_MANAGE)
                    )
                    <div class="ui modal small" id="modal-add-penghargaan">
                        <i class="close icon"></i>
                        <div class="header">Tambah Penghargaan</div>
                        <div class="content">
                            <form id="form-add-penghargaan"
                                action="{{ route('modules::pendaftar.store-penghargaan', ['pendaftar' => $pendaftar->id]) }}"
                                method="POST" enctype="multipart/form-data" class="ui form">
                                @csrf
                                <div class="field required">
                                    <label>Uraian Penghargaan</label>
                                    <input type="text" name="uraian" maxlength="500" required>
                                </div>
                                <div class="field required">
                                    <label>Tahun</label>
                                    <input type="number" name="tahun" min="1900" max="{{ date('Y') }}"
                                        oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                                </div>
                                <div class="field required">
                                    <label>Bukti Dukung (Wajib)</label>
                                    <div class="custom-file-upload">
                                        <input type="file" name="bukti_dukung[]" multiple required
                                            accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                            onchange="handleFileChange(this)">
                                        <div class="upload-icon-circle">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28"
                                                height="28">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                </path>
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
                            <button type="submit" form="form-add-penghargaan" class="ui primary button">Tambah
                                Penghargaan</button>
                        </div>
                    </div>
                @endif

                {{-- ── Bukti Dukung Tambahan (Folder & File Explorer) ──────── --}}
                @if (($buktiDukungStats['total_files'] ?? 0) > 0)
                    <div class="show-card" id="card-bukti-dukung-tambahan">
                        <div class="show-card-header"
                            style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div class="card-icon" style="background: #0284c7;"><i class="folder open icon" style="margin:0"></i></div>
                                <div>
                                    <h3 style="margin:0; font-size: 1.15rem; color: #1e293b;">Bukti Dukung Tambahan</h3>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <a href="{{ route('modules::pendaftar.download-bukti-dukung-zip', $pendaftar->id) }}"
                                    class="ui mini button primary" target="_blank" data-no-loader="true"
                                    title="Unduh seluruh bukti dukung tambahan dalam satu berkas ZIP">
                                    <i class="archive icon"></i> Unduh Semua (ZIP)
                                </a>
                                <button type="button" class="ui mini basic button" onclick="expandAllBdFolders()" title="Buka seluruh subfolder">
                                    <i class="folder open icon"></i> Buka Semua
                                </button>
                                <button type="button" class="ui mini basic button" onclick="collapseAllBdFolders()" title="Tutup seluruh subfolder">
                                    <i class="folder icon"></i> Tutup Semua
                                </button>
                            </div>
                        </div>

                        <div class="show-card-body" style="padding: 1.25rem;">
                            {{-- Stats summary strip --}}
                            <div class="bd-stats-bar">
                                <div class="bd-stat-pill">
                                    <i class="file icon text-blue"></i>
                                    <span><strong>{{ $buktiDukungStats['total_files'] }}</strong> Berkas</span>
                                </div>
                                @if (($buktiDukungStats['total_directories'] ?? 0) > 0)
                                    <div class="bd-stat-pill">
                                        <i class="folder open icon text-amber"></i>
                                        <span><strong>{{ $buktiDukungStats['total_directories'] }}</strong> Subfolder</span>
                                    </div>
                                @endif
                                <div class="bd-stat-pill">
                                    <i class="database icon text-slate"></i>
                                    <span><strong>{{ $buktiDukungStats['formatted_total_size'] }}</strong> Total Ukuran</span>
                                </div>
                            </div>

                            {{-- Filter and Search Controls --}}
                            <div class="bd-controls-bar">
                                <div class="bd-search-box">
                                    <i class="search icon"></i>
                                    <input type="text" id="bd-search-input" placeholder="Cari nama berkas bukti dukung..." onkeyup="filterBdItems()">
                                    <button type="button" id="bd-search-clear" onclick="clearBdSearch()" style="display: none;">&times;</button>
                                </div>

                                <div class="bd-filter-tabs">
                                    <button type="button" class="bd-tab-btn active" data-cat="all" onclick="filterBdCategory('all', this)">
                                        Semua <span class="bd-tab-count">{{ $buktiDukungStats['total_files'] }}</span>
                                    </button>
                                    @if (($buktiDukungStats['by_category']['pdf'] ?? 0) > 0)
                                        <button type="button" class="bd-tab-btn" data-cat="pdf" onclick="filterBdCategory('pdf', this)">
                                            PDF <span class="bd-tab-count">{{ $buktiDukungStats['by_category']['pdf'] }}</span>
                                        </button>
                                    @endif
                                    @if ((($buktiDukungStats['by_category']['word'] ?? 0) + ($buktiDukungStats['by_category']['ppt'] ?? 0)) > 0)
                                        <button type="button" class="bd-tab-btn" data-cat="office" onclick="filterBdCategory('office', this)">
                                            Dokumen <span class="bd-tab-count">{{ ($buktiDukungStats['by_category']['word'] ?? 0) + ($buktiDukungStats['by_category']['ppt'] ?? 0) }}</span>
                                        </button>
                                    @endif
                                    @if (($buktiDukungStats['by_category']['image'] ?? 0) > 0)
                                        <button type="button" class="bd-tab-btn" data-cat="image" onclick="filterBdCategory('image', this)">
                                            Gambar <span class="bd-tab-count">{{ $buktiDukungStats['by_category']['image'] }}</span>
                                        </button>
                                    @endif
                                    @if (($buktiDukungStats['by_category']['video'] ?? 0) > 0)
                                        <button type="button" class="bd-tab-btn" data-cat="video" onclick="filterBdCategory('video', this)">
                                            Video <span class="bd-tab-count">{{ $buktiDukungStats['by_category']['video'] }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div id="bd-no-results" style="display: none; text-align: center; padding: 2rem; color: #94a3b8;">
                                <i class="search icon large"></i>
                                <p style="margin-top: 0.5rem; font-size: 0.95rem;">Tidak ada berkas yang cocok dengan filter atau pencarian.</p>
                            </div>

                            {{-- Folder & File Tree --}}
                            <div class="bd-tree-container">
                                @include('pendaftar::_bukti_dukung_tree', ['items' => $buktiDukungTree, 'level' => 0])
                            </div>
                        </div>
                    </div>
                @endif

            </div>{{-- /left column --}}

            {{-- ═══════════════════════ KOLOM KANAN ══════════════════════ --}}
            <div class="five wide column">

                @php
                    $statusRank = \App\Models\Pendaftar::getStatusRank($pendaftar->status);
                    $showKertasKerja = str_starts_with($pendaftar->status ?? '', 'Lolos');
                    if ($hasRestrictedView && $statusRank < 2) {
                        $showKertasKerja = false;
                    }
                @endphp
                @if ($showKertasKerja)
                    {{-- ── Kertas Kerja Penilaian ──────────────────────────── --}}
                    <div class="show-card" style="border: 2px solid #0284c7; background: #f0f9ff;">
                        @php
                            $currentTimelineStage = \App\Models\Pendaftar::getCurrentTimelineStage();
                            
                            $timelineSequence = [
                                'Lolos Verifikasi Berkas' => 'Seleksi 50 Besar',
                                'Lolos ke Tahap 50 Besar' => 'Seleksi 10 Besar',
                                'Lolos ke Tahap 10 Besar' => 'Seleksi 3 Besar',
                                'Lolos ke Tahap 3 Besar' => 'Seleksi Wawancara',
                                'Lolos ke Tahap Wawancara' => 'Seleksi Final',
                            ];

                            $availableStages = [];
                            $foundCurrent = false;
                            foreach ($timelineSequence as $key => $name) {
                                $availableStages[$key] = [
                                    'name' => $name,
                                    'kk' => $pendaftar->kertasKerja->where('tahap', $key)
                                ];
                                if ($key === $currentTimelineStage) {
                                    $foundCurrent = true;
                                    break;
                                }
                            }
                            
                            if (!$foundCurrent) {
                                $fallbackName = $timelineSequence[$currentTimelineStage] ?? 'Penilaian';
                                $availableStages[$currentTimelineStage] = [
                                    'name' => $fallbackName,
                                    'kk' => $pendaftar->kertasKerja->where('tahap', $currentTimelineStage)
                                ];
                            }

                            $aspeksTotalCount = \App\Models\KategoriAspek::where('kategori', $pendaftar->kategori)->count();
                        @endphp
                        <div class="show-card-header"
                            style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div class="card-icon" style="background: rgba(255,255,255,0.2);"><i
                                        class="clipboard check icon" style="margin:0; color: #fff;"></i></div>
                                <h3 style="color: #fff; margin: 0;">Kertas Kerja Penilaian</h3>
                            </div>
                            @if(count($availableStages) > 1)
                                <div>
                                    <select id="tahapDropdown" onchange="updateKertasKerjaView(this.value)"
                                        style="background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 6px; padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 600; outline: none; cursor: pointer; backdrop-filter: blur(4px);">
                                        @foreach($availableStages as $key => $data)
                                            <option value="{{ $key }}" style="color: #333; background: #fff;" {{ $key === $currentTimelineStage ? 'selected' : '' }}>{{ $data['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="show-card-body" style="text-align: center;">

                            <div id="kkTitle"
                                style="font-size: 0.8rem; font-weight: 700; color: #0369a1; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                Nilai {{ $availableStages[$currentTimelineStage]['name'] }}
                            </div>
                            <div id="kkScore"
                                style="font-size: 2.2rem; font-weight: 800; color: #0284c7; line-height: 1; margin-bottom: 0.5rem;">
                                {{ number_format($availableStages[$currentTimelineStage]['kk']->sum('total') ?? 0, 2) }}
                            </div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">
                                Terisi: <strong id="kkTerisi">{{ $availableStages[$currentTimelineStage]['kk']->whereNotNull('nilai')->count() }}</strong> dari
                                <strong>{{ $aspeksTotalCount }}</strong> Aspek Penilaian
                            </div>

                            <a id="kkLink" href="{{ route('modules::pendaftar.kertas-kerja', ['pendaftar' => $pendaftar->id, 'tahap' => $currentTimelineStage]) }}"
                                class="ui button primary fluid">
                                <i class="edit icon"></i> Buka Kertas Kerja
                            </a>
                            
                            <script>
                                const kkData = {!! json_encode(collect($availableStages)->map(function($data, $key) use ($pendaftar) {
                                    return [
                                        'name' => 'Nilai ' . $data['name'],
                                        'score' => number_format($data['kk']->sum('total') ?? 0, 2),
                                        'terisi' => $data['kk']->whereNotNull('nilai')->count(),
                                        'url' => route('modules::pendaftar.kertas-kerja', ['pendaftar' => $pendaftar->id, 'tahap' => $key])
                                    ];
                                })) !!};
                            
                                function updateKertasKerjaView(tahap) {
                                    if (!kkData[tahap]) return;
                                    document.getElementById('kkTitle').innerText = kkData[tahap].name;
                                    document.getElementById('kkScore').innerText = kkData[tahap].score;
                                    document.getElementById('kkTerisi').innerText = kkData[tahap].terisi;
                                    document.getElementById('kkLink').href = kkData[tahap].url;
                                }
                            </script>
                        </div>
                    </div>
                @endif

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
                        @if ($canManage)
                            @php
                                $pendaftarRank = \App\Models\Pendaftar::getStatusRank($pendaftar->status);
                                $disableStatusEdit = $hasRestrictedView && $pendaftarRank < 2;
                            @endphp

                            @if (!$disableStatusEdit)
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
                                                        $optRank = \App\Models\Pendaftar::getStatusRank($status);
                                                    @endphp
                                                    @if ($hasRestrictedView && $optRank < 2 && $status !== 'Tidak Lolos')
                                                        @continue
                                                    @endif
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
                                                    <i class="edit icon"></i> Keterangan
                                                </h3>

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
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif

                        <hr class="show-divider">

                        {{-- Download all ZIP --}}
                        <x-volt-link-button url="{{ route('modules::pendaftar.download-all', $pendaftar->id) }}"
                            icon="archive" class="blue fluid" target="_blank" data-no-loader="true">
                            Unduh Semua Berkas (ZIP)
                        </x-volt-link-button>

                        <hr class="show-divider">

                        @if(!$hasRestrictedView)
                            {{-- Resend Email --}}
                            <form id="resend-email-form"
                                action="{{ route('modules::pendaftar.resend-email', $pendaftar->id) }}" method="POST">
                                @csrf
                                <x-volt-button type="button" icon="envelope" class="green fluid"
                                    onclick="confirmResendEmail()">
                                    Kirim Ulang Email Bukti Pendaftaran
                                </x-volt-button>
                            </form>
                        @endif
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
                                        <i class="file icon large"></i><br>Berkas Non-Gambar
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
                                    <i class="image icon"></i> Foto tidak diunggah
                                </div>
                            @endif
                            @if ($canManage && !$hasRestrictedView)
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
                            @endif
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
                                <div class="file-block-label"><i class="id card icon"></i> KTP Pendaftar</div>

                                @if (!empty($ktpRaw))
                                    @include('pendaftar::_file_preview_card', [
                                        'file' => $ktpRaw,
                                        'label' => null,
                                    ])
                                @else
                                    <div style="color:#94a3b8; font-size:.85rem; text-align:center; padding:.75rem 0;">
                                        <i class="id card icon"></i> KTP tidak diunggah
                                    </div>
                                @endif
                                @if ($canManage && !$hasRestrictedView)
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
                                @endif
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
                                    default => '#64748b',
                                };
                            @endphp
                            <div
                                style="border-left: 2px solid #e2e8f0; padding-left: 1.75rem; position: relative; padding-bottom: 0.5rem;">
                                <div class="status-dot {{ $optColor }}"
                                    style="position: absolute; left: -9px; top: 2px; width: 14px; height: 14px; margin: 0; box-shadow: 0 0 0 3px #fff;">
                                </div>
                                <div
                                    style="font-size: 15px; font-weight: 700; color: {{ $textColor }}; margin-bottom: 0.4rem;">
                                    {{ $riwayat->status }}
                                </div>
                                <div
                                    style="font-size: 12.5px; color: #475569; margin-bottom: 0.75rem; display: inline-flex; align-items: center; background: #f1f5f9; padding: 0.3rem 0.7rem; border-radius: 6px; font-weight: 500;">
                                    <i class="calendar icon" style="margin-right: 0.4rem; color: #64748b;"></i>
                                    {{ $riwayat->created_at->format('d M Y, H:i') }}
                                </div>

                                @php
                                    $riwayatRank = \App\Models\Pendaftar::getStatusRank($riwayat->status);
                                    $canEditKeterangan =
                                        $riwayat->status !== 'Diajukan' && (!$hasRestrictedView || $riwayatRank >= 3);
                                @endphp
                                @if ($canEditKeterangan)
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

                                        @if ($riwayat->keterangan)
                                            <div x-show="!showForm"
                                                style="font-size: 14px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem; border-radius: 8px; color: #334155; line-height: 1.5;">
                                                {{ $riwayat->keterangan }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @if ($riwayat->keterangan)
                                        <div
                                            style="font-size: 14px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem; border-radius: 8px; margin-top: 0.5rem; color: #334155; line-height: 1.5;">
                                            {{ $riwayat->keterangan }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                        @if (empty($pendaftar->riwayats) || $pendaftar->riwayats->isEmpty())
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

    {{-- ═══════ Modal Pratinjau Bukti Dukung Tambahan ═══════ --}}
    <div class="ui modal" id="modal-preview-bukti-dukung" style="width: 94vw; max-width: 1250px; border-radius: 12px; overflow: hidden; background: #0f172a;">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.25rem; background: #0f172a; color: #fff; border-bottom: 1px solid #334155;">
            <div style="display: flex; align-items: center; gap: 0.75rem; overflow: hidden;">
                <div id="bd-modal-icon" style="width: 34px; height: 34px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; background: rgba(255,255,255,0.1); color: #38bdf8;">
                    <i class="file icon" style="margin:0;"></i>
                </div>
                <div style="overflow: hidden;">
                    <div id="bd-modal-title" style="font-weight: 700; font-size: 1.05rem; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 650px;">
                        Nama Berkas
                    </div>
                    <div style="font-size: 0.75rem; color: #94a3b8; display: flex; align-items: center; gap: 0.4rem; margin-top: 2px;">
                        <span id="bd-modal-ext" style="font-weight: 700; text-transform: uppercase;">PDF</span>
                        <span>&bull;</span>
                        <span id="bd-modal-size">0 KB</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <a id="bd-modal-btn-external" href="#" target="_blank" data-no-loader="true" class="ui mini basic inverted button" title="Buka berkas di tab baru">
                    <i class="external icon"></i> Tab Baru
                </a>
                <a id="bd-modal-btn-download" href="#" target="_blank" data-no-loader="true" class="ui mini blue button" title="Unduh berkas ini">
                    <i class="download icon"></i> Unduh
                </a>
                <button type="button" class="ui mini basic inverted icon button" onclick="closeBdModal()" title="Tutup">
                    <i class="close icon" style="margin: 0;"></i>
                </button>
            </div>
        </div>

        <div class="content" id="bd-modal-body" style="padding: 1rem; background: #0b1120; min-height: 480px; max-height: 82vh; overflow: auto; position: relative;">
            {{-- Loading spinner --}}
            <div id="bd-modal-loader" style="display: none; position: absolute; inset: 0; background: rgba(15,23,42,0.88); z-index: 50; flex-direction: column; align-items: center; justify-content: center; color: #fff;">
                <div class="ui active centered inline loader large"></div>
                <p id="bd-modal-loader-text" style="margin-top: 1rem; font-size: 0.95rem; color: #cbd5e1;">Memuat pratinjau berkas...</p>
            </div>

            {{-- PDF Viewer Container --}}
            <div id="bd-view-pdf" class="bd-viewer-pane" style="display: none;">
                <div style="background: #1e293b; padding: 0.5rem 0.85rem; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; border: 1px solid #334155; border-bottom: none;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #cbd5e1; font-size: 0.85rem; font-weight: 600;">
                        <i class="file pdf icon" style="color: #ef4444; margin: 0;"></i>
                        <span>Dokumen PDF</span>
                        <span id="bd-pdf-filesize-badge" style="font-size: 0.75rem; color: #94a3b8; font-weight: 400;"></span>
                    </div>
                    <div style="display: flex; gap: 0.4rem; align-items: center;">
                        <button type="button" class="ui mini basic inverted button" onclick="reloadBdPdf()" title="Muat ulang pratinjau jika dokumen belum tampil">
                            <i class="redo icon"></i> Muat Ulang
                        </button>
                        <a id="bd-pdf-btn-open" href="#" target="_blank" data-no-loader="true" class="ui mini basic inverted button" title="Buka PDF di tab baru">
                            <i class="external icon"></i> Buka Tab Baru
                        </a>
                        <a id="bd-pdf-btn-dl" href="#" target="_blank" data-no-loader="true" class="ui mini blue button" title="Unduh PDF">
                            <i class="download icon"></i> Unduh PDF
                        </a>
                    </div>
                </div>
                <iframe id="bd-frame-pdf" src="" style="width: 100%; height: 72vh; border: 1px solid #334155; border-radius: 0 0 8px 8px; background: #fff;"></iframe>
            </div>

            {{-- Image Viewer Container --}}
            <div id="bd-view-image" class="bd-viewer-pane" style="display: none; text-align: center; padding: 0.5rem 0;">
                <div style="margin-bottom: 0.75rem; display: flex; justify-content: center; gap: 0.5rem;">
                    <button type="button" class="ui mini basic inverted button" onclick="zoomBdImage(1.2)"><i class="search plus icon"></i> Perbesar</button>
                    <button type="button" class="ui mini basic inverted button" onclick="zoomBdImage(0.8)"><i class="search minus icon"></i> Perkecil</button>
                    <button type="button" class="ui mini basic inverted button" onclick="resetBdImage()"><i class="undo icon"></i> Reset</button>
                    <button type="button" class="ui mini basic inverted button" onclick="rotateBdImage()"><i class="redo icon"></i> Putar</button>
                </div>
                <div style="overflow: auto; max-height: 68vh; display: flex; align-items: center; justify-content: center;">
                    <img id="bd-img-element" src="" alt="Pratinjau Gambar" style="max-width: 100%; max-height: 65vh; object-fit: contain; transition: transform 0.2s ease; border-radius: 6px; box-shadow: 0 4px 20px rgba(0,0,0,0.4);">
                </div>
            </div>

            {{-- Video Viewer Container --}}
            <div id="bd-view-video" class="bd-viewer-pane" style="display: none; text-align: center;">
                <video id="bd-video-element" controls playsinline style="max-width: 100%; max-height: 72vh; border-radius: 8px; outline: none; background: #000;"></video>
            </div>

            {{-- Word DOCX Viewer Container --}}
            <div id="bd-view-docx" class="bd-viewer-pane" style="display: none;">
                <div style="background: #f1f5f9; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: #475569; font-weight: 600;">
                        <i class="file word icon" style="color: #2563eb;"></i> Pratinjau Dokumen Microsoft Word (.docx)
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b;">Halaman dokumen dirender langsung</span>
                </div>
                <div id="bd-docx-container" style="background: #fff; min-height: 400px; padding: 2rem; border-radius: 8px; overflow: auto; max-height: 68vh; color: #1e293b; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
            </div>

            {{-- PowerPoint PPTX Viewer Container --}}
            <div id="bd-view-pptx" class="bd-viewer-pane" style="display: none;">
                <div id="bd-pptx-container" style="background: #fff; padding: 1.5rem; border-radius: 8px; min-height: 400px; max-height: 72vh; overflow-y: auto; color: #1e293b;">
                    <div id="bd-pptx-header" style="border-bottom: 2px solid #fed7aa; padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 44px; height: 44px; border-radius: 8px; background: #fff7ed; border: 1px solid #fed7aa; color: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="file powerpoint icon" style="margin: 0;"></i>
                            </div>
                            <div>
                                <h3 id="bd-pptx-title" style="margin: 0; color: #9a3412;">Presentasi PowerPoint</h3>
                                <div id="bd-pptx-info" style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">Mendeteksi slide...</div>
                            </div>
                        </div>
                    </div>

                    <div id="bd-pptx-thumbnail-box" style="display: none; text-align: center; margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 0.5rem; text-transform: uppercase;">Sampul / Thumbnail Presentasi</div>
                        <img id="bd-pptx-thumbnail" src="" alt="Thumbnail Slide" style="max-height: 260px; max-width: 100%; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    </div>

                    <div id="bd-pptx-slides-list" style="display: flex; flex-direction: column; gap: 1rem;">
                        {{-- Extracted slides rendered here --}}
                    </div>
                </div>
            </div>

            {{-- Audio Viewer Container --}}
            <div id="bd-view-audio" class="bd-viewer-pane" style="display: none; text-align: center; padding: 3rem 1rem;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #fffbeb; border: 1px solid #fde68a; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.5rem auto;">
                    <i class="volume up icon" style="margin: 0;"></i>
                </div>
                <audio id="bd-audio-element" controls style="width: 100%; max-width: 500px;"></audio>
            </div>

            {{-- Fallback Container --}}
            <div id="bd-view-fallback" class="bd-viewer-pane" style="display: none; text-align: center; padding: 3.5rem 1.5rem; color: #f8fafc;">
                <div id="bd-fallback-icon" style="width: 72px; height: 72px; border-radius: 12px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.25rem auto; color: #cbd5e1;">
                    <i class="file icon" style="margin: 0;"></i>
                </div>
                <h3 id="bd-fallback-name" style="margin: 0 0 0.5rem 0; color: #fff;">Nama Berkas</h3>
                <p id="bd-fallback-msg" style="color: #94a3b8; font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                    Pratinjau langsung tidak didukung untuk jenis berkas ini. Anda dapat mengunduh berkas untuk membukanya langsung di perangkat Anda.
                </p>
                <a id="bd-fallback-btn-download" href="#" target="_blank" data-no-loader="true" class="ui primary large button">
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

            window.confirmDelete = function (formId, message) {
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

            window.handleFileChange = function (input) {
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

            window.removeFile = function (btn, index) {
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

            window.renderFilePreview = function (input) {
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

                        let iconHtml = '<i class="file icon large" style="margin:0;"></i>';
                        let bgColor = '#10b981';

                        if (file.type.startsWith('image/')) {
                            const objUrl = URL.createObjectURL(file);
                            iconHtml = `<img src="${objUrl}" alt="Preview" onload="URL.revokeObjectURL(this.src)">`;
                            bgColor = 'transparent';
                        } else if (file.type === 'application/pdf') {
                            iconHtml = '<i class="file pdf icon large" style="margin:0;"></i>';
                            bgColor = '#ef4444';
                        } else if (file.name.match(/\.(doc|docx)$/i)) {
                            iconHtml = '<i class="file word icon large" style="margin:0;"></i>';
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

            document.addEventListener("wheel", function (event) {
                if (document.activeElement.type === "number") {
                    event.preventDefault();
                }
            }, {
                passive: false
            });

            /* ══════════════════════════════════════════════════════════════
               BUKTI DUKUNG TAMBAHAN: FOLDER EXPLORER & PREVIEWER
            ══════════════════════════════════════════════════════════════ */
            window.toggleBdFolder = function (itemId) {
                var content = document.getElementById('content-' + itemId);
                var chevron = document.getElementById('chevron-' + itemId);
                var icon = document.getElementById('folder-icon-' + itemId);
                if (!content) return;

                var isHidden = content.style.display === 'none' || !content.style.display;
                if (isHidden) {
                    content.style.display = 'block';
                    if (chevron) chevron.classList.add('down');
                    if (icon) {
                        icon.classList.remove('folder');
                        icon.classList.add('folder', 'open');
                    }
                } else {
                    content.style.display = 'none';
                    if (chevron) chevron.classList.remove('down');
                    if (icon) {
                        icon.classList.remove('open');
                        icon.classList.add('folder');
                    }
                }
            };

            window.expandAllBdFolders = function () {
                document.querySelectorAll('.bd-folder-content').forEach(function (el) {
                    el.style.display = 'block';
                });
                document.querySelectorAll('.bd-chevron').forEach(function (el) {
                    el.classList.add('down');
                });
                document.querySelectorAll('.bd-folder-icon').forEach(function (el) {
                    el.classList.remove('folder');
                    el.classList.add('folder', 'open');
                });
            };

            window.collapseAllBdFolders = function () {
                document.querySelectorAll('.bd-folder-content').forEach(function (el) {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.bd-chevron').forEach(function (el) {
                    el.classList.remove('down');
                });
                document.querySelectorAll('.bd-folder-icon').forEach(function (el) {
                    el.classList.remove('open');
                    el.classList.add('folder');
                });
            };

            var currentBdCategory = 'all';

            window.filterBdCategory = function (category, btn) {
                currentBdCategory = category;
                document.querySelectorAll('.bd-tab-btn').forEach(function (b) {
                    b.classList.remove('active');
                });
                if (btn) btn.classList.add('active');
                applyBdFilters();
            };

            window.filterBdItems = function () {
                var input = document.getElementById('bd-search-input');
                var clearBtn = document.getElementById('bd-search-clear');
                if (clearBtn) {
                    clearBtn.style.display = (input && input.value.trim().length > 0) ? 'block' : 'none';
                }
                applyBdFilters();
            };

            window.clearBdSearch = function () {
                var input = document.getElementById('bd-search-input');
                var clearBtn = document.getElementById('bd-search-clear');
                if (input) input.value = '';
                if (clearBtn) clearBtn.style.display = 'none';
                applyBdFilters();
            };

            function applyBdFilters() {
                var searchInput = document.getElementById('bd-search-input');
                var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                var files = document.querySelectorAll('.bd-file-item');
                var visibleCount = 0;

                files.forEach(function (fileEl) {
                    var filename = fileEl.getAttribute('data-filename') || '';
                    var cat = fileEl.getAttribute('data-category') || '';

                    // Match category
                    var matchCat = true;
                    if (currentBdCategory === 'all') {
                        matchCat = true;
                    } else if (currentBdCategory === 'office') {
                        matchCat = (cat === 'word' || cat === 'ppt' || cat === 'excel');
                    } else {
                        matchCat = (cat === currentBdCategory);
                    }

                    // Match search query
                    var matchQuery = true;
                    if (query.length > 0) {
                        matchQuery = filename.indexOf(query) !== -1;
                    }

                    if (matchCat && matchQuery) {
                        fileEl.style.display = 'flex';
                        visibleCount++;

                        // Open all ancestor folders so matching file is visible
                        var parentContent = fileEl.closest('.bd-folder-content');
                        while (parentContent) {
                            parentContent.style.display = 'block';
                            var parentFolder = parentContent.closest('.bd-folder-item');
                            if (parentFolder) {
                                var chevron = parentFolder.querySelector('.bd-chevron');
                                var icon = parentFolder.querySelector('.bd-folder-icon');
                                if (chevron) chevron.classList.add('down');
                                if (icon) {
                                    icon.classList.remove('folder');
                                    icon.classList.add('folder', 'open');
                                }
                            }
                            parentContent = parentFolder ? parentFolder.parentElement.closest('.bd-folder-content') : null;
                        }
                    } else {
                        fileEl.style.display = 'none';
                    }
                });

                var noResults = document.getElementById('bd-no-results');
                if (noResults) {
                    noResults.style.display = (visibleCount === 0 && files.length > 0) ? 'block' : 'none';
                }
            }

            // Image Zoom & Rotate State
            var bdImgScale = 1;
            var bdImgRotate = 0;

            window.zoomBdImage = function (factor) {
                bdImgScale = Math.max(0.2, Math.min(bdImgScale * factor, 5));
                updateBdImageTransform();
            };

            window.rotateBdImage = function () {
                bdImgRotate = (bdImgRotate + 90) % 360;
                updateBdImageTransform();
            };

            window.resetBdImage = function () {
                bdImgScale = 1;
                bdImgRotate = 0;
                updateBdImageTransform();
            };

            function updateBdImageTransform() {
                var img = document.getElementById('bd-img-element');
                if (img) {
                    img.style.transform = 'scale(' + bdImgScale + ') rotate(' + bdImgRotate + 'deg)';
                }
            }

            function stopBdMedia() {
                var video = document.getElementById('bd-video-element');
                if (video) {
                    video.pause();
                    video.removeAttribute('src');
                    video.load();
                }
                var audio = document.getElementById('bd-audio-element');
                if (audio) {
                    audio.pause();
                    audio.removeAttribute('src');
                    audio.load();
                }
                var frame = document.getElementById('bd-frame-pdf');
                if (frame) {
                    frame.src = 'about:blank';
                }
                var docxContainer = document.getElementById('bd-docx-container');
                if (docxContainer) {
                    docxContainer.innerHTML = '';
                }
            }

            window.closeBdModal = function () {
                stopBdMedia();
                $('#modal-preview-bukti-dukung').modal('hide');
            };

            window.reloadBdPdf = function () {
                var pdfFrame = document.getElementById('bd-frame-pdf');
                var loader = document.getElementById('bd-modal-loader');
                var loaderText = document.getElementById('bd-modal-loader-text');
                if (pdfFrame && pdfFrame.src && pdfFrame.src !== 'about:blank') {
                    var current = pdfFrame.src;
                    if (loader) {
                        loaderText.textContent = 'Memuat ulang pratinjau PDF...';
                        loader.style.display = 'flex';
                    }
                    pdfFrame.src = 'about:blank';
                    setTimeout(function () {
                        pdfFrame.onload = function () {
                            if (loader) loader.style.display = 'none';
                        };
                        pdfFrame.src = current;
                        setTimeout(function () {
                            if (loader) loader.style.display = 'none';
                        }, 2500);
                    }, 120);
                }
            };

            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return (text || '').replace(/[&<>"']/g, function (m) { return map[m]; });
            }

            window.openBdPreview = function (file) {
                if (!file) return;

                // Set header metadata
                var titleEl = document.getElementById('bd-modal-title');
                var extEl = document.getElementById('bd-modal-ext');
                var sizeEl = document.getElementById('bd-modal-size');
                var iconEl = document.getElementById('bd-modal-icon');
                var btnExt = document.getElementById('bd-modal-btn-external');
                var btnDl = document.getElementById('bd-modal-btn-download');

                if (titleEl) titleEl.textContent = file.name;
                if (extEl) extEl.textContent = file.extension || 'FILE';
                if (sizeEl) sizeEl.textContent = file.formatted_size || '';
                if (btnExt) btnExt.href = file.url;
                if (btnDl) btnDl.href = file.download_url;

                // Set icon & color
                var iconMap = {
                    pdf: { icon: 'file pdf', color: '#ef4444' },
                    word: { icon: 'file word', color: '#2563eb' },
                    ppt: { icon: 'file powerpoint', color: '#ea580c' },
                    excel: { icon: 'file excel', color: '#16a34a' },
                    image: { icon: 'file image', color: '#059669' },
                    video: { icon: 'file video', color: '#7c3aed' },
                    audio: { icon: 'volume up', color: '#d97706' },
                };
                var iconInfo = iconMap[file.category] || { icon: 'file', color: '#64748b' };
                if (iconEl) {
                    iconEl.innerHTML = '<i class="' + iconInfo.icon + ' icon" style="margin:0;"></i>';
                    iconEl.style.color = iconInfo.color;
                }

                // Hide all viewer panes
                document.querySelectorAll('.bd-viewer-pane').forEach(function (pane) {
                    pane.style.display = 'none';
                });

                var loader = document.getElementById('bd-modal-loader');
                var loaderText = document.getElementById('bd-modal-loader-text');
                if (loader) loader.style.display = 'none';

                var cat = file.category;
                var ext = (file.extension || '').toLowerCase();

                // Open Modal first so dimensions are ready
                $('#modal-preview-bukti-dukung').modal({
                    autofocus: false,
                    observeChanges: true,
                    closable: true,
                    onHidden: function () {
                        stopBdMedia();
                    }
                }).modal('show');

                // Process by category
                if (cat === 'pdf') {
                    var pdfPane = document.getElementById('bd-view-pdf');
                    var pdfFrame = document.getElementById('bd-frame-pdf');
                    var pdfOpen = document.getElementById('bd-pdf-btn-open');
                    var pdfDl = document.getElementById('bd-pdf-btn-dl');
                    var pdfSizeBadge = document.getElementById('bd-pdf-filesize-badge');
                    if (pdfPane && pdfFrame) {
                        pdfPane.style.display = 'block';
                        if (pdfOpen) pdfOpen.href = file.url;
                        if (pdfDl) pdfDl.href = file.download_url;
                        if (pdfSizeBadge) pdfSizeBadge.textContent = file.formatted_size ? '(' + file.formatted_size + ')' : '';

                        if (loader) {
                            loaderText.textContent = 'Memuat pratinjau dokumen PDF...';
                            loader.style.display = 'flex';
                        }

                        var loadTimeout = setTimeout(function () {
                            if (loader) loader.style.display = 'none';
                        }, 2500);

                        pdfFrame.onload = function () {
                            clearTimeout(loadTimeout);
                            if (loader) loader.style.display = 'none';
                        };

                        pdfFrame.src = file.url;
                    }
                } else if (cat === 'image') {
                    var imgPane = document.getElementById('bd-view-image');
                    var imgEl = document.getElementById('bd-img-element');
                    if (imgPane && imgEl) {
                        imgPane.style.display = 'block';
                        imgEl.src = file.url;
                        resetBdImage();
                    }
                } else if (cat === 'video') {
                    var videoPane = document.getElementById('bd-view-video');
                    var videoEl = document.getElementById('bd-video-element');
                    if (videoPane && videoEl) {
                        videoPane.style.display = 'block';
                        videoEl.src = file.url;
                        videoEl.load();
                        videoEl.play().catch(function () {});
                    }
                } else if (cat === 'word') {
                    if (ext === 'docx') {
                        var docxPane = document.getElementById('bd-view-docx');
                        var docxContainer = document.getElementById('bd-docx-container');
                        if (docxPane && docxContainer) {
                            docxPane.style.display = 'block';
                            docxContainer.innerHTML = '';
                            if (loader) {
                                loaderText.textContent = 'Memuat dan merender dokumen Word (.docx)...';
                                loader.style.display = 'flex';
                            }

                            fetch(file.url)
                                .then(function (res) {
                                    if (!res.ok) throw new Error('HTTP ' + res.status);
                                    return res.blob();
                                })
                                .then(function (blob) {
                                    if (typeof docx !== 'undefined' && docx.renderAsync) {
                                        return docx.renderAsync(blob, docxContainer, null, {
                                            inWrapper: false,
                                            ignoreWidth: false,
                                            ignoreHeight: false,
                                            breakPages: true,
                                            useBase64URL: true
                                        });
                                    } else {
                                        throw new Error('Pustaka docx-preview tidak ditemukan.');
                                    }
                                })
                                .then(function () {
                                    if (loader) loader.style.display = 'none';
                                })
                                .catch(function (err) {
                                    console.error('Error rendering docx:', err);
                                    if (loader) loader.style.display = 'none';
                                    docxContainer.innerHTML = '<div style="text-align: center; padding: 3rem 1rem; color: #dc2626;">' +
                                        '<i class="exclamation triangle icon large"></i>' +
                                        '<p style="margin-top: 0.5rem; font-weight: 600;">Gagal memproses pratinjau Word langsung.</p>' +
                                        '<p style="color: #64748b; font-size: 0.85rem;">' + escapeHtml(err.message) + '</p>' +
                                        '<a href="' + file.download_url + '" class="ui mini primary button" target="_blank" data-no-loader="true" style="margin-top: 1rem;"><i class="download icon"></i> Unduh Berkas Word</a>' +
                                        '</div>';
                                });
                        }
                    } else {
                        // Older .doc
                        showFallbackPane(file, 'Format dokumen Word versi lawas (.doc). Pratinjau langsung di website disarankan menggunakan format .docx. Silakan unduh dokumen untuk membukanya di Microsoft Word.');
                    }
                } else if (cat === 'ppt') {
                    if (ext === 'pptx') {
                        var pptxPane = document.getElementById('bd-view-pptx');
                        var pptxTitle = document.getElementById('bd-pptx-title');
                        var pptxInfo = document.getElementById('bd-pptx-info');
                        var slidesList = document.getElementById('bd-pptx-slides-list');
                        var thumbBox = document.getElementById('bd-pptx-thumbnail-box');
                        var thumbImg = document.getElementById('bd-pptx-thumbnail');

                        if (pptxPane) {
                            pptxPane.style.display = 'block';
                            if (pptxTitle) pptxTitle.textContent = file.name;
                            if (pptxInfo) pptxInfo.textContent = 'Menganalisis slide presentasi...';
                            if (slidesList) slidesList.innerHTML = '';
                            if (thumbBox) thumbBox.style.display = 'none';

                            if (loader) {
                                loaderText.textContent = 'Membaca struktur slide PowerPoint (.pptx)...';
                                loader.style.display = 'flex';
                            }

                            fetch(file.url)
                                .then(function (res) {
                                    if (!res.ok) throw new Error('HTTP ' + res.status);
                                    return res.arrayBuffer();
                                })
                                .then(async function (arrayBuffer) {
                                    if (typeof JSZip === 'undefined') {
                                        throw new Error('Pustaka JSZip tidak ditemukan.');
                                    }
                                    var zip = await JSZip.loadAsync(arrayBuffer);

                                    // Check thumbnail
                                    var thumbFile = zip.file('docProps/thumbnail.jpeg') || zip.file('docProps/thumbnail.jpg') || zip.file('docProps/thumbnail.png');
                                    if (thumbFile && thumbImg && thumbBox) {
                                        var base64 = await thumbFile.async('base64');
                                        var mime = thumbFile.name.endsWith('.png') ? 'image/png' : 'image/jpeg';
                                        thumbImg.src = 'data:' + mime + ';base64,' + base64;
                                        thumbBox.style.display = 'block';
                                    }

                                    // Find slides
                                    var slideFiles = [];
                                    zip.forEach(function (relativePath) {
                                        if (/^ppt\/slides\/slide[0-9]+\.xml$/i.test(relativePath)) {
                                            slideFiles.push(relativePath);
                                        }
                                    });

                                    slideFiles.sort(function (a, b) {
                                        var numA = parseInt(a.match(/slide([0-9]+)\.xml/i)[1], 10);
                                        var numB = parseInt(b.match(/slide([0-9]+)\.xml/i)[1], 10);
                                        return numA - numB;
                                    });

                                    if (pptxInfo) {
                                        pptxInfo.textContent = 'Terdeteksi ' + slideFiles.length + ' Slide Presentasi';
                                    }

                                    if (slideFiles.length === 0) {
                                        slidesList.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 2rem;">Tidak ada slide yang terdeteksi dalam berkas presentasi ini.</div>';
                                        return;
                                    }

                                    var parser = new DOMParser();
                                    for (var i = 0; i < slideFiles.length; i++) {
                                        var slideXml = await zip.file(slideFiles[i]).async('string');
                                        var xmlDoc = parser.parseFromString(slideXml, 'text/xml');
                                        var textNodes = xmlDoc.getElementsByTagName('a:t');
                                        var lines = [];
                                        for (var t = 0; t < textNodes.length; t++) {
                                            var txt = (textNodes[t].textContent || '').trim();
                                            if (txt) lines.push(txt);
                                        }

                                        var card = document.createElement('div');
                                        card.style.cssText = 'border: 1px solid #fed7aa; background: #fffdfa; border-radius: 8px; padding: 1.2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';

                                        var headerHtml = '<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #fee0b2; padding-bottom: 0.5rem; margin-bottom: 0.75rem;">' +
                                            '<span style="font-weight: 700; color: #ea580c; font-size: 0.92rem;"><i class="play circle icon"></i> Slide ' + (i + 1) + '</span>' +
                                            '<span style="font-size: 0.75rem; color: #94a3b8;">' + (lines.length > 0 ? lines.length + ' poin teks' : 'Slide visual / diagram') + '</span>' +
                                            '</div>';

                                        var bodyHtml = '';
                                        if (lines.length > 0) {
                                            bodyHtml += '<div style="display: flex; flex-direction: column; gap: 0.4rem;">';
                                            var isTitle = true;
                                            for (var l = 0; l < lines.length; l++) {
                                                if (isTitle && lines[l].length < 100) {
                                                    bodyHtml += '<div style="font-weight: 700; font-size: 1.05rem; color: #1e293b; margin-bottom: 0.2rem;">' + escapeHtml(lines[l]) + '</div>';
                                                    isTitle = false;
                                                } else {
                                                    isTitle = false;
                                                    bodyHtml += '<div style="font-size: 0.88rem; color: #475569; display: flex; align-items: flex-start; gap: 0.5rem;">' +
                                                        '<span style="color: #ea580c; font-weight: bold; line-height: 1.2;">&bull;</span>' +
                                                        '<span>' + escapeHtml(lines[l]) + '</span>' +
                                                        '</div>';
                                                }
                                            }
                                            bodyHtml += '</div>';
                                        } else {
                                            bodyHtml += '<div style="color: #94a3b8; font-size: 0.85rem; font-style: italic;"><i class="image icon"></i> Slide berupa media gambar atau diagram grafik.</div>';
                                        }

                                        card.innerHTML = headerHtml + bodyHtml;
                                        slidesList.appendChild(card);
                                    }
                                })
                                .then(function () {
                                    if (loader) loader.style.display = 'none';
                                })
                                .catch(function (err) {
                                    console.error('Error parsing pptx:', err);
                                    if (loader) loader.style.display = 'none';
                                    slidesList.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">' +
                                        '<i class="exclamation triangle icon large"></i>' +
                                        '<p style="margin-top: 0.5rem; font-weight: 600;">Gagal membaca slide PowerPoint secara otomatis.</p>' +
                                        '<a href="' + file.download_url + '" class="ui mini orange button" target="_blank" data-no-loader="true" style="margin-top: 0.75rem;"><i class="download icon"></i> Unduh Berkas PowerPoint</a>' +
                                        '</div>';
                                });
                        }
                    } else {
                        // Older .ppt
                        showFallbackPane(file, 'Format presentasi PowerPoint versi lawas (.ppt). Pratinjau langsung di website disarankan menggunakan format .pptx. Silakan unduh presentasi untuk membukanya di Microsoft PowerPoint.');
                    }
                } else if (cat === 'audio') {
                    var audioPane = document.getElementById('bd-view-audio');
                    var audioEl = document.getElementById('bd-audio-element');
                    if (audioPane && audioEl) {
                        audioPane.style.display = 'block';
                        audioEl.src = file.url;
                        audioEl.load();
                        audioEl.play().catch(function () {});
                    }
                } else {
                    showFallbackPane(file);
                }
            };

            function showFallbackPane(file, customMsg) {
                var fallbackPane = document.getElementById('bd-view-fallback');
                var nameEl = document.getElementById('bd-fallback-name');
                var msgEl = document.getElementById('bd-fallback-msg');
                var dlBtn = document.getElementById('bd-fallback-btn-download');

                if (fallbackPane) {
                    fallbackPane.style.display = 'block';
                    if (nameEl) nameEl.textContent = file.name;
                    if (msgEl && customMsg) msgEl.textContent = customMsg;
                    if (dlBtn) dlBtn.href = file.download_url;
                }
            }

            window.openFilePreview = window.openBdPreview;
        </script>
    @endpush

</x-volt-app>