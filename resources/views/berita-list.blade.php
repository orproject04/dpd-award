<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Berita Terkini - DPDRI AWARDS 2026</title>
    <link rel="icon" href="{{ asset_cb('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3ecdd;
            color: #10131a;
        }

        .cz {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-[#0a0c11] text-[#f3ecdd] antialiased min-h-screen relative">
    <header class="sticky top-0 z-50 bg-[#0a0c11]/96 backdrop-blur-[10px] border-b border-[#e0b53c]/20 relative">
        <a href="{{ route('landing') }}"
            class="absolute left-6 top-1/2 -translate-y-1/2 items-center gap-2.5 text-white/75 hover:text-white text-[16px] font-semibold transition-colors z-10 hidden md:flex">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Kembali
        </a>

        <a href="{{ route('landing') }}"
            class="absolute left-4 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-9 h-9 text-white/75 hover:text-white bg-white/10 hover:bg-white/20 rounded-full transition-colors z-10 md:hidden">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
        </a>

        <div class="max-w-[900px] mx-auto px-6 py-4 flex items-center justify-center">
            <a href="{{ route('landing') }}"
                class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 w-full max-w-[450px]">
                <div class="flex justify-end"><img src="{{ asset_cb('images/dpdlogo.png') }}"
                        class="h-12 object-contain"></div>
                <div class="flex justify-center"><img src="{{ asset_cb('images/setjenlogo.png') }}"
                        class="h-12 object-contain"></div>
                <div class="flex justify-start"><img src="{{ asset_cb('images/detik.png') }}"
                        class="h-12 object-contain" style="transform: translateY(3px);"></div>
            </a>
        </div>
    </header>

    <main class="py-[60px] px-6 min-h-screen bg-gradient-to-b from-[#0a2519] via-[#0c3b28] to-[#0a2519]">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <h1 class="cz text-[clamp(32px,5vw,52px)] font-extrabold uppercase text-white leading-none">
                    BERITA <span class="text-[#e0b53c]">TERKINI</span>
                </h1>
                <p class="text-white/60 mt-4 text-[16px] max-w-2xl mx-auto">Informasi dan pembaruan terbaru seputar
                    pelaksanaan DPDRI <i>AWARDS</i> 2026.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($beritaList as $article)
                    <a href="{{ $article->tautan ?: '#' }}" target="{{ $article->tautan ? '_blank' : '_self' }}"
                        rel="noopener noreferrer"
                        class="group bg-[#fbf7ee] rounded-2xl overflow-hidden border border-[#e0b53c]/10 hover:border-[#e0b53c]/40 hover:-translate-y-1 transition-all duration-300 shadow-[0_10px_30px_rgba(0,0,0,0.25)] flex flex-col">
                        <div class="relative aspect-[16/10] bg-[#efe6ce] overflow-hidden">
                            <img src="{{ $article->gambar ? asset('storage/' . $article->gambar) : asset_cb('images/hero-bg.jpg') }}"
                                alt="{{ $article->judul }}"
                                class="w-full h-full object-cover object-top scale-145 group-hover:scale-[1.55] transition-transform duration-[600ms]"
                                onerror="this.style.opacity='0.15'">
                            <div class="absolute top-4 left-4">
                                <span
                                    class="inline-block bg-[#e0b53c] text-[#0a0c11] text-[11px] font-extrabold tracking-wider px-3 py-1 rounded-full shadow">{{ $article->sumber ?: 'Update' }}</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-[#8a6d1c] text-[11px] font-bold tracking-wider mb-2">
                                {{ $article->tanggal ?: $article->created_at->format('d M Y') }}
                            </div>
                            <h3
                                class="text-[#10131a] text-[17px] font-extrabold leading-snug mb-3 group-hover:text-[#8a6d1c] transition-colors">
                                {{ $article->judul }}
                            </h3>
                            <p class="text-[#4b5262] text-[13.5px] leading-[1.65] mb-5">{{ $article->kutipan }}</p>
                            <div
                                class="mt-auto inline-flex items-center gap-1.5 text-[#8a6d1c] text-[12.5px] font-bold tracking-wider group-hover:gap-2.5 transition-all">
                                Baca Selengkapnya
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 19" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20 text-white/50 text-lg">Belum ada berita yang diterbitkan.
                    </div>
                @endforelse
            </div>

            <div class="mt-12 flex justify-center">
                {{ $beritaList->links() }}
            </div>
        </div>
    </main>

    <footer class="py-10 bg-[#0a0c11] text-center text-white/40 text-[13px] border-t border-white/5">
        &copy; 2026 DPD RI. All rights reserved.
    </footer>
</body>

</html>