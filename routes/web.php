<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\InvoiceController; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WorkOrderController;
use App\Http\Middleware\CheckRole;

// Add this at the top of your routes file, after the <?php line
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Add this route at the top of your routes (after middleware definitions)
Route::get('/debug-role', function () {
    dd([
        'user' => auth()->user(),
        'role' => auth()->user()->role ?? 'no role',
        'authenticated' => auth()->check()
    ]);
})->middleware('auth');

// Add this debug route
Route::get('/debug-estimation/{id}', function($id) {
    $estimation = \App\Models\Estimation::with('serviceRequest')->findOrFail($id);
    dd([
        'estimation' => $estimation,
        'has_service_request' => $estimation->serviceRequest ? true : false,
        'service_request_id' => $estimation->service_request_id
    ]);
})->middleware('auth');

// Add this more detailed debug route
Route::get('/debug-estimation-full/{id}', function($id) {
    $estimation = \App\Models\Estimation::find($id);
    
    // Get the database record directly
    $rawData = DB::table('estimations')->where('id', $id)->first();
    
    // Check if service_request_id exists in the table
    $hasColumn = Schema::hasColumn('estimations', 'service_request_id');
    
    // Check if the service request exists
    $serviceRequestExists = null;
    if ($hasColumn && $rawData && $rawData->service_request_id) {
        $serviceRequestExists = DB::table('service_requests')
            ->where('id', $rawData->service_request_id)
            ->exists();
    }
    
    // Check estimation items
    $estimationItems = \App\Models\EstimationItem::where('estimation_id', $id)->get();
    
    dd([
        'estimation' => $estimation->toArray(),
        'raw_database_record' => $rawData,
        'has_service_request_id_column' => $hasColumn,
        'service_request_exists' => $serviceRequestExists,
        'estimation_items' => $estimationItems->toArray(),
        'fillable_attributes' => $estimation->getFillable()
    ]);
})->middleware('auth');

// Protected routes
Route::group(['middleware' => 'auth'], function() {
    // Service routes
    Route::group(['prefix' => 'service'], function() {
        Route::get('/requests', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->index();
        })->name('requests.index');
        
        // Add the create route
        Route::get('/requests/create', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->create();
        })->name('requests.create');
        
        // Add the store route
        Route::post('/requests', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->store(request());
        })->name('requests.store');
        
        // Add the edit route
        Route::get('/requests/{request}/edit', function($request) {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->edit($request);
        })->name('requests.edit');
        
        // Add the update route
        Route::put('/requests/{request}', function($request) {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->update(request(), $request);
        })->name('requests.update');
        
        // Add the show route
        Route::get('/requests/{request}', function($request) {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->show(
                \App\Models\ServiceRequest::findOrFail($request)
            );
        })->name('requests.show');
        
        // Add the delete route
        Route::delete('/requests/{request}', function($request) {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->destroy(
                \App\Models\ServiceRequest::findOrFail($request)
            );
        })->name('requests.destroy');
        
        // Add this route for submitting to estimator
        Route::post('/service/requests/submit-to-estimator', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->submitToEstimator(request());
        })->name('submit.to.estimator')->middleware('auth');

        // Add this route for unfilled work orders
        Route::get('/unfilled-work-orders', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->unfilledWorkOrders();
        })->name('unfilled.work.orders')->middleware('auth');

        // Add this route for work order history
        Route::get('/service/work-orders/history', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->workOrderHistory();
        })->name('work.orders.history')->middleware('auth');

        // Add this route for resubmitting a rejected work order
        Route::post('/service/work-orders/resubmit', function() {
            if (auth()->user()->role !== 'service') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\ServiceRequestController')->resubmitWorkOrder(request());
        })->name('work.orders.resubmit')->middleware('auth');
    });
    
    // Estimator routes
    Route::group(['middleware' => 'auth', 'prefix' => 'estimator'], function() {
        Route::get('/estimations', function() {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->index();
        })->name('estimations.index');
        
        Route::get('/estimations/history', function() {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->history();
        })->name('estimations.history');
        
        Route::get('/estimations/{estimation}/edit', function($estimation) {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->edit(
                \App\Models\Estimation::findOrFail($estimation)
            );
        })->name('estimations.edit');
        
        Route::put('/estimations/{estimation}', function($estimation) {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->update(
                request(),
                \App\Models\Estimation::findOrFail($estimation)
            );
        })->name('estimations.update');
        
        Route::get('/estimations/{estimation}', function($estimation) {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->show(
                \App\Models\Estimation::findOrFail($estimation)
            );
        })->name('estimations.show');
        
        Route::post('/estimator/estimations/{estimation}/approve', function($estimation) {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->approve(
                request(),
                $estimation
            );
        })->name('estimations.approve')->middleware('auth');
        
        Route::post('/estimations/{estimation}/reject', function($estimation) {
            if (auth()->user()->role !== 'estimator') {
                abort(403, 'Unauthorized action.');
            }
            return app()->make('App\Http\Controllers\EstimationController')->reject(
                request(),
                $estimation
            );
        })->name('estimations.reject');
    });
});

Route::post('/requests/pdf', [ServiceRequestController::class, 'generatePDF'])->name('requests.generatePDF');

Route::delete('/work_orders/{workOrder}', [WorkOrderController::class, 'destroy'])->name('work_orders.destroy');

Route::resource('work_orders', WorkOrderController::class);

// Add this new route for GET requests
Route::get('/requests/pdf/{work_order_id}', [ServiceRequestController::class, 'generatePDF'])->name('requests.generatePDF.get');

// Alternative approach using controller method directly
Route::post('/estimator/estimations/{id}/approve', [App\Http\Controllers\EstimationController::class, 'approveEstimation'])
    ->name('estimations.approve.direct')
    ->middleware('auth');

// Direct route to the approve method
Route::post('/estimator/estimations/{id}/approve', function($id) {
    if (auth()->user()->role !== 'estimator') {
        abort(403, 'Unauthorized action.');
    }
    return app()->make('App\Http\Controllers\EstimationController')->approve(request(), $id);
})->middleware('auth');

