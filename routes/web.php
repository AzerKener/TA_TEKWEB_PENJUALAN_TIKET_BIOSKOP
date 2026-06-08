<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\FnbController as CustomerFnbController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\MovieController as CustomerMovieController;
use App\Http\Controllers\Customer\SeatController;
use App\Http\Controllers\Customer\TransactionController as CustomerTransactionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\FnbController as AdminFnbController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════════
//  AUTH ROUTES
// ═══════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ═══════════════════════════════════════════════════════════════
//  CUSTOMER ROUTES (PUBLIC + AUTH)
// ═══════════════════════════════════════════════════════════════
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/movies', [CustomerMovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{slug}', [CustomerMovieController::class, 'show'])->name('movies.show');
Route::post('/movies/{movie}/review', [CustomerMovieController::class, 'storeReview'])->name('movies.review')->middleware('auth');

// F&B Standalone
Route::get('/fnb', [CustomerFnbController::class, 'index'])->name('fnb.index');

// ═══════════════════════════════════════════════════════════════
//  BOOKING ROUTES (AUTH REQUIRED)
// ═══════════════════════════════════════════════════════════════
Route::middleware('auth')->prefix('booking')->name('booking.')->group(function () {
    Route::get('/schedule/{movie}',     [BookingController::class, 'schedule'])->name('schedule');
    Route::get('/seats/{schedule}',     [BookingController::class, 'seats'])->name('seats');
    Route::get('/fnb/{schedule}',       [BookingController::class, 'fnb'])->name('fnb');
    Route::get('/checkout',             [BookingController::class, 'checkout'])->name('checkout');
    Route::post('/process',             [BookingController::class, 'process'])->name('process');
    Route::get('/success/{transaction}',[BookingController::class, 'success'])->name('success');
});

// Seat lock API (Auth Required)
Route::middleware('auth')->prefix('api/seats')->name('seats.')->group(function () {
    Route::post('/lock',    [SeatController::class, 'lock'])->name('lock');
    Route::post('/unlock',  [SeatController::class, 'unlock'])->name('unlock');
    Route::get('/status/{schedule}', [SeatController::class, 'status'])->name('status');
});

// ═══════════════════════════════════════════════════════════════
//  MY ACCOUNT (AUTH REQUIRED)
// ═══════════════════════════════════════════════════════════════
Route::middleware('auth')->prefix('my')->name('my.')->group(function () {
    Route::get('/transactions',               [CustomerTransactionController::class, 'index'])->name('transactions');
    Route::get('/transactions/{transaction}', [CustomerTransactionController::class, 'show'])->name('transactions.show');
    Route::get('/e-ticket/{code}',            [CustomerTransactionController::class, 'eTicket'])->name('eticket');
});

// ═══════════════════════════════════════════════════════════════
//  ADMIN ROUTES
// ═══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Movies
    Route::resource('movies', AdminMovieController::class);

    // Studios
    Route::resource('studios', StudioController::class);
    Route::get('/studios/{studio}/seats', [StudioController::class, 'seats'])->name('studios.seats');

    // Schedules
    Route::resource('schedules', ScheduleController::class);
    Route::get('/schedules/check-conflict', [ScheduleController::class, 'checkConflict'])->name('schedules.conflict');

    // F&B
    Route::resource('fnb', AdminFnbController::class);

    // Transactions
    Route::get('/transactions',              [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}',[AdminTransactionController::class, 'show'])->name('transactions.show');
    Route::patch('/transactions/{transaction}/status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});
