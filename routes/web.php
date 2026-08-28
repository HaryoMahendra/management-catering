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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/kartu/{nif}', [CateringController::class, 'testBarcode'])->name('kartu.show');

Route::middleware(['auth'])->group(function () {

    Route::middleware([CheckRole::class . ':super_admin,admin_catering,petugas_catering'])->group(function () {
        Route::get('/', [CateringController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [CateringController::class, 'dashboard']);
        
        Route::get('/scan', [CateringController::class, 'scanIndex'])->name('scan.index');
        Route::post('/scan/process', [CateringController::class, 'processScan'])->name('scan.process');
        
        Route::get('/cardcatering', [CateringController::class, 'cardCatering'])->name('kartu.index');
    });

    Route::middleware([CheckRole::class . ':super_admin,admin_catering'])->group(function () {
    Route::get('/booking', [CateringController::class, 'bookingIndex'])->name('booking.index');
    Route::post('/booking/import', [CateringController::class, 'importExcel'])->name('booking.import');
    Route::put('/booking/update/{id}', [CateringController::class, 'updateKaryawan'])->name('booking.update');
    
    Route::get('/whatsapp', [CateringController::class, 'whatsappIndex'])->name('whatsapp.index');
    Route::post('/whatsapp/send', [CateringController::class, 'whatsappSendBlast'])->name('whatsapp.send');
    Route::post('/whatsapp/send-single/{id}', [CateringController::class, 'whatsappSendSingle'])->name('whatsapp.send-single');
    Route::get('/laporan', [CateringController::class, 'laporanIndex'])->name('laporan.index');
});

    Route::middleware([CheckRole::class . ':super_admin'])->group(function () {
        Route::delete('/booking/delete/{id}', [CateringController::class, 'destroyKaryawan'])->name('booking.delete');

        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

});