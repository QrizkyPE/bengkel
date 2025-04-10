@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Admin Dashboard</h1>
            <p class="text-muted">Welcome to the admin panel. You have access to all system data.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-primary text-white rounded-circle shadow-sm p-3 me-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <span class="d-block h5 mb-0">{{ $workOrdersCount }}</span>
                            <span class="text-sm text-muted">Work Orders</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.work-orders') }}" class="btn btn-sm btn-outline-primary w-100">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-warning text-white rounded-circle shadow-sm p-3 me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <span class="d-block h5 mb-0">{{ $pendingEstimationsCount }}</span>
                            <span class="text-sm text-muted">Pending Estimations</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.estimations') }}" class="btn btn-sm btn-outline-warning w-100">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-success text-white rounded-circle shadow-sm p-3 me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <span class="d-block h5 mb-0">{{ $approvedEstimationsCount }}</span>
                            <span class="text-sm text-muted">Approved Estimations</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.estimations') }}" class="btn btn-sm btn-outline-success w-100">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-info text-white rounded-circle shadow-sm p-3 me-3">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <span class="d-block h5 mb-0">{{ $invoicesCount }}</span>
                            <span class="text-sm text-muted">Invoices</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.invoices') }}" class="btn btn-sm btn-outline-info w-100">View All</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4 mt-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">User Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-dark text-white rounded-circle shadow-sm p-3 me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <span class="d-block h5 mb-0">{{ $usersCount }}</span>
                            <span class="text-sm text-muted">Total Users</span>
                        </div>
                    </div>
                    <p>Manage user accounts, assign roles, and control permissions.</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-dark w-100">Manage Users</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.work-orders') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-clipboard-list me-3 text-primary"></i>
                            <span>View All Work Orders</span>
                        </a>
                        <a href="{{ route('admin.estimations') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-calculator me-3 text-warning"></i>
                            <span>View All Estimations</span>
                        </a>
                        <a href="{{ route('admin.invoices') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-file-invoice-dollar me-3 text-info"></i>
                            <span>View All Invoices</span>
                        </a>
                        <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-user-plus me-3 text-success"></i>
                            <span>Add New User</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 1rem 0.5rem;
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .list-group-item i {
        font-size: 1.25rem;
    }
</style>
@endpush
@endsection 