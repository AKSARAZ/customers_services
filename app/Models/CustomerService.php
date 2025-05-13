<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model
     * @var string
     */
    protected $table = 'customers_services';

    /**
     * Daftar atribut yang dapat diisi (mass assignable)
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'service_description',
        'contact_address',
        'phone',
        'email',
        'service_status',
        'estimated_cost',
        'power_selection',
    ];

    /**
     * Relasi dengan model PowerOption
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function powerOption()
    {
        return $this->belongsTo(PowerOption::class, 'power_selection');
    }
    
    /**
     * Mengubah nilai default status service menjadi pending
     * @var array<string, mixed>
     */
    protected $attributes = [
        'service_status' => 'pending',
    ];
}