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
                @php
                    $kategoriIcons = [
                        'Pendidikan' => ['icon' => 'graduation cap', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
                        'Kesehatan' => ['icon' => 'heartbeat', 'color' => '#ec4899', 'bg' => '#fdf2f8', 'border' => '#fbcfe8'],
                        'Pangan' => ['icon' => 'leaf', 'color' => '#10b981', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
                        'Budaya' => ['icon' => 'theater masks', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'border' => '#fde68a'],
                    ];
                @endphp
                @foreach ($defaultKategories as $kat)
                    @php
                        $item = $rekapBobot->firstWhere('kategori', $kat);
                        $totalBobot = $item->total_bobot ?? 0;
                        $totalAspek = $item->total_aspek ?? 0;
                        $isActive = $selectedKategori === $kat;
                        
                        $katKey = collect($kategoriIcons)->keys()->first(fn($k) => str_contains($kat, $k));
                        $meta = $katKey ? $kategoriIcons[$katKey] : ['icon' => 'tag', 'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#e2e8f0'];
                        $activeBorder = $isActive ? "border: 2px solid {$meta['color']};" : "border: 1px solid {$meta['border']};";
                    @endphp
                    <a href="{{ route('kategori-aspek.index', ['kategori' => $isActive ? '' : $kat]) }}"
                        class="ui card link" style="width: calc(25% - 1em); margin: 0.5em; {{ $activeBorder }} background-color: {{ $meta['bg'] }}; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <div class="content" style="text-align: center; padding: 1.5rem 1rem;">
                            <div style="font-size: 2rem; color: {{ $meta['color'] }}; margin-bottom: 0.75rem;">
                                <i class="{{ $meta['icon'] }} icon"></i>
                            </div>
                            <div class="header" style="font-size: 1.1rem; font-weight: 700; color: {{ $meta['color'] }}; margin-bottom: 0.5rem;">{{ $kat }}</div>
                            <div class="meta" style="margin-top: 1rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="ui label" style="background-color: {{ $meta['color'] }}; color: #fff;">
                                    Bobot: {{ $totalBobot }}
                                </span>
                                <span class="ui basic label" style="color: {{ $meta['color'] }}; border-color: {{ $meta['color'] }};">
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
                            @php
                                $katKey = collect($kategoriIcons)->keys()->first(fn($k) => str_contains($item->kategori, $k));
                                $meta = $katKey ? $kategoriIcons[$katKey] : ['icon' => 'tag', 'color' => '#64748b'];
                            @endphp
                            <span style="color: {{ $meta['color'] }}; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center;">
                                <i class="{{ $meta['icon'] }} icon" style="margin-right: 6px; font-size: 1.1em;"></i>
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td style="font-weight: 600; color: #0f172a;">{{ $item->aspek }}</td>
                        <td style="color: #475569; white-space: pre-line;">{{ $item->dimensi ?: '-' }}</td>
                        <td style="text-align: center;">
                            <span class="ui blue label">{{ $item->bobot }}</span>
                        </td>
                        @php
                            $reqPerm = \App\Enums\Permission::getCategoryManagePermission($item->kategori);
                            $canManage = auth()->user()->hasPermission('*') || (
                                auth()->user()->hasPermission(\App\Enums\Permission::SETTING_ASPEK_MANAGE) && 
                                ($reqPerm && auth()->user()->hasPermission($reqPerm))
                            );
                        @endphp
                        @if($canManage)
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
                        @else
                        <td style="text-align: center; color: #94a3b8;">
                            <i class="icon lock" title="Akses Dibatasi"></i>
                        </td>
                        @endif
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
