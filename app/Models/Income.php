<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $table = 'incomes';

    protected $fillable = [

        'driver_id',

        'fees_amount',

        'gst_amount',

        'amount',

        'total_amount',

        'fees_payment_mode',

        'gst_payment_mode',

        'fees_upi_id',

        'gst_upi_id',

        'income_date',

        'description',

    ];

    protected $casts = [

        'fees_amount' => 'decimal:2',

        'gst_amount' => 'decimal:2',

        'amount' => 'decimal:2',

        'total_amount' => 'decimal:2',

        'income_date' => 'date',

    ];


    public function driver()
    {
        return $this->belongsTo(
            Driver::class,
            'driver_id'
        );
    }
}