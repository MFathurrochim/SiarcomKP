@if (Auth::user()->role === 'Dept Head')
    <div x-show="userModalOpen" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        x-effect="if (!userModalOpen) activeAction = 'list'" x-data="{
            activeAction: 'list',
            editData: { id: '', username: '', role: 'Staff' },
            deleteData: { id: '', username: '' },
            scrollToForm() {
                this.$nextTick(() => {
                    setTimeout(() => {
                        // Cara paling ampuh: Gulirkan modal body langsung ke titik paling bawah (scrollHeight)
                        if (this.$refs.modalBody) {
                            this.$refs.modalBody.scrollTo({
                                top: this.$refs.modalBody.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    }, 100);
                });
            }
        }">

        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] border border-gray-100 transform transition-all">

            <!-- Modal Header -->
            <div
                class="px-6 py-4 bg-gradient-to-r from-teal-600 to-[#12B4C9] text-white flex justify-between items-center border-b border-teal-700/30">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-white/20 rounded-lg text-white border border-white/30">
                        <i class="fas fa-users-cog text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base tracking-wide">Manajemen Pengguna Sistem</h3>
                        <p class="text-xs text-teal-100">Kelola Pengguna yang ada didalam Sistem</p>
                    </div>
                </div>
                <button type="button" @click="userModalOpen = false"
                    class="text-white/80 hover:text-white bg-black/10 hover:bg-black/20 p-2 rounded-xl transition text-sm w-9 h-9 flex items-center justify-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div x-ref="modalBody" class="p-6 overflow-y-auto space-y-6 bg-slate-50/50 max-h-[calc(90vh-120px)]">

                <!-- Notifikasi Flash Message -->
                @if (session('success'))
                    <div
                        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-xs text-sm flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-xs text-sm flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Card Tabel Daftar User -->
                <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-200/80">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 flex items-center gap-2">
                            <i class="fas fa-list text-teal-600"></i> Daftar Pengguna Sistem
                        </h4>
                        <button type="button" @click="activeAction = 'add'; scrollToForm()"
                            class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                            <i class="fas fa-plus text-[11px]"></i> Tambah Pengguna
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 text-center w-12">No</th>
                                    <th class="py-3 px-4">Username</th>
                                    <th class="py-3 px-4 text-center">Role</th>
                                    <th class="py-3 px-4 text-center w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach (\App\Models\User::all() as $index => $usr)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 text-center text-slate-500 font-medium">
                                            {{ $index + 1 }}</td>
                                        <td class="py-3 px-4 font-bold text-slate-800">{{ $usr->username }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wide {{ $usr->role === 'Dept Head' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                {{ $usr->role === 'Dept Head' ? 'Administrator' : 'Staff' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($usr->id_user !== Auth::id())
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <!-- Tombol buka form edit -->
                                                    <button type="button"
                                                        @click="activeAction = 'edit'; editData = { id: '{{ $usr->id_user }}', username: '{{ $usr->username }}', role: '{{ $usr->role }}' }; scrollToForm()"
                                                        class="bg-amber-500 hover:bg-amber-600 text-white p-1.5 rounded-lg text-xs transition shadow-xs cursor-pointer"
                                                        title="Edit Pengguna">
                                                        <i class="fas fa-edit text-[11px]"></i>
                                                    </button>
                                                    <!-- Tombol buka konfirmasi hapus -->
                                                    <button type="button"
                                                        @click="activeAction = 'delete'; deleteData = { id: '{{ $usr->id_user }}', username: '{{ $usr->username }}' }; scrollToForm()"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-lg text-xs transition shadow-xs cursor-pointer"
                                                        title="Hapus Pengguna">
                                                        <i class="fas fa-trash-alt text-[11px]"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span
                                                    class="text-[11px] text-gray-400 font-medium italic bg-gray-100 px-2 py-1 rounded-md">Akun
                                                    Anda</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ================= PANEL FORM DINAMIS ================= -->
                <div x-ref="formPanel" class="space-y-6">

                    <!-- 1. Form Tambah User -->
                    <div x-show="activeAction === 'add'" x-transition style="display: none;"
                        class="bg-white p-5 rounded-2xl shadow-xs border border-teal-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4
                                class="font-bold text-xs uppercase tracking-wider text-teal-700 flex items-center gap-2">
                                <i class="fas fa-user-plus text-teal-600"></i> Form Tambah Pengguna Baru
                            </h4>
                            <button type="button" @click="activeAction = 'list'"
                                class="text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fas fa-times"></i> Tutup
                            </button>
                        </div>
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
                                    <input type="text" name="username" placeholder="Masukkan username..."
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-teal-500 focus:ring-teal-500"
                                        value="{{ old('username') }}" required>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                                    <input type="password" name="password" placeholder="••••••••"
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-teal-500 focus:ring-teal-500"
                                        required>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Role / Hak
                                        Akses</label>
                                    <select name="role"
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-teal-500 focus:ring-teal-500"
                                        required>
                                        <option value="Staff">Staff</option>
                                        <option value="Dept Head">Administrator</option>
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-teal-600 to-[#12B4C9] hover:opacity-90 text-white text-xs font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center cursor-pointer"
                                        title="Simpan User">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2. Form Edit User -->
                    <div x-show="activeAction === 'edit'" x-transition style="display: none;"
                        class="bg-white p-5 rounded-2xl shadow-xs border border-amber-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4
                                class="font-bold text-xs uppercase tracking-wider text-amber-700 flex items-center gap-2">
                                <i class="fas fa-user-edit text-amber-600"></i> Form Edit Pengguna:
                                <span x-text="editData.username" class="underline"></span>
                            </h4>
                            <button type="button" @click="activeAction = 'list'"
                                class="text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fas fa-times"></i> Tutup
                            </button>
                        </div>

                        <form :action="window.location.origin + '/pengaturan/users/' + editData.id" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Username Baru</label>
                                    <input type="text" name="username" x-model="editData.username"
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-amber-500 focus:ring-amber-500"
                                        required>
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru
                                        <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span></label>
                                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Role / Hak Akses</label>
                                    <select name="role" x-model="editData.role"
                                        class="w-full text-xs border-gray-300 rounded-xl shadow-xs focus:border-amber-500 focus:ring-amber-500"
                                        required>
                                        <option value="Staff">Staff</option>
                                        <option value="Dept Head">Administrator</option>
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <button type="submit"
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold py-2.5 rounded-xl shadow-md transition flex items-center justify-center cursor-pointer"
                                        title="Update User">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 3. Konfirmasi Hapus User -->
                    <div x-show="activeAction === 'delete'" x-transition style="display: none;"
                        class="bg-white p-5 rounded-2xl shadow-xs border border-red-200">
                        <div class="flex justify-between items-center mb-3">
                            <h4
                                class="font-bold text-xs uppercase tracking-wider text-red-600 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-red-500"></i> Konfirmasi Penghapusan Akun
                            </h4>
                            <button type="button" @click="activeAction = 'list'"
                                class="text-slate-400 hover:text-slate-600 text-xs">
                                <i class="fas fa-times"></i> Tutup
                            </button>
                        </div>
                        <p class="text-xs text-slate-600 mb-4">
                            Apakah kamu yakin ingin menghapus pengguna
                            <strong x-text="deleteData.username" class="text-slate-800"></strong> dari sistem?
                        </p>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="activeAction = 'list'"
                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                                Tidak
                            </button>

                            <form :action="window.location.origin + '/pengaturan/users/' + deleteData.id"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-xs cursor-pointer">
                                    Ya, Yakin Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- ================= /PANEL FORM DINAMIS ================= -->
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-gray-100/80 border-t border-gray-200 flex justify-end">
                <button type="button" @click="userModalOpen = false"
                    class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold px-5 py-2 rounded-xl transition shadow-xs cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    {{-- Buka modal otomatis kalau ada error validasi atau flash message --}}
    @if ($errors->any() || session('success') || session('error'))
        <script>
            document.addEventListener('alpine:initialized', function() {
                // Cari elemen pembungkus modal x-data Anda
                const modalElement = document.querySelector('[x-data*="activeAction"]');
                if (modalElement) {
                    Alpine.$data(modalElement).userModalOpen = true;
                }
            });
        </script>
    @endif
@endif
