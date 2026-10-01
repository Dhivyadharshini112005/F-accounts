<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;

class DailyAccountController extends Controller
{
    public function index()
    {
        $incomes = Income::with('driver')
            ->orderBy('income_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $expenses = Expense::orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('daily-account', compact(
            'incomes',
            'expenses',
            'totalIncome',
            'totalExpense',
            'balance'
        ));
    }
}