@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Daftar Permintaan Sparepart</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('requests.download.pdf') }}" class="btn btn-secondary me-2">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Permintaan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr style="text-align: center">
                    <th style="width: 5%">No</th>
                    <th style="width: 20%">ITEM PEKERJAAN</th>
                    <th style="width: 10%">QTY</th>
                    <th style="width: 25%">KEBUTUHAN PART DAN BAHAN</th>
                    <th style="width: 25%">KET</th>
                    <th style="width: 15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $index => $request)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $request->sparepart_name }} </td>
                    <td class="text-center">{{ $request->quantity }} {{ $request->satuan }}</td>
                    <td style="text-align: left;">{{ $request->kebutuhan_part ?? '' }}</td>
                    <td style="text-align: left;">{{ $request->keterangan ?? '' }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('requests.edit', $request) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('requests.destroy', $request) }}" method="POST" class="delete-form">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    .table-responsive {
        margin-top: 1rem;
    }
    
    .table th {
        background-color: #f8f9fa;
        vertical-align: middle;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
    }

    /* Simplified column styling */
    .table td:nth-child(4),
    .table td:nth-child(5) {
        text-align: left;
    }

    .d-flex.gap-2 {
        display: flex;
        gap: 0.5rem !important;
        justify-content: center;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        min-width: 80px;
    }

    @media (max-width: 768px) {
        .table td, .table th {
            min-width: 100px;
        }
        
        td:nth-child(4), td:nth-child(5) {
            max-width: 200px;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.25rem !important;
        }
    }
</style>
@endpush

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
