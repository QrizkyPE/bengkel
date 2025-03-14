<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\InvoiceController; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WorkOrderController;
use App\Http\Middleware\CheckRole;

// Authentication routes - keep these at the top
Auth::routes(['register' => false]); // Disable registration if not needed

// Basic routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Debug route
Route::get('/debug', function() {
    dd([
        'logged_in' => Auth::check(),
        'user' => Auth::user(),
        'session' => session()->all()
    ]);
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Service routes
    Route::group(['middleware' => CheckRole::class . ':service', 'prefix' => 'service'], function() {
        // Work Orders
        Route::controller(WorkOrderController::class)->group(function () {
            Route::get('/work_orders', 'index')->name('work_orders.index');
            Route::get('/work_orders/create', 'create')->name('work_orders.create');
            Route::post('/work_orders', 'store')->name('work_orders.store');
            Route::get('/work_orders/{workOrder}/edit', 'edit')->name('work_orders.edit');
            Route::put('/work_orders/{workOrder}', 'update')->name('work_orders.update');
            Route::delete('/work_orders/{workOrder}', 'destroy')->name('work_orders.destroy');
        });

        // Service Requests
        Route::controller(ServiceRequestController::class)->group(function () {
            Route::get('/requests', 'index')->name('requests.index');
            Route::get('/requests/create', 'create')->name('requests.create');
            Route::post('/requests', 'store')->name('requests.store');
            Route::get('/requests/{request}/edit', 'edit')->name('requests.edit');
            Route::put('/requests/{request}', 'update')->name('requests.update');
            Route::delete('/requests/{request}', 'destroy')->name('requests.destroy');
            Route::get('/requests/download-pdf', 'generatePDF')->name('requests.download.pdf');
        });
    });

    // Admin routes
    Route::group(['middleware' => CheckRole::class . ':admin', 'prefix' => 'admin'], function() {
        Route::resource('users', UserController::class);
    });

    // Estimator routes
    Route::group(['middleware' => CheckRole::class . ':estimator', 'prefix' => 'estimator'], function() {
        Route::resource('estimations', EstimationController::class);
    });

    // Billing routes
    Route::group(['middleware' => CheckRole::class . ':billing', 'prefix' => 'billing'], function() {
        Route::resource('invoices', InvoiceController::class);
    });
});

// Rute Login & Logout
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// // Rute untuk permintaan sparepart, hanya bisa diakses oleh user yang login
// Route::middleware('auth')->group(function () {
//     Route::resource('requests', ServiceRequestController::class);
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// });

// Route::get('/requests', [ServiceRequestController::class, 'index'])->name('requests.index');

// Route::get('/requests/pdf', [ServiceRequestController::class, 'generatePDF'])->name('requests.pdf');

Route::get('/debug', function() {
    dd([
        'logged_in' => Auth::check(),
        'user' => Auth::user(),
        'session' => session()->all()
    ]);
});

Route::post('/requests/pdf', [ServiceRequestController::class, 'generatePDF'])->name('requests.generatePDF');

Route::delete('/work_orders/{workOrder}', [WorkOrderController::class, 'destroy'])->name('work_orders.destroy');

Route::resource('work_orders', WorkOrderController::class);

// Add this new route for GET requests
Route::get('/requests/pdf/{work_order_id}', [ServiceRequestController::class, 'generatePDF'])->name('requests.generatePDF.get');

