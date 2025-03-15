@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Estimasi</h5>
                </div>
                <div class="card-body">
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
                            <p><strong>Service Advisor:</strong> {{ $estimation->service_advisor }}</p>
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
                                    <td>{{ $item->part_number ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->discount }}%</td>
                                    <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                    <td class="text-end">
                                        <strong>{{ number_format($estimation->estimationItems->sum('total'), 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($estimation->notes)
                    <div class="mt-3">
                        <h6>Catatan:</h6>
                        <p>{{ $estimation->notes }}</p>
                    </div>
                    @endif

                    <div class="mt-4">
                        @if($estimation->status === 'pending')
                            <form action="{{ route('estimations.approve', $estimation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </form>

                            <form action="{{ route('estimations.reject', $estimation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">
                                Status: 
                                @if($estimation->status === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($estimation->status === 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                                pada {{ $estimation->approved_at ? $estimation->approved_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        @endif

                        <a href="{{ route('estimations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 