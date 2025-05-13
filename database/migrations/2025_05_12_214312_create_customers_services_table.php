<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersServicesTable extends Migration
{
    public function up()
    {
        Schema::create('customers_services', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');  // Nama pelanggan
            $table->string('service_category');  // Kategori layanan (Pemasangan Baru atau Tambah Daya)
            $table->text('service_description');  // Deskripsi layanan
            $table->text('contact_address');  // Alamat pelanggan
            $table->string('phone');  // Nomor telepon pelanggan
            $table->string('email')->unique();  // Email pelanggan
            $table->string('service_status')->default('pending');  // Status layanan (misalnya: pending, completed)
            $table->decimal('estimated_cost', 10, 2)->nullable();  // Biaya estimasi layanan
            $table->integer('previous_power')->nullable();  // Daya sebelumnya (untuk Tambah Daya)
            $table->integer('new_power')->nullable();  // Daya baru (untuk Tambah Daya)
            $table->integer('power_selection')->nullable();  // Pilihan daya (untuk Pemasangan Baru)
            $table->timestamps();  // Timestamps untuk created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers_services');
    }
}
