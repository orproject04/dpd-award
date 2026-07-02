<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPD Award 2026</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full flex flex-col bg-white text-dark-deep overflow-x-hidden"
    x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Header -->
    <header :class="scrolled ? 'bg-dark-deep/95 backdrop-blur-md border-b border-white/10 py-3' : 'bg-transparent py-5'"
        class="fixed left-0 right-0 z-[100] transition-all duration-300 top-0">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <a href="#" class="text-3xl font-headline text-white font-bold tracking-wider">
                    DPD <span class="text-primary">AWARD</span>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="#"
                        class="text-white hover:text-primary transition-colors text-sm font-medium tracking-wide">BERANDA</a>
                    <a href="#kategori"
                        class="text-white/80 hover:text-white transition-colors text-sm font-medium tracking-wide">NOMINASI</a>
                    <a href="#timeline"
                        class="text-white/80 hover:text-white transition-colors text-sm font-medium tracking-wide">TIMELINE</a>
                    <a href="#statistik"
                        class="text-white/80 hover:text-white transition-colors text-sm font-medium tracking-wide">STATISTIK</a>
                    <a href="#faq"
                        class="text-white/80 hover:text-white transition-colors text-sm font-medium tracking-wide">FAQ</a>
                    <a href="#kategori"
                        class="bg-primary hover:bg-primary-hover text-dark-deep font-bold px-7 py-2.5 rounded-full transition-colors text-sm">DAFTAR</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-white p-2">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div x-show="mobileMenuOpen" x-transition.opacity.duration.300ms
            class="lg:hidden bg-dark-deep/95 backdrop-blur-xl absolute top-full left-0 right-0 p-6 border-b border-white/10 shadow-2xl">
            <div class="flex flex-col gap-4">
                <a @click="mobileMenuOpen = false" href="#"
                    class="text-white font-medium text-lg border-b border-white/10 pb-3">Beranda</a>
                <a @click="mobileMenuOpen = false" href="#kategori"
                    class="text-white/80 font-medium text-lg border-b border-white/10 pb-3">Nominasi</a>
                <a @click="mobileMenuOpen = false" href="#timeline"
                    class="text-white/80 font-medium text-lg border-b border-white/10 pb-3">Timeline</a>
                <a @click="mobileMenuOpen = false" href="#statistik"
                    class="text-white/80 font-medium text-lg border-b border-white/10 pb-3">Statistik</a>
                <a @click="mobileMenuOpen = false" href="#faq"
                    class="text-white/80 font-medium text-lg border-b border-white/10 pb-3">FAQ</a>
                <a @click="mobileMenuOpen = false" href="#kategori"
                    class="bg-primary text-center text-dark-deep font-bold px-6 py-3 rounded-full mt-4">Daftar</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center py-32 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?q=80&w=2000&auto=format&fit=crop"
                class="w-full h-full object-cover" alt="Award Ceremony">
            <div class="absolute inset-0 bg-gradient-to-t from-dark-deep via-dark-deep/60 to-dark-deep/30"></div>
            <!-- Overlay shadow for text readability on left side -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent"></div>
        </div>

        <div class="relative z-10 px-6 md:px-12 lg:px-20 max-w-7xl mx-auto w-full pt-20">
            <div class="max-w-3xl" x-data="{ shown: false }" x-intersect:enter="shown = true"
                x-intersect:leave="shown = false">
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="inline-block px-4 py-1.5 rounded-full border border-primary/50 text-primary text-sm font-bold tracking-widest mb-6 transition-all duration-1000">
                    PENGHARGAAN NASIONAL</div>
                <h1 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="font-headline text-6xl md:text-8xl uppercase leading-[0.9] text-white mb-6 transition-all duration-1000 delay-100">
                    DPD <span class="text-primary block md:inline">Award</span> 2026
                </h1>
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="text-xl md:text-2xl text-white/90 mb-10 leading-relaxed transition-all duration-1000 delay-200">
                    Mengapresiasi dedikasi dan kontribusi luar biasa dari individu-individu inspiratif di seluruh
                    pelosok Nusantara. Berikan nominasi Anda sekarang.
                </p>
                <div :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    class="transition-all duration-1000 delay-300 flex flex-wrap gap-4">
                    <a href="#kategori"
                        class="inline-block bg-primary hover:bg-primary-hover text-dark-deep font-headline tracking-wider text-xl px-10 py-4 rounded-full uppercase transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(136,196,69,0.4)] cursor-pointer">
                        Daftar
                    </a>
                    <a href="#countdown"
                        class="inline-flex items-center justify-center border border-white/30 hover:bg-white/10 text-white font-headline tracking-wider text-xl px-10 py-4 rounded-full uppercase transition-all cursor-pointer">
                        Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Countdown Section -->
    <section id="countdown" class="py-16 bg-dark-deep border-b border-dark-border relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10">
        </div>
        <div class="max-w-5xl mx-auto px-6 relative z-10" x-data="countdown()" x-init="start()"
            x-intersect:enter="shown = true" x-intersect:leave="shown = false">
            <div :class="shown ? 'opacity-100 scale-100' : 'opacity-0 scale-95'"
                class="transition-all duration-700 bg-dark-surface border border-primary/20 rounded-[2rem] p-10 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-left">
                    <h2 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-wide">Penutupan <span
                            class="text-primary">Pendaftaran</span></h2>
                    <p class="text-muted mt-2">Waktu tersisa untuk mengirimkan usulan Anda</p>
                </div>

                <div class="flex gap-2 sm:gap-4 md:gap-6 text-center justify-center">
                    <div class="flex flex-col">
                        <div
                            class="bg-black/50 border border-dark-border rounded-xl w-14 h-16 sm:w-16 sm:h-20 md:w-20 md:h-24 flex items-center justify-center mb-2">
                            <span class="font-headline text-2xl sm:text-3xl md:text-4xl text-primary"
                                x-text="days">00</span>
                        </div>
                        <span class="text-white/60 text-xs sm:text-sm uppercase tracking-wider font-bold">Hari</span>
                    </div>
                    <div class="hidden sm:block text-2xl md:text-3xl text-white/30 mt-4 md:mt-6">:</div>
                    <div class="flex flex-col">
                        <div
                            class="bg-black/50 border border-dark-border rounded-xl w-14 h-16 sm:w-16 sm:h-20 md:w-20 md:h-24 flex items-center justify-center mb-2">
                            <span class="font-headline text-2xl sm:text-3xl md:text-4xl text-primary"
                                x-text="hours">00</span>
                        </div>
                        <span class="text-white/60 text-xs sm:text-sm uppercase tracking-wider font-bold">Jam</span>
                    </div>
                    <div class="hidden sm:block text-2xl md:text-3xl text-white/30 mt-4 md:mt-6">:</div>
                    <div class="flex flex-col">
                        <div
                            class="bg-black/50 border border-dark-border rounded-xl w-14 h-16 sm:w-16 sm:h-20 md:w-20 md:h-24 flex items-center justify-center mb-2">
                            <span class="font-headline text-2xl sm:text-3xl md:text-4xl text-primary"
                                x-text="minutes">00</span>
                        </div>
                        <span class="text-white/60 text-xs sm:text-sm uppercase tracking-wider font-bold">Menit</span>
                    </div>
                    <div class="hidden sm:block text-2xl md:text-3xl text-white/30 mt-4 md:mt-6">:</div>
                    <div class="flex flex-col">
                        <div
                            class="bg-black/50 border border-dark-border rounded-xl w-14 h-16 sm:w-16 sm:h-20 md:w-20 md:h-24 flex items-center justify-center mb-2">
                            <span class="font-headline text-2xl sm:text-3xl md:text-4xl text-primary"
                                x-text="seconds">00</span>
                        </div>
                        <span class="text-white/60 text-sm uppercase tracking-wider font-bold">Detik</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script for Countdown -->
        <script>
            function countdown() {
                return {
                    shown: false,
                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                    endTime: new Date('2026-11-10T23:59:59').getTime(), // Set your end date here
                    start() {
                        this.update();
                        setInterval(() => {
                            this.update();
                        }, 1000);
                    },
                    update() {
                        const now = new Date().getTime();
                        const distance = this.endTime - now;

                        if (distance < 0) {
                            this.days = '00';
                            this.hours = '00';
                            this.minutes = '00';
                            this.seconds = '00';
                            return;
                        }

                        this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                        this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                        this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                        this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                    }
                }
            }
        </script>
    </section>

    <!-- Kategori Section -->
    <section id="kategori" class="py-24 px-6 md:px-12 lg:px-20 bg-surface overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" x-data="{ shown: false }" x-intersect:enter="shown = true"
                x-intersect:leave="shown = false">
                <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="font-headline text-5xl md:text-7xl uppercase text-dark-deep transition-all duration-700">
                    Kategori <span class="text-primary">Penghargaan</span></h2>
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-xl text-muted mt-4 transition-all duration-700 delay-100 max-w-3xl mx-auto">Pilih
                    kategori yang paling sesuai dengan profil dan dedikasi individu yang ingin Anda nominasikan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Kategori 1 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms;">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Pendidikan">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Pendidikan
                        </h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Apresiasi untuk para pendidik, relawan, atau
                            inovator yang telah memajukan kualitas dan akses pendidikan di daerah terpencil.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>

                <!-- Kategori 2 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 100ms;">
                    <img src="https://images.unsplash.com/photo-1505751172876-fa1923c5c528?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kesehatan">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Kesehatan
                        </h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Ditujukan bagi tenaga medis atau tokoh
                            masyarakat yang secara nyata meningkatkan taraf kesehatan masyarakat luas.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>

                <!-- Kategori 3 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 200ms;">
                    <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Lingkungan">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Lingkungan
                        </h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Bagi pahlawan lingkungan yang telah
                            menginisiasi gerakan pelestarian alam, daur ulang, atau konservasi di daerahnya.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>

                <!-- Kategori 4 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 0ms;">
                    <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Kepemudaan">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Kepemudaan
                        </h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Mengapresiasi tokoh pemuda yang menjadi
                            penggerak komunitas, menciptakan inovasi, atau berprestasi mengharumkan nama bangsa.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>

                <!-- Kategori 5 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 100ms;">
                    <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Sosial Budaya">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Sosial Budaya
                        </h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Apresiasi bagi individu yang gigih
                            melestarikan kesenian daerah, merawat kerukunan warga, atau memajukan budaya Nusantara.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>

                <!-- Kategori 6 -->
                <div class="group relative overflow-hidden rounded-[2rem] aspect-square flex flex-col justify-end"
                    x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 200ms;">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="Ekonomi Kreatif">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    <div class="relative z-10 p-5 sm:p-6 flex flex-col items-start w-full text-left">
                        <span class="text-white/80 text-xs font-bold uppercase tracking-widest mb-1">KATEGORI
                            NOMINASI</span>
                        <h3 class="font-headline text-2xl sm:text-3xl text-white font-bold mb-2 uppercase">Ekonomi
                            Kreatif</h3>
                        <p class="text-white/80 text-sm mb-6 line-clamp-3">Untuk para wirausahawan atau penggiat UMKM
                            yang berhasil menciptakan lapangan kerja dan memberdayakan ekonomi masyarakat sekitar.</p>
                        <button
                            class="bg-primary hover:bg-primary-hover text-dark-deep font-bold py-2.5 px-6 rounded-full transition-colors text-sm uppercase tracking-wide cursor-pointer">Daftar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section id="timeline" class="py-24 px-6 md:px-12 lg:px-20 bg-white overflow-hidden">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16" x-data="{ shown: false }" x-intersect:enter="shown = true"
                x-intersect:leave="shown = false">
                <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="font-headline text-5xl md:text-7xl uppercase text-dark-deep transition-all duration-700">Alur
                    <span class="text-primary">Pemilihan</span></h2>
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-xl text-muted mt-4 transition-all duration-700 delay-100">Tahapan penting menuju malam
                    penganugerahan DPD Award 2026.</p>
            </div>

            <div class="relative border-l-2 border-primary/30 ml-4 md:mx-auto md:border-l-0">
                <!-- Vertical Line (Desktop) -->
                <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-primary/30">
                </div>

                <div class="space-y-12">
                    <!-- Step 1 -->
                    <div class="relative flex md:justify-between items-center group" x-data="{ shown: false }"
                        x-intersect:enter="shown = true" x-intersect:leave="shown = false">
                        <div class="hidden md:block w-5/12 text-right pr-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                            style="transition: all 700ms ease;">
                            <h3 class="font-headline text-3xl text-dark-deep">Pengumpulan Nominasi</h3>
                            <p class="text-muted mt-2">Masyarakat dapat mengusulkan individu yang layak mendapatkan
                                penghargaan.</p>
                        </div>
                        <div
                            class="absolute left-[-9px] md:left-1/2 md:transform md:-translate-x-1/2 w-4 h-4 rounded-full bg-primary ring-4 ring-white shadow-lg transition-transform group-hover:scale-150 z-10">
                        </div>
                        <div class="ml-8 md:ml-0 md:w-5/12 pl-0 md:pl-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                            style="transition: all 700ms ease;">
                            <span
                                class="inline-block px-3 py-1 bg-surface text-primary font-bold rounded-full text-sm mb-2">1
                                Sep - 10 Nov 2026</span>
                            <div class="md:hidden">
                                <h3 class="font-headline text-2xl text-dark-deep">Pengumpulan Nominasi</h3>
                                <p class="text-muted mt-2 text-sm">Masyarakat dapat mengusulkan individu yang layak
                                    mendapatkan penghargaan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative flex md:justify-between items-center group flex-row-reverse md:flex-row"
                        x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false">
                        <div class="hidden md:block w-5/12 pl-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                            style="transition: all 700ms ease;">
                            <span
                                class="inline-block px-3 py-1 bg-surface text-primary font-bold rounded-full text-sm mb-2">12
                                Nov - 20 Nov 2026</span>
                        </div>
                        <div
                            class="absolute left-[-9px] md:left-1/2 md:transform md:-translate-x-1/2 w-4 h-4 rounded-full bg-primary ring-4 ring-white shadow-lg transition-transform group-hover:scale-150 z-10">
                        </div>
                        <div class="ml-8 md:ml-0 md:w-5/12 md:text-right pr-0 md:pr-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                            style="transition: all 700ms ease;">
                            <div class="md:hidden">
                                <span
                                    class="inline-block px-3 py-1 bg-surface text-primary font-bold rounded-full text-sm mb-2">12
                                    Nov - 20 Nov 2026</span>
                            </div>
                            <h3 class="font-headline text-2xl md:text-3xl text-dark-deep">Seleksi Berkas & Verifikasi
                            </h3>
                            <p class="text-muted mt-2 text-sm md:text-base">Tim komite akan melakukan verifikasi data
                                lapangan terhadap seluruh kandidat nominator.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative flex md:justify-between items-center group" x-data="{ shown: false }"
                        x-intersect:enter="shown = true" x-intersect:leave="shown = false">
                        <div class="hidden md:block w-5/12 text-right pr-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                            style="transition: all 700ms ease;">
                            <h3 class="font-headline text-3xl text-dark-deep">Penjurian Final</h3>
                            <p class="text-muted mt-2">Dewan juri independen menentukan penerima penghargaan untuk
                                setiap kategori.</p>
                        </div>
                        <div
                            class="absolute left-[-9px] md:left-1/2 md:transform md:-translate-x-1/2 w-4 h-4 rounded-full bg-primary ring-4 ring-white shadow-lg transition-transform group-hover:scale-150 z-10">
                        </div>
                        <div class="ml-8 md:ml-0 md:w-5/12 pl-0 md:pl-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                            style="transition: all 700ms ease;">
                            <span
                                class="inline-block px-3 py-1 bg-surface text-primary font-bold rounded-full text-sm mb-2">25
                                Nov - 30 Nov 2026</span>
                            <div class="md:hidden">
                                <h3 class="font-headline text-2xl text-dark-deep">Penjurian Final</h3>
                                <p class="text-muted mt-2 text-sm">Dewan juri independen menentukan penerima penghargaan
                                    untuk setiap kategori.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative flex md:justify-between items-center group flex-row-reverse md:flex-row"
                        x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false">
                        <div class="hidden md:block w-5/12 pl-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-12'"
                            style="transition: all 700ms ease;">
                            <span
                                class="inline-block px-3 py-1 bg-primary text-dark-deep font-bold rounded-full text-sm mb-2">15
                                Desember 2026</span>
                        </div>
                        <div
                            class="absolute left-[-13px] md:left-1/2 md:transform md:-translate-x-1/2 w-6 h-6 rounded-full bg-dark-deep ring-4 ring-primary shadow-lg transition-transform group-hover:scale-125 z-10">
                        </div>
                        <div class="ml-8 md:ml-0 md:w-5/12 md:text-right pr-0 md:pr-8"
                            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                            style="transition: all 700ms ease;">
                            <div class="md:hidden">
                                <span
                                    class="inline-block px-3 py-1 bg-primary text-dark-deep font-bold rounded-full text-sm mb-2">15
                                    Desember 2026</span>
                            </div>
                            <h3 class="font-headline text-2xl md:text-3xl text-primary font-bold">Malam Penganugerahan
                            </h3>
                            <p class="text-muted mt-2 text-sm md:text-base">Pengumuman pemenang dan penyerahan
                                penghargaan langsung secara nasional.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="statistik"
        class="py-24 px-6 md:px-12 lg:px-20 bg-dark-deep text-white border-t border-dark-border relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover blur-sm opacity-40" alt="Statistics Background">
            <div class="absolute inset-0 bg-dark-deep/80 backdrop-blur-[2px]"></div>
        </div>
        <div class="max-w-7xl mx-auto relative z-10">
            <h2 class="text-center font-headline text-5xl md:text-7xl uppercase mb-16 tracking-wide"
                x-data="{ shown: false }" x-intersect:enter="shown = true" x-intersect:leave="shown = false"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                style="transition: all 700ms ease;">Statistik <span class="text-primary">Pendaftar</span></h2>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
                <!-- Stat 1 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 1245, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 50ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Pendidikan</p>
                </div>

                <!-- Stat 2 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 982, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 100ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Kesehatan</p>
                </div>

                <!-- Stat 3 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 843, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 150ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Lingkungan</p>
                </div>

                <!-- Stat 4 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 1503, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 200ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Kepemudaan</p>
                </div>

                <!-- Stat 5 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 721, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 250ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Sosial Budaya</p>
                </div>

                <!-- Stat 6 -->
                <div class="relative rounded-[2rem] border border-dark-border bg-gradient-to-b from-dark-surface to-dark-deep p-8 text-center group hover:border-primary/50 transition-colors"
                    x-data="{ shown: false, current: 0, target: 2110, started: false, startCount() { if(this.started) return; this.started = true; let start = null; const duration = 2000; const step = (ts) => { if(!this.started) return; if(!start) start = ts; const progress = Math.min((ts - start)/duration, 1); this.current = Math.floor(progress * this.target); if(progress < 1) requestAnimationFrame(step); else this.current = this.target; }; requestAnimationFrame(step); }, resetCount() { this.started = false; this.current = 0; } }"
                    x-intersect:enter="shown = true; startCount()" x-intersect:leave="shown = false; resetCount()"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    style="transition-duration: 700ms; transition-delay: 300ms;">
                    <h4 class="text-primary font-headline text-5xl md:text-6xl mb-2"
                        x-text="new Intl.NumberFormat('id-ID').format(current)">0</h4>
                    <p class="text-muted text-sm md:text-base uppercase tracking-widest font-bold">Ekonomi Kreatif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 px-6 md:px-12 lg:px-20 bg-surface">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16" x-data="{ shown: false }" x-intersect:enter="shown = true"
                x-intersect:leave="shown = false">
                <h2 :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="font-headline text-5xl md:text-7xl uppercase text-dark-deep transition-all duration-700">
                    Tanya <span class="text-primary">Jawab</span></h2>
                <p :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    class="text-xl text-muted mt-4 transition-all duration-700 delay-100">Informasi lengkap seputar
                    pendaftaran dan nominasi.</p>
            </div>

            <div class="space-y-4" x-data="{ shown: false }" x-intersect:enter="shown = true"
                x-intersect:leave="shown = false"
                :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                style="transition: all 700ms ease;">

                <!-- FAQ Item 1 -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="bg-white border border-border rounded-2xl overflow-hidden transition-all duration-300">
                    <button @click="open = !open"
                        class="w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none cursor-pointer">
                        <span class="font-headline text-2xl text-dark-deep">Siapa saja yang bisa dinominasikan?</span>
                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-6 h-6 text-primary transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="px-8 pb-6 text-muted leading-relaxed">
                            Semua Warga Negara Indonesia (WNI) yang memiliki rekam jejak, inovasi, atau karya nyata yang
                            berdampak positif bagi masyarakat di sekitarnya dalam salah satu dari 6 kategori yang
                            tersedia.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="bg-white border border-border rounded-2xl overflow-hidden transition-all duration-300">
                    <button @click="open = !open"
                        class="w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none cursor-pointer">
                        <span class="font-headline text-2xl text-dark-deep">Apakah saya bisa menominasikan diri
                            sendiri?</span>
                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-6 h-6 text-primary transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="px-8 pb-6 text-muted leading-relaxed">
                            Bisa. Anda dapat mengusulkan diri sendiri atau mengusulkan orang lain (individu/komunitas)
                            selama memenuhi syarat dan ketentuan yang berlaku, serta melampirkan bukti portofolio atau
                            dokumentasi kegiatan.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="bg-white border border-border rounded-2xl overflow-hidden transition-all duration-300">
                    <button @click="open = !open"
                        class="w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none cursor-pointer">
                        <span class="font-headline text-2xl text-dark-deep">Apakah ada biaya pendaftaran?</span>
                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-6 h-6 text-primary transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="px-8 pb-6 text-muted leading-relaxed">
                            Tidak. Seluruh proses pendaftaran dan nominasi DPD Award tidak dipungut biaya sepeser pun
                            (100% Gratis). Hati-hati terhadap segala bentuk penipuan yang mengatasnamakan panitia.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    class="bg-white border border-border rounded-2xl overflow-hidden transition-all duration-300">
                    <button @click="open = !open"
                        class="w-full px-8 py-6 text-left flex justify-between items-center focus:outline-none cursor-pointer">
                        <span class="font-headline text-2xl text-dark-deep">Bagaimana proses penjurian dilakukan?</span>
                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-6 h-6 text-primary transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" x-collapse>
                        <div class="px-8 pb-6 text-muted leading-relaxed">
                            Setelah pendaftaran ditutup, tim kurator akan menyeleksi kelengkapan berkas. Kandidat yang
                            lolos akan dinilai oleh Dewan Juri independen berdasarkan kriteria dampak sosial, inovasi,
                            keberlanjutan, dan inspirasi.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white/60 py-16 px-6 border-t border-dark-border relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
            <div class="flex flex-col items-center md:items-start gap-2">
                <div class="font-headline text-4xl text-white font-bold tracking-wider">
                    DPD <span class="text-primary">AWARD</span>
                </div>
                <p class="text-sm text-white/40">Inspirasi Nyata untuk Indonesia</p>
            </div>

            <div class="flex gap-8">
                <a href="#" class="hover:text-primary transition-colors">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-primary transition-colors">Media Partner</a>
                <a href="#" class="hover:text-primary transition-colors">Kontak Panitia</a>
            </div>

            <p class="text-sm text-white/40">© {{ date('Y') }} DPD Award. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>