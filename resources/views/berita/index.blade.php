<x-volt-app title="Manajemen Berita">
    <x-slot name="actions">
        <x-volt-link-button :url="route('berita.create')" icon="plus" label="Tambah Berita" />
    </x-slot>

    <x-volt-panel title="Daftar Berita">
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($beritas as $berita)
                    <tr>
                        <td>{{ $berita->sumber }}</td>
                        <td>{{ $berita->tanggal }}</td>
                        <td>{{ $berita->judul }}</td>
                        <td>
                            @if($berita->status_aktif)
                                <div class="ui green label">Aktif</div>
                            @else
                                <div class="ui grey label">Tidak Aktif</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('berita.edit', $berita->id) }}" class="ui button icon mini"><i class="icon edit"></i></a>
                            <form action="{{ route('berita.destroy', $berita->id) }}" method="POST" class="inline-block" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="ui button icon mini red" onclick="Swal.fire({ title: 'Hapus berita ini?', text: 'Data tidak dapat dikembalikan!', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' }).then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } });"><i class="icon trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="center aligned">Belum ada berita</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $beritas->links() }}
        </div>
    </x-volt-panel>
</x-volt-app>
