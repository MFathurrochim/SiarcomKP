<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">

    <!-- KOTAK 1: ANGGARAN PER PILAR -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-xl shadow-sm">
        <div class="bg-white p-3 rounded-[11px] flex flex-col justify-between min-h-[220px]">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-extrabold text-slate-800 text-xs">Anggaran per Pilar</h3>

                    <!-- Tombol Simpan (Awalnya tersembunyi / hidden) -->
                    <button type="button" id="btnSimpanAnggaran" onclick="simpanPerubahanAnggaran()"
                        class="hidden text-[9px] font-bold text-white bg-teal-600 hover:bg-teal-700 px-2.5 py-1 rounded transition-all shadow-sm animate-pulse">
                        Simpan Perubahan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="pb-1.5 text-[9px] font-bold text-slate-900 uppercase tracking-wider">Pilar
                                </th>
                                <th
                                    class="pb-1.5 text-[9px] font-bold text-slate-900 uppercase tracking-wider text-right">
                                    Total Anggaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50/50" id="tbody-anggaran-pilar">
                            <!-- Data dimuat lewat AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-[8px] text-slate-800 font-medium italic mt-2 border-t border-slate-8 pt-1">
                Klik 2 kali di bagian anggaran untuk mengubah nominalnya.
            </div>
        </div>
    </div>

    <!-- KOTAK 2: REALISASI PER PILAR -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-xl shadow-sm">
        <div class="bg-white p-3 rounded-[11px] flex flex-col justify-between min-h-[220px]">
            <div class="w-full flex justify-between items-center mb-2">
                <h3 class="font-extrabold text-slate-800 text-xs">Realisasi per Pilar</h3>
                <span class="text-[9px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded">
                    Tahun {{ $tahunTerpilih }}
                </span>
            </div>
            <div id="containerRealisasiPilar" class="w-full flex-1 flex flex-col justify-center space-y-3.5">
                <!-- Loading state -->
            </div>
        </div>
    </div>

    <!-- KOTAK 3: JUMLAH KEGIATAN BAR CHART -->
    <div class="bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] p-[1px] rounded-xl shadow-sm">
        <div class="bg-white p-3 rounded-[11px] flex flex-col justify-between min-h-[220px]">
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <h3 class="font-extrabold text-slate-800 text-xs">Jumlah Realisasi Program {{ $tahunTerpilih }}</h3>
                    <span class="text-[9px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded">
                        Bulanan
                    </span>
                </div>
                <div class="relative h-28 w-full mt-1">
                    <canvas id="barKegiatanBulanan"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let barChartInstance;

    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Chart Bar Kegiatan Bulanan dengan data awal dari Blade (jika ada)
        const ctxBar = document.getElementById('barKegiatanBulanan').getContext('2d');
        const dataBulanan = @json(array_values($kegiatanBulanan ?? []));
        const labelBulanan = @json(array_keys($kegiatanBulanan ?? [])).map(b => b.substring(0, 3).toUpperCase());

        const gradientBarKegiatan = ctxBar.createLinearGradient(0, 0, 0, 150);
        gradientBarKegiatan.addColorStop(0, '#27B78F');
        gradientBarKegiatan.addColorStop(1, '#12B4C9');

        barChartInstance = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labelBulanan,
                datasets: [{
                    label: 'Jumlah Kegiatan',
                    data: dataBulanan,
                    backgroundColor: gradientBarKegiatan,
                    borderRadius: 3,
                    barThickness: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 20 // Memberikan ruang kosong di atas bar agar angka tidak terpotong
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            color: '#64748B',
                            font: {
                                weight: 'bold',
                                size: 9
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: {
                                weight: 'bold',
                                size: 9
                            }
                        }
                    }
                }
            },
            // PLUGIN TAMBAHAN UNTUK MENAMPILKAN ANGKA DI ATAS DIAGRAM
            plugins: [{
                id: 'customCanvasAngka',
                afterDatasetsDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        const meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            const dataVal = dataset.data[index];

                            // Opsional: Lewati jika nilai 0 agar tidak terlalu padat
                            if (dataVal === 0) return;

                            ctx.fillStyle = '#1e293b';
                            ctx.font = 'bold 9px sans-serif';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            const position = bar.tooltipPosition();
                            ctx.fillText(dataVal, position.x, position.y - 4);
                        });
                    });
                }
            }]
        });

        // Load data awal untuk semua kotak menggunakan endpoint gabungan
        refreshAllGrid1Data();
    });

    /**
     * FUNGSI FORMAT RIBUAN (Contoh: 1000000 menjadi 1.000.000)
     */
    function formatRibuan(angka) {
        if (!angka && angka !== 0) return '';
        let numberString = angka.toString().replace(/[^,\d]/g, ''),
            split = numberString.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    }

    /**
     * FUNGSI BERSIHKAN TITIK (Sebelum dikirim ke backend)
     */
    function bersihTitik(nilai) {
        if (!nilai) return 0;
        return nilai.toString().replace(/\./g, '');
    }

    window.addEventListener('csr-filter-changed', function(e) {
        refreshAllGrid1Data();
    });

    function getActiveFilterParams() {
        const bulan = document.getElementById('globalBulan')?.value || '';
        const program = document.getElementById('globalProgram')?.value || '';
        const pilar = document.getElementById('globalPilar')?.value || '';
        const kabupaten = document.getElementById('globalKabupaten')?.value || '';
        const desa = document.getElementById('globalDesa')?.value || '';
        const tahun = document.getElementById('globalTahun')?.value || (typeof tahunAktif !== 'undefined' ? tahunAktif :
            '{{ $tahunTerpilih }}');

        const params = new URLSearchParams({
            tahun: tahun
        });

        if (bulan) params.append('bulan', bulan);
        if (program) params.append('g_program', program);
        if (pilar) params.append('g_pilar', pilar);
        if (kabupaten) params.append('g_kabupaten', kabupaten);
        if (desa) params.append('g_desa', desa);
        if (typeof status !== 'undefined' && status) params.append('g_status', status);

        return params;
    }

    /**
     * FUNGSI UTAMA: Mengambil 3 data sekaligus dalam 1 endpoint gabungan
     */
    function refreshAllGrid1Data() {
        const params = getActiveFilterParams();

        // Menggunakan 1 endpoint gabungan (sesuaikan URL route Anda, misal: /api/csr/pilar-dan-bulanan)
        fetch(`/api/csr/pilar-dan-bulanan?${params.toString()}`)
            .then(res => res.json())
            .then(response => {

                // ==========================================
                // KOTAK 1: RENDER ANGGARAN PER PILAR
                // ==========================================
                const dataAnggaran = response.anggaran_pilar || [];
                const tbody = document.getElementById('tbody-anggaran-pilar');
                tbody.innerHTML = '';

                const btnSimpan = document.getElementById('btnSimpanAnggaran');
                if (btnSimpan) btnSimpan.classList.add('hidden');

                if (dataAnggaran.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="2" class="text-center text-[10px] py-2 text-slate-400">Tidak ada data</td></tr>`;
                } else {
                    dataAnggaran.forEach(item => {
                        const formattedValue = formatRibuan(item.jumlah_anggaran || 0);
                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-1.5 text-[11px] font-bold text-slate-700">${item.nama_pilar}</td>
                                <td class="py-1.5 text-right">
                                    <input type="text" 
                                           name="anggaran[${item.nama_pilar}]" 
                                           value="${formattedValue}" 
                                           oninput="formatInputAnggaran(this)"
                                           class="input-anggaran w-32 text-right text-[11px] font-black text-slate-800 bg-slate-50 border border-gray-200 rounded px-1.5 py-0.5 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:outline-none">
                                </td>
                            </tr>
                        `;
                    });
                }

                // ==========================================
                // KOTAK 2: RENDER REALISASI PER PILAR
                // ==========================================
                const dataRealisasi = response.realisasi_pilar || [];
                const containerRealisasi = document.getElementById('containerRealisasiPilar');
                containerRealisasi.innerHTML = '';

                if (dataRealisasi.length === 0) {
                    containerRealisasi.innerHTML =
                        `<div class="text-center text-xs font-bold text-slate-400 py-4">Tidak ada data.</div>`;
                } else {
                    const maxRealisasi = Math.max(...dataRealisasi.map(item => item.total_realisasi || 0), 1);
                    dataRealisasi.forEach(item => {
                        const jumlah = parseInt(item.total_realisasi || 0);
                        const lebarBar = jumlah > 0 ? Math.round((jumlah / maxRealisasi) * 100) : 0;
                        containerRealisasi.innerHTML += `
                            <div class="w-full">
                                <div class="flex justify-between items-center mb-1 text-[11px] font-bold text-slate-700">
                                    <span>${item.nama_pilar}</span>
                                    <span class="font-black text-slate-800">${jumlah} Realisasi</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] h-2 rounded-full transition-all duration-500" style="width: ${lebarBar}%"></div>
                                </div>
                            </div>
                        `;
                    });
                }

                // ==========================================
                // KOTAK 3: UPDATE CHART KEGIATAN BULANAN
                // ==========================================
                const dataBulananMap = response.kegiatan_bulanan || {};
                const chartData = Object.values(dataBulananMap);

                if (barChartInstance) {
                    barChartInstance.data.datasets[0].data = chartData;
                    barChartInstance.update();
                }
            })
            .catch(error => {
                console.error('Gagal memuat data grid 1:', error);
            });
    }

    /**
     * FUNGSI LIVE FORMAT SAAT USER MENGETIK DI INPUT
     */
    function formatInputAnggaran(input) {
        let cursorPosition = input.selectionStart;
        let oldLength = input.value.length;

        let cleanValue = input.value.replace(/[^0-9]/g, '');
        input.value = formatRibuan(cleanValue);

        let newLength = input.value.length;
        input.setSelectionRange(cursorPosition + (newLength - oldLength), cursorPosition + (newLength - oldLength));

        tampilkanTombolSimpan();
    }

    function tampilkanTombolSimpan() {
        const btnSimpan = document.getElementById('btnSimpanAnggaran');
        if (btnSimpan) btnSimpan.classList.remove('hidden');
    }

    /**
     * FUNGSI SIMPAN DENGAN MEMBERSIHKAN TITIK
     */
    function simpanPerubahanAnggaran() {
        const params = getActiveFilterParams();
        const tahun = params.get('tahun') || '{{ $tahunTerpilih }}';

        const inputs = document.querySelectorAll('.input-anggaran');
        let dataAnggaran = {};

        inputs.forEach(input => {
            let match = input.getAttribute('name').match(/\[(.*?)\]/);
            if (match) {
                let namaPilar = match[1];
                dataAnggaran[namaPilar] = bersihTitik(input.value);
            }
        });

        fetch('/csr/simpan-anggaran-pilar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    tahun: tahun,
                    anggaran: dataAnggaran
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal menyimpan ke server');
                return res.json();
            })
            .then(data => {
                tampilkanNotifikasiCustom('Plafon anggaran pilar berhasil diperbarui!');
                refreshAllGrid1Data();
            })
            .catch(error => {
                console.error('Error:', error);
                tampilkanNotifikasiCustom('Terjadi kesalahan saat menyimpan data.', 'error');
            });
    }

    function tampilkanNotifikasiCustom(pesan, tipe = 'success') {
        const existingToast = document.getElementById('customToastNotif');
        if (existingToast) existingToast.remove();

        const warnaBg = tipe === 'success' ? 'bg-teal-600' : 'bg-rose-600';
        const toast = document.createElement('div');
        toast.id = 'customToastNotif';
        toast.className =
            `fixed bottom-5 right-5 z-50 ${warnaBg} text-white px-4 py-3 rounded-xl shadow-lg text-xs font-bold flex items-center space-x-2 transition-all transform translate-y-5 opacity-0`;

        toast.innerHTML = `<span>${pesan}</span>`;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-5', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-5', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
