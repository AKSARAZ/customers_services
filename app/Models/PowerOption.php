<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'power_value',
        'description'
    ];

    /**
     * Get the customer services with this power option as previous power
     */
    public function customerServicesAsPrevious()
    {
        return $this->hasMany(CustomerService::class, 'previous_power');
    }

    /**
     * Get the customer services with this power option as new power
     */
    public function customerServicesAsNew()
    {
        return $this->hasMany(CustomerService::class, 'new_power');
    }

    /**
     * Get the customer services with this power option as power selection
     */
    public function customerServicesAsSelection()
    {
        return $this->hasMany(CustomerService::class, 'power_selection');
    }
    
    /**
     * Alias for customerServicesAsSelection for compatibility
     */
    public function customerServices()
    {
        return $this->customerServicesAsSelection();
    }
}