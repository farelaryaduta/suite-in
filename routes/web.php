<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HotelManagementController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Partner\PartnerAuthController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Partner\PartnerHotelController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Hotels
Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

// Bookings (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    
    // Payments
    Route::get('/bookings/{bookingId}/payment', [PaymentController::class, 'show'])->name('bookings.payment');
    Route::post('/bookings/{bookingId}/payment', [PaymentController::class, 'process'])->name('payments.process');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin & Hotel Owner Dashboard
Route::middleware(['auth', 'admin.owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Hotel Management
    Route::get('/hotels', [HotelManagementController::class, 'index'])->name('hotels.index');
    Route::get('/hotels/create', [HotelManagementController::class, 'create'])->name('hotels.create');
    Route::post('/hotels', [HotelManagementController::class, 'store'])->name('hotels.store');
    Route::get('/hotels/{hotel}/edit', [HotelManagementController::class, 'edit'])->name('hotels.edit');
    Route::put('/hotels/{hotel}', [HotelManagementController::class, 'update'])->name('hotels.update');
    Route::delete('/hotels/{hotel}', [HotelManagementController::class, 'destroy'])->name('hotels.destroy');
    
    // Room Management
    Route::get('/hotels/{hotel}/rooms', [RoomManagementController::class, 'index'])->name('rooms.index');
    Route::get('/hotels/{hotel}/rooms/create', [RoomManagementController::class, 'create'])->name('rooms.create');
    Route::post('/hotels/{hotel}/rooms', [RoomManagementController::class, 'store'])->name('rooms.store');
    Route::get('/hotels/{hotel}/rooms/{room}/edit', [RoomManagementController::class, 'edit'])->name('rooms.edit');
    Route::put('/hotels/{hotel}/rooms/{room}', [RoomManagementController::class, 'update'])->name('rooms.update');
    Route::delete('/hotels/{hotel}/rooms/{room}', [RoomManagementController::class, 'destroy'])->name('rooms.destroy');
});

// Partner Portal
Route::prefix('partner')->name('partner.')->group(function() {
    // Guest routes
    Route::middleware('guest')->group(function() {
        Route::get('/', function () {
            return view('partner.landing');
        })->name('index');
        
        Route::get('/register', [PartnerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [PartnerAuthController::class, 'register']);
        
        Route::get('/login', [PartnerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [PartnerAuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware(['auth', 'hotel.owner'])->group(function() {
        Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('hotels', PartnerHotelController::class);
    });
});

