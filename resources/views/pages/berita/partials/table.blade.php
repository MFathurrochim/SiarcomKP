<div class="bg-white rounded-2xl shadow-sm border border-black overflow-hidden w-full">
    <!-- Header Tabel -->
    <div class="p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-white">
        <div class="flex items-center gap-2">
            <h2 class="text-base font-bold text-black whitespace-nowrap">Daftar Berita</h2>
        </div>


        <!-- Form Filter Cari & Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Input Pencarian Utama -->
            <form action="{{ url()->current() }}" method="GET" class="relative" id="searchFormBerita"
                onsubmit="return false;">
                <input type="hidden" name="tahun" value="{{ request('tahun', $tahunTerpilih) }}">
                <input type="hidden" name="sort_by" value="{{ request('sort_by', $sortBy) }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', $sortOrder) }}">

                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" id="inputSearchBerita" value="{{ request('search') }}"
                    placeholder="Cari judul, media, reporter..." autocomplete="off"
                    class="w-44 sm:w-52 text-xs bg-slate-50 border border-black rounded-full pl-8 pr-4 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </form>

            <!-- Modal Import Paste Spreadsheet -->
            <button @click="pasteModalOpen = true" type="button"
                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-emerald-600 rounded-xl px-3 py-1.5 hover:bg-emerald-700 transition shadow-md shadow-emerald-100 cursor-pointer">
                <i class="fa-solid fa-file-excel"></i> Paste Spreadsheet
            </button>

            <!-- Tombol Unduh Laporan Excel -->
            <a href="{{ route('berita.export', request()->query()) }}"
                class="inline-flex items-center gap-1 text-xs font-bold text-black bg-white border border-black rounded-xl px-2.5 py-1.5 hover:bg-slate-50 transition shadow-xxs cursor-pointer">
                <i class="fa-solid fa-download text-black"></i> Unduh
            </a>

            <!-- Tombol Reset Filter / Search -->
            <a href="{{ route('pages.berita.index', ['tahun' => $tahunTerpilih]) }}"
                class="w-7 h-7 inline-flex items-center justify-center text-black bg-slate-50 border border-black rounded-xl hover:bg-slate-100 transition cursor-pointer"
                title="Reset Pencarian">
                <i class="fa-solid fa-rotate-left text-xs"></i>
            </a>

            <!-- Tombol Tambah Berita Header -->
            <button @click="tambahModalOpen = true" type="button"
                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-teal-500 rounded-xl px-3 py-1.5 hover:bg-teal-600 transition shadow-md shadow-teal-100 cursor-pointer">
                <i class="fa-solid fa-circle-plus"></i> Tambah
            </button>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="w-full overflow-x-hidden">
        {{-- Bawa SEMUA filter aktif biar AJAX search gak "reset" filter lain (bulan/tone/topik/sifat/status) --}}
        <form id="filterTableForm" action="{{ url()->current() }}" method="GET" onsubmit="return false;">
            <input type="hidden" name="tahun" value="{{ request('tahun', $tahunTerpilih) }}">
            <input type="hidden" name="bulan" value="{{ request('bulan', $bulanBerita) }}">
            <input type="hidden" name="tone" value="{{ request('tone', $toneTerpilih) }}">
            <input type="hidden" name="topik" value="{{ request('topik', $topikTerpilih) }}">
            <input type="hidden" name="sifat_berita" value="{{ request('sifat_berita', $sifatTerpilih) }}">
            <input type="hidden" name="status_publikasi" value="{{ request('status_publikasi', $statusTerpilih) }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <table
                class="w-full text-left border-collapse border border-black divide-y divide-x divide-black table-fixed">
                <thead>
                    <tr
                        class="bg-teal-500 text-white text-[9px] font-bold uppercase tracking-wider divide-x divide-black">
                        <th class="py-2 px-1 text-center w-[4%] border border-black align-middle">No</th>
                        <th class="py-2 px-1 text-center w-[6%] border border-black align-middle">Aksi</th>
                        <th class="py-2 px-1.5 w-[9%] text-center border border-black cursor-pointer select-none align-middle"
                            onclick="sortBy('tanggal')">
                            <div class="flex items-center justify-between gap-0.5">
                                <span>Tgl</span>
                                <i
                                    class="fa-solid {{ $sortBy === 'tanggal' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[9px] opacity-80"></i>
                            </div>
                        </th>
                        <th class="py-2 px-2 w-[23%] text-center border border-black align-middle">Judul Berita</th>
                        <th class="py-2 px-1.5 w-[9%] text-center border border-black align-middle">Media</th>
                        <th class="py-2 px-1.5 w-[8%] text-center border border-black align-middle">Reporter</th>
                        <th class="py-2 px-1.5 w-[9%] text-center border border-black align-middle">Spokesperson</th>
                        <th class="py-2 px-1.5 w-[8%] text-center border border-black align-middle">Role</th>
                        <th class="py-2 px-1 text-center w-[7%] border border-black align-middle">Tone</th>
                        <th class="py-2 px-1 text-center w-[7%] border border-black align-middle">Topik</th>
                        <th class="py-2 px-1 text-center w-[7%] border border-black align-middle">Sifat</th>
                        <th class="py-2 px-1.5 text-center w-[7%] border border-black align-middle">Link</th>
                    </tr>
                </thead>

                <tbody id="tableBodyBerita"
                    class="divide-x divide-y divide-black text-black text-[10px] font-medium align-middle">
                    @forelse ($daftarBerita as $index => $berita)
                        <tr class="hover:bg-slate-50/60 transition-colors divide-x divide-black">
                            <!-- No -->
                            <td class="py-2 px-1 text-center text-black font-normal border border-black">
                                {{ $index + 1 }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-2 px-1 text-center border border-black" onclick="event.stopPropagation();">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" @click="editModalOpen = true"
                                        onclick="loadEditModal('{{ $berita->id_berita }}')"
                                        class="p-1 bg-gray-100 hover:bg-amber-100 text-black hover:text-amber-600 rounded transition cursor-pointer"
                                        title="Edit Berita">
                                        <i class="fas fa-edit text-[9px]"></i>
                                    </button>
                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('pages.berita.destroy', $berita->id_berita) }}', '{{ addslashes($berita->judul) }}')"
                                        class="p-1 bg-gray-100 hover:bg-red-100 text-black hover:text-red-600 rounded transition cursor-pointer"
                                        title="Hapus Berita">
                                        <i class="fas fa-trash-alt text-[9px]"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-2 px-1.5 text-center text-black border border-black cursor-pointer select-none"
                                data-id="{{ $berita->id_berita }}" data-field="tanggal" data-type="date"
                                data-value="{{ \Carbon\Carbon::parse($berita->tanggal)->format('Y-m-d') }}"
                                ondblclick="enableInlineEdit(this)">
                                {{ \Carbon\Carbon::parse($berita->tanggal)->translatedFormat('d/m/y') }}
                            </td>

                            <!-- Judul Berita -->
                            <td class="py-2 px-2 font-semibold text-black border border-black cursor-pointer select-none break-words"
                                title="{{ $berita->judul }}" data-id="{{ $berita->id_berita }}" data-field="judul"
                                data-type="text" data-value="{{ $berita->judul }}"
                                ondblclick="enableInlineEdit(this)">
                                {{ $berita->judul }}
                            </td>

                            <!-- Media -->
                            <td class="py-2 px-1.5 text-black border border-black cursor-pointer select-none break-words"
                                title="{{ $berita->nama_media }}" data-id="{{ $berita->id_berita }}"
                                data-field="nama_media" data-type="text" data-value="{{ $berita->nama_media }}"
                                ondblclick="enableInlineEdit(this)">
                                {{ $berita->nama_media ?? '-' }}
                            </td>

                            <!-- Reporter -->
                            <td class="py-2 px-1.5 text-black border border-black cursor-pointer select-none break-words"
                                title="{{ $berita->reporter }}" data-id="{{ $berita->id_berita }}"
                                data-field="reporter" data-type="text" data-value="{{ $berita->reporter }}"
                                ondblclick="enableInlineEdit(this)">
                                {{ $berita->reporter ?? '-' }}
                            </td>

                            <!-- Spokesperson -->
                            <td class="py-2 px-1.5 text-black border border-black cursor-pointer select-none break-words"
                                title="{{ $berita->spokeperson }}" data-id="{{ $berita->id_berita }}"
                                data-field="spokeperson" data-type="text" data-value="{{ $berita->spokeperson }}"
                                ondblclick="enableInlineEdit(this)">
                                {{ $berita->spokeperson ?? '-' }}
                            </td>

                            <!-- Role Spokesperson -->
                            <td class="py-2 px-1.5 text-black border border-black cursor-pointer select-none break-words"
                                title="{{ $berita->spokeperson_role }}" data-id="{{ $berita->id_berita }}"
                                data-field="spokeperson_role" data-type="text"
                                data-value="{{ $berita->spokeperson_role }}" ondblclick="enableInlineEdit(this)">
                                {{ $berita->spokeperson_role ?? '-' }}
                            </td>

                            <!-- Tone -->
                            <td class="py-2 px-1 text-center border border-black cursor-pointer select-none"
                                data-id="{{ $berita->id_berita }}" data-field="tone" data-type="select"
                                data-options='["Positif","Netral","Negatif"]' data-value="{{ $berita->tone }}"
                                ondblclick="enableInlineEdit(this)">
                                @if ($berita->tone === 'Positif')
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-emerald-600 uppercase inline-block">Positif</span>
                                @elseif($berita->tone === 'Negatif')
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-rose-600 uppercase inline-block">Negatif</span>
                                @else
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-slate-600 uppercase inline-block">Netral</span>
                                @endif
                            </td>

                            <!-- Topik -->
                            <td class="py-2 px-1 text-center border border-black cursor-pointer select-none"
                                data-id="{{ $berita->id_berita }}" data-field="topik" data-type="select"
                                data-options='["Operasi","CSR","Inovasi","Apresiasi"]'
                                data-value="{{ $berita->topik }}" ondblclick="enableInlineEdit(this)">
                                @switch(strtolower($berita->topik))
                                    @case('operasi')
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-teal-600 uppercase inline-block">{{ $berita->topik }}</span>
                                    @break

                                    @case('csr')
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-blue-600 uppercase inline-block">{{ $berita->topik }}</span>
                                    @break

                                    @case('inovasi')
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-amber-600 uppercase inline-block">{{ $berita->topik }}</span>
                                    @break

                                    @case('apresiasi')
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-purple-600 uppercase inline-block">{{ $berita->topik }}</span>
                                    @break

                                    @default
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-slate-500 uppercase inline-block">{{ $berita->topik ?? '-' }}</span>
                                @endswitch
                            </td>

                            <!-- Sifat -->
                            <td class="py-2 px-1 text-center border border-black cursor-pointer select-none"
                                data-id="{{ $berita->id_berita }}" data-field="sifat_berita" data-type="select"
                                data-options='["Internal","Eksternal"]' data-value="{{ $berita->sifat_berita }}"
                                ondblclick="enableInlineEdit(this)">
                                @if (strtolower($berita->sifat_berita) === 'eksternal')
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] font-bold text-stone-900 bg-[#bbf7d0] uppercase inline-block">Eksternal</span>
                                @else
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white bg-[#2b7a78] uppercase inline-block">Internal</span>
                                @endif
                            </td>

                            <!-- Link Berita -->

                            @php
                                $linkBeritaList = $berita->link_berita
                                    ? array_values(array_filter(array_map('trim', explode("\n", $berita->link_berita))))
                                    : [];
                                $singleLink = count($linkBeritaList) > 0 ? $linkBeritaList[0] : '';
                            @endphp
                            <td class="py-2 px-1.5 border border-black text-center relative select-none"
                                data-id="{{ $berita->id_berita }}" data-field="link_berita"
                                ondblclick="openLinkEditModalBerita(this, 'link_berita', '{{ route('berita.inline-update', $berita->id_berita) }}')">

                                <textarea name="link_berita" class="hidden">{{ $berita->link_berita }}</textarea>

                                <div class="inline-block relative" onclick="event.stopPropagation();">
                                    @if (!empty($singleLink))
                                        <!-- Langsung buka link di tab baru saat diklik -->
                                        <a href="{{ $singleLink }}" target="_blank" title="{{ $singleLink }}"
                                            class="text-teal-600 hover:text-teal-700 transition inline-flex items-center gap-0.5 font-semibold text-[9px] cursor-pointer">
                                            <i class="fa-solid fa-link"></i> Link
                                        </a>
                                    @else
                                        <span class="text-black font-normal">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-6 text-center text-black font-normal border border-black">
                                    <i class="fa-solid fa-newspaper text-base mb-1 block"></i>
                                    Tidak ada data berita ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            <!-- TOMBOL TAMBAH -->
            <div class="p-3 border-t border-black bg-white">
                <button @click="tambahModalOpen = true" type="button"
                    class="w-full flex items-center justify-center gap-2 py-2.5 border-2 border-dashed border-teal-400 text-teal-600 hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 font-bold text-[11px] rounded-xl transition-all group cursor-pointer">
                    <i class="fa-solid fa-circle-plus text-sm group-hover:scale-110 transition-transform"></i>
                    Tambah Berita Baru
                </button>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div id="modalDeleteConfirmation"
            class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 transition-all duration-300 bg-slate-900/10 bg-black/50">
            <div
                class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform scale-100 transition-all text-center">
                <div
                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4 border border-red-100">
                    <i class="fas fa-exclamation-triangle text-red-500 text-lg animate-bounce"></i>
                </div>
                <h3 class="text-base font-extrabold text-black mb-1">Konfirmasi Hapus</h3>
                <p class="text-xs text-black leading-relaxed mb-4">
                    Apakah Anda yakin ingin menghapus data berita <br>
                    <span id="deleteTargetName" class="font-bold text-black text-sm"></span>? Tindakan ini tidak bisa
                    dibatalkan.
                </p>
                <form id="formDeleteAction" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl transition">
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
        <!-- Modal Edit Link Berita -->
        <div id="modalLinkEditBerita"
            class="hidden fixed inset-0 z-[95] flex items-center justify-center p-4 bg-slate-900/30 backdrop-blur-xs transition-all duration-300">
            <div
                class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-black transform scale-95 opacity-0 transition-all">
                <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                    <h3 class="text-sm font-extrabold text-black flex items-center gap-2">
                        <i class="fas fa-newspaper text-teal-500"></i> Edit Link Berita
                    </h3>
                    <button type="button" onclick="closeLinkModalBerita()" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div id="modalLinkContainerBerita" class="space-y-1.5 max-h-64 overflow-y-auto pr-1"></div>

                <div class="flex justify-end gap-2 pt-3 mt-3 border-t border-gray-100">
                    <button type="button" onclick="closeLinkModalBerita()"
                        class="px-4 py-1.5 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                        Batal
                    </button>
                    <button type="button" onclick="saveLinkModalBerita()"
                        class="px-4 py-1.5 text-xs font-bold bg-gradient-to-r from-teal-500 to-emerald-500 hover:opacity-90 text-white rounded-lg transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        <!-- MODAL PASTE SPREADSHEET -->
        <div x-show="pasteModalOpen" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
            x-transition.opacity>

            <div @click.away="pasteModalOpen = false"
                class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-5xl overflow-hidden flex flex-col max-h-[90vh]">

                <!-- Header Modal -->
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-emerald-600">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-excel text-white"></i> Import Data Berita via Paste Spreadsheet
                        (Excel)
                    </h3>
                    <button @click="pasteModalOpen = false" class="text-emerald-100 hover:text-white cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    <div
                        class="text-[11px] text-slate-800 bg-emerald-50 border border-emerald-200 p-3 rounded-xl space-y-1">
                        <p class="font-bold text-emerald-900">Panduan:</p>
                        <p>1. Blok dan "copy" baris data dari Spreadsheet Anda (tanpa baris header kolom).</p>
                        <p>2. Urutan kolom spreadsheet yang didukung (kolom "NO" di depan boleh ada/tidak): <br>
                            <code class="font-bold text-emerald-700 bg-emerald-100 px-1 py-0.5 rounded">TANGGAL |
                                LINK | TONE | JUDUL | NAMA MEDIA | REPORTER | SPOKESPERSON | SPOKESPERSON ROLE | TOPIK
                                | INT/EXT</code>
                        </p>
                        <p>3. Lalu "Paste" ke dalam kotak teks di bawah ini.</p>
                    </div>

                    <form id="formPasteSpreadsheet" action="{{ route('berita.store-paste') }}" method="POST">
                        @csrf
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Tempel Data Spreadsheet di Sini:</label>
                            <textarea name="raw_paste_data" id="rawPasteData" rows="8"
                                placeholder="Klik di sini lalu tekan Ctrl + V dari spreadsheet..."
                                class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl p-3 focus:outline-none focus:border-emerald-600 focus:bg-white font-mono"
                                oninput="parseBeritaSpreadsheetData()"></textarea>
                        </div>

                        <!-- Preview Hasil Parsing -->
                        <div class="mt-4">
                            <span class="text-xs font-bold text-slate-800 block mb-1">Pratinjau Data yang Akan Masuk (<span
                                    id="previewCount">0</span> baris valid):</span>
                            <div
                                class="border border-slate-200 rounded-xl overflow-x-auto max-h-64 overflow-y-auto bg-slate-50">
                                <table class="w-max min-w-full text-left border-collapse text-[10px]" id="previewTable">
                                    <thead class="bg-emerald-600 text-white sticky top-0">
                                        <tr>
                                            <th class="p-2 border-b border-emerald-500 min-w-[80px]">Tanggal</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[160px]">Judul</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[100px]">Media</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[90px]">Reporter</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[110px]">Spokesperson</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[110px]">Role</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[70px]">Tone</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[70px]">Topik</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[70px]">Sifat</th>
                                            <th class="p-2 border-b border-emerald-500 min-w-[70px]">Link</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewBody">
                                        <tr>
                                            <td colspan="10" class="p-4 text-center text-slate-400 italic">Belum ada
                                                data yang
                                                dipaste.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden inputs untuk menampung data JSON hasil parsing agar terkirim ke Controller -->
                        <input type="hidden" name="parsed_rows" id="parsedRowsInput">
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2 bg-slate-50">
                    <button @click="pasteModalOpen = false" type="button"
                        class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitBeritaPasteData()"
                        class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-200 cursor-pointer flex items-center gap-1">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Simpan ke Sistem
                    </button>
                </div>
            </div>
        </div>
        <!-- JAVASCRIPT PASTE SPREADSHEET BERITA -->
        <script>
            function parseBeritaSpreadsheetData() {
                const rawText = document.getElementById('rawPasteData').value;
                const lines = rawText.split('\n').filter(line => line.trim() !== '');
                const previewBody = document.getElementById('previewBody');
                const previewCount = document.getElementById('previewCount');
                const parsedRowsInput = document.getElementById('parsedRowsInput');

                previewBody.innerHTML = '';
                let parsedData = [];

                if (lines.length === 0) {
                    previewBody.innerHTML =
                        `<tr><td colspan="10" class="p-4 text-center text-slate-400 italic">Belum ada data yang dipaste.</td></tr>`;
                    previewCount.innerText = '0';
                    parsedRowsInput.value = '';
                    return;
                }

                lines.forEach((line) => {
                    // Pisahkan kolom berdasarkan Tab (hasil copy langsung dari Excel)
                    const cols = line.split('\t').map(col => col.trim());

                    // Cari indeks kolom yang formatnya mirip tanggal (DD/MM/YYYY atau MM/DD/YYYY)
                    let tanggalIdx = cols.findIndex(c => /^\d{1,2}\/\d{1,2}\/\d{2,4}$/.test(c));

                    // Butuh akses sampai index (tanggalIdx + 9) -> panjang minimal harus (tanggalIdx + 10)
                    if (tanggalIdx !== -1 && cols.length >= (tanggalIdx + 10)) {
                        let rawTanggal = cols[tanggalIdx];
                        let tanggalFormatted = formatBeritaDateToSQL(rawTanggal);

                        let linkBerita = cols[tanggalIdx + 1] !== '' ? cols[tanggalIdx + 1] : null;
                        let tone = normalizeTone(cols[tanggalIdx + 2] || 'Positif');
                        let judul = cols[tanggalIdx + 3] || '';
                        let namaMedia = cols[tanggalIdx + 4] || 'Unknown';
                        let reporter = cols[tanggalIdx + 5] || '-';
                        let spokeperson = cols[tanggalIdx + 6] || '-';
                        let spokepersonRole = cols[tanggalIdx + 7] || '-';
                        let topik = normalizeTopik(cols[tanggalIdx + 8] || 'Operasi');
                        let sifatBerita = normalizeSifatBerita(cols[tanggalIdx + 9] || 'INT');

                        let rowObj = {
                            tanggal: tanggalFormatted,
                            link_berita: linkBerita,
                            tone: tone,
                            judul: judul,
                            nama_media: namaMedia,
                            reporter: reporter,
                            spokeperson: spokeperson,
                            spokeperson_role: spokepersonRole,
                            topik: topik,
                            sifat_berita: sifatBerita
                        };

                        parsedData.push(rowObj);

                        // Render baris ke tabel pratinjau modal — 10 kolom, SAMA PERSIS urutannya
                        // dengan <thead> di atas (Tanggal, Judul, Media, Reporter, Spokesperson,
                        // Role, Tone, Topik, Sifat, Link)
                        previewBody.innerHTML += `
                    <tr class="border-b border-slate-200 hover:bg-slate-100 text-slate-700">
                        <td class="p-1.5 border-r border-slate-200 whitespace-nowrap">${rowObj.tanggal || '<span class="text-red-500">Kosong</span>'}</td>
                        <td class="p-1.5 border-r border-slate-200 truncate max-w-[180px]" title="${rowObj.judul}">${rowObj.judul}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.nama_media}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.reporter}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.spokeperson}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.spokeperson_role}</td>
                        <td class="p-1.5 border-r border-slate-200 font-bold">${rowObj.tone}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.topik}</td>
                        <td class="p-1.5 border-r border-slate-200">${rowObj.sifat_berita}</td>
                        <td class="p-1.5">${rowObj.link_berita ? `<a href="${rowObj.link_berita}" target="_blank" class="text-teal-600 hover:underline">Buka</a>` : '-'}</td>
                    </tr>
                `;
                    }
                });

                previewCount.innerText = parsedData.length;
                parsedRowsInput.value = JSON.stringify(parsedData);
            }

            function formatBeritaDateToSQL(dateStr) {
                if (!dateStr) return null;
                let parts = dateStr.trim().split('/');
                if (parts.length === 3) {
                    let month = parts[0].padStart(2, '0');
                    let day = parts[1].padStart(2, '0');
                    let year = parts[2];

                    if (year.length === 2) year = '20' + year;

                    return `${year}-${month}-${day}`;
                }
                return dateStr;
            }

            function normalizeTone(tone) {
                let lower = tone.toLowerCase();
                if (lower.includes('negatif')) return 'Negatif';
                if (lower.includes('netral')) return 'Netral';
                return 'Positif';
            }

            function normalizeTopik(topik) {
                let lower = topik.toLowerCase();
                if (lower.includes('csr')) return 'CSR';
                if (lower.includes('inovasi')) return 'Inovasi';
                if (lower.includes('apresiasi')) return 'Apresiasi';
                return 'Operasi';
            }

            function normalizeSifatBerita(sifat) {
                let upper = sifat.toUpperCase();
                if (upper.includes('INT')) return 'Internal';
                return 'Eksternal';
            }

            function submitBeritaPasteData() {
                const inputVal = document.getElementById('parsedRowsInput').value;
                if (!inputVal || inputVal === '[]') {
                    alert('Data paste berita kosong atau format tidak valid. Harap periksa kembali.');
                    return;
                }
                document.getElementById('formPasteSpreadsheet').submit();
            }
        </script>

        <!-- JAVASCRIPT UTAMA -->
        <script>
            /**
             * 1. FITUR SORTING KOLOM TABEL 
             */
            function sortBy(column) {
                const currentSortBy = "{{ request('sort_by', 'tanggal') }}";
                const currentSortOrder = "{{ request('sort_order', 'desc') }}";

                let newOrder = 'asc';
                if (currentSortBy === column && currentSortOrder === 'asc') {
                    newOrder = 'desc';
                }

                const form = document.getElementById('searchFormBerita');

                form.querySelector('input[name="sort_by"]').value = column;
                form.querySelector('input[name="sort_order"]').value = newOrder;

                window.location.href = `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;
            }

            /**
             * 2. FITUR INLINE EDITING (DOUBLE CLICK CELL)
             */
            function enableInlineEdit(cell) {
                if (cell.querySelector('input') || cell.querySelector('select')) return;

                const id = cell.getAttribute('data-id');
                const field = cell.getAttribute('data-field');
                const type = cell.getAttribute('data-type');
                const currentValue = cell.getAttribute('data-value') || '';
                const originalHTML = cell.innerHTML;

                let inputElement;

                if (type === 'select') {
                    const options = JSON.parse(cell.getAttribute('data-options') || '[]');
                    inputElement = document.createElement('select');
                    inputElement.className =
                        "w-full text-xs bg-white border border-teal-500 rounded px-1.5 py-1 focus:outline-none focus:ring-2 focus:ring-teal-400 font-medium text-black shadow-sm";

                    options.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt;
                        option.textContent = opt;
                        if (opt === currentValue) option.selected = true;
                        inputElement.appendChild(option);
                    });
                } else {
                    inputElement = document.createElement('input');
                    inputElement.type = type === 'date' ? 'date' : 'text';
                    inputElement.value = currentValue;
                    inputElement.className =
                        "w-full text-xs bg-white border border-teal-500 rounded px-1.5 py-1 focus:outline-none focus:ring-2 focus:ring-teal-400 font-medium text-black shadow-sm";
                }

                cell.innerHTML = '';
                cell.appendChild(inputElement);
                inputElement.focus();

                const executeSave = (newValue) => {
                    if (newValue === currentValue) {
                        cell.innerHTML = originalHTML;
                        return;
                    }

                    cell.innerHTML =
                        `<span class="text-gray-400 text-[10px] italic"><i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...</span>`;

                    fetch(`/berita/${id}/inline-update`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                field: field,
                                value: newValue
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal memperbarui data');
                            return response.json();
                        })
                        .then(data => {
                            cell.setAttribute('data-value', newValue);
                            cell.innerHTML = renderDisplayValue(field, newValue);
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Gagal memperbarui data. Silakan coba lagi.');
                            cell.innerHTML = originalHTML;
                        });
                };

                if (type === 'select') {
                    // Khusus dropdown, begitu opsi dipilih langsung simpan tanpa tunggu blur
                    inputElement.addEventListener('change', (e) => {
                        executeSave(e.target.value);
                    });
                } else {
                    let submitted = false;
                    const saveInput = () => {
                        if (submitted) return;
                        submitted = true;
                        executeSave(inputElement.value);
                    };

                    inputElement.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            saveInput();
                        } else if (e.key === 'Escape') {
                            submitted = true;
                            cell.innerHTML = originalHTML;
                        }
                    });

                    inputElement.addEventListener('blur', () => {
                        setTimeout(saveInput, 150);
                    });
                }
            }

            /**
             * HELPER: RENDER DISPLAY VALUE UNTUK INLINE EDIT
             */
            function renderDisplayValue(field, value) {
                if (!value || value.trim() === '') return '-';

                if (field === 'tanggal') {
                    const dateObj = new Date(value);
                    if (!isNaN(dateObj.getTime())) {
                        return dateObj.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                    return value;
                }

                if (field === 'tone') {
                    let badgeClass = 'bg-slate-600';
                    if (value === 'Positif') badgeClass = 'bg-emerald-600';
                    if (value === 'Negatif') badgeClass = 'bg-rose-600';
                    return `<span class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white ${badgeClass} uppercase inline-block">${value}</span>`;
                }

                if (field === 'topik') {
                    let badgeClass = 'bg-slate-500';
                    let valLower = value.toLowerCase();
                    if (valLower === 'operasi') badgeClass = 'bg-teal-600';
                    if (valLower === 'csr') badgeClass = 'bg-blue-600';
                    if (valLower === 'inovasi') badgeClass = 'bg-amber-600';
                    if (valLower === 'apresiasi') badgeClass = 'bg-purple-600';
                    return `<span class="px-1.5 py-0.5 rounded text-[7px] font-bold text-white ${badgeClass} uppercase inline-block">${value}</span>`;
                }

                if (field === 'sifat_berita') {
                    let badgeStyle = (value.toLowerCase() === 'eksternal') ?
                        'bg-[#bbf7d0] text-stone-900' :
                        'bg-[#2b7a78] text-white';
                    return `<span class="px-1.5 py-0.5 rounded text-[7px] font-bold ${badgeStyle} uppercase inline-block">${value}</span>`;
                }

                if (field === 'link_berita') {
                    return `<a href="${value}" target="_blank" class="text-teal-600 hover:text-teal-700 hover:underline inline-flex items-center gap-1 font-semibold">Buka Berita <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i></a>`;
                }

                return value;
            }

            /**
             * Fungsi Edit
             */
            function loadEditModal(id) {
                const container = document.getElementById('editModalContent');
                container.innerHTML = `<div class="bg-white rounded-2xl border border-black shadow-2xl p-10 text-center text-slate-400 text-xs">
        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data berita...
    </div>`;

                fetch(`/branch-comm/media-monitoring/edit-modal/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memuat form edit');
                        return response.text();
                    })
                    .then(html => {
                        container.innerHTML = html;
                        Alpine.initTree(container);
                    })
                    .catch(error => {
                        console.error(error);
                        container.innerHTML = `<div class="bg-white rounded-2xl border border-black shadow-2xl p-10 text-center text-rose-500 text-xs">
                Gagal memuat data. Silakan coba lagi.
            </div>`;
                    });
            }

            function openFormModal(url) {
                document.getElementById('formTambahBerita')?.setAttribute('action', url);
            }

            /**
             * 3. FITUR MODAL KONFIRMASI HAPUS
             */
            function openDeleteModal(deleteUrl, title) {
                const modal = document.getElementById('modalDeleteConfirmation');
                const form = document.getElementById('formDeleteAction');
                const targetName = document.getElementById('deleteTargetName');

                form.setAttribute('action', deleteUrl);
                targetName.textContent = `"${title}"`;

                modal.classList.remove('hidden');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('modalDeleteConfirmation');
                modal.classList.add('hidden');
            }

            let linkModalStateBerita = {
                cell: null,
                updateUrl: null
            };

            function openLinkEditModalBerita(cell, column, updateUrl) {
                const hiddenField = cell.querySelector(`textarea[name="${column}"]`) || cell.querySelector(
                    `input[name="${column}"]`);
                const rawValue = hiddenField ? hiddenField.value.trim() : '';

                linkModalStateBerita = {
                    cell,
                    updateUrl
                };

                const modal = document.getElementById('modalLinkEditBerita');
                const container = document.getElementById('modalLinkContainerBerita');

                container.innerHTML = `
        <div class="w-full">
            <input type="url" id="modalSingleInputBerita" value="${rawValue.replace(/"/g, '&quot;')}" 
                placeholder="https://berita.com/..." 
                class="w-full text-xs border border-gray-300 rounded-lg p-2 focus:ring-1 focus:ring-teal-500 focus:outline-none">
        </div>
    `;

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
                    modal.querySelector('div').classList.add('scale-100', 'opacity-100');
                }, 50);
            }

            function closeLinkModalBerita() {
                const modal = document.getElementById('modalLinkEditBerita');
                modal.querySelector('div').classList.add('scale-95', 'opacity-0');
                modal.querySelector('div').classList.remove('scale-100', 'opacity-100');
                setTimeout(() => modal.classList.add('hidden'), 150);
                linkModalStateBerita = {
                    cell: null,
                    updateUrl: null
                };
            }

            function saveLinkModalBerita() {
                const {
                    cell,
                    updateUrl
                } = linkModalStateBerita;
                if (!cell || !updateUrl) return;

                const input = document.getElementById('modalSingleInputBerita');
                const linkValue = input ? input.value.trim() : '';

                fetch(updateUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            field: 'link_berita',
                            value: linkValue
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal memperbarui data');
                        return response.json();
                    })
                    .then(() => {
                        updateLinkCellViewBerita(cell, linkValue);
                        closeLinkModalBerita();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memperbarui data. Silakan coba lagi.');
                    });
            }

            function updateLinkCellViewBerita(cell, rawValue) {
                const hiddenField = cell.querySelector('textarea[name="link_berita"]') || cell.querySelector(
                    'input[name="link_berita"]');
                if (hiddenField) hiddenField.value = rawValue;

                // Bersihkan dan pecah baris link baru
                const links = rawValue ? rawValue.split('\n').map(l => l.trim()).filter(l => l.length > 0) : [];
                const viewDiv = cell.querySelector('div.inline-block');

                if (links.length === 0) {
                    viewDiv.innerHTML = `<span class="text-black font-normal">-</span>`;
                    return;
                }

                // Buat ulang HTML dropdown yang konsisten seperti format Blade awal
                let badgeHtml = '';
                if (links.length > 1) {
                    badgeHtml =
                        `<span class="text-[6px] font-black bg-teal-100 text-teal-800 rounded-full px-1">${links.length}</span>`;
                }

                let dropdownItems = '';
                links.forEach((link, index) => {
                    dropdownItems += `
            <a href="${link}" target="_blank" title="${link}" 
                class="block text-[9px] font-semibold text-teal-600 hover:underline break-all py-0.5">
                ${index + 1}. ${link}
            </a>
        `;
                });

                viewDiv.innerHTML = `
        <button type="button" onclick="toggleLinkDropdownBerita(this)"
            class="text-teal-600 hover:text-teal-700 transition inline-flex items-center gap-0.5 font-semibold text-[9px] cursor-pointer">
            <i class="fa-solid fa-link"></i> Link ${badgeHtml}
        </button>
        <div class="link-dropdown-berita hidden absolute z-30 top-full right-0 mt-1 bg-white border border-black rounded-lg shadow-lg p-1.5 w-48 text-left max-h-48 overflow-y-auto">
            ${dropdownItems}
        </div>
    `;
            }

            document.addEventListener('DOMContentLoaded', function() {
                const inputSearch = document.getElementById('inputSearchBerita');
                const filterForm = document.getElementById('filterTableForm');
                const tableBody = document.getElementById('tableBodyBerita');

                if (inputSearch && filterForm && tableBody) {
                    let typingTimer;
                    const doneTypingInterval = 400;

                    function triggerSearchBerita() {
                        filterForm.querySelector('input[name="search"]').value = inputSearch.value;

                        const formData = new FormData(filterForm);
                        const params = new URLSearchParams(formData);
                        const targetUrl = filterForm.action;
                        const fullUrl = `${targetUrl}?${params.toString()}`;

                        fetch(fullUrl, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(data.html, 'text/html');
                                const newTbody = doc.getElementById('tableBodyBerita');

                                if (newTbody) {
                                    tableBody.innerHTML = newTbody.innerHTML;
                                }

                                window.history.replaceState({}, '', fullUrl);
                            })
                            .catch(err => {
                                console.error('Gagal memuat hasil pencarian berita:', err);
                            });
                    }

                    inputSearch.addEventListener('input', function() {
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(triggerSearchBerita, doneTypingInterval);
                    });

                    inputSearch.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            clearTimeout(typingTimer);
                            triggerSearchBerita();
                        }
                    });
                }
            });
            // Refresh Posision tetap
            document.addEventListener("DOMContentLoaded", function() {
                // 1. Pulihkan posisi scroll jika ada data yang tersimpan sebelumnya
                const scrollPosition = sessionStorage.getItem('scrollPosition');
                if (scrollPosition) {
                    window.scrollTo(0, parseInt(scrollPosition));
                    sessionStorage.removeItem('scrollPosition'); // Bersihkan setelah digunakan
                }

                // 2. Tangkap semua form inline edit agar menyimpan posisi scroll sebelum halaman reload
                const editForms = document.querySelectorAll('.form-inline-edit'); // Sesuaikan class form edit Anda
                editForms.forEach(form => {
                    form.addEventListener('submit', function() {
                        // Simpan posisi scroll saat ini ke memory browser
                        sessionStorage.setItem('scrollPosition', window.scrollY);
                    });
                });
            });
        </script>
