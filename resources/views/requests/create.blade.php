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
                    <form action="{{ route('requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="sparepart_name" class="form-label">Nama Sparepart</label>
                            <input type="text" 
                                class="form-control @error('sparepart_name') is-invalid @enderror" 
                                id="sparepart_name" 
                                name="sparepart_name" 
                                value="{{ old('sparepart_name') }}" 
                                required>
                            @error('sparepart_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" 
                                class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" 
                                name="quantity" 
                                value="{{ old('quantity') }}" 
                                min="1" 
                                required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" 
                                class="form-control @error('satuan') is-invalid @enderror" 
                                id="satuan" 
                                name="satuan" 
                                value="{{ old('satuan') }}" 
                                required>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kebutuhan_part" class="form-label">Kebutuhan Part (Opsional)</label>
                            <input type="text" 
                                class="form-control @error('kebutuhan_part') is-invalid @enderror" 
                                id="kebutuhan_part" 
                                name="kebutuhan_part" 
                                value="{{ old('kebutuhan_part') }}">
                            @error('kebutuhan_part')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea 
                                class="form-control @error('keterangan') is-invalid @enderror" 
                                id="keterangan" 
                                name="keterangan" 
                                rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
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
@endsection
