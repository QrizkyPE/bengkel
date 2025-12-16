@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Barang Masuk</h1>
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

    <!-- Form Barang Masuk -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Barang Masuk</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.barang-masuk.store') }}" method="POST" id="barangMasukForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sparepart_id" class="form-label">Pilih Sparepart <span class="text-danger">*</span></label>
                            <select class="form-select @error('sparepart_id') is-invalid @enderror" id="sparepart_id" name="sparepart_id" required>
                                <option value="" disabled {{ old('sparepart_id') ? '' : 'selected' }}>Pilih Sparepart</option>
                                @foreach($spareparts as $sparepart)
                                <option value="{{ $sparepart->id }}" 
                                    data-satuan="{{ $sparepart->satuan }}"
                                    data-stok="{{ $sparepart->jumlah }}"
                                    {{ old('sparepart_id') == $sparepart->id ? 'selected' : '' }}>
                                    {{ $sparepart->nama }} (Stok: {{ $sparepart->jumlah }} {{ $sparepart->satuan }})
                                </option>
                                @endforeach
                            </select>
                            @error('sparepart_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jumlah_masuk" class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_masuk') is-invalid @enderror"
                                id="jumlah_masuk" name="jumlah_masuk" value="{{ old('jumlah_masuk') }}" 
                                min="1" required>
                            @error('jumlah_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="satuan_info" class="text-muted"></small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                        id="keterangan" name="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Contoh: Pembelian dari supplier, Retur dari customer, dll</small>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Barang Masuk -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-history"></i> History Barang Masuk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="barangMasukTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Sparepart</th>
                            <th>Jumlah Masuk</th>
                            <th>Stok Sebelum</th>
                            <th>Stok Sesudah</th>
                            <th>Keterangan</th>
                            <th>Ditambahkan Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangMasuk as $index => $bm)
                        @php
                            $stokSesudah = $bm->sparepart->jumlah;
                            $stokSebelum = $stokSesudah - $bm->jumlah_masuk;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $bm->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $bm->sparepart->nama }}</td>
                            <td class="text-success"><strong>+{{ $bm->jumlah_masuk }} {{ $bm->sparepart->satuan }}</strong></td>
                            <td>{{ $stokSebelum }} {{ $bm->sparepart->satuan }}</td>
                            <td class="text-primary"><strong>{{ $stokSesudah }} {{ $bm->sparepart->satuan }}</strong></td>
                            <td>{{ $bm->keterangan ?? '-' }}</td>
                            <td>{{ $bm->creator->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-header {
        border-bottom: 1px solid #dee2e6;
    }

    .table-responsive {
        margin-top: 1rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sparepartSelect = document.getElementById('sparepart_id');
        const satuanInfo = document.getElementById('satuan_info');
        const jumlahMasukInput = document.getElementById('jumlah_masuk');

        // Update satuan info when sparepart is selected
        sparepartSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const satuan = selectedOption.getAttribute('data-satuan');
                const stok = selectedOption.getAttribute('data-stok');
                satuanInfo.textContent = `Satuan: ${satuan} | Stok saat ini: ${stok} ${satuan}`;
            } else {
                satuanInfo.textContent = '';
            }
        });

        // Initialize satuan info if sparepart is pre-selected
        if (sparepartSelect.value) {
            sparepartSelect.dispatchEvent(new Event('change'));
        }

        // Initialize DataTable
        $('#barangMasukTable').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data barang masuk",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            order: [[1, 'desc']], // Sort by tanggal descending
            columnDefs: [
                { responsivePriority: 1, targets: 2 }, // Nama Sparepart
                { responsivePriority: 2, targets: 3 }, // Jumlah Masuk
                { responsivePriority: 3, targets: 1 }, // Tanggal
            ]
        });

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

