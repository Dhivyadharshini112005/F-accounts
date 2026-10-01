<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    use HasFactory;

    protected $table = 'advances';

    protected $fillable = [
        'employee_name',
        'category',
        'amount',
        'payment_mode',
        'upi_id',
        'advance_date',
        'description',
    ];
}