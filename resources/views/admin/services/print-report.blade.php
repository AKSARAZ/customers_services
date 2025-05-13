<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Layanan Pelanggan PLN</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            color: #333;
            background-color: #f9f9f9;
        }
        .report-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            position: relative;
        }
        .header-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: 80px;
            background-color: #0d6efd;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .header-title {
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .header-subtitle {
            color: #6c757d;
            font-size: 1rem;
        }
        .filter-info {
            margin-bottom: 25px;
            font-size: 0.9rem;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #0d6efd;
        }
        .summary-info {
            margin-bottom: 30px;
        }
        .summary-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .summary-card:hover {
            transform: translateY(-5px);
        }
        .summary-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #0d6efd;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        }
        .table thead th {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            border: none;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .table td {
            vertical-align: middle;
        }
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        /* Perbaikan untuk tampilan print */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                background-color: white;
            }
            .report-container {
                box-shadow: none;
                padding: 15px;
            }
            .summary-card:hover {
                transform: none;
            }
            .table-responsive {
                box-shadow: none;
                overflow: visible !important;
            }
            .page-header {
                margin-bottom: 20px;
            }
            .header-title {
                font-size: 1.5rem;
            }
            /* Perbaikan khusus untuk header tabel */
            .table thead th {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                display: table-cell !important;
            }
            .table {
                width: 100% !important;
                page-break-inside: auto;
            }
            .table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .table thead {
                display: table-header-group;
            }
            .table tfoot {
                display: table-footer-group;
            }
            .badge {
                border: 1px solid #000;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="container report-container">
        <!-- Tombol kembali dan cetak (hanya tampil di browser) -->
        <div class="no-print mb-4">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Cetak Sekarang
            </button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Layanan
            </a>
        </div>
        
        <!-- Header Laporan -->
        <div class="page-header">
            <div class="header-logo">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="header-title">LAPORAN LAYANAN PELANGGAN PLN</h1>
            <p class="header-subtitle">Laporan Lengkap dan Terperinci</p>
            <p class="mt-3">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        </div>
        
        <!-- Informasi Filter -->
        <div class="filter-info">
            <strong><i class="fas fa-filter mr-1"></i> Filter yang diterapkan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($filterInfo as $info)
                    <li>{{ $info }}</li>
                @endforeach
            </ul>
        </div>
        
        <!-- Ringkasan Informasi -->
        <div class="summary-info">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card summary-card h-100">
                        <div class="card-body text-center py-4">
                            <div class="summary-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h6 class="card-title">Total Layanan</h6>
                            <h3 class="card-text font-weight-bold text-primary">{{ $services->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card summary-card h-100">
                        <div class="card-body text-center py-4">
                            <div class="summary-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="card-title">Layanan Selesai</h6>
                            <h3 class="card-text font-weight-bold text-success">{{ $completedServices }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card summary-card h-100">
                        <div class="card-body text-center py-4">
                            <div class="summary-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <h6 class="card-title">Layanan Pending</h6>
                            <h3 class="card-text font-weight-bold text-warning">{{ $pendingServices }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card summary-card h-100">
                        <div class="card-body text-center py-4">
                            <div class="summary-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h6 class="card-title">Total Estimasi Biaya</h6>
                            <h3 class="card-text font-weight-bold text-primary">
                                <span style="font-size: 0.8em;">Rp</span> {{ number_format($totalEstimatedCost, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabel Layanan -->
        <table class="table table-bordered table-striped mb-0">
            <thead>
                <tr>
                    <th class="text-center" width="40">#</th>
                    <th>Nama Pelanggan</th>
                    <th>Deskripsi Layanan</th>
                    <th class="text-center">Status</th>
                    <th>Daya Pilihan</th>
                    <th>Alamat</th>
                    <th>Estimasi Biaya</th>
                    <th>Kontak</th>
                </tr>
            </thead>
            <tbody>
                @if($services->count() > 0)
                    @foreach($services as $index => $service)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $service->customer_name }}</td>
                        <td>{{ $service->service_description ?? 'N/A' }}</td>
                        <td class="text-center">
                            @if($service->service_status == 'completed')
                                <span class="badge badge-success px-3 py-2">Selesai</span>
                            @elseif($service->service_status == 'pending')
                                <span class="badge badge-warning px-3 py-2">Pending</span>
                            @elseif($service->service_status == 'cancelled')
                                <span class="badge badge-danger px-3 py-2">Dibatalkan</span>
                            @else
                                <span class="badge badge-secondary px-3 py-2">{{ $service->service_status }}</span>
                            @endif
                        </td>
                        <td>{{ $service->powerOption->power_value ?? 'N/A' }}</td>
                        <td>{{ $service->contact_address ?? 'N/A' }}</td>
                        <td>Rp {{ number_format($service->estimated_cost, 0, ',', '.') }}</td>
                        <td>{{ $service->phone }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-3 d-block"></i>
                            <p class="mb-0">Tidak ada layanan yang sesuai dengan filter</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="report-footer">
            <p class="small text-muted mb-0">
                <i class="fas fa-bolt mr-1"></i> PT PLN (Persero) - Laporan ini dibuat secara otomatis pada {{ date('d M Y, H:i') }}
                <br>© {{ date('Y') }} Layanan Pelanggan PLN
            </p>
        </div>
    </div>

    <!-- Script untuk memastikan header tabel tetap terlihat saat dicetak -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan header tabel tetap terlihat saat dicetak
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('print') && urlParams.get('print') === 'true') {
                window.print();
            }
        });
    </script>
</body>
</html>