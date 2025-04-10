@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>History Work Order</h1>
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
                            <th>No. Polisi</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrders as $workOrder)
                        <tr>
                            <td>{{ $workOrder->no_spk }}</td>
                            <td>{{ $workOrder->no_polisi }}</td>
                            <td>{{ $workOrder->customer_name }}</td>
                            <td>{{ $workOrder->created_at->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $estimation = $workOrder->estimations->first();
                                    $status = $estimation ? ucfirst($estimation->status) : 'Unknown';
                                    $statusClass = $estimation && $estimation->status == 'approved' ? 'bg-success' : 'bg-danger';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $workOrder->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </form>
                                    
                                    @if($estimation && $estimation->status === 'rejected')
                                        <form action="{{ route('work.orders.resubmit') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-redo"></i>
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
</div>

<!-- Detail Modals -->
@foreach($workOrders as $workOrder)
<div class="modal fade" id="detailModal-{{ $workOrder->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $workOrder->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                @php
                    $estimation = $workOrder->estimations->first();
                    $status = $estimation ? ucfirst($estimation->status) : 'Unknown';
                    $statusClass = $estimation && $estimation->status == 'approved' ? 'bg-success' : 'bg-danger';
                @endphp
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
                        <p class="mb-1"><strong>Diproses pada:</strong> {{ $estimation && $estimation->approved_at ? $estimation->approved_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>

                @if($workOrder->serviceRequests->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center">
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">ITEM PEKERJAAN</th>
                                    <th style="width: 10%">QTY</th>
                                    <th style="width: 25%">KEBUTUHAN PART DAN BAHAN</th>
                                    <th style="width: 25%">KET</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrder->serviceRequests as $index => $request)
                                <tr>
                                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                                    <td>{{ $request->sparepart_name }}</td>
                                    <td class="text-center">{{ $request->quantity }} {{ $request->satuan }}</td>
                                    <td style="text-align: left;">{{ $request->kebutuhan_part ?? '' }}</td>
                                    <td style="text-align: left;">{{ $request->keterangan ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end">
                                        <strong>Status:</strong> 
                                        @if($estimation && $estimation->status == 'approved')
                                            <span class="badge bg-success">Disetujui pada {{ $estimation->approved_at->format('d/m/Y H:i') }}</span>
                                        @elseif($estimation && $estimation->status == 'rejected')
                                            <span class="badge bg-danger">Ditolak pada {{ $estimation->approved_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data permintaan sparepart untuk work order ini.
                    </div>
                @endif
                
                @if($estimation && $estimation->notes)
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
                    <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </button>
                    </form>
                    
                    @if($estimation && $estimation->status === 'rejected')
                        <form action="{{ route('work.orders.resubmit') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-redo"></i> Ajukan Ulang
                            </button>
                        </form>
                    @endif
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
        $('#workOrdersTable').DataTable({
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
                { responsivePriority: 2, targets: 5 }, // Actions has second highest priority
                { responsivePriority: 3, targets: 4 }  // Status has third highest priority
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