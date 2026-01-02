<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HotelManagementController;
use App\Http\Controllers\Admin\RoomManagementController;
use App\Http\Controllers\Partner\PartnerAuthController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Partner\PartnerHotelController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication (with rate limiting to prevent brute force attacks)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware(['guest', 'throttle:5,1']); // 5 attempts per minute
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware(['guest', 'throttle:3,1']); // 3 registrations per minute
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Hotels
Route::get('/hotels/search', [HotelController::class, 'search'])->name('hotels.search');
Route::get('/hotels/{id}', [HotelController::class, 'show'])->name('hotels.show');

// Payment Notification (Webhook)
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

// Bookings (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');

    // Payments
    Route::get('/bookings/{bookingId}/payment', [PaymentController::class, 'show'])->name('bookings.payment');
    Route::post('/bookings/{bookingId}/payment', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/bookings/{bookingId}/payment/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin Authentication (separate from main auth)
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin login - no guest middleware, handled in controller
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
    
    // Admin logout (requires auth)
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Admin Dashboard (requires admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
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
Route::prefix('partner')->name('partner.')->group(function () {
    // Landing page - accessible to all
    Route::get('/', function () {
        return view('partner.landing');
    })->name('index');

    // Partner auth - with rate limiting
    Route::get('/register', [PartnerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [PartnerAuthController::class, 'register'])->middleware('throttle:3,1'); // 3 registrations per minute
    Route::get('/login', [PartnerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PartnerAuthController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute

    // Partner logout (requires auth)
    Route::post('/logout', [PartnerAuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Authenticated routes (hotel_owner only)
    Route::middleware(['auth', 'hotel.owner'])->group(function () {
        Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('hotels', PartnerHotelController::class);

        // Partner Room Management
        Route::get('/hotels/{hotel}/rooms/create', [App\Http\Controllers\Partner\PartnerRoomController::class, 'create'])->name('hotels.rooms.create');
        Route::post('/hotels/{hotel}/rooms', [App\Http\Controllers\Partner\PartnerRoomController::class, 'store'])->name('hotels.rooms.store');
    });
});

