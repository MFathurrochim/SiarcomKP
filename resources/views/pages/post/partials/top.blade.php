@php
    // Format ringkas: 850 | 1,2rb | 14,5rb | 110rb | 1,2jt
    $fmt = function ($n) {
        $n = (float) ($n ?? 0);
        if ($n >= 1000000) {
            return rtrim(rtrim(number_format(floor($n / 100000) / 10, 1, ',', '.'), '0'), ',') . 'jt';
        }
        if ($n >= 100000) {
            return number_format(floor($n / 1000), 0, ',', '.') . 'rb';
        }
        if ($n >= 1000) {
            return rtrim(rtrim(number_format(floor($n / 100) / 10, 1, ',', '.'), '0'), ',') . 'rb';
        }
        return number_format($n, 0, ',', '.');
    };
@endphp

<div class="bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 p-[1px] rounded-xl w-full h-full flex">
    <div class="bg-white rounded-[11px] p-2.5 shadow-xs h-full flex flex-col justify-between text-[10px] w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">

            <!-- KOLOM KIRI: Interaksi Tertinggi -->
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

                            <!-- Metrik: tiap kotak punya border slate-600, sentuh/hover = angka lengkap -->
                            <div
                                class="flex flex-col gap-0.5 text-[6px] shrink-0 bg-slate-50 px-1 py-1 rounded border border-black text-slate-700 font-bold self-start mt-0.5 w-[120px]">
                                <div class="grid grid-cols-3 gap-0.5">
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-black shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.04 12.32a1 1 0 0 1 0-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.57 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.57-3.01-9.96-7.18Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($top->view) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            View: {{ number_format($top->view ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-pink-500 shrink-0" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($top->likes) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Like: {{ number_format($top->likes ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-yellow-400 shrink-0" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 3C6.48 3 2 6.92 2 11.75c0 2.4 1.1 4.57 2.9 6.15-.2 1.5-.8 2.8-1.7 3.8a.5.5 0 0 0 .5.8c2-.2 3.7-1 4.9-2A11 11 0 0 0 12 20.5c5.52 0 10-3.92 10-8.75S17.52 3 12 3Z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($top->comments) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full right-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Komentar: {{ number_format($top->comments ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-0.5">
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-indigo-500 shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.31l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($top->share) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Share: {{ number_format($top->share ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-sky-500 shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                            viewBox="0 0 24 24">
                                            <path d="M17 2l4 4-4 4" />
                                            <path d="M3 11V9a3 3 0 0 1 3-3h15" />
                                            <path d="M7 22l-4-4 4-4" />
                                            <path d="M21 13v2a3 3 0 0 1-3 3H3" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($top->retweet) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full right-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Retweet: {{ number_format($top->retweet ?? 0, 0, ',', '.') }}
                                        </span>
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

            <!-- KOLOM KANAN: Interaksi Terendah -->
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

                            <!-- Metrik: tiap kotak punya border slate-600, sentuh/hover = angka lengkap -->
                            <div
                                class="flex flex-col gap-0.5 text-[6px] shrink-0 bg-slate-50 px-1 py-1 rounded border border-black text-slate-700 font-bold self-start mt-0.5 w-[120px]">
                                <div class="grid grid-cols-3 gap-0.5">
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-black shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.04 12.32a1 1 0 0 1 0-.64C3.42 7.51 7.36 4.5 12 4.5c4.64 0 8.57 3.01 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.64 19.5 12 19.5c-4.64 0-8.57-3.01-9.96-7.18Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($bottom->view) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            View: {{ number_format($bottom->view ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-pink-500 shrink-0" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($bottom->likes) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Like: {{ number_format($bottom->likes ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-yellow-400 shrink-0" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 3C6.48 3 2 6.92 2 11.75c0 2.4 1.1 4.57 2.9 6.15-.2 1.5-.8 2.8-1.7 3.8a.5.5 0 0 0 .5.8c2-.2 3.7-1 4.9-2A11 11 0 0 0 12 20.5c5.52 0 10-3.92 10-8.75S17.52 3 12 3Z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($bottom->comments) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full right-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Komentar: {{ number_format($bottom->comments ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-0.5">
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-indigo-500 shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.31l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($bottom->share) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full left-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Share: {{ number_format($bottom->share ?? 0, 0, ',', '.') }}
                                        </span>
                                    </span>
                                    <span
                                        class="relative group flex items-center gap-0.5 min-w-0 cursor-default outline-none border border-slate-600 rounded-sm px-0.5 py-0.5"
                                        tabindex="0">
                                        <svg class="w-2 h-2 text-sky-500 shrink-0" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M17 2l4 4-4 4" />
                                            <path d="M3 11V9a3 3 0 0 1 3-3h15" />
                                            <path d="M7 22l-4-4 4-4" />
                                            <path d="M21 13v2a3 3 0 0 1-3 3H3" />
                                        </svg>
                                        <span class="truncate tabular-nums">{{ $fmt($bottom->retweet) }}</span>
                                        <span
                                            class="hidden group-hover:block group-focus:block absolute bottom-full right-0 mb-0.5 z-20 whitespace-nowrap rounded bg-black px-1 py-0.5 text-[8px] font-semibold text-white">
                                            Retweet: {{ number_format($bottom->retweet ?? 0, 0, ',', '.') }}
                                        </span>
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
