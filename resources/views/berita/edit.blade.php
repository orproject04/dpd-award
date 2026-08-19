<x-volt-app title="Edit Berita">
    <x-slot name="actions">
        <x-volt-link-button :url="route('berita.index')" icon="arrow left" label="Kembali" />
    </x-slot>

    <x-volt-panel title="Form Edit Berita">
        <form action="{{ route('berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data" class="ui form">
            @csrf
            @method('PUT')

            <div class="two fields">
                <div class="field">
                    <label>Judul Berita *</label>
                    <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required>
                </div>
                <div class="field">
                    <label>Sumber (contoh: detikNews)</label>
                    <input type="text" name="sumber" value="{{ old('sumber', $berita->sumber) }}">
                </div>
            </div>

            <div class="two fields">
                <div class="field">
                    <label>Tanggal (contoh: 28 Oktober 2026)</label>
                    <input type="text" name="tanggal" value="{{ old('tanggal', $berita->tanggal) }}">
                </div>
                <div class="field">
                    <label>Tautan URL (URL Berita Asli)</label>
                    <input type="url" name="tautan" value="{{ old('tautan', $berita->tautan) }}">
                </div>
            </div>

            <div class="field">
                <label>Kutipan (Singkat)</label>
                <textarea name="kutipan" rows="3">{{ old('kutipan', $berita->kutipan) }}</textarea>
            </div>

            <div class="field">
                <label>Gambar Berita (Biarkan kosong jika tidak ingin mengubah gambar)</label>
                @if($berita->gambar)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $berita->gambar) }}" alt="Current Image" style="max-height: 100px; display:block; margin-bottom:10px;">
                    </div>
                @endif
                <input type="file" name="gambar" accept="image/*">
            </div>

            <div class="field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', $berita->status_aktif) ? 'checked' : '' }}>
                    <label>Tampilkan di Landing Page</label>
                </div>
            </div>

            <div class="ui divider"></div>
            <button type="submit" class="ui button primary">Perbarui</button>
        </form>
    </x-volt-panel>
</x-volt-app>
