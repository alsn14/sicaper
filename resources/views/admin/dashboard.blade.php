@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<style>
    .dashboard-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
        padding: 25px 15px;
        transition: all 0.3s ease-in-out;
        border-top: 4px solid #003366;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .dashboard-icon {
        font-size: 32px;
        color: #003366;
        margin-bottom: 10px;
    }

    .dashboard-title {
        font-size: 16px;
        font-weight: bold;
        color: #003366;
    }

    .dashboard-section {
        margin-top: 20px;
    }

    .dashboard-header {
        color: #003366;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <h3 class="dashboard-header mb-4">Selamat Datang di Dashboard SICAPER</h3>
    <p><em>Badan Pusat Statistik Kota Sukabumi</em></p>

    <div class="row dashboard-section">
        @php
            $menu = [
                ['route' => 'barang', 'icon' => 'fas fa-box-open', 'label' => 'Barang'],
                ['route' => 'barang.jenis', 'icon' => 'fas fa-tags', 'label' => 'Jenis Barang'],
                ['route' => 'barang.satuan', 'icon' => 'fas fa-cubes', 'label' => 'Satuan Barang'],
                ['route' => 'room', 'icon' => 'fas fa-door-open', 'label' => 'Ruang'],
                ['route' => 'transaksi.masuk', 'icon' => 'fas fa-tools', 'label' => 'Service'],
                ['route' => 'regresi.pengingat', 'icon' => 'fas fa-bell', 'label' => 'Prediksi Jadwal Service'],
                ['route' => 'laporan.masuk', 'icon' => 'fas fa-file-alt', 'label' => 'Laporan'],
                ['route' => 'settings.profile', 'icon' => 'fas fa-user-cog', 'label' => 'Setting Profile'],
                ['route' => 'settings.employee', 'icon' => 'fas fa-users-cog', 'label' => 'Setting Pegawai'],
            ];
        @endphp

        @foreach($menu as $item)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route($item['route']) }}" class="text-decoration-none">
                    <div class="dashboard-card">
                        <div class="dashboard-icon"><i class="{{ $item['icon'] }}"></i></div>
                        <div class="dashboard-title">{{ $item['label'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
