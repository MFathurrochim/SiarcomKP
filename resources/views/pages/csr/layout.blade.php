<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIARCOM - Dashboard CSR</title>
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
        .csr-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 8px;
        }

        .csr-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        .csr-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #27B78F, #4ECDB8, #12B4C9);
            border-radius: 9999px;
            cursor: pointer;
        }

        .csr-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #1f9473, #3ea897, #0e8f9f);
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <!-- Wrapper Utama -->
    <div x-data="{
        sidebarOpen: true,
        logModalOpen: false,
        detailModalOpen: false,
        exportModalOpen: false
    }" class="flex h-screen w-screen overflow-hidden bg-slate-50">

        <!-- SIDEBAR UTAMA -->
        @include('components.sidebar')

        <!-- Container Konten Utama -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0 bg-slate-50">

            <!-- TOP NAVBAR ULTRA CLEAN & COMPACT -->
            <header
                class="min-h-[52px] bg-white border-b border-slate-200/80 px-3 py-1.5 sm:px-4 z-20 shrink-0 relative shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-1.5">

                <!-- SISI KIRI: JUDUL DASHBOARD & LOGO SIARCOM -->
                <div class="flex items-center justify-between shrink-0 gap-2">
                    <div class="flex items-center gap-2">
                        <div class="p-1 bg-teal-50 rounded-md border border-teal-100/80 text-[#27B78F]">
                            <i class="fa-solid fa-hand-holding-heart text-xs"></i>
                        </div>
                        <div>
                            <!-- Judul TETAP (Tidak dikecilkan) -->
                            <h1 class="text-sm sm:text-base font-black tracking-tight text-slate-900 leading-tight">
                                Dashboard CSR
                            </h1>
                        </div>
                    </div>

                    <!-- Logo SIARCOM Layar Kecil -->
                    <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM"
                        class="h-7 w-auto object-contain lg:hidden">
                </div>

                <!-- SISI KANAN: GLOBAL FILTER BAR COMPACT + LOGO DESKTOP -->
                <div class="flex items-center justify-between lg:justify-end gap-2 flex-1 min-w-0">

                    <form id="formGlobalFilter" method="GET" action="{{ route('pages.csr.index') }}"
                        class="flex items-center gap-1.5 flex-wrap lg:flex-nowrap">

                        <!-- 1. MASTER FILTER TAHUN REALISASI -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Tahun</span>
                            <div class="p-[1px] rounded-md bg-gradient-to-r from-[#27B78F] to-[#12B4C9] shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-calendar-days text-[#27B78F] text-[9px] mr-1"></i>
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
                                    <button type="button" onclick="openModal('modalTahun')"
                                        class="p-0.5 bg-teal-50 hover:bg-[#27B78F] text-[#27B78F] hover:text-white rounded transition cursor-pointer ml-1"
                                        title="Tambah Tahun Anggaran Baru">
                                        <i class="fa-solid fa-plus text-[8px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- GARIS PEMBATAS VERTIKAL -->
                        <div class="h-4 w-px bg-slate-300 mx-0.5 hidden sm:block self-end mb-1"></div>

                        <!-- 2. FILTER BULAN REALISASI -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Bulan</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-clock text-black text-[9px] mr-1"></i>
                                    <select name="bulan" id="globalBulan" onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[95px] truncate">
                                        <option value="">Semua Bulan</option>
                                        @php
                                            $listBulanIndo = [
                                                'Januari',
                                                'Februari',
                                                'Maret',
                                                'April',
                                                'Mei',
                                                'Juni',
                                                'Juli',
                                                'Agustus',
                                                'September',
                                                'Oktober',
                                                'November',
                                                'Desember',
                                            ];
                                        @endphp
                                        @foreach ($listBulanIndo as $bln)
                                            <option value="{{ $bln }}"
                                                {{ request('bulan') == $bln ? 'selected' : '' }}>{{ $bln }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 3. FILTER PROGRAM -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Program</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-diagram-project text-black text-[9px] mr-1"></i>
                                    <select name="g_program" id="globalProgram" onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[100px] truncate">
                                        <option value="">Semua Program</option>
                                        @if (isset($daftarProgramUnik))
                                            @foreach ($daftarProgramUnik as $prog)
                                                <option value="{{ $prog }}"
                                                    {{ request('g_program') == $prog ? 'selected' : '' }}>
                                                    {{ $prog }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 4. FILTER PILAR -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Pilar</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-layer-group text-black text-[9px] mr-1"></i>
                                    <select name="g_pilar" id="globalPilar" onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[95px] truncate">
                                        <option value="">Semua Pilar</option>
                                        @php
                                            $pilars = $masterPilarAll ?? ($daftarPilarFilter ?? []);
                                        @endphp
                                        @foreach ($pilars as $p)
                                            @php
                                                $namaPilarVal = is_object($p) ? $p->nama_pilar : $p;
                                            @endphp
                                            <option value="{{ $namaPilarVal }}"
                                                {{ request('g_pilar') == $namaPilarVal ? 'selected' : '' }}>
                                                {{ $namaPilarVal }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 5. FILTER KABUPATEN -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Kabupaten</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-map-location-dot text-black text-[9px] mr-1"></i>
                                    <select name="g_kabupaten" id="globalKabupaten"
                                        onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[95px] truncate">
                                        <option value="">Semua Kab</option>
                                        @php
                                            $kabupatens = $daftarKabupatenUnik ?? ($daftarKabupatenFilter ?? []);
                                        @endphp
                                        @foreach ($kabupatens as $kab)
                                            <option value="{{ $kab }}"
                                                {{ request('g_kabupaten') == $kab ? 'selected' : '' }}>
                                                {{ $kab }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 6. FILTER DESA -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Desa</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-tree-city text-black text-[9px] mr-1"></i>
                                    <select name="g_desa" id="globalDesa" onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[90px] truncate">
                                        <option value="">Semua Desa</option>
                                        @if (isset($daftarDesaFilter))
                                            @foreach ($daftarDesaFilter as $des)
                                                <option value="{{ $des }}"
                                                    {{ request('g_desa') == $des ? 'selected' : '' }}>
                                                    {{ $des }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 7. FILTER STATUS REALISASI -->
                        <div class="flex flex-col">
                            <span
                                class="text-[8px] font-bold text-slate-900 uppercase tracking-wider mb-0.5 px-0.5">Filter
                                Status</span>
                            <div
                                class="p-[1px] rounded-md bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200 hover:from-[#27B78F] hover:to-[#12B4C9] transition-all duration-300 shadow-2xs">
                                <div class="flex items-center bg-white px-1.5 py-0.5 rounded-[5px]">
                                    <i class="fa-solid fa-circle-check text-black text-[9px] mr-1"></i>
                                    <select name="g_status" id="globalStatus" onchange="handleGlobalFilterChange()"
                                        class="text-[10px] font-bold text-black border-0 bg-transparent focus:outline-none cursor-pointer max-w-[90px] truncate">
                                        <option value="">Semua Status</option>
                                        <option value="done" {{ request('g_status') == 'done' ? 'selected' : '' }}>
                                            Done</option>
                                        <option value="undone"
                                            {{ request('g_status') == 'undone' ? 'selected' : '' }}>Undone</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL RESET FILTER -->
                        @if (request()->filled('bulan') ||
                                request()->filled('g_program') ||
                                request()->filled('g_pilar') ||
                                request()->filled('g_kabupaten') ||
                                request()->filled('g_desa') ||
                                request()->filled('g_status') ||
                                request()->filled('status'))
                            <div class="flex flex-col self-end mb-0.5">
                                <span class="text-[8px] text-transparent select-none">Reset</span>
                                <a href="{{ route('pages.csr.index', ['tahun' => $tahunTerpilih]) }}"
                                    class="px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white rounded-md text-[9px] font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs shrink-0"
                                    title="Reset Filter">
                                    <i class="fa-solid fa-rotate-left text-[8px]"></i>
                                    <span>Reset</span>
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- LOGO SIARCOM DESKTOP -->
                    <div class="hidden lg:flex items-center pl-2 border-l border-slate-200 shrink-0">
                        <img src="{{ asset('img/siarcom.png') }}" alt="Logo SIARCOM"
                            class="h-8 w-auto object-contain">
                    </div>

                </div>

                <!-- BORDER BOTTOM GRADASI SIARCOM -->
                <div
                    class="w-full h-[2px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] absolute bottom-0 left-0">
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 csr-scrollbar bg-slate-50/50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- MODAL TAMBAH TAHUN BARU -->
    <div id="modalTahun"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4 border-b pb-3 border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-[#27B78F]"></i> Buat Tahun Realisasi
                </h3>
                <button type="button" onclick="closeModal('modalTahun')"
                    class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('csr.tambah_tahun') }}" method="POST">
                @csrf
                <div class="mb-4 text-left">
                    <label for="input_tahun"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Realisasi
                        Baru</label>
                    <input type="number" min="2020" max="2100" id="input_tahun" name="tahun_baru" required
                        class="w-full px-3 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Contoh: 2027">
                    <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">
                        *Menambahkan tahun baru akan mengizinkan pencatatan program CSR dan inisialisasi pilar anggaran
                        untuk periode tahun tersebut.
                    </p>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalTahun')"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-[#27B78F] to-[#12B4C9] hover:opacity-90 text-white font-bold text-xs rounded-xl transition shadow-md cursor-pointer">
                        Inisialisasi Tahun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT UTAMA -->
    <script>
        function openModal(idModal) {
            const modal = document.getElementById(idModal);
            if (modal) modal.classList.remove('hidden');
        }

        function closeModal(idModal) {
            const modal = document.getElementById(idModal);
            if (modal) modal.classList.add('hidden');
        }

        window.addEventListener('click', function(event) {
            const modalTahun = document.getElementById('modalTahun');
            if (event.target === modalTahun) {
                closeModal('modalTahun');
            }
        });

        function handleGlobalFilterChange() {
            const filters = {
                tahun: document.getElementById('globalTahun').value,
                bulan: document.getElementById('globalBulan').value,
                g_program: document.getElementById('globalProgram').value,
                g_pilar: document.getElementById('globalPilar').value,
                g_kabupaten: document.getElementById('globalKabupaten').value,
                g_desa: document.getElementById('globalDesa').value,
                g_status: document.getElementById('globalStatus').value,
            };

            window.dispatchEvent(new CustomEvent('csr-filter-changed', {
                detail: filters
            }));
            document.getElementById('formGlobalFilter').submit();
        }
    </script>

    @stack('scripts')
</body>

</html>
