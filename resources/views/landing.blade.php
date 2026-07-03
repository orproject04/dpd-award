<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DPD Award 2026</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0c11; color: #10131a; }
        .cz { font-family: 'Cinzel', serif; }
        @keyframes sheen { 0% { background-position: 0% 50%; } 100% { background-position: 200% 50%; } }
        @keyframes floaty { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-sheen { animation: sheen 6s linear infinite; }
        .animate-floaty { animation: floaty 2s ease-in-out infinite; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#050608] text-white antialiased selection:bg-[#88c445] selection:text-[#0a0c11]" 
      x-data="{ 
          scrolled: false, 
          mobileMenuOpen: false,
          selectedCat: null,
          catNames: {
              'pendidikan': 'Pendidikan',
              'kesehatan': 'Kesehatan',
              'lingkungan': 'Lingkungan',
              'kepemudaan': 'Kepemudaan',
              'sosial': 'Sosial Budaya',
              'ekonomi': 'Ekonomi Kreatif'
          }
      }" 
      @scroll.window="scrolled = (window.pageYOffset > 60)">

    <!-- HEADER -->
    <header :class="scrolled ? 'bg-[#0a0c11]/90 backdrop-blur-md border-b border-[#e0b53c]/20 py-3.5' : 'bg-transparent py-6'"
        class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="#beranda" class="cz text-[26px] font-extrabold tracking-wide text-white whitespace-nowrap">
                DPD <span class="text-[#88c445]">AWARD</span>
            </a>
            
            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-white p-2 focus:outline-none">
                <svg x-show="!mobileMenuOpen" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg x-show="mobileMenuOpen" x-cloak width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-[34px]">
                <a href="#beranda" class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">BERANDA</a>
                <a href="#kategori" class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">KATEGORI</a>
                <a href="#alur" class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">ALUR</a>
                <a href="#statistik" class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">STATISTIK</a>
                <a href="#faq" class="text-white/80 hover:text-white text-[13.5px] font-semibold tracking-wider transition-colors">FAQ</a>
                <a href="{{ route('nominasi') }}" class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[13.5px] tracking-wide px-6 py-2.5 rounded-full shadow-[0_8px_30px_rgba(224,181,60,0.28)] hover:scale-105 transition-transform">DAFTAR</a>
            </nav>
        </div>

        <!-- Mobile Nav -->
        <div x-show="mobileMenuOpen" x-cloak x-transition class="lg:hidden absolute top-full left-0 right-0 bg-[#0a0c11] border-b border-[#e0b53c]/20 py-4 px-6 flex flex-col gap-4 shadow-xl">
            <a href="#beranda" @click="mobileMenuOpen = false" class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">BERANDA</a>
            <a href="#kategori" @click="mobileMenuOpen = false" class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">KATEGORI</a>
            <a href="#alur" @click="mobileMenuOpen = false" class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">ALUR</a>
            <a href="#statistik" @click="mobileMenuOpen = false" class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">STATISTIK</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="text-white/80 hover:text-white text-[14px] font-semibold tracking-wider">FAQ</a>
            <a href="{{ route('nominasi') }}" class="bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[14px] tracking-wide px-6 py-3 rounded-full text-center mt-2">DAFTAR SEKARANG</a>
        </div>
    </header>

    <!-- HERO -->
    <section id="beranda" class="relative min-h-screen flex items-center pt-[140px] pb-[90px] px-6 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?q=80&w=2000&auto=format&fit=crop" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0a0c11]/55 via-[#0a0c11]/72 to-[#0a0c11]"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-[#0a0c11]/35 to-transparent"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto w-full">
            <div x-data="{ shown: false }" x-intersect="shown = true">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" class="inline-flex max-w-full items-center justify-center gap-2.5 px-4 sm:px-[18px] py-2 border border-[#e0b53c]/50 rounded-[20px] sm:rounded-full mb-[26px] transition-all duration-[800ms] ease-out">
                    <span class="w-[6px] h-[6px] shrink-0 rounded-full bg-[#e0b53c] shadow-[0_0_8px_#e0b53c]"></span>
                    <span class="text-[#f5da8b] text-[10.5px] sm:text-[12.5px] font-bold tracking-[0.15em] sm:tracking-[0.24em] leading-normal text-center">PENGHARGAAN NASIONAL &nbsp;&middot;&nbsp; DPD RI</span>
                </div>
                
                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" class="cz text-[clamp(52px,8vw,104px)] font-extrabold leading-[0.94] uppercase tracking-wide mb-6 max-w-[900px] transition-all duration-[800ms] ease-out delay-100">
                    <span class="text-white block">DPD Award</span>
                    <span class="inline-block bg-[linear-gradient(100deg,#b8860b_0%,#f5da8b_28%,#fff7e6_46%,#e0b53c_62%,#9c6f16_100%)] bg-[length:200%_auto] text-transparent bg-clip-text animate-sheen">2026</span>
                </h1>
                
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" class="text-white/90 text-[clamp(17px,2vw,21px)] leading-[1.65] max-w-[620px] mb-[38px] transition-all duration-[800ms] ease-out delay-200">
                    Mengapresiasi dedikasi dan kontribusi luar biasa dari individu-individu inspiratif di seluruh pelosok Nusantara. Terbuka untuk umum dan tidak dipungut biaya.
                </p>
                
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" class="flex flex-wrap gap-4 transition-all duration-[800ms] ease-out delay-300">
                    <a href="#kategori" class="inline-flex items-center gap-2.5 bg-[#88c445] text-[#0a0c11] font-extrabold text-[16px] tracking-[0.03em] px-9 py-[17px] rounded-full shadow-[0_10px_40px_rgba(136,196,69,0.4)] hover:shadow-[0_16px_50px_rgba(136,196,69,0.55)] hover:-translate-y-[2px] transition-all">
                        Ajukan Nominasi
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="#alur" class="inline-flex items-center gap-2.5 border-[1.5px] border-[#f5da8b]/55 text-[#f5da8b] font-bold text-[16px] px-[34px] py-[17px] rounded-full hover:bg-[#e0b53c]/10 transition-colors">
                        Lihat Alur Seleksi
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute bottom-[26px] left-1/2 -translate-x-1/2 z-10 text-white/40 flex flex-col items-center gap-1.5">
            <span class="text-[11px] tracking-[0.2em] font-semibold">GULIR</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="animate-floaty"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </section>

    <!-- COUNTDOWN -->
    <section id="countdown" class="py-16 px-6 bg-gradient-to-b from-[#10131a] to-[#0a0c11] border-y border-[#e0b53c]/15">
        <div x-data="countdown()" x-init="start()" x-intersect="shown = true" class="max-w-[1000px] mx-auto bg-gradient-to-br from-[#191d27] to-[#10131a] border border-[#e0b53c]/30 rounded-[24px] sm:rounded-[28px] p-6 md:p-10 flex flex-col lg:flex-row items-center justify-between gap-8 shadow-[0_30px_70px_rgba(0,0,0,0.5)] transition-all duration-[800ms] ease-out text-center lg:text-left" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
            <div>
                <span class="text-[#88c445] text-[11px] sm:text-xs font-bold tracking-[0.2em]">TENGGAT PENDAFTARAN</span>
                <h2 class="cz text-white text-[clamp(26px,4vw,38px)] font-bold uppercase mt-2">Penutupan <span class="text-[#e0b53c]">Nominasi</span></h2>
                <p class="text-white/55 mt-2 text-[14px] sm:text-[15px]">Waktu tersisa untuk mengirimkan usulan Anda</p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                <div class="text-center">
                    <div class="w-[64px] h-[72px] sm:w-[78px] sm:h-[88px] bg-black/50 border border-[#e0b53c]/25 rounded-xl sm:rounded-2xl flex items-center justify-center mb-2">
                        <span class="cz text-[28px] sm:text-[38px] font-bold text-[#e0b53c]" x-text="days">00</span>
                    </div>
                    <span class="text-white/55 text-[10px] sm:text-[11px] font-bold tracking-[0.14em]">HARI</span>
                </div>
                <div class="text-center">
                    <div class="w-[64px] h-[72px] sm:w-[78px] sm:h-[88px] bg-black/50 border border-[#e0b53c]/25 rounded-xl sm:rounded-2xl flex items-center justify-center mb-2">
                        <span class="cz text-[28px] sm:text-[38px] font-bold text-[#e0b53c]" x-text="hours">00</span>
                    </div>
                    <span class="text-white/55 text-[10px] sm:text-[11px] font-bold tracking-[0.14em]">JAM</span>
                </div>
                <div class="text-center">
                    <div class="w-[64px] h-[72px] sm:w-[78px] sm:h-[88px] bg-black/50 border border-[#e0b53c]/25 rounded-xl sm:rounded-2xl flex items-center justify-center mb-2">
                        <span class="cz text-[28px] sm:text-[38px] font-bold text-[#e0b53c]" x-text="minutes">00</span>
                    </div>
                    <span class="text-white/55 text-[10px] sm:text-[11px] font-bold tracking-[0.14em]">MENIT</span>
                </div>
                <div class="text-center">
                    <div class="w-[64px] h-[72px] sm:w-[78px] sm:h-[88px] bg-black/50 border border-[#e0b53c]/25 rounded-xl sm:rounded-2xl flex items-center justify-center mb-2">
                        <span class="cz text-[28px] sm:text-[38px] font-bold text-[#e0b53c]" x-text="seconds">00</span>
                    </div>
                    <span class="text-white/55 text-[10px] sm:text-[11px] font-bold tracking-[0.14em]">DETIK</span>
                </div>

            </div>
        </div>
        
        <script>
            function countdown() {
                return {
                    shown: false,
                    days: '00', hours: '00', minutes: '00', seconds: '00',
                    endTime: new Date('2026-11-10T23:59:59').getTime(),
                    start() {
                        this.update();
                        setInterval(() => this.update(), 1000);
                    },
                    update() {
                        const d = Math.max(0, this.endTime - Date.now());
                        if(d === 0) return;
                        this.days = String(Math.floor(d / 86400000)).padStart(2, '0');
                        this.hours = String(Math.floor((d % 86400000) / 3600000)).padStart(2, '0');
                        this.minutes = String(Math.floor((d % 3600000) / 60000)).padStart(2, '0');
                        this.seconds = String(Math.floor((d % 60000) / 1000)).padStart(2, '0');
                    }
                }
            }
        </script>
    </section>

    <!-- KATEGORI -->
    <section id="kategori" class="py-[110px] px-6 bg-[#fbf7ee]">
        <div class="max-w-7xl mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="text-center mb-16 transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">ENAM BIDANG APRESIASI</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#10131a] mt-3 leading-none">Kategori <span class="text-[#1b6e4c]">Penghargaan</span></h2>
                <p class="text-[#4b5262] text-[18px] leading-[1.6] max-w-[660px] mx-auto mt-[18px]">Pilih kategori yang paling sesuai dengan dedikasi individu yang ingin Anda nominasikan. Klik untuk memilih, lalu lanjutkan pendaftaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[26px]">
                
                @php
                $categories = [
                    ['id' => 'pendidikan', 'title' => 'Pendidikan', 'desc' => 'Apresiasi bagi pendidik, relawan, atau inovator yang memajukan kualitas dan akses pendidikan di daerah.', 'img' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=800&auto=format&fit=crop', 'svg' => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>'],
                    ['id' => 'kesehatan', 'title' => 'Kesehatan', 'desc' => 'Bagi tenaga medis atau tokoh masyarakat yang secara nyata meningkatkan taraf kesehatan masyarakat luas.', 'img' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?q=80&w=800&auto=format&fit=crop', 'svg' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/><path d="M3.5 12h4l2-3 3 5 2-3h4"/>'],
                    ['id' => 'lingkungan', 'title' => 'Lingkungan', 'desc' => 'Bagi pahlawan lingkungan yang menginisiasi gerakan pelestarian alam, daur ulang, atau konservasi di daerahnya.', 'img' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=800&auto=format&fit=crop', 'svg' => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/>'],
                    ['id' => 'kepemudaan', 'title' => 'Kepemudaan', 'desc' => 'Mengapresiasi tokoh pemuda penggerak komunitas, pencipta inovasi, atau berprestasi mengharumkan nama bangsa.', 'img' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=800&auto=format&fit=crop', 'svg' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>'],
                    ['id' => 'sosial', 'title' => 'Sosial Budaya', 'desc' => 'Bagi individu yang gigih melestarikan kesenian daerah, merawat kerukunan warga, atau memajukan budaya Nusantara.', 'img' => 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=800&auto=format&fit=crop', 'svg' => '<line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/>'],
                    ['id' => 'ekonomi', 'title' => 'Ekonomi Kreatif', 'desc' => 'Untuk wirausahawan atau penggiat UMKM yang menciptakan lapangan kerja dan memberdayakan ekonomi masyarakat sekitar.', 'img' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop', 'svg' => '<path d="m12 3-1.9 5.8-5.8 1.9 5.8 1.9L12 18.4l1.9-5.8 5.8-1.9-5.8-1.9z"/><path d="M5 3v4M19 17v4M3 5h4M17 19h4"/>']
                ];
                @endphp

                @foreach($categories as $index => $cat)
                <div x-data="{ shown: false }" x-intersect="shown = true" @click="selectedCat = '{{ $cat['id'] }}'" 
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'"
                     class="group relative rounded-[24px] overflow-hidden aspect-[1/1.05] flex flex-col justify-end cursor-pointer border-2 transition-all duration-300"
                     :style="selectedCat === '{{ $cat['id'] }}' ? 'border-color: #88c445;' : 'border-color: transparent;'"
                     style="transition-delay: {{ $index * 100 }}ms;">
                     
                    <img src="{{ $cat['img'] }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:-translate-y-1.5 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-[#0a0c11]/55 to-[#0a0c11]/95"></div>
                    
                    <template x-if="selectedCat === '{{ $cat['id'] }}'">
                        <div>
                            <div class="absolute inset-0 border-[3px] border-[#88c445] rounded-[22px] z-30"></div>
                            <div class="absolute top-4 right-4 z-40 w-[34px] h-[34px] rounded-full bg-[#88c445] flex items-center justify-center shadow-[0_4px_14px_rgba(136,196,69,0.6)]">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0a0c11" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </div>
                    </template>

                    <div class="relative z-20 p-[26px]">
                        <div class="w-12 h-12 rounded-xl bg-[#e0b53c]/20 border border-[#e0b53c]/50 flex items-center justify-center mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e0b53c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $cat['svg'] !!}</svg>
                        </div>
                        <h3 class="cz text-[28px] font-bold text-white uppercase mb-2">{{ $cat['title'] }}</h3>
                        <p class="text-white/80 text-[14px] leading-[1.55]">{{ $cat['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- STATISTIK -->
    <section id="statistik" class="relative py-[110px] px-6 bg-[#0a0c11] overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2000&auto=format&fit=crop" alt="" class="w-full h-full object-cover opacity-30 blur-[2px]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0a0c11]/80 to-[#0a0c11]"></div>
        </div>
        <div class="relative z-10 max-w-[1100px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="text-center mb-[60px] transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#88c445] text-[12.5px] font-extrabold tracking-[0.22em]">ANTUSIASME NASIONAL</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase text-white mt-3 leading-none">Statistik <span class="text-[#e0b53c]">Pendaftar</span></h2>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
                @php
                $stats = [
                    ['value' => 1245, 'label' => 'Pendidikan'], ['value' => 982, 'label' => 'Kesehatan'], ['value' => 843, 'label' => 'Lingkungan'],
                    ['value' => 1503, 'label' => 'Kepemudaan'], ['value' => 721, 'label' => 'Sosial Budaya'], ['value' => 2110, 'label' => 'Ekonomi Kreatif']
                ];
                @endphp
                @foreach($stats as $index => $st)
                <div x-data="counter({{ $st['value'] }})" x-intersect="startCount()" class="border border-[#e0b53c]/20 hover:border-[#88c445]/55 bg-gradient-to-br from-[#191d27] to-[#10131a] rounded-xl py-5 px-3 text-center transition-colors duration-300">
                    <div class="cz text-[clamp(28px,2.5vw,36px)] font-bold text-[#e0b53c] leading-none" x-text="display">0</div>
                    <p class="text-white/60 text-[11px] font-bold tracking-[0.1em] uppercase mt-2">{{ $st['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        <script>
            function counter(target) {
                return {
                    display: '0',
                    started: false,
                    startCount() {
                        if(this.started) return;
                        this.started = true;
                        let start = null;
                        const duration = 1800;
                        const fmt = new Intl.NumberFormat('id-ID');
                        const step = (ts) => {
                            if(!start) start = ts;
                            const progress = Math.min((ts - start)/duration, 1);
                            this.display = fmt.format(Math.floor(progress * target));
                            if(progress < 1) requestAnimationFrame(step);
                            else this.display = fmt.format(target);
                        };
                        requestAnimationFrame(step);
                    }
                }
            }
        </script>
    </section>

    <!-- ALUR / TIMELINE -->
    <section id="alur" class="py-[110px] px-6 bg-white">
        <div class="max-w-[900px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="text-center mb-[70px] transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#b8860b] text-[12.5px] font-extrabold tracking-[0.22em]">TAHAP DEMI TAHAP</span>
                <h2 class="cz text-[clamp(38px,6vw,68px)] font-extrabold uppercase text-[#10131a] mt-3 leading-none">Alur <span class="text-[#1b6e4c]">Pemilihan</span></h2>
                <p class="text-[#4b5262] text-[18px] leading-[1.6] mt-[18px]">Tahapan penting menuju malam penganugerahan DPD Award 2026.</p>
            </div>
            
            <div class="relative text-[#10131a]">
                <div class="absolute left-[27px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-[#e0b53c] to-[#88c445]/40"></div>
                
                @php
                $timeline = [
                    ['n' => '1', 'date' => '1 Sep – 10 Nov 2026', 'title' => 'Pengumpulan Nominasi', 'desc' => 'Masyarakat dapat mengusulkan individu yang layak mendapatkan penghargaan melalui formulir daring.', 'dot' => '#50721fff', 'num' => '#fff'],
                    ['n' => '2', 'date' => '12 – 20 Nov 2026', 'title' => 'Seleksi Berkas & Verifikasi', 'desc' => 'Tim komite melakukan verifikasi data lapangan terhadap seluruh kandidat nominator.', 'dot' => '#7cb137ff', 'num' => '#fff'],
                    ['n' => '3', 'date' => '25 – 30 Nov 2026', 'title' => 'Penjurian Final', 'desc' => 'Dewan juri independen menentukan penerima penghargaan untuk setiap kategori.', 'dot' => '#8cc043ff', 'num' => '#fff'], 
                    ['n' => '4', 'date' => '15 Desember 2026', 'title' => 'Malam Penganugerahan', 'desc' => 'Pengumuman pemenang dan penyerahan penghargaan langsung secara nasional.', 'dot' => 'linear-gradient(135deg,#d6d45bff, #cac714ff)', 'num' => '#fff'],
                ];
                @endphp

                @foreach($timeline as $index => $step)
                <div x-data="{ shown: false }" x-intersect="shown = true" class="relative flex gap-7 pb-11 transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" style="transition-delay: {{ $index * 100 }}ms;">
                    <div class="shrink-0 w-14 h-14 rounded-full border-[3px] border-white shadow-[0_6px_18px_rgba(0,0,0,0.15)] flex items-center justify-center z-10 cz font-bold text-xl" style="background: {{ $step['dot'] }}; color: {{ $step['num'] }};">{{ $step['n'] }}</div>
                    <div class="pt-1">
                        <span class="inline-block bg-[#f3ecdd] text-[#b8860b] font-bold text-[13px] px-3.5 py-1.5 rounded-full mb-2.5">{{ $step['date'] }}</span>
                        <h3 class="cz text-[26px] font-bold">{{ $step['title'] }}</h3>
                        <p class="text-[#4b5262] text-[15.5px] leading-[1.6] mt-1.5 max-w-[560px]">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- TESTIMONI -->
    <!-- <section id="testimoni" class="py-[110px] px-6 bg-[#fbf7ee] text-[#10131a]">
        <div class="max-w-[1100px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="text-center mb-[60px] transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#1b6e4c] text-[12.5px] font-extrabold tracking-[0.22em]">SUARA PENERIMA PENGHARGAAN</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase mt-3 leading-none">Cerita <span class="text-[#b8860b]">Inspiratif</span></h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-[26px]">
                @php
                $testimonials = [
                    ['quote' => 'Penghargaan ini bukan akhir, melainkan awal dari tanggung jawab yang lebih besar untuk terus mengabdi bagi masyarakat di daerah saya.', 'name' => 'Siti Rahmawati', 'role' => 'Penerima — Pendidikan 2025', 'img' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=400&auto=format&fit=crop'],
                    ['quote' => 'DPD Award mengangkat kerja-kerja senyap di akar rumput menjadi inspirasi nasional. Sebuah kehormatan yang tak ternilai.', 'name' => 'Bagas Pratama', 'role' => 'Penerima — Lingkungan 2025', 'img' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=400&auto=format&fit=crop'],
                    ['quote' => 'Melalui ajang ini, semangat gotong royong dan budaya lokal kami akhirnya mendapat panggung yang layak di tingkat nasional.', 'name' => 'Ni Made Ayu', 'role' => 'Penerima — Sosial Budaya 2025', 'img' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=400&auto=format&fit=crop'],
                ];
                @endphp
                
                @foreach($testimonials as $index => $t)
                <div x-data="{ shown: false }" x-intersect="shown = true" class="bg-white border border-[#e8e0cf] rounded-[22px] p-8 shadow-[0_8px_24px_rgba(11,42,91,0.06)] flex flex-col transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" style="transition-delay: {{ $index * 150 }}ms;">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="#e0b53c" class="opacity-50 mb-3.5"><path d="M9.5 6C6.5 6 4 8.5 4 11.5V18h6v-6H7c0-1.7 1.1-3 2.5-3zm9 0c-3 0-5.5 2.5-5.5 5.5V18h6v-6h-3c0-1.7 1.1-3 2.5-3z"/></svg>
                    <p class="text-[#333a48] text-base leading-[1.65] italic flex-1">{{ $t['quote'] }}</p>
                    <div class="flex items-center gap-3.5 mt-5 pt-5 border-t border-[#eee]">
                        <img src="{{ $t['img'] }}" alt="" class="w-[52px] h-[52px] rounded-full object-cover border-2 border-[#e0b53c]">
                        <div>
                            <div class="font-extrabold text-[#10131a] text-[15px]">{{ $t['name'] }}</div>
                            <div class="text-[#1b6e4c] text-[13px] font-semibold">{{ $t['role'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section> -->

    <!-- SYARAT & KETENTUAN -->
    <section id="syarat" class="py-[110px] px-6 bg-gradient-to-br from-[#0c3b28] to-[#0a0c11]">
        <div class="max-w-[1100px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_1.2fr] gap-14 items-start">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-[30px]'">
                <span class="text-[#88c445] text-[12.5px] font-extrabold tracking-[0.22em]">SEBELUM MENDAFTAR</span>
                <h2 class="cz text-[clamp(36px,5vw,56px)] font-extrabold uppercase mt-3 leading-[1.02] text-white">Syarat &amp; <span class="text-[#e0b53c]">Ketentuan</span></h2>
                <p class="text-white/70 text-[17px] leading-[1.65] mt-5">Pastikan Anda memenuhi kriteria berikut. Seluruh proses <strong class="text-[#88c445]">gratis</strong> dan terbuka untuk umum — waspadai penipuan yang mengatasnamakan panitia.</p>
                <a href="#kategori" class="inline-flex items-center gap-2.5 mt-[30px] bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[15px] px-[30px] py-[15px] rounded-full shadow-[0_10px_34px_rgba(224,181,60,0.3)]">
                    Mulai Pendaftaran
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
            
            <div class="flex flex-col gap-3.5">
                @php
                $reqs = [
                    ['title' => 'Warga Negara Indonesia', 'desc' => 'Nominee adalah WNI yang berdomisili dan berkontribusi di wilayah Indonesia.'],
                    ['title' => 'Karya & Dampak Nyata', 'desc' => 'Memiliki rekam jejak, inovasi, atau karya berdampak positif dalam salah satu dari 6 kategori.'],
                    ['title' => 'Kelengkapan Dokumen', 'desc' => 'Melampirkan KTP, portofolio/dokumentasi kegiatan, dan foto pendukung.'],
                    ['title' => 'Nominasi Diri atau Orang Lain', 'desc' => 'Anda dapat mengusulkan diri sendiri maupun individu/komunitas lain.'],
                    ['title' => 'Gratis & Terbuka untuk Umum', 'desc' => 'Tidak dipungut biaya apa pun sepanjang proses pendaftaran dan penjurian.'],
                ];
                @endphp
                @foreach($reqs as $index => $r)
                <div x-data="{ shown: false }" x-intersect="shown = true" class="flex gap-4 items-start bg-white/5 border border-[#e0b53c]/20 rounded-2xl p-5 transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[30px]'" style="transition-delay: {{ $index * 100 }}ms;">
                    <div class="shrink-0 w-[30px] h-[30px] rounded-lg bg-[#88c445]/15 flex items-center justify-center">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#88c445" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <h4 class="text-white text-base font-bold">{{ $r['title'] }}</h4>
                        <p class="text-white/60 text-[14px] leading-[1.55] mt-1">{{ $r['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-[110px] px-6 bg-[#fbf7ee] text-[#10131a]">
        <div class="max-w-[820px] mx-auto">
            <div x-data="{ shown: false }" x-intersect="shown = true" class="text-center mb-14 transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
                <span class="text-[#b8860b] text-[12.5px] font-extrabold tracking-[0.22em]">INFORMASI PENDAFTARAN</span>
                <h2 class="cz text-[clamp(38px,6vw,64px)] font-extrabold uppercase mt-3 leading-none">Tanya <span class="text-[#1b6e4c]">Jawab</span></h2>
            </div>
            
            <div class="flex flex-col gap-3.5">
                @php
                $faqData = [
                    ['q' => 'Siapa saja yang bisa dinominasikan?', 'a' => 'Semua Warga Negara Indonesia (WNI) yang memiliki rekam jejak, inovasi, atau karya nyata yang berdampak positif bagi masyarakat di salah satu dari 6 kategori yang tersedia.'],
                    ['q' => 'Apakah saya bisa menominasikan diri sendiri?', 'a' => 'Bisa. Anda dapat mengusulkan diri sendiri atau orang lain (individu/komunitas) selama memenuhi syarat dan ketentuan, serta melampirkan bukti portofolio atau dokumentasi kegiatan.'],
                    ['q' => 'Apakah ada biaya pendaftaran?', 'a' => 'Tidak. Seluruh proses pendaftaran dan nominasi DPD Award tidak dipungut biaya sepeser pun (100% Gratis). Hati-hati terhadap segala bentuk penipuan yang mengatasnamakan panitia.'],
                    ['q' => 'Bagaimana proses penjurian dilakukan?', 'a' => 'Setelah pendaftaran ditutup, tim kurator menyeleksi kelengkapan berkas. Kandidat yang lolos dinilai oleh Dewan Juri independen berdasarkan kriteria dampak sosial, inovasi, keberlanjutan, dan inspirasi.'],
                    ['q' => 'Kapan pemenang diumumkan?', 'a' => 'Pemenang untuk setiap kategori diumumkan pada Malam Penganugerahan DPD Award 2026 yang digelar pada 15 Desember 2026 dan disiarkan secara nasional.']
                ];
                @endphp
                
                @foreach($faqData as $index => $f)
                <div x-data="{ shown: false }" x-intersect="shown = true" class="bg-white border border-[#e8e0cf] rounded-2xl overflow-hidden shadow-[0_2px_10px_rgba(11,42,91,0.04)] transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'" style="transition-delay: {{ $index * 100 }}ms;">
                    <button @click="openFaq === {{ $index }} ? openFaq = -1 : openFaq = {{ $index }}" class="w-full text-left p-6 flex items-center justify-between gap-4 outline-none">
                        <span class="cz text-[19px] font-semibold flex-1">{{ $f['q'] }}</span>
                        <span class="w-[30px] h-[30px] shrink-0 rounded-full bg-[#f3ecdd] flex items-center justify-center transition-transform duration-300" :class="openFaq === {{ $index }} ? 'rotate-180' : ''">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b8860b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                    <div x-show="openFaq === {{ $index }}" x-collapse class="overflow-hidden">
                        <p class="px-6 pb-6 text-[#4b5262] text-[15.5px] leading-[1.7]">{{ $f['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    

    <!-- FINAL CTA -->
    <section class="relative py-[120px] px-6 bg-[#0a0c11] text-center overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_40%,rgba(224,181,60,0.14),transparent_60%)]"></div>
        <div x-data="{ shown: false }" x-intersect="shown = true" class="relative z-10 max-w-[760px] mx-auto transition-all duration-[800ms] ease-out" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-[30px]'">
            <div class="text-[52px] mb-2">🏆</div>
            <h2 class="cz text-[clamp(34px,5vw,58px)] font-extrabold uppercase leading-[1.05] text-white">Karya Nyata Anda Layak <span class="text-[#e0b53c]">Diapresiasi</span></h2>
            <p class="text-white/70 text-[18px] leading-[1.6] mt-5 mb-8 max-w-[560px] mx-auto">Ajukan nominasi sekarang dan jadilah bagian dari malam penganugerahan DPD Award 2026.</p>
            <a href="{{ route('nominasi') }}" class="inline-flex items-center gap-2.5 bg-[#88c445] text-[#0a0c11] font-extrabold text-[17px] px-[42px] py-[18px] rounded-full shadow-[0_12px_44px_rgba(136,196,69,0.4)] hover:-translate-y-[3px] transition-transform">Ajukan Nominasi Sekarang</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-black pt-16 pb-10 px-6 border-t border-[#e0b53c]/15">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between gap-8 items-start">
            <div>
                <div class="cz text-[30px] font-extrabold tracking-wide text-white">DPD <span class="text-[#88c445]">AWARD</span></div>
                <p class="text-white/40 text-[14px] mt-2">Inspirasi Nyata untuk Indonesia</p>
                <p class="text-white/30 text-[13px] mt-1">Dewan Perwakilan Daerah Republik Indonesia</p>
            </div>
            <div class="flex gap-10 flex-wrap">
                <a href="#syarat" class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">Syarat &amp; Ketentuan</a>
                <a href="#faq" class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">FAQ</a>
                <a href="#" class="text-white/60 text-[14px] hover:text-[#88c445] transition-colors">Kontak Panitia</a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-10 pt-6 border-t border-white/10 text-white/35 text-[13px]">
            © 2026 DPD Award. Terbuka untuk umum dan tidak dipungut biaya.
        </div>
    </footer>

    <!-- STICKY SELECTION BAR -->
    <div x-show="selectedCat" x-transition.opacity.duration.300ms class="fixed left-0 right-0 bottom-0 z-[200] bg-[#0a0c11]/95 backdrop-blur-[12px] border-t border-[#88c445]/40 p-4 px-6" style="display: none;">
        <div class="max-w-[1100px] mx-auto flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-[38px] h-[38px] rounded-[10px] bg-[#88c445]/15 flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#88c445" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="text-white/55 text-[12px] font-semibold">Kategori dipilih</div>
                    <div class="cz text-white text-[19px] font-bold" x-text="catNames[selectedCat]"></div>
                </div>
            </div>
            <a :href="'{{ route('nominasi') }}?kategori=' + selectedCat" class="bg-[#88c445] text-[#0a0c11] font-extrabold text-[15px] px-[30px] py-[14px] rounded-full flex justify-center w-full sm:w-auto sm:inline-flex items-center gap-2 hover:bg-[#75a83a] transition-colors">
                Lanjutkan Pendaftaran
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="hidden sm:block"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>

</body>
</html>