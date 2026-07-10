<x-volt-app :title="'Detail Pendaftar'">
    <x-volt-backlink url="{{ route('modules::pendaftar.index') }}"/>

    <x-volt-panel title="Detil Pendaftar">
        <table class="ui table definition">
        <tr><td>Id</td><td>{{ $pendaftar->id }}</td></tr>
        <tr><td>Nomor Registrasi</td><td>{{ $pendaftar->nomor_registrasi }}</td></tr>
        <tr><td>Kategori</td><td>{{ $pendaftar->kategori }}</td></tr>
        <tr><td>Nama</td><td>{{ $pendaftar->nama }}</td></tr>
        <tr><td>Tempat Lahir</td><td>{{ $pendaftar->tempat_lahir }}</td></tr>
        <tr><td>Tanggal Lahir</td><td>{{ $pendaftar->tanggal_lahir }}</td></tr>
        <tr><td>Jenis Kelamin</td><td>{{ $pendaftar->jenis_kelamin }}</td></tr>
        <tr><td>Pendidikan</td><td>{{ $pendaftar->pendidikan }}</td></tr>
        <tr><td>Alamat</td><td>{{ $pendaftar->alamat }}</td></tr>
        <tr><td>Nomor Wa</td><td>{{ $pendaftar->nomor_wa }}</td></tr>
        <tr><td>Email</td><td>{{ $pendaftar->email }}</td></tr>
        <tr><td>Ktp</td><td>{{ $pendaftar->ktp }}</td></tr>
        <tr><td>Foto</td><td>{{ $pendaftar->foto }}</td></tr>
        <tr><td>Created At</td><td>{{ $pendaftar->created_at }}</td></tr>
        <tr><td>Updated At</td><td>{{ $pendaftar->updated_at }}</td></tr>
        </table>
    </x-volt-panel>
</x-volt-app>
