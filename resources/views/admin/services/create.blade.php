@extends('layouts.app')

@section('title', 'Instalasi Listrik Baru')

@section('contents')
<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Form Instalasi Listrik Baru</h6>
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
        
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-lg-6">
                    <!-- Nama Pelanggan -->
                    <div class="form-group">
                        <label for="customer_name">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                               placeholder="Masukkan nama pelanggan" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" 
                               placeholder="nama@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="form-group">
                        <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" 
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
                                  placeholder="Masukkan alamat lengkap" required>{{ old('contact_address') }}</textarea>
                        @error('contact_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-lg-6">
                    <!-- Pilih Daya -->
                    <div class="form-group">
                        <label for="power_selection">Pilih Daya <span class="text-danger">*</span></label>
                        <select class="form-control @error('power_selection') is-invalid @enderror" 
                                id="power_selection" name="power_selection" required>
                            <option value="">-- Pilih Daya --</option>
                            @foreach($powerOptions as $power)
                                <option value="{{ $power->id }}" data-power="{{ $power->power_value }}" 
                                        {{ old('power_selection') == $power->id ? 'selected' : '' }}>
                                    {{ number_format($power->power_value, 0, ',', '.') }} VA
                                </option>
                            @endforeach
                        </select>
                        @error('power_selection')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi Layanan (Opsional) -->
                    <div class="form-group">
                        <label for="service_description">Deskripsi Layanan</label>
                        <textarea class="form-control @error('service_description') is-invalid @enderror" 
                                  id="service_description" name="service_description" rows="3" 
                                  placeholder="Masukkan deskripsi layanan (opsional)">{{ old('service_description') }}</textarea>
                        @error('service_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">*Deskripsi bersifat opsional</small>
                    </div>

                    <!-- Estimasi Biaya (Auto Calculate) -->
                    <div class="form-group">
                        <label for="estimated_cost">Estimasi Biaya</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" 
                                   id="estimated_cost_display" value="0" 
                                   placeholder="0" readonly>
                            <input type="hidden" id="estimated_cost" name="estimated_cost" value="0">
                        </div>
                        <small class="form-text text-muted">*Biaya akan dihitung otomatis berdasarkan daya yang dipilih</small>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="row">
                <div class="col-12">
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Layanan
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Tarif biaya per VA
    const COST_PER_VA = 1000;
    const BASE_FEE = 500000;

    // Fungsi untuk format number dengan titik pemisah ribuan
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Fungsi untuk menghitung biaya
    function calculateCost() {
        const powerValue = $('#power_selection option:selected').data('power');
        let cost = 0;
        
        if (powerValue) {
            cost = (powerValue * COST_PER_VA) + BASE_FEE;
        }

        $('#estimated_cost').val(cost);
        $('#estimated_cost_display').val(formatNumber(cost));
    }

    // Calculate cost when power selection changes
    $('#power_selection').change(function() {
        calculateCost();
    });

    // Hitung biaya saat halaman dimuat jika ada old value
    calculateCost();
});
</script>
@endpush
@endsection