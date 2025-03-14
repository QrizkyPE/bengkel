@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Work Order & Permintaan Sparepart</h1>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#pdfModal">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <a href="{{ route('requests.create') }}" class="btn btn-success me-2">
                <i class="fas fa-plus"></i> Buat Permintaan
            </a>
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

    @forelse($requests->groupBy('work_order_id') as $workOrderId => $workOrderRequests)
        @if($workOrderId && $workOrderRequests->first()->workOrder)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">Work Order #{{ $workOrderRequests->first()->workOrder->no_spk }}</h5>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('requests.create', ['work_order' => $workOrderId]) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Tambah Sparepart
                            </a>
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
        @else
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Permintaan Tanpa Work Order</h5>
                </div>
                <div class="card-body">
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
        <div class="alert alert-info">
            Belum ada permintaan sparepart. Silakan buat Work Order terlebih dahulu.
        </div>
    @endforelse

    <!-- Modal for PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Enter Work Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('requests.generatePDF') }}" method="POST" id="pdfForm">
                        @csrf
                        <div class="mb-3">
                            <label for="no_polisi" class="form-label">No. Polisi</label>
                            <input type="text" class="form-control" id="no_polisi" name="no_polisi" required>
                        </div>
                        <div class="mb-3">
                            <label for="kilometer" class="form-label">Kilometer</label>
                            <input type="number" class="form-control" id="kilometer" name="kilometer" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_spk" class="form-label">No. SPK</label>
                            <input type="text" class="form-control" id="no_spk" name="no_spk" required>
                        </div>
                        <div class="mb-3">
                            <label for="type_kendaraan" class="form-label">Type Kendaraan</label>
                            <input type="text" class="form-control" id="type_kendaraan" name="type_kendaraan" required>
                        </div>
                        <div class="mb-3">
                            <label for="keluhan" class="form-label">Keluhan</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="pdfForm" class="btn btn-primary">Generate PDF</button>
                </div>
            </div>
        </div>
    </div>
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
