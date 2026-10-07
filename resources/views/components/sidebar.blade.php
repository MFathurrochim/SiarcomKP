<!-- Container Sidebar Interaktif menggunakan Alpine.js dengan Persistensi State di LocalStorage -->
<div x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') !== null ?
        JSON.parse(localStorage.getItem('sidebarOpen')) : true
}" :class="sidebarOpen ? 'w-56' : 'w-0 -translate-x-full lg:w-14 lg:translate-x-0'"
    class="h-screen bg-gradient-to-b from-[#27B78F] via-[#4ECDB8] to-[#12B4C9] text-white flex flex-col justify-between transition-all duration-300 ease-in-out relative z-30 shadow-2xl">

    <div>
        <!-- HEADER SIDEBAR & TOMBOL TOGGLE -->
        <div class="px-3 flex items-center justify-between border-b border-white/10 h-16">
            <span x-show="sidebarOpen" x-transition class="text-xl font-black tracking-wider text-white pl-1">
                SIARCOM
            </span>
            <!-- Tombol Buka Tutup Sidebar (Memperbarui status di localStorage) -->
            <button @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)"
                class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition duration-200 focus:outline-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- PROFILE CARD -->
        <div class="px-2 py-3" x-show="sidebarOpen" x-transition>
            <div
                class="flex items-center gap-2 bg-white/10 backdrop-blur-sm p-2 rounded-xl border border-white/15 shadow-sm">
                <img src="https://www.computerhope.com/jargon/g/guest-user.png" alt="Guest User Profile"
                    class="w-8 h-8 rounded-lg object-contain bg-white/80 p-0.5 border border-white/30 shadow-inner shrink-0">
                <div class="overflow-hidden">
                    <h4 class="font-bold text-xs text-white truncate">
                        {{ auth()->user()->username ?? (auth()->user()->name ?? 'Guest') }}</h4>
                    <span
                        class="text-[8px] text-teal-100 font-extrabold tracking-wider uppercase block mt-0.5 truncate">
                        @php
                            $roleDb = auth()->user()->role ?? '';
                            $displayRole = $roleDb === 'Dept Head' ? 'Administrator' : ($roleDb ?: 'No Role');
                        @endphp
                        {{ $displayRole }}
                    </span>
                </div>
            </div>
        </div>

        <!-- MENU NAVIGASI -->
        <nav class="px-2 space-y-1 font-medium mt-1">

            <!-- Dashboard Utama -->
            <div class="relative group">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg hover:bg-white/10 transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-white/20 font-bold' : '' }}">
                    <div class="text-white shrink-0">
                        <!-- SVG Logo Diagram / Chart -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" x-transition
                        class="text-xs tracking-wide font-semibold truncate">Dashboard Utama</span>
                </a>
                <span x-show="!sidebarOpen" x-cloak
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-3 whitespace-nowrap bg-black/80 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50">
                    Dashboard Utama
                </span>
            </div>

            <!-- CSR -->
            <div class="relative group">
                <a href="{{ route('pages.csr.index') }}"
                    class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg hover:bg-white/10 transition duration-150 {{ request()->routeIs('csr.*') ? 'bg-white/20 font-bold' : '' }}">
                    <div class="text-white shrink-0">
                        <!-- SVG Logo CSR: Tangan & Hati -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Hati di atas -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.318C10.64 2.89 8.35 2.5 6.64 3.97C4.93 5.44 4.67 7.91 6.02 9.68L12 16L17.98 9.68C19.33 7.91 19.07 5.44 17.36 3.97C15.65 2.5 13.36 2.89 12 4.318Z" />
                            <!-- Telapak tangan menyangga -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 15C3 15 5 13 8 13C11 13 13 15 15 15C17 15 21 12.5 21 12.5M3 15V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V12.5" />
                        </svg>
                    </div>
                    <span x-show="sidebarOpen" x-transition class="text-xs tracking-wide font-semibold truncate">[ CSR
                        ]</span>
                </a>
                <span x-show="!sidebarOpen" x-cloak
                    class="absolute left-full top-1/2 -translate-y-1/2 ml-3 whitespace-nowrap bg-black/80 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50">
                    CSR
                </span>
            </div>

            <!-- Dropdown Branch Communication -->
            <div class="relative group" x-data="{
                dropdownOpen: localStorage.getItem('branchDropdownOpen') !== null ?
                    JSON.parse(localStorage.getItem('branchDropdownOpen')) : {{ request()->routeIs('post.*') || request()->routeIs('berita.*') ? 'true' : 'false' }}
            }">
                <button @click="dropdownOpen = !dropdownOpen; localStorage.setItem('branchDropdownOpen', dropdownOpen)"
                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg hover:bg-white/10 transition duration-150 focus:outline-none cursor-pointer {{ request()->routeIs('post.*') || request()->routeIs('berita.*') ? 'bg-white/15' : '' }}">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="text-white shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11V4.706c0-.949-.533-1.819-1.388-2.238l-.705-.347m6.586 10.52a12.906 12.906 0 01-2.315 5.23m3.033-1.92l-.084-.131M15 11V4.706c0-.949.534-1.819 1.388-2.238l.705-.347M7 9h1a1 1 0 011 1v1M16 9h-1a1 1 0 00-1 1v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <span x-show="sidebarOpen" x-transition
                            class="text-xs tracking-wide text-left font-semibold truncate">[ Branch Comm ]</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="dropdownOpen ? 'rotate-180' : ''"
                        class="w-3 h-3 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <span x-show="!sidebarOpen" x-cloak
                    class="absolute left-full top-3 ml-3 whitespace-nowrap bg-black/80 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 z-50">
                    Branch Communication
                </span>

                <!-- Sub-Menu versi TERBUKA -->
                <div x-show="dropdownOpen && sidebarOpen" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="mt-0.5 pl-7 space-y-0.5 text-[11px]">
                    <a href="{{ route('pages.post.index') }}"
                        class="block py-1.5 px-2 rounded-lg hover:text-teal-200 transition duration-150 {{ request()->routeIs('post.*') ? 'text-teal-200 font-bold underline' : 'text-white font-semibold' }}">
                        - Sosmed Monitoring
                    </a>
                    <a href="{{ route('pages.berita.index') }}"
                        class="block py-1.5 px-2 rounded-lg hover:text-teal-200 transition duration-150 {{ request()->routeIs('berita.*') ? 'text-teal-200 font-bold underline' : 'text-white font-semibold' }}">
                        - Media Monitoring
                    </a>
                </div>

                <!-- Sub-Menu versi TERTUTUP (Flyout) -->
                <div x-show="!sidebarOpen" x-cloak
                    class="absolute left-full top-0 ml-3 w-48 bg-black/90 backdrop-blur-md rounded-xl shadow-2xl border border-white/20 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                    <a href="{{ route('pages.post.index') }}"
                        class="flex items-center gap-2 px-3 py-2 text-xs font-semibold hover:bg-white/10 transition duration-150 {{ request()->routeIs('post.*') ? 'text-teal-200 font-bold' : 'text-white' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342a4 4 0 100-2.684m0 2.684a4 4 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a4 4 0 105.367-1.98 4 4 0 00-5.367 1.98zm0 9.316a4 4 0 105.367 1.98 4 4 0 00-5.367-1.98z">
                            </path>
                        </svg>
                        Sosmed Monitoring
                    </a>
                    <a href="{{ route('pages.berita.index') }}"
                        class="flex items-center gap-2 px-3 py-2 text-xs font-semibold hover:bg-white/10 transition duration-150 {{ request()->routeIs('berita.*') ? 'text-teal-200 font-bold' : 'text-white' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2zM7 8h10M7 12h10M7 16h6">
                            </path>
                        </svg>
                        Media Monitoring
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- BUTTON LOGOUT -->
    <div class="p-2 border-t border-white/10" x-show="sidebarOpen" x-transition>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full bg-cyan-700/40 hover:bg-red-600/60 text-white font-bold py-2 px-2 rounded-lg shadow-md border border-white/10 transition duration-150 flex items-center justify-center gap-1.5 cursor-pointer uppercase text-[9px] tracking-wider">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Logout
            </button>
        </form>
    </div>

</div>
