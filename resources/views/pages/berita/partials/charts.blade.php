<!-- Pustaka html2canvas untuk fitur unduh gambar -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.min.js"></script>

<div x-data="beritaChartsManager()" x-init="initCharts()" class="w-full space-y-4">

    <!-- Container Utama Dashboard -->
    <div id="dashboard-section"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 w-full bg-slate-50 p-4 rounded-xl">

        <!-- 1. TONE PEMBERITAAN -->
        <div id="card-tone"
            class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white p-5 rounded-[11px] flex flex-col justify-between h-[280px]">
                <div class="flex items-center justify-between mb-1 shrink-0">
                    <h3 class="text-xs font-bold text-slate-700 tracking-wider uppercase">Tone Pemberitaan</h3>
                    <div class="flex items-center gap-1.5">
                        <span
                            class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100">Sentimen
                            Berita</span>
                        <button @click.stop="downloadCardAsImage('card-tone', 'chart-tone-pemberitaan.png')"
                            title="Unduh Kartu Ini"
                            class="p-1 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-md border border-teal-100 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative flex-1 w-full my-auto max-h-[190px] min-h-0 flex items-center justify-center pt-2">
                    <canvas id="chartToneCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. JUMLAH PEMBERITAAN -->
        <div id="card-pemberitaan"
            class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white p-3 rounded-[11px] flex flex-col justify-between h-[280px]">

                <!-- Header Card & Tombol Download -->
                <div class="flex items-center justify-between mb-1 shrink-0">
                    <h3 class="text-xs font-bold text-slate-700 tracking-wider uppercase leading-tight truncate">
                        Jumlah Pemberitaan</h3>

                    <div class="flex items-center gap-1.5">
                        <button @click.stop="downloadCardAsImage('card-pemberitaan', 'jumlah-pemberitaan.png')"
                            title="Unduh Kartu Ini"
                            class="p-1 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-md border border-teal-100 transition flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Filter Bulan Kiri & Kanan -->
                <div
                    class="grid grid-cols-2 gap-1 bg-slate-50 p-1 rounded-md border border-slate-100 text-[9px] shrink-0 mt-1">
                    <div>
                        <label class="block text-[8px] font-bold text-slate-500 leading-none mb-0.5">Bulan Kiri:</label>
                        <select x-model="bulanKiri" @change="fetchPemberitaan()"
                            class="w-full bg-white border border-slate-200 rounded px-1 py-0 leading-tight font-bold text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500">
                            <template x-for="(nama, idx) in listBulanNames" :key="idx">
                                <option :value="idx" x-text="nama"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[8px] font-bold text-slate-500 leading-none mb-0.5">Bulan
                            Kanan:</label>
                        <select x-model="bulanKanan" @change="fetchPemberitaan()"
                            class="w-full bg-white border border-slate-200 rounded px-1 py-0 leading-tight font-bold text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500">
                            <template x-for="(nama, idx) in listBulanNames" :key="idx">
                                <option :value="idx" x-text="nama"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Canvas Stacked Chart & Badge Persentase di dalam area diagram -->
                <div
                    class="relative flex-1 w-full my-auto max-h-[130px] min-h-[110px] flex items-center justify-center pt-2">
                    <canvas id="chartPemberitaanCanvas"></canvas>

                    <!-- Badge Persentase Naik/Turun dikecilkan dan diposisikan di dalam pojok kanan atas diagram -->
                    <div class="absolute top-2 right-1 flex items-center gap-0.5 bg-slate-50/90 backdrop-blur-xs px-1.5 py-0.5 rounded-full border border-slate-200 shadow-2xs z-10"
                        x-show="pemberitaanResult.perbandingan">
                        <div class="w-3 h-3 rounded-full flex items-center justify-center shrink-0"
                            :class="{
                                'bg-emerald-500': pemberitaanResult.perbandingan?.status === 'up',
                                'bg-sky-400': pemberitaanResult.perbandingan?.status === 'down',
                                'bg-slate-300': pemberitaanResult.perbandingan?.status === 'equal'
                            }">
                            <svg x-show="pemberitaanResult.perbandingan?.status === 'up'"
                                xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <svg x-show="pemberitaanResult.perbandingan?.status !== 'up'"
                                xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-black text-slate-800"
                            x-text="`${pemberitaanResult.perbandingan?.persen || 0}%`"></span>
                    </div>
                </div>

                <!-- Legend Pill INT / EXT -->
                <div class="flex items-center justify-center gap-1 shrink-0 mt-1">
                    <span
                        class="px-1.5 py-0.5 rounded-full border border-slate-300 text-[8px] font-bold text-slate-600 bg-white">
                        Sifat Berita
                    </span>
                    <span class="px-1.5 py-0.5 rounded-full text-[8px] font-black text-lime-900 bg-[#C4FD73]">
                        EXT
                    </span>
                    <span class="px-1.5 py-0.5 rounded-full text-[8px] font-black text-white bg-[#2E8B99]">
                        INT
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. SPOKESPERSON -->
        <div id="card-spokesperson"
            class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white p-4 rounded-[11px] flex flex-col justify-between h-[280px]">
                <div class="flex items-center justify-between mb-1 shrink-0">
                    <h3 class="text-xs font-bold text-slate-700 tracking-wider uppercase">Spokesperson</h3>
                    <div class="flex items-center gap-1.5">
                        <button @click.stop="downloadCardAsImage('card-spokesperson', 'top-spokesperson.png')"
                            title="Unduh Kartu Ini"
                            class="p-1 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-md border border-teal-100 transition flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div
                    class="relative flex-1 w-full my-auto max-h-[220px] min-h-0 flex items-center justify-center pt-1">
                    <canvas id="chartSpokespersonCanvas"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. TOP 5 MEDIA & JURNALIS -->
        <div id="card-top5"
            class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white p-4 rounded-[11px] flex flex-col justify-between h-[280px]">
                <div class="flex items-center justify-between mb-1 shrink-0">
                    <h3 class="text-xs font-bold text-slate-700 tracking-wider uppercase">Top 5 Media & Jurnalis</h3>
                    <button @click.stop="downloadCardAsImage('card-top5', 'top5-media-jurnalis.png')"
                        title="Unduh Kartu Ini"
                        class="p-1 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-md border border-teal-100 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                </div>
                <div
                    class="grid grid-cols-2 gap-3 text-xs flex-1 items-start pt-1 overflow-y-auto pr-1 berita-scrollbar">
                    <div class="border-r border-slate-100 pr-2 h-full min-w-0">
                        <div class="font-extrabold text-[10px] text-emerald-600 uppercase mb-2 tracking-wider">Media
                        </div>
                        <ol class="space-y-2 font-medium text-slate-700">
                            <template x-for="i in 5" :key="i">
                                <li class="flex items-start gap-1.5 min-w-0">
                                    <span class="font-bold text-slate-400 text-[10px] shrink-0 mt-0.5"
                                        x-text="`${i}.`"></span>
                                    <span class="break-words text-[10px] leading-tight min-w-0 flex-1"
                                        x-text="dataTop5.media && dataTop5.media[i-1] ? dataTop5.media[i-1].nama_media : '-'"></span>
                                </li>
                            </template>
                        </ol>
                    </div>
                    <div class="pl-1 h-full min-w-0">
                        <div class="font-extrabold text-[10px] text-emerald-600 uppercase mb-2 tracking-wider">Jurnalis
                        </div>
                        <ol class="space-y-2 font-medium text-slate-700">
                            <template x-for="i in 5" :key="i">
                                <li class="flex items-start gap-1.5 min-w-0">
                                    <span class="font-bold text-slate-400 text-[10px] shrink-0 mt-0.5"
                                        x-text="`${i}.`"></span>
                                    <span class="break-words text-[10px] leading-tight min-w-0 flex-1"
                                        x-text="dataTop5.jurnalis && dataTop5.jurnalis[i-1] ? (dataTop5.jurnalis[i-1].reporter || 'Anonim') : '-'"></span>
                                </li>
                            </template>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Plugin ChartJS Datalabels -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    function beritaChartsManager() {
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth(); // 0-11 (Januari = 0)
        const previousMonth = currentMonth === 0 ? 11 : currentMonth - 1;

        // Cek apakah ada parameter URL untuk override bulan kiri/kanan jika diperlukan
        const urlParams = new URLSearchParams(window.location.search);

        // Ambil dari URL jika ada, jika tidak gunakan previousMonth & currentMonth yang dinamis
        const initialBulanKiri = urlParams.has('bulan_kiri') ? parseInt(urlParams.get('bulan_kiri')) - 1 :
        previousMonth;
        const initialBulanKanan = urlParams.has('bulan_kanan') ? parseInt(urlParams.get('bulan_kanan')) - 1 :
            currentMonth;

        return {
            bulanKiri: initialBulanKiri,
            bulanKanan: initialBulanKanan,

            getGlobalFilters() {
                const urlParams = new URLSearchParams(window.location.search);
                return new URLSearchParams({
                    tahun: urlParams.get('tahun') || '{{ $tahunTerpilih ?? date('Y') }}',
                    bulan: urlParams.get('bulan') || 'all',
                    tone: urlParams.get('tone') || 'all',
                    topik: urlParams.get('topik') || 'all',
                    sifat_berita: urlParams.get('sifat_berita') || 'all',
                    bulan_kiri: parseInt(this.bulanKiri) + 1,
                    bulan_kanan: parseInt(this.bulanKanan) + 1
                });
            },
            namaBulanList: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ],
            listBulanNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ],
            dataTone: {
                Positif: 0,
                Netral: 0,
                Negatif: 0
            },
            listSpokesperson: [],
            dataTop5: {
                media: [],
                jurnalis: []
            },
            pemberitaanResult: {
                bulan_kiri: {
                    nama: '',
                    internal: 0,
                    eksternal: 0
                },
                bulan_kanan: {
                    nama: '',
                    internal: 0,
                    eksternal: 0
                },
                perbandingan: {
                    status: 'equal',
                    persen: 0
                }
            },
            chartToneInstance: null,
            chartPemberitaanInstance: null,
            chartSpokespersonInstance: null,

            initCharts() {
                if (window.ChartDataLabels && window.Chart) {
                    Chart.register(ChartDataLabels);
                }
                this.fetchTone();
                this.fetchPemberitaan();
                this.fetchSpokesperson();
                this.fetchTop5();
            },

            async fetchTone() {
                try {
                    const res = await fetch(`{{ route('berita.api.tone') }}?${this.getGlobalFilters().toString()}`);
                    this.dataTone = await res.json() || {
                        Positif: 0,
                        Netral: 0,
                        Negatif: 0
                    };
                    this.renderChartTone();
                } catch (e) {
                    this.renderChartTone();
                }
            },

            renderChartTone() {
                this.$nextTick(() => {
                    const canvas = document.getElementById('chartToneCanvas');
                    if (!canvas) return;
                    if (this.chartToneInstance) this.chartToneInstance.destroy();

                    this.chartToneInstance = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['POS', 'NET', 'NEG'],
                            datasets: [{
                                data: [this.dataTone.Positif || 0, this.dataTone.Netral || 0,
                                    this.dataTone.Negatif || 0
                                ],
                                backgroundColor: ['#A3E635', '#0284C7', '#FB7185'],
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.7
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    right: 25
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'right',
                                    color: '#334155',
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    },
                                    formatter: (value) => value
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grace: '20%',
                                    grid: {
                                        color: '#F1F5F9'
                                    },
                                    ticks: {
                                        precision: 0,
                                        font: {
                                            size: 9
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 10,
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            },

            async fetchPemberitaan() {
                try {
                    const res = await fetch(
                        `{{ route('berita.api.pemberitaan') }}?${this.getGlobalFilters().toString()}`);
                    const result = await res.json();
                    this.pemberitaanResult = result;
                    this.renderChartPemberitaan(result);
                } catch (e) {
                    this.renderChartPemberitaan({
                        bulan_kiri: {
                            nama: this.listBulanNames[this.bulanKiri],
                            internal: 0,
                            eksternal: 0
                        },
                        bulan_kanan: {
                            nama: this.listBulanNames[this.bulanKanan],
                            internal: 0,
                            eksternal: 0
                        },
                        perbandingan: {
                            status: 'equal',
                            persen: 0
                        }
                    });
                }
            },

            renderChartPemberitaan(response) {
                const bulanKiriName = (response.bulan_kiri?.nama || this.listBulanNames[this.bulanKiri]).toUpperCase();
                const bulanKananName = (response.bulan_kanan?.nama || this.listBulanNames[this.bulanKanan])
                    .toUpperCase();

                const labels = [bulanKiriName, bulanKananName];

                const internalData = [
                    response.bulan_kiri?.internal || 0,
                    response.bulan_kanan?.internal || 0
                ];
                const eksternalData = [
                    response.bulan_kiri?.eksternal || 0,
                    response.bulan_kanan?.eksternal || 0
                ];

                this.$nextTick(() => {
                    const canvas = document.getElementById('chartPemberitaanCanvas');
                    if (!canvas) return;
                    if (this.chartPemberitaanInstance) this.chartPemberitaanInstance.destroy();

                    this.chartPemberitaanInstance = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Internal',
                                    data: internalData,
                                    backgroundColor: '#2E8B99',
                                    borderRadius: 4,
                                    barPercentage: 0.55,
                                    categoryPercentage: 0.6
                                },
                                {
                                    label: 'Eksternal',
                                    data: eksternalData,
                                    backgroundColor: '#C4FD73',
                                    borderRadius: 4,
                                    barPercentage: 0.55,
                                    categoryPercentage: 0.6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    top: 10
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                datalabels: {
                                    color: (context) => context.datasetIndex === 1 ? '#1E293B' :
                                        '#FFFFFF',
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    },
                                    formatter: (value) => value > 0 ? value : ''
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 10,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    grace: '20%',
                                    grid: {
                                        color: '#F1F5F9'
                                    },
                                    ticks: {
                                        precision: 0,
                                        font: {
                                            size: 9
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            },

            async fetchSpokesperson() {
                try {
                    const res = await fetch(
                        `{{ route('berita.api.spokesperson') }}?${this.getGlobalFilters().toString()}`);
                    this.listSpokesperson = await res.json() || [];
                    this.renderChartSpokesperson();
                } catch (e) {
                    this.listSpokesperson = [];
                    this.renderChartSpokesperson();
                }
            },

            renderChartSpokesperson() {
                this.$nextTick(() => {
                    const canvas = document.getElementById('chartSpokespersonCanvas');
                    if (!canvas) return;
                    if (this.chartSpokespersonInstance) this.chartSpokespersonInstance.destroy();

                    const labels = this.listSpokesperson.map(item => {
                        let name = item.spokeperson;
                        if (name && name.toLowerCase() === 'maya damayanti') {
                            return 'GM - Maya Damayanti';
                        }
                        return name;
                    });

                    const dataValues = this.listSpokesperson.map(item => item.total);
                    let fontSize = this.listSpokesperson.length > 5 ? 8 : 9;

                    this.chartSpokespersonInstance = new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: dataValues,
                                backgroundColor: '#12B4C9',
                                borderRadius: 3,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    right: 25
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'right',
                                    color: '#334155',
                                    font: {
                                        weight: 'bold',
                                        size: fontSize
                                    },
                                    formatter: (value) => value
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grace: '20%',
                                    grid: {
                                        color: '#F1F5F9'
                                    },
                                    ticks: {
                                        precision: 0,
                                        font: {
                                            size: 9
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 9,
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            },

            async fetchTop5() {
                try {
                    const res = await fetch(`{{ route('berita.api.top5') }}?${this.getGlobalFilters().toString()}`);
                    this.dataTop5 = await res.json() || {
                        media: [],
                        jurnalis: []
                    };
                } catch (e) {
                    this.dataTop5 = {
                        media: [],
                        jurnalis: []
                    };
                }
            },

            async downloadCardAsImage(cardId, filename) {
                const element = document.getElementById(cardId);
                if (!element) return;
                try {
                    const canvas = await html2canvas(element, {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff'
                    });
                    const image = canvas.toDataURL('image/png');
                    const a = document.createElement('a');
                    a.href = image;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } catch (err) {
                    console.error('Gagal mengunduh gambar:', err);
                }
            }
        }
    }
</script>
