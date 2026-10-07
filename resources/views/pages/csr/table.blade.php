<div class="bg-white rounded-2xl shadow-sm border border-black overflow-hidden">
    <!-- Form Filter Global & Pencarian Form Wrapper -->
    <form id="formFilterTabular" action="{{ route('pages.csr.index') }}" method="GET"
        onsubmit="event.preventDefault(); fetchFilteredData();">
        <!-- Ambil parameter tahun agar filter tidak mereset tahun yang sedang aktif -->
        <input type="hidden" name="tahun" id="filterTahun" value="{{ $tahunTerpilih }}">

        <!-- Hidden Inputs untuk Menampung Parameter Global Filter dari Layout Header -->
        <input type="hidden" name="bulan" id="filterBulanGlobal" value="{{ request('bulan') }}">
        <input type="hidden" name="g_pilar" id="filterPilarGlobal" value="{{ request('g_pilar') }}">
        <input type="hidden" name="g_program" id="filterProgram" value="{{ request('g_program') }}">
        <input type="hidden" name="g_kabupaten" id="filterKabupatenGlobal" value="{{ request('g_kabupaten') }}">
        <input type="hidden" name="g_desa" id="filterDesaGlobal" value="{{ request('g_desa') }}">
        <input type="hidden" name="g_status" id="filterStatusGlobal" value="{{ request('g_status') }}">

        <!-- Hidden input untuk menyimpan state sorting -->
        <input type="hidden" name="sort_by" id="sortByInput" value="{{ request('sort_by') }}">
        <input type="hidden" name="sort_order" id="sortOrderInput" value="{{ request('sort_order') }}">

        <!-- Top Bar Table: Search & Action -->
        <div class="p-3 flex flex-col sm:flex-row justify-between items-center gap-3 border-b border-black">
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <h3 class="font-bold text-black text-xs whitespace-nowrap">Daftar Kegiatan CSR</h3>

                <!-- Reset Filter Button jika ada filter aktif -->
                <div id="resetFilterWrapper">
                    @if (request()->anyFilled([
                            'bulan',
                            'g_pilar',
                            'g_kabupaten',
                            'g_desa',
                            'search',
                            'sort_by',
                            'nama_program',
                            'status',
                            'tpb',
                            'asta_cita',
                        ]))
                        <a href="{{ route('pages.csr.index', ['tahun' => $tahunTerpilih]) }}"
                            class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded-lg font-bold hover:bg-red-100 transition decoration-transparent">
                            <i class="fas fa-times mr-1"></i> Bersihkan Filter
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                <!-- Fitur Search -->
                <div class="relative w-full sm:w-56">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                        <i class="fas fa-search text-black text-[10px]"></i>
                    </span>
                    <input type="text" name="search" id="inputSearch" value="{{ request('search') }}"
                        oninput="debounceSearch()"
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl pl-8 pr-3 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium placeholder-gray-400"
                        placeholder="Cari data program...">
                </div>

                <!-- BUTTON BUKA MODAL import spreadsheet -->
                <button type="button" onclick="openImportModal()" title="Paste Data dari Spreadsheet"
                    class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-xl transition shadow-sm border border-black">
                    <i class="fas fa-paste"></i> Paste Spreadsheet
                </button>

                <!-- Tombol Download Excel -->
                <button type="button" onclick="openModalExport()"
                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Unduh Laporan Excel
                </button>

                <!-- Tombol Tambah Program -->
                <button type="button" onclick="openFormModal('{{ route('csr.create') }}')"
                    class="flex items-center gap-1.5 bg-teal-500 hover:bg-teal-600 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Program
                </button>
            </div>
        </div>

        <!-- Main Tabular Area -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1650px] border border-black">
                <thead>
                    <!-- Row 1: Judul Kolom -->
                    <tr class="bg-teal-500 text-white text-[10px] font-bold uppercase tracking-wider">
                        <th class="py-1.5 px-1.5 w-8 text-center border border-black">NO</th>
                        <th class="py-1.5 px-1.5 w-16 text-center border border-black">Aksi</th>
                        <!-- Header Nama Program + Sorting -->
                        <th class="py-1.5 px-1.5 border border-black w-52 cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('nama_program')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Nama Program</span>
                                <i id="sort-icon-nama_program"
                                    class="fas {{ request('sort_by') == 'nama_program' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>
                        <!-- Header Pilar + Sorting -->
                        <th class="py-1.5 px-1.5 w-20 border border-black cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('pilar')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Pilar</span>
                                <i id="sort-icon-pilar"
                                    class="fas {{ request('sort_by') == 'pilar' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>

                        <!-- Header Bulan & Tahun + Sorting -->
                        <th class="py-1.5 px-1.5 w-28 border border-black cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('bulan')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Bulan / Tahun</span>
                                <i id="sort-icon-bulan"
                                    class="fas {{ request('sort_by') == 'bulan' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>

                        <!-- Header TPB + Sorting -->
                        <th class="py-1.5 px-1.5 w-40 border border-black cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('tpb')">
                            <div class="flex items-center justify-between gap-1">
                                <span>TPB</span>
                                <i id="sort-icon-tpb"
                                    class="fas {{ request('sort_by') == 'tpb' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>

                        <!-- Header Asta Cita + Sorting -->
                        <th class="py-1.5 px-1.5 w-20 border border-black cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('asta_cita')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Asta Cita</span>
                                <i id="sort-icon-asta_cita"
                                    class="fas {{ request('sort_by') == 'asta_cita' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>

                        <th class="py-1.5 px-1.5 w-16 text-center border border-black">Cabang</th>
                        <th class="py-1.5 px-1.5 border border-black w-48">Bentuk Bantuan</th>
                        <th class="py-1.5 px-1.5 border border-black w-52">Realisasi Program</th>
                        <!-- Header Wilayah (Kabupaten & Desa) + Sorting -->
                        <th class="py-1.5 px-1.5 border border-black w-36 cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('wilayah')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Wilayah (Kab/Desa)</span>
                                <i id="sort-icon-wilayah"
                                    class="fas {{ request('sort_by') == 'wilayah' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>
                        <!-- Header Biaya Program + Sorting -->
                        <th class="py-1.5 px-1.5 border border-black text-right w-28 cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('biaya_program')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Biaya Program</span>
                                <i id="sort-icon-biaya_program"
                                    class="fas {{ request('sort_by') == 'biaya_program' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>
                        <!-- Header Biaya Realisasi + Sorting -->
                        <th class="py-1.5 px-1.5 border border-black text-right w-28 cursor-pointer select-none hover:bg-teal-600 transition"
                            onclick="toggleSort('biaya_realisasi')">
                            <div class="flex items-center justify-between gap-1">
                                <span>Biaya Realisasi</span>
                                <i id="sort-icon-biaya_realisasi"
                                    class="fas {{ request('sort_by') == 'biaya_realisasi' ? (request('sort_order') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-white text-[10px]"></i>
                            </div>
                        </th>
                        <th class="py-1.5 px-1.5 border border-black w-20 text-center">Link IG</th>
                        <th class="py-1.5 px-1.5 border border-black w-20 text-center">Link Berita</th>
                        <th class="py-1.5 px-1.5 border border-black w-20 text-center">Link GDrive</th>
                        <th class="py-1.5 px-1.5 w-20 text-center border border-black">Status</th>
                    </tr>
                </thead>
                <tbody id="tableBodyCsr" class="divide-y divide-black">
                    @forelse($daftarCsr as $index => $csr)
                        @php
                            $igLinks = $csr->link_ig
                                ? array_values(array_filter(array_map('trim', explode("\n", $csr->link_ig))))
                                : [];
                            $beritaLinks = $csr->link_berita
                                ? array_values(array_filter(array_map('trim', explode("\n", $csr->link_berita))))
                                : [];
                            $gdriveLink = $csr->link_gdrive ? trim($csr->link_gdrive) : '';
                        @endphp

                        <tr id="row-{{ $csr->id_csr }}"
                            class="hover:bg-teal-50/40 text-[11px] font-semibold text-black transition-colors">

                            <!-- 1. Nomor -->
                            <td
                                class="py-1 px-1.5 text-center font-bold border border-black bg-gray-50/50 text-[10px]">
                                {{ $index + 1 }}
                            </td>

                            <!-- 2. Tombol Aksi -->
                            <td class="py-1 px-1.5 text-center border border-black"
                                onclick="event.stopPropagation();">
                                <div class="flex items-center justify-center space-x-1">
                                    <button type="button"
                                        onclick="openFormModal('{{ route('csr.edit', $csr->id_csr) }}')"
                                        class="p-0.5 bg-gray-100 hover:bg-blue-100 text-black hover:text-blue-600 rounded transition"
                                        title="Edit via Modal">
                                        <i class="fas fa-edit text-[9px]"></i>
                                    </button>
                                    <button type="button"
                                        onclick="confirmDeleteCSR('{{ route('csr.destroy', $csr->id_csr) }}', '{{ $csr->nama_program }}')"
                                        class="p-0.5 bg-gray-100 hover:bg-red-100 text-black hover:text-red-600 rounded transition"
                                        title="Hapus Data">
                                        <i class="fas fa-trash-alt text-[9px]"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- 3. Nama Program -->
                            <td class="py-1 px-1.5 font-bold border border-black text-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'nama_program', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'text')">
                                <span class="view-state">{{ $csr->nama_program }}</span>
                                <input type="text" name="nama_program" value="{{ $csr->nama_program }}"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] font-semibold text-black focus:outline-none"
                                    placeholder="Masukkan nama program...">
                            </td>

                            <!-- 4. Pilar (Disesuaikan dengan Relasi Tabel Pilar) -->
                            <td class="py-1 px-1.5 border border-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'pilar', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'select')">
                                @php
                                    $badgeColor = match ($csr->nama_pilar) {
                                        'Sosial' => 'bg-blue-100 text-blue-800',
                                        'Ekonomi' => 'bg-emerald-100 text-emerald-800',
                                        'Lingkungan' => 'bg-amber-100 text-amber-800',
                                        default => 'bg-gray-100 text-black',
                                    };
                                @endphp
                                <div class="view-state">
                                    <span class="px-1 py-0.5 text-[9px] font-bold rounded {{ $badgeColor }}">
                                        {{ $csr->nama_pilar ?? '-' }}
                                    </span>
                                </div>
                                <select name="pilar"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] text-black focus:outline-none">
                                    <option value="Sosial" {{ $csr->nama_pilar == 'Sosial' ? 'selected' : '' }}>Sosial
                                    </option>
                                    <option value="Ekonomi" {{ $csr->nama_pilar == 'Ekonomi' ? 'selected' : '' }}>
                                        Ekonomi</option>
                                    <option value="Lingkungan"
                                        {{ $csr->nama_pilar == 'Lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                                </select>
                            </td>

                            <!-- 5. Bulan & Tahun (Digabung) -->
                            <td class="py-1 px-1.5 border border-black text-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'periode', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'double_input')">
                                <div class="view-state">
                                    <span>{{ $csr->bulan_realisasi }} {{ $csr->tahun_realisasi }}</span>
                                </div>
                                <div class="edit-state hidden space-y-1">
                                    <select name="bulan_realisasi"
                                        class="w-full px-1 py-0.5 border border-teal-500 rounded text-[10px] text-black focus:outline-none">
                                        @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bln)
                                            <option value="{{ $bln }}"
                                                {{ $csr->bulan_realisasi == $bln ? 'selected' : '' }}>
                                                {{ $bln }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="tahun_realisasi" value="{{ $csr->tahun_realisasi }}"
                                        placeholder="Tahun"
                                        class="w-full px-1 py-0.5 border border-teal-500 rounded text-[10px] text-black focus:outline-none">
                                </div>
                            </td>

                            <!-- 6. TPB -->
                            <td class="py-1 px-1.5 border border-black text-[10px] text-black max-w-[150px] min-w-[130px] cursor-pointer select-none"
                                title="{{ $csr->tpb }}"
                                ondblclick="enableCellEdit(this, 'tpb', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'select')">
                                <div class="view-state whitespace-normal break-words leading-tight">
                                    {{ $csr->tpb ?? '-' }}
                                </div>
                                <select name="tpb"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[10px] text-black focus:outline-none">
                                    @foreach (['1. Tanpa Kemiskinan', '2. Tanpa Kelaparan', '3. Kehidupan Sehat dan Sejahtera', '4. Pendidikan Berkualitas', '5. Kesetaraan Gender', '6. Air Bersih dan Sanitasi Layak', '7. Energi Bersih dan Terjangkau', '8. Pekerjaan Layak dan Pertumbuhan Ekonomi', '9. Industri, Inovasi dan Infrastruktur', '10. Berkurangnya Kesenjangan', '11. Kota dan Permukiman yang Berkelanjutan', '12. Konsumsi dan Produksi yang Bertanggung Jawab', '13. Penanganan Perubahan Iklim', '14. Ekosistem Kelautan', '15. Ekosistem Daratan', '16. Perdamaian, Keadilan dan Kelembagaan yang Tangguh', '17. Kemitraan untuk Mencapai Tujuan'] as $tpbOption)
                                        <option value="{{ $tpbOption }}"
                                            {{ $csr->tpb == $tpbOption ? 'selected' : '' }}>
                                            {{ $tpbOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 7. Asta Cita -->
                            <td class="py-1 px-1.5 text-center font-black border border-black text-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'asta_cita', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'select')">
                                <span class="view-state">{{ preg_replace('/[^0-9]/', '', $csr->asta_cita) }}</span>
                                <select name="asta_cita"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] text-center text-black focus:outline-none">
                                    @for ($i = 1; $i <= 8; $i++)
                                        <option value="Asta Cita {{ $i }}"
                                            {{ preg_replace('/[^0-9]/', '', $csr->asta_cita) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </td>

                            <!-- 8. Cabang -->
                            <td class="py-1 px-1.5 text-center border border-black text-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'cabang', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'select')">
                                <span class="view-state">{{ $csr->cabang ?? '-' }}</span>
                                <select name="cabang"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] text-center text-black focus:outline-none">
                                    @foreach (['PNK'] as $cab)
                                        <option value="{{ $cab }}"
                                            {{ $csr->cabang == $cab ? 'selected' : '' }}>
                                            {{ $cab }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 9. Bentuk Bantuan -->
                            <td class="py-1 px-1.5 text-[10px] font-medium text-black border border-black cursor-pointer select-none min-w-[150px]"
                                ondblclick="enableCellEdit(this, 'bentuk_bantuan', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'textarea')">
                                <div class="view-state whitespace-normal break-words leading-tight">
                                    {{ $csr->bentuk_bantuan ?? '-' }}
                                </div>
                                <textarea name="bentuk_bantuan" rows="2" maxlength="255"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] focus:outline-none resize-y">{{ $csr->bentuk_bantuan }}</textarea>
                            </td>

                            <!-- 10. Realisasi Program -->
                            <td class="py-1 px-1.5 text-[10px] font-medium text-black border border-black max-w-[180px] min-w-[150px] cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'realisasi_program', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'textarea')">
                                <div class="view-state whitespace-normal break-words leading-tight">
                                    {{ $csr->realisasi_program ?? '-' }}
                                </div>
                                <textarea name="realisasi_program" rows="2"
                                    class="edit-state hidden w-full px-1 py-0.5 border border-teal-500 rounded text-[11px] focus:outline-none resize-y">{{ $csr->realisasi_program }}</textarea>
                            </td>

                            <!-- 11. Wilayah (Kabupaten & Desa) -->
                            <td class="py-1 px-1.5 border border-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'wilayah', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'double_input')">
                                <div class="view-state">
                                    <div class="text-[10px] font-bold text-slate-800">{{ $csr->kabupaten }}</div>
                                    <div class="text-[9px] text-slate-500 font-medium">{{ $csr->desa }}</div>
                                </div>
                                <div class="edit-state hidden space-y-1">
                                    <input type="text" name="kabupaten" value="{{ $csr->kabupaten }}"
                                        placeholder="Kabupaten" maxlength="50"
                                        class="w-full px-1 py-0.5 border border-teal-500 rounded text-[10px] focus:outline-none">
                                    <input type="text" name="desa" value="{{ $csr->desa }}"
                                        placeholder="Desa" maxlength="50"
                                        class="w-full px-1 py-0.5 border border-teal-500 rounded text-[10px] focus:outline-none">
                                </div>
                            </td>

                            <!-- 12. Biaya Program -->
                            <td class="py-1 px-1.5 border border-black text-right font-black text-black cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'biaya_program', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'number')">
                                <span class="view-state">Rp
                                    {{ number_format($csr->biaya_program, 0, ',', '.') }}</span>
                                <div class="edit-state hidden flex items-center justify-end">
                                    <span class="mr-0.5 text-[10px]">Rp</span>
                                    <input type="number" name="biaya_program" value="{{ $csr->biaya_program }}"
                                        class="w-20 px-1 py-0.5 border border-teal-500 rounded text-[11px] text-right focus:outline-none">
                                </div>
                            </td>

                            <!-- 13. Biaya Realisasi -->
                            <td class="py-1 px-1.5 border border-black text-right font-black text-emerald-700 cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'biaya_realisasi', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'number')">
                                <span class="view-state">Rp
                                    {{ number_format($csr->biaya_realisasi ?? 0, 0, ',', '.') }}</span>
                                <div class="edit-state hidden flex items-center justify-end">
                                    <span class="mr-0.5 text-[10px]">Rp</span>
                                    <input type="number" name="biaya_realisasi" value="{{ $csr->biaya_realisasi }}"
                                        class="w-20 px-1 py-0.5 border border-teal-500 rounded text-[11px] text-right focus:outline-none">
                                </div>
                            </td>

                            <!-- 14. Link IG -->
                            <td class="py-1 px-1.5 border border-black text-center relative cursor-pointer select-none"
                                ondblclick="openLinkEditModal(this, 'link_ig', '{{ route('pages.csr.inline-update', $csr->id_csr) }}')">
                                <div class="view-state" onclick="event.stopPropagation();">
                                    @if (count($igLinks))
                                        <button type="button" onclick="toggleLinkDropdown(this)"
                                            class="text-pink-600 hover:text-pink-800 transition inline-flex items-center gap-0.5">
                                            <i class="fab fa-instagram text-sm"></i>
                                            @if (count($igLinks) > 1)
                                                <span
                                                    class="text-[8px] font-black bg-pink-100 rounded-full px-1">{{ count($igLinks) }}</span>
                                            @endif
                                        </button>
                                        <div
                                            class="link-dropdown hidden absolute z-30 top-full left-1/2 -translate-x-1/2 mt-1 bg-white border border-black rounded-lg shadow-lg p-1.5 w-52 text-left max-h-48 overflow-y-auto">
                                            @foreach ($igLinks as $i => $link)
                                                <a href="{{ $link }}" target="_blank"
                                                    title="{{ $link }}"
                                                    class="block text-[10px] font-semibold text-pink-600 hover:underline truncate py-0.5">
                                                    {{ $i + 1 }}. {{ $link }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-black">-</span>
                                    @endif
                                </div>
                                <textarea name="link_ig" rows="1" placeholder="Satu link per baris"
                                    class="edit-state hidden w-28 px-1 py-0.5 border border-teal-500 rounded text-[9px] focus:outline-none">{{ $csr->link_ig }}</textarea>
                            </td>

                            <!-- 15. Link Berita -->
                            <td class="py-1 px-1.5 border border-black text-center relative cursor-pointer select-none"
                                ondblclick="openLinkEditModal(this, 'link_berita', '{{ route('pages.csr.inline-update', $csr->id_csr) }}')">
                                <div class="view-state" onclick="event.stopPropagation();">
                                    @if (count($beritaLinks))
                                        <button type="button" onclick="toggleLinkDropdown(this)"
                                            class="text-blue-600 hover:text-blue-800 transition inline-flex items-center gap-0.5">
                                            <i class="fas fa-newspaper text-sm"></i>
                                            @if (count($beritaLinks) > 1)
                                                <span
                                                    class="text-[8px] font-black bg-blue-100 rounded-full px-1">{{ count($beritaLinks) }}</span>
                                            @endif
                                        </button>
                                        <div
                                            class="link-dropdown hidden absolute z-30 top-full left-1/2 -translate-x-1/2 mt-1 bg-white border border-black rounded-lg shadow-lg p-1.5 w-52 text-left max-h-48 overflow-y-auto">
                                            @foreach ($beritaLinks as $i => $link)
                                                <a href="{{ $link }}" target="_blank"
                                                    title="{{ $link }}"
                                                    class="block text-[10px] font-semibold text-blue-600 hover:underline truncate py-0.5">
                                                    {{ $i + 1 }}. {{ $link }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-black">-</span>
                                    @endif
                                </div>
                                <textarea name="link_berita" rows="1" placeholder="Satu link per baris"
                                    class="edit-state hidden w-28 px-1 py-0.5 border border-teal-500 rounded text-[9px] focus:outline-none">{{ $csr->link_berita }}</textarea>
                            </td>
                            <!-- 16. Link GDrive (Warna Asli Google Drive) -->
                            <td class="py-1 px-1.5 border border-black text-center relative cursor-pointer select-none"
                                ondblclick="openLinkEditModal(this, 'link_gdrive', '{{ route('pages.csr.inline-update', $csr->id_csr) }}')">

                                <!-- State Tampilan Normal -->
                                <div class="view-state" onclick="event.stopPropagation();">
                                    @if (!empty(trim($csr->link_gdrive)))
                                        <a href="{{ $csr->link_gdrive }}" target="_blank"
                                            title="{{ $csr->link_gdrive }}"
                                            class="transition inline-flex items-center justify-center hover:opacity-80">
                                            <i class="fab fa-google-drive text-base text-[#0F9D58]"></i>
                                        </a>
                                    @else
                                        <span class="text-black">-</span>
                                    @endif
                                </div>

                                <textarea name="link_gdrive" rows="1" class="edit-state hidden">{{ $csr->link_gdrive }}</textarea>
                            </td>

                            <!-- 17. Status -->
                            <td class="py-1 px-1.5 border border-black text-center cursor-pointer select-none"
                                ondblclick="enableCellEdit(this, 'status', '{{ $csr->id_csr }}', '{{ route('pages.csr.inline-update', $csr->id_csr) }}', 'select')">
                                <div class="view-state">
                                    @if (strtolower(trim($csr->status ?? '')) === 'done')
                                        <span
                                            class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-extrabold rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                                            <span class="w-1 h-1 rounded-full bg-teal-500"></span> Done
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-extrabold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1 h-1 rounded-full bg-rose-400"></span> Undone
                                        </span>
                                    @endif
                                </div>
                                <select name="status"
                                    class="edit-state hidden px-1 py-0.5 border border-teal-500 rounded text-[10px] text-black focus:outline-none">
                                    <option value="done"
                                        {{ strtolower(trim($csr->status ?? '')) === 'done' ? 'selected' : '' }}>
                                        Done
                                    </option>
                                    <option value="undone"
                                        {{ strtolower(trim($csr->status ?? '')) === 'undone' ? 'selected' : '' }}>
                                        Undone
                                    </option>
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17"
                                class="py-4 px-4 text-center font-bold text-black bg-gray-50 border border-black text-[11px]">
                                <i class="fas fa-folder-open text-lg mb-1 block text-black"></i>
                                Tidak ada data kegiatan CSR ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-black">
            <button type="button" onclick="openFormModal('{{ route('csr.create') }}')"
                class="w-full flex items-center justify-center gap-2 py-2.5 border-2 border-dashed border-teal-400 text-teal-600 hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 font-bold text-[11px] rounded-xl transition-all group">
                <i class="fas fa-plus-circle text-sm group-hover:scale-110 transition-transform"></i>
                Tambah Program Baru
            </button>
        </div>
    </form>
</div>
<!-- Modal unduh excel -->
<div id="modalExport"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-800">Pilih Bulan Laporan CSR</h3>
            <button type="button" onclick="closeModalExport()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form mengarah ke route export excel kita -->
        <form action="{{ route('csr.export.excel') }}" method="GET">
            <!-- Otomatis ikut membawa tahun aktif yang sedang dibuka di halaman saat ini -->
            <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-2">Pilih Bulan</label>
                <select name="bulan" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Bulan --</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                    <option value="Maret">Maret</option>
                    <option value="April">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Juni">Juni</option>
                    <option value="Juli">Juli</option>
                    <option value="Agustus" selected>Agustus</option> <!-- Default bulan berjalan -->
                    <option value="September">September</option>
                    <option value="Oktober">Oktober</option>
                    <option value="November">November</option>
                    <option value="Desember">Desember</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModalExport()"
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">
                    Unduh Excel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Sederhana untuk Buka/Tutup Modal -->
<script>
    function openModalExport() {
        document.getElementById('modalExport').classList.remove('hidden');
    }

    function closeModalExport() {
        document.getElementById('modalExport').classList.add('hidden');
    }
</script>


<!-- MODAL IMPORT PASTE SPREADSHEET  -->

<div id="modalImportPaste"
    class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
    <div
        class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-5xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">

        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-emerald-600">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-excel text-white"></i> Import Data CSR dari Spreadsheet (Tahun
                {{ $tahunTerpilih }})
            </h4>
            <button type="button" onclick="closeImportModal()"
                class="text-emerald-100 hover:text-white cursor-pointer transition">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <!-- Form Import -->
        <form id="formImportPaste" onsubmit="submitImportPaste(event)" action="{{ route('csr.importPaste') }}"
            method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahunTerpilih }}">
            <!-- Input rahasia untuk membedakan antara Preview dan Simpan -->
            <input type="hidden" name="is_preview" id="isPreviewInput" value="1">

            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                <div
                    class="text-[11px] text-slate-800 bg-emerald-50 border border-emerald-200 p-3 rounded-xl space-y-1">
                    <p class="font-bold text-emerald-900">Panduan:</p>
                    <p>1. Blok dan "copy" baris data dari Spreadsheet Anda (tanpa baris header kolom).</p>
                    <p>2. Salin & Paste Sel Excel di bawah ini. Pastikan urutan kolom sesuai standar.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Tempel Data Spreadsheet CSR di Sini <span
                            class="text-red-500">*</span></label>
                    <textarea name="excel_text" id="importExcelText" rows="5" required
                        placeholder="Tempel baris data dari spreadsheet di sini... "
                        class="w-full text-xs font-mono bg-slate-50 border border-slate-300 rounded-xl p-3 focus:outline-none focus:border-emerald-600 focus:bg-white placeholder-gray-400"></textarea>
                </div>

                <!-- TABLE PREVIEW (Awalnya Sembunyi) -->
                <div id="previewContainer" class="hidden mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700">
                            <i class="fas fa-eye text-emerald-600"></i> Preview & Edit Data (Geser ke samping untuk
                            melihat semua kolom)
                        </label>
                        <span class="text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Bisa di-scroll &
                            diketik langsung</span>
                    </div>
                    <!-- max-w-full dengan overflow-x-auto agar bisa diskrol jika melebihi layar -->
                    <div class="border border-slate-200 rounded-xl overflow-x-auto max-h-80 shadow-inner">
                        <table class="w-max text-left text-[10px] border-collapse bg-white">
                            <thead class="bg-slate-100 sticky top-0 z-10 shadow-sm text-slate-600 font-bold">
                                <tr>
                                    <th class="px-2 py-2 border-b min-w-[160px]">Program</th>
                                    <th class="px-2 py-2 border-b min-w-[140px]">Bentuk Bantuan</th>
                                    <th class="px-2 py-2 border-b min-w-[100px]">Pilar</th>
                                    <th class="px-2 py-2 border-b min-w-[140px]">TPB</th>
                                    <th class="px-2 py-2 border-b min-w-[120px]">Asta Cita</th>
                                    <th class="px-2 py-2 border-b min-w-[70px]">Cabang</th>
                                    <th class="px-2 py-2 border-b min-w-[110px]">Biaya Program</th>
                                    <th class="px-2 py-2 border-b min-w-[110px]">Biaya Realisasi</th>
                                    <th class="px-2 py-2 border-b min-w-[160px]">Realisasi Program</th>
                                    <th class="px-2 py-2 border-b min-w-[90px]">Bulan</th>
                                    <th class="px-2 py-2 border-b min-w-[90px]">Status</th>
                                    <th class="px-2 py-2 border-b min-w-[110px]">Kabupaten</th>
                                    <th class="px-2 py-2 border-b min-w-[110px]">Desa</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody" class="divide-y divide-slate-100 text-slate-700">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Footer Modal -->
            <div class="px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2 bg-slate-50">
                <button type="button" onclick="closeImportModal()"
                    class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                    Batal
                </button>
                <!-- Tombol Cek Preview -->
                <button type="button" id="btnCheckPreview" onclick="handlePreview()"
                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-blue-200 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Cek Preview
                </button>
                <!-- Tombol Simpan (Disembunyikan sebelum preview berhasil) -->
                <button type="submit" id="btnSubmitImport"
                    class="hidden px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-200 cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Proses & Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================================= -->
<!-- MODAL FORM TAMBAH / EDIT MENGAMBANG (Dua arah via AJAX: create & edit) -->
<!-- ================================================================================= -->
<div id="modalFormCsr"
    class="hidden fixed inset-0 z-[95] flex items-center justify-center p-4 transition-all duration-300 bg-slate-900/40 backdrop-blur-xs">
    <div
        class="bg-white rounded-2xl max-w-xl w-full max-h-[88vh] shadow-2xl border border-black transform scale-95 opacity-0 transition-all flex flex-col overflow-hidden">
        <div id="modalFormContent" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            <div class="text-center py-8 font-bold text-xs text-gray-600">
                <i class="fas fa-spinner fa-spin mr-2 text-emerald-600"></i>Memuat formulir...
            </div>
        </div>
    </div>
</div>

<!-- ================================================================================= -->
<!-- MODAL KONFIRMASI HAPUS  -->
<!-- ================================================================================= -->
<div id="modalDeleteConfirmation"
    class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 transition-all duration-300 bg-slate-900/40 backdrop-blur-xs">
    <div
        class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-black transform scale-100 transition-all text-center">
        <div
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4 border border-red-200">
            <i class="fas fa-exclamation-triangle text-red-500 text-lg animate-bounce"></i>
        </div>
        <h3 class="text-base font-extrabold text-black mb-1">Konfirmasi Hapus</h3>
        <p class="text-xs text-gray-600 leading-relaxed mb-4">
            Apakah Anda yakin ingin menghapus data program <br>
            <span id="deleteTargetName" class="font-bold text-red-600 text-sm"></span>?
        </p>
        <form id="formDeleteAction" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-black border border-black font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-xs rounded-xl transition shadow-md shadow-red-200">
                    Hapus Permanen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================================= -->
<!-- MODAL EDIT LINK IG / BERITA -->
<!-- ================================================================================= -->
<div id="modalLinkEdit"
    class="hidden fixed inset-0 z-[95] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs transition-all duration-300">
    <div
        class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-black transform scale-95 opacity-0 transition-all">
        <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
            <h3 class="text-sm font-extrabold text-black flex items-center gap-2">
                <i id="modalLinkIcon" class="fas fa-link text-teal-500"></i>
                <span id="modalLinkLabel">Edit Link Media</span>
            </h3>
            <button type="button" onclick="closeLinkModal()" class="text-slate-400 hover:text-black transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div id="modalLinkContainer" class="space-y-2 max-h-64 overflow-y-auto pr-1"></div>

        <div class="flex justify-end gap-2 pt-3 mt-3 border-t border-gray-100">
            <button type="button" onclick="closeLinkModal()"
                class="px-4 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-black rounded-xl transition">
                Batal
            </button>
            <button type="button" onclick="saveLinkModal()"
                class="px-4 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition shadow-sm">
                Simpan
            </button>
        </div>
    </div>
</div>
<script>
    let searchDebounceTimer;
    let linkModalState = {
        cell: null,
        column: null,
        updateUrl: null
    };

    // Opsi dropdown untuk pilihan pilar & bulan
    const listPilarOpts = ['Sosial', 'Ekonomi', 'Lingkungan'];
    const listBulanOpts = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
        'September', 'Oktober', 'November', 'Desember'
    ];

    // EVENT LISTENER: MENDENGARKAN PERUBAHAN FILTER GLOBAL DARI LAYOUT (HEADER NAVBAR)
    window.addEventListener('csr-filter-changed', function(e) {
        const detail = e.detail;

        if (document.getElementById('filterTahun')) {
            document.getElementById('filterTahun').value = detail.tahun || '';
        }
        if (document.getElementById('filterBulanGlobal')) {
            document.getElementById('filterBulanGlobal').value = detail.bulan || '';
        }
        if (document.getElementById('filterPilarGlobal')) {
            document.getElementById('filterPilarGlobal').value = detail.pilar || '';
        }
        if (document.getElementById('filterProgram')) {
            document.getElementById('filterProgram').value = detail.program || '';
        }
        if (document.getElementById('filterKabupatenGlobal')) {
            document.getElementById('filterKabupatenGlobal').value = detail.kabupaten || '';
        }
        if (document.getElementById('filterDesaGlobal')) {
            document.getElementById('filterDesaGlobal').value = detail.desa || '';
        }
        if (document.getElementById('filterStatusGlobal')) {
            document.getElementById('filterStatusGlobal').value = detail.g_status || '';
        }
        fetchFilteredData();
    });

    function executeInjectedScripts(container) {
        const scripts = container.querySelectorAll('script');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    // --- LOGIKA MODAL FORM (TAMBAH / EDIT) DYNAMIC AJAX ---

    function openFormModal(url) {
        const modal = document.getElementById('modalFormCsr');
        const modalBox = modal?.querySelector('div');
        const contentContainer = document.getElementById('modalFormContent');

        if (!modal || !contentContainer) return;

        // Tinggi maksimal ditambah sedikit ke bawah (dari 90vh ke 95vh)
        if (modalBox) {
            modalBox.className =
                "relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-y-auto transform scale-95 opacity-0 transition-all duration-300 flex flex-col";
        }

        contentContainer.innerHTML = `
        <div class="p-8 text-center font-bold text-xs text-gray-500">
            <i class="fas fa-spinner fa-spin mr-2 text-emerald-500 text-sm"></i>Memuat data formulir...
        </div>
    `;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBox?.classList.remove('scale-95', 'opacity-0');
            modalBox?.classList.add('scale-100', 'opacity-100');
        }, 50);

        const tahunAktif = document.getElementById('filterTahun')?.value || new Date().getFullYear();
        const separator = url.includes('?') ? '&' : '?';
        const finalUrl = `${url}${separator}tahun=${tahunAktif}`;

        fetch(finalUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat form');
                return response.text();
            })
            .then(html => {
                contentContainer.innerHTML = html;
                if (typeof executeInjectedScripts === 'function') {
                    executeInjectedScripts(contentContainer);
                }
            })
            .catch(err => {
                contentContainer.innerHTML = `
                <div class="p-6 text-center text-red-500 font-bold text-xs">
                    <i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat formulir. Silakan coba lagi.
                </div>
            `;
                console.error(err);
            });
    }

    function closeFormModal() {
        const modal = document.getElementById('modalFormCsr');
        if (modal) {
            const modalBox = modal.querySelector('div');
            modalBox?.classList.add('scale-95', 'opacity-0');
            modalBox?.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 150);
        }
    }

    // TOGGLE DROPDOWN LINK IG / BERITA
    function toggleLinkDropdown(btn) {
        const target = btn.nextElementSibling;
        document.querySelectorAll('.link-dropdown').forEach(d => {
            if (d !== target) d.classList.add('hidden');
        });
        target?.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.link-dropdown') && !e.target.closest(
                'button[onclick^="toggleLinkDropdown"]')) {
            document.querySelectorAll('.link-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    // Handle klik pagination AJAX (dipasang sekali di luar fetch, bukan diulang tiap fetch)
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#paginationWrapper a');
        if (paginationLink) {
            e.preventDefault();
            fetchFilteredData(paginationLink.href);
        }
    });

    // Tutup modal kalau klik area gelap di luar box (dipasang sekali, bukan lewat window.onclick tiap fetch)
    window.addEventListener('click', function(event) {
        const modalForm = document.getElementById('modalFormCsr');
        const modalDelete = document.getElementById('modalDeleteConfirmation');
        const modalLink = document.getElementById('modalLinkEdit');
        const modalImport = document.getElementById('modalImportPaste');

        if (event.target == modalForm) closeFormModal();
        if (event.target == modalDelete) closeDeleteModal();
        if (event.target == modalLink) closeLinkModal();
        if (event.target == modalImport) closeImportModal();
    });

    document.getElementById('importExcelText')?.addEventListener('input', function() {
        document.getElementById('previewContainer')?.classList.add('hidden');
        document.getElementById('btnCheckPreview')?.classList.remove('hidden');
        document.getElementById('btnSubmitImport')?.classList.add('hidden');
    });

    // FETCH DATA AJAX
    function fetchFilteredData(pageUrl = null) {
        const form = document.getElementById('formFilterTabular');
        const params = new URLSearchParams();

        const fields = {
            tahun: document.getElementById('filterTahun') || document.getElementById('globalTahun'),
            bulan: document.getElementById('filterBulanGlobal') || document.getElementById('globalBulan'),
            g_pilar: document.getElementById('globalPilar') || document.getElementById('filterPilarGlobal'),
            g_kabupaten: document.getElementById('globalKabupaten') || document.getElementById(
                'filterKabupatenGlobal'),
            g_desa: document.getElementById('globalDesa') || document.getElementById('filterDesaGlobal'),
            g_status: document.getElementById('globalStatus'),
            g_program: document.getElementById('globalProgram') || document.getElementById('filterProgram'),
            search: document.getElementById('inputSearch'),
            sort_by: document.getElementById('sortByInput'),
            sort_order: document.getElementById('sortOrderInput'),
        };

        for (const [key, el] of Object.entries(fields)) {
            if (el && el.value !== '') {
                params.append(key, el.value);
            }
        }

        let targetUrl = pageUrl ? pageUrl : (form ? form.action : window.location.pathname);
        if (targetUrl.includes('?')) {
            const baseUrl = targetUrl.split('?')[0];
            const existingParams = new URLSearchParams(targetUrl.split('?')[1]);
            for (const [key, value] of existingParams.entries()) {
                if (!params.has(key)) params.append(key, value);
            }
            targetUrl = baseUrl;
        }

        fetch(`${targetUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response error');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTbody = doc.getElementById('tableBodyCsr');
                const currentTbody = document.getElementById('tableBodyCsr');
                if (newTbody && currentTbody) {
                    currentTbody.innerHTML = newTbody.innerHTML;
                }

                const newPagination = doc.getElementById('paginationWrapper');
                const currentPagination = document.getElementById('paginationWrapper');
                if (newPagination && currentPagination) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                }

                const newResetFilter = doc.getElementById('resetFilterWrapper');
                const currentResetFilter = document.getElementById('resetFilterWrapper');
                if (newResetFilter && currentResetFilter) {
                    currentResetFilter.innerHTML = newResetFilter.innerHTML;
                }

                updateSortIcons();
            })
            .catch(err => {
                console.error(err);
            });
    }

    // LOGIKA MENGUBAH PARAMETER SORTING
    function toggleSort(column) {
        const sortByInput = document.getElementById('sortByInput');
        const sortOrderInput = document.getElementById('sortOrderInput');

        if (!sortByInput || !sortOrderInput) return;

        let currentSortBy = sortByInput.value;
        let currentSortOrder = sortOrderInput.value;

        if (currentSortBy === column) {
            currentSortOrder = (currentSortOrder === 'asc') ? 'desc' : 'asc';
        } else {
            currentSortBy = column;
            currentSortOrder = 'asc';
        }

        sortByInput.value = currentSortBy;
        sortOrderInput.value = currentSortOrder;

        fetchFilteredData();
    }

    function updateSortIcons() {
        const sortByInput = document.getElementById('sortByInput');
        const sortOrderInput = document.getElementById('sortOrderInput');

        if (!sortByInput || !sortOrderInput) return;

        const currentSortBy = sortByInput.value;
        const currentSortOrder = sortOrderInput.value;

        ['bulan', 'tpb', 'asta_cita', 'nama_program', 'pilar', 'wilayah', 'biaya_program',
            'biaya_realisasi'
        ].forEach(col => {
            const icon = document.getElementById(`sort-icon-${col}`);
            if (icon) {
                if (currentSortBy === col) {
                    icon.className =
                        `fas ${currentSortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'} text-white text-[10px]`;
                } else {
                    icon.className = 'fas fa-sort text-white text-[10px]';
                }
            }
        });
    }

    function debounceSearch() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            fetchFilteredData();
        }, 400);
    }

    // ======================================================
    // DELETE MODAL
    // ======================================================
    function confirmDeleteCSR(deleteUrl, programName) {
        const modal = document.getElementById('modalDeleteConfirmation');
        const deleteForm = document.getElementById('formDeleteAction');
        const targetNameElement = document.getElementById('deleteTargetName');

        if (modal && deleteForm && targetNameElement) {
            deleteForm.action = deleteUrl;
            targetNameElement.textContent = programName;
            modal.classList.remove('hidden');
            modal.querySelector('div')?.classList.remove('scale-95', 'opacity-0');
            modal.querySelector('div')?.classList.add('scale-100', 'opacity-100');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalDeleteConfirmation');
        if (modal) {
            modal.querySelector('div')?.classList.add('scale-95', 'opacity-0');
            modal.querySelector('div')?.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
    }


    // ======================================================
    // INLINE EDITING FUNCTIONS
    // ======================================================
    function enableCellEdit(cell, fieldName, id, updateUrl, type = 'text') {
        const viewState = cell.querySelector('.view-state');
        const editState = cell.querySelector('.edit-state');

        if (!viewState || !editState || !editState.classList.contains('hidden')) return;

        viewState.classList.add('hidden');
        editState.classList.remove('hidden');

        let inputEl;
        if (type === 'double_input') {
            inputEl = editState.querySelector('input');
        } else if (['SELECT', 'INPUT', 'TEXTAREA'].includes(editState.tagName)) {
            inputEl = editState;
        } else {
            inputEl = editState.querySelector('input, select, textarea');
        }

        if (inputEl) inputEl.focus();

        const originalValues = {};
        if (type === 'double_input') {
            const inputs = editState.querySelectorAll('input, select');
            inputs.forEach(inp => originalValues[inp.name] = inp.value);
        } else if (inputEl) {
            originalValues[fieldName] = inputEl.value;
        }

        let isSaved = false;

        const handleSave = () => {
            if (isSaved) return;
            isSaved = true;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (type === 'double_input') {
                const inputs = editState.querySelectorAll('input, select');
                let promises = [];

                inputs.forEach(inp => {
                    const colName = inp.name;
                    const colVal = inp.value;

                    if (colVal !== originalValues[colName]) {
                        promises.push(
                            fetch(updateUrl, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    column: colName,
                                    value: colVal
                                })
                            }).then(r => r.json())
                        );
                    }
                });

                if (promises.length === 0) {
                    editState.classList.add('hidden');
                    viewState.classList.remove('hidden');
                    return;
                }

                Promise.all(promises)
                    .then(results => {
                        const allSuccess = results.every(res => res.success);
                        if (allSuccess) {
                            refreshKeepScroll();
                        } else {
                            const errMsg = results.find(res => !res.success)?.message ||
                                'Gagal update data.';
                            alert('Gagal update: ' + errMsg);
                            rollback(editState, originalValues, type);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal koneksi ke server.');
                        rollback(editState, originalValues, type);
                    })
                    .finally(() => {
                        editState.classList.add('hidden');
                        viewState.classList.remove('hidden');
                    });

            } else {
                const currentValue = inputEl ? inputEl.value : '';

                if (currentValue === originalValues[fieldName]) {
                    editState.classList.add('hidden');
                    viewState.classList.remove('hidden');
                    return;
                }

                const payload = {
                    column: fieldName,
                    value: currentValue
                };
                inlineUpdate(updateUrl, payload, token, cell, viewState, editState, originalValues,
                    type, inputEl);
            }
        };

        const handleCancel = () => {
            if (isSaved) return;
            isSaved = true;
            rollback(editState, originalValues, type);
            editState.classList.add('hidden');
            viewState.classList.remove('hidden');
        };

        if (type === 'double_input') {
            const inputs = editState.querySelectorAll('input, select');
            inputs.forEach(inp => {
                inp.addEventListener('blur', () => {
                    setTimeout(() => {
                        // Cek apakah fokus saat ini masih berada di dalam area editState (mencakup input maupun dropdown-nya)
                        if (!editState.contains(document.activeElement)) {
                            handleSave();
                        }
                    }, 150);
                });
                inp.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') handleSave();
                    else if (e.key === 'Escape') handleCancel();
                });
            });
        } else if (inputEl) {
            inputEl.addEventListener('blur', handleSave);
            inputEl.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && type !== 'textarea') handleSave();
                else if (e.key === 'Escape') handleCancel();
            });
        }
    }

    function inlineUpdate(url, payload, token, cell, viewState, editState, originalValues, type, inputEl) {
        fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => {
                    throw err;
                });
                return response.json();
            })
            .then(res => {
                if (res.success) {
                    const updatedValue = res.value !== undefined ? res.value : payload.value;

                    if (type === 'select') {
                        if (payload.column === 'status') {
                            const isDone = updatedValue.toLowerCase() === 'done';
                            viewState.innerHTML = isDone ?
                                `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-extrabold rounded-full bg-teal-50 text-teal-700 border border-teal-200"><span class="w-1 h-1 rounded-full bg-teal-500"></span> Done</span>` :
                                `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-extrabold rounded-full bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1 h-1 rounded-full bg-rose-400"></span> Undone</span>`;
                        } else if (payload.column === 'pilar') {
                            const colors = {
                                Sosial: 'bg-blue-100 text-blue-800',
                                Ekonomi: 'bg-emerald-100 text-emerald-800',
                                Lingkungan: 'bg-amber-100 text-amber-800'
                            };
                            const colorClass = colors[updatedValue] || 'bg-gray-100 text-black';
                            viewState.innerHTML =
                                `<span class="px-1.5 py-0.5 text-[9px] font-bold rounded ${colorClass}">${updatedValue}</span>`;
                        } else {
                            viewState.textContent = updatedValue || '-';
                        }
                    } else if (type === 'number') {
                        const formatted = new Intl.NumberFormat('id-ID', {
                            style: 'decimal'
                        }).format(updatedValue);
                        viewState.textContent = 'Rp ' + formatted;
                    } else {
                        viewState.textContent = updatedValue || '-';
                    }
                    refreshKeepScroll();
                } else {
                    alert('Gagal update: ' + (res.message || 'Error tidak diketahui.'));
                    rollback(editState, originalValues, type);
                }
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Gagal koneksi ke server.');
                rollback(editState, originalValues, type);
            })
            .finally(() => {
                editState.classList.add('hidden');
                viewState.classList.remove('hidden');
            });
    }

    function rollback(editState, originalValues, type) {
        if (type === 'double_input') {
            const inputs = editState.querySelectorAll('input, select');
            inputs.forEach(inp => {
                if (originalValues[inp.name] !== undefined) {
                    inp.value = originalValues[inp.name];
                }
            });
        } else {
            const inputEl = ['SELECT', 'INPUT', 'TEXTAREA'].includes(editState.tagName) ?
                editState : editState.querySelector('input, select, textarea');

            if (inputEl) {
                inputEl.value = Object.values(originalValues)[0] || '';
            }
        }
    }

    function refreshKeepScroll() {
        // Simpan posisi scroll saat ini ke sessionStorage
        sessionStorage.setItem('scrollPosition', window.scrollY);
        // Reload halaman
        window.location.reload();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const savedScrollPosition = sessionStorage.getItem('scrollPosition');
        if (savedScrollPosition !== null) {
            window.scrollTo(0, parseInt(savedScrollPosition));
            sessionStorage.removeItem('scrollPosition');
        }
    });

    // ======================================================
    // LINK MODAL FUNCTIONS (Support Multi-Link & Single-Link)
    // ======================================================
    function openLinkEditModal(cell, column, updateUrl) {
        const hiddenField = cell.querySelector(`textarea[name="${column}"]`);
        const rawValue = hiddenField ? hiddenField.value.trim() : '';

        linkModalState = {
            cell,
            column,
            updateUrl
        };

        const modal = document.getElementById('modalLinkEdit');
        const container = document.getElementById('modalLinkContainer');
        const titleLabel = document.getElementById('modalLinkLabel');
        const iconEl = document.getElementById('modalLinkIcon');

        if (!modal || !container) return;

        container.innerHTML = '';

        // Cek apakah kolom GDrive (Single Link) atau bukan (Multi Link)
        if (column === 'link_gdrive') {
            if (titleLabel) titleLabel.textContent = 'Edit Link GDrive';
            if (iconEl) iconEl.className = 'fab.fa-google-drive text-amber-500 fas';

            // Buat 1 input tunggal saja untuk GDrive
            const row = document.createElement('div');
            row.className = 'flex items-center gap-1.5 modal-link-row w-full';
            row.innerHTML = `
            <input type="text" value="${rawValue.replace(/"/g, '&quot;')}" placeholder="https://drive.google.com/..."
                class="w-full text-xs border border-gray-300 rounded-lg p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none modal-link-input">
        `;
            container.appendChild(row);
        } else {
            // Logika lama untuk Instagram & Berita (Multi-link)
            const links = rawValue.split(/\r\n|\r|\n/).map(l => l.trim()).filter(l => l.length > 0);

            if (column === 'link_ig') {
                if (titleLabel) titleLabel.textContent = 'Edit Link Instagram';
                if (iconEl) iconEl.className = 'fab fa-instagram text-pink-500';
            } else {
                if (titleLabel) titleLabel.textContent = 'Edit Link Berita';
                if (iconEl) iconEl.className = 'fas fa-newspaper text-blue-500';
            }

            const placeholder = column === 'link_ig' ? 'https://instagram.com/...' : 'https://berita.com/...';
            const seedLinks = links.length > 0 ? links : [''];
            seedLinks.forEach((val, idx) => addLinkRow(container, val, idx + 1, placeholder));
            ensureTrailingEmptyRow(container, placeholder);
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('div')?.classList.remove('scale-95', 'opacity-0');
            modal.querySelector('div')?.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function addLinkRow(container, value, number, placeholder) {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-1.5 modal-link-row';
        row.innerHTML = `
        <span class="text-[10px] font-bold text-gray-400 w-4 text-center row-num">${number}.</span>
        <input type="text" value="${value.replace(/"/g, '&quot;')}" placeholder="${placeholder}"
            class="w-full text-xs border border-gray-300 rounded-lg p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none modal-link-input">
    `;
        container.appendChild(row);
        attachLinkRowEvents(row.querySelector('.modal-link-input'), container, placeholder);
    }

    function attachLinkRowEvents(input, container, placeholder) {
        if (!input) return;
        input.addEventListener('input', () => {
            const allInputs = container.querySelectorAll('.modal-link-input');
            const lastInput = allInputs[allInputs.length - 1];
            if (input === lastInput && input.value.trim() !== '') {
                addLinkRow(container, '', allInputs.length + 1, placeholder);
            }
        });

        input.addEventListener('focusout', () => {
            setTimeout(() => {
                const allInputs = container.querySelectorAll('.modal-link-input');
                if (allInputs.length <= 1) return;
                const lastInput = allInputs[allInputs.length - 1];
                if (input.value.trim() === '' && input !== lastInput && document.activeElement !==
                    input) {
                    input.closest('.modal-link-row')?.remove();
                    reorderLinkRows(container);
                }
            }, 100);
        });
    }

    function ensureTrailingEmptyRow(container, placeholder) {
        const allInputs = container.querySelectorAll('.modal-link-input');
        const lastInput = allInputs[allInputs.length - 1];
        if (!lastInput || lastInput.value.trim() !== '') {
            addLinkRow(container, '', allInputs.length + 1, placeholder);
        }
    }

    function reorderLinkRows(container) {
        container.querySelectorAll('.row-num').forEach((span, i) => span.textContent = `${i + 1}.`);
    }

    function closeLinkModal() {
        const modal = document.getElementById('modalLinkEdit');
        if (modal) {
            modal.querySelector('div')?.classList.add('scale-95', 'opacity-0');
            modal.querySelector('div')?.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => modal.classList.add('hidden'), 150);
        }
        linkModalState = {
            cell: null,
            column: null,
            updateUrl: null
        };
    }

    function saveLinkModal() {
        const {
            cell,
            column,
            updateUrl
        } = linkModalState;
        if (!cell || !column || !updateUrl) return;

        const container = document.getElementById('modalLinkContainer');
        if (!container) return;

        const inputs = container.querySelectorAll('.modal-link-input');

        // Jika GDrive, ambil nilai single input langsung tanpa \n
        let joined = '';
        if (column === 'link_gdrive') {
            joined = inputs.length > 0 ? inputs[0].value.trim() : '';
        } else {
            joined = Array.from(inputs).map(i => i.value.trim()).filter(v => v.length > 0).join('\n');
        }

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    column: column,
                    value: joined
                })
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => {
                    throw err;
                });
                return res.json();
            })
            .then(result => {
                if (result.success) {
                    const updatedValue = result.value !== undefined ? result.value : joined;
                    updateLinkCellView(cell, column, updatedValue);
                    closeLinkModal();
                } else {
                    alert('Gagal update: ' + (result.message || 'Error tidak diketahui.'));
                }
            })
            .catch(err => {
                console.error(err);
                alert(err.message || 'Gagal koneksi ke server.');
            });
    }

    function updateLinkCellView(cell, column, rawValue) {
        const hiddenField = cell.querySelector(`textarea[name="${column}"]`);
        if (hiddenField) hiddenField.value = rawValue;

        const viewState = cell.querySelector('.view-state');
        if (!viewState) return;

        // Khusus Single Link GDrive
        if (column === 'link_gdrive') {
            if (!rawValue) {
                viewState.innerHTML = `<span class="text-black">-</span>`;
                return;
            }
            viewState.innerHTML = `
            <a href="${rawValue}" target="_blank" title="${rawValue}"
                class="text-amber-600 hover:text-amber-800 transition inline-flex items-center justify-center" onclick="event.stopPropagation();">
                <i class="fab.fa-google-drive text-base fas"></i>
            </a>
        `;
            return;
        }
        // Logika untuk Multi-link (IG / Berita)
        const links = rawValue.split(/\r\n|\r|\n/).map(l => l.trim()).filter(l => l.length > 0);
        const isIg = column === 'link_ig';

        if (links.length === 0) {
            viewState.innerHTML = `<span class="text-black">-</span>`;
            return;
        }

        const iconClass = isIg ? 'fab fa-instagram text-sm' : 'fas fa-newspaper text-sm';
        const colorClass = isIg ? 'text-pink-600 hover:text-pink-800' : 'text-blue-600 hover:text-blue-800';
        const badgeColor = isIg ? 'bg-pink-100' : 'bg-blue-100';
        const linkColor = isIg ? 'text-pink-600' : 'text-blue-600';
        const countBadge = links.length > 1 ?
            `<span class="text-[8px] font-black ${badgeColor} rounded-full px-1">${links.length}</span>` : '';
        const dropdownLinks = links.map((link, i) => `
        <a href="${link}" target="_blank" title="${link}" class="block text-[10px] font-semibold ${linkColor} hover:underline truncate py-0.5">${i + 1}. ${link}</a>
    `).join('');

        viewState.innerHTML = `
        <button type="button" onclick="toggleLinkDropdown(this)" class="${colorClass} transition inline-flex items-center gap-0.5">
            <i class="${iconClass}"></i>
            ${countBadge}
        </button>
        <div class="link-dropdown hidden absolute z-30 top-full left-1/2 -translate-x-1/2 mt-1 bg-white border border-black rounded-lg shadow-lg p-1.5 w-52 text-left max-h-48 overflow-y-auto">
            ${dropdownLinks}
        </div>
    `;
    }

    // ======================================================
    // MODAL IMPORT FUNCTIONS & PREVIEW LOGIC
    // ======================================================
    function openImportModal() {
        const modal = document.getElementById('modalImportPaste');
        if (modal) modal.classList.remove('hidden');
    }

    function closeImportModal() {
        const form = document.getElementById('formImportPaste');
        if (form) form.reset();

        document.getElementById('previewContainer')?.classList.add('hidden');
        document.getElementById('previewTableBody').innerHTML = '';

        document.getElementById('btnCheckPreview')?.classList.remove('hidden');
        document.getElementById('btnSubmitImport')?.classList.add('hidden');

        const modal = document.getElementById('modalImportPaste');
        if (modal) modal.classList.add('hidden');
    }

    function formatRupiahPreview(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    function handlePreview() {
        const form = document.getElementById('formImportPaste');
        const btnPreview = document.getElementById('btnCheckPreview');
        const textarea = document.getElementById('importExcelText');

        if (!textarea.value.trim()) {
            alert("Silakan tempel (paste) data excel terlebih dahulu!");
            return;
        }

        document.getElementById('isPreviewInput').value = '1';

        btnPreview.disabled = true;
        btnPreview.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Membaca Data...`;

        const formData = new FormData(form);

        fetch("{{ route('csr.importPaste') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            })
            // Menangkap status HTTP dan body JSON agar pesan 422 (Unprocessable Entity) terbaca dengan baik
            .then(response => response.json().then(data => ({
                status: response.status,
                body: data
            })))
            .then(res => {
                // Periksa apakah status 200 dan bernilai sukses
                if (res.status === 200 && res.body.success) {
                    document.getElementById('previewContainer').classList.remove('hidden');
                    const tbody = document.getElementById('previewTableBody');
                    tbody.innerHTML = '';

                    const masterPilar = res.body.pilar_list || [];
                    const listBulanOpts = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                        'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    res.body.data.forEach((item, index) => {
                        let pilarOptions = '';
                        if (masterPilar.length > 0) {
                            pilarOptions = masterPilar.map(p => {
                                const dbPilarName = String(p.nama_pilar).trim().toLowerCase();
                                const itemPilarName = String(item.nama_pilar_preview || '').trim()
                                    .toLowerCase();
                                const isSelected = (itemPilarName === dbPilarName) ? 'selected' :
                                    '';

                                return `<option value="${p.nama_pilar}" ${isSelected}>${p.nama_pilar}</option>`;
                            }).join('');
                        } else {
                            pilarOptions =
                                `<option value="${item.nama_pilar_preview}" selected>${item.nama_pilar_preview}</option>`;
                        }

                        let bulanOptions = listBulanOpts.map(b =>
                            `<option value="${b}" ${item.bulan_realisasi === b ? 'selected' : ''}>${b}</option>`
                        ).join('');

                        // URUTAN KOLOM DISELESAIKAN DENGAN HEADER THEAD HTML
                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50 transition">
                                <!-- 1. Program -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][nama_program]" value="${item.nama_program}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 2. Bentuk Bantuan -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][bentuk_bantuan]" value="${item.bentuk_bantuan}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 3. Pilar -->
                                <td class="px-2 py-1.5 border-b">
                                    <select name="rows[${index}][nama_pilar_preview]" class="w-full text-[10px] font-semibold text-emerald-600 bg-slate-50 border border-slate-200 rounded px-1 py-1 focus:bg-white">
                                        ${pilarOptions}
                                    </select>
                                </td>
                                <!-- 4. TPB -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][tpb]" value="${item.tpb}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 5. Asta Cita -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][asta_cita]" value="${item.asta_cita}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 6. Cabang -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][cabang]" value="${item.cabang}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 7. Biaya Program -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="number" name="rows[${index}][biaya_program]" value="${item.biaya_program}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 8. Biaya Realisasi -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="number" name="rows[${index}][biaya_realisasi]" value="${item.biaya_realisasi}" class="w-full text-[10px] font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 9. Realisasi Program -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][realisasi_program]" value="${item.realisasi_program}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 10. Bulan -->
                                <td class="px-2 py-1.5 border-b">
                                    <select name="rows[${index}][bulan_realisasi]" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1 py-1 focus:bg-white">
                                        ${bulanOptions}
                                    </select>
                                </td>
                                <!-- 11. Status -->
                                <td class="px-2 py-1.5 border-b">
                                    <select name="rows[${index}][status]" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1 py-1 focus:bg-white">
                                        <option value="done" ${item.status === 'done' ? 'selected' : ''}>Done</option>
                                        <option value="undone" ${item.status === 'undone' ? 'selected' : ''}>Undone</option>
                                    </select>
                                </td>
                                <!-- 12. Kabupaten -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][kabupaten]" value="${item.kabupaten}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                                <!-- 13. Desa -->
                                <td class="px-2 py-1.5 border-b">
                                    <input type="text" name="rows[${index}][desa]" value="${item.desa}" class="w-full text-[10px] bg-slate-50 border border-slate-200 rounded px-1.5 py-1 focus:bg-white">
                                </td>
                            </tr>
                        `;
                    });

                    btnPreview.classList.add('hidden');
                    document.getElementById('btnSubmitImport').classList.remove('hidden');
                } else {
                    // Menampilkan pesan spesifik dari controller (misal: master pilar tahun tertentu kosong)
                    alert("Gagal Preview: " + (res.body.message || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error preview:", err);
                alert("Terjadi kesalahan sistem saat memproses preview.");
            })
            .finally(() => {
                btnPreview.disabled = false;
                btnPreview.innerHTML = `<i class="fas fa-search"></i> Cek Preview`;
            });
    }

    function submitImportPaste(event) {
        event.preventDefault();

        const btnSubmit = document.getElementById('btnSubmitImport');
        const form = document.getElementById('formImportPaste');

        document.getElementById('isPreviewInput').value = '0';

        const formData = new FormData(form);

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;

        fetch("{{ route('csr.importPaste') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(response => response.json().then(data => ({
                status: response.status,
                body: data
            })))
            .then(res => {
                if (res.status === 200 && res.body.success) {
                    alert(res.body.message || "Data berhasil disimpan permanen!");
                    closeImportModal();
                    window.location.reload();
                } else {
                    alert("Gagal Simpan: " + (res.body.message || "Terjadi kesalahan."));
                }
            })
            .catch(err => {
                console.error("Error save:", err);
                alert("Terjadi kesalahan jaringan atau server.");
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="fas fa-save"></i> Proses & Simpan`;
            });
    }
</script>
