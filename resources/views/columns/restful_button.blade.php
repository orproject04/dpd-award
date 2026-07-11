@php
    $currentUrl = url()->current();
    $id = $data->id;

    $detailUrl = $actions->get('show');
@endphp
@if ($actions->isNotEmpty())
    {{-- @if ($actions->count() > 1) --}}
    <div class="flex flex-wrap justify-end gap-2 [&>*]:w-auto max-w-full">
        {{-- @endif --}}

        @if ($actions->has('show'))
            <x-volt-link-button url="{{ $detailUrl }}" up-layer="new" up-mode="modal" icon="eye"
                class="icon whitespace-nowrap w-fit" :label="'Detail'">
            </x-volt-link-button>
        @endif

        @if ($actions->has('edit'))
            <x-volt-link-button url="{{ $actions->get('edit') }}" up-layer="new" up-mode="modal" icon="edit"
                class="icon whitespace-nowrap w-fit" :label="'Ubah'">
            </x-volt-link-button>
        @endif

        @if ($actions->has('destroy'))
            <x-volt-button data-form-id="{{ $key }}" icon="trash alternate"
                class="icon red delete-button whitespace-nowrap w-fit" type="button" :label="'Hapus'" />
        @endif

        {{-- @if ($actions->count() > 1) --}}
    </div>
    {{-- @endif --}}

    @if ($actions->has('destroy'))
        <form id="{{ $key }}" action="{{ $actions->get('destroy') }}" method="POST"
            class="hidden delete-form">
            @method('DELETE')
            @csrf
        </form>
    @endif
@endif

<script>
    var areYouSure = 'Apakah Anda yakin?';
    var deleteConfirmation = 'Data akan dihapus secara permanen.';
    var deleteButtonText = 'Ya, hapus';
    var cancelButtonText = 'Batal';
    var error = 'Error';

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const formId = button.getAttribute('data-form-id');
                const form = document.getElementById(formId);

                Swal.fire({
                    title: areYouSure,
                    text: deleteConfirmation,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: deleteButtonText,
                    cancelButtonText: cancelButtonText,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
