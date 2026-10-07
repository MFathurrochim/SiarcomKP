<!-- GRID 2 CONTAINER -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-3">

    <!-- 1. DONUT REALISASI ANGGARAN -->
    <div class="bg-white p-2.5 rounded-xl shadow-sm transition-all flex flex-col justify-between"
        style="border: 2px solid; border-image: linear-gradient(to right, #27B78F, #12B4C9) 1;">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <h3 class="text-[11px] font-bold text-black flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Realisasi Anggaran
                </h3>
                <span id="label-persen-efektifitas"
                    class="text-[9px] font-semibold px-1.5 py-0.5 bg-teal-50 text-teal-700 rounded-full border border-teal-100">
                    0% Terpakai
                </span>
            </div>

            <!-- Chart Canvas -->
            <div class="relative flex justify-center items-center h-32">
                <canvas id="chartDonutRealisasi"></canvas>
            </div>
        </div>

        <!-- Legend / Breakdown Anggaran -->
        <div class="grid grid-cols-3 gap-1 pt-2 mt-1.5 border-t border-gray-100 text-center">
            <div class="p-1 bg-gray-50 rounded-md border border-gray-300">
                <p class="text-[9px] text-black font-medium">Anggaran</p>
                <p id="txt-total-pagu" class="text-[10px] font-bold text-black mt-0.5 truncate">Rp 0</p>
            </div>
            <div class="p-1 bg-emerald-50 rounded-md border border-black">
                <p class="text-[9px] text-black font-medium">Terpakai</p>
                <p id="txt-total-terpakai" class="text-[10px] font-bold text-black mt-0.5 truncate">Rp 0</p>
            </div>
            <div class="p-1 bg-amber-50 rounded-md border border-black">
                <p class="text-[9px] text-black font-medium">Sisa</p>
                <p id="txt-total-sisa" class="text-[10px] font-bold text-black mt-0.5 truncate">Rp 0</p>
            </div>
        </div>
    </div>

    <!-- 2. TABEL RINGKASAN TRIWULAN -->
    <div class="bg-white p-2.5 rounded-xl shadow-sm transition-all flex flex-col justify-between"
        style="border: 2px solid; border-image: linear-gradient(to right, #27B78F, #12B4C9) 1;">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <h3 class="text-[11px] font-bold text-black flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Realisasi Biaya Triwulan
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[9px]">
                    <thead>
                        <tr id="thead-triwulan"
                            class="bg-gray-50 text-black uppercase tracking-wider border-b border-gray-200">
                            <th class="p-1 font-semibold">Periode</th>
                            <th class="p-1 font-semibold text-right">Memuat...</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-triwulan" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="6" class="p-2 text-center text-black">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. TABEL PROGRAM PER WILAYAH -->
    <div class="bg-white p-2.5 rounded-xl shadow-sm transition-all flex flex-col justify-between"
        style="border: 2px solid; border-image: linear-gradient(to right, #27B78F, #12B4C9) 1;">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <h3 class="text-[11px] font-bold text-black flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span id="label-title-wilayah">Realisasi per Wilayah</span>
                </h3>

                <!-- TOMBOL SWITCHER KABUPATEN / DESA -->
                <div class="flex bg-gray-100 p-0.5 rounded-md text-[8px] font-semibold">
                    <button id="btn-mode-kab" type="button" onclick="setWilayahMode('kabupaten')"
                        class="px-1.5 py-0.5 rounded bg-white text-teal-700 shadow-sm transition-all">Kabupaten</button>
                    <button id="btn-mode-desa" type="button" onclick="setWilayahMode('desa')"
                        class="px-1.5 py-0.5 rounded text-gray-600 transition-all">Desa</button>
                </div>
            </div>

            <!-- Container dengan Tinggi Maksimal & Scroll -->
            <div class="relative max-h-40 overflow-y-auto border border-gray-100 rounded-md">
                <table class="w-full text-left text-[9px] table-fixed">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-black border-b border-gray-200">
                        <tr>
                            <th id="th-wilayah-label" class="p-1 font-semibold w-[40%]">Kabupaten</th>
                            <th class="p-1 font-semibold text-right w-[10%]">Total</th>
                            <th id="th-wilayah-label-2" class="p-1 font-semibold w-[40%] border-l border-gray-200 pl-2">
                                Kabupaten</th>
                            <th class="p-1 font-semibold text-right w-[10%]">Total</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-wilayah" class="divide-y divide-gray-100">
                        <tr>
                            <td colspan="4" class="p-2 text-center text-black">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT LOGIC GRID 2 -->
