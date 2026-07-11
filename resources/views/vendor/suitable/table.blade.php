<?php

$tableClass = '';
if ($showHeader && $showFooter) {
    $tableClass = 'attached';
} elseif ($showHeader) {
    $tableClass = 'bottom attached';
} elseif ($showFooter) {
    $tableClass = 'top attached';
}
?>

<style>
    .auto-width-table {
        table-layout: auto;
        white-space: nowrap;
    }

    .auto-width-table td,
    .auto-width-table th {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .auto-width-table td:nth-child(2):has(img),
    .auto-width-table th:nth-child(2):has(img) {
        min-width: 60px;
        width: 1%;
        white-space: nowrap;
        max-width: 100px;
        /* ✅ Boleh kasih batas khusus kolom ini */
    }

    .draggable-table-container {
        cursor: grab;
        overflow-x: auto;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* Internet Explorer 10+ */
    }

    .draggable-table-container::-webkit-scrollbar {
        display: none; /* WebKit */
    }

    .draggable-table-container.dragging {
        cursor: grabbing;
        user-select: none;
    }

    /* Resizable columns styles */
    .resizable-table th {
        position: relative;
        user-select: none;
    }

    .resizable-table th:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        cursor: col-resize;
        background: transparent;
        z-index: 10;
    }

    .resizable-table th:not(:last-child):hover::after {
        background: rgba(0, 123, 255, 0.3);
    }

    .resizable-table th.resizing::after {
        background: rgba(0, 123, 255, 0.6);
    }

    .resizable-table.col-resizing {
        cursor: col-resize;
        user-select: none;
    }

    .resizable-table.col-resizing * {
        cursor: col-resize !important;
        user-select: none !important;
    }

    /* Mobile stacked table: convert rows to card-like blocks with label before value */
    @media (max-width: 767px) {
        .auto-width-table,
        .auto-width-table thead,
        .auto-width-table tbody,
        .auto-width-table th,
        .auto-width-table td,
        .auto-width-table tr {
            display: block;
            width: 100%;
        }

        .auto-width-table thead { /* hide original header */
            position: absolute;
            left: -9999px;
            top: -9999px;
            height: 0;
            overflow: hidden;
        }

        .auto-width-table tbody tr {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 0.75rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        /* Stack label above value and left-align everything */
        .auto-width-table td {
            display: block;
            padding: .4rem .6rem;
            border: none;
            white-space: normal;
            word-break: break-word;
            text-align: left !important;
        }

        .auto-width-table td::before {
            content: attr(data-label);
            display: block;
            font-weight: 600;
            margin: 0 0 .35rem 0;
            color: #6b7280; /* gray-500 */
        }

        .auto-width-table td > * { /* inner content flows normally below label */
            display: block;
            margin: 0;
        }
    }
</style>

<div class="overflow-x-auto w-full">
    <table class="ui {{ $tableClass }} striped table unstackable responsive auto-width-table resizable-table">
        <thead>
            <tr class="text-center">
                @foreach ($columns as $column)
                    @php
                        $header = $column->header();
                        $isInfo = false;

                        // Cek apakah header berupa object Header atau string biasa
                        if ($header instanceof \Laravolt\Suitable\Contracts\Header) {
                            // Coba ambil konten sebagai string mentah
                            $rendered = $header->render();
                            $isInfo = Str::contains(Str::lower(strip_tags($rendered)), 'info mentoring');
                        } else {
                            $isInfo = Str::contains(Str::lower(strip_tags($header)), 'info mentoring');
                        }
                    @endphp

                    @if ($header instanceof \Laravolt\Suitable\Contracts\Header)
                        {!! $isInfo ? str_replace('<th', '<th style="min-width:250px"', $header->render()) : $header->render() !!}
                    @else
                        {!! $isInfo ? str_replace('<th', '<th style="min-width:250px"', $header) : $header !!}
                    @endif
                @endforeach
            </tr>
            @if ($hasSearchableColumns)
                <tr class="ui form" data-role="suitable-header-searchable">
                    @foreach ($columns as $column)
                        @if ($column->isSearchable())
                            {!! $column->searchableHeader()->render() !!}
                        @else
                            <th></th>
                        @endif
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody class="collection">
            @forelse($collection as $data)
                @php($outerLoop = $loop)
                @if ($row)
                    @include($row)
                @else
                    <tr>
                        @foreach ($columns as $column)
                            <td {!! $column->cellAttributes($data) !!}>{!! $column->cell($data, $collection, $outerLoop) !!}</td>
                        @endforeach
                    </tr>
                @endif
            @empty
                @include('suitable::empty')
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll('.auto-width-table td, .auto-width-table th').forEach(cell => {
        if (cell.scrollWidth > cell.clientWidth) {
            cell.style.whiteSpace = 'normal';
        }
    });

    // Drag to scroll functionality - support multiple tables
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll('.draggable-table-container');
        
        containers.forEach(container => {
            const table = container.querySelector('.resizable-table');
            if (!table) return;

            let isDown = false;
            let startX;
            let scrollLeft;

            // Drag to scroll handlers
            container.addEventListener('mousedown', (e) => {
                // Only drag if not clicking on interactive elements
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                    return;
                }
                
                // Check if we're clicking on a resize handle
                const th = e.target.closest('th');
                if (th) {
                    const thElements = table.querySelectorAll('th');
                    const isLastColumn = th === thElements[thElements.length - 1];
                    if (!isLastColumn) {
                        const rect = th.getBoundingClientRect();
                        const isNearRightEdge = e.clientX > rect.right - 6;
                        if (isNearRightEdge) {
                            return; // Don't start drag scrolling
                        }
                    }
                }
                
                isDown = true;
                container.classList.add('dragging');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                e.preventDefault();
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.classList.remove('dragging');
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                container.classList.remove('dragging');
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scroll speed
                container.scrollLeft = scrollLeft - walk;
            });

            // Touch events for mobile
            container.addEventListener('touchstart', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                    return;
                }
                
                isDown = true;
                container.classList.add('dragging');
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('touchend', () => {
                isDown = false;
                container.classList.remove('dragging');
            });

            container.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });

            // Add `data-label` attributes to each td using the corresponding th text
            try {
                const headerCells = Array.from(table.querySelectorAll('thead th'));
                const headers = headerCells.map(h => h.innerText.trim());

                table.querySelectorAll('tbody tr').forEach(tr => {
                    tr.querySelectorAll('td').forEach((td, i) => {
                        if (!td.hasAttribute('data-label')) {
                            td.setAttribute('data-label', headers[i] || '');
                        }
                    });
                });
            } catch (e) {
                // Fail silently if headers cannot be read
                console.debug('responsive table: failed to set data-labels', e);
            }

            // Column resizing functionality
            let isResizing = false;
            let currentTh = null;
            let resizeStartX = 0;
            let startWidth = 0;

            // Add event listeners to all th elements
            const thElements = table.querySelectorAll('th');
            thElements.forEach((th, index) => {
                // Skip the last column
                if (index === thElements.length - 1) return;

                th.addEventListener('mousedown', (e) => {
                    // Check if mouse is near the right edge (within 6px)
                    const rect = th.getBoundingClientRect();
                    const isNearRightEdge = e.clientX > rect.right - 6;
                    
                    if (isNearRightEdge) {
                        isResizing = true;
                        currentTh = th;
                        resizeStartX = e.clientX;
                        startWidth = parseInt(window.getComputedStyle(th).width, 10);
                        
                        th.classList.add('resizing');
                        table.classList.add('col-resizing');
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });

                th.addEventListener('mousemove', (e) => {
                    if (!isResizing) {
                        const rect = th.getBoundingClientRect();
                        const isNearRightEdge = e.clientX > rect.right - 6;
                        th.style.cursor = isNearRightEdge ? 'col-resize' : 'default';
                    }
                });

                th.addEventListener('mouseleave', () => {
                    if (!isResizing) {
                        th.style.cursor = 'default';
                    }
                });
            });

            // Global mouse events for resizing
            const handleMouseMove = (e) => {
                if (!isResizing || currentTh !== table.querySelector('.resizing')) return;
                
                const width = startWidth + e.clientX - resizeStartX;
                if (width > 50) { // Minimum width
                    currentTh.style.width = width + 'px';
                    currentTh.style.minWidth = width + 'px';
                }
            };

            const handleMouseUp = () => {
                if (isResizing && currentTh && currentTh === table.querySelector('.resizing')) {
                    isResizing = false;
                    currentTh.classList.remove('resizing');
                    table.classList.remove('col-resizing');
                    currentTh = null;
                }
            };

            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
        });
    });
</script>
