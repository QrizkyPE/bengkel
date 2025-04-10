@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Service Request</h1>
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

    <!-- Work Orders with Service Requests -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Work Orders dengan Permintaan Sparepart</h5>
            <div class="table-responsive">
                <table id="serviceRequestsTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. SPK</th>
                            <th>Customer</th>
                            <th>No. Polisi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests->groupBy('work_order_id') as $workOrderId => $workOrderRequests)
                            @php
                                $workOrder = $workOrderRequests->first()->workOrder;
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
                            <tr>
                                <td>{{ $workOrder->no_spk }}</td>
                                <td>{{ $workOrder->customer_name }}</td>
                                <td>{{ $workOrder->no_polisi }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $status }}</span></td>
                                <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
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
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{{-- 
    <!-- Empty Work Orders -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Work Orders Kosong</h5>
                <a href="{{ route('unfilled.work.orders') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            @if($workOrders->isNotEmpty())
            <div class="table-responsive">
                <table id="emptyWorkOrdersTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. SPK</th>
                            <th>Customer</th>
                            <th>No. Polisi</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrders->take(5) as $workOrder)
                        <tr>
                            <td>{{ $workOrder->no_spk }}</td>
                            <td>{{ $workOrder->customer_name }}</td>
                            <td>{{ $workOrder->no_polisi }}</td>
                            <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info btn-detail" data-bs-toggle="modal" data-bs-target="#emptyModal-{{ $workOrder->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mt-3">
                Tidak ada work order kosong. <a href="{{ route('work_orders.create') }}" class="alert-link">Buat work order baru</a>.
            </div>
            @endif
        </div>
    </div>
</div> --}}

<!-- Detail Modals for Work Orders with Service Requests -->
@foreach($requests->groupBy('work_order_id') as $workOrderId => $workOrderRequests)
@php
    $workOrder = $workOrderRequests->first()->workOrder;
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
<div class="modal fade" id="detailModal-{{ $workOrder->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $workOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel-{{ $workOrder->id }}">
                    Work Order #{{ $workOrder->no_spk }}
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
                
                <h6 class="mt-4 mb-3">Daftar Permintaan Sparepart</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Nama Sparepart</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Kebutuhan Part</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrderRequests as $index => $request)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $request->sparepart_name }}</td>
                                <td>{{ $request->quantity }}</td>
                                <td>{{ $request->satuan }}</td>
                                <td>{{ $request->kebutuhan_part ?? '-' }}</td>
                                <td>{{ $request->keterangan ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('requests.edit', $request->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('requests.destroy', $request->id) }}" method="POST" class="delete-request-form d-inline">
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
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Sparepart
                    </a>
                    <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Download PDF
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
                                <i class="fas fa-file-invoice-dollar"></i> Ajukan Estimasi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Detail Modals for Empty Work Orders -->
@foreach($workOrders as $workOrder)
<div class="modal fade" id="emptyModal-{{ $workOrder->id }}" tabindex="-1" aria-labelledby="emptyModalLabel-{{ $workOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emptyModalLabel-{{ $workOrder->id }}">Work Order #{{ $workOrder->no_spk }}</h5>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('requests.create', ['work_order' => $workOrder->id]) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Sparepart
                </a>
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

    .btn-sm {
        padding: 0.25rem 0.5rem;
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
        // Initialize DataTables
        $('#serviceRequestsTable').DataTable({
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
            order: [[4, 'desc']], // Sort by date column in descending order
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // No. SPK has highest priority
                { responsivePriority: 2, targets: 5 }, // Actions has second highest priority
                { responsivePriority: 3, targets: 3 }  // Status has third highest priority
            ]
        });
        
        $('#emptyWorkOrdersTable').DataTable({
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
            order: [[3, 'desc']], // Sort by date column in descending order
            paging: false,
            searching: false,
            info: false
        });

        // Add confirmation dialog for delete request buttons
        const deleteRequestForms = document.querySelectorAll('.delete-request-form');
        deleteRequestForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus permintaan sparepart ini?')) {
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
