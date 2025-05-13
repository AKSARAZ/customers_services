<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-bolt"></i>
    </div>
    <div class="sidebar-brand-text mx-3">PLN Admin</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <!-- Nav Item - Daftar Layanan -->
  <li class="nav-item {{ Request::routeIs('admin.services.index') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.services.index') }}">
      <i class="fas fa-fw fa-list"></i>
      <span>Daftar Pelanggan</span></a>
  </li>

  <!-- Nav Item - Pemasangan Baru -->
  <li class="nav-item {{ Request::routeIs('admin.services.create') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.services.create') }}">
      <i class="fas fa-fw fa-plus-circle"></i>
      <span>Instalasi Listrik</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Laporan
  </div>

  <!-- Nav Item - Reports -->
  <li class="nav-item {{ Request::routeIs('admin.services.print-report') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.services.print-report') }}">
      <i class="fas fa-print fa-fw"></i>
      <span>Cetak Laporan</span></a>
  </li>

  <!-- Nav Item - Export CSV -->
  <li class="nav-item {{ Request::routeIs('admin.services.exportData') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.services.exportData') }}">
      <i class="fas fa-fw fa-file-csv"></i>
      <span>Export Data CSV</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
<!-- End of Sidebar -->