<!-- Modal Form Tambah Berita (tambah.blade.php) -->
<div x-show="tambahModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-transparent" x-transition
    x-cloak>

    <div class="w-full max-w-2xl bg-white rounded-2xl overflow-hidden border border-black shadow-2xl max-h-[90vh] flex flex-col"
        @click.away="tambahModalOpen = false">

        <!-- Header Modal -->
        <div
            class="bg-gradient-to-r from-teal-500 to-emerald-500 p-4 flex items-center justify-between border-b border-black shrink-0">
            <h3 class="text-white font-extrabold text-sm sm:text-base tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-newspaper"></i> Tambah Data Media
            </h3>
            <button type="button" @click="tambahModalOpen = false"
                class="text-white/80 hover:text-white transition cursor-pointer text-sm">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form Konten -->
        <form action="{{ route('pages.berita.store') }}" method="POST" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
            @csrf

            <input type="hidden" name="tahun_filter" value="{{ request('tahun', 'all') }}">
            <input type="hidden" name="bulan_filter" value="{{ request('bulan', 'all') }}">
            <input type="hidden" name="tone_filter" value="{{ request('tone', 'all') }}">
            <input type="hidden" name="topik_filter" value="{{ request('topik', 'all') }}">
            <input type="hidden" name="sifat_filter" value="{{ request('sifat_berita', 'all') }}">

            <input type="hidden" name="tahun_terpilih" value="{{ $tahunTerpilih ?? date('Y') }}">

            <!-- Judul Berita -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Judul Berita <span
                        class="text-red-500">*</span></label>
                <input type="text" name="judul" placeholder="Masukkan judul berita..." required
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <!-- Tanggal & Nama Media -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tanggal <span
                            class="text-red-500">*</span></label>

                    <input type="date" name="tanggal"
                        value="{{ (isset($tahunTerpilih) && $tahunTerpilih != 'all' ? $tahunTerpilih : date('Y')) .
                            '-' .
                            (isset($bulanTerpilih) && $bulanTerpilih != 'all' ? str_pad($bulanTerpilih, 2, '0', STR_PAD_LEFT) : date('m')) .
                            '-' .
                            date('d') }}"
                        required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Nama Media <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_media" placeholder="Contoh: Kompas, Pontianak Post" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>
            </div>

            <!-- Tone, Topik, Sifat Berita -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tone <span
                            class="text-red-500">*</span></label>
                    <select name="tone" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                        <option value="Positif">Positif</option>
                        <option value="Netral" selected>Netral</option>
                        <option value="Negatif">Negatif</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Topik <span
                            class="text-red-500">*</span></label>
                    <select name="topik" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                        <option value="Operasi">Operasi</option>
                        <option value="CSR">CSR</option>
                        <option value="Inovasi">Inovasi</option>
                        <option value="Apresiasi">Apresiasi</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Sifat Berita <span
                            class="text-red-500">*</span></label>
                    <select name="sifat_berita" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                        <option value="Internal">Internal</option>
                        <option value="Eksternal" selected>Eksternal</option>
                    </select>
                </div>
            </div>

            <hr class="border-dashed border-gray-300 my-2">

            <!-- Reporter, Spokesperson, Role -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Reporter</label>
                    <input type="text" name="reporter" placeholder="Nama wartawan"
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Spokesperson</label>
                    <input type="text" name="spokeperson" placeholder="Nama narasumber"
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Jabatan
                        Spokesperson</label>
                    <input type="text" name="spokeperson_role" placeholder="Jabatan narasumber"
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>
            </div>

            <!-- Link Berita -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Link Berita (URL)</label>
                <input type="url" name="link_berita" placeholder="https://media.com/berita/..."
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" @click="tambahModalOpen = false"
                    class="py-2 px-5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl transition cursor-pointer border border-black">
                    Batal
                </button>
                <button type="submit"
                    class="py-2 px-6 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-bold text-xs rounded-xl transition shadow-md shadow-teal-100 cursor-pointer border border-teal-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Berita
                </button>
            </div>
        </form>
    </div>
</div>
