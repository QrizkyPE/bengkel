@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Estimasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('estimations.update', $estimation->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="service_advisor" value="{{ auth()->user()->name }}">
                        
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
                                
                                <p><strong>Service Advisor:</strong> {{ auth()->user()->name }}</p>
                                <p><strong>User:</strong> {{ $estimation->estimationItems->first()->serviceRequest->user->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                        <td>
                                            <input type="hidden" name="estimation_item_id[]" value="{{ $item->id }}">
                                            <input type="text" name="part_number[]" class="form-control" value="{{ old('part_number.'.$index, $item->part_number) }}">
                                        </td>
                                        <td>
                                            <input type="text" name="price[]" class="form-control price-input" value="{{ old('price.'.$index, number_format($item->price, 0, '', ',')) }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="discount[]" class="form-control discount-input" value="{{ old('discount.'.$index, $item->discount) }}" min="0" max="100" step="0.01">
                                        </td>
                                        <td class="text-end total-display">
                                            {{ number_format($item->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                        <td class="text-end grand-total">
                                            <strong>{{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="form-group mt-4">
                            <label for="notes">Catatan (opsional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $estimation->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="button" id="validateAndSubmit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('estimations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate totals
        function calculateTotals() {
            const rows = document.querySelectorAll('tbody tr');
            let grandTotal = 0;
            
            rows.forEach(row => {
                const priceInput = row.querySelector('.price-input');
                const discountInput = row.querySelector('.discount-input');
                const totalDisplay = row.querySelector('.total-display');
                
                // Parse price by removing commas
                const priceStr = priceInput.value.replace(/,/g, '');
                const price = parseFloat(priceStr) || 0;
                
                const discount = parseFloat(discountInput.value) || 0;
                const quantityText = row.querySelector('td:nth-child(3)').textContent.trim().split(' ')[0];
                const quantity = parseInt(quantityText) || 1;
                
                // Calculate total without rounding
                const total = price * quantity * (1 - discount / 100);
                grandTotal += total;
                
                // Format with proper decimal places and commas
                totalDisplay.textContent = formatRupiah(total);
            });
            
            document.querySelector('.grand-total strong').textContent = formatRupiah(grandTotal);
        }
        
        // Format number as Indonesian Rupiah (using commas)
        function formatRupiah(number) {
            // Format with commas for thousands
            return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Format input as Rupiah while typing
        function formatPriceInput(input) {
            // Get the caret position
            const caretPos = input.selectionStart;
            const oldLength = input.value.length;
            
            // Remove all non-digits except commas
            let value = input.value.replace(/[^\d,]/g, '');
            
            // Remove all commas
            value = value.replace(/,/g, '');
            
            // Add commas for thousands
            if (value.length > 0) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            
            // Update the input value
            input.value = value;
            
            // Adjust caret position based on added/removed characters
            const newLength = input.value.length;
            const caretAdjust = newLength - oldLength;
            input.setSelectionRange(caretPos + caretAdjust, caretPos + caretAdjust);
        }
        
        // Initialize price inputs with proper formatting
        document.querySelectorAll('.price-input').forEach(input => {
            // Add input event for live formatting
            input.addEventListener('input', function() {
                formatPriceInput(this);
                calculateTotals();
            });
            
            // Handle focus to ensure proper caret position
            input.addEventListener('focus', function() {
                if (this.value === '0') {
                    this.value = '';
                }
            });
            
            // Handle blur to ensure proper value
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.value = '0';
                }
                calculateTotals();
            });
        });
        
        // Add event listeners to discount inputs
        document.querySelectorAll('.discount-input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
        
        // Calculate totals on page load
        calculateTotals();
        
        // Add form validation
        document.getElementById('validateAndSubmit').addEventListener('click', function() {
            const form = document.getElementById('editForm');
            const partNumberInputs = form.querySelectorAll('input[name="part_number[]"]');
            const priceInputs = form.querySelectorAll('input[name="price[]"]');
            let isValid = true;
            let emptyFields = [];
            
            // Check part numbers
            partNumberInputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    isValid = false;
                    emptyFields.push(`Part Number untuk item #${index + 1}`);
                }
            });
            
            // Check prices
            priceInputs.forEach((input, index) => {
                const price = input.value.replace(/,/g, '');
                if (!price || price === '0') {
                    isValid = false;
                    emptyFields.push(`Harga Satuan untuk item #${index + 1}`);
                }
            });
            
            if (!isValid) {
                // Create a message with all empty fields
                const message = 'Mohon lengkapi data berikut sebelum menyimpan:\n- ' + 
                                emptyFields.join('\n- ');
                
                alert(message);
            } else {
                // If all fields are filled, submit the form
                form.submit();
            }
        });
    });
</script>
@endpush 