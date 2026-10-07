<!-- Wrapper Filter Bulan dan Top Cards Grid (Card Berita) -->
<div class="w-full mb-4">
    <!-- Grid kontainer utama: 2 kolom di mobile, 3 di tablet, hingga 7 kolom di layar besar agar rapi -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 w-full">

        <!-- Card 1: Total Berita -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Total
                    Berita</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($totalBerita ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Internal -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Internal</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahInternal ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Eksternal -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Eksternal</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahEksternal ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Operasi -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Operasi</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahOperasi ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 5: Inovasi -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Inovasi</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahInovasi ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 6: Apresiasi -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Apresiasi</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahApresiasi ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 7: CSR -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div class="bg-white rounded-[11px] py-2 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">CSR</span>
                <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($jumlahCsr ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>
</div>
