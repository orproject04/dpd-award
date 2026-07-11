<style>
    /* Base transition untuk semua elemen menu */
    .ui.vertical.menu .item,
    .ui.vertical.menu .title.item,
    .ui.vertical.menu .content .item,
    .ui.vertical.menu a.item {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }

    /* Transition untuk icon */
    .ui.vertical.menu .x-icon {
        transition: fill 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275),
            stroke 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }

    .active-via-url {
        background-color: {{ hexToRgba(config('laravolt.ui.color'), 0.9) }} !important;
        color: white !important;
    }

    .ui.menu div.title.item.active {
        background-color: white !important;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }

    .ui.vertical.menu .title.item:hover {
        background-color: {{ hexToRgba(config('laravolt.ui.color'), 0.9) }} !important;
        /* abu-abu tua */
        color: white !important;
        transform: translateX(4px);
    }

    .ui.vertical.menu .content .item:hover {
        background-color: {{ hexToRgba(config('laravolt.ui.color'), 0.9) }} !important;
        /* abu-abu tua */
        color: white !important;
        transform: translateX(4px);
    }

    .ui.vertical.menu a.title.item.active-via-url .x-icon {
        fill: white !important;
        stroke: white !important;
    }

    /* Saat item di-hover */
    .ui.vertical.menu .title.item:hover .x-icon {
        fill: white !important;
        stroke: white !important;
    }

    .ui.vertical.menu a.title.item.active-via-url .menu-text {
        color: white !important;
    }

    /* Saat item di-hover */
    .ui.vertical.menu .title.item:hover .menu-text {
        color: white !important;
    }

    .ui.vertical.menu .content .ui.list a {
        color: #52525b !important;
    }

    .ui.vertical.menu .content .ui.list a.active-via-url {
        color: white !important;
    }

    .ui.vertical.menu .content .ui.list a:hover {
        color: white !important;
    }


    /* Enhanced hover effect untuk child menu */
    .ui.vertical.menu .content .item {
        position: relative;
        overflow: hidden;
    }

    .ui.vertical.menu .content .item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 0;
    }

    .ui.vertical.menu .content .item:hover::before {
        left: 100%;
    }

    .ui.vertical.menu .content .item>* {
        position: relative;
        z-index: 1;
    }

    /* Smooth accordion animation */
    .ui.accordion .content {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }
</style>

<div class="sidebar__menu mb-8">
    @if (!$items->isEmpty())
        @if (config('laravolt.platform.features.quick_switcher'))
            @include('laravolt::quick-switcher.modal')
        @endif

        <div class="ui attached vertical menu fluid" data-role="original-menu">

            @foreach ($items as $item)
                @if ($item->hasChildren())
                    <div class="item">
                        <div class="header">{{ $item->title }}</div>
                    </div>
                    <div class="ui accordion sidebar__accordion" data-role="sidenav">
                        @include('laravolt::menu.sidebar_items', ['items' => $item->children()])
                    </div>
                @else
                    <div class="ui accordion sidebar__accordion">
                        <a class="title title__1 item empty {{ \Laravolt\Platform\Services\SidebarMenu::setActiveParent($item->children(), $item->isActive) }}"
                            href="{{ $item->url() }}">
                            <i class="left icon {{ $item->data('icon') }}"></i>
                            <span>{{ $item->title }}</span>
                        </a>
                        <div class="content"></div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const currentUrl = window.location.href;

        document.querySelectorAll('.ui.vertical.menu .content a.item').forEach(link => {
            // Sesuaikan jika hanya path yang digunakan (bukan full URL)
            if (currentUrl.includes(link.href)) {
                link.classList.add('active-via-url');
            }
        });

        document.querySelectorAll('.ui.vertical.menu a.title.item').forEach(link => {
            // Sesuaikan jika hanya path yang digunakan (bukan full URL)
            if (currentUrl.includes(link.href)) {
                link.classList.add('active-via-url');
            }
        });
    });
</script>
