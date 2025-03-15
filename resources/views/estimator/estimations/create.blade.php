@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Buat Estimasi</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Work Order #{{ $workOrder->no_spk }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('estimations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. Polisi</label>
                            <input type="text" class="form-control" value="{{ $workOrder->no_polisi }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kilometer</label>
                            <input type="text" class="form-control" value="{{ $workOrder->kilometer }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. SPK</label>
                            <input type="text" class="form-control" value="{{ $workOrder->no_spk }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control" value="{{ $workOrder->created_at->format('d/m/Y') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Type Kendaraan</label>
                            <input type="text" class="form-control" value="{{ $workOrder->type_kendaraan }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" value="{{ $workOrder->customer_name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <input type="text" class="form-control" value="{{ $workOrder->user->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service Advisor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('service_advisor') is-invalid @enderror" name="service_advisor" value="{{ old('service_advisor') }}" required>
                            @error('service_advisor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keluhan</label>
                    <textarea class="form-control" rows="2" readonly>{{ $workOrder->keluhan }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan Estimasi</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center">
                                <th style="width: 5%">No</th>
                                <th style="width: 20%">Item Pekerjaan</th>
                                <th style="width: 15%">Part Number</th>
                                <th style="width: 10%">Quantity</th>
                                <th style="width: 10%">Satuan</th>
                                <th style="width: 15%">Harga Satuan</th>
                                <th style="width: 10%">Discount (%)</th>
                                <th style="width: 15%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workOrder->serviceRequests as $index => $request)
                            <tr>
                                <td style="text-align: center;">{{ $loop->iteration }}</td>
                                <td>
                                    {{ $request->sparepart_name }}
                                    <input type="hidden" name="service_request_id[{{ $index }}]" value="{{ $request->id }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm @error('part_number.'.$index) is-invalid @enderror" name="part_number[{{ $index }}]" value="{{ old('part_number.'.$index) }}">
                                </td>
                                <td style="text-align: center;">{{ $request->quantity }}</td>
                                <td style="text-align: center;">{{ $request->satuan }}</td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm price-input @error('price.'.$index) is-invalid @enderror" name="price[{{ $index }}]" value="{{ old('price.'.$index, 0) }}" required data-index="{{ $index }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm discount-input @error('discount.'.$index) is-invalid @enderror" name="discount[{{ $index }}]" value="{{ old('discount.'.$index, 0) }}" min="0" max="100" data-index="{{ $index }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm total-display" id="total-display-{{ $index }}" value="0" readonly>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end"><strong>Grand Total:</strong></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" id="grand-total" value="0" readonly>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Estimasi
                    </button>
                </div>
            </form>
        </div>
    </div>
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

    .form-label {
        font-weight: 500;
    }

    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceInputs = document.querySelectorAll('.price-input');
        const discountInputs = document.querySelectorAll('.discount-input');
        
        function calculateTotal(index) {
            const price = parseFloat(document.querySelector(`input[name="price[${index}]"]`).value) || 0;
            const discount = parseFloat(document.querySelector(`input[name="discount[${index}]"]`).value) || 0;
            const total = price * (1 - discount / 100);
            
            document.getElementById(`total-display-${index}`).value = total.toFixed(2);
            
            calculateGrandTotal();
        }
        
        function calculateGrandTotal() {
            const totalDisplays = document.querySelectorAll('.total-display');
            let grandTotal = 0;
            
            totalDisplays.forEach(display => {
                grandTotal += parseFloat(display.value) || 0;
            });
            
            document.getElementById('grand-total').value = grandTotal.toFixed(2);
        }
        
        priceInputs.forEach(input => {
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                calculateTotal(index);
            });
        });
        
        discountInputs.forEach(input => {
            input.addEventListener('input', function() {
                const index = this.getAttribute('data-index');
                calculateTotal(index);
            });
        });
        
        // Initialize calculations
        priceInputs.forEach(input => {
            const index = input.getAttribute('data-index');
            calculateTotal(index);
        });
    });
</script>
@endpush
@endsection 