@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Estimasi</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>No. SPK:</strong> {{ $estimation->workOrder->no_spk }}</p>
                            <p><strong>No. Polisi:</strong> {{ $estimation->workOrder->no_polisi }}</p>
                            <p><strong>Type Kendaraan:</strong> {{ $estimation->workOrder->type_kendaraan }}</p>
                            <p><strong>Customer:</strong> {{ $estimation->workOrder->customer_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Kilometer:</strong> {{ $estimation->workOrder->kilometer }}</p>
                            <p><strong>Tanggal:</strong> {{ $estimation->created_at->format('d/m/Y') }}</p>
                            <p><strong>Service Advisor:</strong> {{ $estimation->workOrder->service_advisor }}</p>
                            <p><strong>Keluhan:</strong> {{ $estimation->workOrder->keluhan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr style="text-align: center">
                                    <th style="width: 5%">No</th>
                                    <th style="width: 20%">Kebutuhan Part</th>
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

                    @if($estimation->status === 'pending')
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Tindakan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('estimations.approve', $estimation->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="approve_notes">Catatan (opsional)</label>
                                            <textarea name="notes" id="approve_notes" class="form-control" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Setujui Estimasi
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('estimations.reject', $estimation->id) }}" method="POST" id="rejectForm">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="reject_notes">Catatan <span class="text-danger">*</span></label>
                                            <textarea name="notes" id="reject_notes" class="form-control @error('notes') is-invalid @enderror" rows="3" required></textarea>
                                            <div class="invalid-feedback" id="notesError">
                                                Catatan wajib diisi saat menolak estimasi. Berikan alasan penolakan.
                                            </div>
                                            @error('notes')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <button type="button" id="rejectButton" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Tolak Estimasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Status Estimasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert {{ $estimation->status === 'approved' ? 'alert-success' : 'alert-danger' }}">
                                <strong>Status:</strong> 
                                {{ $estimation->status === 'approved' ? 'Disetujui' : 'Ditolak' }} 
                                pada {{ $estimation->approved_at->format('d/m/Y H:i') }}
                            </div>
                            
                            @if($estimation->notes)
                            <div class="mt-3">
                                <h6>Catatan:</h6>
                                <p>{{ $estimation->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('estimations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        
                        @if($estimation->status === 'approved')
                        <a href="{{ route('estimations.pdf', $estimation->id) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Client-side validation for reject form
        const rejectButton = document.getElementById('rejectButton');
        if (rejectButton) {
            rejectButton.addEventListener('click', function() {
                const rejectNotes = document.getElementById('reject_notes');
                const notesError = document.getElementById('notesError');
                
                if (!rejectNotes.value.trim()) {
                    rejectNotes.classList.add('is-invalid');
                    notesError.style.display = 'block';
                } else {
                    document.getElementById('rejectForm').submit();
                }
            });
            
            // Remove error when user starts typing
            document.getElementById('reject_notes').addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
</script>
@endpush
@endsection 