@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Work Order</h1>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="workOrdersTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. SPK</th>
                            <th>Customer</th>
                            <th>No. Polisi</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrders as $workOrder)
                        <tr>
                            <td>{{ $workOrder->no_spk }}</td>
                            <td>{{ $workOrder->customer_name }}</td>
                            <td>{{ $workOrder->no_polisi }}</td>
                            <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
                            <td>
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
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $workOrder->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-file-pdf"></i>
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
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('work_orders.destroy', $workOrder->id) }}" method="POST" class="delete-work-order-form d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
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
</div>

<!-- Detail Modals for Work Orders -->
@foreach($workOrders as $workOrder)
<div class="modal fade" id="detailModal-{{ $workOrder->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $workOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel-{{ $workOrder->id }}">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Sparepart
                    </a>
                    @php
                        $canSubmit = !$hasEstimation || $hasEstimation->status === 'rejected';
                    @endphp
                    @if($canSubmit)
                        <form action="{{ route('submit.to.estimator') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-invoice-dollar"></i> Ajukan Estimasi
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
</div>
@endforeach

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
    }
    
    .table-responsive {
        margin-top: 1rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .btn-group .btn {
        margin-right: 2px;
    }
    
    /* DataTables customization */
    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
    }
    
    div.dataTables_wrapper div.dataTables_paginate {
        margin-top: 0.5em;
    }
    
    div.dataTables_wrapper div.dataTables_length select {
        width: 5em;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        $('#workOrdersTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada work order yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            order: [[3, 'desc']], // Sort by date column (index 3) in descending order
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // No. SPK has highest priority
                { responsivePriority: 2, targets: 5 }, // Actions has second highest priority
                { responsivePriority: 3, targets: 4 }  // Status has third highest priority
            ]
        });

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