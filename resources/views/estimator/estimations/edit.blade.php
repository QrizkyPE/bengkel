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
                                <p><strong>Service Advisor:</strong> {{ $estimation->workOrder->service_advisor }}</p>
                                <p><strong>User:</strong> {{ $estimation->workOrder->service_user }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                        <td>
                                            <input type="hidden" name="estimation_item_id[]" value="{{ $item->id }}">
                                            <input type="text" name="part_number[]" class="form-control" value="{{ old('part_number.'.$index, $item->part_number) }}">
                                        </td>
                                        <td class="text-center">{{ $item->serviceRequest->quantity }} {{ $item->serviceRequest->satuan }}</td>
                                        <td>
                                            <input type="text" name="price[]" class="form-control price-input" value="{{ old('price.'.$index, number_format($item->price, 0, '', ',')) }}" readonly style="background-color: #e9ecef;">
                                        </td>
                                        <td>
                                            <input type="number" name="discount[]" class="form-control discount-input" value="{{ old('discount.'.$index, $item->discount) }}" min="0" max="100" step="0.01" data-index="{{ $index }}">
                                        </td>
                                        <td>
                                            <input type="text" name="total[]" class="form-control total-input" value="{{ old('total.'.$index, number_format($item->total, 0, '', ',')) }}" data-index="{{ $index }}" data-quantity="{{ $item->serviceRequest->quantity }}" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                        <td class="text-end">
                                            <strong id="grand-total">{{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
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
        // Format number as Indonesian Rupiah (using dots for thousands)
        function formatRupiah(number) {
            return Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Parse formatted number to float
        function parseRupiah(value) {
            return parseFloat(value.toString().replace(/\./g, '').replace(/,/g, '')) || 0;
        }
        
        // Format input as Rupiah while typing
        function formatTotalInput(input) {
            const caretPos = input.selectionStart;
            const oldLength = input.value.length;
            
            // Remove all non-digits except dots and commas
            let value = input.value.replace(/[^\d.,]/g, '');
            
            // Remove all dots and commas
            value = value.replace(/[.,]/g, '');
            
            // Add dots for thousands
            if (value.length > 0) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            
            // Update the input value
            input.value = value;
            
            // Adjust caret position
            const newLength = input.value.length;
            const caretAdjust = newLength - oldLength;
            input.setSelectionRange(caretPos + caretAdjust, caretPos + caretAdjust);
        }
        
        // Calculate price from total and quantity
        // Harga Satuan = Total / Quantity
        // Discount tidak mempengaruhi harga satuan yang ditampilkan
        // Harga satuan adalah harga per unit dari total yang diinput
        function calculatePriceFromTotal(totalInput, discountInput, priceInput, quantity) {
            const total = parseRupiah(totalInput.value);
            
            if (total === 0 || quantity === 0) {
                priceInput.value = '0';
                return;
            }
            
            // Harga satuan = Total / Quantity
            // Discount tidak mempengaruhi perhitungan harga satuan
            const price = total / quantity;
            
            priceInput.value = formatRupiah(price);
        }
        
        // Calculate grand total
        function calculateGrandTotal() {
            const totalInputs = document.querySelectorAll('.total-input');
            let grandTotal = 0;
            
            totalInputs.forEach(input => {
                grandTotal += parseRupiah(input.value);
            });
            
            document.getElementById('grand-total').textContent = formatRupiah(grandTotal);
        }
        
        // Initialize total inputs
        document.querySelectorAll('.total-input').forEach(input => {
            const index = input.getAttribute('data-index');
            const quantity = parseInt(input.getAttribute('data-quantity')) || 1;
            const row = input.closest('tr');
            const priceInput = row.querySelector('.price-input');
            const discountInput = row.querySelector('.discount-input');
            
            // Format on input
            input.addEventListener('input', function() {
                formatTotalInput(this);
                calculatePriceFromTotal(this, discountInput, priceInput, quantity);
                calculateGrandTotal();
            });
            
            // Format on blur
            input.addEventListener('blur', function() {
                if (this.value === '' || this.value === '0') {
                    this.value = '0';
                    priceInput.value = '0';
                }
                calculateGrandTotal();
            });
        });
        
        // Initialize discount inputs
        // Discount tidak mempengaruhi harga satuan, jadi tidak perlu recalculate
        document.querySelectorAll('.discount-input').forEach(input => {
            // Discount hanya untuk informasi, tidak mempengaruhi harga satuan
            // Harga satuan hanya berubah saat Total berubah
        });
        
        // Calculate initial prices and grand total
        document.querySelectorAll('.total-input').forEach(input => {
            const row = input.closest('tr');
            const discountInput = row.querySelector('.discount-input');
            const priceInput = row.querySelector('.price-input');
            const quantity = parseInt(input.getAttribute('data-quantity')) || 1;
            
            calculatePriceFromTotal(input, discountInput, priceInput, quantity);
        });
        
        calculateGrandTotal();
        
        // Add form validation
        document.getElementById('validateAndSubmit').addEventListener('click', function() {
            const form = document.getElementById('editForm');
            const partNumberInputs = form.querySelectorAll('input[name="part_number[]"]');
            const totalInputs = form.querySelectorAll('input[name="total[]"]');
            let isValid = true;
            let emptyFields = [];
            
            // Check part numbers
            partNumberInputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    isValid = false;
                    emptyFields.push(`Part Number untuk item #${index + 1}`);
                }
            });
            
            // Check totals
            totalInputs.forEach((input, index) => {
                const total = parseRupiah(input.value);
                if (!total || total === 0) {
                    isValid = false;
                    emptyFields.push(`Total untuk item #${index + 1}`);
                }
            });
            
            if (!isValid) {
                // Create a message with all empty fields
                const message = 'Mohon lengkapi data berikut sebelum menyimpan:\n- ' + 
                                emptyFields.join('\n- ');
                
                alert(message);
            } else {
                // Convert formatted values to numeric and create hidden inputs
                document.querySelectorAll('.total-input').forEach((totalInput, index) => {
                    const row = totalInput.closest('tr');
                    const priceInput = row.querySelector('.price-input');
                    const quantity = parseInt(totalInput.getAttribute('data-quantity')) || 1;
                    
                    const total = parseRupiah(totalInput.value);
                    // Harga satuan = Total / Quantity (tidak mempertimbangkan discount)
                    const price = quantity > 0 ? total / quantity : 0;
                    
                    // Create hidden input for total (numeric value)
                    const totalHidden = document.createElement('input');
                    totalHidden.type = 'hidden';
                    totalHidden.name = `total[${index}]`;
                    totalHidden.value = total.toString();
                    totalInput.parentNode.appendChild(totalHidden);
                    
                    // Create hidden input for price (calculated value)
                    const priceHidden = document.createElement('input');
                    priceHidden.type = 'hidden';
                    priceHidden.name = `price[${index}]`;
                    priceHidden.value = Math.round(price).toString();
                    priceInput.parentNode.appendChild(priceHidden);
                });
                
                // If all fields are filled, submit the form
                form.submit();
            }
        });
    });
</script>
@endpush 