<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DPD Award 2026</title>

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
        :class="scrolled ? 'bg-[#0a0c11]/90 backdrop-blur-md border-b border-[#e0b53c]/20 py-3.5' : 'bg-transparent py-6'"
        class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="#beranda"
                class="cz text-[26px] font-extrabold tracking-wide text-white whitespace-nowrap flex items-center gap-2">
                <img src="/images/logo.png" alt="Logo DPD" class="w-10 h-10 object-contain">
                <span>DPD <span class="text-[#88c445]">AWARD</span></span>
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
            class="lg:hidden absolute top-full left-0 right-0 bg-[#0a0c11] border-b border-[#e0b53c]/20 py-4 px-6 flex flex-col gap-4 shadow-xl">
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
                class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[14px] tracking-wide px-6 py-3 rounded-full text-center mt-2">DAFTAR
                SEKARANG</a>
        </div>
    </header>

    <!-- 1. BERANDA (HERO) -->
    <section id="beranda" class="relative min-h-screen flex items-center pt-[140px] pb-[90px] px-6 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt=""
                class="w-full h-full object-cover object-[35%_33%] md:object-[40%_33%]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0a0c11]/55 via-[#0a0c11]/72 to-[#0a0c11]"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-[#0a0c11]/35 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto w-full">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="inline-flex max-w-full items-center justify-center gap-2.5 px-4 sm:px-[18px] py-2 border border-[#e0b53c]/50 rounded-[20px] sm:rounded-full mb-[26px] transition-all duration-[800ms] ease-out">
                    <span class="w-[6px] h-[6px] shrink-0 rounded-full bg-[#e0b53c] shadow-[0_0_8px_#e0b53c]"></span>
                    <span
                        class="text-[#f5da8b] text-[10.5px] sm:text-[12.5px] font-bold tracking-[0.15em] sm:tracking-[0.24em] leading-normal text-center">PENGHARGAAN
                        NASIONAL &nbsp;&middot;&nbsp; DPD RI</span>
                </div>

                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="cz text-[clamp(52px,8vw,104px)] font-extrabold leading-[0.94] uppercase tracking-wide mb-6 max-w-[900px] transition-all duration-[800ms] ease-out delay-100">
                    <span class="text-white block">DPD Award</span>
                    <span
                        class="inline-block bg-[linear-gradient(100deg,#b8860b_0%,#f5da8b_28%,#fff7e6_46%,#e0b53c_62%,#9c6f16_100%)] bg-[length:200%_auto] text-transparent bg-clip-text animate-sheen">2026</span>
                </h1>

                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="text-white/90 text-[clamp(17px,2vw,21px)] leading-[1.65] max-w-[720px] mb-[38px] transition-all duration-[800ms] ease-out delay-200">
                    Mengapresiasi dedikasi dan kontribusi luar biasa dari individu-individu inspiratif di seluruh
                    pelosok Nusantara. DPD Award hadir untuk merayakan karya nyata demi kemajuan bangsa.
                </p>

                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                    class="flex flex-wrap gap-4 transition-all duration-[800ms] ease-out delay-300">
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

        <a href="#countdown"
            class="absolute bottom-[26px] left-1/2 -translate-x-1/2 z-10 text-white/40 flex flex-col items-center gap-1.5 hover:text-white/80 transition-colors cursor-pointer">
            <span class="text-[11px] tracking-[0.2em] font-semibold">GULIR</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                class="animate-floaty">
                <polyline points="6 9 12 15 18 9" />
            </svg>
        </a>
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
    <section id="kategori" class="py-[110px] px-6 bg-[#fbf7ee]">
        <div class="max-w-7xl mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-16 transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">EMPAT BIDANG
                    APRESIASI</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#10131a] mt-3 leading-none">
                    Kategori <span class="text-[#1b6e4c]">Penghargaan</span></h2>
                <p class="text-[#4b5262] text-[18px] leading-[1.6] max-w-[660px] mx-auto mt-[18px]">Pilih kategori yang
                    paling sesuai dengan dedikasi individu yang ingin Anda nominasikan. Klik untuk memilih, lalu
                    lanjutkan pendaftaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-[26px]">

                @php
                    $categories = [
                        ['id' => 'pendidikan', 'title' => 'Inovator Pendidikan Non Formal', 'desc' => 'Apresiasi bagi pendidik atau inovator yang memajukan kualitas pendidikan luar sekolah di daerah.', 'img' => asset('images/kat-pendidikan.png'), 'cp' => 'Bapak Dony (0812-xxxx-xxxx)'],
                        ['id' => 'kesehatan', 'title' => 'Inovator Teknologi Kesehatan', 'desc' => 'Bagi tenaga medis atau inovator yang menciptakan teknologi untuk meningkatkan kesehatan masyarakat luas.', 'img' => asset('images/kat-kesehatan.png'), 'cp' => 'Bapak Dani (0813-xxxx-xxxx)'],
                        ['id' => 'pangan', 'title' => 'Penggerak Desa Mandiri Pangan', 'desc' => 'Bagi pahlawan ketahanan pangan yang menginisiasi gerakan mandiri pangan dan pertanian berkelanjutan di desanya.', 'img' => asset('images/kat-pangan.png'), 'cp' => 'Bapak Demto (0811-xxxx-xxxx)'],
                        ['id' => 'budaya', 'title' => 'Pelestari Budaya Daerah', 'desc' => 'Bagi individu yang gigih melestarikan kesenian daerah, merawat kerukunan warga, atau memajukan budaya lokal.', 'img' => asset('images/kat-budaya.png'), 'cp' => 'Ibu Izi (0815-xxxx-xxxx)']
                    ];
                @endphp

                @foreach($categories as $index => $cat)
                    <a href="{{ route('nominasi') }}?kategori={{ $cat['id'] }}" x-data="{ shown: false }"
                        x-intersect="shown = true" x-intersect:leave="shown = false"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        class="group relative rounded-[24px] overflow-hidden aspect-[4/3] sm:aspect-[16/9] md:aspect-[1/0.8] lg:aspect-[16/10] flex flex-col justify-end cursor-pointer border-2 border-transparent hover:border-[#88c445] hover:shadow-[0_12px_40px_rgba(136,196,69,0.25)] transition-all duration-300"
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
                            <p
                                class="text-white/80 text-[12px] sm:text-[14px] leading-[1.55] mb-4 max-w-full sm:max-w-[80%]">
                                {{ $cat['desc'] }}
                            </p>
                            <div
                                class="inline-flex items-center gap-2 bg-[#e0b53c]/20 border border-[#e0b53c]/50 text-[#f5da8b] px-3 py-1.5 rounded-lg text-[13px] font-semibold">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                CP: {{ $cat['cp'] }}
                            </div>
                        </div>
                    </a>
                @endforeach
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
                        ['title' => 'Mengisi Form Pendaftaran', 'desc' => 'Wajib mengunduh dan mengisi formulir pendaftaran dengan benar.'],
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
    <section id="alur" class="py-[110px] px-6 bg-white">
        <div class="max-w-[900px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                class="text-center mb-[70px] transition-all duration-[800ms] ease-out"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#b8860b] text-[12.5px] font-extrabold tracking-[0.22em]">TAHAP DEMI TAHAP</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#10131a] mt-3 leading-none">
                    Timeline <span class="text-[#1b6e4c]">Kegiatan</span></h2>
                <p class="text-[#4b5262] text-[18px] leading-[1.6] mt-[18px]">Rangkaian tahapan penting menuju malam
                    penganugerahan DPD Award 2026.</p>
            </div>

            <div class="relative text-[#10131a] pl-2 md:pl-0">
                <div
                    class="absolute left-[35px] md:left-[27px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-[#e0b53c] to-[#88c445]/40">
                </div>

                @php
                    $timeline = [
                        ['n' => '1', 'title' => 'Pembukaan pendaftaran', 'dot' => '#406010ff', 'num' => '#fff'],
                        ['n' => '2', 'title' => 'Periode Pendaftaran', 'dot' => '#50721fff', 'num' => '#fff'],
                        ['n' => '3', 'title' => 'Verifikasi dan Identifikasi Data', 'dot' => '#658f28ff', 'num' => '#fff'],
                        ['n' => '4', 'title' => 'Penilaian tahap 1', 'dot' => '#7cb137ff', 'num' => '#fff'],
                        ['n' => '5', 'title' => 'Penilaian tahap 2', 'dot' => '#8cc043ff', 'num' => '#fff'],
                        ['n' => '6', 'title' => 'Wawancara', 'dot' => '#9ad44bff', 'num' => '#fff'],
                        ['n' => '7', 'title' => 'Malam Penganugerahan', 'dot' => 'linear-gradient(135deg,#d6d45bff, #cac714ff)', 'num' => '#fff'],
                    ];
                @endphp

                @foreach($timeline as $index => $step)
                    <div x-data="{ shown: false }" x-intersect="shown = true" x-intersect:leave="shown = false"
                        class="group relative flex gap-5 md:gap-7 pb-11 transition-all duration-[800ms] ease-out"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        style="transition-delay: {{ $index * 80 }}ms;">
                        <div class="shrink-0 w-14 h-14 rounded-full border-[3px] border-white shadow-[0_6px_18px_rgba(0,0,0,0.15)] flex items-center justify-center z-10 cz font-bold text-xl group-hover:scale-[1.15] group-hover:shadow-[0_8px_26px_rgba(136,196,69,0.4)] transition-all duration-300"
                            style="background: {{ $step['dot'] }}; color: {{ $step['num'] }};">{{ $step['n'] }}</div>
                        <div class="pt-3 group-hover:-translate-y-1 transition-transform duration-300">
                            <h3
                                class="cz text-[22px] md:text-[26px] font-bold group-hover:text-[#1b6e4c] transition-colors duration-300">
                                {{ $step['title'] }}
                            </h3>
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
                        ['q' => 'Apakah saya bisa menominasikan diri sendiri?', 'a' => 'Bisa. Anda dapat mengusulkan diri sendiri atau orang lain (individu/komunitas) selama memenuhi syarat dan ketentuan, serta melampirkan bukti portofolio atau dokumentasi kegiatan.'],
                        ['q' => 'Apakah ada biaya pendaftaran?', 'a' => 'Tidak. Seluruh proses pendaftaran dan nominasi DPD Award tidak dipungut biaya sepeser pun. Hati-hati terhadap segala bentuk penipuan yang mengatasnamakan panitia.'],
                        ['q' => 'Bagaimana proses penjurian dilakukan?', 'a' => 'Setelah pendaftaran ditutup, tim kurator menyeleksi kelengkapan berkas. Kandidat yang lolos dinilai oleh Dewan Juri independen berdasarkan kriteria dampak sosial, inovasi, keberlanjutan, dan inspirasi.'],
                        ['q' => 'Kapan pemenang diumumkan?', 'a' => 'Pemenang untuk setiap kategori diumumkan pada Malam Penganugerahan DPD Award 2026 yang disiarkan secara nasional.']
                    ];
                @endphp

                @foreach($faqData as $index => $f)
                    <div x-data="{ shown: false, isHovered: false, isClicked: false }" x-intersect="shown = true"
                        x-intersect:leave="shown = false" @mouseenter="isHovered = true" @mouseleave="isHovered = false"
                        @click="isClicked = !isClicked"
                        class="bg-white border border-[#e8e0cf] rounded-2xl overflow-hidden shadow-[0_2px_10px_rgba(11,42,91,0.04)] transition-all duration-[800ms] ease-out cursor-pointer"
                        :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                        style="transition-delay: {{ $index * 100 }}ms;">
                        <div class="p-6 flex items-center justify-between gap-4">
                            <span class="cz text-[19px] font-semibold flex-1 transition-colors duration-300"
                                :class="(isHovered || isClicked) ? 'text-[#1b6e4c]' : 'text-[#10131a]'">{{ $f['q'] }}</span>
                            <span
                                class="w-[30px] h-[30px] shrink-0 rounded-full flex items-center justify-center transition-all duration-300"
                                :class="(isHovered || isClicked) ? 'bg-[#1b6e4c] rotate-180' : 'bg-[#f3ecdd]'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    :stroke="(isHovered || isClicked) ? '#ffffff' : '#b8860b'" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </div>
                        <div x-show="isHovered || isClicked" x-collapse class="overflow-hidden">
                            <p class="px-6 pb-6 text-[#4b5262] text-[15.5px] leading-[1.7]">{{ $f['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. FOOTER (PENUTUP) -->
    <footer class="bg-black pt-10 pb-6 px-6 border-t border-[#e0b53c]/15">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left md:flex-1">
                <div
                    class="cz text-[30px] font-extrabold tracking-wide text-white flex items-center justify-center md:justify-start gap-2">
                    <img src="/images/logo.png" alt="Logo DPD" class="w-11 h-11 object-contain">
                    <span>DPD <span class="text-[#88c445]">AWARD</span></span>
                </div>
                <p class="text-white/40 text-[14px] mt-2">Dari Daerah untuk Indonesia</p>
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