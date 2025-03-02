<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\InvoiceController; 
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckRole;

// Authentication routes
Auth::routes();

// Basic routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Service routes
    Route::middleware(['auth', CheckRole::class . ':service'])->group(function () {
        Route::get('/requests/download-pdf', [ServiceRequestController::class, 'generatePDF'])->name('requests.download.pdf');
        Route::resource('requests', ServiceRequestController::class)->except(['show']);
    });

    // Estimator routes
    Route::middleware(['role:estimator'])->group(function () {
        Route::resource('estimations', EstimationController::class);
    });

    // Billing routes
    Route::middleware(['role:billing'])->group(function () {
        Route::resource('invoices', InvoiceController::class);
    });
});

// Rute Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute untuk permintaan sparepart, hanya bisa diakses oleh user yang login
Route::middleware('auth')->group(function () {
    Route::resource('requests', ServiceRequestController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/requests', [ServiceRequestController::class, 'index'])->name('requests.index');

Route::get('/requests/pdf', [ServiceRequestController::class, 'generatePDF'])->name('requests.pdf');
