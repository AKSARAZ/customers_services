@extends('layouts.app')

@section('title', 'Detail Layanan')

@section('contents')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Layanan</h1>
    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Informasi Pelanggan -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Pelanggan</th>
                        <td>: {{ $service->customer_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>: {{ $service->email }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>: {{ $service->phone }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $service->contact_address }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Informasi Layanan -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Layanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Status</th>
                        <td>
                            : <span class="badge badge-{{ $service->service_status == 'completed' ? 'success' : 'info' }}">
                                {{ $service->service_status == 'completed' ? 'Selesai' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Estimasi Biaya</th>
                        <td>: Rp {{ number_format($service->estimated_cost, 0, ',', '.') }}</td>
                    </tr>
                    <!-- Tanggal Dibuat -->
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>: {{ $service->created_at->setTimezone('Asia/Jakarta')->format('d F Y H:i') }}</td>
                    </tr>

                    <!-- Terakhir Diperbarui -->
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>: {{ $service->updated_at->setTimezone('Asia/Jakarta')->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Deskripsi Layanan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Deskripsi Layanan</h6>
    </div>
    <div class="card-body">
        <p>{{ $service->service_description }}</p>
    </div>
</div>

<!-- Informasi Daya -->
@if($service->power_selection)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Daya</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Daya Terpilih</h6>
                <p class="h4 text-primary">{{ optional($service->powerOption)->power_value ?? 'N/A' }} VA</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tombol Aksi -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div>
                @if($service->service_status == 'pending')
                <form action="{{ route('admin.services.updateStatus', $service->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="service_status" value="completed">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Tandai layanan ini sebagai selesai?')">
                        <i class="fas fa-check"></i> Tandai Selesai
                    </button>
                </form>
                @endif
                
                <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Layanan
                </a>
            </div>
            
            @if($service->service_status == 'pending')
            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini?')">
                    <i class="fas fa-trash"></i> Hapus Layanan
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@endsection