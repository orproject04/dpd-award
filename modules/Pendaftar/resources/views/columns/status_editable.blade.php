@php
    $optColor = match ($data->status) {
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

@if ($canManage)
    <a href="#" class="edit-status-btn" data-id="{{ $data->id }}" data-status="{{ $data->status }}" style="text-decoration: none;">
        <span class="ui label large {{ $optColor }}" style="cursor: pointer;" title="Klik untuk ubah status">
            {{ $data->status ?? 'Diajukan' }} <i class="edit icon" style="margin-left: 0.5rem; opacity: 0.7;"></i>
        </span>
    </a>
@else
    <span class="ui label large {{ $optColor }}">
        {{ $data->status ?? 'Diajukan' }}
    </span>
@endif


