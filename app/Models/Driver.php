<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'ftaxi_driver_id',
        'id_number',
        'name',
        'phone',
        'vehicle_number',
        'vehicle_name',
        'pending_amount',
        'status',
    ];

    public function incomes()
    {
        return $this->hasMany(
            Income::class
        );
    }
}