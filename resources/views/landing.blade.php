<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DPDRI AWARDS 2026</title>
    <meta name="description"
        content="DPDRI AWARDS 2026 - Penghargaan bagi inovator di bidang pendidikan, kesehatan, pangan, dan pelestari budaya daerah.">
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preload" as="image" href="{{ asset('images/hero-bg.jpg') }}" fetchpriority="high">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.15/dist/hls.min.js"></script>
    <script>
        function hlsPlayer(src, poster) {
            return {
                started: false,
                poster,
                hls: null,
                init() {
                    const video = this.$refs.video;
                    if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        video.src = src;
                    } else if (window.Hls && window.Hls.isSupported()) {
                        this.hls = new window.Hls();
                        this.hls.loadSource(src);
                        this.hls.attachMedia(video);
                    } else {
                        video.src = src;
                    }
                },
                play() {
                    this.started = true;
                    this.$nextTick(() => this.$refs.video.play().catch(() => { }));
                }
            }
        }
    </script>

    <style>
        @media (min-width: 1024px) {
            html {
                zoom: 0.9;
            }
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0a0c11;
            color: #10131a;
        }

        .cz {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes flip {
            0% {
                transform: rotateX(0deg);
            }

            50% {
                transform: rotateX(90deg);
                opacity: 0.5;
            }

            51% {
                transform: rotateX(-90deg);
                opacity: 0.5;
            }

            100% {
                transform: rotateX(0deg);
                opacity: 1;
            }
        }

        .animate-flip {
            animation: flip 0.6s ease-in-out;
            transform-style: preserve-3d;
        }

        @keyframes sheen {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 200% 50%;
            }
        }

        @keyframes floaty {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes shimmer {
            100% {
                transform: translateX(250%);
            }
        }

        .animate-sheen {
            animation: sheen 6s linear infinite;
        }

        .animate-floaty {
            animation: floaty 2s ease-in-out infinite;
        }

        .group:hover .shimmer-effect {
            animation: shimmer 1.5s infinite;
        }

        [x-cloak] {
            display: none !important;
        }

        .radial-gradient-bg1 {
            position: absolute;
            top: -70px;
            right: -40px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(242, 207, 110, 0.28) 0%, rgba(230, 184, 75, 0) 70%);
            animation: floatSlow 7s ease-in-out infinite;
            pointer-events: none;
        }

        .radial-gradient-bg2 {
            position: absolute;
            bottom: -20px;
            left: -90px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(230, 184, 75, 0.20) 0%, rgba(230, 184, 75, 0) 70%);
            animation: floatSlow 9s ease-in-out infinite reverse;
            pointer-events: none;
        }

        .radial-gradient-bg3 {
            position: absolute;
            top: 40px;
            right: -90px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(242, 207, 110, 0.28) 0%, rgba(230, 184, 75, 0) 70%);
            animation: floatSlow 7s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes floatSlow {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(20px, -25px);
            }
        }

        .twinkle-star {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #f2cf6e;
            clip-path: polygon(50% 0, 60% 40%, 100% 50%, 60% 60%, 50% 100%, 40% 60%, 0 50%, 40% 40%);
            filter: drop-shadow(0 0 5px rgba(242, 207, 110, 0.7));
            animation: twinkle 3.5s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.15;
                transform: scale(0.7);
            }

            50% {
                opacity: 1;
                transform: scale(1.15);
            }
        }
    </style>
</head>

