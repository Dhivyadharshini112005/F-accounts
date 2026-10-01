<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'expense_name',
        'expense_type',
        'amount',
        'payment_mode',
        'upi_id',
        'date',
        'expense_date',
        'category',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];
}
