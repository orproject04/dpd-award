{!! form()->text('nomor_registrasi')->label('Nomor Registrasi')->required() !!}
{!! form()->text('kategori')->label('Kategori')->required() !!}
{!! form()->text('nama')->label('Nama')->required() !!}
{!! form()->text('tempat_lahir')->label('Tempat Lahir')->required() !!}
{!! form()->date('tanggal_lahir')->label('Tanggal Lahir')->required() !!}
{!! form()->text('jenis_kelamin')->label('Jenis Kelamin')->required() !!}
{!! form()->text('pendidikan')->label('Pendidikan')->required() !!}
{!! form()->textarea('alamat')->label('Alamat')->required() !!}
{!! form()->text('nomor_wa')->label('Nomor Wa')->required() !!}
{!! form()->text('email')->label('Email')->required() !!}
{!! form()->text('ktp')->label('Ktp')->required() !!}
{!! form()->text('foto')->label('Foto')->required() !!}

{!!
    form()->action([
        form()->submit('Simpan'),
        form()->link('Batal', route('modules::pendaftar.index'))
    ])
!!}
