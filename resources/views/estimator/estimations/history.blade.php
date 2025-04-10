@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>History Estimasi</h1>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
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
                            <th>No. Polisi</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimations as $estimation)
                        <tr>
                            <td>{{ $estimation->workOrder->no_spk }}</td>
                            <td>{{ $estimation->workOrder->customer_name }}</td>
                            <td>{{ $estimation->workOrder->no_polisi }}</td>
                            <td>{{ $estimation->created_at->format('d/m/Y') }}</td>
                            <td data-order="{{ $estimation->estimationItems->sum('total') }}">
                                {{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}
                            </td>
                            <td>
                                @if($estimation->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $estimation->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('estimations.show', $estimation->id) }}" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt"></i>
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
                    Work Order #{{ $estimation->workOrder->no_spk }}
                    @if($estimation->status == 'approved')
                        <span class="badge bg-success">Disetujui</span>
                    @else
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>No. Polisi:</strong> {{ $estimation->workOrder->no_polisi }}</p>
                        <p class="mb-1"><strong>Kilometer:</strong> {{ $estimation->workOrder->kilometer }}</p>
                        <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $estimation->workOrder->type_kendaraan }}</p>
                        <p><strong>User:</strong> {{ $estimation->estimationItems->first()->serviceRequest->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong> {{ $estimation->workOrder->customer_name }}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> {{ $estimation->created_at->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Service Advisor:</strong> {{ $estimation->service_advisor }}</p>
                        <p class="mb-1"><strong>Diproses pada:</strong> {{ $estimation->approved_at ? $estimation->approved_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center">
                                <th style="width: 5%">No</th>
                                <th style="width: 20%">Item Pekerjaan</th>
                                <th style="width: 10%">QTY</th>
                                <th style="width: 15%">Part Number</th>
                                <th style="width: 15%">Harga Satuan</th>
                                <th style="width: 10%">Discount (%)</th>
                                <th style="width: 15%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estimation->estimationItems as $index => $item)
                            <tr>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td>{{ $item->serviceRequest->sparepart_name }}</td>
                                <td class="text-center">{{ $item->serviceRequest->quantity }} {{ $item->serviceRequest->satuan }}</td>
                                <td>{{ $item->part_number ?? '-' }}</td>
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
                        <div class="card-header bg-{{ $estimation->status == 'approved' ? 'success' : 'danger' }} text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-{{ $estimation->status == 'approved' ? 'check-circle' : 'exclamation-circle' }}"></i>
                                Catatan {{ $estimation->status == 'approved' ? 'Persetujuan' : 'Penolakan' }}
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
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('estimations.show', $estimation->id) }}" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Lihat Detail Lengkap
                    </a>
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
                zeroRecords: "Tidak ada data yang ditemukan",
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
                { responsivePriority: 2, targets: 6 }, // Actions has second highest priority
                { responsivePriority: 3, targets: 5 }  // Status has third highest priority
            ]
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