@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('contents')
<!-- Dashboard Stats Cards -->
<div class="row mb-4">
    <!-- Total Pelanggan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pelanggan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $services->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selesai Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Layanan Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $services->where('service_status', 'completed')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Layanan Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $services->where('service_status', 'pending')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estimasi Biaya Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Estimasi Biaya</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($services->sum('estimated_cost'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="card shadow mb-4 border-0 card-data-tables">
    <!-- Card Header - Enhanced with gradient -->
    <div class="card-header py-3 bg-gradient-primary-to-secondary">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users mr-2"></i> Data Pelanggan
            </h6>
            <div class="d-flex">
                <!-- Filter dropdown -->
                <div class="dropdown mr-2">
                    <div class="dropdown-menu dropdown-menu-right shadow" 
                         aria-labelledby="filterDropdown">
                        <h6 class="dropdown-header">Status Layanan</h6>
                        <a class="dropdown-item {{ request('status') == '' ? 'active' : '' }}" 
                           href="{{ route('admin.services.index') }}">Semua</a>
                        <a class="dropdown-item {{ request('status') == 'completed' ? 'active' : '' }}" 
                           href="{{ route('admin.services.index', ['status' => 'completed']) }}">Selesai</a>
                        <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" 
                           href="{{ route('admin.services.index', ['status' => 'pending']) }}">Pending</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Pengurutan</h6>
                        <a class="dropdown-item" href="{{ route('admin.services.index', ['sort' => 'newest']) }}">Terbaru</a>
                        <a class="dropdown-item" href="{{ route('admin.services.index', ['sort' => 'oldest']) }}">Terlama</a>
                        <a class="dropdown-item" href="{{ route('admin.services.index', ['sort' => 'cost_high']) }}">Biaya Tertinggi</a>
                        <a class="dropdown-item" href="{{ route('admin.services.index', ['sort' => 'cost_low']) }}">Biaya Terendah</a>
                    </div>
                </div>
                
                <!-- Add new customer button -->
                <a href="{{ route('admin.services.create') }}" class="btn btn-light btn-icon-split">
                    <span class="icon text-white-50 bg-primary">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Tambah Baru</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search Results Notification -->
        @if(request()->has('search') && !empty(request('search')))
            <div class="alert alert-info fade-in">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-search mr-2"></i> Hasil pencarian untuk: 
                        <strong>{{ request('search') }}</strong>
                        ({{ $services->total() }} hasil)
                    </div>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        @endif

        <!-- No Results Message -->
        @if($services->count() == 0)
            <div class="text-center py-5">
                <img src="{{ asset('img/illustrations/no-data.svg') }}" alt="No Data" class="img-fluid mb-3" style="max-width: 200px;">
                <h4 class="text-gray-500 font-weight-light">
                    @if(request()->has('search') && !empty(request('search')))
                        Tidak ditemukan hasil untuk pencarian ini.
                    @else
                        Belum ada data pelanggan.
                    @endif
                </h4>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus mr-2"></i> Tambah Pelanggan Baru
                </a>
            </div>
        @else
            <!-- Table displaying the services - with enhanced styling -->
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Pelanggan</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Estimasi Biaya</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $index => $service)
                        <tr class="service-row">
                            <td class="text-center">{{ $index + 1 + ($services->currentPage() - 1) * $services->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-{{ $service->service_status == 'completed' ? 'success' : 'warning' }}">
                                        {{ substr($service->customer_name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-weight-bold">{{ $service->customer_name }}</div>
                                        <div class="small text-gray-600">ID: #{{ $service->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($service->service_description, 50) }}</td>
                            <td class="text-center">
                                @if($service->service_status == 'completed')
                                    <span class="status-badge completed">
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="status-badge pending">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($service->estimated_cost, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="far fa-calendar-alt text-gray-500 mr-2"></i>
                                    {{ $service->created_at->format('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Detail button -->
                                    <a href="{{ route('admin.services.show', $service->id) }}" 
                                       class="btn btn-sm action-btn detail-btn" data-toggle="tooltip" 
                                       data-placement="top" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit button -->
                                    <a href="{{ route('admin.services.edit', $service->id) }}" 
                                       class="btn btn-sm action-btn edit-btn" data-toggle="tooltip" 
                                       data-placement="top" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Mark as Completed (if status is pending) -->
                                    @if($service->service_status == 'pending')
                                    <form action="{{ route('admin.services.updateStatus', $service->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="service_status" value="completed">
                                        <button type="submit" class="btn btn-sm action-btn complete-btn" 
                                                data-toggle="tooltip" data-placement="top" title="Tandai Selesai"
                                                onclick="return confirm('Tandai layanan ini sebagai selesai?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- Delete button (for both pending and completed status) -->
                                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm action-btn delete-btn" 
                                                data-toggle="tooltip" data-placement="top" title="Hapus"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data layanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination with info -->
            <div class="row mt-4">
                <div class="col-md-6 text-muted">
                    Menampilkan {{ $services->firstItem() ?? 0 }} - {{ $services->lastItem() ?? 0 }} dari {{ $services->total() }} data
                </div>
                <div class="col-md-6">
                    <div class="pagination-container">
                        {{ $services->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Custom Styling -->
<style>
/* Card Zoom Effect */
.zoom-effect {
    transition: transform 0.3s;
}

.zoom-effect:hover {
    transform: translateY(-5px);
}

/* Background gradient for card header */
.bg-gradient-primary-to-secondary {
    background: linear-gradient(45deg, #4e73df, #36b9cc);
}

/* Table Styling */
.card-data-tables {
    border-radius: 8px;
    overflow: hidden;
}

.table thead th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-top: none;
}

.table tbody tr {
    transition: all 0.2s;
}

.table tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.06);
}

/* Avatar Styling */
.avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.bg-success {
    background: linear-gradient(135deg, #1cc88a, #169a6f);
}

.bg-warning {
    background: linear-gradient(135deg, #f6c23e, #dda20a);
}

/* Status Badge Styling */
.status-badge {
    padding: 5px 10px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.status-badge.completed {
    color: #1cc88a;
    background-color: rgba(28, 200, 138, 0.1);
    border: 1px solid rgba(28, 200, 138, 0.2);
}

.status-badge.pending {
    color: #f6c23e;
    background-color: rgba(246, 194, 62, 0.1);
    border: 1px solid rgba(246, 194, 62, 0.2);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: center;
}

.action-btn {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
    transition: all 0.2s;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.detail-btn {
    color: #4e73df;
    background-color: rgba(78, 115, 223, 0.1);
}

.detail-btn:hover {
    background-color: #4e73df;
    color: white;
}

.edit-btn {
    color: #36b9cc;
    background-color: rgba(54, 185, 204, 0.1);
}

.edit-btn:hover {
    background-color: #36b9cc;
    color: white;
}

.complete-btn {
    color: #1cc88a;
    background-color: rgba(28, 200, 138, 0.1);
}

.complete-btn:hover {
    background-color: #1cc88a;
    color: white;
}

.delete-btn {
    color: #e74a3b;
    background-color: rgba(231, 74, 59, 0.1);
}

.delete-btn:hover {
    background-color: #e74a3b;
    color: white;
}

/* Search input styling */
.search-box {
    width: 250px;
}

.search-input {
    border-radius: 20px 0 0 20px !important;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
}

.search-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.search-input + .input-group-append .btn {
    border-radius: 0 20px 20px 0 !important;
    z-index: 0;
}

/* Animation */
.fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Pagination container */
.pagination-container {
    display: flex;
    justify-content: flex-end;
}

.pagination-container .pagination {
    border-radius: 30px;
    background: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 5px;
}

.pagination-container .page-item .page-link {
    border-radius: 20px;
    margin: 0 3px;
    color: #4e73df;
    border: none;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-container .page-item.active .page-link {
    background-color: #4e73df;
    color: white;
}

.pagination-container .page-item .page-link:hover {
    background-color: #eaecf4;
}

/* Enhanced dropdown */
.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

.dropdown-item {
    border-radius: 0.25rem;
    padding: 0.5rem 1.5rem;
}

.dropdown-item:active, .dropdown-item.active {
    background-color: #4e73df;
    color: white;
}

.dropdown-header {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.05rem;
    text-transform: uppercase;
    padding: 0.5rem 1.5rem;
    margin-top: 0;
    margin-bottom: 0;
    color: #b7b9cc;
}

/* Button icon split */
.btn-icon-split .icon {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem 0 0 0.25rem;
}

.btn-icon-split .text {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 0 0.25rem 0.25rem 0;
}

/* Tooltip custom style */
.tooltip .tooltip-inner {
    background-color: #4e73df;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.tooltip.bs-tooltip-top .arrow::before {
    border-top-color: #4e73df;
}
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Add animation class to elements when they come into view
        $(window).scroll(function() {
            $('.card, .service-row').each(function() {
                var position = $(this).offset().top;
                var scroll = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                if (scroll > position - windowHeight + 100) {
                    $(this).addClass('fade-in');
                }
            });
        }).scroll(); // Trigger once on page load
    });
</script>
@endpush
@endsection