<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <x-volt-link-button
            :url="route('modules::pendaftar.create')"
            icon="plus"
            :label="__('laravolt::action.add')"
        />
    </x-slot>

    @livewire(\Modules\Pendaftar\PendaftarTableView::class)
</x-volt-app>
