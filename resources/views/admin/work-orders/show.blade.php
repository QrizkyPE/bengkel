@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Work Order Detail</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.work-orders') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Work Orders
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Work Order Information</h5>
                    <span class="badge bg-primary">{{ $workOrder->no_spk }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>No. Polisi:</strong> {{ $workOrder->no_polisi }}</p>
                            <p class="mb-1"><strong>Kilometer:</strong> {{ $workOrder->kilometer }}</p>
                            <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $workOrder->type_kendaraan }}</p>
                            <p class="mb-1"><strong>Created By:</strong> {{ $workOrder->user->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>User Role:</strong> {{ $workOrder->user->role ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Customer:</strong> {{ $workOrder->customer_name }}</p>
                            <p class="mb-1"><strong>Tanggal:</strong> {{ $workOrder->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Created:</strong> {{ $workOrder->created_at->diffForHumans() }}</p>
                            <p class="mb-1"><strong>Updated:</strong> {{ $workOrder->updated_at->diffForHumans() }}</p>
                            
                            @php
                                $estimation = $workOrder->estimations->first();
                                $status = 'Not submitted';
                                $statusClass = 'bg-secondary';
                                
                                if ($estimation) {
                                    $status = ucfirst($estimation->status);
                                    if ($estimation->status == 'pending') {
                                        $statusClass = 'bg-warning';
                                    } elseif ($estimation->status == 'approved') {
                                        $statusClass = 'bg-success';
                                    } elseif ($estimation->status == 'rejected') {
                                        $statusClass = 'bg-danger';
                                    }
                                }
                            @endphp
                            <p class="mb-1"><strong>Status:</strong> <span class="badge {{ $statusClass }}">{{ $status }}</span></p>
                        </div>
                    </div>

                    @if($workOrder->keluhan)
                    <div class="mt-3">
                        <h6>Keluhan:</h6>
                        <p class="border rounded p-3 bg-light">{{ $workOrder->keluhan }}</p>
                    </div>
                    @endif

                    <div class="d-flex justify-content-end mt-3">
                        <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline me-2">
                            @csrf
                            <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Process Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Work Order Created</h6>
                                <small class="text-muted">{{ $workOrder->created_at->format('d/m/Y H:i') }}</small>
                                <p>Created by {{ $workOrder->user->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($workOrder->serviceRequests->isNotEmpty())
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Service Requests Added</h6>
                                <small class="text-muted">{{ $workOrder->serviceRequests->first()->created_at->format('d/m/Y H:i') }}</small>
                                <p>{{ $workOrder->serviceRequests->count() }} spare part requests added</p>
                            </div>
                        </div>
                        @endif

                        @if($estimation)
                        <div class="timeline-item">
                            <div class="timeline-marker 
                                @if($estimation->status == 'pending') bg-warning 
                                @elseif($estimation->status == 'approved') bg-success 
                                @elseif($estimation->status == 'rejected') bg-danger 
                                @endif">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Estimation Created</h6>
                                <small class="text-muted">{{ $estimation->created_at->format('d/m/Y H:i') }}</small>
                                <p>By {{ $estimation->creator->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($estimation->status != 'pending')
                        <div class="timeline-item">
                            <div class="timeline-marker 
                                @if($estimation->status == 'approved') bg-success 
                                @elseif($estimation->status == 'rejected') bg-danger 
                                @endif">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Estimation {{ ucfirst($estimation->status) }}</h6>
                                <small class="text-muted">{{ $estimation->approved_at ? $estimation->approved_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                <p>By {{ $estimation->service_advisor ?? 'N/A' }}</p>
                                @if($estimation->notes)
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <small>{{ $estimation->notes }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($estimation->invoice)
                        <div class="timeline-item">
                            <div class="timeline-marker 
                                @if($estimation->invoice->status == 'pending') bg-warning 
                                @elseif($estimation->invoice->status == 'paid') bg-success 
                                @else bg-secondary 
                                @endif">
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Invoice Created</h6>
                                <small class="text-muted">{{ $estimation->invoice->created_at->format('d/m/Y H:i') }}</small>
                                <p>Invoice #{{ $estimation->invoice->invoice_number }}</p>
                                <p>Status: <span class="badge 
                                    @if($estimation->invoice->status == 'pending') bg-warning 
                                    @elseif($estimation->invoice->status == 'paid') bg-success 
                                    @else bg-secondary 
                                    @endif">
                                    {{ ucfirst($estimation->invoice->status) }}
                                </span></p>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($workOrder->serviceRequests->isNotEmpty())
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Spare Part Requests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Sparepart</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Kebutuhan Part</th>
                            <th>Keterangan</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrder->serviceRequests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $request->sparepart_name }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>{{ $request->satuan }}</td>
                            <td>{{ $request->kebutuhan_part ?? '-' }}</td>
                            <td>{{ $request->keterangan ?? '-' }}</td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($estimation)
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Estimation Details</h5>
            <span class="badge {{ $statusClass }}">{{ ucfirst($estimation->status) }}</span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>Service Advisor:</strong> {{ $estimation->service_advisor }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Created By:</strong> {{ $estimation->creator->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Created At:</strong> {{ $estimation->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Item</th>
                            <th>Part Number</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Discount (%)</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimation->estimationItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->serviceRequest->sparepart_name ?? '-' }}</td>
                            <td>{{ $item->part_number ?? '-' }}</td>
                            <td>{{ $item->serviceRequest->quantity ?? '-' }} {{ $item->serviceRequest->satuan ?? '' }}</td>
                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->discount }}%</td>
                            <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($estimation->notes)
            <div class="mt-3">
                <div class="card">
                    <div class="card-header bg-{{ $estimation->status == 'approved' ? 'success' : ($estimation->status == 'rejected' ? 'danger' : 'warning') }} text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-{{ $estimation->status == 'approved' ? 'check-circle' : ($estimation->status == 'rejected' ? 'times-circle' : 'clock') }}"></i>
                            {{ $estimation->status == 'approved' ? 'Approval' : ($estimation->status == 'rejected' ? 'Rejection' : 'Pending') }} Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $estimation->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 9px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #6c757d;
    }
    
    .timeline-content {
        padding-bottom: 10px;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: none;
        padding: 1rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
    }
    
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-close alerts after 3 seconds
        const alerts = document.querySelectorAll('.alert:not(.alert-info)');
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            }, 3000);
        });
    });
</script>
@endpush
@endsection 