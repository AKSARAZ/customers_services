@extends('layouts.app')

@section('title', 'Laporan Layanan')

@section('styles')
<style>
    .summary-card {
        transition: all 0.3s;
        border-left: 4px solid #4e73df;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .summary-card.completed {
        border-left-color: #1cc88a;
    }
    .summary-card.pending {
        border-left-color: #f6c23e;
    }
    .summary-card.cost {
        border-left-color: #36b9cc;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-completed {
        background-color: #e6f8ed;
        color: #1cc88a;
    }
    .status-pending {
        background-color: #fff8e6;
        color: #f6c23e;
    }
    .status-processing {
        background-color: #e6f0ff;
        color: #4e73df;
    }
    .status-cancelled {
        background-color: #fee6e6;
        color: #e74a3b;
    }
    .chart-container {
        height: 300px;
        margin-bottom: 30px;
    }
    .filter-form {
        background-color: #f8f9fc;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .service-table tr {
        transition: background-color 0.2s;
    }
    .service-table tr:hover {
        background-color: #f8f9fc;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: none;
            box-shadow: none;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }
        body {
            font-size: 12px;
        }
    }
</style>
@endsection

@section('contents')
<!-- Report Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Laporan Layanan</h6>
            <div>
                <button class="btn btn-sm btn-primary mr-2 no-print" id="toggleFilters">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm no-print">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Form - Initially Hidden -->
        <div class="filter-form no-print" id="filterForm" style="display: none;">
            <form action="{{ route('admin.services.report') }}" method="GET" class="row">
                <div class="col-md-3 mb-2">
                    <label for="search">Kata Kunci</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $searchKeyword ?? '' }}" placeholder="Nama atau email pelanggan...">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="date_from">Dari Tanggal</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="date_to">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 mt-2 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.services.report') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Filter Information -->
        @if($searchKeyword || request('status') || request('date_from') || request('date_to'))
            <div class="alert alert-info no-print">
                <strong><i class="fas fa-filter"></i> Filter Aktif:</strong> 
                @if($searchKeyword)
                    <span class="badge badge-light">Kata Kunci: {{ $searchKeyword }}</span>
                @endif
                @if(request('status'))
                    <span class="badge badge-light">Status: {{ request('status') }}</span>
                @endif
                @if(request('date_from'))
                    <span class="badge badge-light">Dari: {{ request('date_from') }}</span>
                @endif
                @if(request('date_to'))
                    <span class="badge badge-light">Sampai: {{ request('date_to') }}</span>
                @endif
                ({{ $services->count() }} hasil)
                <a href="{{ route('admin.services.report') }}" class="btn btn-sm btn-outline-primary float-right">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            </div>
        @endif

        <!-- Report Summary -->
        <div class="summary-info row mb-4">
            <div class="col-md-3">
                <div class="card mb-2 summary-card">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Layanan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalServices }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-2 summary-card completed">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Layanan Selesai</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedServices }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $totalServices > 0 ? ($completedServices / $totalServices) * 100 : 0 }}%" 
                                 aria-valuenow="{{ $completedServices }}" aria-valuemin="0" aria-valuemax="{{ $totalServices }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-2 summary-card pending">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Layanan Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingServices }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ $totalServices > 0 ? ($pendingServices / $totalServices) * 100 : 0 }}%" 
                                 aria-valuenow="{{ $pendingServices }}" aria-valuemin="0" aria-valuemax="{{ $totalServices }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-2 summary-card cost">
                    <div class="card-body py-2">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Estimasi Biaya</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalEstimatedCost, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Service Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Layanan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered service-table" id="serviceTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Pelanggan</th>
                                <th>Kategori Layanan</th>
                                <th>Status</th>
                                <th>Estimasi Biaya</th>
                                <th>Tanggal Dibuat</th>
                                <th class="no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $index => $service)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $service->customer_name }}</td>
                                    <td>{{ $service->service_category }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $service->service_status }}">
                                            {{ ucfirst($service->service_status) }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($service->estimated_cost, 0, ',', '.') }}</td>
                                    <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="no-print">
                                        <a href="{{ route('admin.services.show', $service->id) }}" 
                                           class="btn btn-info btn-sm" data-toggle="tooltip" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Print and Export buttons -->
        <div class="d-flex justify-content-between">
            <div>
                <a href="{{ route('admin.services.exportData') }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Ekspor Data CSV
                </a>
                <a href="{{ route('admin.services.printReport') }}" class="btn btn-primary ml-2">
                    <i class="fas fa-print"></i> Cetak Laporan
                </a>
            </div>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#serviceTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "order": [[5, 'desc']]
        });

        // Toggle filter form
        $('#toggleFilters').click(function() {
            $('#filterForm').slideToggle();
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending', 'Lainnya'],
                datasets: [{
                    data: [{{ $completedServices }}, {{ $pendingServices }}, {{ $totalServices - $completedServices - $pendingServices }}],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#4e73df'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#2e59d9'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                }
            }
        });

        // Sample data for cost chart (you would replace this with actual data)
        // This is just a placeholder - you'll need to aggregate by category in your controller
        const costCtx = document.getElementById('costChart').getContext('2d');
        const costChart = new Chart(costCtx, {
            type: 'bar',
            data: {
                labels: ['Kategori 1', 'Kategori 2', 'Kategori 3', 'Kategori 4'],
                datasets: [{
                    label: 'Estimasi Biaya (Rp)',
                    data: [{{ $totalEstimatedCost * 0.3 }}, {{ $totalEstimatedCost * 0.25 }}, {{ $totalEstimatedCost * 0.15 }}, {{ $totalEstimatedCost * 0.3 }}],
                    backgroundColor: '#36b9cc',
                    borderColor: '#2c9faf',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        $('#exportPDF').click(function() {
            window.location.href = "{{ route('admin.services.report') }}?export=pdf&search={{ $searchKeyword }}&status=" + $('#status').val() + "&date_from=" + $('#date_from').val() + "&date_to=" + $('#date_to').val();
        });
    });
</script>
@endsection