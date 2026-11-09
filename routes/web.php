<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetupAdminController;
use Illuminate\Support\Facades\Route;

// Root: redirect sesuai status login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// === SETUP ADMIN (Tersembunyi, dilindungi token dari .env) ===
// Akses: /setup-admin?token=TOKEN_RAHASIA
// Token salah → 404 (halaman tidak ditemukan)
Route::get('/setup-admin', [SetupAdminController::class, 'showForm'])->name('setup.admin.form');
Route::post('/setup-admin', [SetupAdminController::class, 'store'])->name('setup.admin.store');

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard: redirect berdasarkan role
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('guru.dashboard');
    })->name('dashboard');

    // --- Profil (shared) ---
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- Export (shared, dilindungi rate limiter 10 request per menit) ---
    Route::get('/export', [ExportController::class, 'export'])->name('export')->middleware('throttle:10,1');

    // === ADMIN ROUTES ===
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Kelas
        Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('kelas.store');
        Route::put('/kelas/{kelas}', [AdminController::class, 'updateKelas'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [AdminController::class, 'destroyKelas'])->name('kelas.destroy');
        Route::delete('/kelas/{kelas}/siswa/{siswa}', [AdminController::class, 'destroyKelasSiswa'])->name('kelas.siswa.destroy');

        // Siswa
        Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('siswa.store');
        Route::delete('/siswa/{siswa}', [AdminController::class, 'destroySiswa'])->name('siswa.destroy');

        // Guru
        Route::post('/guru', [AdminController::class, 'storeGuru'])->name('guru.store');
        Route::delete('/guru/{id}', [AdminController::class, 'destroyGuru'])->name('guru.destroy');
        Route::post('/guru/{id}/restore', [AdminController::class, 'restoreGuru'])->name('guru.restore');
    });

    // === GURU ROUTES ===
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
        Route::post('/absensi', [GuruController::class, 'simpanAbsensi'])->name('absensi.simpan');
        Route::post('/absensi/catatan', [GuruController::class, 'simpanCatatanSingle'])->name('absensi.catatan');
    });
});
