@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Daftar Catatan Service</h3>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Tanggal Service</th>
                <th>Keterangan</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->item->name }}</td>
                    <td>{{ $record->tanggal_service }}</td>
                    <td>{{ $record->keterangan ?? '-' }}</td>
                    <td>Rp{{ number_format($record->biaya ?? 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Belum ada catatan service.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
