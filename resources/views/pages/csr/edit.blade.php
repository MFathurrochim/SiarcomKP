<div class="w-full text-xs flex flex-col h-full min-h-0">

    @if (session('error'))
        <div
            class="bg-red-50 border-l-4 border-red-500 p-2 mx-3 mt-2.5 text-[11px] text-red-700 rounded-r-lg font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Modal -->
    <div
        class="bg-gradient-to-r from-teal-400 to-emerald-400 px-4 py-2.5 flex items-center justify-between border-b border-black shrink-0">
        <h3 class="text-white font-extrabold text-xs sm:text-sm tracking-wide flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i> Edit Kegiatan CSR
        </h3>
        <button type="button" onclick="closeFormModal()"
            class="text-white/90 hover:text-white transition cursor-pointer text-sm">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <!-- Form Input Edit -->
    <form action="{{ route('csr.update', $csr->id_csr) }}" method="POST" id="csrEditForm"
        class="space-y-2.5 p-3.5 overflow-y-auto flex-1 min-h-0">
        @csrf
        @method('PUT')

        <input type="hidden" name="tahun_realisasi"
            value="{{ old('tahun_realisasi', $csr->tahun_realisasi ?? ($tahunTerpilih ?? date('Y'))) }}">

        <!-- Nama Program -->
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                Nama Program <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_program" value="{{ old('nama_program', $csr->nama_program) }}"
                placeholder="Masukkan nama program..." required
                class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
        </div>

        <!-- Baris: Pilar & Bulan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Pilar ({{ $csr->tahun_realisasi ?? $tahunTerpilih }}) <span class="text-red-500">*</span>
                </label>
                <select name="pilar" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Pilar</option>
                    @foreach ($listPilar as $pilar)
                        {{-- Disesuaikan dengan relasi pilar pada controller ($csr->pilarRelasi->nama_pilar atau fallback string langsung) --}}
                        <option value="{{ $pilar }}"
                            {{ old('pilar', optional($csr->pilarRelasi)->nama_pilar ?? $csr->pilar) == $pilar ? 'selected' : '' }}>
                            {{ $pilar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Bulan <span class="text-red-500">*</span>
                </label>
                <select name="bulan_realisasi" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Bulan</option>
                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bln)
                        <option value="{{ $bln }}"
                            {{ old('bulan_realisasi', $csr->bulan_realisasi) == $bln ? 'selected' : '' }}>
                            {{ $bln }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Baris: TPB & Asta Cita -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    TPB <span class="text-red-500">*</span>
                </label>
                <select name="tpb" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih TPB</option>
                    @foreach (['1. Tanpa Kemiskinan', '2. Tanpa Kelaparan', '3. Kehidupan Sehat dan Sejahtera', '4. Pendidikan Berkualitas', '5. Kesetaraan Gender', '6. Air Bersih dan Sanitasi Layak', '7. Energi Bersih dan Terjangkau', '8. Pekerjaan Layak dan Pertumbuhan Ekonomi', '9. Industri, Inovasi dan Infrastruktur', '10. Berkurangnya Kesenjangan', '11. Kota dan Permukiman yang Berkelanjutan', '12. Konsumsi dan Produksi yang Bertanggung Jawab', '13. Penanganan Perubahan Iklim', '14. Ekosistem Kelautan', '15. Ekosistem Daratan', '16. Perdamaian, Keadilan dan Kelembagaan yang Tangguh', '17. Kemitraan untuk Mencapai Tujuan'] as $tpbOption)
                        <option value="{{ $tpbOption }}"
                            {{ old('tpb', $csr->tpb) == $tpbOption ? 'selected' : '' }}>
                            {{ $tpbOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Asta Cita <span class="text-red-500">*</span>
                </label>
                <select name="asta_cita" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Asta Cita</option>
                    @php
                        $listAstaCita = [
                            1 => '1. Memperkokoh ideologi Pancasila, demokrasi, dan hak asasi manusia (HAM);',
                            2 => '2. Memantapkan sistem pertahanan keamanan & kemandirian swasembada pangan, energi, air, ekonomi kreatif, hijau & biru;',
                            3 => '3. Meningkatkan lapangan kerja berkualitas, kewirausahaan, industri kreatif, & pengembangan infrastruktur;',
                            4 => '4. Memperkuat pembangunan SDM, sains, teknologi, pendidikan, kesehatan, olahraga, & kesetaraan gender;',
                            5 => '5. Melanjutkan hilirisasi dan industrialisasi untuk meningkatkan nilai tambah di dalam negeri;',
                            6 => '6. Membangun dari desa dan dari bawah untuk pemerataan ekonomi dan pemberantasan kemiskinan;',
                            7 => '7. Memperkuat reformasi politik, hukum, birokrasi, serta pencegahan dan pemberantasan korupsi & narkoba;',
                            8 => '8. Memperkuat penyelarasan kehidupan harmonis dengan lingkungan, alam, budaya, & toleransi beragama;',
                        ];
                    @endphp

                    @foreach ($listAstaCita as $key => $deskripsiPendek)
                        <option value="Asta Cita {{ $key }}"
                            {{ old('asta_cita', $csr->asta_cita) == "Asta Cita $key" ? 'selected' : '' }}>
                            {{ $deskripsiPendek }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Baris: Cabang & Bentuk Bantuan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Cabang <span class="text-red-500">*</span>
                </label>
                <select name="cabang" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Cabang</option>
                    @foreach (['PNK', 'PKY', 'BDJ', 'BPN'] as $cab)
                        <option value="{{ $cab }}"
                            {{ old('cabang', $csr->cabang) == $cab ? 'selected' : '' }}>
                            {{ $cab }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Bentuk Bantuan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="bentuk_bantuan" value="{{ old('bentuk_bantuan', $csr->bentuk_bantuan) }}"
                    placeholder="Contoh: Modal Kerja" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>
        </div>

        <!-- Baris: Kabupaten & Desa -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Kabupaten</label>
                <input type="text" name="kabupaten" value="{{ old('kabupaten', $csr->kabupaten) }}"
                    placeholder="Contoh: Kubu Raya"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Desa</label>
                <input type="text" name="desa" value="{{ old('desa', $csr->desa) }}"
                    placeholder="Contoh: Limbung"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>
        </div>

        <!-- Baris: Biaya Program, Biaya Realisasi & Status Realisasi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Biaya Program (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="biaya_program" id="edit_biaya_program"
                    value="{{ old('biaya_program', $csr->biaya_program) ? number_format(old('biaya_program', $csr->biaya_program), 0, ',', '.') : '' }}"
                    placeholder="Contoh: 250.000.000" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Biaya Realisasi (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="biaya_realisasi" id="edit_biaya_realisasi"
                    value="{{ old('biaya_realisasi', $csr->biaya_realisasi) ? number_format(old('biaya_realisasi', $csr->biaya_realisasi), 0, ',', '.') : '' }}"
                    placeholder="Contoh: 250.000.000" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Status Realisasi <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="done" {{ old('status', strtolower($csr->status)) == 'done' ? 'selected' : '' }}>
                        Done</option>
                    <option value="undone"
                        {{ old('status', strtolower($csr->status)) == 'undone' ? 'selected' : '' }}>Undone
                    </option>
                </select>
            </div>
        </div>

        <!-- Keterangan Realisasi Program -->
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-bold text-black uppercase tracking-wider">Realisasi Program
                (Keterangan)</label>
            <textarea name="realisasi_program" rows="2" placeholder="Masukkan detail realisasi program..."
                class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">{{ old('realisasi_program', $csr->realisasi_program) }}</textarea>
        </div>

        <!-- Hidden input untuk penggabungan multi-link JS ke Controller -->
        <input type="hidden" name="link_ig" id="final_edit_link_ig" value="{{ old('link_ig', $csr->link_ig) }}">
        <input type="hidden" name="link_berita" id="final_edit_link_berita"
            value="{{ old('link_berita', $csr->link_berita) }}">

        <hr class="border-dashed border-gray-300 my-1.5">

        <!-- Section Link IG, Link Berita & Link GDrive (Multi-Link Dinamis & GDrive Single Input) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 bg-slate-50 p-2 rounded-lg border border-slate-200">
            <div>
                <label class="block text-[9px] font-bold text-gray-700 mb-1">Link IG</label>
                <div id="edit-ig-container" class="space-y-1">
                    @php
                        $igLinks = array_filter(preg_split('/\r\n|\r|\n/', old('link_ig', $csr->link_ig ?? '')));
                    @endphp

                    @foreach ($igLinks as $index => $link)
                        <div class="flex items-center gap-1.5 field-row">
                            <span
                                class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">{{ $index + 1 }}.</span>
                            <input type="text" placeholder="https://instagram.com/..."
                                value="{{ $link }}"
                                class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                        </div>
                    @endforeach

                    <div class="flex items-center gap-1.5 field-row">
                        <span
                            class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">{{ count($igLinks) + 1 }}.</span>
                        <input type="text" placeholder="https://instagram.com/..."
                            class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[9px] font-bold text-gray-700 mb-1">Link Berita</label>
                <div id="edit-berita-container" class="space-y-1">
                    @php
                        $beritaLinks = array_filter(
                            preg_split('/\r\n|\r|\n/', old('link_berita', $csr->link_berita ?? '')),
                        );
                    @endphp

                    @foreach ($beritaLinks as $index => $link)
                        <div class="flex items-center gap-1.5 field-row">
                            <span
                                class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">{{ $index + 1 }}.</span>
                            <input type="text" placeholder="https://berita.com/..." value="{{ $link }}"
                                class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                        </div>
                    @endforeach

                    <div class="flex items-center gap-1.5 field-row">
                        <span
                            class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">{{ count($beritaLinks) + 1 }}.</span>
                        <input type="text" placeholder="https://berita.com/..."
                            class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[9px] font-bold text-gray-700 mb-1">Link GDrive</label>
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                        <input type="text" name="link_gdrive" id="edit_link_gdrive"
                            value="{{ old('link_gdrive', $csr->link_gdrive) }}"
                            placeholder="https://drive.google.com/..."
                            class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" onclick="closeFormModal()"
                class="py-1.5 px-4 bg-slate-100 hover:bg-slate-200 text-black font-bold text-[11px] rounded-lg transition cursor-pointer border border-black">
                Batal
            </button>
            <button type="submit"
                class="py-1.5 px-5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-bold text-[11px] rounded-lg transition shadow-md shadow-teal-100 cursor-pointer border border-teal-600 flex items-center gap-1.5">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    if (!window.__csrEditDynamicInputInitialized) {
        window.__csrEditDynamicInputInitialized = true;

        function reorderEditNumbers(container) {
            if (!container) return;
            container.querySelectorAll(".row-num").forEach((span, index) => {
                span.textContent = `${index + 1}.`;
            });
        }

        function formatRupiahEdit(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        }

        document.addEventListener("input", function(e) {
            if (e.target && (e.target.id === "edit_biaya_program" || e.target.id === "edit_biaya_realisasi")) {
                e.target.value = formatRupiahEdit(e.target.value);
                return;
            }

            if (!e.target.classList.contains("dynamic-input")) return;

            const inputElement = e.target;
            const container = inputElement.closest("#edit-ig-container") ||
                inputElement.closest("#edit-berita-container");
            if (!container) return;

            let type = "ig";
            if (container.id === "edit-berita-container") type = "berita";

            const allInputs = container.querySelectorAll(".dynamic-input");
            const lastInput = allInputs[allInputs.length - 1];
            const value = inputElement.value.trim();

            if (inputElement === lastInput && value !== "") {
                const nextNumber = allInputs.length + 1;
                let placeholderText = "https://instagram.com/...";
                if (type === "berita") placeholderText = "https://berita.com/...";

                const newRow = document.createElement("div");
                newRow.className = "flex items-center gap-1.5 field-row";
                newRow.innerHTML = `
                    <span class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">${nextNumber}.</span>
                    <input type="text" placeholder="${placeholderText}"
                        class="w-full text-[11px] border border-gray-300 rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                `;

                container.appendChild(newRow);

                newRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });

        document.addEventListener("focusout", function(e) {
            if (!e.target.classList.contains("dynamic-input")) return;

            const inputElement = e.target;
            const container = inputElement.closest("#edit-ig-container") ||
                inputElement.closest("#edit-berita-container");
            if (!container) return;

            setTimeout(() => {
                const allInputs = container.querySelectorAll(".dynamic-input");
                if (allInputs.length <= 1) return;

                const lastInput = allInputs[allInputs.length - 1];

                if (inputElement.value.trim() === "" && inputElement !== lastInput && document
                    .activeElement !== inputElement) {
                    const rowToElement = inputElement.closest(".field-row");
                    if (rowToElement) {
                        rowToElement.remove();
                        reorderEditNumbers(container);
                    }
                }
            }, 100);
        });

        document.addEventListener("submit", function(e) {
            const form = e.target;
            if (form.id !== "csrEditForm") return;

            const biayaInput = form.querySelector("#edit_biaya_program");
            if (biayaInput) biayaInput.value = biayaInput.value.replace(/\./g, '');

            const realisasiInput = form.querySelector("#edit_biaya_realisasi");
            if (realisasiInput) realisasiInput.value = realisasiInput.value.replace(/\./g, '');

            const igInputs = form.querySelectorAll("#edit-ig-container .dynamic-input");
            const igValues = Array.from(igInputs).map(input => input.value.trim()).filter(val => val !== "");
            const finalIg = form.querySelector("#final_edit_link_ig");
            if (finalIg) finalIg.value = igValues.join("\n");

            const beritaInputs = form.querySelectorAll("#edit-berita-container .dynamic-input");
            const beritaValues = Array.from(beritaInputs).map(input => input.value.trim()).filter(val => val !==
                "");
            const finalBerita = form.querySelector("#final_edit_link_berita");
            if (finalBerita) finalBerita.value = beritaValues.join("\n");
        });
    }
</script>
