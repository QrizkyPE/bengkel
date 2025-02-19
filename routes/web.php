<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\InvoiceController; 
use Illuminate\Support\Facades\Auth;

// Authentication routes
Auth::routes();

// Role-based routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'role:service'])->group(function () {
    Route::resource('requests', ServiceRequestController::class);
});

Route::middleware(['auth', 'role:estimator'])->group(function () {
    Route::resource('estimations', EstimationController::class);
});

Route::middleware(['auth', 'role:billing'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
});

// Basic routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk permintaan sparepart, hanya bisa diakses oleh user yang login
Route::middleware('auth')->group(function () {
    Route::resource('requests', ServiceRequestController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('requests', ServiceRequestController::class);
});

Route::get('/requests', [ServiceRequestController::class, 'index'])->name('requests.index');
