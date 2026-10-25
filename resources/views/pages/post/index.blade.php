<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIARCOM - Dashboard Monitoring Sosial Media</title>

    <!-- Tailwind v4 & FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar untuk panel konten */
        .sosmed-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 8px;
        }

        .sosmed-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        .sosmed-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #27B78F, #4ECDB8, #12B4C9);
            border-radius: 9999px;
            cursor: pointer;
        }

        .sosmed-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #1f9473, #3ea897, #0e8f9f);
        }
    </style>

    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50 font-sans antialiased text-slate-800">

    <!-- Root data Alpine -->
    <div x-data="{
        sidebarOpen: true,
        tambahModalOpen: false,
        editModalOpen: false,
        tahunModalOpen: false,
        pasteModalOpen: false,
        linkModalOpen: false,
        inputTahunBaru: '',
        editModalHtml: '',
        loadingEdit: false,
    
        // Fungsi untuk mengaplikasikan tahun baru ke URL
        submitTahunBaru() {
            if (!this.inputTahunBaru || isNaN(this.inputTahunBaru)) {
                alert('Masukkan tahun berupa angka yang valid!');
                return;
            }
            let urlParams = new URLSearchParams(window.location.search);
            urlParams.set('tahun', this.inputTahunBaru);
            window.location.search = urlParams.toString();
        },
    
        // Fungsi untuk mengambil data edit via AJAX
        fetchEditData(id) {
            this.editModalOpen = true;
            this.loadingEdit = true;
            this.editModalHtml = '';
    
            fetch('/sosmed-monitoring/edit/' + id)
                .then(res => res.text())
                .then(html => {
                    this.editModalHtml = html;
                    this.loadingEdit = false;
                    this.$nextTick(() => {
                        if (typeof toggleEditScrapeButton === 'function') {
                            toggleEditScrapeButton();
                        }
                    });
                })
                .catch(err => {
                    this.loadingEdit = false;
                    this.editModalHtml = '<div class=\'p-6 text-center text-rose-500 font-medium\'>Gagal memuat data form edit.</div>';
                });
        }
    }" @buka-modal-edit.window="fetchEditData($event.detail.id)"
        class="flex h-screen w-screen overflow-hidden bg-slate-50">

        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0 bg-slate-50">

            <!-- TOP NAVBAR ULTRA CLEAN & COMPACT -->
            <header
                class="min-h-[64px] bg-white border-b border-slate-200/80 px-4 py-2 sm:px-6 z-20 shrink-0 relative shadow-xs flex flex-col xl:flex-row xl:items-center justify-between gap-2">

                <!-- SISI KIRI: JUDUL DASHBOARD-->
                <div class="flex items-center justify-between shrink-0 gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-1.5 bg-teal-50 rounded-lg border border-teal-100/80 text-[#27B78F]">
                            <i class="fa-solid fa-share-nodes text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-sm sm:text-base font-black tracking-tight text-slate-900 leading-tight">
                                Dashboard Monitoring Sosial Media
                            </h1>
                        </div>
                    </div>

                    <!-- Logo SIARCOM Layar Kecil -->
                    <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM"
                        class="h-8 w-auto object-contain xl:hidden">
                </div>

                <!-- SISI KANAN: GLOBAL FILTER BAR COMPACT + LOGO DESKTOP -->
                <div
                    class="flex items-center justify-between xl:justify-end gap-2 flex-1 min-w-0 overflow-x-auto sosmed-scrollbar pb-1 xl:pb-0">

                    <form id="formGlobalFilter" method="GET" action="{{ route('pages.post.index') }}"
                        class="flex items-center gap-1.5 flex-nowrap shrink-0">

                        <!-- 1. MASTER FILTER TAHUN (Lengkap dengan Label di Atasnya) -->
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-semibold text-slate-900 text-center">Filter Tahun</span>
                            <div class="p-[1.5px] rounded-lg bg-gradient-to-r from-[#27B78F] to-[#12B4C9] shadow-sm">
                                <div class="flex items-center bg-white px-1.5 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-calendar-days text-[#27B78F] text-[10px] mr-1"></i>
                                    <select name="tahun" id="globalTahun"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[10px] font-extrabold text-black border-0 bg-transparent focus:outline-none cursor-pointer pr-1">
                                        @foreach ($daftarTahun as $thn)
                                            <option value="{{ $thn }}" class="text-black font-semibold"
                                                {{ (string) $tahunTerpilih === (string) $thn ? 'selected' : '' }}>
                                                {{ $thn }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Tombol Tambah Tahun -->
                                    <button type="button" @click="tahunModalOpen = true; inputTahunBaru = '';"
                                        class="p-1 bg-teal-50 hover:bg-[#27B78F] text-[#27B78F] hover:text-white rounded-md transition cursor-pointer ml-1"
                                        title="Tambah Tahun Postingan Baru">
                                        <i class="fa-solid fa-plus text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- GARIS PEMBATAS VERTIKAL -->
                        <div class="h-4 w-px bg-slate-300 mx-0.5 hidden sm:block"></div>

                        <!-- 2. FILTER BULAN -->
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-semibold text-slate-900 text-center">Filter Bulan</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-1.5 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-clock text-black text-[10px] mr-1"></i>
                                    <select name="bulan_sosmed" id="globalBulan"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[95px] truncate">

                                        <option value="all"
                                            {{ request('bulan_sosmed', 'all') == 'all' ? 'selected' : '' }}>
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

                                        @foreach ($listBulanIndo as $num => $name)
                                            <option value="{{ $num }}"
                                                {{ (string) request('bulan_sosmed') === (string) $num ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 3. FILTER TIPE KONTEN -->
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-semibold text-slate-900 text-center">Filter Tipe</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-1.5 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-photo-film text-black text-[10px] mr-1"></i>
                                    <select name="tipe_konten" id="globalTipe"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[100px] truncate">
                                        <option value="">Semua Tipe</option>
                                        @php
                                            $listTipeKonten = ['Feed/Reels', 'Story'];
                                        @endphp
                                        @foreach ($listTipeKonten as $tipe)
                                            <option value="{{ $tipe }}"
                                                {{ request('tipe_konten') == $tipe ? 'selected' : '' }}>
                                                {{ $tipe }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4. FILTER KATEGORI KONTEN -->
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-semibold text-slate-900 text-center">Filter Kategori</span>
                            <div
                                class="p-[1.5px] rounded-lg bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-sm">
                                <div class="flex items-center bg-white px-1.5 py-1 rounded-[7px]">
                                    <i class="fa-solid fa-tags text-black text-[10px] mr-1"></i>
                                    <select name="kategori_konten" id="globalKategori"
                                        onchange="document.getElementById('formGlobalFilter').submit()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[110px] truncate">
                                        <option value="">Semua Kategori</option>
                                        @php
                                            $listKategoriKonten = [
                                                'Collab Content',
                                                'Owned Production',
                                                'Shared Content',
                                            ];
                                        @endphp
                                        @foreach ($listKategoriKonten as $kat)
                                            <option value="{{ $kat }}"
                                                {{ request('kategori_konten') == $kat ? 'selected' : '' }}>
                                                {{ $kat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL RESET FILTER -->
                        @if (request()->hasAny(['bulan_sosmed', 'tipe_konten', 'kategori_konten']))
                            <div class="flex flex-col justify-end">
                                <a href="{{ route('pages.post.index', ['tahun' => $tahunTerpilih]) }}"
                                    class="px-2 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-[9px] font-bold transition flex items-center gap-1 cursor-pointer shadow-sm shrink-0 self-end mb-0.5"
                                    title="Reset Filter">
                                    <i class="fa-solid fa-rotate-left text-[8px]"></i>
                                    <span>Reset</span>
                                </a>
                            </div>
                        @endif

                    </form>

                    <!-- LOGO SIARCOM DESKTOP -->
                    <div class="hidden xl:flex items-center pl-2 border-l border-slate-200 shrink-0">
                        <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM" class="h-9 w-auto object-contain">
                    </div>

                </div>

                <!-- BORDER BOTTOM GRADASI SIARCOM -->
                <div
                    class="w-full h-[2.5px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] absolute bottom-0 left-0">
                </div>
            </header>

            <!-- Area Konten Utama -->
            <main class="flex-1 overflow-y-auto p-6 sosmed-scrollbar space-y-6">

                <!-- Flash Message Session -->
                @if (session('success') || session('error') || session('info'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                        class="fixed top-20 right-6 z-50 p-4 rounded-xl shadow-lg text-white font-medium flex items-center gap-3 transition-all duration-300 {{ session('success') ? 'bg-emerald-500' : (session('error') ? 'bg-rose-500' : 'bg-amber-500') }}">
                        <i
                            class="fa-solid {{ session('success') ? 'fa-circle-check' : (session('error') ? 'fa-circle-xmark' : 'fa-circle-info') }}"></i>
                        <span>{{ session('success') ?? (session('error') ?? session('info')) }}</span>
                        <button @click="show = false" class="text-white/80 hover:text-white ml-2"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                <!-- Section Summary Card -->
                @include('pages.post.partials.card')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full items-stretch">
                    <!-- Sisi Kiri: Chart -->
                    <div class="w-full">
                        @include('pages.post.partials.chart')
                    </div>

                    <!-- Sisi Kanan: Top Metrics Horizontal -->
                    <div class="w-full">
                        @include('pages.post.partials.top')
                    </div>
                </div>

                <!-- BARIS BAWAH: Tabel Data Utama -->
                <div class="w-full">
                    @include('pages.post.partials.table')
                </div>

            </main>
        </div>

        <!-- Modal Form Tambah Postingan -->
        <div x-show="tambahModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
            x-transition x-cloak>
            <div @click.away="tambahModalOpen = false"
                class="bg-white w-full max-w-xl rounded-2xl shadow-xl overflow-hidden p-6 relative">
                @include('pages.post.partials.tambah')
            </div>
        </div>

        <!-- Modal Form Edit Postingan -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
            x-transition x-cloak>

            <div class="w-full max-w-xl relative">

                <!-- Loading State  -->
                <div x-show="loadingEdit"
                    class="p-8 text-center bg-white rounded-2xl border border-black shadow-2xl text-slate-500 font-semibold text-xs">
                    <i class="fa-solid fa-spinner animate-spin text-2xl text-teal-500 mb-2"></i>
                    <p>Memuat Form Edit...</p>
                </div>

                <!-- Render HTML Edit secara dinamis dari Controller -->
                <div x-show="!loadingEdit" x-html="editModalHtml"></div>
            </div>
        </div>

        <!-- Modal Kustom Tambah Tahun Sosmed -->
        <div x-show="tahunModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs" x-transition
            x-cloak>
            <div @click.away="tahunModalOpen = false"
                class="bg-white w-full max-w-sm rounded-2xl shadow-xl overflow-hidden p-6 relative border border-slate-100">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-emerald-500"></i> Tambah Tahun Sosmed
                    </h3>
                    <button @click="tahunModalOpen = false" class="text-slate-400 hover:text-slate-600 transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Masukkan Tahun Baru</label>
                        <input type="number" x-model="inputTahunBaru" @keydown.enter="submitTahunBaru()"
                            placeholder="Contoh: 2027"
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

</body>

</html>
