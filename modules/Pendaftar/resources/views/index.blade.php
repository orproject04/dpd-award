<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <x-volt-link-button :url="route('modules::pendaftar.export', request()->query())" icon="file excel" class="teal" label="Export Excel" target="_blank"
            data-no-loader="true" />
        <x-volt-link-button :url="route('modules::pendaftar.download-all-zip', request()->query())" icon="archive" class="blue" label="Unduh Semua Berkas (ZIP)" target="_blank"
            data-no-loader="true" />
    </x-slot>

    {!! $table !!}
</x-volt-app>
