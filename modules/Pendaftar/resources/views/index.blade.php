<x-volt-app :title="'Pendaftar'">
    <x-slot name="actions">
        <x-volt-link-button :url="route('modules::pendaftar.export', request()->query())" icon="file excel" class="teal" label="Export Excel" target="_blank"
            data-no-loader="true" />
        <x-volt-link-button :url="route('modules::pendaftar.download-all-zip', request()->query())" icon="archive" class="blue" label="Unduh Semua Berkas (ZIP)" target="_blank"
            data-no-loader="true" />
    </x-slot>

    {!! $table !!}

    <style>
        @media (max-width: 767px) {
            /* 1. Force all menu parts to stack and align perfectly */
            .ui.borderless.stackable.menu .menu.right {
                width: 100% !important;
                display: block !important;
            }
            /* Search box item */
            .ui.borderless.stackable.menu > .item {
                display: block !important;
                width: 100% !important;
                padding: 1rem 1rem 0.25rem 1rem !important; /* 0.25rem bottom to form a 0.5rem gap */
                margin: 0 !important;
            }
            /* Filters item */
            .ui.borderless.stackable.menu .menu.right .item {
                display: block !important;
                width: 100% !important;
                padding: 0.25rem 1rem 1rem 1rem !important; /* 0.25rem top to complete the 0.5rem gap */
                margin: 0 !important;
            }

            /* 2. Target the specific filter form that uses Tailwind 'flex items-center' */
            form[id^="inline-filter-form-"] {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 0.5rem !important;
            }

            /* 3. Make the wrappers, fields, and dropdowns take full width */
            form[id^="inline-filter-form-"] > div,
            form[id^="inline-filter-form-"] .field,
            form[id^="inline-filter-form-"] .ui.dropdown {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                display: block !important;
            }

            /* 4. The search input container */
            .ui.borderless.stackable.menu > .item > form {
                display: block !important;
                width: 100% !important;
            }
            .ui.action.input {
                width: 100% !important;
            }
        }
    </style>
</x-volt-app>
