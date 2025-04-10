@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Admin Dashboard - All Invoices</h1>
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
                <table id="invoicesTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>No. SPK</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->estimation->workOrder->no_spk ?? 'N/A' }}</td>
                            <td>{{ $invoice->estimation->workOrder->customer_name ?? 'N/A' }}</td>
                            <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @php
                                    $statusClass = 'bg-warning';
                                    $statusText = 'Pending';
                                    
                                    if ($invoice->status == 'paid') {
                                        $statusClass = 'bg-success';
                                        $statusText = 'Paid';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>{{ $invoice->creator->name ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $invoice->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form action="{{ route('billing.generate.pdf', ['invoice' => $invoice->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-file-pdf"></i>
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

<!-- Detail Modals -->
@foreach($invoices as $invoice)
<div class="modal fade" id="detailModal-{{ $invoice->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $invoice->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel-{{ $invoice->id }}">
                    Invoice #{{ $invoice->invoice_number }}
                    @php
                        $statusClass = 'bg-warning';
                        $statusText = 'Pending';
                        
                        if ($invoice->status == 'paid') {
                            $statusClass = 'bg-success';
                            $statusText = 'Paid';
                        }
                    @endphp
                    <span class="badge {{ $statusClass }} ms-2">{{ $statusText }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>No. SPK:</strong> {{ $invoice->estimation->workOrder->no_spk ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>No. Polisi:</strong> {{ $invoice->estimation->workOrder->no_polisi ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Kilometer:</strong> {{ $invoice->estimation->workOrder->kilometer ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $invoice->estimation->workOrder->type_kendaraan ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong> {{ $invoice->estimation->workOrder->customer_name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Tanggal Invoice:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Tanggal Bayar:</strong> {{ $invoice->paid_at ? date('d/m/Y', strtotime($invoice->paid_at)) : 'Belum dibayar' }}</p>
                        <p class="mb-1"><strong>Created By:</strong> {{ $invoice->creator->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <h6 class="mt-4 mb-3">Detail Invoice</h6>
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
                            @foreach($invoice->estimation->estimationItems as $item)
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
                                <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">
                                    <strong>{{ number_format($invoice->total_amount, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>PPN (11%):</strong></td>
                                <td class="text-end">
                                    <strong>{{ number_format($invoice->tax_amount, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                <td class="text-end">
                                    <strong>{{ number_format($invoice->total_amount + $invoice->tax_amount, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                @if($invoice->notes)
                <div class="mt-3">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle"></i>
                                Notes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="{{ route('billing.generate.pdf', ['invoice' => $invoice->id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </button>
                </form>
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
        $('#invoicesTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada invoice yang ditemukan",
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
                { responsivePriority: 1, targets: 0 }, // No. Invoice has highest priority
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