<script>
    let currentWilayahMode = 'kabupaten';
    let chartDonutInstance = null;

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number || 0);
    };

    const formatCompactRupiah = (number) => {
        if (!number || number === 0) return 'Rp 0';
        const num = Number(number);
        if (num >= 1e9) {
            return 'Rp ' + (num / 1e9).toFixed(1).replace('.', ',') + ' M';
        } else if (num >= 1e6) {
            return 'Rp ' + (num / 1e6).toFixed(1).replace('.', ',') + ' jt';
        } else if (num >= 1e3) {
            return 'Rp ' + (num / 1e3).toFixed(0) + ' rb';
        }
        return 'Rp ' + num;
    };

    function getActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        const elTahun = document.getElementById('globalTahun');
        const elBulan = document.getElementById('globalBulan');
        const elPilar = document.getElementById('globalPilar');
        const elKab = document.getElementById('globalKabupaten');
        const elDesa = document.getElementById('globalDesa');
        const elProg = document.getElementById('globalProgram');
        const elStatus = document.getElementById('globalStatus');

        return {
            tahun: elTahun ? elTahun.value : (typeof tahunAktif !== 'undefined' ? tahunAktif : (urlParams.get(
                'tahun') || new Date().getFullYear())),
            bulan: elBulan ? elBulan.value : (urlParams.get('bulan') || ''),
            g_pilar: elPilar ? elPilar.value : (urlParams.get('g_pilar') || ''),
            g_kabupaten: elKab ? elKab.value : (urlParams.get('g_kabupaten') || ''),
            g_desa: elDesa ? elDesa.value : (urlParams.get('g_desa') || ''),
            g_program: elProg ? elProg.value : (urlParams.get('g_program') || ''),
            g_status: elStatus ? elStatus.value : (urlParams.get('g_status') || ''),
        };
    }

    function buildQueryString(filters, skipKeys = []) {
        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (!skipKeys.includes(key)) {
                params.append(key, filters[key] !== undefined && filters[key] !== null ? filters[key] : '');
            }
        });
        return params.toString();
    }

    /**
     * FUNGSI UTAMA GRID 2: Memuat semua data (Donut, Triwulan, Wilayah) dalam 1 Request
     */
    async function loadGrid2Data() {
        const filters = getActiveFilters();
        // Tambahkan parameter mode wilayah ke query string
        filters.mode = currentWilayahMode;

        const queryString = buildQueryString(filters);

        try {
            // Memanggil 1 endpoint gabungan sesuai route Anda: /ringkasan-dashboard atau /api/csr/ringkasan-dashboard
            const response = await fetch(`/api/csr/ringkasan-dashboard?${queryString}`);
            const res = await response.json();

            // ==========================================
            // 1. RENDER DONUT REALISASI ANGGARAN
            // ==========================================
            const dataDonut = res.donut_realisasi || {};
            document.getElementById('txt-total-pagu').textContent = formatRupiah(dataDonut.pagu);
            document.getElementById('txt-total-terpakai').textContent = formatRupiah(dataDonut.terpakai);

            const elSisaText = document.getElementById('txt-total-sisa');
            if (elSisaText) {
                elSisaText.textContent = dataDonut.is_filtered_specific ? '-' : formatRupiah(dataDonut.sisa);
            }

            document.getElementById('label-persen-efektifitas').textContent =
                `${dataDonut.efektifitas || 0}% Terpakai`;

            const ctxDonut = document.getElementById('chartDonutRealisasi').getContext('2d');
            if (chartDonutInstance) chartDonutInstance.destroy();

            let chartLabels = ['Terpakai', 'Sisa'];
            let chartDataValues = [dataDonut.terpakai || 0, dataDonut.sisa || 0];
            let chartColors = ['#27B78F', '#3B82F6'];

            if (dataDonut.is_filtered_specific || filters.bulan || filters.g_desa || filters.g_kabupaten) {
                let labelKeterangan = 'Terpakai (Filter Aktif)';
                if (filters.bulan) {
                    const elBulan = document.getElementById('globalBulan');
                    let namaBulan = elBulan && elBulan.selectedIndex > 0 ? elBulan.options[elBulan.selectedIndex]
                        .text : 'Bulan Ini';
                    labelKeterangan = `Terpakai (${namaBulan})`;
                } else if (filters.g_desa) {
                    labelKeterangan = `Terpakai (${filters.g_desa})`;
                } else if (filters.g_kabupaten) {
                    labelKeterangan = `Terpakai (${filters.g_kabupaten})`;
                }

                chartLabels = [labelKeterangan];
                chartDataValues = [dataDonut.terpakai || 0];
                chartColors = ['#27B78F'];
            }

            chartDonutInstance = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartDataValues,
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 6,
                                font: {
                                    size: 8
                                },
                                color: '#000000'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.label}: ${formatRupiah(ctx.raw)}`
                            }
                        }
                    },
                    cutout: '68%'
                }
            });

            // ==========================================
            // 2. RENDER TABEL RINGKASAN TRIWULAN
            // ==========================================
            const dataTriwulanObj = res.ringkasan_triwulan || {};
            const theadTr = document.getElementById('thead-triwulan');
            const tbodyTriwulan = document.getElementById('tbody-triwulan');

            theadTr.innerHTML = `<th class="p-1 font-semibold">Periode</th>`;
            tbodyTriwulan.innerHTML = '';

            const pilars = dataTriwulanObj.pilars || ['Sosial', 'Ekonomi', 'Lingkungan'];
            pilars.forEach(pilar => {
                theadTr.innerHTML += `<th class="p-1 font-semibold text-right">${pilar}</th>`;
            });
            theadTr.innerHTML += `<th class="p-1 font-semibold text-right">Total</th>`;

            const listTw = [{
                    key: 'TW1',
                    label: 'Triwulan 1'
                },
                {
                    key: 'TW2',
                    label: 'Triwulan 2'
                },
                {
                    key: 'TW3',
                    label: 'Triwulan 3'
                },
                {
                    key: 'TW4',
                    label: 'Triwulan 4'
                }
            ];

            const detailDataAnggaran = dataTriwulanObj.data || {};

            listTw.forEach(tw => {
                let trHtml = `<td class="p-1 font-bold text-black">${tw.label}</td>`;
                let totalPerTw = 0;

                pilars.forEach(pilar => {
                    const nominal = detailDataAnggaran[pilar] ? (detailDataAnggaran[pilar][tw
                        .key
                    ] || 0) : 0;
                    totalPerTw += Number(nominal);
                    trHtml +=
                        `<td class="p-1 text-right text-black">${formatCompactRupiah(nominal)}</td>`;
                });

                trHtml +=
                    `<td class="p-1 text-right font-bold text-black">${formatCompactRupiah(totalPerTw)}</td>`;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                tr.innerHTML = trHtml;
                tbodyTriwulan.appendChild(tr);
            });

            // ==========================================
            // 3. RENDER TABEL PROGRAM PER WILAYAH
            // ==========================================
            const titleEl = document.getElementById('label-title-wilayah');
            const thLabel1 = document.getElementById('th-wilayah-label');
            const thLabel2 = document.getElementById('th-wilayah-label-2');

            const labelName = currentWilayahMode === 'desa' ? 'Desa' : 'Kabupaten';
            if (titleEl) titleEl.textContent = `Realisasi per ${labelName}`;
            if (thLabel1) thLabel1.textContent = labelName;
            if (thLabel2) thLabel2.textContent = labelName;

            const tbodyWilayah = document.getElementById('tbody-wilayah');
            if (tbodyWilayah) {
                tbodyWilayah.innerHTML = '';
                const wilayahData = res.program_wilayah || [];

                if (wilayahData.length === 0) {
                    tbodyWilayah.innerHTML =
                        `<tr><td colspan="4" class="p-2 text-center text-black">Tidak ada data</td></tr>`;
                } else {
                    const halfLength = Math.ceil(wilayahData.length / 2);
                    const leftData = wilayahData.slice(0, halfLength);
                    const rightData = wilayahData.slice(halfLength);
                    const maxRows = Math.max(leftData.length, rightData.length);

                    for (let i = 0; i < maxRows; i++) {
                        const leftItem = leftData[i] || {};
                        const rightItem = rightData[i] || {};

                        const leftNama = leftItem.nama_wilayah || '';
                        const leftTotal = leftItem.total || 0;
                        const rightNama = rightItem.nama_wilayah || '';
                        const rightTotal = rightItem.total || 0;

                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition-colors align-top';
                        tr.innerHTML = `
                            <td class="p-1 text-black font-medium whitespace-normal break-words w-[40%]" ${!leftNama ? 'colspan="2"' : ''}>${leftNama}</td>
                            ${leftNama ? `<td class="p-1 text-right text-black font-bold w-[10%]">${leftTotal}</td>` : ''}
                            <td class="p-1 text-black font-medium whitespace-normal break-words w-[40%] border-l border-gray-200 pl-2" ${!rightNama ? 'colspan="2"' : ''}>${rightNama}</td>
                            ${rightNama ? `<td class="p-1 text-right text-black font-bold w-[10%]">${rightTotal}</td>` : ''}
                        `;
                        tbodyWilayah.appendChild(tr);
                    }
                }
            }

        } catch (error) {
            console.error('Gagal memuat data Grid 2:', error);
        }
    }

    // Switcher Button Mode Wilayah (Kabupaten / Desa)
    function setWilayahMode(mode) {
        currentWilayahMode = mode;
        const btnKab = document.getElementById('btn-mode-kab');
        const btnDesa = document.getElementById('btn-mode-desa');

        if (mode === 'kabupaten') {
            if (btnKab) btnKab.className = "px-1.5 py-0.5 rounded bg-white text-teal-700 shadow-sm transition-all";
            if (btnDesa) btnDesa.className = "px-1.5 py-0.5 rounded text-gray-600 transition-all";
        } else {
            if (btnDesa) btnDesa.className = "px-1.5 py-0.5 rounded bg-white text-teal-700 shadow-sm transition-all";
            if (btnKab) btnKab.className = "px-1.5 py-0.5 rounded text-gray-600 transition-all";
        }

        loadGrid2Data();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.currentWilayahMode === 'undefined') {
            window.currentWilayahMode = 'kabupaten';
        }

        // Panggil fungsi pemuatan data gabungan saat halaman siap
        loadGrid2Data();

        // Listener jika filter global berubah
        window.addEventListener('csr-filter-changed', loadGrid2Data);
    });
</script>
