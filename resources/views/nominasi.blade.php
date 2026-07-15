<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DPDRI AWARDS 2026</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        @media (min-width: 1024px) {
            html {
                zoom: 0.9;
            }
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3ecdd;
            color: #10131a;
        }

        .cz {
            font-family: 'Poppins', sans-serif;
        }

        input:focus,
        textarea:focus {
            border-color: #1b6e4c !important;
            box-shadow: 0 0 0 3px rgba(27, 110, 76, .15) !important;
            outline: none;
        }

        @keyframes pop {
            0% {
                transform: scale(.6);
                opacity: 0;
            }

            60% {
                transform: scale(1.08);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pop {
            animation: pop .5s ease;
        }

        [x-cloak] {
            display: none !important;
        }

        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            header,
            .fixed,
            .print-hide {
                display: none !important;
            }

            .print-show {
                display: block !important;
            }
        }
    </style>
</head>

<body class="bg-[#f3ecdd] text-[#10131a] antialiased min-h-screen relative" x-data="nominasiForm()">

    <div class="fixed inset-0 pointer-events-none z-[-1] bg-[radial-gradient(circle_at_50%_-10%,#fff8ea,#f3ecdd_45%)]">
    </div>

    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-[#0a0c11]/96 backdrop-blur-[10px] border-b border-[#e0b53c]/20 relative">
        <!-- Desktop Back Button -->
        <a href="{{ route('landing') }}"
            class="absolute left-6 top-1/2 -translate-y-1/2 items-center gap-2.5 text-white/75 hover:text-white text-[16px] lg:text-[18px] font-semibold transition-colors z-10 hidden md:flex">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Kembali
        </a>

        <!-- Mobile Back Button -->
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
                class="cz text-[22px] font-extrabold tracking-wide text-white whitespace-nowrap flex items-center gap-3">
                <img src="{{ asset('images/dpdlogo.png') }}" alt="Logo DPD RI" class="h-15 object-contain">
                <img src="{{ asset('images/setjenlogo.png') }}" alt="Logo Setjen DPD RI" class="h-15 object-contain">
            </a>
        </div>
    </header>

    <!-- SUCCESS -->
    <template x-if="submitted">
        <div class="max-w-[640px] mx-auto px-6 pt-[70px] pb-[90px] text-center print-hide" x-cloak>
            <div
                class="w-24 h-24 rounded-full bg-gradient-to-br from-[#88c445] to-[#1b6e4c] flex items-center justify-center mx-auto mb-7 shadow-[0_16px_44px_rgba(27,110,76,.4)] animate-pop">
                <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>
            <h1 class="cz text-[clamp(30px,5vw,44px)] font-extrabold uppercase text-[#10131a]">Pendaftaran <span
                    class="text-[#1b6e4c]">Terkirim</span></h1>
            <p class="text-[#4b5262] text-[17px] leading-[1.65] mt-4 max-w-[480px] mx-auto">Terima kasih telah
                berpartisipasi. Pendaftaran Anda telah kami terima dan akan diverifikasi oleh tim komite.</p>

            <div
                class="inline-block mt-6 bg-white border border-[#e8ddc4] rounded-2xl py-4 px-7 shadow-[0_8px_24px_rgba(11,42,91,.08)]">
                <div class="text-[12px] text-[#8a7f66] font-bold tracking-[0.12em]">NOMOR REGISTRASI</div>
                <div class="cz text-[26px] font-bold text-[#b8860b] mt-1 tracking-wide" x-text="regId"></div>
                <div class="mt-4 flex gap-2 justify-center print-hide">
                    <button
                        @click="navigator.clipboard.writeText(regId); Swal.fire({title: 'Berhasil!', text: 'Nomor Registrasi disalin!', icon: 'success', confirmButtonColor: '#1b6e4c'})"
                        class="cursor-pointer text-[13px] font-bold bg-[#f3ecdd] text-[#1b6e4c] px-3.5 py-2 rounded-lg hover:bg-[#e8ddc4] transition-colors border border-[#1b6e4c]/20">Salin
                        Nomor</button>
                    <button @click="window.print()"
                        class="cursor-pointer text-[13px] font-bold bg-[#1b6e4c] text-white px-3.5 py-2 rounded-lg hover:bg-[#11563bff] transition-colors shadow-sm">Cetak
                        Bukti</button>
                </div>
            </div>

            <div class="mt-8 print-hide">
                <a href="{{ route('landing') }}"
                    class="cursor-pointer inline-flex items-center gap-2 bg-[#0a0c11] text-white font-bold text-[15px] px-[30px] py-[14px] rounded-xl hover:bg-black transition-colors">Kembali
                    ke Beranda</a>
            </div>
        </div>
    </template>

    <!-- FORM SHELL -->
    <div x-show="!submitted" class="print:!block">
        <div class="max-w-[900px] mx-auto px-6 pt-11 pb-24 print:pt-0 print:pb-0">

            <div class="text-center mb-9">
                <span class="text-[#1b6e4c] text-[12px] font-extrabold tracking-[0.2em]">FORMULIR PENDAFTARAN</span>
                <h1 class="cz text-[clamp(30px,5vw,46px)] font-extrabold uppercase mt-2">DPDRI <i>AWARDS</i> <span
                        class="text-[#b8860b]">2026</span></h1>

                <template x-if="submitted">
                    <div
                        class="hidden print:inline-block mt-4 bg-white border border-[#e8ddc4] rounded-2xl py-3 px-6 shadow-sm text-center">
                        <div class="text-[11px] text-[#8a7f66] font-bold tracking-[0.12em]">NOMOR REGISTRASI</div>
                        <div class="cz text-[22px] font-bold text-[#b8860b] mt-0.5 tracking-wide" x-text="regId"></div>
                        <div class="text-[11px] text-[#6b7280] mt-1 font-medium">Disubmit pada: <span
                                x-text="submitTime"></span></div>
                    </div>
                </template>
            </div>

            <!-- STEPPER -->
            <div class="flex items-start mb-3.5 print-hide">
                <template x-for="(s, index) in steps" :key="index">
                    <div class="flex items-start min-w-0"
                        :class="index === steps.length - 1 ? 'shrink-0 basis-[64px]' : 'flex-1'">
                        <div class="flex flex-col items-center gap-2 shrink-0 w-16 cursor-pointer hover:opacity-80 transition-opacity"
                            @click="jumpTo(index)">
                            <div class="w-[42px] h-[42px] rounded-full flex items-center justify-center font-bold text-[15px] transition-all duration-300 border-2"
                                :class="stepClasses(index).circle">
                                <template x-if="index < step">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                        stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </template>
                                <template x-if="index >= step">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <span class="text-[10px] sm:text-[11.5px] text-center leading-[1.25]"
                                :class="stepClasses(index).text" x-text="s.label"></span>
                        </div>
                        <template x-if="index < steps.length - 1">
                            <div class="flex-1 h-0.5 mt-5 rounded-sm transition-colors duration-300"
                                :class="index < step ? 'bg-[#88c445]' : 'bg-[#e0d6bd]'"></div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- CARD -->
            <div
                class="bg-white border border-[#e8ddc4] rounded-[22px] shadow-[0_18px_48px_rgba(11,42,91,0.10)] p-[clamp(24px,4vw,44px)] mt-6">

                <template x-if="step > 0 && step < 4 && data.kategori">
                    <div
                        class="mb-6 flex items-start gap-2.5 px-4 py-3 bg-[#1b6e4c]/10 border border-[#1b6e4c]/20 rounded-xl text-[#1b6e4c] text-[13px] leading-snug">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div class="flex flex-col">
                            <span class="font-semibold opacity-90 mt-0.5"
                                x-text="(categories.find(c => c.id === data.kategori)?.name || '') + ' : ' + (categories.find(c => c.id === data.kategori)?.en || '')"></span>
                        </div>
                    </div>
                </template>

                <!-- STEP 0: KATEGORI -->
                <div x-show="step === 0">
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Pilih Kategori</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Tentukan bidang yang paling sesuai dengan
                        kontribusi yang didaftarkan.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <template x-for="c in categories" :key="c.id">
                            <div @click="data.kategori = c.id"
                                class="cursor-pointer border-2 rounded-2xl p-5 flex items-center gap-3.5 transition-all duration-200"
                                :class="data.kategori === c.id ? 'border-[#1b6e4c] bg-[#f0f8f2]' : 'border-[#e8ddc4] bg-white'">
                                <div class="shrink-0 w-[46px] h-[46px] rounded-xl flex items-center justify-center transition-colors"
                                    :class="data.kategori === c.id ? 'bg-[#1b6e4c] text-white' : 'bg-[#f3ecdd] text-[#b8860b]'"
                                    x-html="c.icon"></div>
                                <div class="flex-1">
                                    <div class="cz text-[18px] font-bold text-[#10131a]" x-text="c.name"></div>
                                    <div class="text-[12.5px] text-[#8a7f66] font-semibold" x-text="c.en"></div>
                                </div>
                                <template x-if="data.kategori === c.id">
                                    <div
                                        class="shrink-0 w-6 h-6 rounded-full bg-[#1b6e4c] flex items-center justify-center">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                            stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <template x-if="showErr && errs.kategori">
                        <p class="text-[#c0392b] text-[13.5px] font-semibold mt-3.5">Silakan pilih salah satu kategori.
                        </p>
                    </template>
                </div>

                <!-- STEP 1: DATA DIRI -->
                <div x-show="step === 1" x-cloak>
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Data Diri</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Isi data diri Anda sebelum melanjutkan
                        pendaftaran.</p>

                    <div class="flex flex-col gap-5">

                        <!-- Honeypot Field -->
                        <div class="hidden" aria-hidden="true">
                            <label>Leave this empty if you are human</label>
                            <input type="text" x-model="data.website_url" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-[14px] font-bold mb-2">Nama Lengkap <span
                                    class="text-[#c0392b]">*</span></label>
                            <input x-model="data.namaNominee" type="text" placeholder="Nama lengkap"
                                class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                            <template x-if="showErr && errs.namaNominee">
                                <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.namaNominee">
                                </p>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Tempat Lahir <span
                                        class="text-[#c0392b]">*</span></label>
                                <input x-model="data.wilayah" type="text" placeholder="Tempat lahir"
                                    class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.wilayah">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.wilayah">
                                    </p>
                                </template>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Tanggal Lahir <span
                                        class="text-[#c0392b]">*</span></label>
                                <input x-model="data.tanggalLahir" type="date" onclick="this.showPicker()"
                                    class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200 cursor-pointer">
                                <template x-if="showErr && errs.tanggalLahir">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5"
                                        x-text="errs.tanggalLahir">
                                    </p>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Jenis Kelamin <span
                                        class="text-[#c0392b]">*</span></label>
                                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                                    <button type="button" @click="open = !open"
                                        class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] transition-all duration-200 flex justify-between items-center bg-white text-left focus:border-[#1b6e4c] focus:ring-[3px] focus:ring-[#1b6e4c]/15"
                                        :class="open ? 'border-[#1b6e4c] ring-[3px] ring-[#1b6e4c]/15' : 'border-[#d8cdb4]'">
                                        <span x-text="data.jenisKelamin || 'Pilih Jenis Kelamin'"
                                            :class="!data.jenisKelamin ? 'text-gray-400' : 'text-[#10131a]'"></span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
                                        class="absolute z-10 w-full mt-1 bg-white border border-[#d8cdb4] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] py-1 overflow-hidden">
                                        <div @click="data.jenisKelamin = 'Laki-laki'; open = false"
                                            class="px-4 py-2.5 text-[14.5px] hover:bg-[#f0f8f2] hover:text-[#1b6e4c] cursor-pointer transition-colors">
                                            Laki-laki</div>
                                        <div @click="data.jenisKelamin = 'Perempuan'; open = false"
                                            class="px-4 py-2.5 text-[14.5px] hover:bg-[#f0f8f2] hover:text-[#1b6e4c] cursor-pointer transition-colors">
                                            Perempuan</div>
                                    </div>
                                </div>
                                <template x-if="showErr && errs.jenisKelamin">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5"
                                        x-text="errs.jenisKelamin"></p>
                                </template>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Pendidikan<span
                                        class="text-[#c0392b]">*</span></label>
                                <div x-data="{ open: false, options: ['SMA/Sederajat', 'Diploma I', 'Diploma II', 'Diploma III', 'Diploma IV', 'Sarjana (S1)', 'Magister (S2)', 'Doktor (S3)'] }"
                                    class="relative" @click.outside="open = false">
                                    <button type="button" @click="open = !open"
                                        class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] transition-all duration-200 flex justify-between items-center bg-white text-left focus:border-[#1b6e4c] focus:ring-[3px] focus:ring-[#1b6e4c]/15"
                                        :class="open ? 'border-[#1b6e4c] ring-[3px] ring-[#1b6e4c]/15' : 'border-[#d8cdb4]'">
                                        <span x-text="data.pendidikan || 'Pilih Tingkat Pendidikan'"
                                            :class="!data.pendidikan ? 'text-gray-400' : 'text-[#10131a]'"></span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition.opacity.duration.200ms
                                        class="absolute z-10 w-full mt-1 bg-white border border-[#d8cdb4] rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] py-1 max-h-56 overflow-y-auto">
                                        <template x-for="opt in options" :key="opt">
                                            <div @click="data.pendidikan = opt; open = false"
                                                class="px-4 py-2.5 text-[14.5px] hover:bg-[#f0f8f2] hover:text-[#1b6e4c] cursor-pointer transition-colors"
                                                x-text="opt"></div>
                                        </template>
                                    </div>
                                </div>
                                <template x-if="showErr && errs.pendidikan">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.pendidikan">
                                    </p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[14px] font-bold mb-2">Alamat <span
                                    class="text-[#c0392b]">*</span></label>
                            <input x-model="data.alamat"
                                class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200"
                                type="text" placeholder="Masukkan Alamat">
                            <template x-if="showErr && errs.alamat">
                                <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.alamat">
                                </p>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Nomor WhatsApp <span
                                        class="text-[#c0392b]">*</span></label>
                                <input x-model="data.telp" @input="data.telp = data.telp.replace(/[^0-9]/g, '')"
                                    type="tel" placeholder="08xxxxxxxxxx" maxlength="15"
                                    class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.telp">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.telp"></p>
                                </template>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[14px] font-bold mb-2">Alamat Email <span
                                        class="text-[#c0392b]">*</span></label>
                                <input x-model="data.email" type="email" placeholder="nama@email.com"
                                    class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.email">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.email"></p>
                                </template>
                            </div>
                        </div>

                        <!-- BERKAS IDENTITAS -->
                        <div class="mt-8 pt-6 border-t border-[#d8cdb4]/50">
                            <h3 class="text-[16px] font-bold text-[#10131a] mb-4">Berkas Identitas</h3>
                            <div class="flex flex-col gap-3.5">
                                <template x-for="u in uploads.filter(x => ['ktp', 'foto'].includes(x.key))"
                                    :key="u.key">
                                    <div class="flex flex-col relative group" x-data="{ isDragging: false }"
                                        @dragenter.prevent="isDragging = true"
                                        @dragover.prevent="isDragging = true"
                                        @dragleave.prevent="if($event.target === $refs.overlay) isDragging = false"
                                        @drop.prevent="isDragging = false; handleFileChange($event, u.key)">
                                        
                                        <div x-ref="overlay" x-show="isDragging" class="absolute inset-0 z-20 cursor-copy" x-cloak></div>

                                        <div class="flex items-center gap-4 border-[1.5px] border-dashed rounded-[14px] py-[16px] px-5 transition-all duration-200 group-hover:bg-[#f0f8f2] group-hover:border-[#1b6e4c]"
                                            :class="isDragging ? 'border-[#1b6e4c] bg-[#f0f8f2] scale-[1.02] shadow-[0_0_15px_rgba(27,110,76,0.15)]' : 'border-[#d8cdb4] bg-[#faf6ec]'">
                                            <template x-if="data.previews[u.key]?.type === 'image'">
                                                <a :href="data.previews[u.key].url" target="_blank"
                                                    class="shrink-0 w-11 h-11 rounded-[11px] overflow-hidden border border-[#1b6e4c]/20 hover:opacity-80 transition-opacity"
                                                    title="Lihat Foto">
                                                    <img :src="data.previews[u.key].url"
                                                        class="w-full h-full object-cover">
                                                </a>
                                            </template>
                                            <template
                                                x-if="data.previews[u.key] && data.previews[u.key].type !== 'image'">
                                                <a :href="data.previews[u.key].url" :download="data.files[u.key]"
                                                    target="_blank"
                                                    class="shrink-0 w-11 h-11 rounded-[11px] bg-[#1b6e4c] text-white flex items-center justify-center hover:opacity-80 transition-opacity"
                                                    title="Download File">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path
                                                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                        <polyline points="14 2 14 8 20 8" />
                                                        <line x1="16" y1="13" x2="8" y2="13" />
                                                        <line x1="16" y1="17" x2="8" y2="17" />
                                                        <polyline points="10 9 9 9 8 9" />
                                                    </svg>
                                                </a>
                                            </template>
                                            <template x-if="!data.previews[u.key]">
                                                <div class="shrink-0 w-11 h-11 rounded-[11px] bg-white text-[#b8860b] flex items-center justify-center transition-colors"
                                                    x-html="u.icon"></div>
                                            </template>

                                            <div class="flex-1 min-w-0">
                                                <div class="text-[15px] font-bold text-[#10131a]">
                                                    <span x-text="u.title"></span>
                                                    <template x-if="!u.optional"><span
                                                            class="text-[#c0392b] ml-1">*</span></template>
                                                    <template x-if="u.optional"><span
                                                            class="text-[#9aa2b1] font-medium text-[13px] ml-1">(opsional)</span></template>
                                                </div>
                                                <div class="text-[13px] mt-0.5 truncate"
                                                    :class="data.files[u.key] ? 'text-[#1b6e4c]' : 'text-[#9aa2b1]'"
                                                    x-text="data.files[u.key] || 'Belum ada file'"></div>
                                                <div x-show="data.sizes[u.key]"
                                                    class="text-[11px] text-[#8a7f66] mt-0.5"
                                                    x-text="formatBytes(data.sizes[u.key])"></div>
                                            </div>

                                            <div class="flex items-center gap-3 shrink-0">
                                                <template x-if="data.files[u.key]">
                                                    <a :href="data.previews[u.key].url"
                                                        :download="data.previews[u.key].type !== 'image' ? data.files[u.key] : false"
                                                        target="_blank"
                                                        class="text-[13px] font-bold text-[#b8860b] hover:underline">Lihat</a>
                                                </template>

                                                <label
                                                    class="cursor-pointer px-4 py-2 rounded-xl text-[13px] font-bold transition-colors"
                                                    :class="data.files[u.key] ? 'border border-[#1b6e4c] text-[#1b6e4c] hover:bg-[#1b6e4c] hover:text-white' : 'bg-[#e0b53c] text-[#0a0c11] hover:bg-[#c9a030] shadow-sm'">
                                                    <span x-text="data.files[u.key] ? 'Ganti' : 'Pilih File'"></span>
                                                    <input type="file" class="hidden" accept="image/*,application/pdf"
                                                        @change="handleFileChange($event, u.key)">
                                                </label>
                                            </div>
                                        </div>
                                        <template x-if="showErr && errs[u.key]">
                                            <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5 px-2"
                                                x-text="errs[u.key]"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- STEP 2: KONTRIBUSI -->
                <div x-show="step === 2" x-cloak>
                    <h2 class="cz text-[22px] md:text-[26px] font-bold text-[#10131a]">Daftar Kontribusi / Inovasi</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Ceritakan karya dan dampak nyata yang telah
                        diberikan.</p>

                    <div class="flex flex-col gap-5">
                        <!-- KONTRIBUSI / INOVASI -->
                        <div class="flex flex-col gap-4 mt-2 pt-4 border-t border-[#d8cdb4]/50">
                            <div>
                                <label class="block text-[16px] font-bold mb-1">Kontribusi / Inovasi <span
                                        class="text-[#c0392b]">*</span></label>
                                <p class="text-[#6b7280] text-[13px]">Kontribusi atau inovasi yang pernah dilakukan
                                    terkait kategori yang diikuti</p>
                            </div>

                            <template x-for="(item, index) in data.capaianList" :key="index">
                                <div
                                    class="flex flex-col gap-4 bg-[#faf6ec] p-4 sm:p-5 rounded-xl border border-[#ece2ca]">
                                    <div class="flex items-center justify-between pb-3 border-b border-[#ece2ca]/60">
                                        <div class="font-bold text-[#1b6e4c] flex items-center gap-2.5">
                                            <span
                                                class="flex items-center justify-center w-7 h-7 rounded-full bg-[#1b6e4c]/10 text-[14px]"
                                                x-text="index + 1"></span>
                                            <span class="text-[15px]">Data Kontribusi / Inovasi</span>
                                        </div>
                                        <button x-show="data.capaianList.length > 1"
                                            @click="data.capaianList.splice(index, 1)" type="button"
                                            class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center bg-[#c0392b]/10 text-[#c0392b] hover:bg-[#c0392b] hover:text-white transition-colors"
                                            title="Hapus">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2v2">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex flex-col gap-3">
                                        <div class="mb-1">
                                            <label class="block text-[13.5px] font-bold mb-2">Judul Inovasi /
                                                Kontribusi <span class="text-[#c0392b] ml-1">*</span></label>
                                            <input x-model="item.judul" placeholder="Judul capaian/inovasi..."
                                                class="w-full h-[45px] px-4 border-[1.5px] border-[#d8cdb4] rounded-lg text-[15px] text-[#10131a] transition-all duration-200">
                                        </div>
                                        <div class="mb-1">
                                            <div class="flex justify-between items-end mb-2">
                                                <label class="block text-[13.5px] font-bold">Deskripsi <span
                                                        class="text-[#c0392b] ml-1">*</span></label>
                                                <span class="text-[12px] font-medium transition-colors duration-200"
                                                    :class="((item.deskripsi || '').split(' ').filter(w => w.trim().length > 0)).length >= 200 ? 'text-[#c0392b]' : 'text-[#8a7f66]'"
                                                    x-text="((item.deskripsi || '').split(' ').filter(w => w.trim().length > 0)).length + '/200 kata'">
                                                </span>
                                            </div>
                                            <textarea x-model="item.deskripsi" @input="
                                                    let val = item.deskripsi || '';
                                                    let words = val.split(' ').filter(w => w.trim().length > 0);
                                                    if (words.length > 200) {
                                                        item.deskripsi = words.slice(0, 200).join(' ');
                                                    }
                                                    $el.style.height = 'auto';
                                                    $el.style.height = $el.scrollHeight + 'px';
                                                "
                                                x-init="$nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' })"
                                                placeholder="Deskripsi (maksimal 200 kata per poin)..."
                                                :class="((item.deskripsi || '').split(' ').filter(w => w.trim().length > 0)).length >= 200 ? '!border-[#c0392b] !ring-1 !ring-[#c0392b]/30 bg-[#fdf5f5]' : ''"
                                                class="w-full min-h-[90px] p-4 border-[1.5px] border-[#d8cdb4] rounded-lg text-[14.5px] text-[#10131a] resize-none overflow-hidden md:overflow-y-auto md:max-h-[250px] leading-[1.55] transition-all duration-200"></textarea>
                                        </div>
                                        <div class="mb-1">
                                            <div class="flex justify-between items-end mb-2">
                                                <label class="block text-[13.5px] font-bold">Dampak &
                                                    Pencapaian <span class="text-[#c0392b] ml-1">*</span></label>
                                                <span class="text-[12px] font-medium transition-colors duration-200"
                                                    :class="((item.dampak || '').split(' ').filter(w => w.trim().length > 0)).length >= 200 ? 'text-[#c0392b]' : 'text-[#8a7f66]'"
                                                    x-text="((item.dampak || '').split(' ').filter(w => w.trim().length > 0)).length + '/200 kata'">
                                                </span>
                                            </div>
                                            <textarea x-model="item.dampak" @input="
                                                    let val = item.dampak || '';
                                                    let words = val.split(' ').filter(w => w.trim().length > 0);
                                                    if (words.length > 200) {
                                                        item.dampak = words.slice(0, 200).join(' ');
                                                    }
                                                    $el.style.height = 'auto';
                                                    $el.style.height = $el.scrollHeight + 'px';
                                                "
                                                x-init="$nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' })"
                                                placeholder="Contoh: menjangkau 1.200 anak, 8 desa, sejak 2019..."
                                                :class="((item.dampak || '').split(' ').filter(w => w.trim().length > 0)).length >= 200 ? '!border-[#c0392b] !ring-1 !ring-[#c0392b]/30 bg-[#fdf5f5]' : ''"
                                                class="w-full min-h-[90px] p-4 border-[1.5px] border-[#d8cdb4] rounded-lg text-[14.5px] text-[#10131a] resize-none overflow-hidden md:overflow-y-auto md:max-h-[250px] leading-[1.55] transition-all duration-200"></textarea>
                                        </div>

                                        <div class="mt-2">
                                            <label class="block text-[13.5px] font-bold mb-2">Bukti Dukung
                                                (<i>Evidences</i>) <template x-if="item.judul.trim()"><span
                                                        class="text-[#c0392b] ml-1">*</span></template></label>
                                            <div class="relative group cursor-pointer" x-data="{ isDragging: false }">
                                                <input type="file" multiple
                                                    accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                    @dragenter="isDragging = true"
                                                    @dragleave="isDragging = false"
                                                    @drop="isDragging = false"
                                                    @change="handleMultiFileChange($event, 'capaianList', index)">
                                                <div
                                                    :class="isDragging ? 'bg-[#f0f8f2] border-[#1b6e4c] scale-[1.02] shadow-[0_0_15px_rgba(27,110,76,0.15)]' : 'border-[#d8cdb4] bg-[#faf6ec]'"
                                                    class="flex flex-col items-center justify-center p-6 border-2 border-dashed rounded-xl group-hover:bg-[#f0f8f2] group-hover:border-[#1b6e4c] transition-all duration-300 text-center">
                                                    <div
                                                        class="w-12 h-12 mb-3 rounded-full bg-white shadow-sm flex items-center justify-center text-[#b8860b] group-hover:text-[#1b6e4c] group-hover:scale-110 transition-all duration-300">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <polyline points="17 8 12 3 7 8"></polyline>
                                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                                        </svg>
                                                    </div>
                                                    <p class="text-[14px] font-bold text-[#10131a] mb-1">Klik atau seret
                                                        file ke sini</p>
                                                    <p class="text-[12px] text-[#8a7f66]">Format JPG, PNG, PDF, DOC,
                                                        XLS, PPT, ZIP
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3"
                                                x-show="item.files && item.files.length > 0">
                                                <template x-for="(f, fIndex) in item.files" :key="fIndex">
                                                    <div
                                                        class="relative flex items-center gap-3 bg-white p-3 rounded-xl border border-[#ece2ca] shadow-sm hover:shadow-md hover:border-[#1b6e4c]/40 transition-all group">
                                                        <template x-if="f.type === 'image'">
                                                            <a :href="f.url" target="_blank"
                                                                class="w-10 h-10 rounded-lg overflow-hidden shrink-0 border border-[#ece2ca]">
                                                                <img :src="f.url"
                                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                            </a>
                                                        </template>
                                                        <template x-if="f.type !== 'image'">
                                                            <a :href="f.url" :download="f.name" target="_blank"
                                                                class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#1b6e4c] to-[#124d35] text-white flex items-center justify-center shrink-0 shadow-sm">
                                                                <svg width="18" height="18" viewBox="0 0 24 24"
                                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path
                                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                                    <polyline points="14 2 14 8 20 8" />
                                                                    <line x1="16" y1="13" x2="8" y2="13" />
                                                                    <line x1="16" y1="17" x2="8" y2="17" />
                                                                    <polyline points="10 9 9 9 8 9" />
                                                                </svg>
                                                            </a>
                                                        </template>

                                                        <div class="flex-1 min-w-0 pr-8">
                                                            <div class="text-[13px] font-bold text-[#10131a] truncate group-hover:text-[#1b6e4c] transition-colors"
                                                                x-text="f.name"></div>
                                                            <div
                                                                class="text-[11px] font-medium text-[#8a7f66] uppercase mt-0.5">
                                                                <span x-text="f.type"></span>
                                                                <span x-show="f.size" x-text="', ' + formatBytes(f.size)"></span>
                                                            </div>
                                                        </div>

                                                        <button type="button"
                                                            @click.prevent="removeMultiFile('capaianList', index, fIndex)"
                                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-[#9aa2b1] hover:text-white hover:bg-[#c0392b] hover:rotate-90 transition-all duration-300">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="showErr && errs.capaianList">
                                <p class="text-[#c0392b] text-[13px] font-semibold mt-1" x-text="errs.capaianList"></p>
                            </template>

                            <button @click="data.capaianList.push({ judul: '', deskripsi: '', dampak: '', files: [] })"
                                type="button"
                                class="inline-flex items-center justify-center gap-2 mt-2 self-center text-[#1b6e4c] font-bold text-[14px] px-4 py-2 bg-[#1b6e4c]/10 rounded-lg hover:bg-[#1b6e4c]/20 transition-colors">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Capaian / Inovasi
                            </button>
                        </div>

                        <!-- DAFTAR PENGHARGAAN -->
                        <div class="flex flex-col gap-4 mt-2 pt-4 border-t border-[#d8cdb4]/50">
                            <div>
                                <label class="block text-[16px] font-bold mb-1 text-[#10131a]">Daftar
                                    Penghargaan</label>
                                <p class="text-[#6b7280] text-[13px]">Lampirkan bukti penghargaan berupa
                                    sertifikat/piagam dalam bentuk <i>softcopy</i>.</p>
                            </div>

                            <template x-for="(item, index) in data.penghargaanList" :key="index">
                                <div
                                    class="flex flex-col gap-4 bg-[#faf6ec] p-4 sm:p-5 rounded-xl border border-[#ece2ca]">
                                    <div class="flex items-center justify-between pb-3 border-b border-[#ece2ca]/60">
                                        <div class="font-bold text-[#1b6e4c] flex items-center gap-2.5">
                                            <span
                                                class="flex items-center justify-center w-7 h-7 rounded-full bg-[#1b6e4c]/10 text-[14px]"
                                                x-text="index + 1"></span>
                                            <span class="text-[15px]">Data Penghargaan</span>
                                        </div>
                                        <button x-show="data.penghargaanList.length > 1"
                                            @click="data.penghargaanList.splice(index, 1)" type="button"
                                            class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center bg-[#c0392b]/10 text-[#c0392b] hover:bg-[#c0392b] hover:text-white transition-colors"
                                            title="Hapus">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2v2">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4">
                                        <div class="sm:col-span-9">
                                            <label class="block text-[13.5px] font-bold mb-2">Uraian Penghargaan</label>
                                            <input x-model="item.nama" placeholder="Nama penghargaan..."
                                                class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[14.5px] text-[#10131a] transition-all duration-200">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block text-[13.5px] font-bold mb-2">Tahun</label>
                                            <input x-model="item.tahun"
                                                @input="item.tahun = item.tahun.replace(/[^0-9]/g, '')" type="text"
                                                inputmode="numeric" pattern="[0-9]*" maxlength="4" placeholder="Tahun"
                                                class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[14.5px] text-[#10131a] transition-all duration-200">
                                        </div>
                                        <div class="sm:col-span-12 mt-2">
                                            <label class="block text-[13.5px] font-bold mb-2">Bukti Dukung (<i>Evidences</i>)
                                                <template x-if="item.nama.trim() || item.tahun.toString().trim()"><span
                                                        class="text-[#c0392b] ml-1">*</span></template></label>
                                            <div class="relative group cursor-pointer" x-data="{ isDragging: false }">
                                                <input type="file" multiple
                                                    accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                    @dragenter="isDragging = true"
                                                    @dragleave="isDragging = false"
                                                    @drop="isDragging = false"
                                                    @change="handleMultiFileChange($event, 'penghargaanList', index)">
                                                <div
                                                    :class="isDragging ? 'bg-[#f0f8f2] border-[#1b6e4c] scale-[1.02] shadow-[0_0_15px_rgba(27,110,76,0.15)]' : 'border-[#d8cdb4] bg-[#faf6ec]'"
                                                    class="flex flex-col items-center justify-center p-5 border-2 border-dashed rounded-xl group-hover:bg-[#f0f8f2] group-hover:border-[#1b6e4c] transition-all duration-300 text-center">
                                                    <div
                                                        class="w-10 h-10 mb-2 rounded-full bg-white shadow-sm flex items-center justify-center text-[#b8860b] group-hover:text-[#1b6e4c] group-hover:scale-110 transition-all duration-300">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <polyline points="17 8 12 3 7 8"></polyline>
                                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                                        </svg>
                                                    </div>
                                                    <p class="text-[13px] font-bold text-[#10131a] mb-0.5">Klik atau
                                                        seret file ke sini</p>
                                                    <p class="text-[11px] text-[#8a7f66]">Format JPG, PNG, PDF, DOC,
                                                        XLS, PPT, ZIP
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2"
                                                x-show="item.files && item.files.length > 0">
                                                <template x-for="(f, fIndex) in item.files" :key="fIndex">
                                                    <div
                                                        class="relative flex items-center gap-3 bg-white p-2.5 rounded-xl border border-[#ece2ca] shadow-sm hover:shadow-md hover:border-[#1b6e4c]/40 transition-all group">
                                                        <template x-if="f.type === 'image'">
                                                            <a :href="f.url" target="_blank"
                                                                class="w-9 h-9 rounded-lg overflow-hidden shrink-0 border border-[#ece2ca]">
                                                                <img :src="f.url"
                                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                            </a>
                                                        </template>
                                                        <template x-if="f.type !== 'image'">
                                                            <a :href="f.url" :download="f.name" target="_blank"
                                                                class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#1b6e4c] to-[#124d35] text-white flex items-center justify-center shrink-0 shadow-sm">
                                                                <svg width="16" height="16" viewBox="0 0 24 24"
                                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path
                                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                                    <polyline points="14 2 14 8 20 8" />
                                                                    <line x1="16" y1="13" x2="8" y2="13" />
                                                                    <line x1="16" y1="17" x2="8" y2="17" />
                                                                    <polyline points="10 9 9 9 8 9" />
                                                                </svg>
                                                            </a>
                                                        </template>

                                                        <div class="flex-1 min-w-0 pr-7">
                                                            <div class="text-[12px] font-bold text-[#10131a] truncate group-hover:text-[#1b6e4c] transition-colors"
                                                                x-text="f.name"></div>
                                                            <div
                                                                class="text-[10px] font-medium text-[#8a7f66] uppercase">
                                                                <span x-text="f.type"></span>
                                                                <span x-show="f.size" x-text="', ' + formatBytes(f.size)"></span>
                                                            </div>
                                                        </div>

                                                        <button type="button"
                                                            @click.prevent="removeMultiFile('penghargaanList', index, fIndex)"
                                                            class="absolute right-2 w-7 h-7 rounded-full flex items-center justify-center text-[#9aa2b1] hover:text-white hover:bg-[#c0392b] hover:rotate-90 transition-all duration-300">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="showErr && errs.penghargaanList">
                                <p class="text-[#c0392b] text-[13px] font-semibold mt-1" x-text="errs.penghargaanList">
                                </p>
                            </template>

                            <button @click="data.penghargaanList.push({ nama: '', tahun: '', files: [] })" type="button"
                                class="mt-1 inline-flex items-center justify-center gap-2 mt-2 self-center text-[#1b6e4c] font-bold text-[14px] px-4 py-2 bg-[#1b6e4c]/10 rounded-lg hover:bg-[#1b6e4c]/20 transition-colors">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Penghargaan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: TINJAU -->
                <div x-show="step === 3" x-cloak>
                    <div class="print-hide">
                        <h2 class="cz text-[26px] font-bold text-[#10131a]">Tinjau &amp; Kirim</h2>
                        <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Periksa kembali data Anda sebelum mengirim
                            pendaftaran.</p>
                    </div>

                    <!-- Kategori & Data Diri Card -->
                    <div class="mb-6 bg-white border border-[#ece2ca] rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-[#faf6ec] px-5 py-3 border-b border-[#ece2ca]">
                            <h3 class="font-bold text-[#1b6e4c] text-[15px] flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Informasi Personal
                            </h3>
                        </div>
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Kategori</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="(categories.find(c => c.id === data.kategori)?.name || '') + ' : ' + (categories.find(c => c.id === data.kategori)?.en || '')"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Nama
                                    Lengkap</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.namaNominee || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Tempat
                                    Lahir</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.wilayah || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Tanggal
                                    Lahir</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.tanggalLahir || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Jenis
                                    Kelamin</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.jenisKelamin || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Pendidikan</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.pendidikan || '—'"></span>
                            </div>
                            <div class="sm:col-span-2">
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Alamat</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.alamat || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Nomor
                                    WhatsApp</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.telp || '—'"></span>
                            </div>
                            <div>
                                <span
                                    class="block text-[12px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1">Alamat
                                    Email</span>
                                <span class="block text-[14.5px] text-[#10131a] font-medium"
                                    x-text="data.email || '—'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Pribadi -->
                    <div class="mb-6 bg-white border border-[#ece2ca] rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-[#faf6ec] px-5 py-3 border-b border-[#ece2ca]">
                            <h3 class="font-bold text-[#1b6e4c] text-[15px] flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                Dokumen Pribadi
                            </h3>
                        </div>
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- KTP -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-[#ece2ca] bg-[#faf6ec]/50">
                                <template x-if="data.previews.ktp">
                                    <a :href="data.previews.ktp.url" target="_blank"
                                        class="block w-12 h-12 rounded-lg overflow-hidden border border-[#d8cdb4] shrink-0 bg-white hover:opacity-80 transition-opacity">
                                        <img :src="data.previews.ktp.url" class="w-full h-full object-cover">
                                    </a>
                                </template>
                                <template x-if="!data.previews.ktp">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-[#ece2ca] flex items-center justify-center shrink-0 text-[#8a7f66]">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider">KTP</span>
                                    <span class="block text-[13.5px] text-[#10131a] font-medium truncate"
                                        x-text="data.files.ktp || 'Belum diunggah'"></span>
                                    <span x-show="data.sizes.ktp" class="block text-[11px] text-[#8a7f66] mt-0.5"
                                        x-text="formatBytes(data.sizes.ktp)"></span>
                                </div>
                            </div>
                            <!-- Foto Diri -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-[#ece2ca] bg-[#faf6ec]/50">
                                <template x-if="data.previews.foto">
                                    <a :href="data.previews.foto.url" target="_blank"
                                        class="block w-12 h-12 rounded-lg overflow-hidden border border-[#d8cdb4] shrink-0 bg-white hover:opacity-80 transition-opacity">
                                        <img :src="data.previews.foto.url" class="w-full h-full object-cover">
                                    </a>
                                </template>
                                <template x-if="!data.previews.foto">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-[#ece2ca] flex items-center justify-center shrink-0 text-[#8a7f66]">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider">Foto
                                        Diri</span>
                                    <span class="block text-[13.5px] text-[#10131a] font-medium truncate"
                                        x-text="data.files.foto || 'Belum diunggah'"></span>
                                    <span x-show="data.sizes.foto" class="block text-[11px] text-[#8a7f66] mt-0.5"
                                        x-text="formatBytes(data.sizes.foto)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Capaian & Inovasi -->
                    <div class="mb-6 bg-white border border-[#ece2ca] rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-[#faf6ec] px-5 py-3 border-b border-[#ece2ca]">
                            <h3 class="font-bold text-[#1b6e4c] text-[15px] flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                Capaian &amp; Inovasi
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <template x-for="(c, i) in data.capaianList" :key="i">
                                <template x-if="c.judul || c.deskripsi || c.dampak || (c.files && c.files.length)">
                                    <div class="bg-[#faf6ec]/30 border border-[#ece2ca] rounded-xl p-4">
                                        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-[#ece2ca]/60">
                                            <span
                                                class="w-6 h-6 rounded-full bg-[#1b6e4c]/10 text-[#1b6e4c] flex items-center justify-center text-[12px] font-bold shrink-0"
                                                x-text="i + 1"></span>
                                            <strong class="text-[14.5px] text-[#10131a] break-all"
                                                x-text="c.judul || 'Tanpa Judul'"></strong>
                                        </div>
                                        <div class="space-y-3">
                                            <template x-if="c.deskripsi">
                                                <div>
                                                    <span
                                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider mb-0.5">Deskripsi
                                                        Singkat</span>
                                                    <p class="text-[13.5px] text-[#10131a] leading-relaxed break-all"
                                                        x-text="c.deskripsi"></p>
                                                </div>
                                            </template>
                                            <template x-if="c.dampak">
                                                <div>
                                                    <span
                                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider mb-0.5">Dampak
                                                        &amp; Pencapaian</span>
                                                    <p class="text-[13.5px] text-[#10131a] leading-relaxed break-all"
                                                        x-text="c.dampak"></p>
                                                </div>
                                            </template>
                                            <template x-if="c.files && c.files.length > 0">
                                                <div>
                                                    <span
                                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1.5">Bukti
                                                        Dukung</span>
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="(f, fi) in c.files" :key="fi">
                                                            <a :href="f.url" :download="f.name" target="_blank"
                                                                class="flex items-center gap-2 p-2 max-w-[280px] w-full sm:w-auto bg-white border border-[#ece2ca] rounded-xl shadow-sm hover:border-[#1b6e4c]/40 transition-colors text-left group">
                                                                <template x-if="f.type === 'image'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg overflow-hidden border border-[#d8cdb4]">
                                                                        <img :src="f.url"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'pdf'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#c0392b]/10 text-[#c0392b] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                            <path
                                                                                d="M10 18v-4h2a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2h-2z">
                                                                            </path>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'word'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#2980b9]/10 text-[#2980b9] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                            <path d="M8 13h8"></path>
                                                                            <path d="M8 17h8"></path>
                                                                            <path d="M10 9h4"></path>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'zip'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#f39c12]/10 text-[#f39c12] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                                                            </path>
                                                                            <polyline
                                                                                points="3.27 6.96 12 12.01 20.73 6.96">
                                                                            </polyline>
                                                                            <line x1="12" y1="22.08" x2="12" y2="12">
                                                                            </line>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'doc'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#9aa2b1]/10 text-[#9aa2b1] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <div class="flex-1 min-w-0 pr-1">
                                                                    <div class="text-[12px] font-semibold text-[#10131a] truncate group-hover:text-[#1b6e4c] transition-colors"
                                                                        x-text="f.name"></div>
                                                                    <div
                                                                        class="text-[10px] font-medium text-[#8a7f66] uppercase">
                                                                        <span x-text="f.type"></span>
                                                                        <span x-show="f.size" x-text="', ' + formatBytes(f.size)"></span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template
                                x-if="data.capaianList.length === 0 || !data.capaianList.some(c => c.judul || c.deskripsi || c.dampak || (c.files && c.files.length))">
                                <div class="text-[13.5px] text-[#8a7f66] italic text-center py-2">Belum ada data
                                    capaian.</div>
                            </template>
                        </div>
                    </div>

                    <!-- Penghargaan -->
                    <div class="mb-6 bg-white border border-[#ece2ca] rounded-2xl overflow-hidden shadow-sm">
                        <div class="bg-[#faf6ec] px-5 py-3 border-b border-[#ece2ca]">
                            <h3 class="font-bold text-[#1b6e4c] text-[15px] flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12 15l-3.09 1.62.59-3.44L7 10.76l3.46-.5L12 7l1.54 3.26 3.46.5-2.5 2.42.59 3.44z">
                                    </path>
                                </svg>
                                Penghargaan
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <template x-for="(p, i) in data.penghargaanList" :key="i">
                                <template x-if="p.nama || p.tahun || (p.files && p.files.length)">
                                    <div class="bg-[#faf6ec]/30 border border-[#ece2ca] rounded-xl p-4">
                                        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-[#ece2ca]/60">
                                            <span
                                                class="w-6 h-6 rounded-full bg-[#1b6e4c]/10 text-[#1b6e4c] flex items-center justify-center text-[12px] font-bold shrink-0"
                                                x-text="i + 1"></span>
                                            <strong class="text-[14.5px] text-[#10131a] flex-1 min-w-0 break-all"
                                                x-text="p.nama || 'Tanpa Nama'"></strong>
                                            <template x-if="p.tahun">
                                                <span
                                                    class="px-2.5 py-1 rounded-md bg-[#1b6e4c] text-white text-[12px] font-bold shrink-0"
                                                    x-text="p.tahun"></span>
                                            </template>
                                        </div>
                                        <div>
                                            <template x-if="p.files && p.files.length > 0">
                                                <div>
                                                    <span
                                                        class="block text-[11px] font-bold text-[#8a7f66] uppercase tracking-wider mb-1.5">Sertifikat
                                                        / Piagam</span>
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="(f, fi) in p.files" :key="fi">
                                                            <a :href="f.url" :download="f.name" target="_blank"
                                                                class="flex items-center gap-2 p-2 max-w-[280px] w-full sm:w-auto bg-white border border-[#ece2ca] rounded-xl shadow-sm hover:border-[#1b6e4c]/40 transition-colors text-left group">
                                                                <template x-if="f.type === 'image'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg overflow-hidden border border-[#d8cdb4]">
                                                                        <img :src="f.url"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'pdf'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#c0392b]/10 text-[#c0392b] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                            <path
                                                                                d="M10 18v-4h2a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2h-2z">
                                                                            </path>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'word'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#2980b9]/10 text-[#2980b9] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                            <path d="M8 13h8"></path>
                                                                            <path d="M8 17h8"></path>
                                                                            <path d="M10 9h4"></path>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'zip'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#f39c12]/10 text-[#f39c12] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                                                            </path>
                                                                            <polyline
                                                                                points="3.27 6.96 12 12.01 20.73 6.96">
                                                                            </polyline>
                                                                            <line x1="12" y1="22.08" x2="12" y2="12">
                                                                            </line>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="f.type === 'doc'">
                                                                    <div
                                                                        class="w-8 h-8 shrink-0 rounded-lg bg-[#9aa2b1]/10 text-[#9aa2b1] flex items-center justify-center">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24"
                                                                            fill="none" stroke="currentColor"
                                                                            stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path
                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                            </path>
                                                                            <polyline points="14 2 14 8 20 8">
                                                                            </polyline>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <div class="flex-1 min-w-0 pr-1">
                                                                    <div class="text-[12px] font-semibold text-[#10131a] truncate group-hover:text-[#1b6e4c] transition-colors"
                                                                        x-text="f.name"></div>
                                                                    <div
                                                                        class="text-[10px] font-medium text-[#8a7f66] uppercase">
                                                                        <span x-text="f.type"></span>
                                                                        <span x-show="f.size" x-text="', ' + formatBytes(f.size)"></span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template
                                x-if="data.penghargaanList.length === 0 || !data.penghargaanList.some(p => p.nama || p.tahun || (p.files && p.files.length))">
                                <div class="text-[13.5px] text-[#8a7f66] italic text-center py-2">Belum ada data
                                    penghargaan.</div>
                            </template>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 mt-[22px] cursor-pointer">
                        <input type="checkbox" x-model="data.setuju" class="w-5 h-5 mt-0.5 shrink-0 accent-[#1b6e4c]">
                        <span class="text-[14px] text-[#4b5262] leading-[1.5]">Saya menyatakan bahwa seluruh data yang
                            diisi adalah benar dan menyetujui <a href="{{ route('landing') }}#syarat" target="_blank"
                                class="text-[#1b6e4c] font-bold hover:underline">syarat &amp; ketentuan</a> DPDRI
                            <i>AWARDS</i>
                            2026.</span>
                    </label>
                    <template x-if="showErr && errs.setuju">
                        <p class="text-[#c0392b] text-[13.5px] font-semibold mt-2.5">Anda harus menyetujui syarat &amp;
                            ketentuan.</p>
                    </template>
                </div>

                <!-- NAV BUTTONS -->
                <div class="flex gap-3.5 mt-8 pt-6 border-t border-[#eee6d4] print-hide"
                    :class="step > 0 ? 'justify-between' : 'justify-end'">
                    <template x-if="step > 0">
                        <button @click="back()"
                            class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 sm:gap-2 border-[1.5px] border-[#11563bff] bg-white text-[#11563bff] font-bold text-[14px] sm:text-[15px] px-2 sm:px-[26px] py-[13px] rounded-xl hover:bg-black/5 transition-colors cursor-pointer whitespace-nowrap">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                                <line x1="19" y1="12" x2="5" y2="12" />
                                <polyline points="12 19 5 12 12 5" />
                            </svg>Kembali
                        </button>
                    </template>

                    <template x-if="step < 3">
                        <button @click="next()"
                            class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 sm:gap-2 bg-[#11563bff] text-[#ffffff] font-extrabold text-[14px] sm:text-[15px] px-2 sm:px-[30px] py-[13px] rounded-xl shadow-[0_8px_24px_rgba(136,196,69,.3)] hover:bg-[#1b8d61ff] transition-colors cursor-pointer whitespace-nowrap">Lanjut
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </template>
                    <template x-if="step === 3">
                        <button @click="submitForm()" :disabled="isSubmitting"
                            class="flex-1 sm:flex-none justify-center inline-flex items-center gap-1.5 sm:gap-2 bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#424140] font-extrabold text-[14px] sm:text-[15px] px-2 sm:px-[32px] py-[13px] rounded-xl shadow-[0_10px_30px_rgba(224,181,60,.35)] hover:scale-105 transition-transform cursor-pointer whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim'"></span>
                            <svg x-show="!isSubmitting" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                class="shrink-0">
                                <path d="M22 2 11 13" />
                                <path d="M22 2 15 22l-4-9-9-4z" />
                            </svg>
                        </button>
                    </template>
                </div>

            </div>
        </div>
    </div>



    <script>
        const DB_NAME = 'NominasiDB';
        const STORE_NAME = 'nominasi_store';

        function openDB() {
            return new Promise((resolve, reject) => {
                const req = indexedDB.open(DB_NAME, 1);
                req.onupgradeneeded = (e) => e.target.result.createObjectStore(STORE_NAME);
                req.onsuccess = (e) => resolve(e.target.result);
                req.onerror = (e) => reject(e.target.error);
            });
        }

        async function saveToDB(key, val) {
            const db = await openDB();
            db.transaction(STORE_NAME, 'readwrite').objectStore(STORE_NAME).put(val, key);
        }

        async function getFromDB(key) {
            const db = await openDB();
            return new Promise((resolve) => {
                const req = db.transaction(STORE_NAME).objectStore(STORE_NAME).get(key);
                req.onsuccess = () => resolve(req.result);
            });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('nominasiForm', () => ({
                step: 0,
                submitted: false,
                isSubmitting: false,
                showErr: false,
                regId: '',
                submitTime: '',
                errs: {},
                data: {
                    website_url: '', kategori: '', namaNominee: '', wilayah: '', tanggalLahir: '', jenisKelamin: '', pendidikan: '', alamat: '', email: '', telp: '', judul: '', setuju: false,
                    capaianList: [{ judul: '', deskripsi: '', dampak: '', files: [] }],
                    penghargaanList: [{ nama: '', tahun: '', files: [] }],
                    files: { ktp: '', foto: '' },
                    sizes: { ktp: 0, foto: 0 },
                    previews: { ktp: '', foto: '' }
                },
                steps: [
                    { label: 'Kategori' }, { label: 'Data Diri' }, { label: 'Kontribusi' }, { label: 'Tinjau' }
                ],
                categories: [
                    { id: 'pendidikan', name: 'Bidang Pendidikan', en: 'Kategori Inovator Pendidikan Non Formal/Pendidikan Luar Sekolah', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>' },
                    { id: 'kesehatan', name: 'Bidang Kesehatan', en: 'Kategori Inovator Teknologi Kesehatan', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/><path d="M3.5 12h4l2-3 3 5 2-3h4"/></svg>' },
                    { id: 'pangan', name: 'Bidang Ketahanan Pangan', en: 'Kategori Penggerak Desa Mandiri Pangan', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/></svg>' },
                    { id: 'budaya', name: 'Bidang Seni dan Budaya', en: 'Kategori Pelestari Budaya Daerah', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>' }
                ],
                provinces: [
                    'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau',
                    'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
                    'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
                    'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
                ],
                uploads: [
                    { key: 'ktp', title: 'KTP', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18v14H3z"/><path d="M7 9h4M7 13h6"/></svg>', optional: false },
                    { key: 'foto', title: 'Foto Diri', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z"/><path d="M8.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM21 15l-5-5L5 21"/></svg>', optional: true }
                ],

                init() {
                    getFromDB('formData').then(saved => {
                        if (saved) {
                            this.data = saved;
                            if (!this.data.sizes) this.data.sizes = { ktp: 0, foto: 0 };
                            this.step = saved._step || 0;

                            if (this.data._rawFiles) {
                                Object.entries(this.data._rawFiles).forEach(([key, file]) => {
                                    if (file) {
                                        if (this.data.previews[key]) {
                                            this.data.previews[key].url = URL.createObjectURL(file);
                                        }
                                        if (file.size && (!this.data.sizes[key] || this.data.sizes[key] === 0)) {
                                            this.data.sizes[key] = file.size;
                                        }
                                    }
                                });
                            }
                            const regenMulti = (list) => {
                                list.forEach(item => {
                                    if (item.files) {
                                        item.files.forEach(f => {
                                            if (f.file) {
                                                f.url = URL.createObjectURL(f.file);
                                                if (!f.size && f.file.size) {
                                                    f.size = f.file.size;
                                                }
                                            }
                                        });
                                    }
                                });
                            };
                            if (this.data.capaianList) regenMulti(this.data.capaianList);
                            if (this.data.penghargaanList) regenMulti(this.data.penghargaanList);
                        }

                        const params = new URLSearchParams(window.location.search);
                        const kat = params.get('kategori');
                        if (kat && this.categories.some(c => c.id === kat)) {
                            this.data.kategori = kat;
                            this.step = 1;
                        }

                        Alpine.effect(() => {
                            JSON.stringify(this.data);
                            this.step;
                            const cloneData = Alpine.raw(this.data);
                            cloneData._step = this.step;
                            saveToDB('formData', cloneData).catch(e => console.error('Failed to save state:', e));
                        });
                    });
                },

                formatBytes(bytes) {
                    if (!bytes || bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
                },



                stepClasses(index) {
                    const done = index < this.step;
                    const active = index === this.step;
                    return {
                        circle: done ? 'bg-[#1b6e4c] border-[#1b6e4c] text-white' :
                            active ? 'bg-[#0a0c11] border-[#0a0c11] text-white shadow-[0_0_0_3px_rgba(136,196,69,.35)]' :
                                'bg-white border-[#d8cdb4] text-[#9aa2b1]',
                        text: active ? 'font-extrabold text-[#10131a]' : 'font-semibold text-[#8a7f66]'
                    };
                },

                jumpTo(target) {
                    if (target < this.step) {
                        this.step = target;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else if (target > this.step) {
                        if (!this.validate(this.step)) {
                            this.showErr = false;
                            this.step = target;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            this.showErr = true;
                        }
                    }
                },

                handleFileChange(e, key) {
                    const file = e.dataTransfer ? e.dataTransfer.files[0] : (e.target.files ? e.target.files[0] : null);
                    if (file) {
                        if (file.size > 50 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ukuran Terlalu Besar',
                                text: 'Maksimal ukuran file adalah 50 MB.',
                                confirmButtonColor: '#1b6e4c'
                            });
                            e.target.value = '';
                            return;
                        }
                        this.data.files[key] = file.name;
                        this.data.sizes[key] = file.size;
                        if (!this.data._rawFiles) this.data._rawFiles = {};
                        this.data._rawFiles[key] = file;
                        const ext = file.name.split('.').pop().toLowerCase();

                        const isImage = file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
                        const isPdf = file.type === 'application/pdf' || ext === 'pdf';

                        if (!isImage && !isPdf) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tipe File Tidak Valid',
                                text: 'Hanya file Gambar (JPG/PNG) atau PDF yang diizinkan.',
                                confirmButtonColor: '#1b6e4c'
                            });
                            e.target.value = '';
                            return;
                        }

                        const fileUrl = URL.createObjectURL(file);

                        if (file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            this.data.previews[key] = { type: 'image', url: fileUrl };
                        } else if (file.type === 'application/pdf' || ext === 'pdf') {
                            this.data.previews[key] = { type: 'pdf', url: fileUrl };
                        } else if (file.type.includes('word') || ['doc', 'docx'].includes(ext)) {
                            this.data.previews[key] = { type: 'word', url: fileUrl };
                        } else if (file.type.includes('zip') || ['zip', 'rar', '7z'].includes(ext)) {
                            this.data.previews[key] = { type: 'zip', url: fileUrl };
                        } else {
                            this.data.previews[key] = { type: 'doc', url: fileUrl };
                        }
                    } else {
                        this.data.files[key] = '';
                        this.data.sizes[key] = 0;
                        this.data.previews[key] = null;
                    }
                },

                handleMultiFileChange(e, listName, index) {
                    const files = Array.from(e.target.files);
                    if (!files.length) return;

                    let hasOversized = false;
                    let hasInvalidType = false;
                    const validFiles = files.filter(file => {
                        if (file.size > 50 * 1024 * 1024) {
                            hasOversized = true;
                            return false;
                        }

                        const ext = file.name.split('.').pop().toLowerCase();
                        const isImage = file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
                        const isPdf = file.type === 'application/pdf' || ext === 'pdf';
                        const isWord = file.type.includes('word') || ['doc', 'docx'].includes(ext);
                        const isExcel = file.type.includes('excel') || file.type.includes('spreadsheet') || ['xls', 'xlsx'].includes(ext);
                        const isPpt = file.type.includes('powerpoint') || file.type.includes('presentation') || ['ppt', 'pptx'].includes(ext);
                        const isZip = file.type.includes('zip') || file.type.includes('compressed') || ['zip', 'rar', '7z'].includes(ext);

                        if (!isImage && !isPdf && !isWord && !isExcel && !isPpt && !isZip) {
                            hasInvalidType = true;
                            return false;
                        }

                        return true;
                    });

                    if (hasOversized || hasInvalidType) {
                        let msg = '';
                        if (hasOversized) msg += 'File melebihi batas maksimal 50 MB. ';
                        if (hasInvalidType) msg += 'File memiliki format yang tidak diizinkan.';

                        Swal.fire({
                            icon: 'error',
                            title: 'File Tidak Valid',
                            text: msg.trim(),
                            confirmButtonColor: '#1b6e4c'
                        });
                    }

                    if (!validFiles.length) {
                        e.target.value = '';
                        return;
                    }

                    const list = this.data[listName];
                    if (!list[index].files) list[index].files = [];

                    validFiles.forEach(file => {
                        const ext = file.name.split('.').pop().toLowerCase();
                        const fileUrl = URL.createObjectURL(file);
                        let type = 'doc';
                        if (file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            type = 'image';
                        } else if (file.type === 'application/pdf' || ext === 'pdf') {
                            type = 'pdf';
                        } else if (file.type.includes('word') || ['doc', 'docx'].includes(ext)) {
                            type = 'word';
                        } else if (file.type.includes('zip') || ['zip', 'rar', '7z'].includes(ext)) {
                            type = 'zip';
                        }

                        list[index].files.push({
                            file: file,
                            name: file.name,
                            size: file.size,
                            type: type,
                            url: fileUrl
                        });
                    });

                    e.target.value = '';
                },

                removeMultiFile(listName, itemIndex, fileIndex) {
                    this.data[listName][itemIndex].files.splice(fileIndex, 1);
                },

                validate(step) {
                    this.errs = {};
                    let hasErr = false;
                    const d = this.data;

                    if (step === 0 && !d.kategori) { this.errs.kategori = true; hasErr = true; }

                    if (step === 1) {
                        if (!d.namaNominee?.trim()) { this.errs.namaNominee = "Wajib diisi."; hasErr = true; }
                        if (!d.wilayah?.trim()) { this.errs.wilayah = "Wajib diisi."; hasErr = true; }
                        if (!d.tanggalLahir?.trim()) { this.errs.tanggalLahir = "Wajib diisi."; hasErr = true; }
                        if (!d.jenisKelamin?.trim()) { this.errs.jenisKelamin = "Wajib diisi."; hasErr = true; }
                        if (!d.pendidikan?.trim()) { this.errs.pendidikan = "Wajib diisi."; hasErr = true; }
                        if (!d.alamat?.trim()) { this.errs.alamat = "Wajib diisi."; hasErr = true; }

                        if (!d.telp.trim()) {
                            this.errs.telp = "Wajib diisi."; hasErr = true;
                        } else if (!/^[0-9]+$/.test(d.telp.trim())) {
                            this.errs.telp = "Nomor WhatsApp hanya boleh berisi angka."; hasErr = true;
                        } else if (d.telp.trim().length < 9) {
                            this.errs.telp = "Nomor terlalu pendek."; hasErr = true;
                        } else if (!/^(62|0)/.test(d.telp.trim())) {
                            this.errs.telp = "Nomor WhatsApp harus diawali dengan 62 atau 0."; hasErr = true;
                        }

                        if (!d.email.trim()) {
                            this.errs.email = "Wajib diisi."; hasErr = true;
                        } else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(d.email)) {
                            this.errs.email = "Masukkan email yang valid."; hasErr = true;
                        }

                        if (!d.files.ktp) { this.errs.ktp = "KTP wajib diunggah."; hasErr = true; }
                    }
                    if (step === 2) {
                        let capaianInvalid = false;
                        d.capaianList.forEach(item => {
                            let wCount = (item.deskripsi || '').split(' ').filter(w => w.trim().length > 0).length;
                            let dCount = (item.dampak || '').split(' ').filter(w => w.trim().length > 0).length;
                            if (!item.judul.trim() || !item.deskripsi.trim() || !item.dampak?.trim() || wCount > 200 || dCount > 200 || !item.files || item.files.length === 0) {
                                capaianInvalid = true;
                            }
                        });
                        if (d.capaianList.length === 0 || capaianInvalid) {
                            this.errs.capaianList = "Harap lengkapi judul, deskripsi (maksimal 200 kata), dan dampak (maksimal 200 kata) pada setiap baris capaian/inovasi.";
                            hasErr = true;
                        }

                        let penghargaanInvalid = false;
                        let tahunInvalid = false;
                        d.penghargaanList.forEach((item, idx) => {
                            const tahunStr = item.tahun.toString().trim();
                            const isRowEmpty = !item.nama.trim() && !tahunStr && (!item.files || item.files.length === 0);
                            if (!isRowEmpty) {
                                if (!item.nama.trim() || !tahunStr || !item.files || item.files.length === 0) {
                                    penghargaanInvalid = true;
                                } else if (tahunStr.length !== 4) {
                                    tahunInvalid = true;
                                }
                            }
                        });
                        if (penghargaanInvalid) {
                            this.errs.penghargaanList = "Harap lengkapi uraian dan tahun untuk penghargaan yang ditambahkan.";
                            hasErr = true;
                        } else if (tahunInvalid) {
                            this.errs.penghargaanList = "Tahun penghargaan harus diisi dengan persis 4 digit angka (contoh: 2023).";
                            hasErr = true;
                        }
                    }
                    if (step === 3 && !d.setuju) { this.errs.setuju = true; hasErr = true; }

                    return hasErr;
                },

                next() {
                    if (this.validate(this.step)) {
                        this.showErr = true;
                        return;
                    }
                    this.showErr = false;
                    if (this.step < 3) this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                back() {
                    this.showErr = false;
                    if (this.step > 0) this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async submitForm() {
                    if (this.validate(3)) {
                        this.showErr = true;
                        return;
                    }
                    this.isSubmitting = true;
                    this.showErr = false;

                    if (this.data.website_url) {
                        this.isSubmitting = false;
                        return; // Silent fail for bots
                    }

                    try {
                        let formData = new FormData();

                        const primitives = ['kategori', 'namaNominee', 'wilayah', 'tanggalLahir', 'jenisKelamin', 'pendidikan', 'alamat', 'email', 'telp', 'judul'];
                        primitives.forEach(p => formData.append(p, this.data[p] || ''));

                        if (this.data._rawFiles && this.data._rawFiles.ktp) {
                            formData.append('ktp', this.data._rawFiles.ktp);
                        }
                        if (this.data._rawFiles && this.data._rawFiles.foto) {
                            formData.append('foto', this.data._rawFiles.foto);
                        }

                        this.data.capaianList.forEach((item, index) => {
                            formData.append(`capaianList[${index}][judul]`, item.judul);
                            formData.append(`capaianList[${index}][deskripsi]`, item.deskripsi);
                            formData.append(`capaianList[${index}][dampak]`, item.dampak);
                            if (item.files) {
                                item.files.forEach(f => {
                                    if (f.file) formData.append(`capaianFiles_${index}[]`, f.file);
                                });
                            }
                        });

                        this.data.penghargaanList.forEach((item, index) => {
                            formData.append(`penghargaanList[${index}][nama]`, item.nama);
                            formData.append(`penghargaanList[${index}][tahun]`, item.tahun);
                            if (item.files) {
                                item.files.forEach(f => {
                                    if (f.file) formData.append(`penghargaanFiles_${index}[]`, f.file);
                                });
                            }
                        });

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        Swal.fire({
                            title: 'Mengunggah Data...',
                            html: `
                                <div class="mt-4 mb-2 h-3 w-full bg-gray-200 rounded-full overflow-hidden">
                                    <div id="upload-progress-bar" class="h-full bg-[#1b6e4c] transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <div id="upload-progress-text" class="text-[13px] text-gray-500 font-medium">Mempersiapkan...</div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('nominasi.store') }}', true);
                        if (csrfToken) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        }
                        xhr.setRequestHeader('Accept', 'application/json');

                        xhr.upload.onprogress = (e) => {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                const bar = document.getElementById('upload-progress-bar');
                                const text = document.getElementById('upload-progress-text');
                                if (bar) bar.style.width = percentComplete + '%';
                                if (text) text.innerText = `${percentComplete}% (${this.formatBytes(e.loaded)} / ${this.formatBytes(e.total)})`;
                            }
                        };

                        xhr.onload = () => {
                            this.isSubmitting = false;
                            let result = {};
                            try {
                                result = JSON.parse(xhr.responseText);
                            } catch (e) {
                                result = { success: false, message: 'Respons server tidak valid.' };
                            }

                            if (xhr.status >= 200 && xhr.status < 300 && result.success) {
                                this.regId = result.regId;
                                this.submitTime = new Date().toLocaleString('id-ID', {
                                    day: '2-digit', month: 'short', year: 'numeric',
                                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                                }) + ' WIB';
                                this.submitted = true;
                                saveToDB('formData', null);
                                window.scrollTo({ top: 0, behavior: 'smooth' });

                                Swal.fire({
                                    title: 'Pendaftaran Terkirim!',
                                    text: 'Terima kasih, pendaftaran Anda telah berhasil disubmit.',
                                    icon: 'success',
                                    confirmButtonColor: '#1b6e4c'
                                }).then(() => {
                                    // Otomatis membuka dialog print/save as PDF
                                    window.print();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: result.message || 'Terjadi kesalahan saat menyimpan data.',
                                    icon: 'error',
                                    confirmButtonColor: '#1b6e4c'
                                });
                            }
                        };

                        xhr.onerror = () => {
                            this.isSubmitting = false;
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan koneksi saat mengirim data. Silakan periksa koneksi internet Anda.',
                                icon: 'error',
                                confirmButtonColor: '#1b6e4c'
                            });
                        };

                        xhr.send(formData);
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengirim data. Pastikan ukuran file tidak melebihi batas atau koneksi internet Anda stabil.',
                            icon: 'error',
                            confirmButtonColor: '#1b6e4c'
                        });
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</body>

</html>