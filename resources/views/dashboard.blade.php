<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIARCOM - Dashboard Utama</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Styling Kustom Jalur Rel Scrollbar */
        .csr-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .csr-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        .csr-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to right, #27B78F, #4ECDB8, #12B4C9);
            border-radius: 9999px;
            cursor: pointer;
        }

        .csr-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to right, #1f9473, #3ea897, #0e8f9f);
        }

        /* Scrollbar tipis untuk nama topik di card interaksi */
        .scroll-thin-x {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .scroll-thin-x::-webkit-scrollbar {
            height: 3px;
        }

        .scroll-thin-x::-webkit-scrollbar-track {
            background: transparent;
        }

        .scroll-thin-x::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }

        .scroll-thin-x::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 font-sans antialiased text-slate-800">

    <div id="appRoot" x-data="{
        sidebarOpen: true,
        logModalOpen: false,
        detailModalOpen: false,
        userModalOpen: false,
        logs: [],
        detailLog: {},
        loadingList: false,
        loadingDetail: false
    }" class="flex h-screen overflow-hidden">
        @include('components.sidebar')

        <div id="dashboardScroll" class="flex-1 flex flex-col overflow-y-auto bg-gray-100">
            <!-- HEADER DASHBOARD UTAMA -->
            <div class="bg-white shadow-sm px-6 py-2 flex flex-col md:flex-row justify-between items-start md:items-center gap-2 border-b-4 border-transparent"
                style="border-image: linear-gradient(to right, #27B78F, #4ECDB8, #12B4C9) 1;">

                <div class="flex items-center gap-2.5 shrink-0">
                    <div class="p-1.5 bg-teal-50 rounded-lg border border-teal-100/80 text-[#27B78F]">
                        <i class="fa-solid fa-chart-line text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-sm sm:text-base font-black tracking-tight text-slate-900 leading-tight">
                            Dashboard Utama
                        </h1>
                    </div>
                </div>


                <div class="flex items-center gap-3 w-full md:w-auto">
                    @if (Auth::user()->role === 'Dept Head')
                        <button @click="userModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-white text-[11px] font-bold rounded-lg shadow-sm transition cursor-pointer w-full md:w-auto justify-center"
                            style="background: linear-gradient(to right, #27B78F, #12B4C9);">
                            <i class="fas fa-users-cog text-[10px]"></i> Manajemen Pengguna
                        </button>
                    @endif

                    @if (Auth::user()->role === 'Dept Head')
                        <button @click="logModalOpen = true; fetchActivityLogs();"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-700 hover:bg-slate-800 text-white text-[11px] font-bold rounded-lg shadow-sm transition cursor-pointer w-full md:w-auto justify-center">
                            <i class="fas fa-history text-[10px]"></i> Log Aktivitas
                        </button>
                    @endif

                    <img src="{{ asset('img/siarcom.png') }}" alt="SIARCOM Logo"
                        class="h-10 w-auto object-contain shrink-0">
                </div>
            </div>
            @include('log-aktivitas')
            @include('manajemen-pengguna')
            <!-- KONTEN UTAMA DASHBOARD -->
            <div id="dashboard-container">
                <div class="px-6 py-4 space-y-4">

                    <!-- ===================================== -->
                    <!-- DIAGRAM BAGIAN CSR                    -->
                    <!-- ===================================== -->
                    @php
                        // $anggaranTriwulan dari controller berbentuk per-pilar
                        $twTotal = ['TW1' => 0, 'TW2' => 0, 'TW3' => 0, 'TW4' => 0];
                        foreach ($anggaranTriwulan ?? [] as $namaPilarTw => $twData) {
                            foreach ($twData as $twKey => $twVal) {
                                $twTotal[$twKey] = ($twTotal[$twKey] ?? 0) + $twVal;
                            }
                        }
                    @endphp

                    <div
                        class="w-full bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-3xl shadow-sm mx-0">

                        <div class="w-full bg-white p-4 rounded-[23px]" x-data="{
                            scrollPercent: 0,
                            isDragging: false,
                            updateScrollProgress() {
                                if (this.isDragging) return;
                                const el = this.$refs.sliderContainer;
                                if (el) {
                                    const maxScroll = el.scrollWidth - el.clientWidth;
                                    this.scrollPercent = maxScroll > 0 ? (el.scrollLeft / maxScroll) * 100 : 0;
                                    localStorage.setItem('scroll_left_csr', el.scrollLeft);
                                }
                            },
                            init() {
                                setTimeout(() => {
                                    const savedScroll = localStorage.getItem('scroll_left_csr');
                                    if (savedScroll && this.$refs.sliderContainer) {
                                        this.$refs.sliderContainer.scrollLeft = parseInt(savedScroll);
                                        localStorage.removeItem('scroll_left_csr');
                                    }
                                }, 250);
                            }
                        }">

                            <!-- GLOBAL & CSR FILTER FORM -->
                            <form id="filterForm" action="{{ route('dashboard') }}" method="GET"
                                class="w-full border-b border-gray-900 pb-3 mb-4">
                                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-hand-holding-heart text-blue-600 text-base"></i>
                                            <h2
                                                class="text-sm font-black text-black uppercase tracking-wide whitespace-nowrap">
                                                Data Corporate Social Responsibility (CSR)
                                            </h2>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <!-- Filter: Tahun -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Tahun:</label>
                                        <select name="tahun" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer">
                                            @foreach ($daftarTahun ?? [] as $t)
                                                <option value="{{ $t }}"
                                                    {{ ($tahunTerpilih ?? '') == $t ? 'selected' : '' }}>
                                                    {{ $t }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter: Pilar CSR (pakai nama pilar, bukan id_pilar karena beda tiap tahun) -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Pilar:</label>
                                        <select name="g_pilar" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer">
                                            <option value="all"
                                                {{ ($pilarFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                                                Semua Pilar
                                            </option>
                                            <option value="Sosial"
                                                {{ ($pilarFilter ?? '') == 'Sosial' ? 'selected' : '' }}>
                                                Pilar Sosial
                                            </option>
                                            <option value="Ekonomi"
                                                {{ ($pilarFilter ?? '') == 'Ekonomi' ? 'selected' : '' }}>
                                                Pilar Ekonomi
                                            </option>
                                            <option value="Lingkungan"
                                                {{ ($pilarFilter ?? '') == 'Lingkungan' ? 'selected' : '' }}>
                                                Pilar Lingkungan
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Filter: Bulan Realisasi -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Bulan:</label>
                                        <select name="g_bulan" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer">
                                            <option value="all"
                                                {{ ($bulanFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                                                Semua Bulan
                                            </option>
                                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bln)
                                                <option value="{{ $bln }}"
                                                    {{ ($bulanFilter ?? '') == $bln ? 'selected' : '' }}>
                                                    {{ $bln }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter: Kabupaten -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Kab:</label>
                                        <select name="g_kabupaten" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer max-w-[110px]">
                                            <option value="all"
                                                {{ ($kabupatenFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                                                Semua Kab
                                            </option>
                                            @foreach ($daftarKabupatenCsr ?? [] as $kab)
                                                <option value="{{ $kab }}"
                                                    {{ ($kabupatenFilter ?? '') == $kab ? 'selected' : '' }}>
                                                    {{ $kab }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter: Desa -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Desa:</label>
                                        <select name="g_desa" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer max-w-[110px]">
                                            <option value="all"
                                                {{ ($desaFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                                                Semua Desa
                                            </option>
                                            @foreach ($daftarDesaCsr ?? [] as $ds)
                                                <option value="{{ $ds }}"
                                                    {{ ($desaFilter ?? '') == $ds ? 'selected' : '' }}>
                                                    {{ $ds }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Filter: Program -->
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 border border-gray-900 rounded-lg px-2.5 py-1 shadow-sm">
                                        <label class="text-[10px] font-bold text-black uppercase">Program:</label>
                                        <select name="g_program" onchange="submitFilter('filterForm')"
                                            class="bg-transparent text-xs font-bold text-black outline-none cursor-pointer max-w-[140px]">
                                            <option value="all"
                                                {{ ($programFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                                                Semua Program
                                            </option>
                                            @foreach ($daftarProgramUnikCsr ?? [] as $prog)
                                                <option value="{{ $prog }}"
                                                    {{ ($programFilter ?? '') == $prog ? 'selected' : '' }}>
                                                    {{ $prog }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Hidden Inputs untuk Menjaga State Filter Sosmed/Berita/Wilayah -->
                                <input type="hidden" name="kategori_konten"
                                    value="{{ request('kategori_konten', 'all') }}">
                                <input type="hidden" name="tipe_konten"
                                    value="{{ request('tipe_konten', 'Keseluruhan') }}">
                                <input type="hidden" name="tahun_sosmed"
                                    value="{{ $tahunSosmedTerpilih ?? request('tahun_sosmed') }}">
                                <input type="hidden" name="bulan_sosmed"
                                    value="{{ $bulanSosmed ?? request('bulan_sosmed') }}">
                                <input type="hidden" name="tahun_berita"
                                    value="{{ $tahunBeritaTerpilih ?? request('tahun_berita') }}">
                                <input type="hidden" name="bulan_berita"
                                    value="{{ $bulanBerita ?? request('bulan_berita') }}">
                                <input type="hidden" name="tampilkan_berdasarkan"
                                    value="{{ $modeWilayah ?? request('tampilkan_berdasarkan', 'kabupaten') }}">
                            </form>

                            <!-- HORIZONTAL SLIDER CONTAINER -->
                            <div x-ref="sliderContainer" @scroll="updateScrollProgress()"
                                class="flex flex-row overflow-x-auto gap-3 pb-2 csr-scrollbar snap-x snap-mandatory select-none w-full px-1">

                                <!-- CSR Card 1: Realisasi Anggaran -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[240px] flex-none snap-start flex flex-col justify-between h-[230px]">
                                    <div class="flex flex-col h-full justify-between">
                                        <div class="mb-1">
                                            <h3 class="font-bold text-black text-[11px] leading-tight">Realisasi
                                                Anggaran</h3>
                                            <p class="text-[9px] text-black leading-tight">Perbandingan dana terpakai &
                                                sisa</p>
                                        </div>

                                        <div
                                            class="relative flex-1 flex justify-center items-center my-1 min-h-[95px]">
                                            <canvas id="chartDonutCsr" class="max-h-full max-w-full"></canvas>
                                        </div>

                                        <div
                                            class="border-t border-gray-300 pt-1 text-[8.5px] text-black space-y-0.5 font-semibold">
                                            <div class="flex justify-between leading-none">
                                                <span class="text-black font-normal">Realisasi Dana:</span>
                                                <span class="font-bold text-black">Rp
                                                    {{ number_format($realisasiAnggaran['total_realisasi'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between leading-none">
                                                <span class="text-black font-normal">Sisa Anggaran:</span>
                                                <span class="font-bold text-black">Rp
                                                    {{ number_format($realisasiAnggaran['sisa_anggaran'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between border-t border-dashed border-gray-400 pt-1 mt-0.5 font-bold leading-none">
                                                <span class="text-black font-medium">Total:</span>
                                                <span class="font-black text-black">Rp
                                                    {{ number_format(($realisasiAnggaran['total_realisasi'] ?? 0) + ($realisasiAnggaran['sisa_anggaran'] ?? 0), 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CSR Card 2: Anggaran Pilar -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[260px] flex-none snap-start flex flex-col justify-between">
                                    <div>
                                        <div class="mb-1.5">
                                            <h3 class="font-bold text-black text-[11px] leading-tight">Anggaran Pilar
                                            </h3>
                                            <p class="text-[9px] text-black">Alokasi dana per pilar program</p>
                                        </div>
                                        <div class="h-auto overflow-y-auto max-h-[100px]">
                                            <table class="w-full text-left border-collapse text-[10px] text-black">
                                                <thead>
                                                    <tr class="border-b border-gray-300 text-black font-bold">
                                                        <th class="pb-1 font-bold">Nama Pilar</th>
                                                        <th class="pb-1 text-right font-bold">Anggaran</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @forelse($anggaranPilar ?? [] as $anggaran)
                                                        <tr>
                                                            <td class="py-1 font-bold text-black">
                                                                Pilar
                                                                {{ data_get($anggaran, 'nama_pilar', 'Tidak Diketahui') }}
                                                            </td>
                                                            <td class="py-1 text-right font-bold text-black">
                                                                Rp
                                                                {{ number_format(data_get($anggaran, 'jumlah_anggaran', 0), 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2"
                                                                class="py-2 text-center text-black italic font-bold">
                                                                Belum ada data.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- CSR Card 3: Realisasi Per Pilar -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[260px] flex-none snap-start flex flex-col justify-between">
                                    <div>
                                        <div class="mb-1.5">
                                            <h3 class="font-bold text-black text-[11px] leading-tight">Realisasi Per
                                                Pilar</h3>
                                            <p class="text-[9px] text-black">Total kegiatan yang sudah terealisasi</p>
                                        </div>
                                        <div class="h-auto overflow-y-auto max-h-[100px]">
                                            <table class="w-full text-left border-collapse text-[10px] text-black">
                                                <thead>
                                                    <tr class="border-b border-gray-300 text-black font-bold">
                                                        <th class="pb-1 font-bold">Nama Pilar</th>
                                                        <th class="pb-1 text-right font-bold">Capaian</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @forelse($realisasiPerPilar ?? [] as $pilar)
                                                        <tr>
                                                            <td class="py-1 font-bold text-black">
                                                                Pilar
                                                                {{ data_get($pilar, 'nama_pilar', 'Tidak Diketahui') }}
                                                            </td>
                                                            <td class="py-1 text-right font-bold text-black">
                                                                {{ data_get($pilar, 'jumlah_kegiatan_done', 0) }}
                                                                Program
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2"
                                                                class="py-2 text-center text-black italic font-bold">
                                                                Belum ada data.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- CSR Card 4: Kegiatan Bulanan -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[280px] flex-none snap-start flex flex-col">
                                    <div class="mb-1.5">
                                        <h3 class="font-bold text-black text-[11px] leading-tight">Kegiatan Bulanan
                                        </h3>
                                        <p class="text-[9px] text-black">Intensitas program tahun
                                            {{ $tahunTerpilih ?? '' }}</p>
                                    </div>
                                    <div class="relative flex-1 min-h-[100px]">
                                        <canvas id="chartBarCsr"
                                            data-bulan="{{ json_encode(array_keys($kegiatanBulanan ?? [])) }}"
                                            data-jumlah="{{ json_encode(array_values($kegiatanBulanan ?? [])) }}">
                                        </canvas>
                                    </div>
                                </div>

                                <!-- CSR Card 5: Anggaran Triwulan (Per Pilar) -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm w-[440px] flex-none flex flex-col justify-between">
                                    <div>
                                        <!-- Header Card -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <h3 class="font-bold text-gray-800 text-[11px]">Anggaran Biaya Triwulan
                                            </h3>
                                        </div>

                                        <!-- Tabel -->
                                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                            <table class="w-full text-left border-collapse text-[9px] text-gray-800"
                                                id="tabelTriwulanCsr">
                                                <thead>
                                                    <tr
                                                        class="text-gray-600 uppercase tracking-wider bg-gray-50 border-b border-gray-200 font-bold">
                                                        <th class="py-1.5 px-2 border-r border-gray-200">Periode</th>
                                                        @foreach ($anggaranTriwulan ?? [] as $namaPilarTw => $twDataHead)
                                                            <th
                                                                class="py-1.5 px-2 text-right border-r border-gray-200 last:border-r-0">
                                                                {{ $namaPilarTw }}</th>
                                                        @endforeach
                                                        <th class="py-1.5 px-2 text-right">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach (['TW1' => 'Triwulan 1', 'TW2' => 'Triwulan 2', 'TW3' => 'Triwulan 3', 'TW4' => 'Triwulan 4'] as $twKey => $labelTw)
                                                        <tr>
                                                            <td
                                                                class="py-1.5 px-2 font-semibold text-gray-900 whitespace-nowrap border-r border-gray-200 bg-gray-50/50">
                                                                {{ $labelTw }}
                                                            </td>
                                                            @foreach ($anggaranTriwulan ?? [] as $namaPilarTw => $twData)
                                                                <td
                                                                    class="py-1.5 px-2 text-right whitespace-nowrap text-gray-600 border-r border-gray-200 last:border-r-0">
                                                                    Rp
                                                                    {{ number_format($twData[$twKey] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                            @endforeach
                                                            <td
                                                                class="py-1.5 px-2 text-right whitespace-nowrap font-bold text-gray-900">
                                                                Rp
                                                                {{ number_format($twTotal[$twKey] ?? 0, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- CSR Card 6: Program per Wilayah -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[340px] flex-none snap-start flex flex-col">
                                    <div class="flex flex-row items-center justify-between gap-1 mb-2">
                                        <div>
                                            <h3
                                                class="font-bold text-black text-[11px] leading-tight flex items-center gap-1">
                                                <i class="fas fa-map-marker-alt text-emerald-600"></i> Program per
                                                {{ ucfirst($modeWilayah) }}
                                            </h3>
                                        </div>

                                        <form id="wilayahForm" method="GET" action="{{ route('dashboard') }}"
                                            class="flex items-center bg-gray-100 p-0.5 rounded border border-gray-300 scale-90 origin-right">
                                            @foreach (request()->except(['tampilkan_berdasarkan']) as $key => $value)
                                                <input type="hidden" name="{{ $key }}"
                                                    value="{{ $value }}">
                                            @endforeach

                                            <label
                                                class="px-2 py-0.5 text-[9px] font-bold rounded cursor-pointer transition-all {{ ($modeWilayah ?? 'kabupaten') == 'kabupaten' ? 'bg-black text-white shadow-xs' : 'text-black' }}">
                                                <input type="radio" name="tampilkan_berdasarkan" value="kabupaten"
                                                    onchange="submitFilter('wilayahForm')" class="hidden"
                                                    {{ ($modeWilayah ?? 'kabupaten') == 'kabupaten' ? 'checked' : '' }}>
                                                Kabupaten
                                            </label>
                                            <label
                                                class="px-2 py-0.5 text-[9px] font-bold rounded cursor-pointer transition-all {{ ($modeWilayah ?? '') == 'desa' ? 'bg-black text-white shadow-xs' : 'text-black' }}">
                                                <input type="radio" name="tampilkan_berdasarkan" value="desa"
                                                    onchange="submitFilter('wilayahForm')" class="hidden"
                                                    {{ ($modeWilayah ?? '') == 'desa' ? 'checked' : '' }}>
                                                Desa
                                            </label>
                                        </form>
                                    </div>

                                    <!-- Container Tabel Data Wilayah (Dibagi 2 Kolom Sejajar) -->
                                    <div class="flex-1 overflow-y-auto max-h-[180px] scroll-thin pr-0.5">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="border-b border-gray-200 text-[10px] text-gray-700">
                                                    <th class="py-1 px-1 font-bold capitalize">{{ $modeWilayah }}
                                                    </th>
                                                    <th class="py-1 px-1 font-bold text-right w-10">Total</th>
                                                    <th
                                                        class="py-1 px-1 font-bold capitalize border-l border-l-gray-100 pl-2">
                                                        {{ $modeWilayah }}</th>
                                                    <th class="py-1 px-1 font-bold text-right w-10">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-[9px] divide-y divide-gray-100">
                                                @forelse($dataWilayahChart->chunk(2) as $row)
                                                    <tr>
                                                        <!-- Kolom Kiri -->
                                                        @isset($row[0])
                                                            <td
                                                                class="py-1.5 px-1 text-black font-medium truncate max-w-[90px]">
                                                                {{ $row[0]->nama_lokasi }}</td>
                                                            <td class="py-1.5 px-1 text-black font-bold text-right">
                                                                {{ $row[0]->total_program }}</td>
                                                        @else
                                                            <td class="py-1.5 px-1"></td>
                                                            <td class="py-1.5 px-1"></td>
                                                        @endisset

                                                        <!-- Kolom Kanan -->
                                                        @isset($row[1])
                                                            <td
                                                                class="py-1.5 px-1 text-black font-medium truncate max-w-[90px] border-l border-l-gray-100 pl-2">
                                                                {{ $row[1]->nama_lokasi }}</td>
                                                            <td class="py-1.5 px-1 text-black font-bold text-right">
                                                                {{ $row[1]->total_program }}</td>
                                                        @else
                                                            <td class="py-1.5 px-1 border-l border-l-gray-100 pl-2"></td>
                                                            <td class="py-1.5 px-1"></td>
                                                        @endisset
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4"
                                                            class="text-center py-4 text-gray-400 text-[9px]">Tidak ada
                                                            data wilayah</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- SECTION 2: MONITORING MEDIA SOSIAL                                        -->
                    <!-- ========================================================================= -->
                    <div
                        class="w-full bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[2px] rounded-2xl shadow-sm mt-6">
                        <div class="w-full bg-white p-4 rounded-[14px]">

                            <form id="sosmedFilterForm" action="{{ route('dashboard') }}" method="GET"
                                class="w-full">
                                <div
                                    class="flex flex-col lg:flex-row lg:items-center gap-2.5 border-b border-gray-200 pb-3 w-full mb-3">

                                    <div class="flex flex-wrap items-center gap-2.5 flex-1">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-chart-line text-emerald-600 text-base"></i>
                                            <h2
                                                class="text-sm font-black text-gray-950 uppercase tracking-wide whitespace-nowrap">
                                                Data Publikasi Media Sosial</h2>
                                        </div>

                                        <!-- 1. Filter: Tahun -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                            <label
                                                class="text-[9px] font-bold text-gray-950 uppercase tracking-wider">Tahun:</label>
                                            <select name="tahun_sosmed" onchange="submitFilter('sosmedFilterForm')"
                                                class="bg-transparent text-[11px] font-bold text-gray-950 outline-none cursor-pointer">
                                                @foreach ($daftarTahun as $t)
                                                    <option value="{{ $t }}"
                                                        {{ $tahunSosmedTerpilih == $t ? 'selected' : '' }}>
                                                        {{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- [BARU] 2. Filter: Bulan -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                            <label
                                                class="text-[9px] font-bold text-gray-950 uppercase tracking-wider">Bulan:</label>
                                            <select name="bulan_sosmed" onchange="submitFilter('sosmedFilterForm')"
                                                class="bg-transparent text-[11px] font-bold text-gray-950 outline-none cursor-pointer">
                                                <option value="all" {{ $bulanSosmed == 'all' ? 'selected' : '' }}>
                                                    Semua Bulan</option>
                                                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $namaBln)
                                                    <option value="{{ $num }}"
                                                        {{ $bulanSosmed == $num ? 'selected' : '' }}>
                                                        {{ $namaBln }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- [BARU] 3. Filter: Kategori -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                            <label
                                                class="text-[9px] font-bold text-gray-950 uppercase tracking-wider">Kategori:</label>
                                            <select name="kategori_konten" onchange="submitFilter('sosmedFilterForm')"
                                                class="bg-transparent text-[11px] font-bold text-gray-950 outline-none cursor-pointer">
                                                <option value="all"
                                                    {{ $kategoriKontenTerpilih == 'all' ? 'selected' : '' }}>Semua
                                                    Kategori</option>
                                                <option value="Collab Content"
                                                    {{ $kategoriKontenTerpilih == 'Collab Content' ? 'selected' : '' }}>
                                                    Collab Content</option>
                                                <option value="Owned Production"
                                                    {{ $kategoriKontenTerpilih == 'Owned Production' ? 'selected' : '' }}>
                                                    Owned Production</option>
                                                <option value="Shared Content"
                                                    {{ $kategoriKontenTerpilih == 'Shared Content' ? 'selected' : '' }}>
                                                    Shared Content</option>
                                            </select>
                                        </div>

                                        <!-- 4. Filter: Tipe -->
                                        <div
                                            class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                            <label
                                                class="text-[9px] font-bold text-gray-950 uppercase tracking-wider">Tipe:</label>
                                            <select name="tipe_konten" onchange="submitFilter('sosmedFilterForm')"
                                                class="bg-transparent text-[11px] font-bold text-gray-950 outline-none cursor-pointer">
                                                <option value="Keseluruhan"
                                                    {{ request('tipe_konten', 'Keseluruhan') == 'Keseluruhan' ? 'selected' : '' }}>
                                                    Keseluruhan</option>
                                                <option value="Feed/Reels"
                                                    {{ request('tipe_konten') == 'Feed/Reels' ? 'selected' : '' }}>
                                                    Feed/Reels</option>
                                                <option value="Story"
                                                    {{ request('tipe_konten') == 'Story' ? 'selected' : '' }}>Story
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <input type="hidden" name="tahun" value="{{ $tahunTerpilih }}">
                                    <input type="hidden" name="id_pilar" value="{{ request('id_pilar', 'all') }}">
                                    <input type="hidden" name="tahun_berita" value="{{ $tahunBeritaTerpilih }}">
                                    <input type="hidden" name="bulan_berita" value="{{ $bulanBerita }}">
                                </div>
                            </form>

                            <!-- Grid Dashboard Content -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">

                                <!-- Card 1: Tren Postingan (Teks <p> dihapus & tinggi canvas diperbesar) -->
                                <div
                                    class="bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 p-[1px] rounded-xl w-full h-full flex shrink-0">
                                    <div
                                        class="bg-white rounded-[11px] p-3 shadow-xs w-full h-full flex flex-col justify-between">
                                        <div class="flex justify-between items-center mb-2 relative">
                                            <div>
                                                <h3 class="font-bold text-black text-[11px] uppercase tracking-wider">
                                                    Tren Postingan Bulanan {{ $tahunBeritaTerpilih }}
                                                </h3>
                                            </div>
                                        </div>
                                        <!-- Tinggi diperbesar menjadi h-48 agar diagram makin lebar ke atas -->
                                        <div class="relative h-48 w-full">
                                            <canvas id="chartSosmedBulanan"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Top 5 Interaksi Tertinggi -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm flex flex-col justify-between w-full h-full">
                                    <div class="mb-2">
                                        <h3 class="font-black text-emerald-700 text-[11px] uppercase tracking-wider">
                                            <i class="fas fa-arrow-up mr-1"></i> Top 5 Interaksi Tertinggi
                                        </h3>
                                    </div>

                                    <!-- Tinggi disesuaikan agar sama proporsionalnya, menggunakan h-48 atau flex-1 penuh -->
                                    <div class="flex-1 flex flex-col justify-between gap-1">
                                        @forelse($interaksiTertinggi as $index => $item)
                                            <div
                                                class="px-2 py-1.5 bg-slate-50 border border-l-4 border-l-emerald-600 border-gray-300 rounded-lg flex items-start gap-2">
                                                <span
                                                    class="text-[10px] font-black text-black bg-gray-200 px-1 py-0.5 rounded-md shrink-0 mt-0.5">#{{ $index + 1 }}</span>
                                                <!-- Menggunakan whitespace-normal agar teks otomatis turun ke bawah (wrap) jika panjang -->
                                                <div class="flex-1 min-w-0">
                                                    <span
                                                        class="text-[8px] font-bold text-black block leading-tight whitespace-normal break-words">{{ $item->topik }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-500 font-bold text-center py-4">Tidak ada data
                                                postingan</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Card 3: Top 5 Interaksi Terendah -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm flex flex-col justify-between w-full h-full">
                                    <div class="mb-2">
                                        <h3 class="font-black text-rose-600 text-[11px] uppercase tracking-wider">
                                            <i class="fas fa-arrow-down mr-1"></i> Top 5 Interaksi Terendah
                                        </h3>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-between gap-1">
                                        @forelse($interaksiTerendah as $index => $item)
                                            <div
                                                class="px-2 py-1.5 bg-slate-50 border border-l-4 border-l-rose-600 border-gray-300 rounded-lg flex items-start gap-2">
                                                <span
                                                    class="text-[10px] font-black text-black bg-gray-200 px-1 py-0.5 rounded-md shrink-0 mt-0.5">#{{ $index + 1 }}</span>
                                                <!-- Menggunakan whitespace-normal agar teks otomatis turun ke bawah (wrap) jika panjang -->
                                                <div class="flex-1 min-w-0">
                                                    <span
                                                        class="text-[8px] font-bold text-black block leading-tight whitespace-normal break-words">{{ $item->topik }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-gray-500 font-bold text-center py-4">Tidak ada data
                                                postingan</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- SECTION 3: MEDIA MONITORING DASHBOARD (BERITA)                            -->
                    <!-- ========================================================================= -->
                    <div class="w-full bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[2px] rounded-2xl shadow-sm mt-6"
                        x-data="{
                            scrollPercent: 0,
                            isDragging: false,
                            updateScrollProgress() {
                                if (this.isDragging) return;
                                // Disesuaikan dengan ref milikmu: sliderContainerBerita
                                const el = this.$refs.sliderContainerBerita;
                                if (el) {
                                    const maxScroll = el.scrollWidth - el.clientWidth;
                                    this.scrollPercent = maxScroll > 0 ? (el.scrollLeft / maxScroll) * 100 : 0;
                        
                                    // SIMPAN: Gunakan nama kunci yang spesifik untuk Berita/Media Monitoring
                                    localStorage.setItem('scroll_left_medmon', el.scrollLeft);
                                }
                            },
                            init() {
                                // AMBIL: Kembalikan posisi slider berita setelah reload halaman
                                setTimeout(() => {
                                    const savedScroll = localStorage.getItem('scroll_left_medmon');
                                    const el = this.$refs.sliderContainerBerita;
                                    if (savedScroll && el) {
                                        el.scrollLeft = parseInt(savedScroll);
                                        localStorage.removeItem('scroll_left_medmon');
                                    }
                                }, 250);
                            }
                        }">
                        <div class="w-full bg-white p-4 rounded-[14px] relative z-30">

                            <!-- Filter Form -->
                            <form id="beritaFilterForm" action="{{ route('dashboard') }}" method="GET"
                                class="w-full border-b border-gray-200 pb-3 mb-3">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">

                                    <!-- Sisi Kiri: Judul Dashboard & Input Filter Dropdown (Disatukan agar berdampingan) -->
                                    <div class="flex flex-wrap items-center gap-3">
                                        <!-- Judul Dashboard -->
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-newspaper text-amber-600 text-base"></i>
                                            <h2
                                                class="text-sm font-black text-gray-900 uppercase tracking-wide whitespace-nowrap">
                                                Data Media Berita</h2>
                                        </div>

                                        <!-- Input Filter Dropdown -->
                                        <div class="flex flex-wrap items-center gap-2.5">
                                            <!-- Filter: Tahun -->
                                            <div
                                                class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                                <label
                                                    class="text-[9px] font-bold text-gray-900 uppercase tracking-wider">Tahun:</label>
                                                <select name="tahun_berita"
                                                    onchange="submitFilter('beritaFilterForm')"
                                                    class="bg-transparent text-[11px] font-bold text-gray-900 outline-none cursor-pointer">
                                                    @foreach ($daftarTahun as $t)
                                                        <option value="{{ $t }}"
                                                            {{ $tahunBeritaTerpilih == $t ? 'selected' : '' }}>
                                                            {{ $t }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Filter: Bulan -->
                                            <div
                                                class="flex items-center gap-1.5 bg-gray-50 border border-gray-400 rounded-lg px-2 py-1 shadow-sm">
                                                <label
                                                    class="text-[9px] font-bold text-gray-900 uppercase tracking-wider">Bulan:</label>
                                                <select name="bulan_berita"
                                                    onchange="submitFilter('beritaFilterForm')"
                                                    class="bg-transparent text-[11px] font-bold text-gray-900 outline-none cursor-pointer">
                                                    <option value="all"
                                                        {{ $bulanBerita == 'all' ? 'selected' : '' }}>
                                                        Semua Bulan</option>
                                                    @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $key => $namaBln)
                                                        <option value="{{ $key }}"
                                                            {{ $bulanBerita == $key ? 'selected' : '' }}>
                                                            {{ $namaBln }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div></div>

                                    <!-- Hidden Inputs -->
                                    <input type="hidden" name="tahun" value="{{ $tahunTerpilih }}">
                                    <input type="hidden" name="id_pilar" value="{{ request('id_pilar', 'all') }}">
                                    <input type="hidden" name="tahun_sosmed" value="{{ $tahunSosmedTerpilih }}">
                                    <input type="hidden" name="bulan_sosmed" value="{{ $bulanSosmed }}">
                                    <input type="hidden" name="kategori_konten"
                                        value="{{ $kategoriKontenTerpilih }}">
                                    <input type="hidden" name="tipe_konten"
                                        value="{{ request('tipe_konten', 'Keseluruhan') }}">
                                </div>
                            </form>

                            <!-- Slider Container Diagram  -->
                            <div x-ref="sliderContainerBerita" @scroll="updateScrollProgress()"
                                class="flex flex-row overflow-x-auto gap-4 pb-3 csr-scrollbar snap-x snap-mandatory select-none w-full direct-slider-touch">

                                <!-- Berita Card 1: Tone Pemberitaan -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[calc(100%-2rem)] sm:w-[300px] md:flex-1 min-w-[260px] flex-none snap-start flex flex-col justify-between text-black">
                                    <div>
                                        <div class="mb-2 flex justify-between items-start">
                                            <div>
                                                <h3 class="font-bold text-black text-[11px] uppercase tracking-wider">
                                                    Tone
                                                    Pemberitaan</h3>
                                                <p class="text-[9px] text-gray-600 font-medium mt-0.5">Sebaran
                                                    sentimen
                                                    pemberitaan media</p>
                                            </div>
                                        </div>

                                        <div class="space-y-2.5 my-auto pt-1">
                                            <div>
                                                <div
                                                    class="flex justify-between text-[10px] font-bold text-gray-900 mb-1">
                                                    <span>POSITIF</span>
                                                    <span
                                                        class="font-black text-emerald-600">{{ $dataToneBerita['Positif'] ?? 0 }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-emerald-600 h-full rounded-full transition-all duration-500"
                                                        style="width: {{ isset($dataToneBerita) && array_sum($dataToneBerita) > 0 ? min(100, (($dataToneBerita['Positif'] ?? 0) / array_sum($dataToneBerita)) * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <div
                                                    class="flex justify-between text-[10px] font-bold text-gray-900 mb-1">
                                                    <span>NETRAL</span>
                                                    <span
                                                        class="font-black text-slate-900">{{ $dataToneBerita['Netral'] ?? 0 }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-slate-700 h-full rounded-full transition-all duration-500"
                                                        style="width: {{ isset($dataToneBerita) && array_sum($dataToneBerita) > 0 ? min(100, (($dataToneBerita['Netral'] ?? 0) / array_sum($dataToneBerita)) * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <div
                                                    class="flex justify-between text-[10px] font-bold text-gray-900 mb-1">
                                                    <span>NEGATIF</span>
                                                    <span
                                                        class="font-black text-rose-600">{{ $dataToneBerita['Negatif'] ?? 0 }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-rose-600 h-full rounded-full transition-all duration-500"
                                                        style="width: {{ isset($dataToneBerita) && array_sum($dataToneBerita) > 0 ? min(100, (($dataToneBerita['Negatif'] ?? 0) / array_sum($dataToneBerita)) * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Berita Card 2: Jumlah Pemberitaan -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[calc(100%-2rem)] sm:w-[310px] md:flex-1 min-w-[270px] flex-none snap-start flex flex-col text-black">
                                    <div class="mb-2 flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-black text-[11px] uppercase tracking-wider">
                                                Jumlah
                                                Pemberitaan</h3>
                                            <p class="text-[9px] text-gray-600 font-medium mt-0.5">Perbandingan
                                                Publikasi
                                                Internal & Eksternal</p>
                                        </div>
                                        <button class="text-black hover:text-gray-700"></button>
                                    </div>
                                    <div class="relative flex-1 min-h-[130px] w-full">
                                        <canvas id="chartJumlahPemberitaan"></canvas>
                                    </div>
                                </div>

                                <!-- Berita Card 3: Spokesperson -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[calc(100%-2rem)] sm:w-[320px] md:flex-1 min-w-[280px] flex-none snap-start flex flex-col text-black">
                                    <div class="mb-2 flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-black text-[11px] uppercase tracking-wider">
                                                Spokesperson</h3>
                                            <p class="text-[9px] text-gray-600 font-medium mt-0.5">Top
                                                Spokeperson</p>
                                        </div>
                                        <button class="text-black hover:text-gray-700"></button>
                                    </div>
                                    <div class="relative flex-1 min-h-[130px] w-full">
                                        <canvas id="chartSpokesperson"></canvas>
                                    </div>
                                </div>

                                <!-- Berita Card 4: Top 5 Media & Jurnalis -->
                                <div
                                    class="bg-white p-3 rounded-xl border border-gray-300 shadow-sm w-[calc(100%-2rem)] sm:w-[340px] md:flex-1 min-w-[290px] flex-none snap-start flex flex-col justify-between text-black">
                                    <div>
                                        <div class="mb-2 flex justify-between items-start">
                                            <div>
                                                <h3 class="font-bold text-black text-[11px] uppercase tracking-wider">
                                                    Top 5
                                                    Media & Jurnalis</h3>
                                                <p class="text-[9px] text-gray-600 font-medium mt-0.5"></p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 text-[10px] pt-1">
                                            <div class="space-y-1.5 border-r border-gray-300 pr-2">
                                                <div
                                                    class="font-black text-gray-900 uppercase text-[9px] tracking-wider mb-1.5">
                                                    Nama Media</div>
                                                @forelse($topMedia ?? [] as $index => $med)
                                                    <div class="flex items-center gap-1.5 truncate text-black font-bold"
                                                        title="{{ $med->nama_media }}">
                                                        <span
                                                            class="text-black font-black w-4 text-right">{{ $index + 1 }}.</span>
                                                        <span class="truncate">{{ $med->nama_media }}</span>
                                                    </div>
                                                @empty
                                                    <p class="text-[10px] text-gray-500 font-bold italic">Tidak ada
                                                        data
                                                        media</p>
                                                @endforelse
                                            </div>

                                            <div class="space-y-1.5 pl-1.5">
                                                <div
                                                    class="font-black text-gray-900 uppercase text-[9px] tracking-wider mb-1.5">
                                                    Jurnalis / Reporter</div>
                                                @forelse($topJurnalis ?? [] as $index => $jur)
                                                    <div class="flex items-center gap-1.5 truncate text-black font-bold"
                                                        title="{{ $jur->reporter }}">
                                                        <span
                                                            class="text-emerald-600 font-black w-4 text-right">{{ $index + 1 }}.</span>
                                                        <span
                                                            class="truncate">{{ $jur->reporter ?: 'Anonim' }}</span>
                                                    </div>
                                                @empty
                                                    <p class="text-[10px] text-gray-500 font-bold italic">Tidak ada
                                                        data
                                                        jurnalis</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // --- LOG AKTIVITAS (GLOBAL FUNCTIONS) ---
                        function fetchActivityLogs() {
                            const alpineState = Alpine.$data(document.querySelector('[x-data]'));
                            if (!alpineState) return;
                            alpineState.loadingList = true;

                            fetch('/pengaturan/log-aktivitas', {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    alpineState.logs = data;
                                    alpineState.loadingList = false;
                                })
                                .catch(err => {
                                    console.error('Gagal memuat log:', err);
                                    alpineState.loadingList = false;
                                });
                        }

                        function fetchDetailLog(idLog) {
                            const alpineState = Alpine.$data(document.querySelector('[x-data]'));
                            if (!alpineState) return;
                            alpineState.detailModalOpen = true;
                            alpineState.loadingDetail = true;

                            fetch(`/pengaturan/log-aktivitas/${idLog}`)
                                .then(res => res.json())
                                .then(response => {
                                    if (response.status === 'success') {
                                        alpineState.detailLog = response.data;
                                    }
                                    alpineState.loadingDetail = false;
                                })
                                .catch(err => {
                                    console.error('Gagal memuat detail log:', err);
                                    alpineState.loadingDetail = false;
                                });
                        }

                        //Fungsi Filter CSR
                        function submitFilter(formId) {
                            const form = document.getElementById(formId);
                            if (form) form.submit();
                        }
                        // --- INITIALIZATION ALL CHARTS ---
                        document.addEventListener("DOMContentLoaded", function() {

                            // --- DATA INJECT FROM CONTROLLER ---
                            const dataRealisasi = @json($realisasiAnggaran ?? ['total_realisasi' => 0, 'sisa_anggaran' => 0]);
                            const dataAnggaranPilar = @json($anggaranPilar ?? []);
                            const dataWilayah = @json($dataWilayahChart ?? []);
                            const dataTrenSosmed = @json($trenPostinganBulanan ?? array_fill(0, 12, 0));
                            const dataSifatBerita = @json($dataSifatPemberitaan ?? ['Internal' => 0, 'Eksternal' => 0]);
                            const dataSpokesperson = @json($dataSpokesperson ?? []);

                            // --- MODUL CSR: CHART ANGGARAN PILAR ---
                            const ctxAnggaranPilar = document.getElementById('chartAnggaranPilar');
                            if (ctxAnggaranPilar) {
                                const ctxPilar = ctxAnggaranPilar.getContext('2d');
                                const gradienPilar = ctxPilar.createLinearGradient(0, 0, 0, 180);
                                gradienPilar.addColorStop(0, '#27B78F');
                                gradienPilar.addColorStop(0.5, '#4ECDB8');
                                gradienPilar.addColorStop(1, '#12B4C9');

                                const labelsPilar = Array.isArray(dataAnggaranPilar) && dataAnggaranPilar.length > 0 ?
                                    dataAnggaranPilar.map(item => item.nama_pilar ? 'Pilar ' + item.nama_pilar : 'Pilar -') : [
                                        'Tidak Ada Data'
                                    ];

                                const valuesPilar = Array.isArray(dataAnggaranPilar) && dataAnggaranPilar.length > 0 ?
                                    dataAnggaranPilar.map(item => Number(item.jumlah_anggaran) || 0) : [0];

                                new Chart(ctxPilar, {
                                    type: 'bar',
                                    data: {
                                        labels: labelsPilar,
                                        datasets: [{
                                            label: 'Pagu Anggaran',
                                            data: valuesPilar,
                                            backgroundColor: gradienPilar,
                                            borderRadius: 6,
                                            borderWidth: 0,
                                            barThickness: 22
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return ' Anggaran: Rp ' + new Intl.NumberFormat('id-ID').format(
                                                            context.raw);
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    color: '#000000',
                                                    font: {
                                                        size: 11,
                                                        weight: 'bold'
                                                    }
                                                },
                                                border: {
                                                    display: false
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    display: false
                                                },
                                                border: {
                                                    display: false
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            // --- MODUL CSR: CHART REALISASI ANGGARAN (DONUT) ---
                            const ctxDonutEl = document.getElementById('chartDonutCsr');
                            if (ctxDonutEl) {
                                const ctxDonut = ctxDonutEl.getContext('2d');
                                const gradienHijauBiru = ctxDonut.createLinearGradient(0, 0, 0, 160);
                                gradienHijauBiru.addColorStop(0, '#27B78F');
                                gradienHijauBiru.addColorStop(0.5, '#4ECDB8');
                                gradienHijauBiru.addColorStop(1, '#12B4C9');

                                const realisasiVal = Number(dataRealisasi.total_realisasi) || 0;
                                const sisaVal = Number(dataRealisasi.sisa_anggaran) || 0;

                                new Chart(ctxDonut, {
                                    type: 'doughnut',
                                    data: {
                                        labels: ['Realisasi Dana', 'Sisa Anggaran'],
                                        datasets: [{
                                            data: [realisasiVal, sisaVal],
                                            backgroundColor: [gradienHijauBiru, '#D1D5DB'],
                                            borderWidth: 2,
                                            borderColor: '#ffffff'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    boxWidth: 12,
                                                    color: '#000000',
                                                    font: {
                                                        size: 11,
                                                        weight: 'bold'
                                                    }
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return ` ${context.label}: Rp ` + new Intl.NumberFormat('id-ID')
                                                            .format(context.raw);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            // --- MODUL CSR: CHART KEGIATAN BULANAN ---
                            const ctxBarCsr = document.getElementById('chartBarCsr');
                            if (ctxBarCsr) {
                                const labelBulan = JSON.parse(ctxBarCsr.getAttribute('data-bulan') || '[]');
                                const jumlahKegiatan = JSON.parse(ctxBarCsr.getAttribute('data-jumlah') || '[]');

                                const ctx = ctxBarCsr.getContext('2d');
                                const gradienBarHijauBiru = ctx.createLinearGradient(0, 0, 0, 180);
                                gradienBarHijauBiru.addColorStop(0, '#27B78F');
                                gradienBarHijauBiru.addColorStop(0.5, '#4ECDB8');
                                gradienBarHijauBiru.addColorStop(1, '#12B4C9');

                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labelBulan, // Berisi ['Jan', 'Feb', 'Mar', ..., 'Des']
                                        datasets: [{
                                            label: 'Jumlah Kegiatan',
                                            data: jumlahKegiatan,
                                            backgroundColor: gradienBarHijauBiru,
                                            borderRadius: 4,
                                            borderSkipped: false,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return ` ${context.raw} Program`;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 9,
                                                        weight: 'bold'
                                                    },
                                                    color: '#000000',
                                                    minRotation: 45, // Teks dimiringkan 45 derajat agar tidak tabrakan
                                                    maxRotation: 45, // Mengunci sudut miringnya
                                                    autoSkip: false // Semua bulan (Jan-Des) tetap tampil
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                grace: '15%',
                                                ticks: {
                                                    stepSize: 1,
                                                    font: {
                                                        size: 10,
                                                        weight: 'bold'
                                                    },
                                                    color: '#000000'
                                                },
                                                grid: {
                                                    color: '#f3f4f6'
                                                }
                                            }
                                        },
                                        animation: {
                                            onComplete: function() {
                                                const chartInstance = this;
                                                const ctx = chartInstance.ctx;
                                                ctx.font = 'bold 10px sans-serif';
                                                ctx.textAlign = 'center';
                                                ctx.textBaseline = 'bottom';
                                                ctx.fillStyle = '#000000';

                                                chartInstance.data.datasets.forEach(function(dataset, i) {
                                                    const meta = chartInstance.getDatasetMeta(i);
                                                    meta.data.forEach(function(bar, index) {
                                                        const data = dataset.data[index];
                                                        if (data > 0) {
                                                            ctx.fillText(data, bar.x, bar.y - 3);
                                                        }
                                                    });
                                                });
                                            }
                                        }
                                    }
                                });
                            }



                            // --- MODUL SOSMED: CHART TREN POSTINGAN ---
                            const ctxSosmedEl = document.getElementById('chartSosmedBulanan');
                            if (ctxSosmedEl) {
                                const ctxSosmed = ctxSosmedEl.getContext('2d');

                                const initialMonthlyData = @json($trenPostinganBulanan ?? ($dataTrenSosmed ?? []));

                                new Chart(ctxSosmed, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                                            'Nov', 'Des'
                                        ],
                                        datasets: [{
                                            label: 'Total Postingan',
                                            data: initialMonthlyData,
                                            backgroundColor: '#12B4C9',
                                            borderColor: '#27B78F',
                                            borderWidth: 1,
                                            borderRadius: 3,
                                            barThickness: 6,
                                        }]
                                    },
                                    options: {
                                        indexAxis: 'y',
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return ` ${context.raw} Postingan`;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                beginAtZero: true,
                                                grid: {
                                                    color: '#f1f5f9'
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 8
                                                    },
                                                    color: '#000000',
                                                    precision: 0
                                                }
                                            },
                                            y: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 7, // <-- Ubah ukuran font di sini (misal: 7 atau 6 agar lebih kecil dan tidak tabrakan)
                                                        weight: 'bold'
                                                    },
                                                    color: '#000000',
                                                    autoSkip: false
                                                }
                                            }
                                        },
                                        animation: {
                                            onComplete: function() {
                                                const chartInstance = this;
                                                const ctx = chartInstance.ctx;
                                                // Ukuran font angka di samping batang juga bisa dikecilkan di sini jika perlu
                                                ctx.font = 'bold 8px sans-serif';
                                                ctx.textAlign = 'left';
                                                ctx.textBaseline = 'middle';
                                                ctx.fillStyle = '#000000';

                                                chartInstance.data.datasets.forEach(function(dataset, i) {
                                                    const meta = chartInstance.getDatasetMeta(i);
                                                    meta.data.forEach(function(bar, index) {
                                                        const data = dataset.data[index];
                                                        if (data > 0) {
                                                            ctx.fillText(data, bar.x + 4, bar.y);
                                                        }
                                                    });
                                                });
                                            }
                                        }
                                    }
                                });
                            }

                            // --- MODUL BERITA: CHART SIFAT PEMBERITAAN ---
                            const ctxJumlahPemberitaan = document.getElementById('chartJumlahPemberitaan');
                            if (ctxJumlahPemberitaan) {
                                new Chart(ctxJumlahPemberitaan.getContext('2d'), {
                                    type: 'bar',
                                    data: {
                                        labels: ['Realisasi Sifat'],
                                        datasets: [{
                                                label: 'Internal',
                                                data: [dataSifatBerita.Internal ?? 0],
                                                backgroundColor: '#0F172A',
                                                borderRadius: 4,
                                                barThickness: 35
                                            },
                                            {
                                                label: 'Eksternal',
                                                data: [dataSifatBerita.Eksternal ?? 0],
                                                backgroundColor: '#4ADE80',
                                                borderRadius: 4,
                                                barThickness: 35
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    boxWidth: 10,
                                                    font: {
                                                        size: 10,
                                                        weight: 'bold'
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                stacked: true,
                                                grid: {
                                                    display: false
                                                }
                                            },
                                            y: {
                                                stacked: true,
                                                beginAtZero: true,
                                                grid: {
                                                    color: '#F3F4F6'
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                            // --- MODUL BERITA: CHART SPOKESPERSON ---
                            const ctxSpokesperson = document.getElementById('chartSpokesperson');
                            if (ctxSpokesperson) {
                                const labelsSpk = dataSpokesperson.map(item => item.spokeperson ? item.spokeperson : 'No Name');
                                const valuesSpk = dataSpokesperson.map(item => Number(item.total) || 0);

                                new Chart(ctxSpokesperson.getContext('2d'), {
                                    type: 'bar',
                                    data: {
                                        labels: labelsSpk,
                                        datasets: [{
                                            label: 'Kutipan',
                                            data: valuesSpk,
                                            backgroundColor: '#1E293B',
                                            borderRadius: 4,
                                            barThickness: 12
                                        }]
                                    },
                                    options: {
                                        indexAxis: 'y',
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        scales: {
                                            x: {
                                                beginAtZero: true,
                                                grid: {
                                                    color: '#F3F4F6'
                                                },
                                                ticks: {
                                                    stepSize: 1
                                                }
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    </script>

</body>

</html>
