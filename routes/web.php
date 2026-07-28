<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\RoomController;


// Public Routes
Route::get('/', [AuthController::class, 'home']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::post('/staff', [AdminController::class, 'staffStore'])->name('staff.store');
    Route::put('/staff/{staff}', [AdminController::class, 'staffUpdate'])->name('staff.update');
    Route::delete('/staff/{staff}', [AdminController::class, 'staffDestroy'])->name('staff.destroy');
    Route::get('/staff/data', [AdminController::class, 'staffData'])->name('staff.data');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/data', [RoomController::class, 'roomsData'])->name('rooms.data');
    Route::post('/rooms/batch', [RoomController::class, 'batchStore'])->name('rooms.batchStore');
    Route::post('/rooms/bulk-price', [RoomController::class, 'bulkPriceUpdate'])->name('rooms.bulkPrice');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    Route::get('/reports/data', [AdminController::class, 'reportsData'])->name('admin.reports.data');
});


Route::middleware(['auth'])->prefix('staff')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/rooms/available', [StaffController::class, 'availableRooms'])->name('staff.rooms.available');
    Route::post('/checkin', [StaffController::class, 'checkinStore'])->name('staff.checkin.store');

    Route::get('/checkout/search', [StaffController::class, 'checkoutSearch'])->name('staff.checkout.search');
    Route::post('/checkout/process', [StaffController::class, 'checkoutProcess'])->name('staff.checkout.process');

    Route::post('/reservation/store', [StaffController::class, 'reservationStore'])->name('staff.reservation.store');
    Route::get('/reservation/list/search', [StaffController::class, 'reservationSearch'])->name('staff.reservation.search');
    Route::post('/reservation/complete', [StaffController::class, 'reservationComplete'])->name('staff.reservation.complete');

    Route::get('/guest/check', [StaffController::class, 'checkGuestStatus'])->name('staff.guest.check');
    Route::post('/guest/ban', [StaffController::class, 'banGuest'])->name('staff.guest.ban');
    Route::post('/guest/unban', [StaffController::class, 'unbanGuest'])->name('staff.guest.unban');

    Route::get('/checkout/detail/{reservation}', [StaffController::class, 'checkoutShow'])->name('staff.checkout.show');
    Route::get('/reservation/detail/{reservation}', [StaffController::class, 'reservationShow'])->name('staff.reservation.show');
});