@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Buat Work Order</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('work_orders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="no_polisi" class="form-label">No. Polisi</label>
                            <input type="text" 
                                class="form-control @error('no_polisi') is-invalid @enderror" 
                                id="no_polisi" 
                                name="no_polisi" 
                                value="{{ old('no_polisi') }}" 
                                required>
                            @error('no_polisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kilometer" class="form-label">Kilometer</label>
                            <input type="number" 
                                class="form-control @error('kilometer') is-invalid @enderror" 
                                id="kilometer" 
                                name="kilometer" 
                                value="{{ old('kilometer') }}" 
                                required>
                            @error('kilometer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="no_spk" class="form-label">No. SPK</label>
                            <input type="text" 
                                class="form-control @error('no_spk') is-invalid @enderror" 
                                id="no_spk" 
                                name="no_spk" 
                                value="{{ old('no_spk') }}" 
                                required>
                            @error('no_spk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type_kendaraan" class="form-label">Type Kendaraan</label>
                            <input type="text" 
                                class="form-control @error('type_kendaraan') is-invalid @enderror" 
                                id="type_kendaraan" 
                                name="type_kendaraan" 
                                value="{{ old('type_kendaraan') }}" 
                                required>
                            @error('type_kendaraan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Customer</label>
                            <input type="text" 
                                class="form-control @error('customer_name') is-invalid @enderror" 
                                id="customer_name" 
                                name="customer_name" 
                                value="{{ old('customer_name') }}" 
                                required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="keluhan_option" class="form-label">Keluhan</label>
                            <select class="form-select" id="keluhan_option" name="keluhan_option" required onchange="toggleKeluhanInput()">
                                <option value="default" {{ old('keluhan_option', old('keluhan') === 'SB. 10.000 KM' ? 'default' : '') == 'default' ? 'selected' : '' }}>SB. 10.000 KM</option>
                                <option value="lainnya" {{ old('keluhan_option', old('keluhan') !== 'SB. 10.000 KM' && old('keluhan') ? 'lainnya' : '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <textarea 
                                class="form-control @error('keluhan') is-invalid @enderror mt-2" 
                                id="keluhan" 
                                name="keluhan" 
                                rows="3"
                                {{ old('keluhan_option', old('keluhan') === 'SB. 10.000 KM' ? 'default' : '') == 'default' ? 'readonly' : '' }}>{{ old('keluhan_option', old('keluhan') === 'SB. 10.000 KM' ? 'default' : '') == 'default' ? 'SB. 10.000 KM' : old('keluhan') }}</textarea>
                            @error('keluhan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @push('scripts')
                        <script>
                        function toggleKeluhanInput() {
                            var select = document.getElementById('keluhan_option');
                            var textarea = document.getElementById('keluhan');
                            if (select.value === 'default') {
                                textarea.value = 'SB. 10.000 KM';
                                textarea.setAttribute('readonly', true);
                            } else {
                                textarea.value = '';
                                textarea.removeAttribute('readonly');
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            toggleKeluhanInput();
                        });
                        </script>
                        @endpush
                        <div class="mb-3">
                                <label for="service_advisor" class="form-label">Service Advisor</label>
                                <select class="form-select @error('service_advisor') is-invalid @enderror" id="service_advisor" name="service_advisor"
                                    required>
                                    <option value="" disabled {{ old('service_advisor') ? '' : 'selected' }}>Pilih Service Advisor</option>
                                    <option value="Proma Asandi" {{ old('service_advisor') == 'Proma Asandi' ? 'selected' : '' }}>Proma Asandi</option>
                                    <option value="Anjas M." {{ old('service_advisor') == 'Anjas M.' ? 'selected' : '' }}>Anjas M.</option>
                                    <option value="Tongky Ari W." {{ old('service_advisor') == 'Tongky Ari W.' ? 'selected' : '' }}>Tongky Ari W.</option>
                                    <option value="Anggara" {{ old('service_advisor') == 'Anggara' ? 'selected' : '' }}>Anggara</option>
                                    <option value="Irwansyah" {{ old('service_advisor') == 'Irwansyah' ? 'selected' : '' }}>Irwansyah</option>
                                    <option value="Heru" {{ old('service_advisor') == 'Heru' ? 'selected' : '' }}>Heru</option>
                                    <option value="Erik Purnomo" {{ old('service_advisor') == 'Erik Purnomo' ? 'selected' : '' }}>Erik Purnomo</option>
                                </select>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        <!-- <div class="mb-3">
                            <label for="service_advisor" class="form-label">Service Advisor</label>
                            <input type="text" 
                                class="form-control @error('service_advisor') is-invalid @enderror" 
                                id="service_advisor" 
                                name="service_advisor" 
                                value="{{ old('service_advisor') }}" 
                                required>
                            @error('service_advisor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->
                        <div class="mb-3">
                            <label for="service_user" class="form-label">User</label>
                            <input type="text" 
                                class="form-control @error('service_user') is-invalid @enderror" 
                                id="service_user" 
                                name="service_user" 
                                value="{{ old('service_user') }}" 
                                required>
                            @error('service_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Work Order
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
@endsection 