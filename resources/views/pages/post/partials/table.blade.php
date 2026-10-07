<div class="bg-white rounded-2xl shadow-sm border border-black overflow-hidden w-full">
    <!-- Header Tabel: Judul, Cari, Unduh & Tambah -->
    <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
        <div>
            <h2 class="text-base font-bold text-black">Daftar Postingan Sosial Media</h2>
        </div>

        <!-- Form Filter Cari & Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Input Pencarian Utama -->
            <form action="{{ url()->current() }}" method="GET" class="relative" id="searchForm" onsubmit="return false;">
                {{-- Mempertahankan filter yang sedang aktif agar tidak hilang saat mencari --}}
                <input type="hidden" name="tahun" value="{{ request('tahun', $tahunTerpilih) }}">
                <input type="hidden" name="bulan_sosmed" value="{{ request('bulan_sosmed', $bulanSosmed) }}">
                <input type="hidden" name="kategori_konten"
                    value="{{ request('kategori_konten', $kategoriKontenTerpilih) }}">
                <input type="hidden" name="tipe_konten" value="{{ request('tipe_konten', $tipeKontenTerpilih) }}">
                <input type="hidden" name="sort_by" value="{{ request('sort_by', $sortBy) }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', $sortOrder) }}">

                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" id="inputSearchPost" value="{{ request('search') }}"
                    placeholder="Cari data postingan..." autocomplete="off"
                    class="w-48 sm:w-56 text-xs bg-slate-50 border border-black rounded-full pl-8 pr-4 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </form>

            <!-- Tombol Paste Spreadsheet -->
            <button @click="pasteModalOpen = true" type="button"
                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-green-700 rounded-xl px-3 py-1.5 hover:bg-green-800 transition shadow-md shadow-indigo-100 cursor-pointer">
                <i class="fa-solid fa-file-excel"></i> Paste Spreadsheet
            </button>

            <!-- Tombol Unduh Laporan Excel -->
            <a href="{{ route('post.export', array_merge(request()->query())) }}"
                class="inline-flex items-center gap-1 text-xs font-bold text-black bg-white border border-black rounded-xl px-3 py-1.5 hover:bg-slate-50 transition shadow-xxs cursor-pointer">
                <i class="fa-solid fa-download text-black"></i> Unduh
            </a>

            <!-- Tombol Reset Filter -->
            <a href="{{ route('pages.post.index', ['tahun' => $tahunTerpilih]) }}"
                class="w-7 h-7 inline-flex items-center justify-center text-black bg-slate-50 border border-black rounded-xl hover:bg-slate-100 transition cursor-pointer"
                title="Reset Semua Filter">
                <i class="fa-solid fa-rotate-left text-xs"></i>
            </a>

            <!-- Tombol Tambah Postingan -->
            <button @click="tambahModalOpen = true" type="button"
                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-teal-500 rounded-xl px-4 py-1.5 hover:bg-teal-600 transition shadow-md shadow-teal-100 cursor-pointer">
                <i class="fa-solid fa-circle-plus"></i> Tambah Postingan
            </button>
        </div>
    </div>

    <!-- Responsive Table Container -->
    <div class="w-full overflow-hidden">
        {{-- Form filter kolom tabel — bawa SEMUA filter aktif biar AJAX search gak "reset" filter lain --}}
        <form id="filterTableForm" action="{{ url()->current() }}" method="GET" onsubmit="return false;">
            <input type="hidden" name="tahun" value="{{ request('tahun', $tahunTerpilih) }}">
            <input type="hidden" name="bulan_sosmed" value="{{ request('bulan_sosmed', $bulanSosmed) }}">
            <input type="hidden" name="kategori_konten"
                value="{{ request('kategori_konten', $kategoriKontenTerpilih) }}">
            <input type="hidden" name="tipe_konten" value="{{ request('tipe_konten', $tipeKontenTerpilih) }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'tanggal') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="w-full overflow-x-auto">
                <table
                    class="w-full text-left border-collapse border-b border-black divide-y divide-x divide-black table-fixed">
                    <thead>
                        {{-- BARIS 1: NAMA KOLOM & TOMBOL SORTING --}}
                        <tr
                            class="bg-teal-500 text-white text-[7px] font-bold uppercase tracking-wider divide-x divide-black">
                            <th class="py-1.5 px-0.5 text-center w-6 border border-black">No</th>
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black">Aksi</th>

                            {{-- Sort Tanggal --}}
                            <th class="py-1.5 px-0.5 w-12 border border-black cursor-pointer select-none"
                                onclick="sortBy('tanggal')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>Tanggal</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'tanggal' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>

                            {{-- Topik  --}}
                            <th class="py-1.5 px-1 w-36 sm:w-48 border border-black text-center">Topik</th>
                            <th class="py-1.5 px-0.5 w-10 border border-black text-center">Link</th>

                            {{-- Sort Kategori --}}
                            <th class="py-1.5 px-0.5 text-center w-14 border border-black">
                                <span>Kategori</span>
                            </th>

                            <th class="py-1.5 px-0.5 w-12 border border-black text-center">Tipe</th>
                            <th class="py-1.5 px-0.5 w-14 border border-black text-center">Sosmed</th>

                            {{-- Sort Angka Metrics --}}
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black cursor-pointer select-none"
                                onclick="sortBy('view')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>View</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'view' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black cursor-pointer select-none"
                                onclick="sortBy('likes')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>Like</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'likes' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black cursor-pointer select-none"
                                onclick="sortBy('comments')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>Comment</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'comments' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black cursor-pointer select-none"
                                onclick="sortBy('share')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>Share</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'share' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>
                            <th class="py-1.5 px-0.5 text-center w-9 border border-black cursor-pointer select-none"
                                onclick="sortBy('retweet')">
                                <div class="flex items-center justify-center gap-0.5">
                                    <span>Retweet</span>
                                    <i
                                        class="fa-solid {{ $sortBy === 'retweet' ? ($sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} text-[7px] opacity-80"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyPost"
                        class="divide-x divide-y divide-black text-black text-[9px] font-medium transition-opacity duration-150">
                        @forelse ($daftarPost as $index => $post)
                            <tr class="hover:bg-slate-50/60 transition-colors divide-x divide-black">
                                <td class="py-1.5 px-0.5 text-center text-black font-normal border border-black">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Kolom Aksi -->
                                <td class="py-1.5 px-0.5 text-center border border-black"
                                    onclick="event.stopPropagation();">
                                    <div class="flex items-center justify-center gap-0.5">
                                        <button type="button"
                                            @click="$dispatch('buka-modal-edit', { id: {{ $post->id_post }} })"
                                            class="flex items-center justify-center w-4 h-4 rounded text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition cursor-pointer"
                                            title="Edit Postingan">
                                            <i class="fas fa-edit text-[9px]"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDeletePost('{{ route('pages.post.destroy', $post->id_post) }}', '{{ addslashes($post->topik) }}')"
                                            class="p-0.5 bg-gray-100 hover:bg-red-100 text-black hover:text-red-600 rounded transition cursor-pointer"
                                            title="Hapus Data">
                                            <i class="fas fa-trash-alt text-[7px]"></i>
                                        </button>
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-1.5 px-0.5 text-black whitespace-nowrap border border-black cursor-pointer select-none truncate text-center"
                                    data-id="{{ $post->id_post }}" data-field="tanggal" data-type="date"
                                    data-value="{{ \Carbon\Carbon::parse($post->tanggal)->format('Y-m-d') }}"
                                    ondblclick="enableInlineEdit(this)">
                                    {{ \Carbon\Carbon::parse($post->tanggal)->translatedFormat('d/m/y') }}
                                </td>

                                <!-- Topik -->
                                <td class="py-1.5 px-1 font-medium text-black text-[9px] break-words border border-black cursor-pointer select-none"
                                    data-id="{{ $post->id_post }}" data-field="topik" data-type="text"
                                    data-value="{{ $post->topik }}" ondblclick="enableInlineEdit(this)">
                                    <div class="line-clamp-2" title="{{ $post->topik }}">{{ $post->topik }}</div>
                                </td>

                                <!-- Link Post -->
                                <td class="py-1.5 px-0.5 whitespace-nowrap border border-black cursor-pointer select-none text-center"
                                    data-id="{{ $post->id_post }}" data-field="link_post"
                                    data-value="{{ $post->link_post }}" ondblclick="openLinkModal(this)">
                                    @if ($post->link_post)
                                        <a href="{{ $post->link_post }}" target="_blank"
                                            title="{{ $post->link_post }}"
                                            class="text-teal-600 hover:text-teal-700 hover:underline font-semibold text-[8px]">
                                            Buka <i class="fa-solid fa-arrow-up-right-from-square text-[7px]"></i>
                                        </a>
                                    @else
                                        <span class="text-black font-normal">-</span>
                                    @endif
                                </td>

                                <!-- Kategori -->
                                <td class="py-1.5 px-0.5 text-center whitespace-nowrap border border-black cursor-pointer select-none"
                                    data-id="{{ $post->id_post }}" data-field="kategori_konten" data-type="select"
                                    data-options='["Collab Content","Owned Production","Shared Content"]'
                                    data-value="{{ $post->kategori_konten }}" ondblclick="enableInlineEdit(this)">
                                    @if ($post->kategori_konten === 'Collab Content')
                                        <span
                                            class="px-1 py-0.2 rounded-full text-[7px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Collab</span>
                                    @elseif($post->kategori_konten === 'Owned Production')
                                        <span
                                            class="px-1 py-0.2 rounded-full text-[7px] font-bold bg-blue-50 text-blue-600 border border-blue-100">Owned</span>
                                    @else
                                        <span
                                            class="px-1 py-0.2 rounded-full text-[7px] font-bold bg-purple-50 text-purple-600 border border-purple-100">Shared</span>
                                    @endif
                                </td>

                                <!-- Tipe -->
                                <td class="py-1.5 px-0.5 text-black text-center whitespace-nowrap border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="tipe_konten" data-type="select"
                                    data-options='["Feed/Reels","Story"]' data-value="{{ $post->tipe_konten }}"
                                    ondblclick="enableInlineEdit(this)">
                                    {{ $post->tipe_konten }}
                                </td>

                                <!-- Sosmed -->
                                <td class="py-1.5 px-0.5 text-black text-center whitespace-nowrap border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="sosial_media" data-type="select"
                                    data-options='["Instagram","Facebook","Twitter/X","TikTok"]'
                                    data-value="{{ $post->sosial_media }}" ondblclick="enableInlineEdit(this)">
                                    <span class="inline-flex items-center justify-center gap-0.5">
                                        @if ($post->sosial_media === 'Instagram')
                                            <i class="fa-brands fa-instagram text-rose-500 text-[9px]"></i>
                                        @elseif($post->sosial_media === 'Facebook')
                                            <i class="fa-brands fa-facebook text-blue-600 text-[9px]"></i>
                                        @elseif($post->sosial_media === 'Twitter/X' || $post->sosial_media === 'Twitter')
                                            <i class="fa-brands fa-x-twitter text-black text-[9px]"></i>
                                        @elseif($post->sosial_media === 'TikTok')
                                            <i class="fa-brands fa-tiktok text-black text-[9px]"></i>
                                        @endif
                                        <span class="truncate">{{ $post->sosial_media }}</span>
                                    </span>
                                </td>

                                <!-- Views -->
                                <td class="py-1.5 px-0.5 text-center font-bold text-black border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="view" data-type="number"
                                    data-value="{{ $post->view }}" ondblclick="enableInlineEdit(this)">
                                    {{ number_format($post->view, 0, ',', '.') }}
                                </td>
                                <!-- Likes -->
                                <td class="py-1.5 px-0.5 text-center text-black border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="likes" data-type="number"
                                    data-value="{{ $post->likes }}" ondblclick="enableInlineEdit(this)">
                                    {{ number_format($post->likes, 0, ',', '.') }}
                                </td>
                                <!-- Comments -->
                                <td class="py-1.5 px-0.5 text-center text-black border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="comments" data-type="number"
                                    data-value="{{ $post->comments }}" ondblclick="enableInlineEdit(this)">
                                    {{ number_format($post->comments, 0, ',', '.') }}
                                </td>
                                <!-- Share -->
                                <td class="py-1.5 px-0.5 text-center text-black border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="share" data-type="number"
                                    data-value="{{ $post->share }}" ondblclick="enableInlineEdit(this)">
                                    {{ number_format($post->share, 0, ',', '.') }}
                                </td>
                                <!-- Retweet -->
                                <td class="py-1.5 px-0.5 text-center text-black border border-black cursor-pointer select-none text-[8px]"
                                    data-id="{{ $post->id_post }}" data-field="retweet" data-type="number"
                                    data-value="{{ $post->retweet }}" ondblclick="enableInlineEdit(this)">
                                    {{ number_format($post->retweet, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13"
                                    class="py-8 text-center text-black font-normal border border-black">
                                    <i class="fa-solid fa-folder-open text-xl mb-1 block"></i>
                                    Tidak ada data postingan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-white border-t border-black w-full">
                <button type="button" @click="tambahModalOpen = true"
                    class="w-full flex items-center justify-center gap-2 py-2 border-2 border-dashed border-teal-400 text-teal-600 hover:bg-teal-50 hover:border-teal-500 hover:text-teal-700 font-bold text-[11px] rounded-xl transition-all group cursor-pointer">
                    <i class="fas fa-plus-circle text-sm group-hover:scale-110 transition-transform"></i>
                    Tambah Postingan Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PASTE SPREADSHEET -->
<div x-show="pasteModalOpen" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
    x-transition.opacity>

    <div @click.away="pasteModalOpen = false"
        class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">

        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-emerald-600">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-white"></i> Import Data Postingan via Paste Spreadsheet (Excel)
            </h3>
            <button @click="pasteModalOpen = false"
                class="text-emerald-100 hover:text-white cursor-pointer transition">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-6 overflow-y-auto space-y-4 flex-1">
            <div class="text-[11px] text-slate-800 bg-emerald-50 border border-emerald-200 p-3 rounded-xl space-y-1">
                <p class="font-bold text-emerald-900">Panduan:</p>
                <p>1. Blok dan "copy" baris data dari Spreadsheet Anda (tanpa baris header kolom).</p>
                <p>2. Urutan kolom spreadsheet yang didukung: <br>
                    <code class="font-bold text-emerald-700 bg-emerald-100 px-1 py-0.5 rounded">NO | TANGGAL | BULAN |
                        TOPIK/JUDUL | KATEGORI | LINK/EVIDENCE | SOSIAL MEDIA | VIEWERS | LIKES | COMMENTS | SHARE |
                        RETWEET | SAVED</code>
                </p>
                <p>3. Lalu "Paste" ke dalam kotak teks di bawah ini.</p>
            </div>

            <form id="formPasteSpreadsheet" action="{{ route('post.store-paste') }}" method="POST">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Tempel Data Spreadsheet di Sini:</label>
                    <textarea name="raw_paste_data" id="rawPasteData" rows="8"
                        placeholder="Klik di sini lalu tekan Ctrl + V dari spreadsheet..."
                        class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl p-3 focus:outline-none focus:border-emerald-600 focus:bg-white font-mono"
                        oninput="parseSpreadsheetData()"></textarea>
                </div>

                <!-- Preview Hasil Parsing -->
                <div class="mt-4">
                    <span class="text-xs font-bold text-slate-800 block mb-1">Pratinjau Data yang Akan Masuk (<span
                            id="previewCount">0</span> baris valid):</span>
                    <div
                        class="border border-slate-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto bg-slate-50">
                        <table class="w-full text-left border-collapse text-[10px]" id="previewTable">
                            <thead class="bg-emerald-600 text-white sticky top-0">
                                <tr>
                                    <th class="p-2 border-b border-emerald-500">Tgl</th>
                                    <th class="p-2 border-b border-emerald-500">Topik</th>
                                    <th class="p-2 border-b border-emerald-500">Kategori</th>
                                    <th class="p-2 border-b border-emerald-500">Tipe</th>
                                    <th class="p-2 border-b border-emerald-500">Sosmed</th>
                                    <th class="p-2 border-b border-emerald-500 text-right">View</th>
                                    <th class="p-2 border-b border-emerald-500 text-right">Likes</th>
                                    <th class="p-2 border-b border-emerald-500 text-right">Comm</th>
                                    <th class="p-2 border-b border-emerald-500 text-right">Share</th>
                                    <th class="p-2 border-b border-emerald-500 text-right">Retweet</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                                <tr>
                                    <td colspan="10" class="p-4 text-center text-slate-400 italic">Belum ada data
                                        yang dipaste.</td>
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
            <button type="button" onclick="submitPasteData()"
                class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-200 cursor-pointer flex items-center gap-1">
                <i class="fa-solid fa-cloud-arrow-up"></i> Simpan ke Sistem
            </button>
        </div>
    </div>
</div>
<!-- Modal Edit Link -->
<div id="modalEditLink" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h3 class="text-base font-bold mb-4">Edit Link Post</h3>
        <form onsubmit="saveLinkViaModal(event)">

            {{-- PASTIKAN ID INI ADA DAN TIDAK SALAH KETIK --}}
            <input type="hidden" id="modalLinkPostId">

            <div class="mb-4">
                <label class="block text-xs font-semibold mb-1">URL / Link:</label>

                {{-- PASTIKAN ID INI ADA DAN TIDAK SALAH KETIK --}}
                <input type="text" id="modalLinkInput" class="w-full px-3 py-2 border rounded-lg text-xs"
                    required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeLinkModal()"
                    class="px-4 py-2 bg-gray-300 text-xs rounded-lg">Batal</button>
                <button type="submit" id="btnSaveLink"
                    class="px-4 py-2 bg-teal-600 text-white text-xs rounded-lg">Simpan</button>
            </div>
        </form>
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

<!-- JavaScript Parser Spreadsheet -->
<script>
    function parseSpreadsheetData() {
        const rawText = document.getElementById('rawPasteData').value;
        const lines = rawText.split('\n').filter(line => line.trim() !== '');
        const previewBody = document.getElementById('previewBody');
        const previewCount = document.getElementById('previewCount');
        const parsedRowsInput = document.getElementById('parsedRowsInput');

        previewBody.innerHTML = '';
        let parsedData = [];

        if (lines.length === 0) {
            previewBody.innerHTML =
                `<tr><td colspan="10" class="p-4 text-center text-black italic">Belum ada data yang dipaste.</td></tr>`;
            previewCount.innerText = '0';
            parsedRowsInput.value = '';
            return;
        }

        lines.forEach((line) => {
            // Pisahkan berdasarkan Tab (standar copy-paste dari Excel)
            const cols = line.split('\t').map(col => col.trim());

            // Jika karena suatu hal tidak ada tab, coba pisahkan dengan regex spasi ganti kolom
            let validCols = cols;
            if (cols.length < 7) {
                // Fallback jika kopiannya menempel rapat tanpa tab, kita bersihkan kata BULAN (Mei/Juni/Juli dll)
                let cleanLine = line.replace(
                    /(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|October|Oktober|November|Desember)/gi,
                    '\t$1\t');
                validCols = cleanLine.split('\t').map(col => col.trim()).filter(c => c !== '');
            }

            // Cari posisi kolom tanggal (yang mengandung format angka/angka/angka)
            let tanggalIdx = validCols.findIndex(c => /^\d{1,2}\/\d{1,2}\/\d{2,4}$/.test(c));

            if (tanggalIdx !== -1 && validCols.length >= (tanggalIdx + 7)) {
                let rawTanggal = validCols[tanggalIdx];
                let tanggalFormatted = formatDateToSQL(rawTanggal);

                // Kolom setelah tanggal biasanya bulan (kita lewati), lalu topik, kategori, dst.
                let topik = validCols[tanggalIdx + 2] || '';
                let kategori = normalizeKategori(validCols[tanggalIdx + 3] || '');
                let tipe = validCols[tanggalIdx + 4] || 'Feed/Reels';
                let link = validCols[tanggalIdx + 5] !== '' ? validCols[tanggalIdx + 5] : null;
                let sosmed = normalizeSosmed(validCols[tanggalIdx + 6] || 'Instagram');

                let view = parseNumber(validCols[tanggalIdx + 7]);
                let likes = parseNumber(validCols[tanggalIdx + 8]);
                let comments = parseNumber(validCols[tanggalIdx + 9]);
                let share = parseNumber(validCols[tanggalIdx + 10] || 0);
                let retweet = parseNumber(validCols[tanggalIdx + 11] || 0);

                let rowObj = {
                    tanggal: tanggalFormatted,
                    topik: topik,
                    kategori_konten: kategori,
                    tipe_konten: tipe,
                    link_post: link,
                    sosial_media: sosmed,
                    view: view,
                    likes: likes,
                    comments: comments,
                    share: share,
                    retweet: retweet
                };

                parsedData.push(rowObj);

                // Render ke tabel pratinjau
                previewBody.innerHTML += `
                <tr class="border-b border-slate-200 hover:bg-slate-100">
                    <td class="p-1.5 border-r border-slate-200">${rowObj.tanggal || '<span class="text-red-500">Kosong</span>'}</td>
                    <td class="p-1.5 border-r border-slate-200 truncate max-w-[120px]" title="${rowObj.topik}">${rowObj.topik}</td>
                    <td class="p-1.5 border-r border-slate-200">${rowObj.kategori_konten}</td>
                    <td class="p-1.5 border-r border-slate-200">${rowObj.tipe_konten}</td>
                    <td class="p-1.5 border-r border-slate-200">${rowObj.sosial_media}</td>
                    <td class="p-1.5 border-r border-slate-200 text-right">${rowObj.view}</td>
                    <td class="p-1.5 border-r border-slate-200 text-right">${rowObj.likes}</td>
                    <td class="p-1.5 border-r border-slate-200 text-right">${rowObj.comments}</td>
                    <td class="p-1.5 border-r border-slate-200 text-right">${rowObj.share}</td>
                    <td class="p-1.5 text-right">${rowObj.retweet}</td>
                </tr>
            `;
            }
        });

        previewCount.innerText = parsedData.length;
        parsedRowsInput.value = JSON.stringify(parsedData);
    }

    function parseNumber(val) {
        if (!val || val.trim() === '' || val.trim() === '-') return 0;
        let clean = val.replace(/[^0-9]/g, '');
        return clean === '' ? 0 : parseInt(clean);
    }

    function formatDateToSQL(dateStr) {
        if (!dateStr) return null;
        let parts = dateStr.split('/');
        if (parts.length === 3) {
            let month = parts[0].padStart(2, '0'); // Format 5/1/2026 -> Bulan 5 (Mei)
            let day = parts[1].padStart(2, '0'); // Hari ke-1
            let year = parts[2];
            if (year.length === 2) year = '20' + year;
            return `${year}-${month}-${day}`;
        }
        return dateStr;
    }

    function normalizeSosmed(sos) {
        let lower = sos.toLowerCase();
        if (lower.includes('facebook')) return 'Facebook';
        if (lower.includes('twitter') || lower.includes('x')) return 'Twitter/X';
        if (lower.includes('tiktok')) return 'TikTok';
        return 'Instagram';
    }

    function normalizeKategori(kat) {
        let lower = kat.toLowerCase();
        if (lower.includes('collab')) return 'Collab Content';
        if (lower.includes('share') || lower.includes('shared')) return 'Shared Content';
        return 'Owned Production';
    }

    function submitPasteData() {
        const inputVal = document.getElementById('parsedRowsInput').value;
        if (!inputVal || inputVal === '[]') {
            alert('Data paste kosong atau format tidak valid. Harap periksa kembali.');
            return;
        }
        document.getElementById('formPasteSpreadsheet').submit();
    }
</script>
<!-- JAVASCRIPT RE-ENGINEERED FOR SORT & EDIT -->
<script>
    function sortBy(column) {
        const form = document.getElementById('filterTableForm');
        if (!form) return;
        let currentSortBy = form.querySelector('input[name="sort_by"]').value;
        let currentOrder = form.querySelector('input[name="sort_order"]').value;

        if (currentSortBy === column) {
            form.querySelector('input[name="sort_order"]').value = (currentOrder === 'asc') ? 'desc' : 'asc';
        } else {
            form.querySelector('input[name="sort_by"]').value = column;
            form.querySelector('input[name="sort_order"]').value = 'asc';
        }

        // Sorting tetap full reload (di luar scope perbaikan search live)
        window.location.href = `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;
    }

    // --- MODAL EDIT HANDLER ---
    function openEditModal(id, dataPayload) {
        window.dispatchEvent(new CustomEvent('open-edit-modal', {
            detail: {
                id: id,
                data: dataPayload
            }
        }));
    }

    function handleEditButtonClick(button) {
        const rawData = button.getAttribute('data-post');
        if (!rawData) return;

        try {
            const dataPayload = JSON.parse(rawData);
            const postId = dataPayload.id_post || dataPayload.id;
            openEditModal(postId, dataPayload);
        } catch (err) {
            console.error("JSON Parsing error:", err);
            alert('Gagal memproses data postingan.');
        }
    }

    // --- DELETE MODAL HANDLER ---
    function confirmDeletePost(deleteUrl, postTitle) {
        const modal = document.getElementById('modalDeleteConfirmation');
        const targetNameSpan = document.getElementById('deleteTargetName');
        const deleteForm = document.getElementById('formDeleteAction');

        if (!modal || !deleteForm) return;

        if (targetNameSpan) targetNameSpan.textContent = postTitle;
        deleteForm.action = deleteUrl;

        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('modalDeleteConfirmation');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
        }
    });

    // --- HELPER INLINE EDIT ---
    function renderDisplayValue(field, value) {
        switch (field) {
            case 'kategori_konten': {
                const styleMap = {
                    'Collab Content': 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'Owned Production': 'bg-blue-50 text-blue-600 border-blue-100',
                    'Shared Content': 'bg-purple-50 text-purple-600 border-purple-100'
                };
                const labelMap = {
                    'Collab Content': 'Collab',
                    'Owned Production': 'Owned',
                    'Shared Content': 'Shared'
                };
                const cls = styleMap[value] || 'bg-gray-50 text-gray-600 border-gray-100';
                const label = labelMap[value] || value;
                return `<span class="px-2 py-0.5 rounded-full text-[9px] font-bold border uppercase tracking-wide ${cls}">${label}</span>`;
            }
            case 'sosial_media': {
                const iconMap = {
                    'Instagram': '<i class="fa-brands fa-instagram text-rose-500"></i>',
                    'Facebook': '<i class="fa-brands fa-facebook text-blue-600"></i>',
                    'Twitter/X': '<i class="fa-brands fa-x-twitter text-black"></i>',
                    'TikTok': '<i class="fa-brands fa-tiktok text-black"></i>'
                };
                const icon = iconMap[value] || '';
                return `<span class="flex items-center gap-1">${icon} ${value}</span>`;
            }
            case 'view':
            case 'likes':
            case 'comments':
            case 'share':
            case 'retweet':
                return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
            case 'tanggal': {
                const d = new Date(value);
                if (isNaN(d)) return value;
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }
            case 'link_post':
                if (!value) return `<span class="text-black font-normal">-</span>`;
                return `<a href="${value}" target="_blank" title="${value}" class="text-teal-600 hover:text-teal-700 hover:underline inline-flex items-center gap-1 font-semibold">Buka Link <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i></a>`;
            default:
                return value;
        }
    }

    function enableInlineEdit(tdElement) {
        if (tdElement.querySelector('input, select')) return;

        const id = tdElement.getAttribute('data-id');
        const field = tdElement.getAttribute('data-field');
        const type = tdElement.getAttribute('data-type') || 'text';
        const originalValue = tdElement.getAttribute('data-value') ?? tdElement.innerText.trim();

        let inputHtml = '';

        if (type === 'select') {
            const options = JSON.parse(tdElement.getAttribute('data-options') || '[]');
            let optionsHtml = options.map(opt => `
                <option value="${opt}" ${opt === originalValue ? 'selected' : ''}>${opt}</option>
            `).join('');

            inputHtml = `<select class="w-full text-xs p-1 border border-teal-500 rounded-lg focus:outline-none bg-white font-medium text-black">
                ${optionsHtml}
            </select>`;
        } else {
            inputHtml =
                `<input type="${type}" value="${originalValue}" 
                class="w-full text-xs px-2 py-1 border border-teal-500 rounded-lg focus:outline-none bg-white font-medium text-black" />`;
        }

        tdElement.innerHTML = inputHtml;
        const inputControl = tdElement.querySelector('input, select');
        inputControl.focus();

        let isSaving = false; // Flag cegah double submit

        function saveChange() {
            if (isSaving) return;
            isSaving = true;

            const newValue = inputControl.value;

            if (newValue === originalValue) {
                tdElement.innerHTML = renderDisplayValue(field, originalValue);
                return;
            }

            tdElement.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-teal-600 text-xs"></i>`;

            const url = `{{ route('pages.post.inline-update', 0) }}`.replace('/0', `/${id}`);

            fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        column: field,
                        value: newValue
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const savedValue = data.value !== undefined ? data.value : newValue;
                        tdElement.setAttribute('data-value', savedValue);
                        tdElement.innerHTML = renderDisplayValue(field, savedValue);
                        tdElement.classList.add('bg-emerald-100');
                        setTimeout(() => tdElement.classList.remove('bg-emerald-100'), 1500);
                    } else {
                        alert(data.message || 'Gagal memperbarui data.');
                        tdElement.innerHTML = renderDisplayValue(field, originalValue);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan koneksi server.');
                    tdElement.innerHTML = renderDisplayValue(field, originalValue);
                });
        }

        inputControl.addEventListener('blur', saveChange);

        if (type !== 'select') {
            inputControl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveChange();
                } else if (e.key === 'Escape') {
                    isSaving = true;
                    tdElement.innerHTML = renderDisplayValue(field, originalValue);
                }
            });
        } else {
            inputControl.addEventListener('change', saveChange);
        }
    }
    // --- LINK MODAL HANDLER ---
    let currentLinkTdTarget = null;

    function openLinkModal(tdElement) {
        currentLinkTdTarget = tdElement;
        const id = tdElement.getAttribute('data-id');
        const currentValue = tdElement.getAttribute('data-value') || '';

        document.getElementById('modalLinkPostId').value = id;
        document.getElementById('modalLinkInput').value = currentValue;

        const modal = document.getElementById('modalEditLink');
        modal.classList.remove('hidden');

        setTimeout(() => {
            document.getElementById('modalLinkInput').focus();
        }, 100);
    }

    function closeLinkModal() {
        const modal = document.getElementById('modalEditLink');
        if (modal) modal.classList.add('hidden');
        currentLinkTdTarget = null;
    }

    function saveLinkViaModal(event) {
        event.preventDefault();

        const id = document.getElementById('modalLinkPostId').value;
        const newUrl = document.getElementById('modalLinkInput').value.trim();
        const btnSave = document.getElementById('btnSaveLink');

        if (!currentLinkTdTarget) return;

        const originalBtnText = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = `<i class="fa-solid fa-spinner animate-spin text-xs"></i> Menyimpan...`;

        const url = `{{ route('pages.post.inline-update', 0) }}`.replace('/0', `/${id}`);

        fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    column: 'link_post',
                    value: newUrl
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const savedValue = data.value !== undefined ? data.value : newUrl;

                    currentLinkTdTarget.setAttribute('data-value', savedValue);
                    currentLinkTdTarget.innerHTML = renderDisplayValue('link_post', savedValue);

                    currentLinkTdTarget.classList.add('bg-emerald-100');
                    setTimeout(() => currentLinkTdTarget.classList.remove('bg-emerald-100'), 1500);

                    closeLinkModal();
                } else {
                    alert(data.message || 'Gagal memperbarui Link Post.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi server.');
            })
            .finally(() => {
                btnSave.disabled = false;
                btnSave.innerHTML = originalBtnText;
            });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            closeLinkModal();
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const inputSearch = document.getElementById('inputSearchPost');
        const filterForm = document.getElementById('filterTableForm');
        const tableBody = document.getElementById('tableBodyPost');

        if (inputSearch && filterForm && tableBody) {
            let typingTimer;
            const doneTypingInterval = 400; // jeda 0.4 detik biar gak nembak request tiap huruf

            function triggerSearch() {
                filterForm.querySelector('input[name="search"]').value = inputSearch.value;

                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const targetUrl = filterForm.action;
                const fullUrl = `${targetUrl}?${params.toString()}`;

                tableBody.style.opacity = '0.4';

                fetch(fullUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        // data.html = seluruh render ulang file tabel ini (form+thead+tbody)
                        // kita ambil cuma <tbody id="tableBodyPost"> dari situ
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');
                        const newTbody = doc.getElementById('tableBodyPost');

                        if (newTbody) {
                            tableBody.innerHTML = newTbody.innerHTML;
                        }
                        tableBody.style.opacity = '1';

                        // Update URL browser tanpa reload halaman
                        window.history.replaceState({}, '', fullUrl);
                    })
                    .catch(err => {
                        console.error('Gagal memuat hasil pencarian:', err);
                        tableBody.style.opacity = '1';
                    });
            }

            inputSearch.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(triggerSearch, doneTypingInterval);
            });

            // Enter tetap didukung: langsung search tanpa nunggu debounce, TAPI tidak reload halaman
            inputSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(typingTimer);
                    triggerSearch();
                }
            });
        }
    });
</script>
