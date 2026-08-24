<x-volt-app title="Tambah Setting Aspek & Bobot">
    <x-slot name="actions">
        <x-volt-link-button :url="route('kategori-aspek.index', ['kategori' => $selectedKategori])" icon="arrow left" label="Kembali" class="basic" />
    </x-slot>

    <x-volt-panel title="Form Tambah Setting Aspek, Dimensi & Bobot">
        <form action="{{ route('kategori-aspek.store') }}" method="POST" class="ui form">
            @csrf

            <div class="field required @error('kategori') error @enderror">
                <label>Kategori</label>
                <select name="kategori" class="ui dropdown search selection" required>
                    <option value="">-- Pilih atau ketik Kategori --</option>
                    @foreach($defaultKategories as $kat)
                        <option value="{{ $kat }}" {{ (old('kategori', $selectedKategori) == $kat) ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>
                @error('kategori')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field required @error('aspek') error @enderror">
                <label>Aspek Penilaian</label>
                <input type="text" name="aspek" value="{{ old('aspek') }}" placeholder="Contoh: Integritas dan Rekam Jejak, Dampak Sosial..." required max="255">
                @error('aspek')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field @error('dimensi') error @enderror">
                <label>Dimensi / Uraian Indikator</label>
                <textarea name="dimensi" rows="4" placeholder="Jelaskan dimensi atau rincian indikator penilaian untuk aspek ini...">{{ old('dimensi') }}</textarea>
                @error('dimensi')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field required @error('bobot') error @enderror">
                <label>Bobot (Integer / Nilai Persentase)</label>
                <div class="ui right labeled input" style="max-width: 250px;">
                    <input type="number" name="bobot" value="{{ old('bobot', 0) }}" min="0" step="1" required placeholder="0">
                    <div class="ui basic label">%</div>
                </div>
                @error('bobot')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="ui divider m-y-2"></div>

            <div class="actions">
                <button type="submit" class="ui button primary">
                    <i class="icon save"></i> Simpan Data
                </button>
                <a href="{{ route('kategori-aspek.index', ['kategori' => $selectedKategori]) }}" class="ui button basic">Batal</a>
            </div>
        </form>
    </x-volt-panel>
</x-volt-app>
