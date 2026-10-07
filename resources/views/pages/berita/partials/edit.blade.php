<form action="{{ route('pages.berita.update', $berita->id_berita) }}" method="POST" id="formEditBerita"
    x-data="{
        isSubmitting: false,
        async submitEdit(e) {
            this.isSubmitting = true;
            const form = e.target;
            const formData = new FormData(form);
    
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
    
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memperbarui data');
    
                updateRowInTable('{{ $berita->id_berita }}', formData);
                editModalOpen = false;
                showFlashMessage('success', data.message);
            } catch (error) {
                showFlashMessage('error', error.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }" @submit.prevent="submitEdit($event)"
    class="w-full bg-white rounded-2xl overflow-hidden border border-black shadow-2xl space-y-0">
    @csrf
    @method('PUT')

    <!-- Header Modal -->
    <div
        class="bg-gradient-to-r from-teal-400 to-emerald-400 p-4 flex items-center justify-between border-b border-black">
        <h3 class="text-white font-extrabold text-sm sm:text-base tracking-wide flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i> Edit Berita
        </h3>
        <button type="button" @click="editModalOpen = false"
            class="text-white/90 hover:text-white transition cursor-pointer text-sm p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Body Form -->
    <div class="p-5 sm:p-6 space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Tanggal -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Tanggal <span
                        class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required
                    value="{{ old('tanggal', \Carbon\Carbon::parse($berita->tanggal)->format('Y-m-d')) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('tanggal')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Media -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Nama Media <span
                        class="text-red-500">*</span></label>
                <input type="text" name="nama_media" placeholder="Contoh: Kompas, Detik, dll" required
                    value="{{ old('nama_media', $berita->nama_media) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('nama_media')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Judul Berita -->
        <div class="flex flex-col gap-1.5">
            <label class="text-[10px] font-bold text-black uppercase tracking-wider">Judul Berita <span
                    class="text-red-500">*</span></label>
            <textarea name="judul" rows="2" required placeholder="Masukkan judul berita"
                class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium resize-none">{{ old('judul', $berita->judul) }}</textarea>
            @error('judul')
                <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Reporter -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Reporter <span
                        class="text-red-500">*</span></label>
                <input type="text" name="reporter" placeholder="Nama reporter" required
                    value="{{ old('reporter', $berita->reporter) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('reporter')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Link Berita -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Link Berita</label>
                <input type="url" name="link_berita" placeholder="https://..."
                    value="{{ old('link_berita', $berita->link_berita) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('link_berita')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Spokeperson -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Spokeperson <span
                        class="text-red-500">*</span></label>
                <input type="text" name="spokeperson" placeholder="Nama narasumber" required
                    value="{{ old('spokeperson', $berita->spokeperson) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('spokeperson')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Spokeperson Role -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold text-black uppercase tracking-wider">Role Spokeperson <span
                        class="text-red-500">*</span></label>
                <input type="text" name="spokeperson_role" placeholder="Contoh: Direktur Utama" required
                    value="{{ old('spokeperson_role', $berita->spokeperson_role) }}"
                    class="w-full text-xs bg-slate-50 border border-black rounded-xl px-3 py-2 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                @error('spokeperson_role')
                    <p class="text-red-500 text-[10px] font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <hr class="border-dashed border-gray-300 my-2">

        <!-- SEKSI KLASIFIKASI BERITA -->
        <div class="bg-slate-50/50 p-3 rounded-xl border border-gray-200">
            <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-widest mb-3">
                <i class="fa-solid fa-tags"></i> Klasifikasi Berita
            </span>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Tone -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Tone <span class="text-red-500">*</span></label>
                    <select name="tone" required
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                        <option value="Positif" {{ old('tone', $berita->tone) == 'Positif' ? 'selected' : '' }}>Positif
                        </option>
                        <option value="Netral" {{ old('tone', $berita->tone) == 'Netral' ? 'selected' : '' }}>Netral
                        </option>
                        <option value="Negatif" {{ old('tone', $berita->tone) == 'Negatif' ? 'selected' : '' }}>Negatif
                        </option>
                    </select>
                </div>

                <!-- Topik -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Topik <span class="text-red-500">*</span></label>
                    <select name="topik" required
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                        <option value="Operasi" {{ old('topik', $berita->topik) == 'Operasi' ? 'selected' : '' }}>
                            Operasi</option>
                        <option value="CSR" {{ old('topik', $berita->topik) == 'CSR' ? 'selected' : '' }}>CSR
                        </option>
                        <option value="Inovasi" {{ old('topik', $berita->topik) == 'Inovasi' ? 'selected' : '' }}>
                            Inovasi</option>
                        <option value="Apresiasi" {{ old('topik', $berita->topik) == 'Apresiasi' ? 'selected' : '' }}>
                            Apresiasi</option>
                    </select>
                </div>

                <!-- Sifat Berita -->
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-black">Sifat Berita <span
                            class="text-red-500">*</span></label>
                    <select name="sifat_berita" required
                        class="w-full text-xs bg-white border border-black rounded-xl px-3 py-1.5 focus:outline-none focus:border-teal-500 transition font-semibold text-black">
                        <option value="Internal"
                            {{ old('sifat_berita', $berita->sifat_berita) == 'Internal' ? 'selected' : '' }}>Internal
                        </option>
                        <option value="Eksternal"
                            {{ old('sifat_berita', $berita->sifat_berita) == 'Eksternal' ? 'selected' : '' }}>Eksternal
                        </option>
                    </select>
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
