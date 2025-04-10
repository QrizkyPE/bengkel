<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\ServiceRequest;
use App\Models\Estimation;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Count totals for dashboard
        $workOrdersCount = WorkOrder::count();
        $pendingEstimationsCount = Estimation::where('status', 'pending')->count();
        $approvedEstimationsCount = Estimation::where('status', 'approved')->count();
        $invoicesCount = Invoice::count();
        $usersCount = User::count();
        
        return view('admin.dashboard', compact(
            'workOrdersCount', 
            'pendingEstimationsCount', 
            'approvedEstimationsCount',
            'invoicesCount',
            'usersCount'
        ));
    }
    
    public function allWorkOrders()
    {
        // Get all work orders with their relations
        $workOrders = WorkOrder::with([
            'serviceRequests', 
            'estimations',
            'user'
        ])->orderBy('created_at', 'desc')->get();
        
        return view('admin.work-orders.index', compact('workOrders'));
    }
    
    public function showWorkOrder($id)
    {
        // Get the work order with all relations
        $workOrder = WorkOrder::with([
            'serviceRequests',
            'estimations.estimationItems.serviceRequest',
            'estimations.creator',
            'estimations.approver',
            'user'
        ])->findOrFail($id);
        
        return view('admin.work-orders.show', compact('workOrder'));
    }
    
    public function allEstimations()
    {
        // Get all estimations
        $estimations = Estimation::with([
            'workOrder',
            'estimationItems.serviceRequest',
            'creator',
            'approver'
        ])->orderBy('created_at', 'desc')->get();
        
        return view('admin.estimations.index', compact('estimations'));
    }
    
    public function allInvoices()
    {
        // Get all invoices
        $invoices = Invoice::with([
            'estimation.workOrder',
            'estimation.estimationItems.serviceRequest',
            'creator'
        ])->orderBy('created_at', 'desc')->get();
        
        return view('admin.invoices.index', compact('invoices'));
    }
    
    public function users()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }
    
    public function storeUser(Request $request)
    {
        // Validate user data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,service,estimator,billing',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            // Create user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            
            return redirect()->route('admin.users')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            Log::error('Create User Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while creating the user: ' . $e->getMessage());
        }
    }
    
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Validate user data
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role' => 'required|in:admin,service,estimator,billing',
            ];
            
            // Only validate password if it's provided
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8';
            }
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            // Update basic info
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            Log::error('Update User Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the user: ' . $e->getMessage());
        }
    }
    
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting the last admin user
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()->route('admin.users')
                        ->with('error', 'Cannot delete the last admin user');
                }
            }
            
            $user->delete();
            
            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            Log::error('Delete User Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the user: ' . $e->getMessage());
        }
    }
} 