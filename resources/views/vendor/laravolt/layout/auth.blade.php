<x-volt-base>
    @push('style')
        <style>
            .layout--auth.is-modern,
            .layout--auth.is-classic {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                min-height: 100vh !important;
                padding-top: 0 !important;
            }
        </style>
    @endpush
    <div class="layout--auth is-{!! config('laravolt.ui.login_layout') !!}">
        <div class="layout--auth__container">
            <div class="x-inspire" style="background-image: url('{!! config('laravolt.ui.login_background') !!}')">
                <div class="x-inspire__content"
                    style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                    <img src="/images/dpdlogo.png" alt="Logo DPD RI"
                        style="max-width: 100%; max-height: 100%; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5));">
                </div>
            </div>


            <div class="x-auth">
                <main class="x-auth__content" up-main="root">
                    <div style="padding: 0.5rem; margin-bottom: 2rem; text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 14px;">
                            <img src="/images/logo.png" alt="Logo DPD"
                                style="width: 44px; height: 44px; object-fit: contain;">
                            <div style="display: flex; flex-direction: column; text-align: left;">
                                <span
                                    style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 800; letter-spacing: 0.025em; color: #10131a; line-height: 1.1;">DPDRI
                                    <span style="color: #88c445;"><i>AWARDS</i></span> 2026</span>
                                <span style="color: #6b7280; font-size: 13px; letter-spacing: 0.025em;">Dari Daerah
                                    untuk Indonesia</span>
                            </div>
                        </div>
                    </div>

                    {{ $slot }}
                    @stack('main')

                </main>
            </div>
        </div>
    </div>
</x-volt-base>
