<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CateringController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| Web Routes (Authentication & Public Routes)
|--------------------------------------------------------------------------
*/

// Route Auth / Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Hanya Bisa Diakses Setelah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 1. DASHBOARD & SCAN QR: BISA DIAKSES OLEH SEMUA ROLE (Super Admin, Admin Catering, Petugas)
    Route::middleware([CheckRole::class . ':super_admin,admin_catering,petugas_catering'])->group(function () {
        Route::get('/', [CateringController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CateringController::class, 'dashboard']);
        
        // Scan QR
        Route::get('/scan', [CateringController::class, 'scanIndex'])->name('scan.index');
        Route::post('/scan/process', [CateringController::class, 'processScan'])->name('scan.process');
        
        // Print / Test Barcode
        Route::get('/test-barcode', [CateringController::class, 'testBarcode'])->name('barcode.test');
    });

    // 2. VIEW DATA, EDIT & UPLOAD: DIAKSES OLEH SUPER ADMIN & ADMIN CATERING
    Route::middleware([CheckRole::class . ':super_admin,admin_catering'])->group(function () {
        Route::get('/booking', [CateringController::class, 'bookingIndex'])->name('booking.index');
        Route::post('/booking/import', [CateringController::class, 'importExcel'])->name('booking.import');
        Route::put('/booking/update/{id}', [CateringController::class, 'updateKaryawan'])->name('booking.update');
        
        Route::get('/whatsapp', [CateringController::class, 'whatsappIndex'])->name('whatsapp.index');
        Route::post('/whatsapp/send', [CateringController::class, 'whatsappSendBlast'])->name('whatsapp.send');
        Route::get('/laporan', [CateringController::class, 'laporanIndex'])->name('laporan.index');
    });

    // 3. KHUSUS SUPER ADMIN ONLY (Aksi Hapus & Manajemen User/Role)
    Route::middleware([CheckRole::class . ':super_admin'])->group(function () {
        Route::delete('/booking/delete/{id}', [CateringController::class, 'destroyKaryawan'])->name('booking.delete');

        // CRUD Menu User
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        // Menu Role
        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    });

    // 4. PROFILE USER
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

});