<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsrController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DashboardController;

// 1. GUEST ROUTES
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/register', [AuthController::class, 'showRegisterTrial'])
    ->name('register.trial');

Route::post('/register', [AuthController::class, 'registerTrial'])
    ->name('register.trial.store');
    
});

// 2. AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {

    // Dashboard Utama & Logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========================================================
    // ROUTE MANAJEMEN CSR
    // ========================================================
    Route::prefix('csr')->group(function () {
        Route::get('/', [CsrController::class, 'index'])->name('pages.csr.index');
        Route::post('/tambah-tahun', [CsrController::class, 'tambahTahun'])->name('csr.tambah_tahun');
        Route::get('/tambah', [CsrController::class, 'create'])->name('csr.create');
        Route::post('/store', [CsrController::class, 'store'])->name('csr.store');
        Route::get('/edit/{id_csr}', [CsrController::class, 'edit'])->name('csr.edit');
        Route::put('/update/{id_csr}', [CsrController::class, 'update'])->name('csr.update');
        Route::delete('/delete/{id_csr}', [CsrController::class, 'destroy'])->name('csr.destroy');
        Route::patch('/inline-update/{id_csr}', [CsrController::class, 'inlineUpdate'])->name('pages.csr.inline-update');
        Route::post('/import-paste', [CsrController::class, 'importPaste'])->name('csr.importPaste');
        
        Route::get('/csr/export-excel', [CsrController::class, 'exportExcel'])->name('csr.export.excel');
        Route::post('/simpan-anggaran-pilar', [CsrController::class, 'simpanAnggaranPilar'])->name('csr.simpan_anggaran_pilar');
    });

    // API Endpoints CSR (Dashboard/Analytics Widgets)
    Route::prefix('api/csr')->group(function () {
        Route::match(['get', 'post'], '/pilar-dan-bulanan', [CsrController::class, 'apiKelompokPilarDanBulanan'])->name('csr.api.pilar_dan_bulanan');
    Route::match(['get', 'post'], '/ringkasan-dashboard', [CsrController::class, 'apiRingkasanDashboardGabungan'])->name('csr.api.ringkasan_dashboard');;
        Route::get('/asta-cita', [CsrController::class, 'apiAstaCita'])->name('csr.api.astacita');
        Route::get('/program-by-tahun', [CsrController::class, 'apiGetProgramByTahun'])->name('csr.api.program_by_tahun');
        Route::get('/statistik-card', [CsrController::class, 'apiStatistikCard'])->name('csr.api.statistik_card');
    });

    // ========================================================
    // ROUTE MEDIA SOSIAL MONITORING
    // ========================================================
    Route::get('/sosmed-monitoring', [PostController::class, 'index'])->name('pages.post.index');
    Route::get('/api/sosmed/grafik-bulanan', [PostController::class, 'apiGrafikBulanan'])->name('pages.post.api.grafik');
    Route::post('/sosmed-monitoring/regenerate', [PostController::class, 'regenerateMetrics'])->name('pages.post.regenerate');
    Route::post('/sosmed-monitoring/store', [PostController::class, 'store'])->name('pages.post.store');
    Route::get('/sosmed-monitoring/edit/{id}', [PostController::class, 'edit'])->name('pages.post.edit');
    Route::put('/sosmed-monitoring/update/{id}', [PostController::class, 'update'])->name('pages.post.update');
    Route::delete('/sosmed-monitoring/delete/{id}', [PostController::class, 'destroy'])->name('pages.post.destroy');
    Route::patch('/sosmed-monitoring/inline-update/{id}', [PostController::class, 'inlineUpdate'])->name('pages.post.inline-update');
    Route::get('/post/export-excel', [PostController::class, 'exportExcel'])->name('post.export');
    Route::post('/post/store-paste', [PostController::class, 'storePaste'])->name('post.store-paste');

    // ========================================================
    // ROUTE MEDIA MONITORING / BERITA
    // ========================================================
    Route::get('/branch-comm/media-monitoring', [BeritaController::class, 'index'])->name('pages.berita.index');
    Route::post('/branch-comm/media-monitoring/store', [BeritaController::class, 'store'])->name('pages.berita.store');
    Route::put('/branch-comm/media-monitoring/update/{id}', [BeritaController::class, 'update'])->name('pages.berita.update');
    Route::delete('/branch-comm/media-monitoring/delete/{id}', [BeritaController::class, 'destroy'])->name('pages.berita.destroy');
    Route::patch('/berita/{id}/inline-update', [BeritaController::class, 'inlineUpdate'])->name('berita.inline-update');
    

    // Partial Edit Modal AJAX & Legacy Berita
    Route::get('/branch-comm/media-monitoring/edit-modal/{id}', [BeritaController::class, 'editModal'])->name('pages.berita.partials.edit');
    Route::post('/branch-comm/media-monitoring/store-legacy', [BeritaController::class, 'store'])->name('berita.store');
    Route::put('/branch-comm/media-monitoring/update-legacy/{id}', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/branch-comm/media-monitoring/delete-legacy/{id}', [BeritaController::class, 'destroy'])->name('berita.destroy');

    // API & Export Berita
    Route::get('/api/media-monitoring/tone', [BeritaController::class, 'apiTone'])->name('berita.api.tone');
    Route::get('/api/media-monitoring/pemberitaan', [BeritaController::class, 'apiPemberitaan'])->name('berita.api.pemberitaan');
    Route::get('/api/media-monitoring/spokesperson', [BeritaController::class, 'apiSpokesperson'])->name('berita.api.spokesperson');
    Route::get('/api/media-monitoring/top5', [BeritaController::class, 'apiTop5'])->name('berita.api.top5');
    Route::post('/berita/tambah-tahun', [BeritaController::class, 'tambahTahun'])->name('pages.berita.tambah-tahun');
    Route::get('/berita/export-excel', [BeritaController::class, 'exportExcel'])->name('berita.export'); 
    Route::post('/berita/store-paste', [BeritaController::class, 'storePaste'])->name('berita.store-paste');

    // ========================================================
    // KHUSUS DEPT HEAD
    // ========================================================
    Route::middleware(['checkrole:Dept Head'])->group(function () {
        Route::get('/pengaturan/log-aktivitas', [ActivityLogController::class, 'index'])->name('pengaturan.log_aktivitas');
        Route::get('/pengaturan/log-aktivitas/{id}', [ActivityLogController::class, 'show'])->name('pengaturan.log_show');
        
        // CRUD Pengguna (Manajemen User)
        Route::get('/pengaturan/users', [AuthController::class, 'indexUser'])->name('users.index'); 
        Route::post('/pengaturan/users', [AuthController::class, 'storeUser'])->name('users.store');
        Route::put('/pengaturan/users/{id}', [AuthController::class, 'updateUser'])->name('users.update'); 
        Route::delete('/pengaturan/users/{id}', [AuthController::class, 'destroyUser'])->name('users.destroy');
    });
});