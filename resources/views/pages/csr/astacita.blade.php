<!-- Container Utama Realisasi per Asta Cita dengan Border Gradien -->
<div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-2xl shadow-sm mb-6">
    <div class="bg-white p-5 rounded-[15px]">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-black text-base flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-[#27B78F] text-sm"></i> Realisasi per Asta Cita
            </h3>
        </div>

        <!-- Grid Indikator Panjang Horizontal (8 Asta Cita) - Diberi ID untuk target AJAX -->
        <div id="container-asta-cita" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <!-- Loading state awal -->
            <div class="col-span-full text-center py-4 text-xs text-gray-500">Memuat data Asta Cita...</div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT AJAX ASTA CITA -->
<script>
    async function loadAstaCita() {
        // Menggunakan helper getActiveFilters() yang sudah dibuat di Grid 2 sebelumnya
        const filters = getActiveFilters();
        const queryString = buildQueryString(filters);

        try {
            const response = await fetch(`/api/csr/asta-cita?${queryString}`);
            const dataAstaCitaChart = await response.json();

            // 1. Lakukan pemetaan data (map) seperti logika PHP Anda sebelumnya di JS
            let mapAstaCita = {};
            let maxJumlah = 0;

            dataAstaCitaChart.forEach(item => {
                if (item.asta_cita) {
                    let cleaned = item.asta_cita.replace(/Asta Cita/i, '').trim();
                    let key = !isNaN(cleaned) && cleaned !== '' ? 'Asta Cita ' + parseInt(cleaned) : item
                        .asta_cita.trim();
                    let total = parseInt(item.total_jumlah) || 0;

                    mapAstaCita[key] = total;
                    if (total > maxJumlah) maxJumlah = total;
                }
            });

            // 2. Render ulang HTML Grid (1 sampai 8)
            const container = document.getElementById('container-asta-cita');
            container.innerHTML = '';

            for (let i = 1; i <= 8; i++) {
                let keyEnum = 'Asta Cita ' + i;
                let jumlahKegiatan = mapAstaCita[keyEnum] || 0;
                let widthPercent = 0;

                if (jumlahKegiatan > 0) {
                    widthPercent = maxJumlah > 0 ? Math.max(8, Math.min(100, Math.round((jumlahKegiatan /
                        maxJumlah) * 100))) : 0;
                }

                let itemHtml = `
                    <div class="flex flex-col justify-between p-2.5 rounded-xl border border-gray-150 bg-gray-50">
                        <!-- Label Atas & Jumlah -->
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[11px] font-black text-gray-700" title="Asta Cita ${i}">
                                AC ${i}
                            </span>
                            <span class="text-xs font-black text-black">
                                ${jumlahKegiatan}
                            </span>
                        </div>

                        <!-- Progress Line Mini Component -->
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            ${jumlahKegiatan > 0 ? `
                                <div class="bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] h-1.5 rounded-full transition-all duration-500"
                                    style="width: ${widthPercent}%"></div>
                            ` : `
                                <div class="bg-transparent h-1.5 rounded-full" style="width: 0%"></div>
                            `}
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            }

        } catch (error) {
            console.error('Gagal memuat Data Asta Cita:', error);
            document.getElementById('container-asta-cita').innerHTML =
                `<div class="col-span-full text-center py-4 text-xs text-red-500">Gagal memuat data Asta Cita.</div>`;
        }
    }

    // Daftarkan ke event listener global agar ikut ter-refresh saat filter atas diubah
    document.addEventListener('DOMContentLoaded', function() {
        loadAstaCita();

        // Menyatukan event listener dengan event grid sebelumnya
        window.addEventListener('csr-filter-changed', function() {
            loadAstaCita();
        });
    });
</script>
