<x-volt-app :title="__('laravolt::action.add') . ' Pendaftar'">
    <x-volt-backlink url="{{ route('modules::pendaftar.index') }}"></x-backlink>

    <x-volt-panel title="Tambah Pendaftar">
        {!! form()->post(route('modules::pendaftar.store'))->horizontal()->multipart() !!}
            @include('pendaftar::_form')
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
