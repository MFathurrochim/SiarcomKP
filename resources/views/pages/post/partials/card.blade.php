<!-- Wrapper Filter Bulan dan Top Cards Grid -->
<div class="w-full mb-4">


    <!-- Grid kontainer utama dari ujung ke ujung dengan gap lebih rapat -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 w-full">

        <!-- Card 1: Total Postingan -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Total
                    Postingan</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_postingan ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Postingan Story -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Postingan
                    Story</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_story ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Feed / Reels -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span
                    class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Feed/Reels</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_feed_reels ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Collab Content -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Collab
                    Content</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_collab ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 5: Owned Production -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Owned
                    Production</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_owned ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 6: Shared Content -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] shadow-xs">
            <div
                class="bg-white rounded-[11px] py-1.5 px-3 flex flex-col items-center justify-center text-center h-full">
                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wide leading-tight">Shared
                    Content</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 leading-none">
                    {{ number_format($cards->total_shared ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>
</div>
