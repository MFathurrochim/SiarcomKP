<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIARCOM - Dashboard Media Monitoring</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS v4 & FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        .berita-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 8px;
        }

        .berita-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        .berita-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #27B78F, #4ECDB8, #12B4C9);
            border-radius: 9999px;
            cursor: pointer;
        }

        .berita-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #1f9473, #3ea897, #0e8f9f);
        }
    </style>

    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <!-- Wrapper Utama Alpine.js State -->
    <div x-data="{
        sidebarOpen: true,
        tambahModalOpen: false,
        editModalOpen: false,
        tahunModalOpen: false,
        pasteModalOpen: false,
        inputTahunBaru: '',
        activeEditId: null,
        submitTahunBaru() {
            if (this.inputTahunBaru && !isNaN(this.inputTahunBaru) && this.inputTahunBaru.trim() !== '') {
                window.location.href = '?tahun=' + this.inputTahunBaru.trim();
            }
        }
    }" class="flex h-screen w-screen overflow-hidden bg-slate-50">

        <!-- SIDEBAR UTAMA -->
        @include('components.sidebar')

        <!-- Container Konten Utama -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0 bg-slate-50">

            <!-- TOP NAVBAR -->
            <header
                class="min-h-[64px] bg-white border-b border-slate-200/80 px-4 py-2 sm:px-6 z-20 shrink-0 relative shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-2">

                <!-- SISI KIRI: JUDUL DASHBOARD & LOGO SIARCOM MOBILE -->
                <div class="flex items-center justify-between shrink-0 gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-1.5 bg-teal-50 rounded-lg border border-teal-100/80 text-[#27B78F]">
                            <i class="fa-solid fa-newspaper text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-sm sm:text-base font-black tracking-tight text-slate-900 leading-tight">
                                Dashboard Media Monitoring
                            </h1>
                        </div>
                    </div>

                    <!-- Logo SIARCOM Mobile -->
                    <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM"
                        class="h-9 w-auto object-contain lg:hidden">
                </div>

                <!-- SISI KANAN: GLOBAL FILTER BAR COMPACT + LOGO DESKTOP -->
                <div class="flex items-center justify-between lg:justify-end gap-3 flex-1 min-w-0">

                    <form id="formGlobalFilter" method="GET" action="{{ route('pages.berita.index') }}"
                        class="flex items-center gap-2 flex-wrap lg:flex-nowrap">

                        <!-- 1. MASTER FILTER TAHUN -->
                        <div class="p-[1.5px] rounded-lg bg-gradient-to-r from-[#27B78F] to-[#12B4C9] shadow-sm">
                            <div class="flex items-center bg-white px-2 py-1 rounded-[7px]">
                                <i class="fa-solid fa-calendar-days text-[#27B78F] text-[11px] mr-1"></i>
                                <select name="tahun" id="globalTahun"
                                    onchange="document.getElementById('formGlobalFilter').submit()"
                                    class="text-[11px] font-extrabold text-black border-0 bg-transparent focus:outline-none cursor-pointer pr-1">
                                    @foreach ($daftarTahun as $thn)
                                        <option value="{{ $thn }}" class="text-black font-semibold"
                                            {{ (string) $tahunTerpilih === (string) $thn ? 'selected' : '' }}>
                                            {{ $thn }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Tombol Tambah Tahun -->
                                <button type="button" @click="tahunModalOpen = true; inputTahunBaru = '';"
                                    class="p-1 bg-teal-50 hover:bg-[#27B78F] text-[#27B78F] hover:text-white rounded-md transition cursor-pointer ml-1.5"
                                    title="Tambah Tahun Liputan Baru">
                                    <i class="fa-solid fa-plus text-[9px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- GARIS PEMBATAS VERTIKAL -->
                        <div class="h-5 w-px bg-slate-300 mx-0.5 hidden sm:block"></div>

                        <!-- 2. FILTER BULAN -->
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold text-slate-900 text-center ">Filter Bulan</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-2 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-clock text-black text-[11px] mr-1"></i>
                                    <select name="bulan" id="globalBulan"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[11px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[110px] truncate">
                                        <option value="all" {{ request('bulan', 'all') == 'all' ? 'selected' : '' }}>
                                            Semua Bulan</option>
                                        @php
                                            $listBulanIndo = [
                                                '1' => 'Januari',
                                                '2' => 'Februari',
                                                '3' => 'Maret',
                                                '4' => 'April',
                                                '5' => 'Mei',
                                                '6' => 'Juni',
                                                '7' => 'Juli',
                                                '8' => 'Agustus',
                                                '9' => 'September',
                                                '10' => 'Oktober',
                                                '11' => 'November',
                                                '12' => 'Desember',
                                            ];
                                        @endphp
                                        @foreach ($listBulanIndo as $val => $bln)
                                            <option value="{{ $val }}"
                                                {{ (string) request('bulan') === (string) $val ? 'selected' : '' }}>
                                                {{ $bln }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 3. FILTER TONE -->
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold text-slate-900 text-center ">Filter Tone</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-2 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-face-smile text-black text-[11px] mr-1"></i>
                                    <select name="tone" id="globalTone"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[11px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[105px] truncate">
                                        <option value="all" {{ request('tone', 'all') == 'all' ? 'selected' : '' }}>
                                            Semua Tone</option>
                                        <option value="Positif" {{ request('tone') == 'Positif' ? 'selected' : '' }}>
                                            Positif</option>
                                        <option value="Netral" {{ request('tone') == 'Netral' ? 'selected' : '' }}>
                                            Netral
                                        </option>
                                        <option value="Negatif" {{ request('tone') == 'Negatif' ? 'selected' : '' }}>
                                            Negatif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4. FILTER TOPIK -->
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold text-slate-900 text-center ">Filter Topik</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-2 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-tags text-black text-[11px] mr-1"></i>
                                    <select name="topik" id="globalTopik"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[11px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[110px] truncate">
                                        <option value="all"
                                            {{ request('topik', 'all') == 'all' ? 'selected' : '' }}>
                                            Semua Topik</option>
                                        <option value="Operasi" {{ request('topik') == 'Operasi' ? 'selected' : '' }}>
                                            Operasi</option>
                                        <option value="CSR" {{ request('topik') == 'CSR' ? 'selected' : '' }}>CSR
                                        </option>
                                        <option value="Inovasi" {{ request('topik') == 'Inovasi' ? 'selected' : '' }}>
                                            Inovasi</option>
                                        <option value="Apresiasi"
                                            {{ request('topik') == 'Apresiasi' ? 'selected' : '' }}>
                                            Apresiasi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 5. FILTER SIFAT BERITA -->
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-semibold text-slate-900 text-center ">Filter Sifat
                                Berita</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-2 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-sliders text-black text-[11px] mr-1"></i>
                                    <select name="sifat_berita" id="globalSifat"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[11px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[110px] truncate">
                                        <option value="all"
                                            {{ request('sifat_berita', 'all') == 'all' ? 'selected' : '' }}>Semua Sifat
                                        </option>
                                        <option value="Internal"
                                            {{ request('sifat_berita') == 'Internal' ? 'selected' : '' }}>Internal
                                        </option>
                                        <option value="Eksternal"
                                            {{ request('sifat_berita') == 'Eksternal' ? 'selected' : '' }}>Eksternal
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL RESET FILTER -->
                        @if (request()->hasAny(['bulan', 'tone', 'topik', 'sifat_berita']) &&
                                (request('bulan') != 'all' ||
                                    request('tone') != 'all' ||
                                    request('topik') != 'all' ||
                                    request('sifat_berita') != 'all'))
                            <a href="{{ route('pages.berita.index', ['tahun' => $tahunTerpilih]) }}"
                                class="px-2.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-[10px] font-bold transition flex items-center gap-1 cursor-pointer shadow-sm shrink-0"
                                title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-[9px]"></i>
                                <span>Reset</span>
                            </a>
                        @endif

                    </form>

                    <!-- LOGO SIARCOM DESKTOP -->
                    <div class="hidden lg:flex items-center pl-2 border-l border-slate-200 shrink-0">
                        <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM"
                            class="h-10 w-auto object-contain">
                    </div>

                </div>

                <!-- BORDER BOTTOM GRADASI SIARCOM -->
                <div
                    class="w-full h-[2.5px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] absolute bottom-0 left-0">
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-1 berita-scrollbar bg-slate-50/50">

                <!-- Flash Message Notification -->
                @if (session('success') || session('error') || session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="fixed top-20 right-6 z-50 p-4 rounded-xl shadow-lg text-white font-medium flex items-center gap-3 transition-all duration-300 {{ session('success') ? 'bg-emerald-500' : (session('error') ? 'bg-rose-500' : 'bg-amber-500') }}">
                        <i
                            class="fa-solid {{ session('success') ? 'fa-circle-check' : (session('error') ? 'fa-circle-xmark' : 'fa-circle-info') }}"></i>
                        <span>{{ session('success') ?? (session('error') ?? session('info')) }}</span>
                        <button @click="show = false" class="text-white/80 hover:text-white ml-2">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                <!-- SECTION CARD: Widget Ringkasan Card -->
                <div class="w-full">
                    @include('pages.berita.partials.card')
                </div>

                <!-- SECTION 1: 4 Widget Diagram Analitik -->
                <div class="w-full">
                    @include('pages.berita.partials.charts')
                </div>

                <!-- SECTION 2: Tabel Utama Data Berita, Filter, Search & Delete Overlay -->
                <div class="w-full">
                    @include('pages.berita.partials.table')
                </div>


            </main>
        </div>

        <!-- MODAL 1: Form Pop-Up Tambah Berita Baru -->
        <div x-show="tambahModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
            x-transition x-cloak>
            <div @click.away="tambahModalOpen = false"
                class="bg-white w-full max-w-xl rounded-2xl shadow-xl overflow-hidden p-6 relative max-h-[90vh] overflow-y-auto berita-scrollbar">
                @include('pages.berita.partials.tambah')
            </div>
        </div>

        <!-- MODAL 2: Form Pop-Up Edit Data Berita -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
            x-transition x-cloak>
            <div @click.away="editModalOpen = false"
                class="w-full max-w-2xl max-h-[90vh] overflow-y-auto berita-scrollbar relative">
                <div id="editModalContent"></div>
            </div>
        </div>

        <!-- MODAL 3: Pop-Up Tambah Tahun Berita Baru -->
        <div x-show="tahunModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs" x-transition
            x-cloak>
            <div @click.away="tahunModalOpen = false"
                class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden p-6 relative border border-slate-100">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-[#27B78F]"></i> Tambah Tahun Berita
                    </h3>
                    <button @click="tahunModalOpen = false" class="text-slate-400 hover:text-slate-600 transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Masukkan Tahun
                            Baru</label>
                        <input type="number" x-model="inputTahunBaru" @keydown.enter="submitTahunBaru()"
                            placeholder="Contoh: 2026"
                            class="w-full text-xs font-medium bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-emerald-500 focus:bg-white transition shadow-inner">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button @click="tahunModalOpen = false" type="button"
                            class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button @click="submitTahunBaru()" type="button"
                            class="px-4 py-2 bg-gradient-to-r from-[#27B78F] to-[#4ECDB8] text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-200 hover:opacity-90 transition cursor-pointer">
                            Terapkan
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @stack('scripts')

</body>

</html>
