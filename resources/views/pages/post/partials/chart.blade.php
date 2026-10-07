<!-- Wrapper Border Gradien Tebal 1px -->
<div class="bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 p-[1px] rounded-xl w-full h-full flex shrink-0">
    <div class="bg-white rounded-[11px] p-3 shadow-xs w-full h-full flex flex-col justify-between">
        <div class="flex items-start justify-between mb-2 gap-2">
            <div class="min-w-0">
                <h3 class="text-xs font-bold text-black truncate">Grafik Performa Bulanan</h3>
                <p class="text-[10px] text-black truncate">Total produksi postingan per bulan</p>
            </div>
        </div>

        <!-- Main Canvas (Tinggi diperbesar menjadi 260px) -->
        <div class="relative w-full h-[260px]">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>

<!-- Load Chart.js & ChartDataLabels Plugin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxMonthly = document.getElementById('monthlyTrendChart').getContext('2d');

        const initialMonthlyData = @json($trenPostinganBulanan);

        const monthlyChart = new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                    'Des'
                ],
                datasets: [{
                    label: 'Total Postingan',
                    data: initialMonthlyData,
                    backgroundColor: '#12B4C9',
                    borderColor: '#27B78F',
                    borderWidth: 1,
                    borderRadius: 3,
                    barThickness: 13, // Diperbesar agar batangnya lebih proporsional memenuhi ruang vertikal
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'start',
                        color: '#ffffff',
                        font: {
                            size: 9,
                            weight: 'bold'
                        },
                        formatter: function(value) {
                            return value > 0 ? value : '';
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: 20,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 8
                            },
                            color: '#000000',
                            stepSize: 2,
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            },
                            color: '#000000',
                            autoSkip: false
                        }
                    }
                }
            }
        });
    });
</script>
