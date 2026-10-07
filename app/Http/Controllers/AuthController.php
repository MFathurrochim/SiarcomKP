<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper; 
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegisterTrial()
    {
        return view('auth.register-trial');
    }

    public function registerTrial(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ],[
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal harus 6 karakter.'
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'Staff'
        ]);

        if (Auth::check()) {
            ActivityLog::create([
                'id_user' => Auth::user()->id_user,
                'nama_user' => Auth::user()->username,
                'aktivitas' => [
                    'modul' => 'USER',
                    'nama_user' => Auth::user()->username,
                    'aksi' => 'REGISTER',
                    'deskripsi' => 'Menambahkan user baru '.$user->username,
                    'data_lama' => null,
                    'data_baru' => [
                        'username' => $user->username,
                        'role' => $user->role
                    ]
                ],
                'created_at' => now()
            ]);
        }

        return redirect()
                ->route('login')
                ->with('success','Akun berhasil dibuat, silakan login.');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            ActivityLog::create([
                'id_user'   => $user->id_user, 
                'nama_user' => $user->username, 
                'aktivitas' => [
                    'modul'       => 'AUTH', 
                    'nama_user'   => $user->username, 
                    'aksi'        => 'LOGIN',
                    'deskripsi'   => 'User ' . $user->username . ' berhasil masuk ke sistem dengan role ' . $user->role,
                    'data_lama'   => null, 
                    'data_baru'   => [
                        'username' => $user->username,
                        'role'     => $user->role,
                    ]
                ],
                'created_at' => now()
            ]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang kamu masukkan salah.',
        ])->onlyInput('username');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::create([
                'id_user'   => $user->id_user, 
                'nama_user' => $user->username,
                'aktivitas' => [
                    'modul'       => 'AUTH', 
                    'nama_user'   => $user->username, 
                    'aksi'        => 'LOGOUT',
                    'deskripsi'   => 'User ' . $user->username . ' keluar dari sistem',
                    'data_lama'   => null, 
                    'data_baru'   => [
                        'username' => $user->username,
                        'role'     => $user->role,
                    ]
                ],
                'created_at' => now()
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ========================================================
    // MANAJEMEN PENGGUNA (KHUSUS DEPT HEAD - CRUD VIA MODAL)
    // ========================================================

    public function indexUser()
    {
        if (Auth::user()->role !== 'Dept Head') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all();
        return view('dashboard', compact('users'));
    }

    public function storeUser(Request $request)
    {
        if (Auth::user()->role !== 'Dept Head') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:Dept Head,Staff',
        ],[
            'username.unique' => 'Username sudah terdaftar.',
            'password.min'    => 'Password minimal harus 6 karakter.'
        ]);

        $newUser = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role']
        ]);

        ActivityLog::create([
            'id_user'   => Auth::user()->id_user,
            'nama_user' => Auth::user()->username,
            'aktivitas' => [
                'modul'     => 'USER_MANAGEMENT',
                'nama_user' => Auth::user()->username,
                'aksi'      => 'CREATE',
                'deskripsi' => 'Administrator menambahkan pengguna baru: ' . $newUser->username,
                'data_lama' => null,
                'data_baru' => [
                    'username' => $newUser->username,
                    'role'     => $newUser->role
                ]
            ],
            'created_at' => now()
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

   public function updateUser(Request $request, int $id)
{
    if (Auth::user()->role !== 'Dept Head') {
        abort(403, 'Unauthorized action.');
    }

    $targetUser = User::findOrFail($id);

    // Ubah validasi agar sesuai dengan name attribute di form (username, password, role)
    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . $id . ',id_user',
        'password' => 'nullable|string|min:6',
        'role'     => 'required|in:Dept Head,Staff',
    ], [
        'username.unique' => 'Username sudah digunakan akun lain.',
        'password.min'    => 'Password minimal harus 6 karakter.'
    ]);

    $oldData = [
        'username' => $targetUser->username,
        'role'     => $targetUser->role
    ];

    $targetUser->username = $validated['username'];
    $targetUser->role = $validated['role'];
    
    // Periksa password baru
    if (!empty($validated['password'])) {
        $targetUser->password = Hash::make($validated['password']);
    }
    
    $targetUser->save();

    ActivityLog::create([
        'id_user'   => Auth::user()->id_user,
        'nama_user' => Auth::user()->username,
        'aktivitas' => [
            'modul'     => 'USER_MANAGEMENT',
            'nama_user' => Auth::user()->username,
            'aksi'      => 'UPDATE',
            'deskripsi' => 'Administrator memperbarui pengguna: ' . $targetUser->username,
            'data_lama' => $oldData,
            'data_baru' => [
                'username' => $targetUser->username,
                'role'     => $targetUser->role
            ]
        ],
        'created_at' => now()
    ]);

    return back()->with('success', 'Pengguna berhasil diperbarui.');
}

    public function destroyUser(int $id)
    {
        if (Auth::user()->role !== 'Dept Head') {
            abort(403, 'Unauthorized action.');
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->id_user === Auth::user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $oldData = [
            'username' => $targetUser->username,
            'role'     => $targetUser->role
        ];

        $targetUser->delete();

        ActivityLog::create([
            'id_user'   => Auth::user()->id_user,
            'nama_user' => Auth::user()->username,
            'aktivitas' => [
                'modul'     => 'USER_MANAGEMENT',
                'nama_user' => Auth::user()->username,
                'aksi'      => 'DELETE',
                'deskripsi' => 'Administrator menghapus pengguna: ' . $oldData['username'],
                'data_lama' => $oldData,
                'data_baru' => null
            ],
            'created_at' => now()
        ]);

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}