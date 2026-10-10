<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CateringController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\UserMasterController;
use App\Models\ActivityLog;
/*
|--------------------------------------------------------------------------
| Web Routes (Authentication & Public Routes)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/kartu/{nif}', [CateringController::class, 'testBarcode'])->name('kartu.show');

Route::middleware(['auth'])->group(function () {

    // Hak akses untuk Super Admin, Admin Catering, dan Petugas Catering
    Route::middleware([CheckRole::class . ':super_admin,admin_catering,petugas_catering'])->group(function () {
        Route::get('/', [CateringController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CateringController::class, 'dashboard']);

        Route::get('/scan', [CateringController::class, 'scanIndex'])->name('scan.index');
        Route::post('/scan/process', [CateringController::class, 'processScan'])->name('scan.process');

        Route::get('/cardcatering', [CateringController::class, 'cardCatering'])->name('kartu.index');
        
        // Menu Laporan dipindah ke sini agar petugas_catering bisa akses
        Route::get('/laporan', [CateringController::class, 'laporanIndex'])->name('laporan.index');
    });

    // Hak akses khusus Super Admin dan Admin Catering
    Route::middleware([CheckRole::class . ':super_admin,admin_catering'])->group(function () {
        Route::get('/booking', [CateringController::class, 'bookingIndex'])->name('booking.index');
        Route::post('/booking/store', [CateringController::class, 'storeKaryawan'])->name('booking.store');
        Route::post('/booking/import', [CateringController::class, 'importExcel'])->name('booking.import');
        Route::put('/booking/update/{id}', [CateringController::class, 'updateKaryawan'])->name('booking.update');

        Route::get('/whatsapp', [CateringController::class, 'whatsappIndex'])->name('whatsapp.index');
        Route::post('/whatsapp/send', [CateringController::class, 'whatsappSendBlast'])->name('whatsapp.send');
        Route::post('/whatsapp/send-single/{id}', [CateringController::class, 'whatsappSendSingle'])->name('whatsapp.send-single');
    });

    // Hak akses khusus Super Admin
    Route::middleware([CheckRole::class . ':super_admin'])->group(function () {
        Route::delete('/booking/delete/{id}', [CateringController::class, 'destroyKaryawan'])->name('booking.delete');
        Route::delete('/booking/delete-multiple', [CateringController::class, 'destroyMultipleKaryawan'])->name('booking.delete-multiple');

        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user-master', [UserMasterController::class, 'index'])->name('user.master.index');
    Route::post('/user-master', [UserMasterController::class, 'store'])->name('user.master.store');
    Route::put('/user-master/{id}', [UserMasterController::class, 'update'])->name('user.master.update');
    Route::delete('/user-master/{id}', [UserMasterController::class, 'destroy'])->name('user.master.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/activity-logs', [CateringController::class, 'activityIndex'])
        ->name('user.activity.index');
});
