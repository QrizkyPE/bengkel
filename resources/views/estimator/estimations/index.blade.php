@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Estimasi</h1>
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

    @forelse($estimations as $estimation)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">Work Order #{{ $estimation->workOrder->no_spk }}</h5>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('estimations.edit', $estimation->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Harga
                            </a>
                            <form action="{{ route('estimations.pdf', ['id' => $estimation->id]) }}" method="GET" class="d-inline">
                                
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </form>
                            @if($estimation->status == 'pending')
                                <form action="{{ url('/estimator/estimations/'.$estimation->id.'/approve') }}" method="POST" class="d-inline approve-form" data-estimation-id="{{ $estimation->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-success approve-btn" data-estimation-id="{{ $estimation->id }}">
                                        <i class="fas fa-check"></i> Setujui
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
                        <p class="mb-1"><strong>No. Polisi:</strong> {{ $estimation->workOrder->no_polisi }}</p>
                        <p class="mb-1"><strong>Kilometer:</strong> {{ $estimation->workOrder->kilometer }}</p>
                        <p class="mb-1"><strong>Type Kendaraan:</strong> {{ $estimation->workOrder->type_kendaraan }}</p>
                        <p><strong>User:</strong> {{ $estimation->workOrder->service_user }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Customer:</strong> {{ $estimation->workOrder->customer_name }}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> {{ $estimation->created_at->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Service Advisor:</strong> {{ $estimation->workOrder->service_advisor }}</p>

                        <p class="mb-1"><strong>Status:</strong> 
                            @if($estimation->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($estimation->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($estimation->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center">
                                <th style="width: 5%">No</th>
                                <th style="width: 20%">Item Pekerjaan</th>
                                <th style="width: 15%">Part Number</th>
                                <th style="width: 10%">QTY</th>
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
                                <td>{{ $item->part_number ?? '-' }}</td>
                                <td class="text-center">{{ $item->serviceRequest->quantity }} {{ $item->serviceRequest->satuan }}</td>
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
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada work order yang dipindahkan ke halaman Estimasi.
        </div>
    @endforelse
</div>

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
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

    .btn-group form {
        display: inline-block;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group form:last-child .btn {
        margin-right: 0;
    }
    
    .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
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
        
        // Add validation for approve buttons
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const estimationId = this.getAttribute('data-estimation-id');
                const card = this.closest('.card');
                const items = card.querySelectorAll('tbody tr');
                let isValid = true;
                let emptyFields = [];
                
                items.forEach((item, index) => {
                    const partNumber = item.querySelector('td:nth-child(4)').textContent.trim();
                    const price = item.querySelector('td:nth-child(5)').textContent.trim();
                    
                    if (partNumber === '-' || partNumber === '') {
                        isValid = false;
                        emptyFields.push(`Part Number untuk item #${index + 1}`);
                    }
                    
                    if (price === '0' || price === '' || price === '0,00') {
                        isValid = false;
                        emptyFields.push(`Harga Satuan untuk item #${index + 1}`);
                    }
                });
                
                if (!isValid) {
                    // Create a message with all empty fields
                    const message = 'Mohon lengkapi data berikut sebelum menyetujui estimasi:\n- ' + 
                                    emptyFields.join('\n- ');
                    
                    alert(message);
                    
                    // Redirect to edit page
                    window.location.href = "{{ url('/estimator/estimations') }}/" + estimationId + "/edit";
                } else {
                    // If all fields are filled, submit the form
                    document.querySelector(`.approve-form[data-estimation-id="${estimationId}"]`).submit();
                }
            });
        });
    });
</script>
@endpush
@endsection 