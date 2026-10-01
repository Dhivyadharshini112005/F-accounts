<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FTaxiMonthlyAccount extends Model
{
    protected $table = 'ftaxi_monthly_accounts';

    protected $fillable = [
        'month',
        'driver_payout',
        'driver_amt',
        'company_amt',
        'commission',
        'gst',
        'business_commission',
        'pending_amt',
        'amount_paid',
        'advance_amount',
        'toll_fee',
        'service_charge',
    ];

    protected $casts = [
        'driver_payout' => 'decimal:2',
        'driver_amt' => 'decimal:2',
        'company_amt' => 'decimal:2',
        'commission' => 'decimal:2',
        'gst' => 'decimal:2',
        'business_commission' => 'decimal:2',
        'pending_amt' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'toll_fee' => 'decimal:2',
        'service_charge' => 'decimal:2',
    ];
}