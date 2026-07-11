@php
    $filters = $builder->filters();
    $isMultiple = isset($filters) && count($filters) > 1;
    $color = hexToRgba(config('laravolt.ui.color'), 0.9);
    $filterKeys = collect($filters)
        ->map(function ($filter) {
            preg_match('/name="([^"]+)"/', $filter->render(), $matches);
            return $matches[1] ?? null;
        })
        ->filter()
        ->values()
        ->all();

    $isFiltered = collect(request()->only($filterKeys))
        ->filter()
        ->isNotEmpty();
@endphp

<style>
    .two-column {
        width: 100%;
    }

    .single-column {
        width: 100%;
    }

    /* Default state */
    .ui.basic.button.icon {
        transition: all 0.2s ease;
    }

    /* Hover state */
    .ui.basic.button.icon:hover,
    .ui.basic.button.icon:focus {
        border-color: {{ $color }};
        background-color: white;
    }

    .ui.basic.button.icon:hover i.icon,
    .ui.basic.button.icon:focus i.icon {
        color: {{ $color }};
    }

    /* Active state (popup terbuka) */
    .ui.basic.button.icon.active {
        border-color: {{ $color }};
        background-color: white;
    }

    .ui.basic.button.icon.active i.icon {
        color: {{ $color }};
    }

    .rotate-transition {
        transition: transform 0.3s ease;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .ui.dropdown.w-auto {
        width: auto !important;
        min-width: unset;
    }

    /* Modern pagination wrapper: segmented style */
    .pagination-modern {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 6px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        /* ensure inner corners are clipped */
    }

    /* Make inner pagination lay out nicely */
    .pagination-modern nav,
    .pagination-modern ul.pagination,
    .pagination-modern .ui.pagination.menu {
        display: flex;
        align-items: center;
        gap: 0;
        /* seamless segmented look */
        margin: 0;
        background: transparent;
        border: 0;
        box-shadow: none;
    }

    /* Semantic UI pagination styling */
    .pagination-modern .ui.pagination.menu .item {
        margin: 0 !important;
        border: none !important;
        /* no individual borders */
        border-radius: 0 !important;
        color: {{ $color }};
        /* blue link like screenshot */
        padding: 8px 12px !important;
        min-width: 36px;
        height: 36px;
        display: inline-flex;
        /* center numbers & symbols */
        align-items: center;
        justify-content: center;
        line-height: 1;
        /* avoid visual offset */
        text-align: center;
        transition: background-color 0.15s ease, color 0.15s ease;
        background: transparent !important;
        box-shadow: inset 1px 0 0 #e5e7eb;
        /* separators */
    }

    .pagination-modern .ui.pagination.menu .item:first-child {
        box-shadow: none;
    }

    .pagination-modern .ui.pagination.menu .item:hover {
        background: rgba(0, 0, 0, 0.03) !important;
    }

    .pagination-modern .ui.pagination.menu .item.active {
        background: {{ $color }} !important;
        color: #ffffff !important;
        box-shadow: inset 1px 0 0 {{ $color }};
        /* hide separator */
    }

    .pagination-modern .ui.pagination.menu .item.disabled,
    .pagination-modern .ui.pagination.menu .item.disabled:hover {
        opacity: .6 !important;
        cursor: not-allowed !important;
        color: #9ca3af !important;
        background: transparent !important;
    }

    /* Make navigation arrows larger and clearer */
    .pagination-modern .ui.pagination.menu .item[aria-label="First page"],
    .pagination-modern .ui.pagination.menu .item[aria-label="Previous page"],
    .pagination-modern .ui.pagination.menu .item[aria-label="Next page"],
    .pagination-modern .ui.pagination.menu .item[aria-label="Last page"] {
        font-size: 24px;
    }

    /* Laravel default pagination styling */
    .pagination-modern ul.pagination {
        list-style: none;
        padding: 0;
    }

    .pagination-modern .pagination .page-item {
        display: inline-flex;
    }

    .pagination-modern .pagination .page-link,
    .pagination-modern .pagination .page-item>a,
    .pagination-modern .pagination .page-item>span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        min-width: 36px;
        padding: 8px 12px;
        border: none;
        /* no per-item border */
        border-radius: 0;
        color: {{ config('laravolt.ui.color') }};
        background: transparent;
        text-decoration: none;
        transition: background-color 0.15s ease, color 0.15s ease;
        box-shadow: inset 1px 0 0 #e5e7eb;
        /* separators */
    }

    .pagination-modern .pagination .page-item:first-child .page-link {
        box-shadow: none;
    }

    .pagination-modern .pagination .page-link:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    .pagination-modern .pagination .page-item.active .page-link {
        background: {{ config('laravolt.ui.color') }};
        color: #ffffff;
        box-shadow: inset 1px 0 0 {{ config('laravolt.ui.color') }};
    }

    .pagination-modern .pagination .page-item.disabled .page-link {
        opacity: .6;
        cursor: not-allowed;
        color: #9ca3af;
        background: transparent;
    }

    /* Compact on very small screens: keep container styling; internal pagination may still overflow horizontally if many pages */
    @media (max-width: 420px) {
        .pagination-modern {
            padding: 4px;
        }

        .pagination-modern .ui.pagination.menu .item,
        .pagination-modern .pagination .page-link,
        .pagination-modern .pagination .page-item>a,
        .pagination-modern .pagination .page-item>span {
            min-width: 32px;
            height: 32px;
            padding: 6px 8px;
        }
    }

    /* Stack action buttons (clear/apply) vertically on small screens */
    @media (max-width: 767px) {
        .action-buttons {
            flex-direction: column !important;
            gap: .5rem !important;
            align-items: stretch;
        }

        .action-buttons>* {
            width: 100% !important;
        }
    }
