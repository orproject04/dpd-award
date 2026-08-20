<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <div class="mobile-action-buttons" style="display: flex; gap: 0.3rem; justify-content: flex-end; align-items: center;">
            @if (auth()->user()->hasPermission('*') || auth()->user()->hasPermission(\App\Enums\Permission::UPDATE_DATA_PENDAFTAR))
                <button type="button" class="ui mini button orange" onclick="$('#modal-import-keterangan').modal('show')" style="margin: 0;">
                    <i class="upload icon"></i> <span class="desktop-text">Update Catatan</span><span class="mobile-text">Catatan</span>
                </button>
            @endif
            <a href="{{ route('modules::pendaftar.export', request()->query()) }}" target="_blank" class="ui mini teal button" style="margin: 0;" data-no-loader="true">
                <i class="file excel icon"></i> <span class="desktop-text">Export Excel</span><span class="mobile-text">Excel</span>
            </a>
            <a href="{{ route('modules::pendaftar.download-all-zip', request()->query()) }}" target="_blank" class="ui mini blue button" style="margin: 0;" data-no-loader="true">
                <i class="archive icon"></i> <span class="desktop-text">Unduh Semua Berkas (ZIP)</span><span class="mobile-text">ZIP</span>
            </a>
        </div>
    </x-slot>

    {!! $table !!}

    {{-- Modal Update Catatan Verifikator --}}
    <div class="ui modal small" id="modal-import-keterangan" style="position: relative;">
        <i class="close icon"
            style="top: 1.2rem !important; right: 1.2rem !important; color: #64748b !important; position: absolute !important;"></i>
        <div class="header" style="padding-right: 3rem;">
            <i class="file excel icon"></i> Update Catatan Verifikator
        </div>
        <div class="content">
            <form id="form-import-keterangan" action="{{ route('modules::pendaftar.import-keterangan') }}"
                method="POST" enctype="multipart/form-data" class="ui form">
                @csrf
                <div class="field">
                    <label>Template Excel</label>
                    <p style="font-size: 0.9em; color: #666; margin-bottom: 0.5rem;">
                        Gunakan template Excel berikut untuk mengisi Nomor Registrasi dan Catatan Verifikator yang ingin
                        diperbarui.
                    </p>
                    <a href="{{ route('modules::pendaftar.template-keterangan') }}" class="ui button basic teal compact"
                        target="_blank" data-no-loader="true">
                        <i class="download icon"></i> Unduh Template Excel (.xlsx)
                    </a>
                </div>
                <div class="ui divider"></div>
                <div class="field required">
                    <label>File Excel (.xlsx / .xls)</label>
                    <input type="file" name="file" accept=".xlsx, .xls" required>
                </div>
            </form>
        </div>
        <div class="actions">
            <button type="button" class="ui button deny">Batal</button>
            <button type="submit" form="form-import-keterangan" class="ui button primary">
                <i class="upload icon"></i> Simpan / Upload
            </button>
        </div>
    </div>

    <style>
        .mobile-action-buttons {
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .mobile-text {
            display: none;
        }

        @media (max-width: 767px) {
            .mobile-action-buttons {
                flex-wrap: nowrap !important;
                white-space: nowrap !important;
            }
            
            .desktop-text {
                display: none !important;
            }

            .mobile-text {
                display: inline !important;
            }

            .mobile-action-buttons .ui.button {
                padding: 0.6em 0.8em !important;
                font-size: 0.8rem !important;
            }

            /* 1. Force all menu parts to stack and align perfectly */
            .ui.borderless.stackable.menu .menu.right {
                width: 100% !important;
                display: block !important;
            }

            /* Search box item */
            .ui.borderless.stackable.menu>.item {
                display: block !important;
                width: 100% !important;
                padding: 1rem 1rem 0.25rem 1rem !important;
                /* 0.25rem bottom to form a 0.5rem gap */
                margin: 0 !important;
            }

            /* Filters item */
            .ui.borderless.stackable.menu .menu.right .item {
                display: block !important;
                width: 100% !important;
                padding: 0.25rem 1rem 1rem 1rem !important;
                /* 0.25rem top to complete the 0.5rem gap */
                margin: 0 !important;
            }

            /* 2. Target the specific filter form that uses Tailwind 'flex items-center' */
            form[id^="inline-filter-form-"] {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 0.5rem !important;
            }

            /* 3. Make the wrappers, fields, and dropdowns take full width */
            form[id^="inline-filter-form-"]>div,
            form[id^="inline-filter-form-"] .field,
            form[id^="inline-filter-form-"] .ui.dropdown {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }

            /* 4. The search input container */
            .ui.borderless.stackable.menu>.item>form {
                display: block !important;
                width: 100% !important;
            }

            .ui.action.input {
                width: 100% !important;
            }
        }
    </style>
</x-volt-app>
