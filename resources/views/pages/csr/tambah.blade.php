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
            <i class="fa-solid fa-hand-holding-heart"></i> Tambah Kegiatan CSR
        </h3>
        <button type="button" onclick="closeFormModal()"
            class="text-white/90 hover:text-white transition cursor-pointer text-sm">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <!-- Form Container — flex-1 + min-h-0 WAJIB biar overflow-y-auto beneran jalan -->
    <form action="{{ route('csr.store') }}" method="POST" id="csrForm"
        class="space-y-2.5 p-3.5 overflow-y-auto flex-1 min-h-0">
        @csrf

        <input type="hidden" name="tahun_realisasi" value="{{ $tahunTerpilih ?? date('Y') }}">

        <!-- Baris 1: Nama Program (Full Width) -->
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                Nama Program <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="text" name="nama_program" id="nama_program_input" list="list_nama_program"
                    value="{{ old('nama_program') }}" placeholder="Ketik atau pilih nama program..." required
                    autocomplete="off"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">

                <datalist id="list_nama_program">
                    @php
                        $defaultPrograms = [
                            'Injourney Airport Berdaya UMK',
                            'Injourney Airport Cerdaskan Bangsa',
                            'Injourney Airport Alam Lestari',
                            'Injourney Airport Pangan Berdaya',
                            'Injourney Airport Peduli Fasilitas',
                            'Injourney Airport Rawat Yatim dan Lansia',
                        ];
                        $existingPrograms = isset($daftarProgramUnik) ? $daftarProgramUnik->toArray() : [];
                        $allPrograms = array_unique(array_merge($defaultPrograms, $existingPrograms));
                    @endphp

                    @foreach ($allPrograms as $prog)
                        <option value="{{ $prog }}"></option>
                    @endforeach
                </datalist>
            </div>
            <span class="text-[8px] text-gray-500 italic">* Pilih opsi atau ketik nama program baru.</span>
        </div>

        <!-- Baris 2: Pilar & Bulan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Pilar ({{ $tahunTerpilih }}) <span class="text-red-500">*</span>
                </label>
                <select name="pilar" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Pilar</option>
                    @foreach ($listPilar as $p)
                        <option value="{{ $p }}" {{ old('pilar') == $p ? 'selected' : '' }}>
                            {{ $p }}</option>
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
                        <option value="{{ $bln }}" {{ old('bulan_realisasi') == $bln ? 'selected' : '' }}>
                            {{ $bln }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Baris 3: Asta Cita -->
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                Asta Cita <span class="text-red-500">*</span>
            </label>
            <div class="relative w-full">
                <select name="asta_cita" required
                    class="w-full text-[10px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
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
                            {{ old('asta_cita') == "Asta Cita $key" ? 'selected' : '' }}>
                            {{ $deskripsiPendek }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Baris 4: TPB -->
        <div class="flex flex-col gap-1">
            <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                TPB <span class="text-red-500">*</span>
            </label>
            <select name="tpb" required
                class="w-full text-[10px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                <option value="">Pilih TPB</option>
                @foreach (['1. Tanpa Kemiskinan', '2. Tanpa Kelaparan', '3. Kehidupan Sehat dan Sejahtera', '4. Pendidikan Berkualitas', '5. Kesetaraan Gender', '6. Air Bersih dan Sanitasi Layak', '7. Energi Bersih dan Terjangkau', '8. Pekerjaan Layak dan Pertumbuhan Ekonomi', '9. Industri, Inovasi dan Infrastruktur', '10. Berkurangnya Kesenjangan', '11. Kota dan Permukiman yang Berkelanjutan', '12. Konsumsi dan Produksi yang Bertanggung Jawab', '13. Penanganan Perubahan Iklim', '14. Ekosistem Kelautan', '15. Ekosistem Daratan', '16. Perdamaian, Keadilan dan Kelembagaan yang Tangguh', '17. Kemitraan untuk Mencapai Tujuan'] as $tpbOption)
                    <option value="{{ $tpbOption }}" {{ old('tpb') == $tpbOption ? 'selected' : '' }}>
                        {{ $tpbOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Baris 5: Cabang & Bentuk Bantuan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Cabang <span class="text-red-500">*</span>
                </label>
                <select name="cabang" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="">Pilih Cabang</option>
                    @foreach (['PNK'] as $cab)
                        <option value="{{ $cab }}" {{ old('cabang') == $cab ? 'selected' : '' }}>
                            {{ $cab }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Bentuk Bantuan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="bentuk_bantuan" value="{{ old('bentuk_bantuan') }}"
                    placeholder="Contoh: Modal Kerja" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>
        </div>

        <!-- Baris 6: Kabupaten & Desa -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Kabupaten</label>
                <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" placeholder="Contoh: Kubu Raya"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Desa</label>
                <input type="text" name="desa" value="{{ old('desa') }}" placeholder="Contoh: Limbung"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>
        </div>

        <!-- Baris 7: Biaya Program & Biaya Realisasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Biaya Program (Rp)</label>
                <input type="text" id="biaya_program_formatted"
                    value="{{ old('biaya_program') ? number_format(old('biaya_program'), 0, ',', '.') : '' }}"
                    placeholder="Contoh: 250.000.000"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium format-rupiah">
                <input type="hidden" name="biaya_program" id="biaya_program" value="{{ old('biaya_program') }}">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Biaya Realisasi (Rp)</label>
                <input type="text" id="biaya_realisasi_formatted"
                    value="{{ old('biaya_realisasi') ? number_format(old('biaya_realisasi'), 0, ',', '.') : '' }}"
                    placeholder="Contoh: 250.000.000"
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium format-rupiah">
                <input type="hidden" name="biaya_realisasi" id="biaya_realisasi"
                    value="{{ old('biaya_realisasi') }}">
            </div>
        </div>

        <!-- Baris 8: Status Realisasi & Keterangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">
                    Status Realisasi <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
                    <option value="done" {{ old('status', 'done') == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="undone" {{ old('status') == 'undone' ? 'selected' : '' }}>Undone</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[9px] font-bold text-black uppercase tracking-wider">Realisasi Program</label>
                <input type="text" name="realisasi_program" value="{{ old('realisasi_program') }}"
                    placeholder="Detail realisasi..."
                    class="w-full text-[11px] bg-slate-50 border border-black rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-teal-500 focus:bg-white transition text-black font-medium">
            </div>
        </div>

        <!-- Hidden inputs untuk kompresi link JS -->
        <input type="hidden" name="link_ig" id="final_link_ig" value="{{ old('link_ig') }}">
        <input type="hidden" name="link_berita" id="final_link_berita" value="{{ old('link_berita') }}">

        <hr class="border-dashed border-gray-300 my-1.5">
        <!-- Section Link IG & Link Berita -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 bg-slate-50 p-2 rounded-lg border border-black">
            <div>
                <label class="block text-[9px] font-bold text-gray-700 mb-1">Link IG</label>
                <div id="ig-container" class="space-y-1">
                    <div class="flex items-center gap-1.5 field-row">
                        <span class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">1.</span>
                        <input type="text" placeholder="https://instagram.com/..."
                            class="w-full text-[11px] border border-black rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[9px] font-bold text-gray-700 mb-1">Link Berita</label>
                <div id="berita-container" class="space-y-1">
                    <div class="flex items-center gap-1.5 field-row">
                        <span class="text-[9px] font-bold text-gray-400 w-4 text-center row-num">1.</span>
                        <input type="text" placeholder="https://berita.com/..."
                            class="w-full text-[11px] border border-black rounded-md p-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none dynamic-input">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Link GDrive (Single Link) -->
        <div class="bg-slate-50 p-2 rounded-lg border border-black">
            <label class="block text-[9px] font-bold text-gray-700 mb-1 flex items-center gap-1">
                <i class="fab fa-google-drive text-[#0F9D58]"></i> Link Google Drive
            </label>
            <div class="relative">
                <input type="text" name="link_gdrive" value="{{ old('link_gdrive') }}"
                    placeholder="https://drive.google.com/..."
                    class="w-full text-[11px] border border-black rounded-md px-2.5 py-1.5 focus:ring-1 focus:ring-teal-500 focus:outline-none bg-white font-medium text-black">
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
                <i class="fa-solid fa-floppy-disk"></i> Simpan Program
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        function formatRupiah(angka) {
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

        function reorderNumbers(container) {
            if (!container) return;
            container.querySelectorAll(".row-num").forEach((span, index) => {
                span.textContent = `${index + 1}.`;
            });
        }

        const form = document.getElementById("csrForm");
        if (form && !form.dataset.initialized) {
            form.dataset.initialized = "true";

            // Handler format Rupiah secara real-time
            form.addEventListener("input", function(e) {
                if (e.target && e.target.classList.contains("format-rupiah")) {
                    let formatted = formatRupiah(e.target.value);
                    e.target.value = formatted;

                    if (e.target.id === 'biaya_program_formatted') {
                        document.getElementById('biaya_program').value = formatted.replace(/\./g, '');
                    } else if (e.target.id === 'biaya_realisasi_formatted') {
                        document.getElementById('biaya_realisasi').value = formatted.replace(/\./g, '');
                    }
                    return;
                }

                // Handler Dynamic Input untuk Link IG & Berita
                if (!e.target.classList.contains("dynamic-input")) return;

                const inputElement = e.target;
                const container = inputElement.closest("#ig-container") || inputElement.closest(
                    "#berita-container");
                if (!container) return;

                const type = container.id === "ig-container" ? "ig" : "berita";
                const allInputs = container.querySelectorAll(".dynamic-input");
                const lastInput = allInputs[allInputs.length - 1];
                const value = inputElement.value.trim();

                // Jika input terakhir diisi, otomatis buat baris baru di bawahnya
                if (inputElement === lastInput && value !== "") {
                    const nextNumber = allInputs.length + 1;
                    const placeholderText = type === "ig" ? "https://instagram.com/..." :
                        "https://berita.com/...";

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

            // Hapus baris kosong jika pengguna meninggalkan input (focusout) selain baris terakhir
            form.addEventListener("focusout", function(e) {
                if (!e.target.classList.contains("dynamic-input")) return;

                const inputElement = e.target;
                const container = inputElement.closest("#ig-container") || inputElement.closest(
                    "#berita-container");
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
                            reorderNumbers(container);
                        }
                    }
                }, 100);
            });

            // Sebelum form disubmit, gabungkan semua input link dengan format baris baru (\n) agar terbaca oleh multiLinkRule() di Controller
            form.addEventListener("submit", function() {
                const igInputs = form.querySelectorAll("#ig-container .dynamic-input");
                let igValues = [];
                igInputs.forEach(inp => {
                    if (inp.value.trim() !== '') igValues.push(inp.value.trim());
                });
                const finalIg = document.getElementById("final_link_ig");
                if (finalIg) finalIg.value = igValues.join("\n");

                const beritaInputs = form.querySelectorAll("#berita-container .dynamic-input");
                let beritaValues = [];
                beritaInputs.forEach(inp => {
                    if (inp.value.trim() !== '') beritaValues.push(inp.value.trim());
                });
                const finalBerita = document.getElementById("final_link_berita");
                if (finalBerita) finalBerita.value = beritaValues.join("\n");
            });
        }
    })();
</script>
