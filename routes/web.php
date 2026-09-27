<?php

use App\Http\Controllers\ParkingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [ParkingController::class, 'loginForm'])->name('login');
    Route::post('/login', [ParkingController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [ParkingController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [ParkingController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [ParkingController::class, 'transactions'])->name('transactions')->middleware('role:admin,petugas,owner');
    Route::post('/transaksi', [ParkingController::class, 'storeTransaction'])->name('transactions.store')->middleware('role:admin,petugas');
    Route::patch('/transaksi/{transaction}/checkout', [ParkingController::class, 'checkout'])->name('transactions.checkout')->middleware('role:admin,petugas');
    Route::get('/transaksi/{transaction}/struk', [ParkingController::class, 'receipt'])->name('receipt');
    Route::get('/laporan', [ParkingController::class, 'report'])->name('reports')->middleware('role:admin,owner');
    Route::get('/log-aktivitas', [ParkingController::class, 'logs'])->name('logs')->middleware('role:admin');
    Route::middleware('role:admin')->group(function (): void {
        Route::get('/pengguna', [ParkingController::class, 'users'])->name('users');
        Route::post('/pengguna', [ParkingController::class, 'storeUser'])->name('users.store');
        Route::put('/pengguna/{user}', [ParkingController::class, 'updateUser'])->name('users.update');
        Route::delete('/pengguna/{user}', [ParkingController::class, 'deleteUser'])->name('users.delete');
        Route::get('/tarif', [ParkingController::class, 'rates'])->name('rates');
        Route::post('/tarif', [ParkingController::class, 'storeRate'])->name('rates.store');
        Route::put('/tarif/{rate}', [ParkingController::class, 'updateRate'])->name('rates.update');
        Route::delete('/tarif/{rate}', [ParkingController::class, 'deleteRate'])->name('rates.delete');
        Route::get('/area', [ParkingController::class, 'areas'])->name('areas');
        Route::post('/area', [ParkingController::class, 'storeArea'])->name('areas.store');
        Route::put('/area/{area}', [ParkingController::class, 'updateArea'])->name('areas.update');
        Route::delete('/area/{area}', [ParkingController::class, 'deleteArea'])->name('areas.delete');
        Route::get('/kendaraan', [ParkingController::class, 'vehicles'])->name('vehicles');
        Route::post('/kendaraan', [ParkingController::class, 'storeVehicle'])->name('vehicles.store');
        Route::put('/kendaraan/{vehicle}', [ParkingController::class, 'updateVehicle'])->name('vehicles.update');
        Route::delete('/kendaraan/{vehicle}', [ParkingController::class, 'deleteVehicle'])->name('vehicles.delete');
    });
});
