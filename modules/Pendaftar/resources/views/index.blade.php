<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <div class="mobile-action-buttons" style="display: flex; gap: 0.3rem; justify-content: flex-end; align-items: center;">
            @php
                $hasRestrictedView = auth()->user()->hasPermission(\App\Enums\Permission::PENILAIAN_VIEW_TERBATAS) && !auth()->user()->hasPermission('*');
            @endphp
            @if (!$hasRestrictedView)
                <button type="button" class="ui mini button violet" onclick="startCertificateEmailBlast()" style="margin: 0;" data-no-loader="true">
                    <i class="envelope icon"></i> <span class="desktop-text">Kirim Email Sertifikat</span><span class="mobile-text">Kirim Email</span>
                </button>
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
            flex-wrap: wrap;
            white-space: nowrap;
        }

        .mobile-text {
            display: none;
        }

        @media (max-width: 767px) {
            .mobile-action-buttons {
                flex-wrap: wrap !important;
                white-space: nowrap !important;
            }
            
            .desktop-text {
                display: none !important;
            }

            .mobile-text {
                display: inline !important;
            }

            .ui.mini.button {
                padding: 0.5em 0.5em !important;
                font-size: 0.75rem !important;
            }

            .ui.pointing.dropdown.button.mini {
                padding: 0.5em 0.5em !important;
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
    
    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statuses = {!! json_encode([
                    'Diajukan' => 'Diajukan',
                    'Lolos Verifikasi Berkas' => 'Lolos Verifikasi Berkas',
                    'Lolos ke Tahap 50 Besar' => 'Lolos ke Tahap 50 Besar',
                    'Lolos ke Tahap 10 Besar' => 'Lolos ke Tahap 10 Besar',
                    'Lolos ke Tahap 3 Besar' => 'Lolos ke Tahap 3 Besar',
                    'Lolos ke Tahap Wawancara' => 'Lolos ke Tahap Wawancara',
                    'Lolos ke Tahap Final' => 'Lolos ke Tahap Final',
                    'Tidak Lolos' => 'Tidak Lolos'
                ]) !!};

                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.edit-status-btn');
                    if (btn) {
                        e.preventDefault();
                        
                        if (typeof Swal === 'undefined') {
                            console.error('SweetAlert2 is not loaded!');
                            return;
                        }

                        const pendaftarId = btn.getAttribute('data-id');
                        const currentStatus = btn.getAttribute('data-status');

                        Swal.fire({
                            title: 'Ubah Status Pendaftar',
                            html: `
                                <div class="ui form" style="text-align: left;">
                                    <div class="field required" style="margin-bottom: 1rem;">
                                        <label>Pilih Status Baru</label>
                                        <select id="swal-status-input" class="ui dropdown fluid">
                                            ${Object.keys(statuses).map(s => `<option value="${s}" ${s === currentStatus ? 'selected' : ''}>${s}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="field required">
                                        <label>Keterangan</label>
                                        <textarea id="swal-keterangan-input" rows="3" placeholder="Contoh: Lolos berkas administrasi..."></textarea>
                                    </div>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: '<i class="save icon"></i> Perbarui',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'ui primary button',
                                cancelButton: 'ui button'
                            },
                            buttonsStyling: false,
                            preConfirm: () => {
                                const newStatus = document.getElementById('swal-status-input').value;
                                const keterangan = document.getElementById('swal-keterangan-input').value;
                                if (!keterangan.trim()) {
                                    Swal.showValidationMessage('Keterangan wajib diisi!');
                                    return false;
                                }
                                return { newStatus, keterangan };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `{{ route('modules::pendaftar.update-status', ':id') }}`.replace(':id', pendaftarId);
                                
                                const csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = '{{ csrf_token() }}';
                                form.appendChild(csrfInput);

                                const statusInput = document.createElement('input');
                                statusInput.type = 'hidden';
                                statusInput.name = 'status';
                                statusInput.value = result.value.newStatus;
                                form.appendChild(statusInput);

                                const ketInput = document.createElement('input');
                                ketInput.type = 'hidden';
                                ketInput.name = 'keterangan';
                                ketInput.value = result.value.keterangan;
                                form.appendChild(ketInput);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    }
                });
            });

            function escapeHtmlCert(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function getActiveFilterQueryString() {
                const params = new URLSearchParams(window.location.search);
                ['kategori-filter', 'provinsi-filter', 'status-filter', 'email-sertifikat-filter'].forEach(function(key) {
                    const el = document.querySelector(`[name="${key}"]`);
                    if (el) {
                        params.set(key, el.value || '');
                    }
                });
                const searchEl = document.querySelector('input[name="search"]');
                if (searchEl) {
                    params.set('search', searchEl.value || '');
                }
                const qs = params.toString();
                return qs ? '?' + qs : '';
            }

            window.startCertificateEmailBlast = async function() {
                if (typeof Swal === 'undefined') {
                    alert('SweetAlert2 belum dimuat.');
                    return;
                }

                const qs = getActiveFilterQueryString();
                const initUrl = "{{ route('modules::pendaftar.send-cert-email-init') }}" + qs;

                Swal.fire({
                    title: 'Memeriksa Data Sertifikat',
                    html: '<div style="padding:0.75rem 0;color:#475569;">Membaca filter Status aktif dan mencocokkan file PDF sertifikat...</div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const initRes = await fetch(initUrl, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const initData = await initRes.json();

                    if (!initRes.ok || !initData.success) {
                        await Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Sertifikat untuk Dikirim',
                            text: initData.message || 'Tidak ada peserta yang memenuhi syarat.',
                            confirmButtonText: 'Tutup',
                            customClass: { confirmButton: 'ui primary button' },
                            buttonsStyling: false
                        });
                        return;
                    }

                    const hasPending = initData.pending_count > 0;
                    const defaultMode = hasPending ? 'pending' : 'all';

                    const confirmRes = await Swal.fire({
                        title: 'Kirim Email Sertifikat',
                        width: 560,
                        html: `
                            <div style="text-align:left;font-size:13.5px;color:#334155;line-height:1.6;">
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:14px;">
                                    <div><strong>Filter Status (Dropdown):</strong> <span style="color:#0f766e;">${escapeHtmlCert(initData.status_filter_label)}</span></div>
                                    <div><strong>Total Peserta Tersaring:</strong> ${initData.total_filtered} peserta</div>
                                    <div><strong>Memiliki Sertifikat PDF &amp; Email Valid:</strong> <strong>${initData.total_with_certificate}</strong> peserta</div>
                                    <div style="margin-top:4px;font-size:12.5px;">
                                        <span style="color:#15803d;">&#10003; Sudah terkirim: <strong>${initData.already_sent_count}</strong></span> &nbsp;|&nbsp;
                                        <span style="color:#b45309;">&#9679; Belum terkirim / gagal: <strong>${initData.pending_count}</strong></span>
                                        ${initData.invalid_email_count > 0 ? `<br><span style="color:#dc2626;">&#9888; ${initData.invalid_email_count} peserta dilewati karena email tidak valid</span>` : ''}
                                    </div>
                                </div>
                                <div style="margin-bottom:12px;">
                                    <label style="display:block;font-weight:700;margin-bottom:6px;">Target Pengiriman</label>
                                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:5px;cursor:pointer;">
                                        <input type="radio" name="cert-send-mode" value="pending" ${defaultMode === 'pending' ? 'checked' : ''} ${!hasPending ? 'disabled' : ''}>
                                        <span>Hanya yang belum berhasil dikirim (${initData.pending_count} email)</span>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                        <input type="radio" name="cert-send-mode" value="all" ${defaultMode === 'all' ? 'checked' : ''}>
                                        <span>Kirim ulang semua yang memiliki sertifikat (${initData.total_with_certificate} email)</span>
                                    </label>
                                </div>
                                <div>
                                    <label for="cert-send-limit" style="display:block;font-weight:700;margin-bottom:6px;">Batas Maksimal Sesi Ini (Keamanan Kuota Gmail)</label>
                                    <select id="cert-send-limit" class="ui dropdown fluid" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                                        <option value="0">Semua sesuai target (tanpa batas)</option>
                                        <option value="100">Maksimal 100 email (Sangat aman untuk Gmail)</option>
                                        <option value="150">Maksimal 150 email</option>
                                        <option value="250">Maksimal 250 email</option>
                                        <option value="400">Maksimal 400 email</option>
                                    </select>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="paper plane icon"></i> Mulai Kirim Email',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'ui violet button',
                            cancelButton: 'ui button'
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const selectedMode = document.querySelector('input[name="cert-send-mode"]:checked')?.value || 'pending';
                            const limitVal = parseInt(document.getElementById('cert-send-limit')?.value || '0', 10) || 0;
                            return { mode: selectedMode, limit: limitVal };
                        }
                    });

                    if (!confirmRes.isConfirmed) {
                        return;
                    }

                    const mode = confirmRes.value.mode;
                    const limit = confirmRes.value.limit;
                    const baseCount = mode === 'pending' ? initData.pending_count : initData.total_with_certificate;
                    const totalToSend = limit > 0 ? Math.min(baseCount, limit) : baseCount;

                    if (totalToSend <= 0) {
                        await Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Email Baru',
                            text: 'Semua peserta pada filter ini sudah pernah dikirimi sertifikat.',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'ui primary button' },
                            buttonsStyling: false
                        });
                        return;
                    }

                    let stopRequested = false;

                    Swal.fire({
                        title: 'Mengirim Email Sertifikat...',
                        width: 520,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        showCancelButton: false,
                        html: `
                            <div style="text-align:center;padding:6px 4px;">
                                <div style="margin-bottom:12px;font-size:15px;color:#1e293b;">
                                    <i class="notched circle loading icon violet" style="font-size:1.3em;"></i>
                                    Mengirim <strong id="cert-progress-counter" style="font-size:18px;color:#6d28d9;">0 dari ${totalToSend}</strong> email
                                </div>
                                <div style="width:100%;height:14px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-bottom:12px;">
                                    <div id="cert-progress-bar" style="width:0%;height:100%;background:linear-gradient(90deg,#1b6e4c,#7c3aed);transition:width 0.25s ease;"></div>
                                </div>
                                <div id="cert-progress-current" style="font-size:12.5px;color:#475569;min-height:20px;margin-bottom:12px;">
                                    Menyiapkan koneksi SMTP...
                                </div>
                                <div style="display:flex;justify-content:center;gap:16px;font-size:13px;margin-bottom:16px;">
                                    <span style="color:#15803d;font-weight:700;">Berhasil: <span id="cert-progress-sent">0</span></span>
                                    <span style="color:#dc2626;font-weight:700;">Gagal: <span id="cert-progress-failed">0</span></span>
                                </div>
                                <button type="button" id="cert-stop-btn" class="ui mini red basic button">
                                    <i class="stop icon"></i> Hentikan Pengiriman
                                </button>
                            </div>
                        `,
                        didOpen: () => {
                            const stopBtn = document.getElementById('cert-stop-btn');
                            if (stopBtn) {
                                stopBtn.addEventListener('click', function() {
                                    stopRequested = true;
                                    stopBtn.disabled = true;
                                    stopBtn.innerHTML = '<i class="spinner loading icon"></i> Menghentikan setelah email ini...';
                                });
                            }
                        }
                    });

                    let sentCount = 0;
                    let failedCount = 0;
                    let processedCount = 0;
                    let quotaHalted = false;
                    const failedItems = [];

                    for (let idx = 0; idx < totalToSend; idx++) {
                        if (stopRequested) {
                            break;
                        }

                        const counterEl = document.getElementById('cert-progress-counter');
                        const barEl = document.getElementById('cert-progress-bar');
                        const currentEl = document.getElementById('cert-progress-current');
                        const sentEl = document.getElementById('cert-progress-sent');
                        const failedEl = document.getElementById('cert-progress-failed');

                        if (counterEl) {
                            counterEl.textContent = `${idx + 1} dari ${totalToSend}`;
                        }
                        if (currentEl) {
                            currentEl.textContent = `Memproses email ke-${idx + 1} dari ${totalToSend}...`;
                        }

                        const processRes = await fetch("{{ route('modules::pendaftar.send-cert-email-process') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                batch_id: initData.batch_id,
                                mode: mode,
                                limit: limit,
                                index: idx
                            })
                        });

                        const step = await processRes.json();

                        if (!processRes.ok && step.fatal) {
                            failedItems.push({ nama: 'Sistem', email: '-', error: step.message || 'Sesi berakhir' });
                            break;
                        }

                        if (step.done) {
                            break;
                        }

                        processedCount++;
                        if (step.success) {
                            sentCount++;
                        } else {
                            failedCount++;
                            failedItems.push({
                                nama: step.nama || '-',
                                email: step.email || '-',
                                error: step.error || 'Gagal dikirim'
                            });
                        }

                        if (sentEl) sentEl.textContent = String(sentCount);
                        if (failedEl) failedEl.textContent = String(failedCount);
                        if (barEl) {
                            const pct = Math.min(100, Math.round((processedCount / totalToSend) * 100));
                            barEl.style.width = `${pct}%`;
                        }
                        if (currentEl) {
                            const statusBadge = step.success ? '✓ Terkirim ke' : '✗ Gagal ke';
                            currentEl.textContent = `${statusBadge} ${step.nama || ''} (${step.email || ''})`;
                        }

                        if (step.quota_exceeded) {
                            quotaHalted = true;
                            break;
                        }

                        // Jeda singkat agar tidak memicu burst throttle SMTP Gmail
                        if (!stopRequested && idx + 1 < totalToSend) {
                            await new Promise(resolve => setTimeout(resolve, 350));
                        }
                    }

                    const failedListHtml = failedItems.length > 0
                        ? `
                            <div style="margin-top:12px;text-align:left;max-height:170px;overflow-y:auto;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;padding:10px;font-size:12px;">
                                <strong style="color:#991b1b;">Detail Email Gagal (${failedItems.length}):</strong>
                                <ul style="margin:6px 0 0 16px;padding:0;color:#7f1d1d;">
                                    ${failedItems.map(item => `<li><strong>${escapeHtmlCert(item.nama)}</strong> (${escapeHtmlCert(item.email)}): ${escapeHtmlCert(item.error)}</li>`).join('')}
                                </ul>
                            </div>
                        `
                        : '';

                    let titleText = 'Pengiriman Email Selesai';
                    let iconType = failedCount > 0 ? 'warning' : 'success';
                    let extraNotice = '';

                    if (quotaHalted) {
                        titleText = 'Dihentikan: Batas Kuota Gmail Tercapai';
                        iconType = 'warning';
                        extraNotice = '<p style="color:#9a3412;font-size:12.5px;margin-top:8px;">Server Gmail melaporkan batas pengiriman tercapai. Anda bisa melanjutkan sisanya besok/nanti dengan memilih mode <strong>Hanya yang belum berhasil dikirim</strong>.</p>';
                    } else if (stopRequested) {
                        titleText = 'Pengiriman Dihentikan oleh Admin';
                        iconType = 'info';
                    }

                    await Swal.fire({
                        icon: iconType,
                        title: titleText,
                        width: 560,
                        html: `
                            <div style="font-size:14px;color:#334155;">
                                <div>Total Diproses: <strong>${processedCount} dari ${totalToSend}</strong></div>
                                <div style="margin-top:6px;">
                                    <span style="color:#15803d;font-weight:700;">Berhasil: ${sentCount}</span> &nbsp;|&nbsp;
                                    <span style="color:#dc2626;font-weight:700;">Gagal: ${failedCount}</span>
                                </div>
                                ${extraNotice}
                                ${failedListHtml}
                            </div>
                        `,
                        confirmButtonText: 'Selesai & Muat Ulang',
                        customClass: { confirmButton: 'ui primary button' },
                        buttonsStyling: false
                    });

                    window.location.reload();
                } catch (err) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: err?.message || 'Gagal memproses pengiriman email sertifikat.',
                        confirmButtonText: 'Tutup',
                        customClass: { confirmButton: 'ui primary button' },
                        buttonsStyling: false
                    });
                }
            };
        </script>
    @endpush
</x-volt-app>
