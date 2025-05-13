@extends('layouts.app')

@section('title', 'Edit Data Layanan Pelanggan')

@section('contents')
<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data</h6>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-lg-6">
                    <!-- Nama Pelanggan -->
                    <div class="form-group">
                        <label for="customer_name">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" 
                               value="{{ old('customer_name', $service->customer_name) }}" 
                               placeholder="Masukkan nama pelanggan" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" 
                               value="{{ old('email', $service->email) }}" 
                               placeholder="nama@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" 
                               value="{{ old('phone', $service->phone) }}" 
                               placeholder="08xxxxxxxxxx" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="form-group">
                        <label for="contact_address">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('contact_address') is-invalid @enderror" 
                                  id="contact_address" name="contact_address" rows="3" 
                                  placeholder="Masukkan alamat lengkap" required>{{ old('contact_address', $service->contact_address) }}</textarea>
                        @error('contact_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-lg-6">
                    <!-- Status Layanan (Read Only) -->
                    <div class="form-group">
                        <label>Status Layanan</label>
                        <input type="text" class="form-control" 
                               value="{{ $service->service_status == 'completed' ? 'Selesai' : 'Pending' }}" readonly>
                        <small class="form-text text-muted">*Status dapat diubah dari halaman daftar layanan</small>
                    </div>

                    <!-- Power Selection Dropdown -->
                    <div class="form-group">
                        <label for="power_selection">Pilih Daya <span class="text-danger">*</span></label>
                        <select class="form-control @error('power_selection') is-invalid @enderror" 
                                id="power_selection" name="power_selection" required>
                            <option value="">-- Pilih Daya --</option>
                            @foreach($powerOptions as $power)
                                <option value="{{ $power->id }}" 
                                    {{ old('power_selection', $service->power_selection) == $power->id ? 'selected' : '' }}>
                                    {{ number_format($power->power_value, 0, ',', '.') }} VA
                                </option>
                            @endforeach
                        </select>
                        @error('power_selection')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi Layanan -->
                    <div class="form-group">
                        <label for="service_description">Deskripsi Layanan</label>
                        <textarea class="form-control @error('service_description') is-invalid @enderror" 
                                    id="service_description" name="service_description" rows="3" 
                                    placeholder="Masukkan deskripsi layanan">{{ old('service_description', $service->service_description) }}</textarea>
                        @error('service_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estimasi Biaya -->
                    <div class="form-group">
                        <label for="estimated_cost">Estimasi Biaya <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                    id="estimated_cost" name="estimated_cost" 
                                    value="{{ old('estimated_cost', $service->estimated_cost) }}" 
                                    placeholder="0" min="0" required>
                        </div>
                        @error('estimated_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Daya (Read Only) -->
            @if($service->power_selection)
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>Daya Terpilih:</strong> {{ optional($service->powerOption)->power_value }} VA
                    </div>
                </div>
            </div>
            @endif

            <!-- Tombol Submit -->
            <div class="row">
                <div class="col-12">
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Layanan
                    </button>
                    <a href="{{ route('admin.services.show', $service->id) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
