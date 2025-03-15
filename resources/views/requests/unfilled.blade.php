@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Work Order Belum Terisi</h1>
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

    @if($workOrders->isNotEmpty())
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
                        Belum ada permintaan sparepart untuk work order ini. <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="alert-link">Tambahkan sparepart sekarang</a>.
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            Tidak ada work order yang belum terisi. <a href="{{ route('work_orders.create') }}" class="alert-link">Buat work order baru</a>.
        </div>
    @endif
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
        // Add confirmation dialog for delete work order buttons
        const deleteWorkOrderForms = document.querySelectorAll('.delete-work-order-form');
        deleteWorkOrderForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus work order ini?')) {
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