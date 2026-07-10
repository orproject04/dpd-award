<x-volt-app :title="__('laravolt::action.edit') . ' Pendaftar'">
    <x-volt-backlink url="{{ route('modules::pendaftar.index') }}"></x-backlink>

    <x-volt-panel title="Tambah Pendaftar">
        {!! form()->bind($pendaftar)->put(route('modules::pendaftar.update', $pendaftar->getRouteKey()))->horizontal()->multipart() !!}
            @include('pendaftar::_form')
        {!! form()->close() !!}
    </x-volt-panel>
</x-volt-app>
