<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <div class="mobile-action-buttons" style="display: flex; gap: 0.3rem; justify-content: flex-end; align-items: center;">
            @php
                $hasRestrictedView = auth()->user()->hasPermission(\App\Enums\Permission::PENILAIAN_VIEW_TERBATAS) && !auth()->user()->hasPermission('*');
            @endphp
            @if (!$hasRestrictedView)
                <button type="button" class="ui mini button orange" onclick="$('#modal-import-keterangan').modal('show')" style="margin: 0;">
                    <i class="upload icon"></i> <span class="desktop-text">Update Catatan</span><span class="mobile-text">Catatan</span>
                </button>
            @endif
            <a href="{{ route('modules::pendaftar.export', request()->query()) }}" target="_blank" class="ui mini teal button" style="margin: 0;" data-no-loader="true">
                <i class="file excel icon"></i> <span class="desktop-text">Export Excel</span><span class="mobile-text">Excel</span>
            </a>
            
            <div class="ui pointing dropdown button mini primary" style="margin: 0;" id="export-kertas-kerja-dropdown">
                <span class="text"><i class="download icon"></i> <span class="desktop-text">Semua Kertas Kerja</span><span class="mobile-text">Kertas Kerja</span></span>
                <div class="menu">
                    <a href="javascript:void(0)" onclick="startBatchExport('excel')" class="item" data-no-loader="true">
                        <i class="file excel icon green"></i> Dalam format Excel
                    </a>
                    <a href="javascript:void(0)" onclick="startBatchExport('pdf')" class="item" data-no-loader="true">
                        <i class="file pdf icon red"></i> Dalam format PDF
                    </a>
                </div>
            </div>

            {{-- 
            <a href="{{ route('modules::pendaftar.download-all-zip', request()->query()) }}" target="_blank" class="ui mini blue button" style="margin: 0;" data-no-loader="true">
                <i class="archive icon"></i> <span class="desktop-text">Semua Berkas (ZIP)</span><span class="mobile-text">ZIP</span>
            </a>
            --}}
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.jQuery && jQuery('#export-kertas-kerja-dropdown').dropdown) {
                    // Hancurkan inisialisasi default dari Laravolt jika ada
                    jQuery('#export-kertas-kerja-dropdown').dropdown('destroy');
                    // Inisialisasi ulang dengan mode hide (tidak mengubah teks)
                    jQuery('#export-kertas-kerja-dropdown').dropdown({ action: 'hide' });
                }
            });
        </script>
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

    <!-- Modal Batch Export -->
    <div class="ui tiny modal" id="modal-batch-export" data-backdrop="static" data-keyboard="false">
        <div class="header">
            <i class="download icon"></i> Mengekspor <span id="batch-export-format-label"></span>
        </div>
        <div class="content">
            <p id="batch-export-message">Menghitung total data yang akan diekspor...</p>
            <div class="ui indicating progress" id="batch-export-progress" data-value="0" data-total="100">
                <div class="bar">
                    <div class="progress"></div>
                </div>
                <div class="label" id="batch-export-progress-label">Menyiapkan...</div>
            </div>
        </div>
    </div>

    <script>
        function startBatchExport(format) {
            // Paksa kembalikan teks tombol jika Semantic UI mengubahnya
            setTimeout(function() {
                $('#export-kertas-kerja-dropdown .text').html('<i class="download icon"></i> <span class="desktop-text">Semua Kertas Kerja</span><span class="mobile-text">Kertas Kerja</span>');
            }, 10);
            
            $('#batch-export-format-label').text(format === 'excel' ? 'Excel' : 'PDF');
            $('#batch-export-message').text('Menginisialisasi penarikan data...');
            $('#batch-export-progress').progress('reset');
            $('#batch-export-progress-label').text('0 / 0 Diproses');
            
            $('#modal-batch-export').modal({
                closable: false
            }).modal('show');
            
            // Collect current query params for filtering
            var queryString = window.location.search;
            var initUrl = "{{ route('modules::pendaftar.export-batch-init') }}" + queryString + (queryString ? '&' : '?') + 'format=' + format;
            
            $.ajax({
                url: initUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success === false) {
                        $('#modal-batch-export').modal('hide');
                        alert(response.message || 'Terjadi kesalahan.');
                        return;
                    }
                    
                    var batchId = response.batch_id;
                    var totalChunks = response.total_chunks;
                    var totalParticipants = response.total_participants;
                    
                    $('#batch-export-progress').progress({
                        total: totalChunks
                    });
                    
                    processBatchChunk(batchId, format, 0, totalChunks, totalParticipants);
                },
                error: function() {
                    $('#modal-batch-export').modal('hide');
                    alert('Gagal menginisialisasi export batch. Silakan coba lagi.');
                }
            });
        }
        
        function processBatchChunk(batchId, format, chunkIndex, totalChunks, totalParticipants) {
            $('#batch-export-message').text('Memproses ' + totalParticipants + ' peserta (Bagian ' + (chunkIndex + 1) + ' dari ' + totalChunks + ')...');
            
            var processUrl = "{{ route('modules::pendaftar.export-batch-process') }}";
            
            $.ajax({
                url: processUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    batch_id: batchId,
                    format: format,
                    chunk_index: chunkIndex
                },
                success: function(response) {
                    $('#batch-export-progress').progress('increment');
                    $('#batch-export-progress-label').text((chunkIndex + 1) + ' / ' + totalChunks + ' Diproses');
                    
                    if (chunkIndex + 1 < totalChunks) {
                        // Process next chunk
                        processBatchChunk(batchId, format, chunkIndex + 1, totalChunks, totalParticipants);
                    } else {
                        // All chunks processed!
                        $('#batch-export-message').text('Selesai! Sedang mengompres ke dalam format ZIP...');
                        
                        setTimeout(function() {
                            $('#modal-batch-export').modal('hide');
                            // Trigger download
                            var downloadUrl = "{{ route('modules::pendaftar.export-batch-download') }}?batch_id=" + batchId + "&format=" + format;
                            window.location.href = downloadUrl;
                        }, 1000);
                    }
                },
                error: function() {
                    $('#modal-batch-export').modal('hide');
                    alert('Terjadi kesalahan saat memproses chunk ke-' + (chunkIndex + 1) + '. Proses dihentikan.');
                }
            });
        }
    </script>
</x-volt-app>