<body class="bg-[#050608] text-white antialiased selection:bg-[#88c445] selection:text-[#0a0c11]" x-data="{
        scrolled: false,
        mobileMenuOpen: false
    }" @scroll.window="scrolled = (window.pageYOffset > 60)">

    <!-- HEADER -->
    <header :class="(scrolled || mobileMenuOpen) ? 'pt-4 pb-3 lg:py-5' : 'py-4 lg:py-8'"
        class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300">

        <div class="absolute inset-0 pointer-events-none transition-all duration-300"
            :class="mobileMenuOpen ? 'opacity-100' : 'opacity-80'"
            :style="mobileMenuOpen ? '' :
                '-webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 90%); mask-image: linear-gradient(to bottom, black 60%, transparent 100%);'">
            <div class="absolute inset-0 backdrop-blur-xl transition-colors duration-300"
                :class="mobileMenuOpen ? 'bg-[#0a0c11]' : 'bg-[#0a0c11]/90'"></div>
        </div>

        <div class="relative w-full px-4 lg:px-12 flex items-start lg:items-center justify-between">
            <a href="#beranda"
                class="cz text-[26px] font-extrabold tracking-wide text-white whitespace-nowrap flex items-center gap-0 lg:gap-2 mt-0.5 sm:mt-0">
                <img src="{{ asset('images/detik.png') }}" alt="Logo Detik.com"
                    class="h-12 sm:h-15 md:h-15 lg:h-[65px] object-contain transition-all duration-300">
                <img src="{{ asset('images/dpdlogo.png') }}" alt="Logo DPD RI"
                    class="h-12 sm:h-15 md:h-15 lg:h-[65px] object-contain transition-all duration-300">
                <img src="{{ asset('images/setjenlogo.png') }}" alt="Logo Setjen DPD RI"
                    class="h-12 sm:h-15 md:h-15 lg:h-[65px] object-contain transition-all duration-300">
            </a>

            <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu"
                class="lg:hidden text-white p-2 focus:outline-none cursor-pointer mt-2.5 sm:mt-3.5">
                <svg x-show="!mobileMenuOpen" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <line x1="3" y1="18" x2="21" y2="18" />
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <nav class="hidden lg:flex items-center gap-6 xl:gap-8">
                <a href="#beranda"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">BERANDA</a>
                <a href="#kategori"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">KATEGORI</a>
                <a href="#syarat"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">KETENTUAN</a>
                <a href="#alur"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">TIMELINE</a>
                <a href="#lacak"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">LACAK</a>
                <a href="#statistik"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors">STATISTIK</a>
                <a href="#faq"
                    class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider transition-colors whitespace-nowrap">TANYA
                    JAWAB</a>
                <a href="{{ route('nominasi') }}"
                    class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[14px] tracking-wide px-6 py-2.5 rounded-full shadow-[0_8px_30px_rgba(224,181,60,0.28)] hover:scale-105 transition-transform">DAFTAR</a>
            </nav>
        </div>

        <div x-show="mobileMenuOpen" x-cloak x-transition
            class="lg:hidden absolute top-full left-0 right-0 bg-[#0a0c11]/95 backdrop-blur-xl border-b border-[#e0b53c]/20 py-6 px-6 flex flex-col gap-5 shadow-xl">
            <a href="#beranda" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">BERANDA</a>
            <a href="#kategori" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">KATEGORI</a>
            <a href="#syarat" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">KETENTUAN</a>
            <a href="#alur" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">TIMELINE</a>
            <a href="#lacak" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">LACAK</a>
            <a href="#statistik" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">STATISTIK</a>
            <a href="#faq" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[17px] font-semibold tracking-wider">TANYA JAWAB</a>
            <a href="{{ route('nominasi') }}"
                class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[17px] tracking-wide px-6 py-4 rounded-full text-center mt-3">DAFTAR</a>
        </div>
    </header>

    <!-- 1. BERANDA (HERO) -->
    <section id="beranda" class="relative min-h-screen flex items-center pt-[140px] pb-[90px] px-6 overflow-hidden">
        <div class="absolute inset-0 z-0 bg-[#0a0c11]">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="DPDRI Awards Hero" width="1920" height="1080"
                fetchpriority="high"
                class="w-full h-full object-cover object-[40%_center] md:object-center scale-[1.2] sm:scale-100 origin-[40%_center] sm:origin-center">
        </div>


        <!-- Full-Width Artistic Brush Stroke Background -->
        <div class="absolute bottom-0 left-0 w-full z-0 h-[250px] pointer-events-none overflow-visible">
            <!-- Base gradient to ensure the absolute bottom is solid black -->
            <div class="absolute bottom-0 w-full h-[150px] bg-gradient-to-t from-[#020305] to-transparent"></div>

            <!-- Layered blurred ellipses to create an organic, artistic brush stroke edge -->
            <div
                class="absolute bottom-[-60px] left-[-10%] w-[120%] h-[200px] bg-[#05070a]/95 blur-[35px] rounded-[100%] rotate-2">
            </div>
            <div
                class="absolute bottom-[-40px] left-[-5%] w-[110%] h-[180px] bg-black blur-[45px] rounded-[100%] -rotate-3">
            </div>
            <div
                class="absolute bottom-[-20px] left-[15%] w-[80%] h-[150px] bg-[#020305] blur-[30px] rounded-[100%] rotate-1">
            </div>
        </div>

        <div
            class="absolute bottom-[20px] sm:bottom-[30px] left-1/2 -translate-x-1/2 z-10 w-full max-w-[1300px] px-6 lg:px-12 flex items-center justify-center lg:justify-between gap-4 md:gap-12 lg:gap-16">

            <svg width="0" height="0" style="position: absolute; width: 0; height: 0;" aria-hidden="true">
                <defs>
                    <linearGradient id="goldLaurel" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fceabb" />
                        <stop offset="30%" stop-color="#f8b500" />
                        <stop offset="70%" stop-color="#b8860b" />
                        <stop offset="100%" stop-color="#8c6b14" />
                    </linearGradient>
                    <g id="laurelLeaf">
                        <path d="M 0 0 C 8 -12 20 -10 25 -2 C 16 4 8 6 0 0 Z" fill="url(#goldLaurel)" />
                        <path d="M 0 0 Q 12 -4 23 -2" stroke="#604000" stroke-width="0.75" fill="none" />
                        <path d="M 0 0 C 8 -12 20 -10 25 -2" stroke="#fceabb" stroke-width="0.5" fill="none"
                            opacity="0.6" />
                    </g>
                    <g id="laurelBranch" transform="translate(0, 5)">
                        <!-- Stem -->
                        <path d="M 5 35 Q 120 75 240 10" stroke="url(#goldLaurel)" stroke-width="2.5"
                            stroke-linecap="round" />

                        <!-- Top side Leaves -->
                        <use href="#laurelLeaf" transform="translate(8, 38) rotate(-10) scale(0.6)" />
                        <use href="#laurelLeaf" transform="translate(25, 40) rotate(-25) scale(0.7)" />
                        <use href="#laurelLeaf" transform="translate(45, 43) rotate(-35) scale(0.85)" />
                        <use href="#laurelLeaf" transform="translate(70, 47) rotate(-45) scale(1)" />
                        <use href="#laurelLeaf" transform="translate(100, 50) rotate(-55) scale(1.1)" />
                        <use href="#laurelLeaf" transform="translate(135, 49) rotate(-65) scale(1.15)" />
                        <use href="#laurelLeaf" transform="translate(170, 43) rotate(-75) scale(1.1)" />
                        <use href="#laurelLeaf" transform="translate(200, 31) rotate(-85) scale(0.9)" />
                        <use href="#laurelLeaf" transform="translate(225, 17) rotate(-95) scale(0.75)" />

                        <!-- Bottom side Leaves -->
                        <use href="#laurelLeaf" transform="translate(35, 44) rotate(70) scale(0.7)" />
                        <use href="#laurelLeaf" transform="translate(58, 48) rotate(60) scale(0.85)" />
                        <use href="#laurelLeaf" transform="translate(85, 51) rotate(50) scale(1)" />
                        <use href="#laurelLeaf" transform="translate(118, 52) rotate(40) scale(1.1)" />
                        <use href="#laurelLeaf" transform="translate(153, 49) rotate(30) scale(1.15)" />
                        <use href="#laurelLeaf" transform="translate(185, 40) rotate(20) scale(1.05)" />
                        <use href="#laurelLeaf" transform="translate(212, 25) rotate(10) scale(0.85)" />

                        <!-- Tip Leaf -->
                        <use href="#laurelLeaf" transform="translate(238, 10) rotate(-25) scale(0.9)" />
                    </g>
                </defs>
            </svg>

            <!-- Left Laurel Separator -->
            <div
                class="hidden md:block flex-1 max-w-[200px] lg:max-w-[330px] opacity-90 drop-shadow-[0_0_8px_rgba(224,181,60,0.5)]">
                <svg viewBox="0 -10 260 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#laurelBranch" />
                </svg>
            </div>

            <!-- Intertwined Trophies -->
            <div class="flex items-center justify-center shrink-0">
                <!-- 1. Far Left -->
                <div class="relative z-10 -mr-5 md:-mr-8 group" style="animation-delay: 0ms;">
                    <div
                        class="absolute inset-0 bg-[#e0b53c]/40 blur-[15px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500">
                    </div>
                    <div
                        class="relative w-[75px] h-[75px] md:w-[85px] md:h-[85px] lg:w-[100px] lg:h-[100px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_8px_25px_rgba(0,0,0,0.8)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala"
                            class="h-[80%] w-auto object-contain drop-shadow-[0_0_8px_rgba(224,181,60,0.4)]">
                    </div>
                </div>

                <!-- 2. Left Middle -->
                <div class="relative z-20 -mr-4 md:-mr-6 group" style="animation-delay: 200ms;">
                    <div
                        class="absolute inset-0 bg-[#e0b53c]/40 blur-[20px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500">
                    </div>
                    <div
                        class="relative w-[100px] h-[100px] md:w-[105px] md:h-[105px] lg:w-[125px] lg:h-[125px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_10px_30px_rgba(0,0,0,0.9)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala"
                            class="h-[80%] w-auto object-contain drop-shadow-[0_0_10px_rgba(224,181,60,0.5)]">
                    </div>
                </div>

                <!-- 3. Right Middle -->
                <div class="relative z-30 -mr-5 md:-mr-8 group" style="animation-delay: 400ms;">
                    <div
                        class="absolute inset-0 bg-[#e0b53c]/40 blur-[20px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500">
                    </div>
                    <div
                        class="relative w-[100px] h-[100px] md:w-[105px] md:h-[105px] lg:w-[125px] lg:h-[125px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_10px_30px_rgba(0,0,0,0.9)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala"
                            class="h-[80%] w-auto object-contain drop-shadow-[0_0_10px_rgba(224,181,60,0.5)]">
                    </div>
                </div>

                <!-- 4. Far Right -->
                <div class="relative z-10 group" style="animation-delay: 600ms;">
                    <div
                        class="absolute inset-0 bg-[#e0b53c]/40 blur-[15px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500">
                    </div>
                    <div
                        class="relative w-[75px] h-[75px] md:w-[85px] md:h-[85px] lg:w-[100px] lg:h-[100px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_8px_25px_rgba(0,0,0,0.8)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala"
                            class="h-[80%] w-auto object-contain drop-shadow-[0_0_8px_rgba(224,181,60,0.4)]">
                    </div>
                </div>
            </div>

            <!-- Right Laurel Separator -->
            <div
                class="hidden md:block flex-1 max-w-[200px] lg:max-w-[330px] opacity-90 drop-shadow-[0_0_8px_rgba(224,181,60,0.5)] scale-x-[-1]">
                <svg viewBox="0 -10 260 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#laurelBranch" />
                </svg>
            </div>

        </div>
    </section>

    <!-- 1.5 PEMBUKAAN -->
    <section class="py-8 px-6 bg-[#0a0c11] text-center border-b border-[#e0b53c]/15">
        <div class="mx-auto w-full">
            <div class="max-w-6xl mx-auto px-4" x-data="{ shown: false }" x-intersect="shown = true"
                x-intersect:leave="shown = false">
                <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="cz text-[clamp(28px,5vw,48px)] font-extrabold uppercase text-white mb-6 transition-all duration-[800ms] ease-out">
                    DPDRI <span class="text-[#e0b53c]"><i>AWARDS</i> 2026</span>
                </h2>
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="text-white/80 text-[clamp(16px,2vw,20px)] leading-[1.7] transition-all duration-[800ms] ease-out delay-100">
                    Mengapresiasi dedikasi dan kontribusi luar biasa dari individu-individu inspiratif di seluruh
                    pelosok&nbsp;Nusantara.<br class="hidden md:block"> DPDRI <i>AWARDS</i> hadir untuk merayakan karya
                    nyata demi kemajuan bangsa.
                </p>
            </div>
            <div class="relative z-10 max-w-7xl mx-auto w-full">
                <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false">
                    <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="flex flex-wrap justify-center gap-4 mt-8 transition-all duration-[800ms] ease-out delay-300">
                        <a href="{{ route('nominasi') }}"
                            class="inline-flex items-center gap-2.5 bg-[#88c445] text-[#0a0c11] font-extrabold text-[16px] tracking-[0.03em] px-9 py-[17px] rounded-full shadow-[0_10px_40px_rgba(136,196,69,0.4)] hover:shadow-[0_16px_50px_rgba(136,196,69,0.6)] hover:-translate-y-[2px] transition-all relative overflow-hidden group">
                            <span
                                class="absolute top-0 -left-[100%] w-[120%] h-full bg-gradient-to-r from-transparent via-white/60 to-transparent skew-x-[-20deg] shimmer-effect"></span>
                            <span class="relative z-10 flex items-center gap-2.5">
                                Daftar Sekarang
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                    class="group-hover:translate-x-1 transition-transform">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </span>
                        </a>
                        <a href="#alur"
                            class="inline-flex items-center gap-2.5 border-[1.5px] border-[#f5da8b]/55 text-[#f5da8b] font-bold text-[16px] px-[34px] py-[17px] rounded-full hover:bg-[#e0b53c]/10 transition-colors">
                            Lihat Alur Seleksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HIGHLIGHT MALAM ANUGERAH -->
    <section id="highlight" class="relative py-[50px] md:py-[90px] px-6 bg-[#0a0c11] overflow-hidden">
        <span class="twinkle-star" style="top: 8%; left: 12%; width: 10px; height: 10px; animation-delay: 0.5s;"></span>
        <span class="twinkle-star"
            style="top: 14%; right: 18%; width: 12px; height: 12px; animation-delay: 1.6s;"></span>
        <span class="twinkle-star"
            style="bottom: 10%; left: 20%; width: 9px; height: 9px; animation-delay: 2.4s;"></span>
        <span class="twinkle-star"
            style="bottom: 16%; right: 10%; width: 11px; height: 11px; animation-delay: 0.9s;"></span>

        <div class="relative max-w-5xl mx-auto">


            <div x-data="hlsPlayer('{{ asset('videos/highlight/index.m3u8') }}', '{{ asset('images/hero-bg.jpg') }}')"
                x-init="init()"
                class="relative bg-[#0a1e15]/70 border border-[#e0b53c]/25 rounded-[28px] overflow-hidden backdrop-blur-sm shadow-[0_20px_60px_rgba(0,0,0,0.5)]">
                <div class="absolute top-5 left-5 z-20 pointer-events-none">
                </div>

                <div class="relative aspect-video bg-gradient-to-br from-[#0c3b28] to-[#0a0c11] overflow-hidden group">
                    <video autoplay muted loop playsinline x-ref="video" :poster="poster" playsinline preload="metadata"
                        class="w-full h-full object-cover" :controls="started" @play="started = true"></video>

                    <button x-show="!started" @click="play()"
                        class="absolute inset-0 flex flex-col items-center justify-center cursor-pointer bg-black/20 hover:bg-black/10 transition-colors"
                        aria-label="Putar video">
                        <div
                            class="w-20 h-20 rounded-full bg-white/15 backdrop-blur-sm border border-white/25 flex items-center justify-center group-hover:bg-[#e0b53c] group-hover:border-[#e0b53c] group-hover:scale-110 transition-all duration-300 shadow-[0_0_40px_rgba(224,181,60,0.35)]">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"
                                class="text-white group-hover:text-[#0a0c11] ml-1 transition-colors">
                                <polygon points="6 4 20 12 6 20 6 4" />
                            </svg>
                        </div>
                        <p class="mt-5 text-white/70 text-[13px] font-semibold tracking-wide">Putar Highlight</p>
                    </button>
                </div>

                <div class="text-center mt-4 mb-4 md:mt-6 md:mb-8" x-data="{ shown: false }" x-intersect="shown = true"
                    x-intersect:leave="shown = false">
                    <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="cz text-[clamp(28px,5vw,48px)] font-extrabold uppercase text-white mb-2 transition-all duration-[800ms] ease-out delay-100">
                        AWARDS <span class="text-[#e0b53c]">HIGHLIGHT</span>
                    </h2>
                    <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="text-white/80 text-[clamp(16px,2vw,22px)] tracking-widest font-medium transition-all duration-[800ms] ease-out delay-200">
                        <i>DPDRI Awards</i> 2025
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- 1.6 KILAS BALIK PEMENANG -->
    <section id="pemenang"
        class="relative pt-[50px] pb-[75px] px-6 bg-gradient-to-b from-[#0a2519] via-[#0c3b28] to-[#0a2519] overflow-hidden">
        <span class="twinkle-star" style="top: 6%;  left: 8%;  width: 10px; height: 10px; animation-delay: 0s;"></span>
        <span class="twinkle-star"
            style="top: 12%; right: 12%; width: 14px; height: 14px; animation-delay: 0.7s;"></span>
        <span class="twinkle-star"
            style="top: 22%; left: 22%; width: 8px;  height: 8px;  animation-delay: 1.4s;"></span>
        <span class="twinkle-star"
            style="top: 4%;  right: 32%; width: 12px; height: 12px; animation-delay: 2.1s;"></span>
        <span class="twinkle-star"
            style="top: 38%; left: 4%;  width: 11px; height: 11px; animation-delay: 0.4s;"></span>
        <span class="twinkle-star"
            style="top: 48%; right: 5%; width: 13px; height: 13px; animation-delay: 1.8s;"></span>
        <span class="twinkle-star"
            style="bottom: 20%; left: 10%; width: 9px; height: 9px; animation-delay: 2.6s;"></span>
        <span class="twinkle-star"
            style="bottom: 12%; right: 18%; width: 12px; height: 12px; animation-delay: 1.1s;"></span>
        <span class="twinkle-star"
            style="bottom: 30%; right: 40%; width: 8px; height: 8px; animation-delay: 3s;"></span>
        <span class="twinkle-star"
            style="bottom: 6%;  left: 32%; width: 11px; height: 11px; animation-delay: 0.9s;"></span>

        <div class="radial-gradient-bg1"></div>
        <div class="radial-gradient-bg2"></div>

        <div>
            <div class="relative max-w-6xl mx-auto">
                <div class="text-center mb-8" x-data="{ shown: false }" x-intersect="shown = true"
                    x-intersect:leave="shown = false">
                    <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="cz text-[clamp(30px,5vw,52px)] font-extrabold uppercase text-white mb-4 transition-all duration-[800ms] ease-out delay-100">
                        KILAS BALIK <span class="text-[#e0b53c]">PEMENANG</span>
                    </h2>
                    <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="text-white/70 text-[clamp(15px,1.6vw,18px)] leading-[1.7] max-w-2xl mx-auto transition-all duration-[800ms] ease-out delay-200">
                        Sosok-sosok inspiratif yang telah diapresiasi DPD RI Awards atas dedikasi nyata mereka bagi
                        bangsa.
                    </p>
                </div>

                <div x-data="pemenangCarousel()" x-init="init()" class="relative">

                    <div
                        class="relative min-h-[400px] md:min-h-[540px] flex items-center justify-center overflow-hidden">
                        <template x-for="(it, i) in items" :key="i">
                            <div class="absolute top-1/2 left-1/2 transition-all duration-[600ms] ease-out cursor-pointer will-change-transform"
                                :style="cardStyle(i)" @click="i !== index && goTo(i)">
                                <div
                                    class="group relative w-[300px] sm:w-[260px] md:w-[360px] aspect-[4/5] rounded-[26px] overflow-hidden border border-[#e0b53c]/25 bg-gradient-to-br from-[#0c3b28] to-[#0a0c11] shadow-[0_20px_60px_rgba(0,0,0,0.6)]">
                                    <template x-if="it.type !== 'video'">
                                        <img :src="it.image" :alt="it.name"
                                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-[600ms] group-hover:scale-105"
                                            onerror="this.style.opacity='0.2'">
                                    </template>

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-[#0a0c11] via-[#0a0c11]/30 to-transparent opacity-70 group-hover:opacity-100 transition-opacity duration-500">
                                    </div>

                                    <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6 transition-all duration-500 ease-out"
                                        :class="i === index ? 'opacity-100 translate-y-0 md:opacity-0 md:translate-y-6 md:group-hover:opacity-100 md:group-hover:translate-y-0' : 'opacity-0 translate-y-6'">
                                        <template x-if="it.category">
                                            <span
                                                class="inline-block bg-[#e0b53c] text-[#0a0c11] text-[9px] sm:text-[11px] font-extrabold tracking-wider px-2 py-0.5 sm:py-1 rounded-full mb-1.5 sm:mb-2"
                                                x-text="it.category"></span>
                                        </template>
                                        <h3 class="cz text-white text-[15px] sm:text-[20px] md:text-[22px] font-extrabold leading-tight mb-1"
                                            x-text="it.name"></h3>
                                        <div class="text-[#e0b53c] text-[9px] sm:text-[11px] md:text-[12px] font-bold tracking-[0.16em] uppercase mb-1.5 sm:mb-2"
                                            x-text="it.role"></div>
                                        <p class="text-white/80 text-[11px] sm:text-[13px] md:text-[14px] leading-[1.55] line-clamp-3 sm:line-clamp-none"
                                            x-text="it.description"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button @click="prev()" aria-label="Sebelumnya"
                            class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-30 w-11 h-11 flex items-center justify-center bg-[#0a0c11]/80 hover:bg-[#e0b53c] hover:text-[#0a0c11] text-white/80 border border-white/10 rounded-full backdrop-blur-sm transition-all">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </button>
                        <button @click="next()" aria-label="Berikutnya"
                            class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-30 w-11 h-11 flex items-center justify-center bg-[#0a0c11]/80 hover:bg-[#e0b53c] hover:text-[#0a0c11] text-white/80 border border-white/10 rounded-full backdrop-blur-sm transition-all">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>

                    <div
                        class="hidden sm:flex flex-wrap justify-center gap-3 mt-8 max-w-4xl mx-auto sm:[&>*]:w-32 md:[&>*]:w-36">
                        <template x-for="(it, i) in items" :key="`thumb-${i}`">
                            <button @click="goTo(i)"
                                class="relative aspect-[4/5] rounded-xl overflow-hidden border-2 transition-all group"
                                :class="index === i ? 'border-[#e0b53c] shadow-[0_0_20px_rgba(224,181,60,0.35)]' : 'border-white/10 hover:border-white/30'">
                                <template x-if="it.type !== 'video'">
                                    <div class="relative w-full h-full bg-gradient-to-br from-[#0c3b28] to-[#0a0c11]">
                                        <img :src="it.image" alt=""
                                            class="w-full h-full object-cover opacity-70 group-hover:opacity-100 transition-opacity"
                                            onerror="this.style.display='none'">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-[#0a0c11] via-[#0a0c11]/30 to-transparent">
                                        </div>
                                    </div>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function pemenangCarousel() {
                return {
                    index: 0,
                    autoplayTimer: null,
                    items: [
                        {
                            type: 'image',
                            short: 'Aqsa',
                            name: 'Aqsa Aufa Syauqi S.',
                            role: 'Peneliti Medis Muda & Penggagas Riset',
                            category: 'Pembangunan Sosial & Kesehatan',
                            description: 'Pemuda pelopor bidang teknologi dan inovasi yang membawa semangat anak muda Indonesia ke tingkat lebih tinggi.',
                            image: '{{ asset('images/Aqsa.jpg') }}'
                        },
                        {
                            type: 'image',
                            short: 'Sugeng',
                            name: 'Sugeng Handoko',
                            role: 'Penggerak Wisata Desa Nglanggeran',
                            category: 'Pariwisata & Kebudayaan',
                            description: 'Pelopor pemuda pariwisata desa yang mengantar Nglanggeran menjadi salah satu desa wisata terbaik dunia versi UNWTO.',
                            image: '{{ asset('images/Sugeng.jpg') }}'
                        },
                        {
                            type: 'image',
                            short: 'Febrita',
                            name: 'Febrita Lustia',
                            role: 'TP-PKK Provinsi Sumatera Selatan',
                            category: 'Ekonomi Kreatif',
                            description: 'Tokoh inspiratif yang memberikan kontribusi luar biasa bagi masyarakat melalui pengabdiannya di berbagai bidang.',
                            image: '{{ asset('images/Febrita.jpg') }}'
                        },
                        {
                            type: 'image',
                            short: 'Khofifah',
                            name: 'Khofifah Indar Parawansa',
                            role: 'Gubernur Jawa Timur',
                            category: 'Perlindungan Anak & Pemberdayaan Perempuan',
                            description: 'Perempuan inspiratif dengan kontribusi luar biasa mewujudkan kesetaraan gender, perlindungan dan pemberdayaan perempuan tahun 2025.',
                            image: '{{ asset('images/Gubjatim.jpg') }}'
                        },
                        {
                            type: 'image',
                            short: 'Bayu',
                            name: 'Bayu Krisnamurthi',
                            role: 'Tokoh Ketahanan Pangan',
                            category: 'Ketahanan Pangan',
                            description: 'Kontribusi nyata dalam pembangunan sektor pangan nasional dan pemberdayaan petani Indonesia.',
                            image: '{{ asset('images/Bayu.jpg') }}'
                        }
                    ],
                    init() {
                        this.startAutoplay();
                    },
                    cardStyle(i) {
                        const n = this.items.length;
                        let offset = i - this.index;
                        if (offset > n / 2) offset -= n;
                        if (offset < -n / 2) offset += n;
                        const abs = Math.abs(offset);
                        const w = window.innerWidth;
                        const isMobile = w < 640;
                        if (isMobile) {
                            const opacity = abs === 0 ? 1 : 0;
                            const z = 20 - abs;
                            return `transform: translate(-50%, -50%); opacity: ${opacity}; z-index: ${z}; pointer-events: ${abs === 0 ? 'auto' : 'none'};`;
                        }
                        const step = w >= 768 ? 260 : 220;
                        const x = offset * step;
                        const scale = abs === 0 ? 1 : abs === 1 ? 0.82 : abs === 2 ? 0.66 : 0.55;
                        const opacity = abs === 0 ? 1 : abs === 1 ? 0.55 : abs === 2 ? 0.25 : 0;
                        const rotateY = offset === 0 ? 0 : offset > 0 ? -12 : 12;
                        const z = 20 - abs;
                        return `transform: translate(-50%, -50%) translateX(${x}px) scale(${scale}) perspective(1200px) rotateY(${rotateY}deg); opacity: ${opacity}; z-index: ${z}; pointer-events: ${abs > 2 ? 'none' : 'auto'};`;
                    },
                    next() {
                        this.index = (this.index + 1) % this.items.length;
                        this.restartAutoplay();
                    },
                    prev() {
                        this.index = (this.index - 1 + this.items.length) % this.items.length;
                        this.restartAutoplay();
                    },
                    goTo(i) {
                        this.index = i;
                        this.restartAutoplay();
                    },
                    startAutoplay() {
                        this.autoplayTimer = setInterval(() => this.next(), 6000);
                    },
                    restartAutoplay() {
                        clearInterval(this.autoplayTimer);
                        this.startAutoplay();
                    }
                }
            }
        </script>
    </section>



    <!-- 1.8 SOROTAN MEDIA -->
    <section id="sorotan-media"
        class="relative pt-[45px] pb-[90px] px-6 bg-gradient-to-b from-[#0a2519] via-[#0c3b28] to-[#0a2519] overflow-hidden">
        <span class="twinkle-star"
            style="top: 20%; right: 22%; width: 10px; height: 10px; animation-delay: 1.2s;"></span>
        <span class="twinkle-star"
            style="bottom: 12%; left: 8%; width: 12px; height: 12px; animation-delay: 2s;"></span>

        <div class="radial-gradient-bg3"></div>

        <div class="relative max-w-6xl mx-auto">
            <div
                class="flex items-center justify-center gap-4 mb-10 text-[#e0b53c]/60 text-[12px] font-bold tracking-[0.3em]">
                <span class="h-px w-16 sm:w-24 bg-[#e0b53c]/25"></span>
                <span class="flex items-center gap-2 whitespace-nowrap">
                    <span>&#10022;</span>
                    KILAS BALIK BERITA
                    <span>&#10022;</span>
                </span>
                <span class="h-px w-16 sm:w-24 bg-[#e0b53c]/25"></span>
            </div>

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10" x-data="{ shown: false }"
                x-intersect="shown = true" x-intersect:leave="shown = false">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[20px]'"
                    class="transition-all duration-[700ms] ease-out">
                    <h2 class="cz text-[clamp(30px,5vw,48px)] font-extrabold uppercase text-white leading-none">
                        SOROTAN <span class="text-[#e0b53c]">MEDIA</span>
                    </h2>
                </div>
            </div>

            @php
                $mediaHighlights = [
                    [
                        'source' => 'detikNews',
                        'date' => '28 Oktober 2025',
                        'title' => 'DPD Award 2025 Angkat Kiprah Tokoh Daerah ke Panggung Nasional',
                        'excerpt' => 'Jakarta - Dewan Perwakilan Daerah (DPD) RI menyelenggarakan DPD Award sebagai bentuk pengakuan nasional terhadap kontribusi tokoh daerah yang memiliki peran penting dalam pembangunan.',
                        'image' => asset('images/artikel1.jpeg'),
                        'url' => 'https://news.detik.com/berita/d-8183015/dpd-award-2025-angkat-kiprah-tokoh-daerah-ke-panggung-nasional',
                    ],
                    [
                        'source' => '20detik',
                        'date' => '29 Oktober 2025',
                        'title' => 'Video DPD Award 2025, Angkat Kiprah Tokoh Daerah ke Panggung Nasional',
                        'excerpt' => 'Dewan Perwakilan Daerah (DPD) RI menyelenggarakan DPD Award 2025. Acara tersebut diselenggarakan sebagai bentuk pengakuan nasional terhadap kontribusi tokoh daerah yang memiliki peran penting dalam pembangunan.',
                        'image' => asset('images/artikel2.jpg'),
                        'url' => 'https://20.detik.com/detikupdate/20251029-251029002/video-dpd-award-2025-angkat-kiprah-tokoh-daerah-ke-panggung-nasional',
                    ],
                    [
                        'source' => 'detikNews',
                        'date' => '28 Oktober 2025',
                        'title' => 'Daftar Pemenang DPD RI Awards 2025, Khofifah Terima Penghargaan',
                        'excerpt' => 'Jakarta - DPD RI menggelar acara DPD RI Awards 2025. Ada sejumlah bidang yang dianugerahi penghargaan. Pemberian penghargaan DPD RI Awards ini digelar di Tribrata Hotel and Convention, Jakarta Selatan, Selasa (28/10/2025). Ada 5 kategori yang dianugerahi penghargaan.',
                        'image' => asset('images/artikel3.jpeg'),
                        'url' => 'https://news.detik.com/berita/d-8183132/daftar-pemenang-dpd-ri-awards-2025-khofifah-terima-penghargaan',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($mediaHighlights as $article)
                    <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer"
                        class="group bg-[#fbf7ee] rounded-2xl overflow-hidden border border-[#e0b53c]/10 hover:border-[#e0b53c]/40 hover:-translate-y-1 transition-all duration-300 shadow-[0_10px_30px_rgba(0,0,0,0.25)] flex flex-col">
                        <div class="relative aspect-[16/10] bg-[#efe6ce] overflow-hidden">
                            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}"
                                class="w-full h-full object-cover object-top scale-145 group-hover:scale-[1.55] transition-transform duration-[600ms]"
                                onerror="this.style.opacity='0.15'">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="inline-block bg-[#e0b53c] text-[#0a0c11] text-[11px] font-extrabold tracking-wider px-3 py-1 rounded-full shadow">{{ $article['source'] }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-[#8a6d1c] text-[11px] font-bold tracking-wider mb-2">{{ $article['date'] }}
                            </div>
                            <h3
                                class="text-[#10131a] text-[17px] font-extrabold leading-snug mb-3 group-hover:text-[#8a6d1c] transition-colors">
                                {{ $article['title'] }}
                            </h3>
                            <p class="text-[#4b5262] text-[13.5px] leading-[1.65] mb-5">{{ $article['excerpt'] }}</p>
                            <div
                                class="mt-auto inline-flex items-center gap-1.5 text-[#8a6d1c] text-[12.5px] font-bold tracking-wider group-hover:gap-2.5 transition-all">
                                Baca Selengkapnya
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    <!-- 2. COUNTDOWN -->
    <section id="countdown"
        class="py-10 md:py-16 px-6 bg-gradient-to-b from-[#10131a] to-[#0a0c11] border-y border-[#e0b53c]/15">
        <div x-data="countdown()" x-init="start()" x-intersect="shown = true" x-intersect:leave="shown = false"
            class="max-w-[1200px] mx-auto bg-gradient-to-br from-[#191d27] to-[#10131a] border border-[#e0b53c]/30 rounded-[24px] sm:rounded-[28px] p-6 md:p-10 flex flex-col lg:flex-row items-center justify-between gap-8 shadow-[0_30px_70px_rgba(0,0,0,0.5)] transition-all duration-[800ms] ease-out text-center lg:text-left"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
            <div>
                <span class="text-[#88c445] text-[11px] sm:text-xs font-bold tracking-[0.2em]">TENGGAT
                    PENDAFTARAN</span>
                <h2 class="cz text-white text-[clamp(26px,4vw,38px)] font-bold uppercase mt-2">Penutupan <span
                        class="text-[#e0b53c]">Nominasi</span></h2>
                <p class="text-white/55 mt-2 text-[14px] sm:text-[15px]">Waktu tersisa untuk mengirimkan usulan Anda
                </p>
            </div>

            <div class="flex justify-center gap-1.5 sm:gap-3">
                <div class="text-center">
                    <div
                        class="relative w-[52px] h-[64px] sm:w-[78px] sm:h-[88px] bg-[#111] border border-[#e0b53c]/25 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-2 shadow-[inset_0_0_15px_rgba(0,0,0,1)] perspective-1000">
                        <div class="absolute inset-0 h-1/2 border-b-2 border-black/80 bg-white/5 z-0"></div>
                        <span x-ref="day"
                            class="cz text-[24px] sm:text-[42px] font-bold text-[#e0b53c] relative z-10 block"
                            x-text="days">00</span>
                    </div>
                    <span class="text-white/55 text-[9px] sm:text-[11px] font-bold tracking-[0.14em]">HARI</span>
                </div>
                <div class="text-center">
                    <div
                        class="relative w-[52px] h-[64px] sm:w-[78px] sm:h-[88px] bg-[#111] border border-[#e0b53c]/25 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-2 shadow-[inset_0_0_15px_rgba(0,0,0,1)] perspective-1000">
                        <div class="absolute inset-0 h-1/2 border-b-2 border-black/80 bg-white/5 z-0"></div>
                        <span x-ref="hour"
                            class="cz text-[24px] sm:text-[42px] font-bold text-[#e0b53c] relative z-10 block"
                            x-text="hours">00</span>
                    </div>
                    <span class="text-white/55 text-[9px] sm:text-[11px] font-bold tracking-[0.14em]">JAM</span>
                </div>
                <div class="text-center">
                    <div
                        class="relative w-[52px] h-[64px] sm:w-[78px] sm:h-[88px] bg-[#111] border border-[#e0b53c]/25 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-2 shadow-[inset_0_0_15px_rgba(0,0,0,1)] perspective-1000">
                        <div class="absolute inset-0 h-1/2 border-b-2 border-black/80 bg-white/5 z-0"></div>
                        <span x-ref="min"
                            class="cz text-[24px] sm:text-[42px] font-bold text-[#e0b53c] relative z-10 block"
                            x-text="minutes">00</span>
                    </div>
                    <span class="text-white/55 text-[9px] sm:text-[11px] font-bold tracking-[0.14em]">MENIT</span>
                </div>
                <div class="text-center">
                    <div
                        class="relative w-[52px] h-[64px] sm:w-[78px] sm:h-[88px] bg-[#111] border border-[#e0b53c]/25 rounded-lg sm:rounded-2xl flex items-center justify-center mb-1.5 sm:mb-2 shadow-[inset_0_0_15px_rgba(0,0,0,1)] perspective-1000">
                        <div class="absolute inset-0 h-1/2 border-b-2 border-black/80 bg-white/5 z-0"></div>
                        <span x-ref="sec"
                            class="cz text-[24px] sm:text-[42px] font-bold text-[#e0b53c] relative z-10 block"
                            x-text="seconds">00</span>
                    </div>
                    <span class="text-white/55 text-[9px] sm:text-[11px] font-bold tracking-[0.14em]">DETIK</span>
                </div>
            </div>
        </div>

        <script>
            function countdown() {
                return {
                    shown: true,
                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                    endTime: (function () {
                        const raw = '{{ config('laravolt.ui.tanggal_penutupan_pendaftaran') }}';
                        const parsed = raw ? new Date(raw).getTime() : NaN;
                        return isNaN(parsed) ? new Date('{{ now()->addDays(30)->toDateString() }}').getTime() : parsed;
                    })(),

                    initialized: false,
                    triggerFlip(stateName, refName, newVal) {
                        if (this[stateName] !== newVal) {
                            if (this.$refs[refName] && this.initialized) {
                                this.$refs[refName].classList.remove('animate-flip');
                                void this.$refs[refName].offsetWidth;
                                this.$refs[refName].classList.add('animate-flip');
                                setTimeout(() => {
                                    this[stateName] = newVal;
                                }, 300);
                            } else {
                                this[stateName] = newVal;
                            }
                        }
                    },

                    start() {
                        this.update();
                        setInterval(() => {
                            this.update();
                        }, 1000);
                    },
                    update() {
                        const now = new Date().getTime();
                        const distance = this.endTime - now;

                        if (distance < 0) return;

                        let d = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                        let h = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        let m = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        let s = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

                        this.triggerFlip('days', 'day', d);
                        this.triggerFlip('hours', 'hour', h);
                        this.triggerFlip('minutes', 'min', m);
                        this.triggerFlip('seconds', 'sec', s);

                        this.initialized = true;
                    }
                }
            }
        </script>
    </section>

    <!-- 3. KATEGORI -->
    <section id="kategori" class="py-[110px] px-6 bg-[#fbf7ee]" x-data="{ modalOpen: false, activeCat: null }">
        <div class="max-w-7xl mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-16 transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">EMPAT BIDANG
                    APRESIASI</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#10131a] mt-3 leading-none">
                    Kategori <span class="text-[#1b6e4c]">Penghargaan</span></h2>
                <p class="text-[#4b5262] text-[18px] leading-[1.6] max-w-[660px] mx-auto mt-[18px]">
                    Pilih kategori yang paling sesuai dengan prestasi dan inovasi yang Anda miliki.<br
                        class="hidden md:block">
                    Klik untuk melanjutkan pendaftaran
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[26px]">

                @php
                    $categories = [
                        [
                            'id' => 'pendidikan',
                            'title' => 'Bidang Pendidikan',
                            'desc' => 'Kategori Inovator Pendidikan Non Formal/Pendidikan Luar Sekolah',
                            'img' => asset('images/kat-pendidikan.png'),
                            'cp' => [
                                ['name' => 'MOHAMAD HAFID', 'phone' => '6281285229613', 'display' => '0812-8522-9613'],
                                ['name' => 'ADZAN RAMADHAN', 'phone' => '6285157973661', 'display' => '0851-5797-3661'],
                            ],
                            'penjelasan' =>
                                'Diberikan kepada individu yang memiliki dedikasi dan berkontribusi dalam pendirian dan pengembangan pendidikan nonformal guna memperluas akses pembelajaran dan meningkatkan kesempatan belajar bagi berbagai kelompok masyarakat.',
                            'pos' => 'center 40%',
                        ],
                        [
                            'id' => 'kesehatan',
                            'title' => 'Bidang Kesehatan',
                            'desc' => 'Kategori Inovator Teknologi Kesehatan',
                            'img' => asset('images/kat-kesehatan.png'),
                            'cp' => [
                                [
                                    'name' => 'SENDRI SANRISAGI',
                                    'phone' => '6283870745443',
                                    'display' => '0838-7074-5443',
                                ],
                                [
                                    'name' => 'DONY DWI PRABOWO',
                                    'phone' => '6281225677965',
                                    'display' => '0812-2567-7965',
                                ],
                            ],
                            'penjelasan' =>
                                'Diberikan kepada individu, profesional atau akademisi yang menghasilkan inovasi di bidang kesehatan melalui riset klinis maupun laboratorium, termasuk penemuan obat, terapi, atau alat kesehatan yang memberikan manfaat bagi masyarakat.',
                            'pos' => 'center 40%',
                        ],
                        [
                            'id' => 'pangan',
                            'title' => 'Bidang Ketahanan Pangan',
                            'desc' => 'Kategori Penggerak Desa Mandiri Pangan',
                            'img' => asset('images/kat-pangan.png'),
                            'cp' => [
                                [
                                    'name' => 'PUGO SURYA ADHITAMA',
                                    'phone' => '628567009410',
                                    'display' => '0856-7009-410',
                                ],
                                [
                                    'name' => 'NOVA AULIA FADJAR',
                                    'phone' => '6281808880109',
                                    'display' => '0818-0888-0109',
                                ],
                                // [
                                //     'name' => 'MUHAMMAD RAMDHANI',
                                //     'phone' => '6285697571514',
                                //     'display' => '0856-9757-1514',
                                // ],
                            ],
                            'penjelasan' =>
                                'Diberikan kepada individu yang menjadi penggerak dalam membangun desa mandiri pangan melalui pemanfaatan potensi pangan lokal, pemberdayaan masyarakat, dan penguatan ketahanan pangan secara berkelanjutan yang memberikan manfaat bagi masyarakat desa.',
                            'pos' => 'center 15%',
                        ],
                        [
                            'id' => 'budaya',
                            'title' => 'Bidang Seni dan Budaya',
                            'desc' => 'Kategori Pelestari Budaya Daerah',
                            'img' => asset('images/kat-budaya.png'),
                            'cp' => [
                                ['name' => 'NURUL HUSNA', 'phone' => '6281285003000', 'display' => '0812-8500-3000'],
                                [
                                    'name' => 'BAGAS MAULANA HASAN',
                                    'phone' => '6281270643734',
                                    'display' => '0812-7064-3734',
                                ],
                            ],
                            'penjelasan' =>
                                'Diberikan kepada individu yang memiliki inovasi dalam pengembangan dan pelestarian budaya daerah serta berperan aktif dalam mempromosikan budaya daerah di tingkat nasional maupun internasional.',
                            'pos' => 'center 40%',
                        ],
                    ];
                @endphp

                @foreach ($categories as $index => $cat)
                    <div @click="activeCat = {{ json_encode($cat) }}; modalOpen = true" x-data="{ shown: false }"
                        x-intersect="shown = true" x-intersect:leave="shown = false"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="group relative rounded-[24px] overflow-hidden aspect-[4/3] sm:aspect-[16/9] md:aspect-[1/0.8] lg:aspect-[3/4] flex flex-col justify-end cursor-pointer border-2 border-transparent hover:border-[#88c445] hover:shadow-[0_12px_40px_rgba(136,196,69,0.25)] transition-all duration-300"
                        style="transition-delay: {{ $index * 100 }}ms;">

                        <img src="{{ $cat['img'] }}" alt=""
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-black/5 via-[#0a0c11]/55 to-[#0a0c11]/95 transition-opacity duration-300 group-hover:opacity-90">
                        </div>

                        <div
                            class="absolute inset-0 border-[3px] border-[#88c445] rounded-[22px] z-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                        <div
                            class="absolute top-5 right-5 z-40 w-10 h-10 rounded-full bg-[#88c445] flex items-center justify-center opacity-0 translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 ease-out shadow-[0_4px_14px_rgba(136,196,69,0.6)]">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0a0c11" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </div>

                        <div class="relative z-20 p-[26px]">
                            <h3 class="cz text-[20px] sm:text-[28px] font-bold text-white mb-2 leading-tight">
                                {{ $cat['title'] }}
                            </h3>
                            <p class="text-white/80 text-[12px] sm:text-[14px] leading-[1.55] max-w-full sm:max-w-[80%]">
                                {{ $cat['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Modal Detail Kategori -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center px-4"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-[#0a0c11]/80 backdrop-blur-sm transition-opacity" @click="modalOpen = false">
            </div>

            <!-- Modal Panel -->
            <div x-show="modalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.4)] max-w-3xl w-full flex flex-col max-h-[95vh]">

                <!-- Modal Header Image -->
                <div class="relative h-[200px] sm:h-[220px] md:h-[180px] w-full shrink-0">
                    <img :src="activeCat?.img" alt="" class="w-full h-full object-cover"
                        :style="`object-position: ${activeCat?.pos || 'center'}`">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <button @click="modalOpen = false" aria-label="Tutup"
                        class="absolute top-4 right-4 w-10 h-10 bg-black/40 hover:bg-black/70 backdrop-blur text-white rounded-full flex items-center justify-center transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <div class="absolute bottom-8 sm:bottom-10 left-6 right-0 pr-[180px] sm:pr-[220px]">
                        <h3 class="cz text-[28px] sm:text-[36px] font-bold text-white leading-tight"
                            x-text="activeCat?.title"></h3>
                        <p class="text-[#88c445] font-semibold text-[14px] sm:text-[15px] mt-2 sm:mt-1 leading-snug"
                            x-text="activeCat?.desc"></p>
                    </div>
                    <a :href="'{{ route('nominasi') }}?kategori=' + activeCat?.id"
                        class="absolute -bottom-5 right-6 sm:right-8 z-20 inline-flex items-center justify-center gap-2 bg-gradient-to-br from-[#1b6e4c] to-[#259b6b] text-white font-bold text-[14px] px-6 py-2.5 rounded-full shadow-[0_8px_20px_rgba(27,110,76,0.25)] hover:scale-105 transition-transform">
                        Daftar Sekarang
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>

                <!-- Modal Body -->
                <div class="p-6 sm:p-8 overflow-y-auto">
                    <h3 class="text-[14px] font-bold text-[#b8860b] tracking-wider mb-2">PENJELASAN KATEGORI</h3>
                    <p class="text-[#4b5262] text-[15px] sm:text-[16px] leading-[1.7]" x-text="activeCat?.penjelasan">
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 sm:p-6 border-t border-gray-100 bg-gray-50 flex flex-col w-full gap-4 shrink-0">
                    <div class="w-full">
                        <span class="text-[#1da851] font-bold text-[12px] uppercase flex items-center gap-1.5 mb-1.5">
                            Contact Person
                        </span>
                        <div class="flex flex-col sm:flex-row gap-2 w-full"
                            :class="activeCat?.cp?.length < 3 ? 'flex-wrap' : ''">
                            <template x-for="(cp, i) in activeCat?.cp" :key="i">
                                <div :class="activeCat?.cp?.length < 3 ? 'grow' : 'flex-1 min-w-0'">
                                    <template x-if="cp.phone">
                                        <a :href="'https://wa.me/' + cp.phone + '?text=' + encodeURIComponent('Halo, Saya ingin bertanya tentang DPDRI AWARDS pada ' + activeCat?.title + ' - ' + activeCat?.desc)"
                                            target="_blank"
                                            class="w-full h-full justify-center px-3 py-2 bg-[#25d366]/10 hover:bg-[#25d366]/20 transition-colors rounded-xl border border-[#25d366]/30 flex items-center gap-2 cursor-pointer group"
                                            :class="activeCat?.cp?.length < 3 ? 'whitespace-nowrap' : ''">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#25d366"
                                                class="shrink-0 group-hover:scale-110 transition-transform">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                            </svg>
                                            <!-- Logic <= 2 CP -->
                                            <div x-show="activeCat?.cp?.length < 3"
                                                class="text-[#10131a] font-medium text-[12px] leading-relaxed truncate"
                                                x-text="cp.name + ' (' + cp.display + ')'"></div>

                                            <!-- Logic >= 3 CP -->
                                            <div x-show="activeCat?.cp?.length >= 3" class="sm:w-full" x-cloak>
                                                <!-- Mobile: Sama persis dengan < 3 CP -->
                                                <div class="sm:hidden text-[#10131a] font-medium text-[12px] leading-relaxed truncate"
                                                    x-text="cp.name + ' (' + cp.display + ')'"></div>
                                                <!-- Desktop: 2 Baris -->
                                                <div
                                                    class="hidden sm:flex text-[#10131a] font-semibold text-[11.5px] leading-tight flex-col items-center w-full overflow-hidden">
                                                    <span x-text="cp.name" class="w-full truncate text-center"></span>
                                                    <span x-text="'(' + cp.display + ')'"
                                                        class="shrink-0 text-[#10131a] text-center"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                    <template x-if="!cp.phone">
                                        <div class="w-full h-full justify-center px-3 py-2 bg-gray-100/50 rounded-xl border border-gray-200 flex items-center gap-2"
                                            :class="activeCat?.cp?.length < 3 ? 'whitespace-nowrap' : ''">

                                            <!-- Logic <= 2 CP -->
                                            <div x-show="activeCat?.cp?.length < 3"
                                                class="text-[#64748b] font-medium text-[12px] leading-relaxed truncate"
                                                x-text="cp.name + ' (Belum ada nomor)'"></div>

                                            <!-- Logic >= 3 CP -->
                                            <div x-show="activeCat?.cp?.length >= 3" class="sm:w-full" x-cloak>
                                                <!-- Mobile: Sama persis dengan < 3 CP -->
                                                <div class="sm:hidden text-[#64748b] font-medium text-[12px] leading-relaxed truncate"
                                                    x-text="cp.name + ' (Belum ada nomor)'"></div>
                                                <!-- Desktop: 2 Baris -->
                                                <div
                                                    class="hidden sm:flex text-[#64748b] font-semibold text-[11.5px] leading-tight flex-col items-center w-full overflow-hidden">
                                                    <span x-text="cp.name" class="w-full truncate text-center"></span>
                                                    <span class="shrink-0 text-center">(Belum ada nomor)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- 4. KETENTUAN UMUM & ALUR PENDAFTARAN -->
    <section id="syarat" class="py-[110px] px-6 bg-gradient-to-br from-[#0c3b28] to-[#0a0c11]">
        <div class="max-w-[1100px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_1.2fr] gap-14 items-start">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[30px]'">
                <span class="text-[#88c445] text-[12.5px] font-extrabold tracking-[0.22em]">PERLU DIPERHATIKAN</span>
                <h2 class="cz text-[clamp(36px,5vw,56px)] font-extrabold uppercase mt-3 leading-[1.02] text-white">
                    Ketentuan Umum dan <span class="text-[#e0b53c]">Alur Pendaftaran</span></h2>
                <p class="text-white/70 text-[17px] leading-[1.65] mt-5">Pastikan Anda memenuhi kriteria berikut.
                    <br class="hidden md:block">Seluruh proses pendaftaran dilakukan <br class="hidden md:block">
                    secara transparan dan terbuka untuk umum.
                </p>
                <a href="#kategori"
                    class="group relative overflow-hidden inline-flex items-center gap-2.5 mt-[30px] bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[15px] px-[30px] py-[15px] rounded-full shadow-[0_10px_34px_rgba(224,181,60,0.3)] hover:-translate-y-1 transition-transform">
                    <span
                        class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-[150%] group-hover:animate-[sheen_1.5s_infinite]"></span>
                    <span class="relative z-10">Daftar Sekarang</span>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="relative z-10 group-hover:translate-x-1 transition-transform">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </div>

            <div class="flex flex-col gap-3.5">
                @php
                    $reqs = [
                        [
                            'title' => 'Warga Negara Indonesia',
                            'desc' => 'Nominee adalah WNI yang berdomisili dan berkontribusi di wilayah Indonesia.',
                        ],
                        [
                            'title' => 'Mengisi Form Pendaftaran',
                            'desc' => 'Peserta mengisi formulir pendaftaran dengan benar.',
                        ],
                        [
                            'title' => 'Memiliki Karya dan Dampak Nyata',
                            'desc' =>
                                'Memiliki rekam jejak, inovasi, atau karya berdampak positif dalam salah satu dari 4 kategori.',
                        ],
                        [
                            'title' => 'Melengkapi Dokumen',
                            'desc' => 'Melampirkan KTP, portofolio/dokumentasi kegiatan, dan foto pendukung.',
                        ],
                    ];
                @endphp
                @foreach ($reqs as $index => $r)
                    <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                        class="group flex gap-4 items-start bg-white/5 border border-[#e0b53c]/20 rounded-2xl p-5  hover:bg-white/10 hover:border-[#88c445]/50 hover:-translate-y-1 hover:shadow-[0_8px_30px_rgba(136,196,69,0.15)] transition-all duration-[400ms] ease-out"
                        :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[30px]'"
                        style="transition-delay: {{ $index * 100 }}ms;">
                        <div
                            class="shrink-0 w-[30px] h-[30px] rounded-lg bg-[#88c445]/15 flex items-center justify-center group-hover:bg-[#88c445] transition-colors duration-300">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                                class="text-[#88c445] group-hover:text-[#0a0c11] transition-colors duration-300">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-white text-base font-bold group-hover:text-[#e0b53c] transition-colors duration-300">
                                {{ $r['title'] }}
                            </h4>
                            <p class="text-white/60 text-[14px] leading-[1.55] mt-1">{{ $r['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. TIMELINE KEGIATAN -->
    <section id="alur" class="relative py-[90px] px-6 bg-[#f8f9fa] border-y border-black/5 overflow-hidden">
        <div class="max-w-[1200px] mx-auto relative z-10">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-[70px] transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">TAHAP DEMI TAHAP</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#0a3622] mt-3 leading-none">
                    Timeline <span class="text-[#e0b53c]">Kegiatan</span></h2>
                <p class="text-gray-600 text-[18px] leading-[1.6] mt-[18px]">Rangkaian tahapan penting menuju malam
                    penganugerahan DPDRI <i>AWARDS</i> 2026.</p>
            </div>

            @php
                $timeline = [
                    [
                        'n' => '1',
                        'title' => 'Pembukaan Pendaftaran',
                        'date' => config('laravolt.ui.timeline_pembukaan_pendaftaran'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#1b6e4c] via-[#124d34] to-[#0a3622] shadow-[inset_0_2px_5px_rgba(255,255,255,0.4),0_10px_20px_rgba(10,54,34,0.3)] text-white',
                    ],
                    [
                        'n' => '2',
                        'title' => 'Periode Pendaftaran',
                        'date' => config('laravolt.ui.timeline_periode_pendaftaran'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#2a7a50] via-[#1a5a3a] to-[#0d3f26] shadow-[inset_0_2px_5px_rgba(255,255,255,0.4),0_10px_20px_rgba(15,65,40,0.3)] text-white',
                    ],
                    [
                        'n' => '3',
                        'title' => 'Verifikasi dan Identifikasi Data',
                        'date' => config('laravolt.ui.timeline_verifikasi_identifikasi'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#3e8953] via-[#246740] to-[#12492a] shadow-[inset_0_2px_5px_rgba(255,255,255,0.5),0_10px_20px_rgba(20,75,45,0.3)] text-white',
                    ],
                    [
                        'n' => '4',
                        'title' => 'Penilaian Tahap 1',
                        'date' => config('laravolt.ui.timeline_penilaian_tahap_1'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#599955] via-[#337743] to-[#17542d] shadow-[inset_0_2px_5px_rgba(255,255,255,0.5),0_10px_20px_rgba(25,85,50,0.3)] text-white',
                    ],
                    [
                        'n' => '5',
                        'title' => 'Penilaian Tahap 2',
                        'date' => config('laravolt.ui.timeline_penilaian_tahap_2'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#7eab56] via-[#4b8941] to-[#20612c] shadow-[inset_0_2px_5px_rgba(255,255,255,0.6),0_10px_20px_rgba(35,100,55,0.3)] text-white',
                    ],
                    [
                        'n' => '6',
                        'title' => 'Penilaian Tahap 3',
                        'date' => config('laravolt.ui.timeline_penilaian_tahap_3'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#a7be53] via-[#6d9d3a] to-[#2c6e26] shadow-[inset_0_2px_5px_rgba(255,255,255,0.6),0_10px_20px_rgba(50,115,50,0.3)]  text-white',
                    ],
                    [
                        'n' => '7',
                        'title' => 'Wawancara',
                        'date' => config('laravolt.ui.timeline_wawancara'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#d4d24a] via-[#97b32d] to-[#407e1b] shadow-[inset_0_2px_5px_rgba(255,255,255,0.7),0_10px_20px_rgba(75,135,45,0.35)]  text-[#1a1405]',
                    ],
                    [
                        'n' => '8',
                        'title' => 'Malam Penganugerahan',
                        'date' => config('laravolt.ui.timeline_malam_penganugerahan'),
                        'color_class' =>
                            'bg-gradient-to-br from-[#fceabb] via-[#d4af37] to-[#8c6b14] shadow-[inset_0_2px_5px_rgba(255,255,255,0.8),0_10px_20px_rgba(212,175,55,0.4)] text-[#1a1405]',
                    ],
                ];
            @endphp

            <!-- Desktop Snake Timeline (Hidden on mobile/tablet) -->
            <div class="relative hidden lg:block w-full py-10 mt-10">
                <style>
                    @keyframes sparkle {

                        0%,
                        100% {
                            opacity: 0;
                            transform: scale(0.5);
                        }

                        50% {
                            opacity: 1;
                            transform: scale(1.2);
                        }
                    }

                    .sparkle-1 {
                        animation: sparkle 2s infinite ease-in-out;
                    }

                    .sparkle-2 {
                        animation: sparkle 2.5s infinite ease-in-out 0.5s;
                    }

                    .sparkle-3 {
                        animation: sparkle 1.8s infinite ease-in-out 1s;
                    }

                    .sparkle-4 {
                        animation: sparkle 2.2s infinite ease-in-out 1.5s;
                    }
                </style>
                <!-- Row 1 -->
                <div class="grid grid-cols-4 relative z-10">
                    <div
                        class="absolute top-[27px] left-[12.5%] right-[12.5%] border-t-[3px] border-dashed border-[#1b6e4c]/30 z-[-1]">
                    </div>
                    <div
                        class="absolute top-[27px] right-[0%] w-[12.5%] h-[calc(100%+60px)] border-t-[3px] border-r-[3px] border-b-[3px] border-dashed border-[#1b6e4c]/30 rounded-r-[150px] z-[-1]">
                    </div>
                    @foreach (array_slice($timeline, 0, 4) as $index => $step)
                        <div class="relative z-10 flex flex-col items-center px-4 group">
                            <!-- Circle -->
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-xl mb-6 transition-all duration-500 relative {{ $step['color_class'] }}">
                                <div
                                    class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none">
                                </div>
                                <span class="relative z-10">{{ $step['n'] }}</span>
                            </div>

                            <!-- Card -->
                            <div
                                class="rounded-2xl p-4 w-full flex-1 flex flex-col items-center justify-center min-h-[75px] transition-all duration-500 relative overflow-hidden group-hover:-translate-y-1 {{ $step['n'] == '8' ? 'bg-gradient-to-b from-[#1a4a34] to-[#0a2b1d] border border-[#d4af37]/30 border-t-[#d4af37]/60 border-b-black/40 shadow-[inset_0_1px_3px_rgba(212,175,55,0.3),0_10px_25px_rgba(212,175,55,0.2)] group-hover:shadow-[0_15px_30px_rgba(212,175,55,0.3)]' : 'bg-gradient-to-b from-[#124d34] to-[#0a3622] border border-[#1b6e4c]/30 border-t-[#228059]/50 border-b-black/40 shadow-[inset_0_1px_2px_rgba(255,255,255,0.1),0_10px_20px_rgba(10,54,34,0.15)] group-hover:shadow-[0_15px_25px_rgba(10,54,34,0.25)]' }}">

                                @if ($step['n'] == '8')
                                    <div
                                        class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-40 animate-pulse mix-blend-screen pointer-events-none">
                                    </div>
                                    <div
                                        class="absolute inset-0 z-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] group-hover:animate-[sheen_1.5s_infinite]">
                                    </div>

                                    <!-- Subtle Sprinkles -->
                                    <div
                                        class="absolute top-2 left-3 w-1 h-1 bg-[#e0b53c] rounded-full sparkle-1 shadow-[0_0_4px_#e0b53c]">
                                    </div>
                                    <div
                                        class="absolute bottom-3 right-4 w-1.5 h-1.5 bg-white rounded-full sparkle-2 shadow-[0_0_6px_#fff]">
                                    </div>
                                    <div
                                        class="absolute top-4 right-2 w-1 h-1 bg-[#f5da8b] rounded-full sparkle-3 shadow-[0_0_4px_#f5da8b]">
                                    </div>
                                    <div
                                        class="absolute bottom-2 left-6 w-1 h-1 bg-white rounded-full sparkle-4 shadow-[0_0_4px_#fff]">
                                    </div>
                                @endif

                                <h3
                                    class="font-semibold text-center tracking-wide text-[14.5px] leading-snug relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]' : 'text-white' }}">
                                    {{ $step['title'] }}
                                </h3>
                                @if (!empty($step['date']))
                                    <p
                                        class="text-center text-[12.5px] font-normal leading-tight mt-1 relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]/80' : 'text-white/80' }}">
                                        {{ $step['date'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-4 relative z-10 mt-[60px]">
                    <div
                        class="absolute top-[27px] left-[5%] right-[12.5%] border-t-[3px] border-dashed border-[#1b6e4c]/30 z-[-1]">
                    </div>

                    <!-- Arrow at the end (left side) -->
                    <div class="absolute top-[16px] left-[5%] -translate-x-[50%] text-[#1b6e4c]/50 z-[-1]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 18 4 12 10 6"></polyline>
                        </svg>
                    </div>

                    @foreach (array_reverse(array_slice($timeline, 4, 4)) as $index => $step)
                        <div class="relative z-10 flex flex-col items-center px-4 group">
                            <!-- Circle -->
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-xl mb-6 transition-all duration-500 relative {{ $step['color_class'] }}">
                                <div
                                    class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none">
                                </div>
                                <span class="relative z-10">{{ $step['n'] }}</span>
                            </div>

                            <!-- Card -->
                            <div
                                class="rounded-2xl p-4 w-full flex-1 flex flex-col items-center justify-center min-h-[75px] transition-all duration-500 relative overflow-hidden group-hover:-translate-y-1 {{ $step['n'] == '8' ? 'bg-gradient-to-b from-[#1a4a34] to-[#0a2b1d] border border-[#d4af37]/30 border-t-[#d4af37]/60 border-b-black/40 shadow-[inset_0_1px_3px_rgba(212,175,55,0.3),0_10px_25px_rgba(212,175,55,0.2)] group-hover:shadow-[0_15px_30px_rgba(212,175,55,0.3)]' : 'bg-gradient-to-b from-[#124d34] to-[#0a3622] border border-[#1b6e4c]/30 border-t-[#228059]/50 border-b-black/40 shadow-[inset_0_1px_2px_rgba(255,255,255,0.1),0_10px_20px_rgba(10,54,34,0.15)] group-hover:shadow-[0_15px_25px_rgba(10,54,34,0.25)]' }}">

                                @if ($step['n'] == '8')
                                    <div
                                        class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-40 animate-pulse mix-blend-screen pointer-events-none">
                                    </div>
                                    <div
                                        class="absolute inset-0 z-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] group-hover:animate-[sheen_1.5s_infinite]">
                                    </div>

                                    <!-- Subtle Sprinkles -->
                                    <div
                                        class="absolute top-2 left-3 w-1 h-1 bg-[#e0b53c] rounded-full sparkle-1 shadow-[0_0_4px_#e0b53c]">
                                    </div>
                                    <div
                                        class="absolute bottom-3 right-4 w-1.5 h-1.5 bg-white rounded-full sparkle-2 shadow-[0_0_6px_#fff]">
                                    </div>
                                    <div
                                        class="absolute top-4 right-2 w-1 h-1 bg-[#f5da8b] rounded-full sparkle-3 shadow-[0_0_4px_#f5da8b]">
                                    </div>
                                    <div
                                        class="absolute bottom-2 left-6 w-1 h-1 bg-white rounded-full sparkle-4 shadow-[0_0_4px_#fff]">
                                    </div>
                                @endif

                                <h3
                                    class="font-semibold text-center tracking-wide text-[14.5px] leading-snug relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]' : 'text-white' }}">
                                    {{ $step['title'] }}
                                </h3>
                                @if (!empty($step['date']))
                                    <p
                                        class="text-center text-[12.5px] font-normal leading-tight mt-1 relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]/80' : 'text-white/80' }}">
                                        {{ $step['date'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mobile & Tablet Timeline (Standard Vertical) -->
            <div class="relative block lg:hidden w-full mt-10 pl-2 sm:pl-4">
                <!--
                     pl-2 = 8px padding. Circle is w-14 (56px). Center is 8 + (56/2) = 36px.
                     sm:pl-4 = 16px padding. Center is 16 + 28 = 44px.
                -->
                <div
                    class="absolute left-[36px] sm:left-[44px] top-2 bottom-2 w-[3px] bg-gradient-to-b from-[#1b6e4c]/50 to-transparent">
                </div>

                @foreach ($timeline as $index => $step)
                    <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                        class="group relative flex items-start gap-5 sm:gap-7 mb-10 transition-all duration-[800ms] ease-out"
                        :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[30px]'"
                        style="transition-delay: {{ $index * 50 }}ms;">

                        <div
                            class="shrink-0 w-14 h-14 rounded-full flex items-center justify-center z-10 relative {{ $step['color_class'] }}">
                            <div
                                class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none">
                            </div>
                            <span class="font-bold text-[20px] relative z-10">{{ $step['n'] }}</span>
                        </div>

                        <div class="pt-1">
                            <h3
                                class="cz text-[18px] sm:text-[22px] font-bold tracking-wide transition-colors duration-300 leading-[1.3] mb-1 {{ $step['n'] == '8' ? 'text-[#d4af37]' : 'text-[#0a3622]' }}">
                                {{ $step['title'] }}
                            </h3>
                            @if (!empty($step['date']))
                                <p class="text-[14.5px] font-semibold text-[#1b6e4c] mb-2">{{ $step['date'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. LACAK PENDAFTARAN -->
    <section id="lacak" class="py-[90px] px-6 bg-white border-t border-gray-200 relative overflow-hidden">
        <div class="absolute inset-0 z-0 pointer-events-none">
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#1b6e4c]/5 rounded-full blur-[100px]">
            </div>
        </div>
        <div class="relative z-10 max-w-[700px] mx-auto text-center" x-data="tracking()" x-intersect="shown = true"
            x-intersect:leave="shown = false">
            <div class="transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">CEK STATUS</span>
                <h2 class="cz text-[clamp(32px,5vw,52px)] font-extrabold uppercase mt-3 leading-[1.1] text-[#0a3622]">
                    Lacak <span class="text-[#e0b53c]">Pendaftaran</span>
                </h2>
                <p class="text-[#4b5262] text-[16px] leading-[1.65] mt-4 mb-8">
                    Masukkan nomor registrasi untuk melihat status pendaftaran Anda.
                </p>

                <!-- Form Tracking -->
                <form @submit.prevent="cekStatus" class="relative group max-w-[500px] mx-auto">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-[#1b6e4c] to-[#e0b53c] rounded-2xl blur opacity-15 group-hover:opacity-25 transition duration-500">
                    </div>
                    <div
                        class="relative flex items-center bg-white border border-gray-200 rounded-2xl p-2 shadow-lg focus-within:border-[#e0b53c]/50 focus-within:ring-2 focus-within:ring-[#e0b53c]/20 transition-all">
                        <div class="pl-4 text-[#e0b53c]">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <input type="text" x-model="regId" placeholder="Contoh: DPD-BP26-1505900001" required
                            class="w-full bg-transparent border-none text-[#10131a] text-[15px] px-4 py-3 focus:outline-none placeholder:text-gray-400 uppercase">
                        <button type="submit" :disabled="isLoading"
                            class="shrink-0 bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-bold text-[14px] px-6 py-3 rounded-xl hover:scale-[1.05] hover:shadow-[0_4px_15px_rgba(224,181,60,0.4)] hover:brightness-110 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:hover:scale-100 disabled:hover:shadow-none cursor-pointer flex items-center gap-2">
                            <span x-show="!isLoading">Cek Status</span>
                            <span x-show="isLoading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-[#10131a]" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mencari...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Hasil Pencarian -->
                <div x-show="result" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-10 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                    class="mt-8 text-left bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-xl max-w-[500px] mx-auto transform">
                    <template x-if="success">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-[#1b6e4c]/10 flex items-center justify-center shrink-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1b6e4c"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-[#10131a] font-bold text-[18px]">Data Ditemukan</h4>
                                    <p class="text-[#1b6e4c] text-[13px] font-semibold tracking-wide uppercase"
                                        x-text="resultData?.nomor_registrasi"></p>
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-gray-100">
                                <div>
                                    <div class="text-gray-400 text-[12px] font-bold tracking-wider mb-1">NAMA</div>
                                    <div class="text-[#10131a] font-semibold text-[16px] cz" x-text="resultData?.nama">
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-[12px] font-bold tracking-wider mb-1">KATEGORI</div>
                                    <div class="text-[#4b5262] font-medium text-[15px]" x-text="resultData?.kategori">
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-400 text-[12px] font-bold tracking-wider mb-1">STATUS</div>
                                    <div
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#e0b53c]/10 border border-[#e0b53c]/20 mt-1">
                                        <div class="w-2 h-2 rounded-full bg-[#e0b53c] animate-pulse"></div>
                                        <span class="text-[#b8860b] font-bold text-[14px] uppercase"
                                            x-text="resultData?.status"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!success">
                        <div class="text-center py-4">
                            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e74c3c"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                            <h4 class="text-[#10131a] font-bold text-[18px] mb-2">Tidak Ditemukan</h4>
                            <p class="text-[#4b5262] text-[14px] leading-relaxed" x-text="message"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <script>
            function tracking() {
                return {
                    shown: false,
                    regId: '',
                    isLoading: false,
                    result: false,
                    success: false,
                    message: '',
                    resultData: null,

                    async cekStatus() {
                        if (!this.regId) return;
                        this.isLoading = true;
                        this.result = false;

                        try {
                            const res = await fetch('{{ route('track') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    reg_id: this.regId
                                })
                            });

                            const data = await res.json();

                            this.success = data.success;
                            if (data.success) {
                                this.resultData = data.data;
                                this.resultData.nomor_registrasi = this.regId;
                            } else {
                                this.message = data.message || 'Nomor registrasi tidak valid.';
                            }
                        } catch (err) {
                            this.success = false;
                            this.message =
                                'Permintaan terlalu banyak (Rate Limit). Silakan tunggu maksimal 1 menit untuk mencoba kembali.';
                        } finally {
                            this.isLoading = false;
                            this.result = true;
                        }
                    }
                }
            }
        </script>
    </section>

    <!-- 6. STATISTIK PENDAFTAR -->
    <section id="statistik" class="relative py-[110px] px-6 bg-[#0a0c11] overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2000&auto=format&fit=crop"
                alt="DPDRI AWARDS" loading="lazy" class="w-full h-full object-cover opacity-30 blur-[2px]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0a0c11]/80 to-[#0a0c11]"></div>
        </div>
        <div class="relative z-10 max-w-[1000px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-[60px] transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#88c445] text-[12.5px] font-extrabold tracking-[0.22em]">ANTUSIASME NASIONAL</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase text-white mt-3 leading-none">
                    Statistik <span class="text-[#e0b53c]">Pendaftar</span></h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                @php
                    $stats = [
                        [
                            'value' => $kategoriCounts['Bidang Pendidikan'] ?? 0,
                            'label' => 'Bidang Pendidikan',
                            'img' => asset('images/kat-pendidikan.png'),
                        ],
                        [
                            'value' => $kategoriCounts['Bidang Kesehatan'] ?? 0,
                            'label' => 'Bidang Kesehatan',
                            'img' => asset('images/kat-kesehatan.png'),
                        ],
                        [
                            'value' => $kategoriCounts['Bidang Ketahanan Pangan'] ?? 0,
                            'label' => 'Bidang Ketahanan Pangan',
                            'img' => asset('images/kat-pangan.png'),
                        ],
                        [
                            'value' => $kategoriCounts['Bidang Seni dan Budaya'] ?? 0,
                            'label' => 'Bidang Seni dan Budaya',
                            'img' => asset('images/kat-budaya.png'),
                        ],
                    ];
                @endphp
                @foreach ($stats as $index => $st)
                    <div x-data="counter({{ $st['value'] }})" x-intersect="startCount()"
                        class="relative overflow-hidden border border-[#e0b53c]/20 hover:border-[#88c445]/55 bg-gradient-to-br from-[#191d27] to-[#10131a] rounded-xl py-6 px-3 text-center transition-colors duration-300">

                        <!-- Background Image for statistics card -->
                        <img src="{{ $st['img'] }}" alt=""
                            class="absolute inset-0 w-full h-full object-cover opacity-15 pointer-events-none z-0">

                        <div class="relative z-10">
                            <div class="cz text-[clamp(32px,3vw,42px)] font-bold text-[#e0b53c] leading-none"
                                x-text="display">0
                            </div>
                            <p class="text-white/60 text-[12px] font-bold tracking-[0.1em] uppercase mt-3 px-2">
                                {{ $st['label'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <script>
            function counter(target) {
                return {
                    display: '0',
                    animationId: null,
                    startCount() {
                        if (this.animationId) cancelAnimationFrame(this.animationId);
                        this.display = '0';
                        let start = null;
                        const duration = 2000;
                        const fmt = new Intl.NumberFormat('id-ID');
                        const step = (ts) => {
                            if (!start) start = ts;
                            const progress = Math.min((ts - start) / duration, 1);
                            const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                            this.display = fmt.format(Math.floor(easeProgress * target));
                            if (progress < 1) this.animationId = requestAnimationFrame(step);
                            else this.display = fmt.format(target);
                        };
                        this.animationId = requestAnimationFrame(step);
                    }
                }
            }
        </script>
    </section>

    <!-- 7. QNA (FAQ) -->
    <section id="faq" class="py-[110px] px-6 bg-[#fbf7ee] text-[#10131a]">
        <div class="max-w-[820px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-14 transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#b8860b] text-[12.5px] font-extrabold tracking-[0.22em]">INFORMASI
                    PENDAFTARAN</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase mt-3 leading-none">Tanya <span
                        class="text-[#1b6e4c]">Jawab</span></h2>
            </div>

            <div class="flex flex-col gap-3.5">
                @php
                    $faqData = [
                        [
                            'q' => 'Siapa saja yang bisa dinominasikan?',
                            'a' =>
                                'Semua Warga Negara Indonesia (WNI) yang memiliki rekam jejak, inovasi, atau karya nyata yang berdampak positif bagi masyarakat di salah satu dari 4 kategori yang tersedia.',
                        ],
                        [
                            'q' => 'Apakah saya bisa menominasikan diri sendiri?',
                            'a' =>
                                'Bisa. Anda dapat mengusulkan diri sendiri selama memenuhi syarat dan ketentuan, serta melampirkan bukti portofolio atau dokumentasi kegiatan.',
                        ],
                        [
                            'q' => 'Apakah ada biaya pendaftaran?',
                            'a' =>
                                'Tidak. Seluruh proses pendaftaran dan nominasi DPDRI <i>AWARDS</i> tidak dipungut biaya sepeser pun. Hati-hati terhadap segala bentuk penipuan yang mengatasnamakan panitia.',
                        ],
                        [
                            'q' => 'Bagaimana proses penjurian dilakukan?',
                            'a' =>
                                'Setelah pendaftaran ditutup, tim kurator menyeleksi kelengkapan berkas. Kandidat yang lolos dinilai oleh Dewan Juri independen berdasarkan kriteria dampak sosial, inovasi, keberlanjutan, dan inspirasi.',
                        ],
                        [
                            'q' => 'Kapan pemenang diumumkan?',
                            'a' =>
                                'Pemenang untuk setiap kategori diumumkan pada Malam Penganugerahan DPDRI <i>AWARDS</i> 2026 yang disiarkan secara nasional.',
                        ],
                        [
                            'q' => 'Apakah pemenang tahun lalu bisa mendaftar lagi?',
                            'a' =>
                                'Tidak bisa. Pemenang DPDRI <i>AWARDS</i> sebelumnya tidak diperkenankan mendaftar kembali.',
                        ],
                    ];
                @endphp

                @foreach ($faqData as $index => $f)
                    <div x-data="{ shown: false, isHovered: false, isClicked: false, forceClose: false, get isOpen() { return this.isClicked || (this.isHovered && !this.forceClose); } }"
                        x-intersect="shown = true" x-intersect:leave="shown = false"
                        @mouseenter="isHovered = true; forceClose = false"
                        @mouseleave="isHovered = false; forceClose = false"
                        @click="isClicked = !isClicked; forceClose = !isClicked"
                        class="bg-white border border-[#e8e0cf] rounded-2xl overflow-hidden shadow-[0_2px_10px_rgba(11,42,91,0.04)] transition-all duration-[800ms] ease-out cursor-pointer"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        style="transition-delay: {{ $index * 100 }}ms;">
                        <div class="p-6 flex items-center justify-between gap-4">
                            <span class="cz text-[19px] font-semibold flex-1 transition-colors duration-300"
                                :class="(isHovered || isClicked) ? 'text-[#1b6e4c]' : 'text-[#10131a]'">{{ $f['q'] }}</span>
                            <span
                                class="w-[30px] h-[30px] shrink-0 rounded-full flex items-center justify-center transition-all duration-300"
                                :class="[(isHovered || isClicked) ? 'bg-[#1b6e4c]' : 'bg-[#f3ecdd]', isOpen ? 'rotate-180' : '']">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    :stroke="(isHovered || isClicked) ? '#ffffff' : '#b8860b'" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </div>
                        <div x-show="isOpen" x-collapse class="overflow-hidden">
                            <p class="px-6 pb-6 text-[#4b5262] text-[15.5px] leading-[1.7]">{!! $f['a'] !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. STATEMENT PENUTUP & CTA -->
    <section
        class="py-[80px] px-6 bg-gradient-to-br from-[#0c2619] via-[#16422b] to-[#05110a] border-t border-[#e0b53c]/20 relative overflow-hidden flex items-center min-h-[400px]">

        <!-- Metallic Reflection Layers -->
        <div
            class="absolute inset-0 bg-[linear-gradient(110deg,rgba(255,255,255,0.08)_0%,transparent_20%,rgba(0,0,0,0.6)_100%)] pointer-events-none">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_5%_10%,rgba(136,196,69,0.15),transparent_40%)] pointer-events-none">
        </div>
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_30%_30%,rgba(224,181,60,0.08),transparent_30%)] pointer-events-none">
        </div>

        <!-- Giant Golden Trophy (Background Watermark on Mobile, Left Image on Desktop) -->
        <div
            class="absolute top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2 md:left-[5%] md:translate-x-0 w-[500px] h-[500px] sm:w-[600px] sm:h-[600px] md:w-auto md:h-[140%] pointer-events-none select-none opacity-20 md:opacity-100 mix-blend-screen md:mix-blend-normal">
            <!-- This layer creates a massive soft golden glow that stretches to the right -->
            <div class="absolute inset-0 bg-[#e0b53c]/20 blur-[80px] rounded-full translate-x-[35%] hidden md:block">
            </div>

            <img src="{{ asset('images/logo.png') }}" alt="Piala Emas"
                class="relative z-10 w-full h-full md:w-auto object-contain brightness-75 md:brightness-100 contrast-80 saturate-80 drop-shadow-none md:drop-shadow-[10px_0_20px_rgba(255,215,0,0.6)]">
        </div>

        <!-- Content on the right -->
        <div class="relative max-w-8xl mx-auto w-full flex justify-center md:justify-end">
            <div class="w-full md:w-[70%] lg:w-[60%] text-center md:text-right" x-data="{ shown: false }"
                x-intersect.half="shown = true" x-intersect:leave="shown = false">
                <div :class="shown ? 'opacity-100 translate-x-0 md:-translate-x-10' :
                    'opacity-0 translate-x-[20px] md:translate-x-[40px]'"
                    class="transition-all duration-[1000ms] ease-out flex flex-col items-center md:items-end px-6 md:px-0">

                    <div class="relative py-4 md:pr-6 w-full">
                        <!-- Subtle Quote Marks -->
                        <div
                            class="absolute -top-6 right-0 md:-right-2 text-[#e0b53c]/15 text-[60px] md:text-[80px] font-serif leading-none select-none">
                            "</div>
                        <div
                            class="absolute -bottom-6 left-0 text-[#e0b53c]/15 text-[60px] font-serif leading-none select-none md:hidden rotate-180">
                            "</div>

                        <p
                            class="text-white/90 text-[15px] sm:text-[17px] md:text-[16px] leading-[1.8] font-medium mb-5 relative z-10 drop-shadow-md">
                            Melalui <span class="text-[#e0b53c] font-bold tracking-wide">DPDRI <i>AWARDS</i>
                                2026</span>, DPD RI menegaskan posisinya
                            sebagai mitra sejati daerah yang hadir <span
                                class="text-white font-bold bg-white/5 px-2 py-0.5 rounded">mendengar, mendorong, dan
                                mengapresiasi</span> kerja nyata di seluruh pelosok&nbsp;Nusantara.
                        </p>

                        <p
                            class="text-white/80 text-[15px] sm:text-[17px] md:text-[16px] leading-[1.8] relative z-10 italic drop-shadow-md">
                            Penghargaan ini diharapkan menjadi cermin keberhasilan sekaligus <span
                                class="text-[#88c445] font-bold">pemantik semangat</span> bagi seluruh tokoh dan
                            individu untuk
                            terus <span
                                class="text-white underline decoration-[#88c445] decoration-2 underline-offset-4">berinovasi,
                                berkolaborasi</span>, dan memberikan yang terbaik bagi <span
                                class="font-semibold text-white"> masyarakat dan daerahnya.</span>
                        </p>
                    </div>

                    <div class="mt-6 md:mt-4 relative z-10 md:pr-6">
                        <a href="{{ route('nominasi') }}"
                            class="inline-flex items-center gap-2 bg-[#e0b53c] text-[#0a0c11] font-bold text-[15px] tracking-wide px-7 py-3 rounded-full shadow-[0_8px_20px_rgba(224,181,60,0.2)] hover:shadow-[0_12px_25px_rgba(224,181,60,0.4)] hover:-translate-y-1 transition-all">
                            Daftar Sekarang
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 9. FOOTER -->
    <footer class="bg-black pt-10 pb-6 px-6 border-t border-[#e0b53c]/15">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left md:flex-1">
                <div class="flex items-center justify-center md:justify-start gap-3.5">
                    <img src="/images/logo.png" alt="Logo DPD" class="w-11 h-11 object-contain">
                    <div class="flex flex-col w-full text-left">
                        <span class="cz text-[22px] font-extrabold tracking-wide text-white leading-[1.1]">DPDRI <span
                                class="text-[#88c445]"><i>AWARDS</i></span> 2026</span>
                        <span class="text-white/40 text-[13px] tracking-wide">Dari Daerah untuk Indonesia</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center gap-4 md:flex-[2]">
                <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-3">
                    <a href="#kategori"
                        class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">Kategori</a>
                    <a href="#syarat"
                        class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">Ketentuan</a>
                    <a href="#faq" class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">Tanya
                        Jawab</a>
                </div>
                <div class="flex items-center gap-3.5">
                    <a href="#" aria-label="Instagram"
                        class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#88c445] hover:text-[#0a0c11] transition-all text-white/50">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                        </svg>
                    </a>
                    <a href="#" aria-label="YouTube"
                        class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#88c445] hover:text-[#0a0c11] transition-all text-white/50">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z" />
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="text-center md:text-right md:flex-1">
                <p class="text-white/40 text-[13px]">&copy; 2026 DPD RI. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Scroll to Top -->
    <div x-data="{ showScroll: false }" @scroll.window="showScroll = (window.pageYOffset > 500)">
        <button @click="customScrollToTop()" x-show="showScroll" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-12" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-12"
            class="cursor-pointer fixed bottom-8 right-8 z-50 w-11 h-11 rounded-[14px] bg-gradient-to-tr from-[#1b6e4c] to-[#259b6b] hover:scale-105 hover:shadow-[0_12px_24px_rgba(27,110,76,0.4)] text-white flex items-center justify-center shadow-[0_8px_16px_rgba(27,110,76,0.3)] transition-all duration-300">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
    </div>

    <script>
        function customScrollToTop() {
            const html = document.documentElement;
            // Hindari reflow lag dengan menggunakan style property secara langsung
            html.style.scrollBehavior = 'auto';

            const duration = 800; // 0.8s (lebih lambat)
            const startPosition = window.pageYOffset;
            let start = null;

            window.requestAnimationFrame(function step(timestamp) {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                const percentage = Math.min(progress / duration, 1);

                // Linear: Kecepatan sama di awal, tengah, dan akhir
                const ease = percentage;

                window.scrollTo(0, startPosition * (1 - ease));

                if (progress < duration) {
                    window.requestAnimationFrame(step);
                } else {
                    html.style.scrollBehavior = '';
                }
            });
        }
    </script>

</body>

</html>