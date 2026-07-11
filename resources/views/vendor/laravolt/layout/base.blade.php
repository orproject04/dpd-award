<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" data-font-size="{{ config('laravolt.ui.font_size') }}"
    data-theme="{{ config('laravolt.ui.theme') }}" data-sidebar-density="{{ config('laravolt.ui.sidebar_density') }}"
    data-spa="{{ config('laravolt.platform.features.spa') }}">

<head>
    <title>DPDRI AWARDS 2026</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <meta charset="UTF-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta content="no-cache">

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    <style>
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        #page-loader.hidden {
            display: none !important;
        }

        .loader-logo-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 150px;
            height: 150px;
        }

        .loader-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            animation: loader-pulse 2s infinite ease-in-out;
        }

        .loader-spinner {
            position: absolute;
            width: 130px;
            height: 130px;
            border: 4px solid transparent;
            border-top-color: #88c445; /* primary color */
            border-bottom-color: #22abe1; /* secondary color */
            border-radius: 50%;
            animation: loader-spin 1.5s linear infinite;
        }

        .loader-spinner-inner {
            position: absolute;
            width: 110px;
            height: 110px;
            border: 4px solid transparent;
            border-left-color: #10131a;
            border-right-color: #e2e8f0;
            border-radius: 50%;
            animation: loader-spin-reverse 2s linear infinite;
        }

        @keyframes loader-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes loader-spin-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes loader-pulse {
            0%, 100% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
        }

        .loader-text {
            margin-top: 24px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #10131a;
            letter-spacing: 0.05em;
            animation: loader-fade 1.5s infinite ease-in-out;
        }

        @keyframes loader-fade {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
    </style>
    @stack('head')

    @laravoltScripts
</head>

<body class="{{ $bodyClass ?? '' }} @yield('body.class')">

    {{ $slot }}

    @livewireScripts
    @stack('script')
    @stack('body')
</body>

<script>
    const modalHandlers = {};

    function openModal(id) {
        const modal = document.getElementById(id);
        const modalBox = document.getElementById(id + '-box');

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        modalBox.classList.remove('scale-95');
        modalBox.classList.add('scale-100');

        setupClickOutsideToClose(id);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const modalBox = document.getElementById(id + '-box');

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        modalBox.classList.remove('scale-100');
        modalBox.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('pointer-events-none');
        }, 300);

        // ❗ Hapus event listener setelah modal ditutup
        if (modalHandlers[id]) {
            document.removeEventListener('click', modalHandlers[id]);
            delete modalHandlers[id];
        }
    }

    function setupClickOutsideToClose(id) {
        const modal = document.getElementById(id);
        const modalBox = document.getElementById(id + '-box');

        // Hapus dulu listener lama jika ada
        if (modalHandlers[id]) {
            document.removeEventListener('click', modalHandlers[id]);
        }

        const handler = function(event) {
            if (!modalBox.contains(event.target)) {
                closeModal(id);
            }
        };

        // Simpan handler supaya bisa dihapus nanti
        modalHandlers[id] = handler;

        // Delay sedikit supaya klik pembuka tidak langsung menutup
        setTimeout(() => {
            document.addEventListener('click', handler);
        }, 10);
    }

    function showAlert(type = 'success', title = '', text = '') {
        Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonText: {!! json_encode(__('action.confirm')) !!}
        });
    }

    function showLoading() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.remove('hidden');
        }
    }

    function hideLoading() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    }

    function goTo(url) {
        const loader = document.getElementById('page-loader');
        if (loader) loader.classList.remove('hidden');

        // Delay kecil agar loader sempat muncul
        setTimeout(() => {
            window.location.href = url;
        }, 50);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');

        // Handle browser back/forward navigation - hide loader when page is restored from cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted && loader) {
                // Page was loaded from cache (back/forward button)
                loader.classList.add('hidden');
            }
        });

        // Handle popstate (browser back/forward buttons)
        window.addEventListener('popstate', function(event) {
            if (loader) {
                loader.classList.add('hidden');
            }
        });

        // Modern approach: use Navigation API if available
        if ('navigation' in window) {
            window.navigation.addEventListener('navigate', function(event) {
                // Check if this is a back/forward navigation
                if (event.navigationType === 'traverse') {
                    setTimeout(() => {
                        if (loader) {
                            loader.classList.add('hidden');
                        }
                    }, 50);
                }
            });
        }

        // Tangani semua link
        document.querySelectorAll('a[href]:not([target="_blank"])').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = link.getAttribute('href');
                // Pastikan bukan link ke #
                if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    loader.classList.remove('hidden');
                }
            });
        });

        // Tangani submit form via klik
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                loader.classList.remove('hidden');
            });

            // Override native submit method supaya form.submit() juga trigger loader
            const originalSubmit = form.submit;
            form.submit = function() {
                loader.classList.remove('hidden');
                originalSubmit.call(form);
            };
        });
    });

    (function() {
        const method = document.querySelector('meta[name="request-method"]')?.content || "GET";
        const urlObj = new URL(window.location.href);
        const currentPath = urlObj.origin + urlObj.pathname + urlObj.hash;

        let customHistory = JSON.parse(sessionStorage.getItem("customHistory") || "[]");

        if (customHistory.length > 0) {
            let last = customHistory[customHistory.length - 1];
            const lastUrlObj = new URL(last.url);
            const lastPath = lastUrlObj.origin + lastUrlObj.pathname + lastUrlObj.hash;

            // Kalau URL terakhir sama (abaikan query string), update method (replace) bukan push
            if (lastPath === currentPath) {
                last.method = method;
                customHistory[customHistory.length - 1] = last;
            } else {
                customHistory.push({
                    method,
                    url: urlObj.href
                });
            }
        } else {
            customHistory.push({
                method,
                url: urlObj.href
            });
        }

        sessionStorage.setItem("customHistory", JSON.stringify(customHistory));

        // Only hide loader if we detect this is a back navigation by checking performance navigation type
        if (performance.navigation && performance.navigation.type === 2) {
            // Navigation type 2 = back/forward button
            setTimeout(() => {
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.classList.add('hidden');
                }
            }, 100);
        }
    })();


    function safeBack(fallbackUrl, dashboardUrl) {
        // Hide loader before navigation
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }

        let customHistory = JSON.parse(sessionStorage.getItem("customHistory") || "[]");

        // hapus halaman sekarang
        customHistory.pop();

        // cari GET terakhir
        let target = null;
        while (customHistory.length > 0) {
            const last = customHistory.pop();
            if (last.method === "GET") {
                target = last.url;
                break;
            }
        }

        // simpan ulang history setelah dipangkas
        sessionStorage.setItem("customHistory", JSON.stringify(customHistory));

        if (target) {
            window.location.href = target;
        } else if (fallbackUrl && fallbackUrl.trim() !== "") {
            window.location.href = fallbackUrl;
        } else {
            window.location.href = dashboardUrl;
        }
    }
</script>
