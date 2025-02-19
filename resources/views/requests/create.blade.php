@extends('layouts.app')

@section('content')
<h1>Buat Permintaan Sparepart</h1>
<form action="{{ route('requests.store') }}" method="POST">
    @csrf
    <label>Nama Sparepart:</label>
    <input type="text" name="sparepart_name" required>

    <label>Jumlah:</label>
    <input type="number" name="quantity" required min="1">

    <label>Kebutuhan Part (Opsional):</label>
    <input type="text" name="kebutuhan_part">

    <label>Keterangan (Opsional):</label>
    <textarea name="keterangan"></textarea>

    <button type="submit">Simpan</button>
</form>
@endsection
