@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Admin Dashboard - All Estimations</h1>
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
                <table id="estimationsTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. SPK</th>
                            <th>Customer</th>
                            <th>Service Advisor</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimations as $estimation)
                        <tr>
                            <td>{{ $estimation->workOrder->no_spk ?? 'N/A' }}</td>
                            <td>{{ $estimation->workOrder->customer_name ?? 'N/A' }}</td>
                            <td>{{ $estimation->workOrder->service_advisor ?? 'N/A' }}</td>
                            <td>{{ $estimation->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                {{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}
                            </td>
                            <td>
                                @php
                                    $statusClass = 'bg-secondary';
                                    
                                    if ($estimation->status == 'pending') {
                                        $statusClass = 'bg-warning';
                                    } elseif ($estimation->status == 'approved') {
                                        $statusClass = 'bg-success';
                                    } elseif ($estimation->status == 'rejected') {
                                        $statusClass = 'bg-danger';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($estimation->status) }}</span>
                            </td>
                            <td>{{ $estimation->creator->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $estimation->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.estimations.pdf', ['id' => $estimation->id]) }}" class="btn btn-secondary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
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

<!-- Detail Modals -->
@foreach($estimations as $estimation)
<div class="modal fade" id="detailModal-{{ $estimation->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $estimation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel-{{ $estimation->id }}">
                    Estimation for WO #{{ $estimation->workOrder->no_spk ?? 'N/A' }}
                    @php
                        $statusClass = 'bg-secondary';
                        
                        if ($estimation->status == 'pending') {
                            $statusClass = 'bg-warning';
                        } elseif ($estimation->status == 'approved') {
                            $statusClass = 'bg-success';
                        } elseif ($estimation->status == 'rejected') {
                            $statusClass = 'bg-danger';
                        }
                    @endphp
                    <span class="badge {{ $statusClass }} ms-2">{{ ucfirst($estimation->status) }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>No. Polisi:</strong> {{ $estimation->workOrder->no_polisi ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Kilometer:</strong> {{ $estimation->workOrder->kilometer ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $estimation->workOrder->type_kendaraan ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>User:</strong> {{ $estimation->workOrder->service_user ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong> {{ $estimation->workOrder->customer_name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> {{ $estimation->created_at->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Created By:</strong> {{ $estimation->creator->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Processed By:</strong> {{ $estimation->service_advisor }}</p>
                        <p class="mb-1"><strong>Service Advisor:</strong> {{ $estimation->workOrder->service_advisor ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <h6 class="mt-4 mb-3">Detail Estimasi</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
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
                                <td>{{ $item->serviceRequest->sparepart_name ?? 'N/A' }}</td>
                                <td>{{ $item->part_number ?? 'N/A' }}</td>
                                <td>{{ $item->serviceRequest->quantity ?? 'N/A' }} {{ $item->serviceRequest->satuan ?? '' }}</td>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.estimations.pdf', ['id' => $estimation->id]) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Download PDF
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
        // Initialize DataTable
        $('#estimationsTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada estimasi yang ditemukan",
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
                { responsivePriority: 2, targets: 7 }, // Actions has second highest priority
                { responsivePriority: 3, targets: 5 }  // Status has third highest priority
            ]
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