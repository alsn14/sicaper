<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
    <img src="{{ asset('logo-bps.png') }}" alt="Logo BPS" class="brand-image img-circle elevation-3">
    <div class="brand-text font-weight-bold">
      SICAPER<br>
      <small class="text-white-50">BPS Kota Sukabumi</small>
    </div>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- User Panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ asset('user.png') }}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block text-white">{{ Auth::user()->name }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2 text-capitalize">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <li class="nav-header text-white">{{ __('menu') }}</li>
        
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>{{ __('dashboard') }}</p>
          </a>
        </li>

        <li class="nav-item">
              <a href="{{ route('barang') }}" class="nav-link">
                <i class="nav-icon fas fa-box-open"></i>
                <p>{{ __('goods') }}</p>
              </a>
            </li>

        <li class="nav-item has-treeview {{ request()->is('master/*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link text-white">
            <i class="nav-icon fas fa-box"></i>
            <p>
              {{ __('master of goods') }}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('barang.jenis') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('category') }}</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('barang.satuan') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('unit') }}</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('barang.merk') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('brand') }}</p>
              </a>
            </li>
          </ul>
        </li>
        
        <li class="nav-item has-treeview {{ request()->is('master/*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link text-white">
            <i class="nav-icon fas fa-door-open"></i>
            <p>
              {{ __('Ruangan') }}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('room') }}" class="nav-link text-white">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>{{ __('rooms') }}</p>
                </a>
              </li>            </li>
            <li class="nav-item">
              <a href="{{ route('pic') }}" class="nav-link text-white {{ request()->routeIs('pic') ? 'active' : '' }}">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('penanggung jawab') }}</p>
              </a>
            </li>
          </ul>
        

        <li class="nav-item has-treeview {{ request()->is('transaksi/*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link text-white">
            <i class="nav-icon fas fa-exchange-alt"></i>
            <p>
              {{ __('Service') }}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('transaksi.masuk') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('Service Barang') }}</p>
              </a>
            </li>
            {{-- Tambahkan ini --}}
           <!--li class="nav-item">
            <a href="{{ route('pengingat.index') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>Pengingat Service</p>
            </a>
            </li-->
            <li class="nav-item">
            <a href="{{ route('regresi.pengingat') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>Prediksi Service</p>
            </a>
            </li>

          </ul>
          
        </li>


        <li class="nav-item has-treeview {{ request()->is('laporan/*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link text-white">
            <i class="nav-icon fas fa-print"></i>
            <p>
              {{ __('report') }}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('laporan.masuk') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('laporan service') }}</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-header text-white">{{ __('others') }}</li>

        <li class="nav-item has-treeview {{ request()->is('settings/*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link text-white">
            <i class="nav-icon fas fa-cog"></i>
            <p>
              {{ __('setting') }}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            @if(Auth::user()->role->name != 'employee')
            <li class="nav-item">
              <a href="{{ route('settings.employee') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('employee') }}</p>
              </a>
            </li>
            @endif
            <li class="nav-item">
              <a href="{{ route('settings.profile') }}" class="nav-link text-white">
                <i class="fas fa-angle-right nav-icon"></i>
                <p>{{ __('profile') }}</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('login.delete') }}" class="nav-link text-white">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>{{ __('messages.logout') }}</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
