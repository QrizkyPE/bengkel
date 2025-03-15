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

    @forelse($workOrders as $workOrder)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">
                            Work Order #{{ $workOrder->no_spk }}
                            @php
                                $estimation = $workOrder->estimations->first();
                                $status = $estimation ? ucfirst($estimation->status) : 'Unknown';
                                $statusClass = $estimation && $estimation->status == 'approved' ? 'bg-success' : 'bg-danger';
                            @endphp
                            <span class="badge {{ $statusClass }} ms-2">{{ $status }}</span>
                        </h5>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('requests.generatePDF') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </form>
                            
                            @if($estimation && $estimation->status === 'rejected')
                                <form action="{{ route('work.orders.resubmit') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-invoice-dollar"></i> Ajukan Ulang
                                    </button>
                                </form>
                            @endif
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
                            
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data permintaan sparepart untuk work order ini.
                    </div>
                @endif
                
                @if($estimation && $estimation->notes)
                <div class="mt-3">
                    <h6>Catatan Estimator:</h6>
                    <p>{{ $estimation->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada work order yang disetujui atau ditolak.
        </div>
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

    .btn-sm {
        padding: 0.25rem 0.5rem;
        min-width: 80px;
    }

    .card-header {
        background-color: #f8f9fa;
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