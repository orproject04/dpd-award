@php
    /** @var \Laravolt\Platform\Services\SidebarMenu */
    $items = app('laravolt.menu.sidebar')->allMenu();
@endphp

<nav class="sidebar" data-role="sidebar" id="sidebar">
    <script>
        if (document.body.clientWidth < 991 || localStorage.getItem('layout-mode') === 'full') {
            document.getElementById('sidebar').classList.add('hide');
            document.getElementById('topbar').classList.add('full');
        } else {
            document.getElementById('sidebar').classList.add('show');
        }
    </script>

    <div class="sidebar__scroller">
        <!-- Modern Sidebar Header -->
        <!-- Enhanced Logo Container -->
        <div class="flex-shrink-0 cursor-pointer p-1 !pb-0" onclick="window.location.href='{{ route('dashboard') }}'">
            <div style="padding: 0.5rem; margin-bottom: 2rem; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 14px;">
                    <img src="/images/logo.png" alt="Logo DPD" style="width: 44px; height: 44px; object-fit: contain;">
                    <div style="display: flex; flex-direction: column; text-align: left;">
                        <span
                            style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 800; letter-spacing: 0.025em; color: #10131a; line-height: 1.1;">DPDRI
                            <span style="color: #88c445;"><i>AWARDS</i></span></span>
                        <span style="color: #6b7280; font-size: 11px; letter-spacing: 0.025em;">Dari Daerah
                            untuk Indonesia</span>
                    </div>
                </div>
            </div>
        </div>

        @include('laravolt::menu.sidebar_profile')
        @include('laravolt::menu.sidebar_menu')

        <div class="px-2 py-2 text-center text-xs text-gray-500">
            <span>&copy; 2026 DPD RI. All rights reserved.</span>
        </div>
    </div>

</nav>
