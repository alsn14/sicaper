@extends('layouts.app')


@section('content')
<style>
    .table th {
        background-color: #007bff;
        color: white;
        font-weight: 600;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .table td, .table th {
        padding: 12px 10px;
        font-size: 14px;
        text-align: center;
    }

    .table th {
        vertical-align: middle;
    }

    .table td:first-child, .table td:nth-child(2) {
        text-align: left; /* Nama Barang dan Jenis Barang tetap rata kiri */
    }

    .table {
        table-layout: auto;
        width: 100%;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    h3 {
        color: #343a40;
        font-weight: 600;
    }

    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .rekomendasi {
        padding: 4px 10px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 20px;
        display: inline-block;
        color: white;
    }

    .tinggi {
        background-color: #dc3545; /* merah */
    }

    .sedang {
        background-color: #ffc107; /* kuning */
        color: black;
    }

    .rendah {
        background-color: #28a745; /* hijau */
    }

    h3 {
        font-weight: 600;
        font-size: 22px;
        color: #003366;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }


</style>


<div class="card-custom">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Hasil Prediksi Jadwal Service</h3>
        <a href="{{ url('/generate-prediksi-service') }}" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2">
            Generate Jadwal Service Dengan AI
        </a>
    </div>
    
    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!--th>ID Barang</th-->
                    <th>Nama Barang</th>
                    <th>Jenis Barang</th>
                    <th>Tanggal Pembelian</th>
                    <th>Tanggal Terakhir Service</th>
                    <th>Prediksi Jadwal Service</th>
                    <th>Rekomendasi</th> {{-- Tambahan --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr>
                    <!--td>{{ $item->item_id }}</td-->
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jenis_barang }}</td>
                    <td>{{ $item->tanggal_pembelian }}</td>
                    <td>
                        @if ($item->tanggal_service_terakhir)
                            {{ $item->tanggal_service_terakhir }}
                        @else
                            <span class="text-danger">Belum Pernah Diservice</span>
                        @endif
                    </td>
                    <td>{{ $item->jadwal_service }}</td>
                    <td>
                        @if($item->rekomendasi == 'Prioritas Tinggi')
                            <span class="rekomendasi tinggi">Prioritas Tinggi</span>
                        @elseif($item->rekomendasi == 'Prioritas Sedang')
                            <span class="rekomendasi sedang">Prioritas Sedang</span>
                        @else
                            <span class="rekomendasi rendah">Prioritas Rendah</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