</style>

<div id="{{ $id }}" data-role="suitable" class="ui segments panel x-suitable" style="border:1px solid #dbe0e6">
    @foreach ($segments as $segment)
        @unless ($segment->isEmpty())
            <div class="ui borderless stackable menu {{ $loop->first ? 'top' : '' }} attached">
                {!! $segment->render() !!}
                <div class="menu right">
                    @if ($builder->perYear())
                        <div id="per-year-item-{{ $id }}" class="item"
                            style="{{ isset($filters) && count($filters) > 0 ? 'padding-right: 0;' : '' }}">
                            <div class="flex justify-end w-full">
                                {!! app(\App\Http\Filters\TahunEndedFilter::class)->withLabel(false)->addClass('w-auto')->render() !!}
                            </div>
                        </div>
                        @if (isset($filters) && count($filters) > 0)
                            <style>
                                @media (max-width: 767px) {
                                    #per-year-item-{{ $id }} {
                                        padding-right: 20px !important;
                                        padding-bottom: 0 !important;
                                    }
                                }
                            </style>
                        @endif
                    @endif
                    @if (isset($filters) && count($filters) > 0)
                        <div class="item">
                            <div class="flex justify-end w-full">
                                <div class="ui basic button icon filter-icon {{ $isFiltered ? 'active' : '' }}"
                                    id="filter-icon-{{ $id }}">
                                    <i class="icon filter"></i>
                                    <i class="icon angle {{ $isFiltered ? 'up' : 'down' }}"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endunless
    @endforeach

    <div id="filter-wrapper-{{ $id }}"
        class="filter-wrapper ui attached segment !p-0 {{ $isMultiple ? 'two-column' : 'single-column' }} {{ $isFiltered ? '' : 'hidden' }}"
        style="background-color: white; {{ $isFiltered ? '' : 'display: none;' }}">
        <div class="pt-6">
            <h3 class="text-center font-semibold">Filter</h3>
        </div>

        <form class="ui form px-6 py-4 filter-form" method="GET" action="{{ request()->url() }}">
            <div class="{{ $isMultiple ? 'grid md:grid-cols-2 gap-3' : 'space-y-3' }}">
                @foreach ($filters as $filter)
                    <div>
                        {!! preg_replace_callback(
                            '/<input\b([^>]*)>/i',
                            function ($matches) {
                                $input = $matches[0];
                        
                                if (preg_match('/style\s*=\s*"/i', $input)) {
                                    return preg_replace(
                                        '/style="([^"]*)"/i',
                                        function ($styleMatch) {
                                            $styles = $styleMatch[1];
                                            if (!preg_match('/width\s*:\s*100%/', $styles)) {
                                                $styles .= '; width: 100%';
                                            }
                                            return 'style="' . $styles . '"';
                                        },
                                        $input,
                                    );
                                } else {
                                    return str_replace('<input', '<input style="width: 100%"', $input);
                                }
                            },
                            $filter->render(),
                        ) !!}
                    </div>
                @endforeach
            </div>
        </form>

        <div class="px-6 pb-6 flex gap-4 action-buttons">
            <x-volt-button type="reset" class="basic rounded-lg reset-filter" icon="times circle">
                Hapus Filter
            </x-volt-button>
            <x-volt-button type="button" class="rounded-lg apply-filter" icon="search">
                Terapkan
            </x-volt-button>
        </div>
    </div>

    @include('suitable::table')

    @if ($showFooter)
        <footer class="ui bottom attached segment !bg-white/90 backdrop-blur-sm border-t !p-0">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 px-4 py-3">
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="info circle icon" style="color: {{ $color }}"></i>
                    <small class="leading-none">{{ $builder->summary() }}</small>
                </div>

                @if ($showPerPage)
                    <div class="flex items-center gap-2">
                        <span class="text-xs tracking-wide text-gray-500">Jumlah per halaman</span>
                        <div id="per-page-{{ $id }}" class="ui selection dropdown mini w-auto">
                            <input type="hidden" name="per_page"
                                value="{{ request('per_page', $collection->perPage()) }}">
                            <i class="dropdown icon"></i>
                            <div class="default text">{{ request('per_page', $collection->perPage()) }}</div>
                            <div class="menu">
                                @foreach ($perPageOptions as $n)
                                    <div class="item" data-value="{{ $n }}">{{ $n }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($collection instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="w-full md:w-auto md:ml-auto">
                        @if ($collection instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                            @php
                                $currentPage = $collection->currentPage();
                                $lastPage = max(1, $collection->lastPage());
                                $maxLinks = 7; // maksimal 7 page yang tampil

                                if ($lastPage <= $maxLinks) {
                                    $start = 1;
                                    $end = $lastPage;
                                } else {
                                    $half = intdiv($maxLinks, 2); // 3
                                    if ($currentPage <= $half + 1) {
                                        // di awal
                                        $start = 1;
                                        $end = $maxLinks;
                                    } elseif ($currentPage >= $lastPage - $half) {
                                        // di akhir
                                        $start = $lastPage - $maxLinks + 1;
                                        $end = $lastPage;
                                    } else {
                                        // di tengah
                                        $start = $currentPage - $half;
                                        $end = $currentPage + $half;
                                    }
                                }

                                // jaga-jaga biar tetap dalam rentang valid
                                $start = max(1, $start);
                                $end = min($lastPage, $end);
                                $appends = request()->input();
                            @endphp

                            <div class="pagination-modern" aria-label="Table pagination">
                                <div class="ui pagination menu">
                                    <a class="item {{ $collection->onFirstPage() ? 'disabled' : '' }}"
                                        href="{{ $collection->onFirstPage() ? '#' : $collection->appends($appends)->url(1) }}"
                                        aria-label="First page">&laquo;</a>

                                    <a class="item {{ $collection->onFirstPage() ? 'disabled' : '' }}"
                                        href="{{ $collection->onFirstPage() ? '#' : $collection->appends($appends)->previousPageUrl() }}"
                                        aria-label="Previous page">&lsaquo;</a>

                                    @if ($start > 1)
                                        <a class="item" href="{{ $collection->appends($appends)->url(1) }}">1</a>
                                        @if ($start > 2)
                                            <span class="item disabled">...</span>
                                        @endif
                                    @endif

                                    @for ($page = $start; $page <= $end; $page++)
                                        <a class="item {{ $page == $currentPage ? 'active' : '' }}"
                                            href="{{ $collection->appends($appends)->url($page) }}">{{ $page }}</a>
                                    @endfor

                                    @if ($end < $lastPage)
                                        @if ($end < $lastPage - 1)
                                            <span class="item disabled">...</span>
                                        @endif
                                        <a class="item"
                                            href="{{ $collection->appends($appends)->url($lastPage) }}">{{ $lastPage }}</a>
                                    @endif

                                    <a class="item {{ $currentPage == $lastPage ? 'disabled' : '' }}"
                                        href="{{ $currentPage == $lastPage ? '#' : $collection->appends($appends)->nextPageUrl() }}"
                                        aria-label="Next page">&rsaquo;</a>

                                    <a class="item {{ $currentPage == $lastPage ? 'disabled' : '' }}"
                                        href="{{ $currentPage == $lastPage ? '#' : $collection->appends($appends)->url($lastPage) }}"
                                        aria-label="Last page">&raquo;</a>
                                </div>
                            </div>
                        @else
                            <div class="pagination-modern" aria-label="Table pagination">
                                {!! $collection->appends(request()->input())->links($paginationView) !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </footer>
    @endif
</div>

@if ($hasSearchableColumns)
    <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable"
        style="display: none">
        <input type="submit">
    </form>
@endif

<script>
    $(function() {
        $('[data-role="suitable"]').each(function() {
            const container = $(this);
            const id = container.attr('id'); // id unik per table
            const wrapper = $(`#filter-wrapper-${id}`);
            const icon = $(`#filter-icon-${id}`);
            const resetBtn = container.find('.reset-filter');
            const applyBtn = container.find('.apply-filter');
            const form = container.find('.filter-form');
            const perPageDropdown = container.find(`#per-page-${id}`);

            // Reset filter
            resetBtn.on('click', function() {
                showLoading();

                form.form('clear');
                form.find('.ui.dropdown').dropdown('set selected', '0');

                const params = new URLSearchParams(window.location.search);
                const tahun = params.get('tahun-ended-filter');

                const baseUrl = window.location.origin + window.location.pathname;
                const newUrl = tahun ?
                    `${baseUrl}?tahun-ended-filter=${encodeURIComponent(tahun)}` : baseUrl;

                window.location.href = newUrl;
            });

            // Apply filter
            applyBtn.on('click', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const tahun = urlParams.get('tahun-ended-filter');

                if (tahun) {
                    form.find('input[name="tahun-ended-filter"]').remove();

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'tahun-ended-filter',
                        value: tahun
                    }).appendTo(form);
                }

                form.submit();
            });

            // Toggle filter wrapper
            icon.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const angleIcon = $(this).find('.icon.angle');

                if (wrapper.is(':visible')) {
                    wrapper.slideUp(200, function() {
                        wrapper.addClass('hidden');
                    });
                    icon.removeClass('active');
                    angleIcon.removeClass('up').addClass('down');
                } else {
                    wrapper.removeClass('hidden').hide().slideDown(200);
                    icon.addClass('active');
                    angleIcon.removeClass('down').addClass('up');

                    $('html, body').animate({
                        scrollTop: wrapper.offset().top - 100
                    }, 300);
                }
            });

            // Init and handle per-page change
            if (perPageDropdown.length) {
                try {
                    perPageDropdown.dropdown({
                        action: 'activate',
                        onChange: function(value) {
                            if (!value) return;
                            const url = new URL(window.location.href);
                            url.searchParams.set('per_page', value);
                            window.location.href = url.toString();
                        }
                    });
                } catch (e) {
                    // Fallback: navigate on click if dropdown failed to init
                    perPageDropdown.find('.item').on('click', function() {
                        const value = $(this).data('value');
                        const url = new URL(window.location.href);
                        url.searchParams.set('per_page', value);
                        window.location.href = url.toString();
                    });
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const filter = document.getElementById('tahun-ended-filter');

        if (filter) {
            filter.addEventListener('change', function() {
                showLoading();
                const selected = this.value;
                const url = new URL(window.location.href);

                // Update atau tambahkan parameter
                url.searchParams.set('tahun-ended-filter', selected);

                // Redirect ke URL baru
                window.location.href = url.toString();
            });
        }
    });
</script>