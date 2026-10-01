<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Driver;

class DashboardController extends Controller
{
    public function index()
    {
        /* TOTAL INCOME */
        $totalIncome = (float) Income::sum('amount');

        /* EXPENSE + ADVANCE */
        $totalExpenseOnly = (float) Expense::sum('amount');
        $totalAdvance = (float) Advance::sum('amount');
        $totalExpense = $totalExpenseOnly + $totalAdvance;

        /* BALANCE */
        $balance = $totalIncome - $totalExpense;

        /* CASH / A-C INCOME BREAKDOWN */
        $cashIncome =
            (float) Income::where('fees_payment_mode', 'cash')->sum('fees_amount')
            +
            (float) Income::where('gst_payment_mode', 'cash')->sum('gst_amount');

        $accountIncome =
            (float) Income::where('fees_payment_mode', 'account')->sum('fees_amount')
            +
            (float) Income::where('gst_payment_mode', 'account')->sum('gst_amount');

        /* CASH / A-C EXPENSE BREAKDOWN */
        $cashExpense = (float) Expense::where('payment_mode', 'cash')->sum('amount');
        $accountExpense = (float) Expense::where('payment_mode', 'account')->sum('amount');

        /* CASH / A-C ADVANCE BREAKDOWN */
        $cashAdvance = (float) Advance::where('payment_mode', 'cash')->sum('amount');
        $accountAdvance = (float) Advance::where('payment_mode', 'account')->sum('amount');

        /* AVAILABLE BALANCES */
        $cashBalance = $cashIncome - $cashExpense - $cashAdvance;
        $accountBalance = $accountIncome - $accountExpense - $accountAdvance;

        /* RECENT INCOME */
        $recentIncome = Income::with('driver')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        /* RECENT EXPENSE */
        $recentExpense = Expense::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        /* MONTH VARIABLES */
        $month = request()->get('month', now()->format('Y-m'));

        try {
            $monthDisplay = \Carbon\Carbon::createFromFormat('Y-m', $month)
                ->format('F Y');
        } catch (\Exception $e) {
            $month = now()->format('Y-m');
            $monthDisplay = now()->format('F Y');
        }

        /* EMPTY MONTH DATA */
        $monthly = [
            'driver_payout' => 0,
            'driver_amt' => 0,
            'company_amt' => 0,
            'commission' => 0,
            'gst' => 0,
            'business_commission' => 0,
            'pending_amt' => 0,
            'amount_paid' => 0,
            'advance_amount' => 0,
            'toll_fee' => 0,
            'service_charge' => 0,
        ];

        /* AVAILABLE MONTHS */
        $availableMonths = Income::query()
            ->whereNotNull('income_date')
            ->orderBy('income_date', 'desc')
            ->pluck('income_date')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('M-y');
            })
            ->unique()
            ->values();

        $selectedMonth = $availableMonths->first() ?? now()->format('M-y');

        /* LAST 6 MONTHS TREND */
        $monthlyTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $income = Income::whereYear('income_date', $date->year)
                ->whereMonth('income_date', $date->month)
                ->sum('amount');

            $expense = Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $advance = Advance::whereYear('advance_date', $date->year)
                ->whereMonth('advance_date', $date->month)
                ->sum('amount');

            $monthlyTrend[] = [
                'label' => $date->format('M'),
                'income' => (float) $income,
                'expense' => (float) $expense + (float) $advance,
            ];
        }

        /* CHART MAX */
        $trendMax = 0;

        foreach ($monthlyTrend as $trend) {
            $trendMax = max(
                $trendMax,
                (float) $trend['income'],
                (float) $trend['expense']
            );
        }

        if ($trendMax <= 0) {
            $trendMax = 1;
        }

        return view(
            'dashboard.index',
            compact(
                'totalIncome',
                'totalExpense',
                'totalExpenseOnly',
                'totalAdvance',
                'balance',
                'totalDrivers',
                'cashIncome',
                'accountIncome',
                'cashExpense',
                'accountExpense',
                'cashAdvance',
                'accountAdvance',
                'cashBalance',
                'accountBalance',
                'recentIncome',
                'recentExpense',
                'month',
                'monthDisplay',
                'availableMonths',
                'selectedMonth',
                'monthly',
                'monthlyTrend',
                'trendMax'
            )
        );
    }
}
