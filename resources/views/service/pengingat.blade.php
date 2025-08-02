@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Daftar Pengingat Service</h2>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <!--a href="{{ route('pengingat.generate.ai') }}" class="btn btn-primary">
    Generate Jadwal Service dengan AI
    </a-->
    <a href="{{ url('/generate-prediksi-service') }}" class="btn btn-primary">
    Generate Jadwal Service dengan AI
    </a>


    
    {{-- Tabel Barang --}}
    <table class="table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <!--<th>Keterangan</th>-->
                <th>Tanggal Service Terakhir</th>
                <th>Rekomendasi Service</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $item)
                <tr>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->item->category->name ?? '-' }}</td>
                    
                    <td>{{ \Carbon\Carbon::parse($item->date_received)->format('d-m-Y') }}</td>
                    <td>{{ $item->rekomendasi ?? '-' }}</td>
                    <td>
                        <form action="{{ route('pengingat.sudahDiservice', $item->id) }}" method="POST" onsubmit="return confirm('Yakin barang ini sudah diservice?');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Sudah Diservice</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody!>
    </table>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $("#cekPrediksiBtn").on('click', function() {
            const jenis_barang = $("select[name='jenisbarang']").val();
            const tanggal_pembelian = $("input[name='tanggal_pembelian']").val();
            const terakhir_service = $("input[name='terakhir_service']").val();

            $.ajax({
                url: "/prediksi-service",
                method: "POST",
                data: {
                    jenis_barang,
                    tanggal_pembelian,
                    terakhir_service,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#hasil-prediksi").text("Prediksi service berikutnya dalam " + response.prediksi_hari_service + " hari").show();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert("Gagal memprediksi service.");
                }
            });
        });
    });
</script>
@endsection
