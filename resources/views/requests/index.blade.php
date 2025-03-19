@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Work Order & Sparepart</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('work_orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Work Order
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

    <!-- Display Work Orders with Requests -->
    @forelse($requests->groupBy('work_order_id') as $workOrderId => $workOrderRequests)
        @if($workOrderId && $workOrderRequests->first()->workOrder)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">
                                Work Order #{{ $workOrderRequests->first()->workOrder->no_spk }}
                                @php
                                    $hasEstimation = \App\Models\Estimation::where('work_order_id', $workOrderId)->first();
                                    $status = 'Not sent';
                                    $statusClass = 'bg-secondary';
                                    
                                    if ($hasEstimation) {
                                        $status = ucfirst($hasEstimation->status);
                                        if ($hasEstimation->status == 'pending') {
                                            $statusClass = 'bg-warning';
                                        } elseif ($hasEstimation->status == 'approved') {
                                            $statusClass = 'bg-success';
                                        } elseif ($hasEstimation->status == 'rejected') {
                                            $statusClass = 'bg-danger';
                                        }
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }} ms-2">{{ $status }}</span>
                            </h5>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('requests.create', ['work_order' => $workOrderId]) }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Sparepart
                                </a>
                                <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="work_order_id" value="{{ $workOrderId }}">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </form>
                                @php
                                    $canSubmit = !$hasEstimation || $hasEstimation->status === 'rejected';
                                @endphp
                                @if($canSubmit)
                                    <form action="{{ route('submit.to.estimator') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="work_order_id" value="{{ $workOrderId }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-invoice-dollar"></i> Ajukan
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('work_orders.destroy', $workOrderRequests->first()->workOrder->id) }}" method="POST" class="delete-work-order-form d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>No. Polisi:</strong> {{ $workOrderRequests->first()->workOrder->no_polisi }}</p>
                            <p class="mb-1"><strong>Kilometer:</strong> {{ $workOrderRequests->first()->workOrder->kilometer }}</p>
                            <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $workOrderRequests->first()->workOrder->type_kendaraan }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Customer:</strong> {{ $workOrderRequests->first()->workOrder->customer_name }}</p>
                            <p class="mb-1"><strong>Tanggal:</strong> {{ $workOrderRequests->first()->workOrder->created_at->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Keluhan:</strong> {{ $workOrderRequests->first()->workOrder->keluhan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center">
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">ITEM PEKERJAAN</th>
                                    <th style="width: 10%">QTY</th>
                                    <th style="width: 25%">KEBUTUHAN PART DAN BAHAN</th>
                                    <th style="width: 25%">KET</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrderRequests as $index => $request)
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>{{ $request->sparepart_name }}</td>
                                    <td class="text-center">{{ $request->quantity }} {{ $request->satuan }}</td>
                                    <td style="text-align: left;">{{ $request->kebutuhan_part ?? '' }}</td>
                                    <td style="text-align: left;">{{ $request->keterangan ?? '' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('requests.edit', $request) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('requests.destroy', $request) }}" method="POST" class="delete-form">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <!-- If no requests with work orders, check if we have any work orders -->
        @if(isset($workOrders) && $workOrders->isNotEmpty())
            @foreach($workOrders as $workOrder)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    Work Order #{{ $workOrder->no_spk }}
                                    @php
                                        $hasEstimation = \App\Models\Estimation::where('work_order_id', $workOrder->id)->first();
                                        $status = 'Not sent';
                                        $statusClass = 'bg-secondary';
                                        
                                        if ($hasEstimation) {
                                            $status = ucfirst($hasEstimation->status);
                                            if ($hasEstimation->status == 'pending') {
                                                $statusClass = 'bg-warning';
                                            } elseif ($hasEstimation->status == 'approved') {
                                                $statusClass = 'bg-success';
                                            } elseif ($hasEstimation->status == 'rejected') {
                                                $statusClass = 'bg-danger';
                                            }
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }} ms-2">{{ $status }}</span>
                                </h5>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Sparepart
                                    </a>
                                    <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </button>
                                    </form>
                                    @php
                                        $hasEstimation = \App\Models\Estimation::where('work_order_id', $workOrder->id)->first();
                                        $canSubmit = !$hasEstimation || $hasEstimation->status === 'rejected';
                                    @endphp
                                    @if($canSubmit)
                                        <form action="{{ route('submit.to.estimator') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-file-invoice-dollar"></i> Ajukan
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('work_orders.destroy', $workOrder->id) }}" method="POST" class="delete-work-order-form d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>No. Polisi:</strong> {{ $workOrder->no_polisi }}</p>
                                <p class="mb-1"><strong>Kilometer:</strong> {{ $workOrder->kilometer }}</p>
                                <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $workOrder->type_kendaraan }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Customer:</strong> {{ $workOrder->customer_name }}</p>
                                <p class="mb-1"><strong>Tanggal:</strong> {{ $workOrder->created_at->format('d/m/Y') }}</p>
                                <p class="mb-1"><strong>Keluhan:</strong> {{ $workOrder->keluhan ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            Belum ada permintaan sparepart untuk work order ini.
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                Belum ada work order atau permintaan sparepart.
            </div>
        @endif
    @endforelse
</div>

@push('styles')
<style>
    .table-responsive {
        margin-top: 1rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
    }

    .table td:nth-child(4),
    .table td:nth-child(5) {
        text-align: left;
    }

    .d-flex.gap-2 {
        display: flex;
        gap: 0.5rem !important;
        justify-content: center;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        min-width: 80px;
    }

    .card-header {
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .table td, .table th {
            min-width: 100px;
        }
        
        td:nth-child(4), td:nth-child(5) {
            max-width: 200px;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.25rem !important;
        }
    }

    .delete-work-order-form {
        display: inline-block !important;
    }
    
    .card-header .btn-sm {
        margin-bottom: 5px;
        display: inline-block;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .btn-group form {
        display: inline-block;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group form:last-child .btn {
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add confirmation dialog for delete buttons
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
                    this.submit();
                }
            });
        });

        // Add confirmation dialog for delete work order buttons
        const deleteWorkOrderForms = document.querySelectorAll('.delete-work-order-form');
        deleteWorkOrderForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus work order ini? Semua permintaan sparepart terkait juga akan dihapus.')) {
                    this.submit();
                }
            });
        });

        // Auto-close alerts after 3 seconds
        const alerts = document.querySelectorAll('.alert');
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
