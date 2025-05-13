@extends('layouts.app')

@section('title', 'PLN Service Dashboard')

@section('contents')

<!-- Content Row -->
<div class="row">
    <!-- Total Layanan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Layanan</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 counter-number">{{ $totalServices }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-bolt fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Tampilkan perbandingan dengan bulan lalu -->
                @if(isset($totalServicesChange) && $totalServicesChange['status'] == 'increase')
                    <div class="mt-3 text-xs font-weight-bold text-success">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>{{ $totalServicesChange['percentage'] }}% lebih tinggi dari bulan lalu</span>
                    </div>
                @elseif(isset($totalServicesChange) && $totalServicesChange['status'] == 'decrease')
                    <div class="mt-3 text-xs font-weight-bold text-danger">
                        <i class="fas fa-arrow-down mr-1"></i>
                        <span>{{ $totalServicesChange['percentage'] }}% lebih rendah dari bulan lalu</span>
                    </div>
                @else
                    <div class="mt-3 text-xs font-weight-bold text-gray-500">
                        <i class="fas fa-equals mr-1"></i>
                        <span>Tidak ada perubahan dari bulan lalu</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Layanan Selesai Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Layanan Selesai</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 counter-number">{{ $completedServices }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-3">
                    @php
                        $completionRate = $totalServices > 0 ? ($completedServices / $totalServices) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ $completionRate }}%" 
                            aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="text-xs font-weight-bold text-success mt-1">{{ number_format($completionRate, 1) }}% dari total</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layanan Pending Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 zoom-effect">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Layanan Pending</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800 counter-number">{{ $pendingServices }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-hourglass-half fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-3">
                    @php
                        $pendingRate = $totalServices > 0 ? ($pendingServices / $totalServices) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" 
                            style="width: {{ $pendingRate }}%" 
                            aria-valuenow="{{ $pendingRate }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="text-xs font-weight-bold text-warning mt-1">{{ number_format($pendingRate, 1) }}% dari total</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Estimasi Biaya Total</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">
                            <span class="currency">Rp</span>
                            <span class="counter-number">{{ number_format($totalEstimatedCost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-info">
                            <i class="fas fa-money-bill-wave fa-lg text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-xs font-weight-bold text-info">
                    <i class="fas fa-info-circle mr-1"></i><span>Rata-rata Rp {{ number_format($totalServices > 0 ? $totalEstimatedCost/$totalServices : 0, 0, ',', '.') }} per layanan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Chart Area -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary-to-secondary d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-white">Analisis Layanan Berdasarkan Daya</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Opsi Grafik:</div>
                        <a class="dropdown-item chart-type active" data-type="doughnut" href="#">Doughnut Chart</a>
                        <a class="dropdown-item chart-type" data-type="pie" href="#">Pie Chart</a>
                        <a class="dropdown-item chart-type" data-type="bar" href="#">Bar Chart</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.services.print-report') }}">Lihat Laporan Lengkap</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="chart-container" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="servicePowerChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <div id="chart-legend-items" class="d-flex flex-wrap justify-content-center">
                        <!-- Legend items will be inserted here by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layanan Terbaru & Aksi -->
    <div class="col-xl-4 col-lg-5">

        <!-- Aksi Cepat -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary-to-secondary">
                <h6 class="m-0 font-weight-bold text-white">Aksi Cepat</h6>
            </div>
            <div class="card-body p-0">
                <div class="quick-action-list">
                    <a href="{{ route('admin.services.create') }}" class="quick-action-item">
                        <div class="quick-action-icon bg-primary">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div class="quick-action-text">Tambah Layanan Baru</div>
                    </a>
                    <a href="{{ route('admin.services.exportData') }}" class="quick-action-item">
                        <div class="quick-action-icon bg-info">
                            <i class="fas fa-file-export text-white"></i>
                        </div>
                        <div class="quick-action-text">Export Data CSV</div>
                    </a>
                    <a href="{{ route('admin.services.print-report') }}" class="quick-action-item">
                        <div class="quick-action-icon bg-secondary">
                            <i class="fas fa-print text-white"></i>
                        </div>
                        <div class="quick-action-text">Cetak Laporan</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Layanan Terbaru -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-primary-to-secondary">
                <h6 class="m-0 font-weight-bold text-white">Layanan Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <div class="latest-services">
                    @php
                        $latestServices = App\Models\CustomerService::with('powerOption')
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();
                    @endphp
                    
                    @if($latestServices->count() > 0)
                        @foreach($latestServices as $service)
                            <div class="service-item p-3 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="service-icon mr-3">
                                        @if($service->service_status == 'completed')
                                            <span class="icon-circle bg-success small-icon">
                                                <i class="fas fa-check text-white"></i>
                                            </span>
                                        @elseif($service->service_status == 'pending')
                                            <span class="icon-circle bg-warning small-icon">
                                                <i class="fas fa-clock text-white"></i>
                                            </span>
                                        @else
                                            <span class="icon-circle bg-primary small-icon">
                                                <i class="fas fa-bolt text-white"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-0">{{ $service->customer_name }}</h6>
                                        <div class="small text-gray-600">{{ $service->service_description }}</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-gray-600">
                                        <i class="fas fa-bolt fa-sm mr-1"></i>{{ $service->powerOption->power_value ?? 'N/A' }}
                                        <span class="mx-1">•</span>
                                        <i class="fas fa-calendar-alt fa-sm mr-1"></i>{{ $service->created_at->format('d M Y') }}
                                    </div>
                                    <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <p>Belum ada layanan yang ditambahkan</p>
                        </div>
                    @endif
                </div>
                <div class="text-center py-3">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua Layanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styling for Dashboard -->
<style>
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.small-icon {
    width: 30px;
    height: 30px;
}

.counter-number {
    font-family: 'Montserrat', sans-serif;
}

.currency {
    font-size: 14px;
    font-weight: 600;
    vertical-align: top;
}

.zoom-effect {
    transition: transform 0.3s;
}

.zoom-effect:hover {
    transform: translateY(-5px);
}

.chart-legend span {
    display: inline-block;
    margin: 0 5px;
    font-weight: 600;
}

.latest-services {
    max-height: 350px;
    overflow-y: auto;
}

.service-item {
    transition: background-color 0.2s;
}

.service-item:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.bg-gradient-primary-to-secondary {
    background: linear-gradient(45deg, #4e73df, #36b9cc);
}

.progress {
    border-radius: 0.5rem;
    background-color: rgba(0,0,0,0.05);
}

/* Custom scrollbar for the latest services */
.latest-services::-webkit-scrollbar {
    width: 8px;
}

.latest-services::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.latest-services::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.latest-services::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* New Quick Action Styles */
.quick-action-list {
    display: flex;
    flex-direction: column;
}

.quick-action-item {
    display: flex;
    align-items: center;
    padding: 15px;
    color: #333;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f0f0f0;
    text-decoration: none !important;
}

.quick-action-item:last-child {
    border-bottom: none;
}

.quick-action-item:hover {
    background-color: rgba(78, 115, 223, 0.05);
}

.quick-action-icon {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    transition: transform 0.2s;
}

.quick-action-item:hover .quick-action-icon {
    transform: scale(1.1);
}

.quick-action-text {
    font-weight: 600;
    color: #4e73df;
}

.bg-primary {
    background: linear-gradient(135deg, #4e73df, #224abe) !important;
}

.bg-info {
    background: linear-gradient(135deg, #36b9cc, #1a8a98) !important;
}

.bg-secondary {
    background: linear-gradient(135deg, #858796, #60616f) !important;
}

.dropdown-item.active, .dropdown-item:active {
    background-color: #4e73df;
    color: white;
}

/* Make sure Canvas is visible */
#chart-container {
    width: 100%;
    height: 350px;
    min-height: 350px;
    position: relative;
    background-color: #fff;
    display: block;
}

canvas#servicePowerChart {
    display: block;
    width: 100%;
    height: 350px;
}
</style>

@push('scripts')
<!-- Pastikan untuk menyertakan Chart.js sebelum script Anda -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<!-- PENTING: Gunakan versi UMD dari CountUp.js, bukan versi module -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.8/countUp.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");
    
    // Periksa ketersediaan Chart.js
    console.log("Chart.js loaded:", typeof Chart !== 'undefined');
    
    // Periksa ketersediaan CountUp
    console.log("CountUp loaded:", typeof CountUp !== 'undefined');
    
    // Periksa keberadaan elemen
    console.log("Chart container exists:", !!document.getElementById('chart-container'));
    console.log("Chart canvas exists:", !!document.getElementById('servicePowerChart'));
    
    // Animated counters - menggunakan versi UMD dari CountUp.js
    try {
        const counterElements = document.querySelectorAll('.counter-number');
        console.log("Counter elements found:", counterElements.length);
        
        counterElements.forEach(function(element) {
            const value = parseInt(element.innerText.replace(/[^\d]/g, ''));
            console.log("Counter value:", value);
            
            const countUp = new CountUp(element, value, {
                duration: 2.5,
                separator: '.',
                decimal: ','
            });
            
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error("CountUp error:", countUp.error);
            }
        });
    } catch (error) {
        console.error("Error initializing counters:", error);
    }

    // Ambil data chart dari PHP
    // Simpan dalam variabel untuk debugging
    const powerDataString = '{!! json_encode(
        App\Models\CustomerService::join("power_options", "customers_services.power_selection", "=", "power_options.id")
            ->select("power_options.description", "power_options.power_value", \DB::raw("count(*) as count"))
            ->groupBy("power_options.id", "power_options.description", "power_options.power_value")
            ->get()
    ) !!}';
    
    console.log("Power data string:", powerDataString);
    
    // Parse data sebagai JSON
    let powerData;
    try {
        powerData = JSON.parse(powerDataString);
        console.log("Power Data parsed:", powerData);
        console.log("Power Data Type:", typeof powerData);
        console.log("Power Data Length:", powerData ? powerData.length : "undefined");
    } catch (error) {
        console.error("Error parsing power data:", error);
        powerData = [];
    }
    
    // Periksa apakah data valid dan tidak kosong
    if (!powerData || powerData.length === 0) {
        console.warn("Power data is empty or invalid");
        document.getElementById('chart-container').innerHTML = 
            '<div class="text-center py-5">' +
            '<i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i>' +
            '<p>Tidak ada data layanan untuk ditampilkan</p>' +
            '</div>';
        return;
    }
    
    // Siapkan data chart
    const powerLabels = powerData.map(p => p.power_value + ' VA - ' + p.description);
    const serviceCounts = powerData.map(p => p.count);
    const chartColors = [
        'rgba(78, 115, 223, 0.8)',
        'rgba(28, 200, 138, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(75, 192, 192, 0.8)'
    ];
    
    console.log("Chart data prepared:", {
        labels: powerLabels,
        counts: serviceCounts
    });
    
    // Instance chart
    let chartInstance = null;
    let currentChartType = 'doughnut';
    
    // Inisialisasi chart
    function initChart() {
        console.log("Initializing chart with type:", currentChartType);
        
        const canvas = document.getElementById('servicePowerChart');
        if (!canvas) {
            console.error("Canvas element not found");
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error("Canvas context not available");
            return;
        }
        
        // Destroy existing chart jika ada
        if (chartInstance) {
            console.log("Destroying previous chart instance");
            chartInstance.destroy();
        }
        
        // Definisikan opsi chart
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: currentChartType !== 'bar',
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} layanan (${percentage}%)`;
                        }
                    }
                }
            },
            scales: (currentChartType === 'bar') ? {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Jumlah Layanan'
                    }
                }
            } : {}
        };
        
        // Buat chart
        try {
            chartInstance = new Chart(ctx, {
                type: currentChartType,
                data: {
                    labels: powerLabels,
                    datasets: [{
                        label: 'Jumlah Layanan',
                        data: serviceCounts,
                        backgroundColor: chartColors,
                        borderColor: 'rgba(255, 255, 255, 1)',
                        borderWidth: 2
                    }]
                },
                options: chartOptions
            });
            
            console.log("Chart instance created");
            
            // Generate custom legend
            generateCustomLegend();
        } catch (error) {
            console.error("Error creating chart:", error);
        }
    }
    
    // Generate legend items
    function generateCustomLegend() {
        console.log("Generating custom legend");
        
        const legendContainer = document.getElementById('chart-legend-items');
        if (!legendContainer) {
            console.error("Legend container not found");
            return;
        }
        
        legendContainer.innerHTML = '';
        
        // Skip legend untuk bar charts
        if (currentChartType === 'bar') return;
        
        powerLabels.forEach((label, index) => {
            const item = document.createElement('div');
            item.className = 'px-2 py-1 m-1 legend-item d-flex align-items-center';
            item.innerHTML = `
                <div style="width: 12px; height: 12px; background-color: ${chartColors[index]}; margin-right: 5px; border-radius: 2px;"></div>
                <span class="small">${label} (${serviceCounts[index]})</span>
            `;
            legendContainer.appendChild(item);
        });
        
        console.log("Legend items generated");
    }
    
    // Inisialisasi chart
    try {
        console.log("Starting chart initialization");
        
        if (typeof powerData === 'object' && powerData.length > 0) {
            initChart();
            console.log("Chart initialization completed");
        } else {
            console.error("Power data is empty or not in expected format");
            document.getElementById('chart-container').innerHTML = 
                '<div class="text-center py-5">' +
                '<i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i>' +
                '<p>Tidak ada data layanan untuk ditampilkan</p>' +
                '</div>';
        }
    } catch (error) {
        console.error("Error initializing chart:", error);
    }
    
    // Chart type switcher
    document.querySelectorAll('.chart-type').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            console.log("Chart type clicked:", this.getAttribute('data-type'));
            
            // Update active state
            document.querySelectorAll('.chart-type').forEach(el => {
                el.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update chart type
            currentChartType = this.getAttribute('data-type');
            
            // Reinitialize chart
            try {
                initChart();
                console.log("Chart reinitialized to:", currentChartType);
            } catch (error) {
                console.error("Error reinitializing chart:", error);
            }
        });
    });
    
    // Debugging akhir
    setTimeout(function() {
        console.log("Final check after 1 second:");
        console.log("Chart container:", document.getElementById('chart-container'));
        console.log("Chart canvas:", document.getElementById('servicePowerChart'));
        console.log("Chart instance exists:", !!chartInstance);
    }, 1000);
});
</script>
@endpush
@endsection