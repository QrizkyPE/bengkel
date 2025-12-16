@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Buat Permintaan Sparepart</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('requests.store') }}" method="POST" id="createForm">
                            @csrf

                            @if(request()->has('work_order'))
                                <input type="hidden" name="work_order_id" value="{{ request('work_order') }}">
                            @endif

                            <div class="mb-3 position-relative">
                                <label for="sparepart_name" class="form-label">Nama Sparepart</label>
                                <input type="text" class="form-control @error('sparepart_name') is-invalid @enderror"
                                    id="sparepart_name" name="sparepart_name" value="{{ old('sparepart_name') }}" 
                                    autocomplete="off" required>
                                <input type="hidden" id="sparepart_id" name="sparepart_id" value="{{ old('sparepart_id') }}">
                                <div id="sparepart_suggestions" class="list-group" style="position: absolute; z-index: 1000; display: none; max-height: 200px; overflow-y: auto; width: 100%; top: 100%; margin-top: 2px;"></div>
                                @error('sparepart_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <!-- <small class="text-muted">Mulai ketik untuk mencari sparepart dari database</small> -->
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Jumlah</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small id="stock_info" class="text-muted" style="display: none;"></small>
                            </div>

                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan"
                                    required>
                                    <option value="" disabled {{ old('satuan') ? '' : 'selected' }}>Pilih Satuan</option>
                                    <option value="PCS" {{ old('satuan') == 'PCS' ? 'selected' : '' }}>PCS</option>
                                    <option value="BH" {{ old('satuan') == 'BH' ? 'selected' : '' }}>BH</option>
                                    <option value="LT" {{ old('satuan') == 'LT' ? 'selected' : '' }}>LT</option>
                                    <option value="SET" {{ old('satuan') == 'SET' ? 'selected' : '' }}>SET</option>
                                    <option value="UN" {{ old('satuan') == 'UN' ? 'selected' : '' }}>UN</option>
                                    <option value="BTL" {{ old('satuan') == 'BTL' ? 'selected' : '' }}>BTL</option>
                                </select>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Satuan akan otomatis terisi saat memilih sparepart dari database</small>
                            </div>

                            <div class="mb-3">
                                <label for="kebutuhan_part" class="form-label">Kebutuhan Part (Opsional)</label>
                                <input type="text" class="form-control @error('kebutuhan_part') is-invalid @enderror"
                                    id="kebutuhan_part" name="kebutuhan_part" value="{{ old('kebutuhan_part') }}">
                                @error('kebutuhan_part')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan"
                                    name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .card {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                margin-top: 2rem;
            }

            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }

            .form-label {
                font-weight: 500;
            }

            .btn {
                padding: 0.5rem 1.5rem;
            }
        </style>
    @endpush

    @push('styles')
        <style>
            #sparepart_suggestions {
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
                background-color: white;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            #sparepart_suggestions .list-group-item {
                cursor: pointer;
                border: none;
                border-bottom: 1px solid #dee2e6;
            }
            #sparepart_suggestions .list-group-item:hover {
                background-color: #f8f9fa;
            }
            #sparepart_suggestions .list-group-item:last-child {
                border-bottom: none;
            }
            .position-relative {
                position: relative;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('createForm');
                const submitBtn = document.getElementById('submitBtn');
                const sparepartNameInput = document.getElementById('sparepart_name');
                const sparepartIdInput = document.getElementById('sparepart_id');
                const satuanSelect = document.getElementById('satuan');
                const quantityInput = document.getElementById('quantity');
                const suggestionsDiv = document.getElementById('sparepart_suggestions');
                const stockInfo = document.getElementById('stock_info');
                let searchTimeout;

                // Container is already position-relative from HTML

                // Autocomplete functionality
                sparepartNameInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    
                    clearTimeout(searchTimeout);
                    
                    if (query.length < 2) {
                        suggestionsDiv.style.display = 'none';
                        sparepartIdInput.value = '';
                        satuanSelect.value = '';
                        satuanSelect.removeAttribute('readonly');
                        stockInfo.style.display = 'none';
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        fetch(`{{ route('admin.spareparts.search') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.length === 0) {
                                    suggestionsDiv.style.display = 'none';
                                    return;
                                }

                                suggestionsDiv.innerHTML = '';
                                data.forEach(function(sparepart) {
                                    const item = document.createElement('div');
                                    item.className = 'list-group-item';
                                    item.innerHTML = `
                                        <strong>${sparepart.nama}</strong><br>
                                        <small class="text-muted">Stok: ${sparepart.jumlah} ${sparepart.satuan}</small>
                                    `;
                                    item.addEventListener('click', function() {
                                        sparepartNameInput.value = sparepart.nama;
                                        sparepartIdInput.value = sparepart.id;
                                        satuanSelect.value = sparepart.satuan;
                                        satuanSelect.style.backgroundColor = '#e9ecef';
                                        satuanSelect.style.cursor = 'default';
                                        suggestionsDiv.style.display = 'none';
                                        
                                        // Show stock info
                                        stockInfo.textContent = `Stok tersedia: ${sparepart.jumlah} ${sparepart.satuan}`;
                                        stockInfo.style.display = 'block';
                                        
                                        // Set max quantity
                                        quantityInput.setAttribute('max', sparepart.jumlah);
                                        
                                        // Check if quantity exceeds stock
                                        if (parseInt(quantityInput.value) > sparepart.jumlah) {
                                            quantityInput.value = sparepart.jumlah;
                                        }
                                    });
                                    suggestionsDiv.appendChild(item);
                                });
                                suggestionsDiv.style.display = 'block';
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }, 300);
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!sparepartNameInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                        suggestionsDiv.style.display = 'none';
                    }
                });

                // Check quantity against stock
                quantityInput.addEventListener('input', function() {
                    const sparepartId = sparepartIdInput.value;
                    if (sparepartId && stockInfo.textContent) {
                        const stockMatch = stockInfo.textContent.match(/Stok tersedia: (\d+)/);
                        if (stockMatch) {
                            const availableStock = parseInt(stockMatch[1]);
                            const requestedQuantity = parseInt(this.value);
                            if (requestedQuantity > availableStock) {
                                this.setCustomValidity(`Jumlah melebihi stok tersedia (${availableStock})`);
                                this.reportValidity();
                            } else {
                                this.setCustomValidity('');
                            }
                        }
                    }
                });

                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Disable the submit button to prevent double submission
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                    // Check required fields
                    const sparepart_name = document.getElementById('sparepart_name').value;
                    const quantity = document.getElementById('quantity').value;
                    const satuan = document.getElementById('satuan').value;

                    if (!sparepart_name || !quantity || !satuan) {
                        alert('Harap isi semua field yang wajib diisi');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
                        return;
                    }

                    // Submit the form
                    console.log('Submitting form...');
                    this.submit();
                });
            });
        </script>
    @endpush
@endsection