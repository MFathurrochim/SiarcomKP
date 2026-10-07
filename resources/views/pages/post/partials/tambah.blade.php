<div x-show="tambahModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/10" x-transition
    x-cloak>

    <div class="w-full max-w-2xl bg-white rounded-2xl overflow-hidden border border-black shadow-2xl"
        @click.away="tambahModalOpen = false">

        <!-- Header Modal -->
        <div
            class="bg-gradient-to-r from-teal-400 to-emerald-400 p-4 flex items-center justify-between border-b border-black">
            <h3 class="text-white font-extrabold text-sm sm:text-base tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-square-plus"></i> Tambah Postingan Sosial Media
            </h3>
            <button type="button" @click="tambahModalOpen = false"
                class="text-white/90 hover:text-white transition cursor-pointer text-sm">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form Konten dengan Alpine State Lokal -->
        <form action="{{ route('pages.post.store') }}" method="POST" class="p-5 sm:p-6 space-y-4"
            x-data="{
                sosmed: 'Instagram',
                linkPost: '',
                // Set default tanggal ke tahun terpilih (contoh: 2026-01-01)
                tanggal: '{{ $tahunTerpilih }}-{{ date('m-d') }}',
                isScraping: false,
                metrics: { view: 0, likes: 0, comments: 0, share: 0, retweet: 0 },
            
                async tarikData() {
                    if (!this.linkPost) {
                        alert('Masukkan link postingan Instagram terlebih dahulu!');
                        return;
                    }
                    this.isScraping = true;
                    try {
                        let res = await fetch(`/sosmed-monitoring/scrape-ig?link_post=${encodeURIComponent(this.linkPost)}`);
                        let data = await res.json();
                        if (data.success) {
                            this.metrics.likes = data.data.likes ?? 0;
                            this.metrics.comments = data.data.comments ?? 0;
                            this.metrics.view = data.data.view ?? 0;
                            alert('Data metrik Instagram berhasil ditarik otomatis!');
                        } else {
                            alert(data.message || 'Gagal menarik data. Coba cek link kembali.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal terhubung ke server scraping. Masukkan data secara manual.');
                    } finally {
                        this.isScraping = false;
                    }
                }
            }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Input Tanggal dengan Validasi Tahun & Auto Set -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">
                        Tanggal (Tahun {{ $tahunTerpilih }})
                    </label>
                    <input type="date" name="tanggal" x-model="tanggal" min="{{ $tahunTerpilih }}-01-01"
                        max="{{ $tahunTerpilih }}-12-31" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <p class="text-[9px] text-gray-500">
                        *Terisi otomatis sesuai filter tahun (1 Jan - 31 Des {{ $tahunTerpilih }}).
                    </p>
                </div>

                <!-- Input Topik -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Topik</label>
                    <input type="text" name="topik" placeholder="Masukkan topik postingan" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Dropdown Kategori Konten -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Kategori Konten</label>
                    <select name="kategori_konten" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                        <option value="Collab Content">Collab Content</option>
                        <option value="Owned Production">Owned Production</option>
                        <option value="Shared Content">Shared Content</option>
                    </select>
                </div>

                <!-- Dropdown Tipe Konten -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tipe Konten</label>
                    <select name="tipe_konten" required
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                        <option value="Feed/Reels">Feed/Reels</option>
                        <option value="Story">Story</option>
                    </select>
                </div>
            </div>

            <!-- Input Sosial Media (menggunakan x-model) -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Sosial Media</label>
                <select name="sosial_media" required x-model="sosmed"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="Instagram">Instagram</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Twitter/X">Twitter/X</option>
                    <option value="TikTok">TikTok</option>
                </select>
            </div>

            <!-- Input Link Post + Fitur Tarik Data -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Link Post</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="url" name="link_post" x-model="linkPost"
                            placeholder="https://www.instagram.com/p/..."
                            class="w-full text-xs bg-slate-50 border border-black rounded-xl pl-3 pr-10 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    </div>

                    <!-- Tombol Tarik Data Otomatis (Reaktif via Alpine) -->
                    <button type="button" @click="tarikData()" :disabled="sosmed !== 'Instagram' || isScraping"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 bg-teal-50 border border-teal-500 rounded-xl px-4 py-2 hover:bg-teal-100 transition shadow-xxs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer whitespace-nowrap">
                        <i
                            :class="isScraping ? 'fa-solid fa-circle-notch animate-spin' : 'fa-solid fa-wand-magic-sparkles'"></i>
                        <span x-text="isScraping ? 'Memproses...' : 'Tarik Data'"></span>
                    </button>
                </div>
                <p class="text-[10px] text-gray-500 mt-0.5"><i class="fa-solid fa-circle-info"></i> Fitur tarik data
                    otomatis sementara hanya mendukung platform Instagram.</p>
            </div>

            <hr class="border-dashed border-gray-300 my-2">

            <!-- SEKSI INPUT METRIK MANUAL ATAU HASIL SCRAPE -->
            <div class="bg-slate-50/50 p-3 rounded-xl border border-gray-200">
                <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-widest mb-3"><i
                        class="fa-solid fa-chart-simple"></i> Data Metrik Postingan</span>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <!-- Views -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-black">Views</label>
                        <input type="number" name="view" x-model="metrics.view" min="0"
                            class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                    </div>
                    <!-- Likes -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-black">Likes</label>
                        <input type="number" name="likes" x-model="metrics.likes" min="0"
                            class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                    </div>
                    <!-- Comments -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-black">Comments</label>
                        <input type="number" name="comments" x-model="metrics.comments" min="0"
                            class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                    </div>
                    <!-- Share -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-black">Share</label>
                        <input type="number" name="share" x-model="metrics.share" min="0"
                            class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                    </div>
                    <!-- Retweet -->
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-black">Retweet</label>
                        <input type="number" name="retweet" x-model="metrics.retweet" min="0"
                            class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" @click="tambahModalOpen = false"
                    class="py-2 px-5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl transition cursor-pointer border border-black">
                    Batal
                </button>
                <button type="submit"
                    class="py-2 px-6 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-bold text-xs rounded-xl transition shadow-md shadow-teal-100 cursor-pointer border border-teal-600">
                    Tambah Postingan
                </button>
            </div>
        </form>
    </div>
</div>
