<div class="bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 p-[1px] rounded-xl w-full h-full flex">
    <div class="bg-white rounded-[11px] p-2.5 shadow-xs h-full flex flex-col justify-between text-[10px] w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">

            <!-- KOLOM KIRI: Top 5 Highest Interactions -->
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center justify-between mb-1 border-b border-slate-100 pb-1">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Interaksi Tertinggi
                    </h4>
                    <span class="text-[8px] font-bold bg-emerald-50 px-1 rounded-sm text-emerald-700">Top 5</span>
                </div>

                <div class="flex-1 flex flex-col justify-start pt-0.5 divide-y divide-black/20">
                    @forelse($interaksiTertinggi as $index => $top)
                        <div
                            class="flex items-start justify-between py-1.5 px-1 hover:bg-slate-50/80 rounded transition-colors duration-150 gap-2 border-b border-black">
                            <div class="flex items-start gap-1 min-w-0 flex-1">
                                <span
                                    class="flex items-center justify-center w-3.5 h-3.5 rounded text-[8px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <span
                                    class="text-slate-900 font-medium text-[9px] leading-tight whitespace-normal break-words flex-1"
                                    title="{{ $top->topik }}">
                                    {{ $top->topik }}
                                </span>
                            </div>

                            <!-- Metrik: Atas (View, Like, Comment) | Bawah (Share, Retweet) -->
                            <div
                                class="flex flex-col gap-0.5 text-[6.5px] shrink-0 bg-slate-50 px-1.5 py-1 rounded border border-black text-slate-700 font-bold self-start mt-0.5">
                                <!-- Baris 1: View | Likes | Comments -->
                                <div class="flex items-center gap-1 justify-end">
                                    <span class="flex items-center gap-0.5" title="View">
                                        <svg class="w-2 h-2 text-amber-500 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ number_format($top->view ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Likes">
                                        <svg class="w-2 h-2 text-blue-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 00.666 1.493L8.5 19a.5.5 0 00.745-.093l1.83-2.44A2.5 2.5 0 0113.047 15.5h1.953c.966 0 1.75-.784 1.75-1.75 0-.172-.025-.34-.074-.5.49-.244.824-.746.824-1.324 0-.317-.1-.613-.272-.857a1.5 1.5 0 00.022-1.819 1.495 1.495 0 00-.472-.468c.045-.19.07-.389.07-.594 0-.966-.784-1.75-1.75-1.75h-3.414l.432-1.728a2.5 2.5 0 00-2.425-3.107H8.5a.5.5 0 00-.47.333l-2.03 6.09z" />
                                        </svg>
                                        {{ number_format($top->likes ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Comments">
                                        <svg class="w-2 h-2 text-emerald-500 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ number_format($top->comments ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <!-- Baris 2: Share | Retweet -->
                                <div class="flex items-center gap-1 justify-end border-t border-slate-200 pt-0.5">
                                    <span class="flex items-center gap-0.5" title="Share">
                                        <svg class="w-2 h-2 text-indigo-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.31l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                        {{ number_format($top->share ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Retweet">
                                        <svg class="w-2 h-2 text-sky-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M5 4a1 1 0 00-2 0v7H1.5a.5.5 0 00-.354.854l3 3a.5.5 0 00.708 0l3-3A.5.5 0 007.5 11H6V4zM15 16a1 1 0 002 0V9h1.5a.5.5 0 00.354-.854l-3-3a.5.5 0 00-.708 0l-3 3A.5.5 0 0012.5 9H14v7z" />
                                        </svg>
                                        {{ number_format($top->retweet ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="text-center py-2 flex items-center justify-center flex-1 text-slate-400 italic text-[9px]">
                            Tidak ada data interaksi
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- KOLOM KANAN: Top 5 Lowest Interactions -->
            <div
                class="flex flex-col h-full justify-between border-t md:border-t-0 md:border-l border-slate-100 pt-2 md:pt-0 md:pl-3">
                <div class="flex items-center justify-between mb-1 border-b border-slate-100 pb-1">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-rose-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                        </svg>
                        Interaksi Terendah
                    </h4>
                    <span class="text-[8px] font-bold bg-rose-50 px-1 rounded-sm text-rose-700">Top 5</span>
                </div>

                <div class="flex-1 flex flex-col justify-start pt-0.5 divide-y divide-black/20">
                    @forelse($interaksiTerendah as $index => $bottom)
                        <div
                            class="flex items-start justify-between py-1.5 px-1 hover:bg-slate-50/80 rounded transition-colors duration-150 gap-2 border-b border-black">
                            <div class="flex items-start gap-1 min-w-0 flex-1">
                                <span
                                    class="flex items-center justify-center w-3.5 h-3.5 rounded text-[8px] font-bold bg-rose-50 text-rose-700 border border-rose-100 shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <span
                                    class="text-slate-900 font-medium text-[9px] leading-tight whitespace-normal break-words flex-1"
                                    title="{{ $bottom->topik }}">
                                    {{ $bottom->topik }}
                                </span>
                            </div>

                            <!-- Metrik: Atas (View, Like, Comment) | Bawah (Share, Retweet) -->
                            <div
                                class="flex flex-col gap-0.5 text-[6.5px] shrink-0 bg-slate-50 px-1.5 py-1 rounded border border-black text-slate-700 font-bold self-start mt-0.5">
                                <!-- Baris 1: View | Likes | Comments -->
                                <div class="flex items-center gap-1 justify-end">
                                    <span class="flex items-center gap-0.5" title="View">
                                        <svg class="w-2 h-2 text-amber-500 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ number_format($bottom->view ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Likes">
                                        <svg class="w-2 h-2 text-blue-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 00.666 1.493L8.5 19a.5.5 0 00.745-.093l1.83-2.44A2.5 2.5 0 0113.047 15.5h1.953c.966 0 1.75-.784 1.75-1.75 0-.172-.025-.34-.074-.5.49-.244.824-.746.824-1.324 0-.317-.1-.613-.272-.857a1.5 1.5 0 00.022-1.819 1.495 1.495 0 00-.472-.468c.045-.19.07-.389.07-.594 0-.966-.784-1.75-1.75-1.75h-3.414l.432-1.728a2.5 2.5 0 00-2.425-3.107H8.5a.5.5 0 00-.47.333l-2.03 6.09z" />
                                        </svg>
                                        {{ number_format($bottom->likes ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Comments">
                                        <svg class="w-2 h-2 text-emerald-500 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ number_format($bottom->comments ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <!-- Baris 2: Share | Retweet -->
                                <div class="flex items-center gap-1 justify-end border-t border-slate-200 pt-0.5">
                                    <span class="flex items-center gap-0.5" title="Share">
                                        <svg class="w-2 h-2 text-indigo-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.31l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                        {{ number_format($bottom->share ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-0.5" title="Retweet">
                                        <svg class="w-2 h-2 text-sky-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M5 4a1 1 0 00-2 0v7H1.5a.5.5 0 00-.354.854l3 3a.5.5 0 00.708 0l3-3A.5.5 0 007.5 11H6V4zM15 16a1 1 0 002 0V9h1.5a.5.5 0 00.354-.854l-3-3a.5.5 0 00-.708 0l-3 3A.5.5 0 0012.5 9H14v7z" />
                                        </svg>
                                        {{ number_format($bottom->retweet ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="text-center py-2 flex items-center justify-center flex-1 text-slate-400 italic text-[9px]">
                            Tidak ada data interaksi
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
