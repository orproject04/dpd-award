<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nominasi - DPD Award 2026</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3ecdd; color: #10131a; }
        .cz { font-family: 'Cinzel', serif; }
        input:focus, textarea:focus { border-color: #1b6e4c !important; box-shadow: 0 0 0 3px rgba(27,110,76,.15) !important; outline: none; }
        @keyframes pop { 0% { transform: scale(.6); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
        .animate-pop { animation: pop .5s ease; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f3ecdd] text-[#10131a] antialiased min-h-screen relative" x-data="nominasiForm()">
    
    <div class="fixed inset-0 pointer-events-none z-[-1] bg-[radial-gradient(circle_at_50%_-10%,#fff8ea,#f3ecdd_45%)]"></div>

    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-[#0a0c11]/96 backdrop-blur-[10px] border-b border-[#e0b53c]/20">
        <div class="max-w-[900px] mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5 text-white/75 hover:text-white text-[14px] font-semibold transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
            </a>
            <a href="{{ route('landing') }}" class="cz text-[22px] font-extrabold tracking-wide text-white whitespace-nowrap">
                DPD <span class="text-[#88c445]">AWARD</span>
            </a>
            <span class="text-white/40 text-[13px] font-semibold">2026</span>
        </div>
    </header>

    <!-- FORM SHELL -->
    <template x-if="!submitted">
        <div class="max-w-[900px] mx-auto px-6 pt-11 pb-24">
            
            <div class="text-center mb-9">
                <span class="text-[#1b6e4c] text-[12px] font-extrabold tracking-[0.2em]">FORMULIR NOMINASI</span>
                <h1 class="cz text-[clamp(30px,5vw,46px)] font-extrabold uppercase mt-2">Ajukan <span class="text-[#b8860b]">Nominasi</span></h1>
            </div>

            <!-- STEPPER -->
            <div class="flex items-start mb-3.5">
                <template x-for="(s, index) in steps" :key="index">
                    <div class="flex items-start min-w-0" :class="index === steps.length - 1 ? 'shrink-0 basis-[64px]' : 'flex-1'">
                        <div class="flex flex-col items-center gap-2 shrink-0 w-16">
                            <div class="w-[42px] h-[42px] rounded-full flex items-center justify-center font-bold text-[15px] transition-all duration-300 border-2"
                                 :class="stepClasses(index).circle">
                                <template x-if="index < step">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </template>
                                <template x-if="index >= step">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <span class="text-[11.5px] text-center leading-[1.25]" :class="stepClasses(index).text" x-text="s.label"></span>
                        </div>
                        <template x-if="index < steps.length - 1">
                            <div class="flex-1 h-0.5 mt-5 rounded-sm transition-colors duration-300" :class="index < step ? 'bg-[#88c445]' : 'bg-[#e0d6bd]'"></div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- CARD -->
            <div class="bg-white border border-[#e8ddc4] rounded-[22px] shadow-[0_18px_48px_rgba(11,42,91,0.10)] p-[clamp(24px,4vw,44px)] mt-6">
                
                <!-- STEP 0: KATEGORI -->
                <div x-show="step === 0">
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Pilih Kategori</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Tentukan bidang yang paling sesuai dengan kontribusi yang dinominasikan.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <template x-for="c in categories" :key="c.id">
                            <div @click="data.kategori = c.id" 
                                 class="cursor-pointer border-2 rounded-2xl p-5 flex items-center gap-3.5 transition-all duration-200"
                                 :class="data.kategori === c.id ? 'border-[#1b6e4c] bg-[#f0f8f2]' : 'border-[#e8ddc4] bg-white'">
                                <div class="shrink-0 w-[46px] h-[46px] rounded-xl flex items-center justify-center transition-colors"
                                     :class="data.kategori === c.id ? 'bg-[#1b6e4c] text-white' : 'bg-[#f3ecdd] text-[#b8860b]'" x-html="c.icon"></div>
                                <div class="flex-1">
                                    <div class="cz text-[18px] font-bold text-[#10131a]" x-text="c.name"></div>
                                    <div class="text-[12.5px] text-[#8a7f66] font-semibold" x-text="c.en"></div>
                                </div>
                                <template x-if="data.kategori === c.id">
                                    <div class="shrink-0 w-6 h-6 rounded-full bg-[#1b6e4c] flex items-center justify-center">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <template x-if="showErr && errs.kategori">
                        <p class="text-[#c0392b] text-[13.5px] font-semibold mt-3.5">Silakan pilih salah satu kategori.</p>
                    </template>
                </div>

                <!-- STEP 1: DATA DIRI -->
                <div x-show="step === 1" x-cloak>
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Data Diri</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Isi data individu yang dinominasikan dan data Anda sebagai pengaju.</p>
                    
                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-[14px] font-bold mb-2">Nama Lengkap Nominee <span class="text-[#c0392b]">*</span></label>
                            <input x-model="data.namaNominee" type="text" placeholder="Nama individu yang dinominasikan" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                            <template x-if="showErr && errs.namaNominee"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.namaNominee"></p></template>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[14px] font-bold mb-2">Provinsi / Daerah <span class="text-[#c0392b]">*</span></label>
                                <input x-model="data.wilayah" type="text" placeholder="Contoh: Nusa Tenggara Timur" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.wilayah"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.wilayah"></p></template>
                            </div>
                            <div>
                                <label class="block text-[14px] font-bold mb-2">Hubungan dengan Nominee</label>
                                <input x-model="data.hubungan" type="text" placeholder="Contoh: Diri sendiri / Rekan" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                            </div>
                        </div>
                        <div class="h-px bg-[#eee6d4] my-0.5"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[14px] font-bold mb-2">Nama Pengaju <span class="text-[#c0392b]">*</span></label>
                                <input x-model="data.namaPengaju" type="text" placeholder="Nama Anda" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.namaPengaju"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.namaPengaju"></p></template>
                            </div>
                            <div>
                                <label class="block text-[14px] font-bold mb-2">Nomor WhatsApp <span class="text-[#c0392b]">*</span></label>
                                <input x-model="data.telp" type="tel" placeholder="08xxxxxxxxxx" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                                <template x-if="showErr && errs.telp"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.telp"></p></template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold mb-2">Alamat Email <span class="text-[#c0392b]">*</span></label>
                            <input x-model="data.email" type="email" placeholder="nama@email.com" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                            <template x-if="showErr && errs.email"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.email"></p></template>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: KONTRIBUSI -->
                <div x-show="step === 2" x-cloak>
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Detail Kontribusi</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Ceritakan karya dan dampak nyata yang telah diberikan.</p>
                    
                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-[14px] font-bold mb-2">Judul / Nama Karya <span class="text-[#c0392b]">*</span></label>
                            <input x-model="data.judul" type="text" placeholder="Contoh: Gerakan Literasi Pesisir" class="w-full h-[50px] px-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] transition-all duration-200">
                            <template x-if="showErr && errs.judul"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.judul"></p></template>
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold mb-2">Deskripsi Kontribusi <span class="text-[#c0392b]">*</span></label>
                            <textarea x-model="data.deskripsi" placeholder="Jelaskan latar belakang, kegiatan, dan pihak yang terdampak..." class="w-full min-h-[130px] p-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] resize-y leading-[1.55] transition-all duration-200"></textarea>
                            <template x-if="showErr && errs.deskripsi"><p class="text-[#c0392b] text-[13px] font-semibold mt-1.5" x-text="errs.deskripsi"></p></template>
                        </div>
                        <div>
                            <label class="block text-[14px] font-bold mb-2">Dampak & Pencapaian</label>
                            <textarea x-model="data.dampak" placeholder="Contoh: menjangkau 1.200 anak, 8 desa, sejak 2019..." class="w-full min-h-[90px] p-4 border-[1.5px] border-[#d8cdb4] rounded-xl text-[15px] text-[#10131a] resize-y leading-[1.55] transition-all duration-200"></textarea>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: BERKAS -->
                <div x-show="step === 3" x-cloak>
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Berkas Pendukung</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Unggah dokumen pendukung. Format PDF/JPG/PNG, maks. 5MB per file.</p>
                    
                    <div class="flex flex-col gap-3.5">
                        <template x-for="u in uploads" :key="u.key">
                            <div class="flex flex-col">
                                <label class="cursor-pointer flex items-center gap-4 border-[1.5px] border-dashed rounded-[14px] py-[18px] px-5 transition-all duration-200"
                                       :class="data.files[u.key] ? 'border-[#1b6e4c] bg-[#f0f8f2]' : 'border-[#d8cdb4] bg-[#faf6ec]'">
                                    <div class="shrink-0 w-11 h-11 rounded-[11px] flex items-center justify-center transition-colors"
                                         :class="data.files[u.key] ? 'bg-[#1b6e4c] text-white' : 'bg-white text-[#b8860b]'" x-html="u.icon"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[15px] font-bold text-[#10131a]">
                                            <span x-text="u.title"></span>
                                            <template x-if="u.optional"><span class="text-[#9aa2b1] font-medium text-[13px] ml-1">(opsional)</span></template>
                                        </div>
                                        <div class="text-[13px] mt-0.5 truncate" :class="data.files[u.key] ? 'text-[#1b6e4c]' : 'text-[#9aa2b1]'" x-text="data.files[u.key] || 'Klik untuk memilih file'"></div>
                                    </div>
                                    <span class="shrink-0 text-[13px] font-bold text-[#1b6e4c]" x-text="data.files[u.key] ? 'Ganti' : 'Pilih file'"></span>
                                    <input type="file" class="hidden" @change="handleFileChange($event, u.key)">
                                </label>
                                <template x-if="showErr && errs[u.key]">
                                    <p class="text-[#c0392b] text-[13px] font-semibold mt-1.5 px-2" x-text="errs[u.key]"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- STEP 4: TINJAU -->
                <div x-show="step === 4" x-cloak>
                    <h2 class="cz text-[26px] font-bold text-[#10131a]">Tinjau &amp; Kirim</h2>
                    <p class="text-[#6b7280] text-[15px] mt-1.5 mb-6">Periksa kembali data Anda sebelum mengirim nominasi.</p>
                    
                    <div class="bg-[#faf6ec] border border-[#ece2ca] rounded-2xl px-5 py-2">
                        <template x-for="(r, index) in reviewData" :key="index">
                            <div class="flex justify-between gap-5 py-3.5 border-b border-[#ece2ca] last:border-b-0">
                                <span class="text-[13.5px] text-[#8a7f66] font-semibold shrink-0" x-text="r.label"></span>
                                <span class="text-[14.5px] text-[#10131a] font-semibold text-right" x-text="r.value"></span>
                            </div>
                        </template>
                    </div>
                    
                    <label class="flex items-start gap-3 mt-[22px] cursor-pointer">
                        <input type="checkbox" x-model="data.setuju" class="w-5 h-5 mt-0.5 shrink-0 accent-[#1b6e4c]">
                        <span class="text-[14px] text-[#4b5262] leading-[1.5]">Saya menyatakan bahwa seluruh data yang diisi adalah benar dan menyetujui <a href="{{ route('landing') }}#syarat" target="_blank" class="text-[#1b6e4c] font-bold hover:underline">syarat &amp; ketentuan</a> DPD Award 2026.</span>
                    </label>
                    <template x-if="showErr && errs.setuju">
                        <p class="text-[#c0392b] text-[13.5px] font-semibold mt-2.5">Anda harus menyetujui syarat &amp; ketentuan.</p>
                    </template>
                </div>

                <!-- NAV BUTTONS -->
                <div class="flex justify-between gap-3.5 mt-8 pt-6 border-t border-[#eee6d4]">
                    <div>
                        <template x-if="step > 0">
                            <button @click="back()" class="inline-flex items-center gap-2 border-[1.5px] border-[#cfc4a8] bg-white text-[#4b5262] font-bold text-[15px] px-[26px] py-[13px] rounded-xl hover:bg-gray-50 transition-colors">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>Kembali
                            </button>
                        </template>
                    </div>
                    
                    <div>
                        <template x-if="step < 4">
                            <button @click="next()" class="inline-flex items-center gap-2 bg-[#88c445] text-[#0a0c11] font-extrabold text-[15px] px-[30px] py-[13px] rounded-xl shadow-[0_8px_24px_rgba(136,196,69,.3)] hover:bg-[#75a83a] transition-colors">Lanjut
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </template>
                        <template x-if="step === 4">
                            <button @click="submitForm()" class="inline-flex items-center gap-2 bg-gradient-to-br from-[#f5da8b] via-[#e0b53c] to-[#b8860b] text-[#10131a] font-extrabold text-[15px] px-[32px] py-[13px] rounded-xl shadow-[0_10px_30px_rgba(224,181,60,.35)] hover:scale-105 transition-transform">Kirim Nominasi
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></svg>
                            </button>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </template>

    <!-- SUCCESS -->
    <template x-if="submitted">
        <div class="max-w-[640px] mx-auto px-6 pt-[70px] pb-[90px] text-center" x-cloak>
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#88c445] to-[#1b6e4c] flex items-center justify-center mx-auto mb-7 shadow-[0_16px_44px_rgba(27,110,76,.4)] animate-pop">
                <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h1 class="cz text-[clamp(30px,5vw,44px)] font-extrabold uppercase text-[#10131a]">Nominasi <span class="text-[#1b6e4c]">Terkirim</span></h1>
            <p class="text-[#4b5262] text-[17px] leading-[1.65] mt-4 max-w-[480px] mx-auto">Terima kasih telah berpartisipasi. Nominasi Anda telah kami terima dan akan diverifikasi oleh tim komite.</p>
            
            <div class="inline-block mt-6 bg-white border border-[#e8ddc4] rounded-2xl py-4 px-7 shadow-[0_8px_24px_rgba(11,42,91,.08)]">
                <div class="text-[12px] text-[#8a7f66] font-bold tracking-[0.12em]">NOMOR REGISTRASI</div>
                <div class="cz text-[26px] font-bold text-[#b8860b] mt-1 tracking-wide" x-text="regId"></div>
            </div>
            
            <div class="mt-8">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 bg-[#0a0c11] text-white font-bold text-[15px] px-[30px] py-[14px] rounded-xl hover:bg-black transition-colors">Kembali ke Beranda</a>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('nominasiForm', () => ({
                step: 0,
                submitted: false,
                showErr: false,
                regId: '',
                errs: {},
                data: {
                    kategori: '', namaNominee: '', wilayah: '', hubungan: '', namaPengaju: '', 
                    email: '', telp: '', judul: '', deskripsi: '', dampak: '', setuju: false,
                    files: { ktp: '', porto: '', foto: '' }
                },
                steps: [
                    { label: 'Kategori' }, { label: 'Data Diri' }, { label: 'Kontribusi' }, { label: 'Berkas' }, { label: 'Tinjau' }
                ],
                categories: [
                    { id: 'pendidikan', name: 'Pendidikan', en: 'Education', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>' },
                    { id: 'kesehatan', name: 'Kesehatan', en: 'Health', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/><path d="M3.5 12h4l2-3 3 5 2-3h4"/></svg>' },
                    { id: 'lingkungan', name: 'Lingkungan', en: 'Environment', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/></svg>' },
                    { id: 'kepemudaan', name: 'Kepemudaan', en: 'Youth', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>' },
                    { id: 'sosial', name: 'Sosial Budaya', en: 'Social & Culture', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>' },
                    { id: 'ekonomi', name: 'Ekonomi Kreatif', en: 'Creative Economy', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8-5.8 1.9 5.8 1.9L12 18.4l1.9-5.8 5.8-1.9-5.8-1.9z"/><path d="M5 3v4M19 17v4M3 5h4M17 19h4"/></svg>' }
                ],
                uploads: [
                    { key: 'ktp', title: 'KTP / Identitas Nominee', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18v14H3z"/><path d="M7 9h4M7 13h6"/></svg>', optional: false },
                    { key: 'porto', title: 'Portofolio / Dokumentasi', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>', optional: false },
                    { key: 'foto', title: 'Foto Nominee', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z"/><path d="M8.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM21 15l-5-5L5 21"/></svg>', optional: true }
                ],

                init() {
                    const params = new URLSearchParams(window.location.search);
                    const kat = params.get('kategori');
                    if(kat && this.categories.some(c => c.id === kat)) {
                        this.data.kategori = kat;
                    }
                },

                get reviewData() {
                    const catName = this.categories.find(c => c.id === this.data.kategori)?.name || '—';
                    const fileCount = Object.values(this.data.files).filter(Boolean).length;
                    return [
                        { label: 'Kategori', value: catName },
                        { label: 'Nama Nominee', value: this.data.namaNominee || '—' },
                        { label: 'Daerah', value: this.data.wilayah || '—' },
                        { label: 'Pengaju', value: this.data.namaPengaju || '—' },
                        { label: 'Kontak', value: (this.data.email || '—') + (this.data.telp ? ' · ' + this.data.telp : '') },
                        { label: 'Judul Karya', value: this.data.judul || '—' },
                        { label: 'Berkas Terlampir', value: fileCount + ' file' },
                    ];
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

                handleFileChange(e, key) {
                    const file = e.target.files[0];
                    if(file) this.data.files[key] = file.name;
                },

                validate(step) {
                    this.errs = {};
                    let hasErr = false;
                    const d = this.data;
                    
                    if(step === 0 && !d.kategori) { this.errs.kategori = true; hasErr = true; }
                    if(step === 1) {
                        if(!d.namaNominee.trim()) { this.errs.namaNominee = "Wajib diisi."; hasErr = true; }
                        if(!d.wilayah.trim()) { this.errs.wilayah = "Wajib diisi."; hasErr = true; }
                        if(!d.namaPengaju.trim()) { this.errs.namaPengaju = "Wajib diisi."; hasErr = true; }
                        
                        if(!d.telp.trim()) { 
                            this.errs.telp = "Wajib diisi."; hasErr = true; 
                        } else if(!/^[0-9]+$/.test(d.telp.trim())) {
                            this.errs.telp = "Nomor WhatsApp hanya boleh berisi angka."; hasErr = true;
                        } else if(d.telp.trim().length < 9) {
                            this.errs.telp = "Nomor terlalu pendek."; hasErr = true;
                        } else if(!/^(62|0)/.test(d.telp.trim())) {
                            this.errs.telp = "Nomor WhatsApp harus diawali dengan 62 atau 0."; hasErr = true;
                        }

                        if(!d.email.trim()) {
                            this.errs.email = "Wajib diisi."; hasErr = true;
                        } else if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(d.email)) { 
                            this.errs.email = "Masukkan email yang valid."; hasErr = true; 
                        }
                    }
                    if(step === 2) {
                        if(!d.judul.trim()) { this.errs.judul = "Wajib diisi."; hasErr = true; }
                        if(!d.deskripsi.trim()) { this.errs.deskripsi = "Wajib diisi."; hasErr = true; }
                    }
                    if(step === 3) {
                        if(!d.files.ktp) { this.errs.ktp = "KTP wajib diunggah."; hasErr = true; }
                        if(!d.files.porto) { this.errs.porto = "Portofolio wajib diunggah."; hasErr = true; }
                    }
                    if(step === 4 && !d.setuju) { this.errs.setuju = true; hasErr = true; }
                    
                    return hasErr;
                },

                next() {
                    if(this.validate(this.step)) {
                        this.showErr = true;
                        return;
                    }
                    this.showErr = false;
                    if(this.step < 4) this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                back() {
                    this.showErr = false;
                    if(this.step > 0) this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                submitForm() {
                    if(this.validate(4)) {
                        this.showErr = true;
                        return;
                    }
                    this.regId = 'DPD-2026-' + Math.floor(1000 + Math.random() * 9000);
                    this.submitted = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }));
        });
    </script>
</body>
</html>
