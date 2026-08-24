<x-volt-app title="Edit Setting Aspek & Bobot">
    <x-slot name="actions">
        <x-volt-link-button :url="route('kategori-aspek.index', ['kategori' => $kategoriAspek->kategori])" icon="arrow left" label="Kembali" class="basic" />
    </x-slot>

    <x-volt-panel title="Form Edit Setting Aspek, Dimensi & Bobot">
        <form action="{{ route('kategori-aspek.update', $kategoriAspek->id) }}" method="POST" class="ui form">
            @csrf
            @method('PUT')

            <div class="field required @error('kategori') error @enderror">
                <label>Kategori</label>
                <select name="kategori" class="ui dropdown search selection" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($defaultKategories as $kat)
                        <option value="{{ $kat }}" {{ (old('kategori', $kategoriAspek->kategori) == $kat) ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                    @if(!in_array($kategoriAspek->kategori, $defaultKategories))
                        <option value="{{ $kategoriAspek->kategori }}" selected>{{ $kategoriAspek->kategori }}</option>
                    @endif
                </select>
                @error('kategori')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field required @error('aspek') error @enderror">
                <label>Aspek Penilaian</label>
                <input type="text" name="aspek" value="{{ old('aspek', $kategoriAspek->aspek) }}" placeholder="Contoh: Integritas dan Rekam Jejak, Dampak Sosial..." required max="255">
                @error('aspek')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field @error('dimensi') error @enderror">
                <label>Dimensi / Uraian Indikator</label>
                <textarea name="dimensi" rows="4" placeholder="Jelaskan dimensi atau rincian indikator penilaian untuk aspek ini...">{{ old('dimensi', $kategoriAspek->dimensi) }}</textarea>
                @error('dimensi')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="field required @error('bobot') error @enderror">
                <label>Bobot (Integer / Nilai Persentase)</label>
                <div class="ui right labeled input" style="max-width: 250px;">
                    <input type="number" name="bobot" value="{{ old('bobot', $kategoriAspek->bobot) }}" min="0" step="1" required placeholder="0">
                    <div class="ui basic label">%</div>
                </div>
                @error('bobot')
                    <div class="ui error message display-block" style="display:block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="ui divider m-y-2"></div>

            <div class="actions">
                <button type="submit" class="ui button primary">
                    <i class="icon save"></i> Perbarui Data
                </button>
                <a href="{{ route('kategori-aspek.index', ['kategori' => $kategoriAspek->kategori]) }}" class="ui button basic">Batal</a>
            </div>
        </form>
    </x-volt-panel>
</x-volt-app>
