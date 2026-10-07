<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIARCOM</title>
    <!-- Gunakan Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .smooth-gradient-border {
            border: 2px solid transparent;
            background-image: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.45)),
                linear-gradient(to bottom, #27B78F, #36C29F, #4ECDB8, #31C4C7, #12B4C9);
            background-origin: border-box;
            background-clip: padding-box, border-box;
        }
    </style>
</head>

<body class="w-full min-h-screen flex flex-col justify-between relative py-1"
    style="background: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.8)), url('/img/bg.jpg') center center / cover no-repeat;">

    <!-- HEADER (Lebih ke atas dan lebih kecil) -->
    <header
        class="w-full bg-transparent px-6 md:px-16 py-2.5 z-10 flex flex-col md:flex-row items-center gap-3 md:gap-4"
        style="border-bottom: 2px solid transparent; border-image: linear-gradient(to right, #27B78F, #36C29F, #4ECDB8, #31C4C7, #12B4C9) 1;">

        <img src="/img/siarcom.png" alt="SIARCOM Logo" class="h-10 md:h-11 w-auto">

        <div class="border-t-2 md:border-t-0 md:border-l border-white/20 pt-2 md:pt-0 md:pl-4 text-center md:text-left">
            <h1 class="text-xl md:text-2xl font-extrabold text-white tracking-tight leading-none drop-shadow-md">
                SIARCOM</h1>
            <span
                class="text-[9px] md:text-[11px] text-slate-300 font-semibold tracking-wider uppercase mt-1 block drop-shadow-sm">Sistem
                Informasi Manajemen Arsip CSR dan Branch Communication</span>
        </div>
    </header>

    <!-- MAIN CONTENT (Jarak gap kiri-kanan dijauhkan dengan lg:gap-24) -->
    <main
        class="w-full max-w-[90%] lg:max-w-[82%] mx-auto px-4 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-24 items-center z-10 py-6">

        <!-- SISI KIRI: TEKS SELAMAT DATANG -->
        <div
            class="lg:col-span-6 bg-slate-950/20 backdrop-blur-sm border border-white/10 p-6 md:p-8 rounded-2xl text-white shadow-2xl self-center">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-3 tracking-tight leading-tight text-white drop-shadow-sm">
                Selamat datang di <span class="text-[#14b8a6]">SIARCOM</span>
            </h2>

            <p class="text-xs md:text-sm text-slate-200 leading-relaxed font-normal opacity-90">
                Sistem Informasi Manajemen Arsip CSR dan Branch Communication. Platform digital ini dirancang secara
                khusus untuk menyederhanakan proses pengarsipan data pada bidang CSR dan Branch Communication pada
                PT.Angkasa Pura Cabang Supadio secara terstruktur, aman, serta terintegrasi. Dengan menyajikan
                visualisasi grafik analisis data yang akurat.
            </p>
        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div
            class="lg:col-span-6 smooth-gradient-border backdrop-blur-md p-6 md:p-8 rounded-2xl shadow-2xl w-full max-w-md lg:ml-auto">
            <div class="text-center mb-5">
                <h3 class="text-lg md:text-xl font-extrabold text-white tracking-tight">Login ke Dashboard SIARCOM</h3>
                <p class="text-[11px] text-gray-300 mt-1">Masukkan akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div
                    class="mb-4 p-2.5 bg-red-500/20 border-l-4 border-red-500 rounded-lg text-[11px] font-semibold text-red-200 shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-3.5">
                @csrf

                <div>
                    <label for="username"
                        class="block text-[9px] font-extrabold text-slate-300 uppercase tracking-widest mb-1">Username:</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-2 rounded-xl border border-slate-600 bg-slate-900/60 text-xs text-white font-medium placeholder-gray-500 focus:outline-none focus:border-[#14b8a6] focus:bg-slate-900 transition duration-200"
                        placeholder="Masukkan username anda">
                </div>

                <div>
                    <label for="password"
                        class="block text-[9px] font-extrabold text-slate-300 uppercase tracking-widest mb-1">Password:</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full pl-4 pr-12 py-2 rounded-xl border border-slate-600 bg-slate-900/60 text-xs text-white font-medium placeholder-gray-500 focus:outline-none focus:border-[#14b8a6] focus:bg-slate-900 transition duration-200"
                            placeholder="Masukkan password anda">

                        <button type="button" id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-400 focus:outline-none transition duration-150">
                            <svg id="eyeOpenIcon" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeCloseIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4M9.88 9.88a3 3 0 104.24 4.24M6 20L18 4m-2 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-[#14b8a6] hover:bg-[#0d9488] active:scale-[0.99] text-white font-extrabold py-2.5 px-5 rounded-xl shadow-lg shadow-teal-500/10 transition duration-150 cursor-pointer text-center text-xs tracking-wider mt-2 uppercase">
                    Masuk
                </button>
            </form>
        </div>
    </main>

    <!-- FOOTER (3 Card Dikecilkan Namun Tetap Rapi) -->
    <footer class="w-full max-w-[90%] lg:max-w-[82%] mx-auto px-4 mb-3 z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-white items-stretch">

            <!-- CSR -->
            <div
                class="smooth-gradient-border backdrop-blur-sm py-3.5 px-4 rounded-xl shadow-2xl flex flex-col justify-between">
                <div>
                    <div class="p-1.5 rounded-lg bg-[#14b8a6]/25 text-teal-300 shadow-inner shrink-0 w-fit mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-extrabold tracking-wide text-teal-300">CSR</h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-relaxed font-normal opacity-90">Mengarsip data
                        program dana
                        bantuan dan memantau visualisasi grafik realisasi anggaran.</p>
                </div>
            </div>

            <!-- SOSMED MONITORING -->
            <div
                class="smooth-gradient-border backdrop-blur-sm py-3.5 px-4 rounded-xl shadow-2xl flex flex-col justify-between">
                <div>
                    <div class="p-1.5 rounded-lg bg-[#14b8a6]/25 text-teal-300 shadow-inner shrink-0 w-fit mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-extrabold tracking-wide text-teal-300">Sosmed Monitoring</h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-relaxed font-normal opacity-90">Mengarsip data
                        interaksi konten
                        dan memonitoring data engagementnya secara real-time.</p>
                </div>
            </div>

            <!-- MEDIA MONITORING -->
            <div
                class="smooth-gradient-border backdrop-blur-sm py-3.5 px-4 rounded-xl shadow-2xl flex flex-col justify-between">
                <div>
                    <div class="p-1.5 rounded-lg bg-[#14b8a6]/25 text-teal-300 shadow-inner shrink-0 w-fit mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 2v4a2 2 0 002 2h4"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-extrabold tracking-wide text-teal-300">Media Monitoring</h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-relaxed font-normal opacity-90">Mengarsip media
                        berita terkait
                        bandara dan menampilkan visualisasi grafik nya.</p>
                </div>
            </div>

        </div>
    </footer>

    <!-- SCRIPT UNTUK EYE PASSWORD TOGGLE -->
    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeOpenIcon = document.getElementById('eyeOpenIcon');
        const eyeCloseIcon = document.getElementById('eyeCloseIcon');

        togglePasswordButton.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpenIcon.classList.add('hidden');
                eyeCloseIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpenIcon.classList.remove('hidden');
                eyeCloseIcon.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
