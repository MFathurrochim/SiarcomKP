@if (Auth::user()->role === 'Dept Head')
    <!-- Modal Log Aktivitas -->
    <div x-show="logModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        style="display: none;">

        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col"
            @click.away="logModalOpen = false">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-slate-50 rounded-t-2xl">
                <h3 class="font-bold text-gray-800 text-base">
                    <i class="fas fa-history text-slate-600 mr-2"></i> Log Aktivitas Sistem
                </h3>
                <button @click="logModalOpen = false"
                    class="text-gray-500 hover:text-black text-xl font-bold px-2">&times;</button>
            </div>

            <div class="p-4 flex-1 overflow-y-auto min-h-[300px]">
                <template x-if="loadingList">
                    <div class="flex flex-col justify-center items-center py-20">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-slate-700"></div>
                        <p class="text-xs text-black font-bold mt-3">Tunggu Sebentar Saja...</p>
                    </div>
                </template>

                <template x-if="!loadingList">
                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-xs font-black text-black border-b border-gray-200">
                                    <th class="p-3 w-16 text-center">No</th>
                                    <th class="p-3 w-36">Waktu</th>
                                    <th class="p-3 w-36">User</th>
                                    <th class="p-3 w-24 text-center">Modul</th>
                                    <th class="p-3">Aksi / Keterangan</th>
                                    <th class="p-3 w-24 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs font-medium text-gray-800 divide-y divide-gray-200">
                                <template x-for="(log, idx) in logs" :key="log.id_log">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="p-3 text-center font-bold text-black" x-text="idx + 1"></td>
                                        <td class="p-3 text-black" x-text="log.waktu"></td>
                                        <td class="p-3 font-bold text-black" x-text="log.nama_user"></td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 rounded font-black text-[10px] text-white"
                                                :class="{
                                                    'bg-emerald-600': log.modul === 'CSR',
                                                    'bg-blue-600': log.modul === 'SOSMED',
                                                    'bg-amber-600': log.modul === 'BERITA',
                                                    'bg-indigo-600': log.modul === 'AUTH',
                                                    'bg-slate-600': log.modul !== 'CSR' && log
                                                        .modul !== 'SOSMED' &&
                                                        log.modul !== 'BERITA' && log.modul !== 'AUTH'
                                                }"
                                                x-text="log.modul"></span>
                                        </td>
                                        <td class="p-3 text-black">
                                            <span
                                                class="font-extrabold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded mr-1"
                                                x-text="log.aksi"></span>
                                            <span x-text="log.deskripsi || log.detail || '-'"></span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button @click="fetchDetailLog(log.id_log)"
                                                class="bg-slate-800 hover:bg-black text-white px-2 py-1 rounded-md text-[10px] font-black shadow-sm transition">
                                                <i class="fas fa-eye"></i> Cek
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="logs.length === 0">
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-black font-semibold italic">
                                            Tidak ditemukan rekaman aktivitas terbaru.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Detail Log -->
    <div x-show="detailModalOpen"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 overflow-y-auto"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        style="display: none;">

        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col"
            @click.away.stop="detailModalOpen = false" @click.stop>

            <div
                class="p-4 border-b border-gray-200 flex justify-between items-center bg-slate-900 text-white rounded-t-2xl">
                <h4 class="font-bold text-sm">
                    <i class="fas fa-info-circle mr-2"></i> Detail Rekaman Perubahan Data
                </h4>
                <button @click.stop="detailModalOpen = false"
                    class="text-gray-400 hover:text-white text-xl font-bold px-2">&times;</button>
            </div>

            <div class="p-5 flex-1 overflow-y-auto text-xs text-black">
                <template x-if="loadingDetail">
                    <div class="flex flex-col justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-900"></div>
                    </div>
                </template>

                <template x-if="!loadingDetail">
                    <div class="space-y-4">
                        <div
                            class="grid grid-cols-2 gap-2 p-3 bg-slate-50 border border-gray-200 rounded-xl font-semibold">
                            <div>
                                <span class="text-gray-500 font-medium">Pelaku:</span>
                                <span class="font-bold" x-text="detailLog.nama_user"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Waktu Eksekusi:</span>
                                <span x-text="detailLog.tanggal"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Modul/Fitur:</span>
                                <span class="font-black" x-text="detailLog.modul"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-medium">Jenis Aksi:</span>
                                <span class="font-bold text-slate-800" x-text="detailLog.aksi"></span>
                            </div>
                        </div>

                        <div class="mt-2 w-full">
                            <!-- Khusus UPDATE: tabel komparasi data lama vs baru -->
                            <div x-show="detailLog.aksi === 'UPDATE' || detailLog.aksi === 'Update'" class="space-y-2">
                                <span class="text-slate-800 font-black block mb-1">
                                    <i class="fas fa-exchange-alt text-teal-600 mr-1"></i> Perbandingan Detail
                                    Perubahan Kolom:
                                </span>

                                <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
                                    <table class="w-full text-left border-collapse table-fixed">
                                        <thead>
                                            <tr
                                                class="bg-slate-100 text-[11px] font-bold text-slate-700 border-b border-gray-200">
                                                <th class="p-2.5 w-1/4">Nama Field</th>
                                                <th
                                                    class="p-2.5 w-3/8 bg-rose-50/50 text-rose-900 border-l border-gray-200">
                                                    Sebelum (Data Lama)
                                                </th>
                                                <th
                                                    class="p-2.5 w-3/8 bg-emerald-50/50 text-emerald-900 border-l border-gray-200">
                                                    Sesudah (Data Baru)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 font-medium text-slate-700 text-[11px]">
                                            <template x-for="(valBaru, key) in detailLog.data_baru"
                                                :key="key">
                                                <!-- Sembunyikan field id dan timestamp internal -->
                                                <template
                                                    x-if="!key.toLowerCase().startsWith('id') && key !== 'id' && !['created_at', 'updated_at', 'deleted_at'].includes(key)">
                                                    <tr
                                                        :class="String(detailLog.data_lama?.[key]) !== String(valBaru) ?
                                                            'bg-amber-50/40' : 'opacity-60 bg-white'">
                                                        <td class="p-2.5 font-mono font-bold text-black truncate"
                                                            x-text="key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())">
                                                        </td>

                                                        <td class="p-2.5 border-l border-gray-200 max-w-0 overflow-hidden text-black"
                                                            :class="String(detailLog.data_lama?.[key]) !== String(valBaru) ?
                                                                'bg-rose-50/30 text-rose-700 font-semibold' : ''">
                                                            <span
                                                                :class="String(detailLog.data_lama?.[key]) !== String(valBaru) ?
                                                                    'line-through bg-rose-100 px-1 rounded' : ''"
                                                                x-text="typeof detailLog.data_lama?.[key] === 'object' ? JSON.stringify(detailLog.data_lama?.[key]) : (detailLog.data_lama?.[key] ?? '-')">
                                                            </span>
                                                        </td>

                                                        <td class="p-2.5 border-l border-gray-200 max-w-0 overflow-hidden text-black"
                                                            :class="String(detailLog.data_lama?.[key]) !== String(valBaru) ?
                                                                'bg-emerald-50/30 text-emerald-800 font-bold' : ''">
                                                            <span
                                                                :class="String(detailLog.data_lama?.[key]) !== String(valBaru) ?
                                                                    'bg-emerald-100 px-1 rounded' : ''"
                                                                x-text="typeof valBaru === 'object' ? JSON.stringify(valBaru) : (valBaru ?? '-')">
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Bukan UPDATE (CREATE, STORE, DELETE, dll) -->
                            <div x-show="detailLog.aksi !== 'UPDATE' && detailLog.aksi !== 'Update'"
                                class="space-y-4">
                                <div class="p-4 bg-slate-50 border border-gray-200 rounded-xl w-full">
                                    <span class="text-slate-700 font-black block mb-2">
                                        <i class="fas fa-info-circle text-slate-600 mr-1"></i> Detail Aktivitas
                                    </span>
                                    <p class="text-gray-600 leading-relaxed font-semibold"
                                        x-text="detailLog.detail || 'Tidak ada deskripsi aktivitas.'"></p>
                                </div>

                                <template
                                    x-if="(detailLog.data_baru && Object.keys(detailLog.data_baru).length > 0) || (detailLog.data_lama && Object.keys(detailLog.data_lama).length > 0)">
                                    <div class="space-y-2">
                                        <span class="text-slate-800 font-black block">
                                            <i class="fas fa-database text-indigo-600 mr-1"></i> Detail Isian Data
                                            <span
                                                x-text="detailLog.aksi === 'DELETE' || detailLog.aksi === 'Delete' ? '(Data Terhapus)' : '(Data Baru)'"></span>:
                                        </span>

                                        <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white">
                                            <table class="w-full text-left border-collapse table-fixed">
                                                <thead>
                                                    <tr
                                                        class="bg-slate-100 text-[11px] font-bold text-slate-700 border-b border-gray-200">
                                                        <th class="p-2.5 w-1/3">Nama Field</th>
                                                        <th class="p-2.5 w-2/3 border-l border-gray-200"
                                                            :class="detailLog.aksi === 'DELETE' || detailLog
                                                                .aksi === 'Delete' ?
                                                                'bg-rose-50/40 text-rose-950' :
                                                                'bg-emerald-50/40 text-emerald-950'">
                                                            Isi Field
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="divide-y divide-gray-200 font-medium text-slate-700 text-[11px]">
                                                    <template
                                                        x-for="(value, key) in (detailLog.data_baru && Object.keys(detailLog.data_baru).length > 0 ? detailLog.data_baru : detailLog.data_lama)"
                                                        :key="key">
                                                        <template
                                                            x-if="!key.toLowerCase().startsWith('id') && key !== 'id' && !['created_at', 'updated_at', 'deleted_at'].includes(key)">
                                                            <tr class="hover:bg-slate-50/50">
                                                                <td class="p-2.5 font-mono font-bold text-black truncate"
                                                                    x-text="key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())">
                                                                </td>
                                                                <td class="p-2.5 border-l border-gray-200 text-black break-all"
                                                                    :class="detailLog.aksi === 'DELETE' || detailLog
                                                                        .aksi === 'Delete' ?
                                                                        'text-rose-700 bg-rose-50/10' :
                                                                        'text-emerald-800 bg-emerald-50/10'">
                                                                    <span class="px-1.5 py-0.5 rounded font-semibold"
                                                                        :class="detailLog.aksi === 'DELETE' || detailLog
                                                                            .aksi === 'Delete' ?
                                                                            'bg-rose-50 text-rose-800' :
                                                                            'bg-emerald-50 text-emerald-800'"
                                                                        x-text="typeof value === 'object' ? JSON.stringify(value) : (value ?? '-')">
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        // --- Log aktivitas: ambil daftar log ---
        function fetchActivityLogs() {
            const alpineState = Alpine.$data(document.getElementById('appRoot'));
            if (!alpineState) return;
            alpineState.loadingList = true;

            fetch('/pengaturan/log-aktivitas', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    alpineState.logs = data;
                    alpineState.loadingList = false;
                })
                .catch(err => {
                    console.error('Gagal memuat log:', err);
                    alpineState.loadingList = false;
                });
        }

        // --- Log aktivitas: ambil detail satu log ---
        function fetchDetailLog(idLog) {
            const alpineState = Alpine.$data(document.getElementById('appRoot'));
            if (!alpineState) return;
            alpineState.detailModalOpen = true;
            alpineState.loadingDetail = true;

            fetch(`/pengaturan/log-aktivitas/${idLog}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        alpineState.detailLog = response.data;
                    }
                    alpineState.loadingDetail = false;
                })
                .catch(err => {
                    console.error('Gagal memuat detail log:', err);
                    alpineState.loadingDetail = false;
                });
        }
    </script>
@endif
