@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Permintaan Sparepart</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('requests.create') }}" class="btn btn-primary">Buat Permintaan</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Jumlah</th>
                <th>Kebutuhan Part</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $index => $request)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $request->sparepart_name }}</td>
                <td>{{ $request->quantity }}</td>
                <td>{{ $request->kebutuhan_part ?? '-' }}</td>
                <td>{{ $request->keterangan ?? '-' }}</td>
                <td>
                    <a href="{{ route('requests.edit', $request) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('requests.destroy', $request) }}" method="POST" class="d-inline delete-form">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add confirmation dialog for delete buttons
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus permintaan ini?')) {
                    this.submit();
                }
            });
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
