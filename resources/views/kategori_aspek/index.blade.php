<x-volt-app title="Setting Aspek, Dimensi & Bobot">
    <x-slot name="actions">
        @if ($totalDataCount == 0)
            <form action="{{ route('kategori-aspek.seed') }}" method="POST" style="display: inline-block; margin-right: 0.5rem;">
                @csrf
                <button type="submit" class="ui button green">
                    <i class="icon download"></i> Generate Data Default
                </button>
            </form>
        @endif
        <x-volt-link-button :url="route('kategori-aspek.create', ['kategori' => $selectedKategori])" icon="plus" label="Tambah Aspek" />
    </x-slot>

    <!-- Ringkasan Bobot per Kategori -->
    <div class="ui grid m-b-2">
        <div class="sixteen wide column">
            <div class="ui cards">
                @foreach ($defaultKategories as $kat)
                    @php
                        $item = $rekapBobot->firstWhere('kategori', $kat);
                        $totalBobot = $item->total_bobot ?? 0;
                        $totalAspek = $item->total_aspek ?? 0;
                        $isActive = $selectedKategori === $kat;
                    @endphp
                    <a href="{{ route('kategori-aspek.index', ['kategori' => $isActive ? '' : $kat]) }}"
                        class="ui card link {{ $isActive ? 'teal' : '' }}" style="width: calc(25% - 1em); margin: 0.5em;">
                        <div class="content">
                            <div class="header" style="font-size: 1rem; color: #1e293b;">{{ $kat }}</div>
                            <div class="meta" style="margin-top: 0.5rem;">
                                <span class="ui badge {{ $totalBobot > 0 ? 'teal' : 'grey' }} label">
                                    Total Bobot: <strong>{{ $totalBobot }}</strong>
                                </span>
                                <span class="ui label mini basic" style="margin-left: 0.25rem;">
                                    {{ $totalAspek }} Aspek
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <x-volt-panel title="Daftar Aspek, Dimensi & Bobot">
        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('kategori-aspek.index') }}" class="ui form m-b-2">
            <div class="fields">
                <div class="eight wide field">
                    <label>Filter Kategori</label>
                    <select name="kategori" class="ui dropdown" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($defaultKategories as $kat)
                            <option value="{{ $kat }}" {{ $selectedKategori == $kat ? 'selected' : '' }}>
                                {{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="eight wide field">
                    <label>Cari Aspek / Dimensi</label>
                    <div class="ui icon input">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari berdasarkan nama aspek, dimensi, atau kategori...">
                        <i class="search icon"></i>
                    </div>
                </div>
            </div>
            <div class="fields">
                <div class="sixteen wide field">
                    <button type="submit" class="ui button primary">
                        <i class="icon search"></i> Cari
                    </button>
                    @if ($selectedKategori || $search)
                        <a href="{{ route('kategori-aspek.index') }}" class="ui button basic">
                            <i class="icon undo"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if (session('success'))
            <div class="ui success message m-b-2">
                <i class="close icon"></i>
                <div class="header">Berhasil</div>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="ui error message m-b-2">
                <i class="close icon"></i>
                <div class="header">Perhatian</div>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <table class="ui celled compact table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 220px;">Kategori</th>
                    <th>Aspek</th>
                    <th>Dimensi</th>
                    <th style="width: 100px; text-align: center;">Bobot</th>
                    <th style="width: 110px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aspeks as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $aspeks->firstItem() + $index }}</td>
                        <td>
                            <span class="ui label teal basic mini">{{ $item->kategori }}</span>
                        </td>
                        <td style="font-weight: 600; color: #0f172a;">{{ $item->aspek }}</td>
                        <td style="color: #475569; white-space: pre-line;">{{ $item->dimensi ?: '-' }}</td>
                        <td style="text-align: center;">
                            <span class="ui blue label">{{ $item->bobot }}</span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('kategori-aspek.edit', $item->id) }}" class="ui button icon mini teal"
                                title="Edit">
                                <i class="icon edit"></i>
                            </a>
                            <form action="{{ route('kategori-aspek.destroy', $item->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="ui button icon mini red" title="Hapus"
                                    onclick="Swal.fire({ title: 'Hapus Aspek ini?', text: 'Data tidak dapat dikembalikan!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' }).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } });">
                                    <i class="icon trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center aligned" style="padding: 3rem 1.5rem; color: #64748b;">
                            <i class="icon inbox massive" style="color: #cbd5e1; margin-bottom: 1rem;"></i><br>
                            <h4 style="color: #475569; margin-top: 0.5rem;">Belum ada data Setting Aspek, Dimensi & Bobot</h4>
                            @if ($totalDataCount == 0)
                                <p style="color: #64748b;">Tabel masih kosong. Anda dapat meng-generate data default awal (Bidang Seni & Budaya, Ketahanan Pangan, Kesehatan, Pendidikan) secara otomatis.</p>
                                <form action="{{ route('kategori-aspek.seed') }}" method="POST" style="margin-top: 1rem;">
                                    @csrf
                                    <button type="submit" class="ui button green large">
                                        <i class="icon download"></i> Generate Data Default Sekarang
                                    </button>
                                </form>
                            @else
                                <p style="color: #64748b;">Tidak ada data yang sesuai dengan filter/pencarian Anda.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $aspeks->links() }}
        </div>
    </x-volt-panel>
</x-volt-app>
