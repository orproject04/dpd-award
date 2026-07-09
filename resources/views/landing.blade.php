<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DPDRI AWARDS 2026</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
    </style>
</head>

<body class="bg-[#050608] text-white antialiased selection:bg-[#88c445] selection:text-[#0a0c11]" x-data="{ 
          scrolled: false, 
          mobileMenuOpen: false
      }" @scroll.window="scrolled = (window.pageYOffset > 60)">

    <!-- HEADER -->
    <header
        :class="(scrolled || mobileMenuOpen) ? 'py-3.5' : 'py-5'"
        class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300">
        
        <div class="absolute inset-0 pointer-events-none transition-opacity duration-300"
             :class="(scrolled || mobileMenuOpen) ? 'opacity-70' : 'opacity-70'"
             style="-webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 90%); mask-image: linear-gradient(to bottom, black 60%, transparent 100%);">
             <div class="absolute inset-0 bg-[#0a0c11]/80 backdrop-blur-lg"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="#beranda"
                class="cz text-[26px] font-extrabold tracking-wide text-white whitespace-nowrap flex items-center gap-2">
                <img src="{{ asset('images/dpdlogo.png') }}" alt="Logo DPD RI" class="h-10 object-contain">
                <img src="{{ asset('images/setjenlogo.png') }}" alt="Logo Setjen DPD RI" class="h-10 object-contain">
            </a>

            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden text-white p-2 focus:outline-none cursor-pointer">
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

            <nav class="hidden lg:flex items-center gap-[34px]">
                <a href="#beranda"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">BERANDA</a>
                <a href="#kategori"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">KATEGORI</a>
                <a href="#syarat"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">KETENTUAN</a>
                <a href="#alur"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">TIMELINE</a>
                <a href="#statistik"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">STATISTIK</a>
                <a href="#faq"
                    class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">TANYA
                    JAWAB</a>
                <a href="{{ route('nominasi') }}"
                    class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[13.5px] tracking-wide px-6 py-2.5 rounded-full shadow-[0_8px_30px_rgba(224,181,60,0.28)] hover:scale-105 transition-transform">DAFTAR</a>
            </nav>
        </div>

        <div x-show="mobileMenuOpen" x-cloak x-transition
            class="lg:hidden absolute top-full left-0 right-0 bg-[#0a0c11]/95 backdrop-blur-xl border-b border-[#e0b53c]/20 py-4 px-6 flex flex-col gap-4 shadow-xl">
            <a href="#beranda" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">BERANDA</a>
            <a href="#kategori" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">KATEGORI</a>
            <a href="#syarat" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">KETENTUAN</a>
            <a href="#alur" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">TIMELINE</a>
            <a href="#statistik" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">STATISTIK</a>
            <a href="#faq" @click="mobileMenuOpen = false"
                class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">TANYA JAWAB</a>
            <a href="{{ route('nominasi') }}"
                class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[14px] tracking-wide px-6 py-3 rounded-full text-center mt-2">DAFTAR</a>
        </div>
    </header>

    <!-- 1. BERANDA (HERO) -->
    <section id="beranda" class="relative min-h-screen flex items-center pt-[140px] pb-[90px] px-6 overflow-hidden">
        <div class="absolute inset-0 z-0 bg-[#0a0c11]">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt=""
                class="w-full h-full object-cover object-[40%_center] md:object-center">
        </div>


        <!-- Full-Width Artistic Brush Stroke Background -->
        <div class="absolute bottom-0 left-0 w-full z-0 h-[250px] pointer-events-none overflow-visible">
            <!-- Base gradient to ensure the absolute bottom is solid black -->
            <div class="absolute bottom-0 w-full h-[150px] bg-gradient-to-t from-[#020305] to-transparent"></div>
            
            <!-- Layered blurred ellipses to create an organic, artistic brush stroke edge -->
            <div class="absolute bottom-[-60px] left-[-10%] w-[120%] h-[200px] bg-[#05070a]/95 blur-[35px] rounded-[100%] rotate-2"></div>
            <div class="absolute bottom-[-40px] left-[-5%] w-[110%] h-[180px] bg-black blur-[45px] rounded-[100%] -rotate-3"></div>
            <div class="absolute bottom-[-20px] left-[15%] w-[80%] h-[150px] bg-[#020305] blur-[30px] rounded-[100%] rotate-1"></div>
        </div>

        <div class="absolute bottom-[20px] sm:bottom-[30px] left-1/2 -translate-x-1/2 z-10 w-full flex items-center justify-center gap-6 md:gap-14 lg:gap-20">
            
            <svg width="0" height="0" style="position: absolute; width: 0; height: 0;" aria-hidden="true">
                <defs>
                    <linearGradient id="goldLaurel" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fceabb"/>
                        <stop offset="30%" stop-color="#f8b500"/>
                        <stop offset="70%" stop-color="#b8860b"/>
                        <stop offset="100%" stop-color="#8c6b14"/>
                    </linearGradient>
                    <g id="laurelLeaf">
                        <path d="M 0 0 C 8 -12 20 -10 25 -2 C 16 4 8 6 0 0 Z" fill="url(#goldLaurel)"/>
                        <path d="M 0 0 Q 12 -4 23 -2" stroke="#604000" stroke-width="0.75" fill="none"/>
                        <path d="M 0 0 C 8 -12 20 -10 25 -2" stroke="#fceabb" stroke-width="0.5" fill="none" opacity="0.6"/>
                    </g>
                    <g id="laurelBranch" transform="translate(0, 5)">
                        <!-- Stem -->
                        <path d="M 5 35 Q 75 70 140 10" stroke="url(#goldLaurel)" stroke-width="2" stroke-linecap="round"/>
                        
                        <!-- Top side Leaves -->
                        <use href="#laurelLeaf" transform="translate(20, 41) rotate(-35) scale(0.8)"/>
                        <use href="#laurelLeaf" transform="translate(45, 48) rotate(-45) scale(1)"/>
                        <use href="#laurelLeaf" transform="translate(73, 48) rotate(-60) scale(1.1)"/>
                        <use href="#laurelLeaf" transform="translate(100, 40) rotate(-75) scale(1)"/>
                        <use href="#laurelLeaf" transform="translate(120, 27) rotate(-85) scale(0.8)"/>
                        
                        <!-- Bottom side Leaves -->
                        <use href="#laurelLeaf" transform="translate(28, 44) rotate(65) scale(0.8)"/>
                        <use href="#laurelLeaf" transform="translate(56, 49) rotate(55) scale(1)"/>
                        <use href="#laurelLeaf" transform="translate(85, 46) rotate(40) scale(1.1)"/>
                        <use href="#laurelLeaf" transform="translate(110, 34) rotate(25) scale(1)"/>
                        
                        <!-- Tip Leaf -->
                        <use href="#laurelLeaf" transform="translate(138, 10) rotate(-25) scale(0.9)"/>
                    </g>
                </defs>
            </svg>

            <!-- Left Laurel Separator -->
            <div class="hidden md:block w-[120px] lg:w-[160px] opacity-90 drop-shadow-[0_0_8px_rgba(224,181,60,0.5)]">
                <svg viewBox="0 -10 170 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#laurelBranch" />
                </svg>
            </div>

            <!-- Intertwined Trophies -->
            <div class="flex items-center justify-center shrink-0">
                <!-- 1. Far Left -->
                <div class="relative z-10 -mr-6 md:-mr-8 group" style="animation-delay: 0ms;">
                    <div class="absolute inset-0 bg-[#e0b53c]/40 blur-[15px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500"></div>
                    <div class="relative w-[85px] h-[85px] md:w-[60px] md:h-[60px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_8px_25px_rgba(0,0,0,0.8)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala" class="h-[80%] w-auto object-contain drop-shadow-[0_0_8px_rgba(224,181,60,0.4)]">
                    </div>
                </div>
                
                <!-- 2. Left Middle -->
                <div class="relative z-20 -mr-5 md:-mr-7 group" style="animation-delay: 200ms;">
                    <div class="absolute inset-0 bg-[#e0b53c]/40 blur-[20px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500"></div>
                    <div class="relative w-[110px] h-[110px] md:w-[85px] md:h-[85px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_10px_30px_rgba(0,0,0,0.9)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala" class="h-[80%] w-auto object-contain drop-shadow-[0_0_10px_rgba(224,181,60,0.5)]">
                    </div>
                </div>
                
                <!-- 3. Right Middle -->
                <div class="relative z-30 -mr-6 md:-mr-8 group" style="animation-delay: 400ms;">
                    <div class="absolute inset-0 bg-[#e0b53c]/40 blur-[20px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500"></div>
                    <div class="relative w-[110px] h-[110px] md:w-[85px] md:h-[85px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_10px_30px_rgba(0,0,0,0.9)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala" class="h-[80%] w-auto object-contain drop-shadow-[0_0_10px_rgba(224,181,60,0.5)]">
                    </div>
                </div>
                
                <!-- 4. Far Right -->
                <div class="relative z-10 group" style="animation-delay: 600ms;">
                    <div class="absolute inset-0 bg-[#e0b53c]/40 blur-[15px] rounded-full group-hover:bg-[#e0b53c]/60 transition-colors duration-500"></div>
                    <div class="relative w-[85px] h-[85px] md:w-[60px] md:h-[60px] bg-gradient-to-b from-[#1a1c23] to-[#0a0c11] border-[2.5px] border-[#e0b53c] rounded-full flex items-center justify-center shadow-[0_8px_25px_rgba(0,0,0,0.8)] group-hover:scale-105 transition-transform duration-500 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Piala" class="h-[80%] w-auto object-contain drop-shadow-[0_0_8px_rgba(224,181,60,0.4)]">
                    </div>
                </div>
            </div>
            
            <!-- Right Laurel Separator -->
            <div class="hidden md:block w-[120px] lg:w-[160px] opacity-90 drop-shadow-[0_0_8px_rgba(224,181,60,0.5)] scale-x-[-1]">
                <svg viewBox="0 -10 170 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#laurelBranch" />
                </svg>
            </div>

        </div>
    </section>

    <!-- 1.5 PEMBUKAAN -->
    <section class="py-8 px-6 bg-[#0a0c11] text-center border-b border-[#e0b53c]/15">
        <div class="mx-auto w-full">
        <div class="max-w-4xl mx-auto" x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false">
            <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                class="cz text-[clamp(28px,5vw,48px)] font-extrabold uppercase text-white mb-6 transition-all duration-[800ms] ease-out">
                DPDRI <span class="text-[#e0b53c]"><i>AWARDS</i> 2026</span>
            </h2>
            <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                class="text-white/80 text-[clamp(16px,2vw,20px)] leading-[1.7] transition-all duration-[800ms] ease-out delay-100">
                Mengapresiasi dedikasi dan kontribusi luar biasa dari individu-individu inspiratif di seluruh pelosok Nusantara. DPDRI <i>AWARDS</i> hadir untuk merayakan karya nyata demi kemajuan bangsa.
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

    <!-- 2. COUNTDOWN -->
    <section id="countdown"
        class="py-16 px-6 bg-gradient-to-b from-[#10131a] to-[#0a0c11] border-y border-[#e0b53c]/15">
        <div x-data="countdown()" x-init="start()" x-intersect="shown = true" x-intersect:leave="shown = false"
            class="max-w-[1000px] mx-auto bg-gradient-to-br from-[#191d27] to-[#10131a] border border-[#e0b53c]/30 rounded-[24px] sm:rounded-[28px] p-6 md:p-10 flex flex-col lg:flex-row items-center justify-between gap-8 shadow-[0_30px_70px_rgba(0,0,0,0.5)] transition-all duration-[800ms] ease-out text-center lg:text-left"
            :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
            <div>
                <span class="text-[#88c445] text-[11px] sm:text-xs font-bold tracking-[0.2em]">TENGGAT
                    PENDAFTARAN</span>
                <h2 class="cz text-white text-[clamp(26px,4vw,38px)] font-bold uppercase mt-2">Penutupan <span
                        class="text-[#e0b53c]">Nominasi</span></h2>
                <p class="text-white/55 mt-2 text-[14px] sm:text-[15px]">Waktu tersisa untuk mengirimkan usulan Anda</p>
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
                    shown: false,
                    days: '00', hours: '00', minutes: '00', seconds: '00',
                    endTime: new Date('2026-11-10T23:59:59').getTime(),

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
                <p class="text-[#4b5262] text-[18px] leading-[1.6] max-w-[660px] mx-auto mt-[18px]">Pilih kategori yang paling sesuai dengan prestasi dan inovasi yang Anda miliki. Klik untuk melanjutkan pendaftaran</p>
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
                                ['name' => 'ADZAN RAMADHAN', 'phone' => '6285157973661', 'display' => '0851-5797-3661']
                            ], 
                            'penjelasan' => 'Diberikan kepada individu yang memiliki dedikasi dan berkontribusi dalam pendirian dan pengembangan pendidikan nonformal guna memperluas akses pembelajaran dan meningkatkan kesempatan belajar bagi berbagai kelompok masyarakat.', 
                            'pos' => 'center 40%'
                        ],
                        [
                            'id' => 'kesehatan', 
                            'title' => 'Bidang Kesehatan', 
                            'desc' => 'Kategori Inovator Teknologi Kesehatan', 
                            'img' => asset('images/kat-kesehatan.png'), 
                            'cp' => [
                                ['name' => 'SENDRI SANRISAGI', 'phone' => '6283870745443', 'display' => '0838-7074-5443'],
                                ['name' => 'DONY DWI PRABOWO', 'phone' => '6281225677965', 'display' => '0812-2567-7965']
                            ], 
                            'penjelasan' => 'Diberikan kepada individu, profesional atau akademisi yang menghasilkan inovasi di bidang kesehatan melalui riset klinis maupun laboratorium, termasuk penemuan obat, terapi, atau alat kesehatan yang memberikan manfaat bagi masyarakat.', 
                            'pos' => 'center 40%'
                        ],
                        [
                            'id' => 'pangan', 
                            'title' => 'Bidang Ketahanan Pangan', 
                            'desc' => 'Kategori Penggerak Desa Mandiri Pangan', 
                            'img' => asset('images/kat-pangan.png'), 
                            'cp' => [
                                ['name' => 'NOVA AULIA FADJAR', 'phone' => '', 'display' => ''],
                                ['name' => 'PUGO SURYA ADHITAMA', 'phone' => '628567009410', 'display' => '0856-7009-410']
                            ], 
                            'penjelasan' => 'Diberikan kepada individu yang menjadi penggerak dalam membangun desa mandiri pangan melalui pemanfaatan potensi pangan lokal, pemberdayaan masyarakat, dan penguatan ketahanan pangan secara berkelanjutan yang memberikan manfaat bagi masyarakat desa.', 
                            'pos' => 'center 15%'
                        ],
                        [
                            'id' => 'budaya', 
                            'title' => 'Bidang Seni dan Budaya', 
                            'desc' => 'Kategori Pelestari Budaya Daerah', 
                            'img' => asset('images/kat-budaya.png'), 
                            'cp' => [
                                ['name' => 'NURUL HUSNA', 'phone' => '6281285003000', 'display' => '0812-8500-3000'],
                                ['name' => 'BAGAS MAULANA HASAN', 'phone' => '6281270643734', 'display' => '0812-7064-3734']
                            ], 
                            'penjelasan' => 'Diberikan kepada individu yang memiliki inovasi dalam pengembangan dan pelestarian budaya daerah serta berperan aktif dalam mempromosikan budaya daerah di tingkat nasional maupun internasional.', 
                            'pos' => 'center 40%'
                        ]
                    ];
                @endphp

                @foreach($categories as $index => $cat)
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
                class="relative bg-white rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.4)] max-w-2xl w-full flex flex-col max-h-[90vh]">

                <!-- Modal Header Image -->
                <div class="relative h-[200px] sm:h-[220px] md:h-[200px] w-full shrink-0">
                    <img :src="activeCat?.img" alt="" class="w-full h-full object-cover"
                        :style="`object-position: ${activeCat?.pos || 'center'}`">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <button @click="modalOpen = false"
                        class="absolute top-4 right-4 w-10 h-10 bg-black/40 hover:bg-black/70 backdrop-blur text-white rounded-full flex items-center justify-center transition-colors">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="cz text-[28px] sm:text-[36px] font-bold text-white leading-tight"
                            x-text="activeCat?.title"></h3>
                        <p class="text-[#88c445] font-semibold text-[14px] sm:text-[15px] mt-1"
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
                    <h4 class="text-[14px] font-bold text-[#b8860b] tracking-wider mb-2">PENJELASAN KATEGORI</h4>
                    <p class="text-[#4b5262] text-[15px] sm:text-[16px] leading-[1.7]" x-text="activeCat?.penjelasan">
                    </p>
                </div>

                <!-- Modal Footer -->
                <div
                    class="p-5 sm:p-6 border-t border-gray-100 bg-gray-50 flex flex-col-reverse sm:flex-row justify-between items-center gap-4 shrink-0">
                    <div class="w-full sm:w-auto">
                        <span class="text-[#1da851] font-bold text-[12px] uppercase flex items-center gap-1.5 mb-1.5">
                            Contact Person
                        </span>
                        <div class="flex flex-col sm:flex-row gap-2 w-full flex-wrap">
                            <template x-for="(cp, i) in activeCat?.cp" :key="i">
                                <div>
                                    <template x-if="cp.phone">
                                        <a :href="'https://wa.me/' + cp.phone + '?text=' + encodeURIComponent('Halo, Saya ingin bertanya tentang DPDRI Award pada ' + activeCat?.title + ' - ' + activeCat?.desc)" target="_blank" class="px-3 py-2 bg-[#25d366]/10 hover:bg-[#25d366]/20 transition-colors rounded-xl border border-[#25d366]/30 whitespace-nowrap flex items-center gap-2 cursor-pointer group">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#25d366" class="group-hover:scale-110 transition-transform">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                            </svg>
                                            <div class="text-[#10131a] font-medium text-[12px] leading-relaxed" x-text="cp.name + ' (' + cp.display + ')'"></div>
                                        </a>
                                    </template>
                                    <template x-if="!cp.phone">
                                        <div class="px-3 py-2 bg-gray-100/50 rounded-xl border border-gray-200 whitespace-nowrap flex items-center">
                                            <div class="text-[#64748b] font-medium text-[12px] leading-relaxed" x-text="cp.name + ' (Belum ada nomor)'"></div>
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
                <span class="text-[#88c445] text-[12.5px] font-extrabold tracking-[0.22em]">SEBELUM MENDAFTAR</span>
                <h2 class="cz text-[clamp(36px,5vw,56px)] font-extrabold uppercase mt-3 leading-[1.02] text-white">
                    Ketentuan Umum dan <span class="text-[#e0b53c]">Alur Pendaftaran</span></h2>
                <p class="text-white/70 text-[17px] leading-[1.65] mt-5">Pastikan Anda memenuhi kriteria berikut.
                    Seluruh proses pendaftaran dilakukan secara transparan dan terbuka untuk umum.</p>
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
                        ['title' => 'Warga Negara Indonesia', 'desc' => 'Nominee adalah WNI yang berdomisili dan berkontribusi di wilayah Indonesia.'],
                        ['title' => 'Mengisi Form Pendaftaran', 'desc' => 'Peserta mengisi formulir pendaftaran dengan benar.'],
                        ['title' => 'Memiliki Karya dan Dampak Nyata', 'desc' => 'Memiliki rekam jejak, inovasi, atau karya berdampak positif dalam salah satu dari 4 kategori.'],
                        ['title' => 'Melengkapi Dokumen', 'desc' => 'Melampirkan KTP, portofolio/dokumentasi kegiatan, dan foto pendukung.']
                    ];
                @endphp
                @foreach($reqs as $index => $r)
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
                <p class="text-gray-600 text-[18px] leading-[1.6] mt-[18px]">Rangkaian tahapan penting menuju malam penganugerahan DPDRI <i>AWARDS</i> 2026.</p>
            </div>

            @php
                $timeline = [
                    ['n' => '1', 'title' => 'Pembukaan Pendaftaran'],
                    ['n' => '2', 'title' => 'Periode Pendaftaran', 'date' => '13 Juli – 1 Agustus 2026'],
                    ['n' => '3', 'title' => 'Verifikasi dan Identifikasi Data'],
                    ['n' => '4', 'title' => 'Penilaian Tahap 1'],
                    ['n' => '5', 'title' => 'Penilaian Tahap 2'],
                    ['n' => '6', 'title' => 'Penilaian Tahap 3'],
                    ['n' => '7', 'title' => 'Wawancara'],
                    ['n' => '8', 'title' => 'Malam Penganugerahan'],
                ];
            @endphp

            <!-- Desktop Snake Timeline (Hidden on mobile/tablet) -->
            <div class="relative hidden lg:block w-full py-10 mt-10">
                <style>
                @keyframes sparkle {
                    0%, 100% { opacity: 0; transform: scale(0.5); }
                    50% { opacity: 1; transform: scale(1.2); }
                }
                .sparkle-1 { animation: sparkle 2s infinite ease-in-out; }
                .sparkle-2 { animation: sparkle 2.5s infinite ease-in-out 0.5s; }
                .sparkle-3 { animation: sparkle 1.8s infinite ease-in-out 1s; }
                .sparkle-4 { animation: sparkle 2.2s infinite ease-in-out 1.5s; }
                </style>
                <!-- Row 1 -->
                <div class="grid grid-cols-4 relative z-10">
                    <div class="absolute top-[27px] left-[12.5%] right-[12.5%] border-t-[3px] border-dashed border-[#1b6e4c]/30 z-[-1]"></div>
                    <div class="absolute top-[27px] right-[0%] w-[12.5%] h-[calc(100%+60px)] border-t-[3px] border-r-[3px] border-b-[3px] border-dashed border-[#1b6e4c]/30 rounded-r-[150px] z-[-1]"></div>
                    @foreach(array_slice($timeline, 0, 4) as $index => $step)
                        <div class="relative z-10 flex flex-col items-center px-4 group">
                            <!-- Circle -->
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-xl mb-6 transition-all duration-500 relative {{ $step['n'] == '8' ? 'bg-gradient-to-br from-[#fceabb] via-[#d4af37] to-[#8c6b14] shadow-[inset_0_2px_5px_rgba(255,255,255,0.8),0_10px_20px_rgba(212,175,55,0.4)] border-2 border-white/60 text-[#1a1405]' : 'bg-gradient-to-br from-[#1b6e4c] via-[#124d34] to-[#0a3622] shadow-[inset_0_2px_5px_rgba(255,255,255,0.4),0_10px_20px_rgba(10,54,34,0.3)] border-2 border-[#3bc48b]/40 text-white' }}">
                                <div class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none"></div>
                                <span class="relative z-10">{{ $step['n'] }}</span>
                            </div>
                            
                            <!-- Card -->
                            <div class="rounded-2xl p-4 w-full flex-1 flex flex-col items-center justify-center min-h-[75px] transition-all duration-500 relative overflow-hidden group-hover:-translate-y-1 {{ $step['n'] == '8' ? 'bg-gradient-to-b from-[#1a4a34] to-[#0a2b1d] border border-[#d4af37]/30 border-t-[#d4af37]/60 border-b-black/40 shadow-[inset_0_1px_3px_rgba(212,175,55,0.3),0_10px_25px_rgba(212,175,55,0.2)] group-hover:shadow-[0_15px_30px_rgba(212,175,55,0.3)]' : 'bg-gradient-to-b from-[#124d34] to-[#0a3622] border border-[#1b6e4c]/30 border-t-[#228059]/50 border-b-black/40 shadow-[inset_0_1px_2px_rgba(255,255,255,0.1),0_10px_20px_rgba(10,54,34,0.15)] group-hover:shadow-[0_15px_25px_rgba(10,54,34,0.25)]' }}">
                                
                                @if($step['n'] == '8')
                                    <div class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-40 animate-pulse mix-blend-screen pointer-events-none"></div>
                                    <div class="absolute inset-0 z-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] group-hover:animate-[sheen_1.5s_infinite]"></div>
                                    
                                    <!-- Subtle Sprinkles -->
                                    <div class="absolute top-2 left-3 w-1 h-1 bg-[#e0b53c] rounded-full sparkle-1 shadow-[0_0_4px_#e0b53c]"></div>
                                    <div class="absolute bottom-3 right-4 w-1.5 h-1.5 bg-white rounded-full sparkle-2 shadow-[0_0_6px_#fff]"></div>
                                    <div class="absolute top-4 right-2 w-1 h-1 bg-[#f5da8b] rounded-full sparkle-3 shadow-[0_0_4px_#f5da8b]"></div>
                                    <div class="absolute bottom-2 left-6 w-1 h-1 bg-white rounded-full sparkle-4 shadow-[0_0_4px_#fff]"></div>
                                @endif
                                
                                <h3 class="font-semibold text-center tracking-wide text-[14.5px] leading-snug relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]' : 'text-white' }}">{{ $step['title'] }}</h3>
                                @if(isset($step['date']))
                                    <p class="text-center text-[12.5px] font-normal leading-tight mt-1 relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]/80' : 'text-white/80' }}">{{ $step['date'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-4 relative z-10 mt-[60px]">
                    <div class="absolute top-[27px] left-[5%] right-[12.5%] border-t-[3px] border-dashed border-[#1b6e4c]/30 z-[-1]"></div>
                    
                    <!-- Arrow at the end (left side) -->
                    <div class="absolute top-[16px] left-[5%] -translate-x-[50%] text-[#1b6e4c]/50 z-[-1]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 18 4 12 10 6"></polyline>
                        </svg>
                    </div>
                    
                    @foreach(array_reverse(array_slice($timeline, 4, 4)) as $index => $step)
                        <div class="relative z-10 flex flex-col items-center px-4 group">
                            <!-- Circle -->
                            <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-xl mb-6 transition-all duration-500 relative {{ $step['n'] == '8' ? 'bg-gradient-to-br from-[#fceabb] via-[#d4af37] to-[#8c6b14] shadow-[inset_0_2px_5px_rgba(255,255,255,0.8),0_10px_20px_rgba(212,175,55,0.4)] text-[#1a1405]' : 'bg-gradient-to-br from-[#1b6e4c] via-[#124d34] to-[#0a3622] shadow-[inset_0_2px_5px_rgba(255,255,255,0.4),0_10px_20px_rgba(10,54,34,0.3)] border-2 border-[#3bc48b]/40 text-white' }}">
                                <div class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none"></div>
                                <span class="relative z-10">{{ $step['n'] }}</span>
                            </div>
                            
                            <!-- Card -->
                            <div class="rounded-2xl p-4 w-full flex-1 flex flex-col items-center justify-center min-h-[75px] transition-all duration-500 relative overflow-hidden group-hover:-translate-y-1 {{ $step['n'] == '8' ? 'bg-gradient-to-b from-[#1a4a34] to-[#0a2b1d] border border-[#d4af37]/30 border-t-[#d4af37]/60 border-b-black/40 shadow-[inset_0_1px_3px_rgba(212,175,55,0.3),0_10px_25px_rgba(212,175,55,0.2)] group-hover:shadow-[0_15px_30px_rgba(212,175,55,0.3)]' : 'bg-gradient-to-b from-[#124d34] to-[#0a3622] border border-[#1b6e4c]/30 border-t-[#228059]/50 border-b-black/40 shadow-[inset_0_1px_2px_rgba(255,255,255,0.1),0_10px_20px_rgba(10,54,34,0.15)] group-hover:shadow-[0_15px_25px_rgba(10,54,34,0.25)]' }}">
                                
                                @if($step['n'] == '8')
                                    <div class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-40 animate-pulse mix-blend-screen pointer-events-none"></div>
                                    <div class="absolute inset-0 z-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] group-hover:animate-[sheen_1.5s_infinite]"></div>
                                    
                                    <!-- Subtle Sprinkles -->
                                    <div class="absolute top-2 left-3 w-1 h-1 bg-[#e0b53c] rounded-full sparkle-1 shadow-[0_0_4px_#e0b53c]"></div>
                                    <div class="absolute bottom-3 right-4 w-1.5 h-1.5 bg-white rounded-full sparkle-2 shadow-[0_0_6px_#fff]"></div>
                                    <div class="absolute top-4 right-2 w-1 h-1 bg-[#f5da8b] rounded-full sparkle-3 shadow-[0_0_4px_#f5da8b]"></div>
                                    <div class="absolute bottom-2 left-6 w-1 h-1 bg-white rounded-full sparkle-4 shadow-[0_0_4px_#fff]"></div>
                                @endif
                                
                                <h3 class="font-semibold text-center tracking-wide text-[14.5px] leading-snug relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]' : 'text-white' }}">{{ $step['title'] }}</h3>
                                @if(isset($step['date']))
                                    <p class="text-center text-[12.5px] font-normal leading-tight mt-1 relative z-10 {{ $step['n'] == '8' ? 'text-[#fceabb]/80' : 'text-white/80' }}">{{ $step['date'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mobile & Tablet Timeline (Standard Vertical) -->
            <div class="relative block lg:hidden w-full mt-10 pl-2 sm:pl-4">
                <div class="absolute left-[28px] sm:left-[36px] top-2 bottom-2 w-[3px] bg-gradient-to-b from-[#1b6e4c]/50 to-transparent"></div>

                @foreach($timeline as $index => $step)
                    <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                        class="group relative flex items-start gap-5 sm:gap-7 mb-10 transition-all duration-[800ms] ease-out"
                        :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[30px]'"
                        style="transition-delay: {{ $index * 50 }}ms;">
                        
                        <div class="shrink-0 w-14 h-14 rounded-full flex items-center justify-center z-10 relative {{ $step['n'] == '8' ? 'bg-gradient-to-br from-[#fceabb] via-[#d4af37] to-[#8c6b14] shadow-[inset_0_2px_5px_rgba(255,255,255,0.8),0_10px_20px_rgba(212,175,55,0.4)] border-2 border-white/60 text-[#1a1405]' : 'bg-gradient-to-br from-[#1b6e4c] via-[#124d34] to-[#0a3622] shadow-[inset_0_2px_5px_rgba(255,255,255,0.4),0_10px_20px_rgba(10,54,34,0.3)] border-2 border-[#3bc48b]/40 text-white' }}">
                            <div class="absolute inset-0 z-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30 mix-blend-overlay rounded-full pointer-events-none"></div>
                            <span class="font-bold text-[20px] relative z-10">{{ $step['n'] }}</span>
                        </div>

                        <div class="pt-1">
                            <h3 class="cz text-[18px] sm:text-[22px] font-bold tracking-wide transition-colors duration-300 leading-[1.3] mb-1 {{ $step['n'] == '8' ? 'text-[#d4af37]' : 'text-[#0a3622]' }}">
                                {{ $step['title'] }}
                            </h3>
                            @if(isset($step['date']))
                                <p class="text-[14px] leading-relaxed {{ $step['n'] == '8' ? 'text-[#d4af37]/80' : 'text-gray-600' }}">{{ $step['date'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. STATISTIK PENDAFTAR -->
    <section id="statistik" class="relative py-[110px] px-6 bg-[#0a0c11] overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2000&auto=format&fit=crop" alt=""
                class="w-full h-full object-cover opacity-30 blur-[2px]">
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
                        ['value' => 1245, 'label' => 'Pendidikan Non Formal'],
                        ['value' => 982, 'label' => 'Teknologi Kesehatan'],
                        ['value' => 843, 'label' => 'Desa Mandiri Pangan'],
                        ['value' => 721, 'label' => 'Pelestari Budaya']
                    ];
                @endphp
                @foreach($stats as $index => $st)
                    <div x-data="counter({{ $st['value'] }})" x-intersect="startCount()"
                        class="border border-[#e0b53c]/20 hover:border-[#88c445]/55 bg-gradient-to-br from-[#191d27] to-[#10131a] rounded-xl py-6 px-3 text-center transition-colors duration-300">
                        <div class="cz text-[clamp(32px,3vw,42px)] font-bold text-[#e0b53c] leading-none" x-text="display">0
                        </div>
                        <p class="text-white/60 text-[12px] font-bold tracking-[0.1em] uppercase mt-3 px-2">
                            {{ $st['label'] }}
                        </p>
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
                <span class="text-[#b8860b] text-[12.5px] font-extrabold tracking-[0.22em]">INFORMASI PENDAFTARAN</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase mt-3 leading-none">Tanya <span
                        class="text-[#1b6e4c]">Jawab</span></h2>
            </div>

            <div class="flex flex-col gap-3.5">
                @php
                    $faqData = [
                        ['q' => 'Siapa saja yang bisa dinominasikan?', 'a' => 'Semua Warga Negara Indonesia (WNI) yang memiliki rekam jejak, inovasi, atau karya nyata yang berdampak positif bagi masyarakat di salah satu dari 4 kategori yang tersedia.'],
                        ['q' => 'Apakah saya bisa menominasikan diri sendiri?', 'a' => 'Bisa. Anda dapat mengusulkan diri sendiri selama memenuhi syarat dan ketentuan, serta melampirkan bukti portofolio atau dokumentasi kegiatan.'],
                        ['q' => 'Apakah ada biaya pendaftaran?', 'a' => 'Tidak. Seluruh proses pendaftaran dan nominasi DPDRI <i>AWARDS</i> tidak dipungut biaya sepeser pun. Hati-hati terhadap segala bentuk penipuan yang mengatasnamakan panitia.'],
                        ['q' => 'Bagaimana proses penjurian dilakukan?', 'a' => 'Setelah pendaftaran ditutup, tim kurator menyeleksi kelengkapan berkas. Kandidat yang lolos dinilai oleh Dewan Juri independen berdasarkan kriteria dampak sosial, inovasi, keberlanjutan, dan inspirasi.'],
                        ['q' => 'Kapan pemenang diumumkan?', 'a' => 'Pemenang untuk setiap kategori diumumkan pada Malam Penganugerahan DPDRI <i>AWARDS</i> 2026 yang disiarkan secara nasional.']
                    ];
                @endphp

                @foreach($faqData as $index => $f)
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
    <section class="py-[80px] px-6 bg-gradient-to-b from-[#193931] to-[#05160e] border-t border-white/15 relative overflow-hidden flex items-center min-h-[400px]">
        <!-- Giant Background Logo Overlay (Left) -->
        <div class="absolute top-1/2 -translate-y-1/2 -left-[40%] md:-left-[15%] lg:-left-[5%] h-[150%] md:h-[200%] opacity-30 pointer-events-none select-none mix-blend-multiply">
            <img src="{{ asset('images/logo.png') }}" alt="" class="w-auto h-full object-contain filter brightness-0">
        </div>
        
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(27,110,76,0.15),transparent)] pointer-events-none"></div>

        <!-- Content on the right -->
        <div class="relative max-w-7xl mx-auto w-full flex justify-end">
            <div class="w-full md:w-[70%] lg:w-[60%] text-right" x-data="{ shown: false }" x-intersect.half="shown = true" x-intersect:leave="shown = false">
                <div :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[40px]'"
                    class="transition-all duration-[1000ms] ease-out flex flex-col items-end">
                    
                    <div class="relative py-4 pr-6">
                        <!-- Subtle Quote Marks -->
                        <div class="absolute -top-6 -right-2 text-[#e0b53c]/15 text-[60px] md:text-[80px] font-serif leading-none select-none">"</div>
                        
                        <p class="text-white/90 text-[15px] md:text-[17px] leading-[1.8] font-medium mb-5 relative z-10">
                            Melalui <span class="text-[#e0b53c] font-bold tracking-wide">DPDRI <i>AWARDS</i> 2026</span>, DPD RI menegaskan posisinya
                            sebagai mitra sejati daerah yang hadir <span class="text-white font-bold bg-white/5 px-2 py-0.5 rounded">mendengar, mendorong, dan mengapresiasi</span> kerja nyata di seluruh pelosok Nusantara.
                        </p>
                        
                        <p class="text-white/80 text-[15px] md:text-[17px] leading-[1.8] relative z-10 italic">
                            Penghargaan ini diharapkan menjadi cermin keberhasilan sekaligus <span
                                class="text-[#88c445] font-bold not-italic">pemantik semangat</span> bagi seluruh tokoh dan individu untuk
                            terus <span class="text-white font-semibold not-italic underline decoration-[#88c445] decoration-2 underline-offset-4">berinovasi, berkolaborasi</span>, dan memberikan yang terbaik bagi masyarakat dan daerahnya.
                        </p>
                    </div>
                    
                    <div class="mt-4 relative z-10 pr-6">
                        <a href="{{ route('nominasi') }}"
                            class="inline-flex items-center gap-2 bg-[#e0b53c] text-[#0a0c11] font-bold text-[14px] tracking-wide px-7 py-3 rounded-full shadow-[0_8px_20px_rgba(224,181,60,0.2)] hover:shadow-[0_12px_25px_rgba(224,181,60,0.4)] hover:-translate-y-1 transition-all">
                            Daftar Sekarang
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                    <div class="flex flex-col text-left">
                        <span class="cz text-[28px] font-extrabold tracking-wide text-white leading-[1.1]">DPDRI <span
                                class="text-[#88c445]"><i>AWARDS</i></span></span>
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
                    <a href="#"
                        class="w-9 h-9 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#88c445] hover:text-[#0a0c11] transition-all text-white/50">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                        </svg>
                    </a>
                    <a href="#"
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