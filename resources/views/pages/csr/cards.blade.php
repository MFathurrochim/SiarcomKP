<!-- Top Cards Grid -->
<div class="mb-2.5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">

    <!-- Card 1: Jumlah Kegiatan -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-md shadow-2xs">
        <div class="bg-white rounded-[5px] p-1.5 flex flex-col items-center justify-center text-center h-full">
            <span class="text-[8px] font-bold text-black uppercase tracking-wide">Total Kegiatan</span>
            <span id="card-total-kegiatan"
                class="text-xs font-black text-black mt-0.5 leading-tight">{{ $totalKegiatan }}</span>
        </div>
    </div>

    <!-- Card 2: Realisasi Selesai -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-md shadow-2xs">
        <div class="bg-white rounded-[5px] p-1.5 flex flex-col items-center justify-center text-center h-full">
            <span class="text-[8px] font-bold text-black uppercase tracking-wide">Realisasi Selesai</span>
            <span id="card-realisasi-done"
                class="text-xs font-black text-black mt-0.5 leading-tight">{{ $totalRealisasiDone }}</span>
        </div>
    </div>

    <!-- Card 3: Total Realisasi -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-md shadow-2xs">
        <div class="bg-white rounded-[5px] p-1.5 flex flex-col items-center justify-center text-center h-full">
            <span class="text-[8px] font-bold text-black uppercase tracking-wide">Total Realisasi</span>
            <span id="card-total-realisasi" class="text-[11px] font-black text-black mt-0.5 leading-tight">Rp
                {{ number_format($totalSumBiayaRealisasi ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Card 4: Kabupaten -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-md shadow-2xs">
        <div class="bg-white rounded-[5px] p-1.5 flex flex-col items-center justify-center text-center h-full">
            <span class="text-[8px] font-bold text-black uppercase tracking-wide">Kabupaten</span>
            <span id="card-total-kabupaten"
                class="text-xs font-black text-black mt-0.5 leading-tight">{{ $totalKabupaten }}</span>
        </div>
    </div>

    <!-- Card 5: Total Desa -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-md shadow-2xs">
        <div class="bg-white rounded-[5px] p-1.5 flex flex-col items-center justify-center text-center h-full">
            <span class="text-[8px] font-bold text-black uppercase tracking-wide">Total Desa</span>
            <span id="card-total-desa"
                class="text-xs font-black text-black mt-0.5 leading-tight">{{ $totalDesa }}</span>
        </div>
    </div>

</div>

<!-- SCRIPT UNTUK MENANGKAP FILTER GLOBAL DAN UPDATE DATA CARD VIA AJAX -->
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('csr-filter-changed', function(event) {
                const filters = event.detail;
                fetchTopCardsData(filters);
            });
        });

        function fetchTopCardsData(filters) {
            const params = new URLSearchParams({
                tahun: filters.tahun || '',
                bulan: filters.bulan || '',
                g_pilar: filters.g_pilar || '',
                g_program: filters.g_program || '',
                g_kabupaten: filters.g_kabupaten || '',
                g_desa: filters.g_desa || '',
                g_status: filters.g_status || '', // Pastikan g_status ikut dikirim via AJAX
            });

            fetch(`/api/csr/statistik-card?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('card-total-kegiatan').textContent = data.total_kegiatan ?? 0;
                    document.getElementById('card-realisasi-done').textContent = data.realisasi_done ?? 0;

                    // Total realisasi biaya (otomatis 0 jika status di-filter undone)
                    document.getElementById('card-total-realisasi').textContent = 'Rp ' + new Intl.NumberFormat('id-ID')
                        .format(data.total_realisasi ?? 0);

                    // Memperbaiki typo dari data.total_upaten menjadi data.total_kabupaten
                    document.getElementById('card-total-kabupaten').textContent = data.total_kabupaten ?? 0;
                    document.getElementById('card-total-desa').textContent = data.total_desa ?? 0;
                })
                .catch(error => console.error('Gagal memperbarui card statistik:', error));
        }
    </script>
@endpush
