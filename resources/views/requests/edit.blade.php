@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Edit Permintaan Sparepart</h1>
        </div>
    </div>

    <form action="{{ route('requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3 position-relative">
            <label for="sparepart_name" class="form-label">Nama Sparepart</label>
            <input type="text" class="form-control @error('sparepart_name') is-invalid @enderror" 
                id="sparepart_name" name="sparepart_name" value="{{ old('sparepart_name', $request->sparepart_name) }}" autocomplete="off" required>
            <div id="sparepart-suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
            @error('sparepart_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                id="quantity" name="quantity" value="{{ old('quantity', $request->quantity) }}" required>
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="satuan" class="form-label">Satuan</label>
            <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                <option value="" disabled {{ old('satuan', $request->satuan) ? '' : 'selected' }}>Pilih Satuan</option>
                <option value="PCS" {{ old('satuan', $request->satuan) == 'PCS' ? 'selected' : '' }}>PCS</option>
                <option value="BH" {{ old('satuan', $request->satuan) == 'BH' ? 'selected' : '' }}>BH</option>
                <option value="LT" {{ old('satuan', $request->satuan) == 'LT' ? 'selected' : '' }}>LT</option>
                <option value="SET" {{ old('satuan', $request->satuan) == 'SET' ? 'selected' : '' }}>SET</option>
                <option value="UN" {{ old('satuan', $request->satuan) == 'UN' ? 'selected' : '' }}>UN</option>
                <option value="BTL" {{ old('satuan', $request->satuan) == 'BTL' ? 'selected' : '' }}>BTL</option>
            </select>
            @error('satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kebutuhan_part" class="form-label">Kebutuhan Part</label>
            <input type="text" class="form-control @error('kebutuhan_part') is-invalid @enderror" 
                id="kebutuhan_part" name="kebutuhan_part" value="{{ old('kebutuhan_part', $request->kebutuhan_part) }}">
            @error('kebutuhan_part')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $request->keterangan) }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <a href="{{ route('requests.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('sparepart_name');
    const suggestions = document.getElementById('sparepart-suggestions');

    input.addEventListener('input', function () {
        const query = this.value;
        if (query.length < 2) {
            suggestions.innerHTML = '';
            return;
        }
        fetch(`/api/spareparts/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length === 0) {
                    suggestions.style.display = 'none';
                    return;
                }
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action';
                    div.textContent = item;
                    div.onclick = () => {
                        input.value = item;
                        suggestions.innerHTML = '';
                        suggestions.style.display = 'none';
                    };
                    suggestions.appendChild(div);
                });
                suggestions.style.display = 'block';
            });
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
        }
    });
});
</script>
@endpush 