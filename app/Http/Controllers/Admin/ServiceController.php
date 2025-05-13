<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerOption;
use App\Models\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Construct method
     */
    public function __construct()
    {
        // Middleware dipindahkan ke routes
    }

    /**
     * Menampilkan dashboard dengan data perbandingan bulan ini vs bulan lalu
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        try {
            // Tentukan rentang bulan ini
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            
            // Tentukan rentang bulan lalu
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            
            // --- TOTAL LAYANAN ---
            // Data bulan ini
            $totalServices = CustomerService::count();
            $totalServicesThisMonth = CustomerService::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
            
            // Data bulan lalu
            $totalServicesLastMonth = CustomerService::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            
            // Hitung persentase perubahan untuk total layanan
            $totalServicesChange = $this->calculatePercentageChange($totalServicesThisMonth, $totalServicesLastMonth);
            
            // --- LAYANAN SELESAI ---
            // Data bulan ini
            $completedServices = CustomerService::where('service_status', 'completed')->count();
            $completedServicesThisMonth = CustomerService::where('service_status', 'completed')
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
            
            // Data bulan lalu
            $completedServicesLastMonth = CustomerService::where('service_status', 'completed')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            
            // Hitung persentase perubahan untuk layanan selesai
            $completedServicesChange = $this->calculatePercentageChange($completedServicesThisMonth, $completedServicesLastMonth);
            
            // --- LAYANAN PENDING ---
            // Data bulan ini
            $pendingServices = CustomerService::where('service_status', 'pending')->count();
            $pendingServicesThisMonth = CustomerService::where('service_status', 'pending')
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count();
            
            // Data bulan lalu
            $pendingServicesLastMonth = CustomerService::where('service_status', 'pending')
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            
            // Hitung persentase perubahan untuk layanan pending
            $pendingServicesChange = $this->calculatePercentageChange($pendingServicesThisMonth, $pendingServicesLastMonth);
            
            // --- TOTAL ESTIMASI BIAYA ---
            // Data bulan ini
            $totalEstimatedCost = CustomerService::sum('estimated_cost');
            $totalEstimatedCostThisMonth = CustomerService::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->sum('estimated_cost');
            
            // Data bulan lalu
            $totalEstimatedCostLastMonth = CustomerService::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('estimated_cost');
            
            // Hitung persentase perubahan untuk estimasi biaya
            $totalEstimatedCostChange = $this->calculatePercentageChange($totalEstimatedCostThisMonth, $totalEstimatedCostLastMonth);
            
            return view('admin.dashboard', compact(
                'totalServices',
                'completedServices',
                'pendingServices',
                'totalEstimatedCost',
                'totalServicesChange',
                'completedServicesChange',
                'pendingServicesChange',
                'totalEstimatedCostChange'
            ));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Helper method untuk menghitung persentase perubahan
     * 
     * @param float $currentValue Nilai saat ini
     * @param float $previousValue Nilai sebelumnya
     * @return array Array berisi persentase perubahan dan status
     */
    private function calculatePercentageChange($currentValue, $previousValue)
    {
        $percentage = 0;
        $status = 'same'; // 'increase', 'decrease', atau 'same'
        
        if ($previousValue > 0) {
            $percentage = (($currentValue - $previousValue) / $previousValue) * 100;
            $status = $percentage > 0 ? 'increase' : ($percentage < 0 ? 'decrease' : 'same');
        } elseif ($currentValue > 0) {
            // Jika nilai sebelumnya 0 dan nilai sekarang ada, maka kenaikan 100%
            $percentage = 100;
            $status = 'increase';
        }
        
        // Bulatkan persentase ke 1 desimal dan ambil nilai absolut
        return [
            'percentage' => abs(round($percentage, 1)),
            'status' => $status
        ];
    }

    /**
     * Menampilkan daftar layanan pelanggan dengan kemampuan pencarian di semua field
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = CustomerService::query();
            
            // Check if search keyword exists
            if ($request->has('search') && !empty($request->search)) {
                $keyword = $request->search;
                
                // Search in all text fields
                $query->where(function($q) use ($keyword) {
                    $q->where('customer_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('service_description', 'LIKE', "%{$keyword}%")
                      ->orWhere('contact_address', 'LIKE', "%{$keyword}%")
                      ->orWhere('phone', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%")
                      ->orWhere('service_status', 'LIKE', "%{$keyword}%")
                      // Search in estimated_cost (convert to string for LIKE comparison)
                      ->orWhereRaw('CAST(estimated_cost AS CHAR) LIKE ?', ["%{$keyword}%"]);
                      
                    // Search in power_option relation
                    $q->orWhereHas('powerOption', function($query) use ($keyword) {
                        $query->Where('power_value', 'LIKE', "%{$keyword}%");
                    });
                });
            }
            
            $services = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('admin.services.index', compact('services'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data layanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk pemasangan baru
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            // Mengambil data daya untuk dropdown
            $powerOptions = PowerOption::orderBy('id')->get();
            
            return view('admin.services.create', compact('powerOptions'));
        } catch (Exception $e) {
            return redirect()->route('admin.services.index') ->with('error', 'Terjadi kesalahan saat membuka form: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data pelanggan dan layanan ke dalam database
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'contact_address' => 'required|string',
            'phone' => 'required|string|max:20|regex:/^[0-9]+$/',
            'email' => 'required|email|max:255|unique:customers_services,email',
            'estimated_cost' => 'required|numeric|min:0',
            'power_selection' => 'required|exists:power_options,id',
        ], [
            'customer_name.required' => 'Nama pelanggan wajib diisi',
            'phone.regex' => 'Nomor telepon harus berupa angka',
            'email.unique' => 'Email sudah terdaftar dalam sistem',
            'power_selection.required' => 'Pilihan daya wajib dipilih',
        ]);

        DB::beginTransaction();
        try {
            // Menyimpan data layanan pelanggan
            $serviceData = [
                'customer_name' => $validated['customer_name'],
                'service_description' => $validated['service_description'] ?? 'Layanan Pemasangan Baru',
                'contact_address' => $validated['contact_address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'service_status' => 'pending',
                'estimated_cost' => $validated['estimated_cost'],
                'power_selection' => $validated['power_selection'],
            ];

            $service = CustomerService::create($serviceData);

            DB::commit();

            return redirect()->route('admin.services.index')
                        ->with('success', 'Layanan pelanggan berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman detail layanan pelanggan
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            // Menampilkan data layanan pelanggan berdasarkan ID
            $service = CustomerService::findOrFail($id);
            
            // Mengambil data daya untuk dropdown
            $powerOptions = PowerOption::orderBy('power_value')->get();
            
            return view('admin.services.show', compact('service', 'powerOptions'));
        } catch (Exception $e) {
            return redirect()->route('admin.services.index')
                           ->with('error', 'Layanan tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit layanan
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $service = CustomerService::findOrFail($id);
            $powerOptions = PowerOption::orderBy('id')->get();
            
            return view('admin.services.edit', compact('service', 'powerOptions'));
        } catch (Exception $e) {
            return redirect()->route('admin.services.index')
                           ->with('error', 'Data layanan pelanggan tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Update data layanan
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $service = CustomerService::findOrFail($id);
        
        // Validasi yang sama dengan store, kecuali email
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'service_description' => 'required|string',
            'contact_address' => 'required|string',
            'phone' => 'required|string|max:20|regex:/^[0-9]+$/',
            'email' => 'required|email|max:255|unique:customers_services,email,' . $id,
            'estimated_cost' => 'required|numeric|min:0',
            'power_selection' => 'exists:power_options,id', // ditambahkan untuk update power selection
        ]);

        try {
            $service->update($validated);
            
            return redirect()->route('admin.services.show', $id)
                           ->with('success', 'Data layanan pelanggan berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi untuk mengubah status layanan
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'service_status' => 'required|in:pending,completed',
        ]);

        try {
            $service = CustomerService::findOrFail($id);
            $service->service_status = $validated['service_status'];
            $service->save();

            $statusText = $validated['service_status'] === 'completed' ? 'diselesaikan' : 'dipending';
            
            return redirect()->route('admin.services.index')
                           ->with('success', "Status layanan pelanggan berhasil diubah menjadi {$statusText}!");
        } catch (Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Fungsi untuk menghapus layanan pelanggan
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Find the service by ID
            $service = CustomerService::findOrFail($id);

            // Delete the service
            $service->delete();

            return redirect()->route('admin.services.index')
                            ->with('success', 'Data layanan pelanggan berhasil dihapus!');
        } catch (Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus data layanan pelanggan: ' . $e->getMessage());
        }
    }

    public function generateReport(Request $request)
    {
        // Set filter parameters
        $searchKeyword = $request->search;
        $status = $request->status;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        
        // Base query
        $servicesQuery = CustomerService::query(); // Ganti Service menjadi CustomerService
        
        // Apply filters
        if ($searchKeyword) {
            $servicesQuery->where(function($query) use ($searchKeyword) {
                $query->where('customer_name', 'like', "%{$searchKeyword}%")
                    ->orWhere('customer_email', 'like', "%{$searchKeyword}%")
                    ->orWhere('description', 'like', "%{$searchKeyword}%");
            });
        }
        
        if ($status) {
            $servicesQuery->where('service_status', $status);
        }
        
        if ($dateFrom) {
            $servicesQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $servicesQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Get filtered services
        $services = $servicesQuery->get();
        
        // Calculate summary
        $totalServices = $services->count();
        $completedServices = $services->where('service_status', 'completed')->count();
        $pendingServices = $services->where('service_status', 'pending')->count();
        $totalEstimatedCost = $services->sum('estimated_cost');
        
        // Prepare summary data
        $summary = [
            'totalServices' => $totalServices,
            'completedServices' => $completedServices,
            'pendingServices' => $pendingServices,
            'totalEstimatedCost' => $totalEstimatedCost,
        ];
        
        // Check if export is requested
        if ($request->has('export')) {
            return $this->exportReport($services, $request->export, $summary);
        }
        
        // Return view with data
        return view('admin.services.report', compact(
            'services',
            'totalServices',
            'completedServices',
            'pendingServices',
            'totalEstimatedCost',
            'searchKeyword'
        ));
    }

    // protected function exportReport($services, $format, $summary)
    // {
    //     $filename = 'laporan_layanan_' . date('Y-m-d');
        
    //     if ($format == 'pdf') {
    //         $pdf = PDF::loadView('admin.services.pdf_report', [
    //             'services' => $services,
    //             'totalServices' => $summary['totalServices'],
    //             'completedServices' => $summary['completedServices'],
    //             'pendingServices' => $summary['pendingServices'],
    //             'totalEstimatedCost' => $summary['totalEstimatedCost'],
    //         ]);
            
    //         return $pdf->download($filename . '.pdf');
    //     }
        
    //     return redirect()->back()->with('error', 'Format ekspor tidak didukung');
    // }

    public function exportData()
    {
        try {
            // Set header untuk file CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="customer-services-export-' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['#', 'Nama Pelanggan', 'Deskripsi Layanan', 'Status Layanan', 'Estimasi Biaya', 'Daya Pilihan', 'Kontak']);
                
                // Variabel untuk nomor urut
                $counter = 1;
                
                // Menggunakan chunk untuk menangani dataset besar
                // Pastikan kita menggunakan model CustomerService yang benar yang tabel-nya 'customers_services'
                CustomerService::with('powerOption')->chunk(500, function($services) use ($file, &$counter) {
                    foreach ($services as $service) {
                        fputcsv($file, [
                            $counter++, // Nomor urut yang konsisten
                            $service->customer_name,
                            $service->service_description ?? 'N/A',
                            $service->service_status,
                            'Rp ' . number_format($service->estimated_cost, 2, ',', '.'),
                            // Akses power_value dari relasi powerOption yang merujuk ke power_selection
                            $service->powerOption->power_value ?? 'N/A',
                            $service->phone, // Kontak telepon pelanggan
                        ]);
                    }
                });

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            // Log error
            \Log::error('CSV Export error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }

    public function printReport(Request $request)
    {
        try {
            // Query dasar layanan pelanggan dengan relasi powerOption
            $query = CustomerService::with('powerOption');

            // Filter berdasarkan status layanan
            if ($request->has('status') && !empty($request->status)) {
                $query->where('service_status', $request->status);
            }

            // Filter berdasarkan rentang tanggal dengan validasi
            if ($request->has('date_from') && $request->date_from) {
                $dateFrom = $request->date_from;
                if (\Carbon\Carbon::hasFormat($dateFrom, 'Y-m-d')) {
                    $query->whereDate('created_at', '>=', $dateFrom);
                }
            }
            
            if ($request->has('date_to') && $request->date_to) {
                $dateTo = $request->date_to;
                if (\Carbon\Carbon::hasFormat($dateTo, 'Y-m-d')) {
                    $query->whereDate('created_at', '<=', $dateTo);
                }
            }

            // Pengurutan berdasarkan kolom tertentu dengan opsi asc/desc
            $sortBy = $request->sort_by ?? 'customer_name';
            $sortDirection = $request->sort_direction ?? 'asc'; // Default asc
            $allowedSortFields = ['customer_name', 'service_status', 'estimated_cost', 'created_at'];
            $allowedDirections = ['asc', 'desc'];

            if (in_array($sortBy, $allowedSortFields) && in_array($sortDirection, $allowedDirections)) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('customer_name', 'asc');
            }

            // Dapatkan layanan
            $services = $query->get();

            // Hitung statistik
            $totalEstimatedCost = $services->sum('estimated_cost');
            $completedServices = $services->where('service_status', 'completed')->count();
            $pendingServices = $services->where('service_status', 'pending')->count();
            
            // Tambahkan data daya yang digunakan dengan jumlah layanan
            $powerOptions = PowerOption::withCount(['customerServices' => function($query) use ($request) {
                if ($request->has('status') && !empty($request->status)) {
                    $query->where('service_status', $request->status);
                }
                
                if ($request->has('date_from') && $request->date_from) {
                    if (\Carbon\Carbon::hasFormat($request->date_from, 'Y-m-d')) {
                        $query->whereDate('created_at', '>=', $request->date_from);
                    }
                }
                
                if ($request->has('date_to') && $request->date_to) {
                    if (\Carbon\Carbon::hasFormat($request->date_to, 'Y-m-d')) {
                        $query->whereDate('created_at', '<=', $request->date_to);
                    }
                }
            }])->get();

            // Siapkan informasi filter untuk tampilan
            $filterInfo = $this->getFilterInfo($request);

            return view('admin.services.print-report', compact(
                'services',
                'totalEstimatedCost',
                'completedServices',
                'pendingServices',
                'powerOptions',
                'filterInfo'
            ));
        } catch (\Exception $e) {
            \Log::error('Print report error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan: ' . $e->getMessage());
        }
    }

    private function getFilterInfo($request)
    {
        $info = [];

        // Info status layanan
        if ($request->has('status') && !empty($request->status)) {
            $info[] = 'Status: ' . ucfirst($request->status);
        } else {
            $info[] = 'Status: Semua';
        }

        // Info rentang tanggal
        if ($request->has('date_from') && $request->date_from) {
            if (\Carbon\Carbon::hasFormat($request->date_from, 'Y-m-d')) {
                $formattedDate = \Carbon\Carbon::parse($request->date_from)->format('d-m-Y');
                $info[] = 'Dari Tanggal: ' . $formattedDate;
            }
        }
        
        if ($request->has('date_to') && $request->date_to) {
            if (\Carbon\Carbon::hasFormat($request->date_to, 'Y-m-d')) {
                $formattedDate = \Carbon\Carbon::parse($request->date_to)->format('d-m-Y');
                $info[] = 'Sampai Tanggal: ' . $formattedDate;
            }
        }

        // Info pengurutan
        $sortLabels = [
            'customer_name' => 'Nama Pelanggan',
            'service_status' => 'Status Layanan',
            'estimated_cost' => 'Estimasi Biaya',
            'created_at' => 'Tanggal Dibuat',
        ];

        $sortBy = $request->sort_by ?? 'customer_name';
        $sortDirection = $request->sort_direction ?? 'asc';
        $directionLabel = $sortDirection === 'asc' ? 'Menaik' : 'Menurun';
        
        $info[] = 'Diurutkan berdasarkan: ' . ($sortLabels[$sortBy] ?? 'Nama Pelanggan') . ' (' . $directionLabel . ')';

        return $info;
    }

}