<form action="{{ route('pages.post.update', $post->id_post) }}" method="POST" id="formEditPost" x-data="{
    selectedSosmed: '{{ old('sosial_media', $post->sosial_media) }}',
    linkPost: '{{ old('link_post', $post->link_post) }}',
    isScraping: false,
    isSubmitting: false
}"
    @submit="isSubmitting = true"
    class="w-full bg-white rounded-2xl overflow-hidden border border-black shadow-2xl space-y-0">
    @csrf
    @method('PUT')

    <!-- Header Modal -->
    <div
        class="bg-gradient-to-r from-teal-400 to-emerald-400 p-4 flex items-center justify-between border-b border-black">
        <h3 class="text-white font-extrabold text-sm sm:text-base tracking-wide flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i> Edit Postingan
        </h3>
        <button type="button" @click="editModalOpen = false"
            class="text-white/90 hover:text-white transition cursor-pointer text-sm p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Body Form -->
    <div class="p-5 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Input Tanggal -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tanggal <span
                        class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required
                    value="{{ old('tanggal', \Carbon\Carbon::parse($post->tanggal)->format('Y-m-d')) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('tanggal')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Topik -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Topik <span
                        class="text-red-500">*</span></label>
                <input type="text" name="topik" placeholder="Masukkan topik postingan" required
                    value="{{ old('topik', $post->topik) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('topik')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Dropdown Kategori Konten -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Kategori Konten <span
                        class="text-red-500">*</span></label>
                <select name="kategori_konten" required
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="Collab Content"
                        {{ old('kategori_konten', $post->kategori_konten) == 'Collab Content' ? 'selected' : '' }}>
                        Collab Content</option>
                    <option value="Owned Production"
                        {{ old('kategori_konten', $post->kategori_konten) == 'Owned Production' ? 'selected' : '' }}>
                        Owned Production</option>
                    <option value="Shared Content"
                        {{ old('kategori_konten', $post->kategori_konten) == 'Shared Content' ? 'selected' : '' }}>
                        Shared Content</option>
                </select>
                @error('kategori_konten')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dropdown Tipe Konten -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tipe Konten <span
                        class="text-red-500">*</span></label>
                <select name="tipe_konten" required
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="Feed/Reels"
                        {{ old('tipe_konten', $post->tipe_konten) == 'Feed/Reels' ? 'selected' : '' }}>Feed/Reels
                    </option>
                    <option value="Story" {{ old('tipe_konten', $post->tipe_konten) == 'Story' ? 'selected' : '' }}>
                        Story</option>
                </select>
                @error('tipe_konten')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Input Sosial Media -->
        <div class="flex flex-col gap-1.5">
            <label class="text-[10px] font-bold text-black uppercase tracking-wider">Sosial Media <span
                    class="text-red-500">*</span></label>
            <select name="sosial_media" required x-model="selectedSosmed"
                class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                <option value="Instagram">Instagram</option>
                <option value="Facebook">Facebook</option>
                <option value="Twitter/X">Twitter/X</option>
                <option value="TikTok">TikTok</option>
            </select>
            @error('sosial_media')
                <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Input Link Post + Fetch Data -->
        <div class="flex flex-col gap-1.5">
            <label class="text-[10px] font-bold text-black uppercase tracking-wider">Link Post</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input type="url" id="edit_link_post" name="link_post" x-model="linkPost"
                        placeholder="https://www.instagram.com/p/..."
                        class="w-full text-xs bg-slate-50 border border-black rounded-xl pl-3 pr-10 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                </div>

                <button type="button" id="btnScrapeEdit"
                    @click="isScraping = true; setTimeout(() => isScraping = false, 1500)"
                    :disabled="selectedSosmed !== 'Instagram' || isScraping"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 bg-teal-50 border border-teal-500 rounded-xl px-4 py-2 hover:bg-teal-100 transition shadow-xxs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer whitespace-nowrap">
                    <i
                        :class="isScraping ? 'fa-solid fa-circle-notch animate-spin' : 'fa-solid fa-wand-magic-sparkles'"></i>
                    <span x-text="isScraping ? 'Memproses...' : 'Fetch Data'"></span>
                </button>
            </div>
            @error('link_post')
                <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
            @enderror
        </div>

        <hr class="border-dashed border-gray-300 my-2">

        <!-- SEKSI INPUT METRIK -->
        <div class="bg-slate-50/50 p-3 rounded-xl border border-gray-200">
            <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-widest mb-3">
                <i class="fa-solid fa-chart-simple"></i> Data Metrik Postingan
            </span>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Views</label>
                    <input type="number" name="view" min="0" value="{{ old('view', $post->view ?? 0) }}"
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Likes</label>
                    <input type="number" name="likes" min="0" value="{{ old('likes', $post->likes ?? 0) }}"
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Comments</label>
                    <input type="number" name="comments" min="0"
                        value="{{ old('comments', $post->comments ?? 0) }}"
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Share</label>
                    <input type="number" name="share" min="0"
                        value="{{ old('share', $post->share ?? 0) }}"
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Retweet</label>
                    <input type="number" name="retweet" min="0"
                        value="{{ old('retweet', $post->retweet ?? 0) }}"
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" @click="editModalOpen = false"
                class="py-2 px-5 bg-slate-100 hover:bg-slate-200 text-black font-bold text-xs rounded-xl transition cursor-pointer border border-black">
                Batal
            </button>
            <button type="submit" :disabled="isSubmitting"
                class="py-2 px-6 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-bold text-xs rounded-xl transition shadow-md shadow-teal-100 cursor-pointer border border-teal-600 disabled:opacity-50">
                <span x-show="!isSubmitting" class="flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </span>
                <span x-show="isSubmitting" class="flex items-center gap-1.5" x-cloak>
                    <i class="fa-solid fa-circle-notch animate-spin"></i> Menyimpan...
                </span>
            </button>
        </div>
    </div>
</form>
