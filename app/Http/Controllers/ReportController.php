<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Report Filter
        |--------------------------------------------------------------------------
        */

        $period = $request->get('period', 'all');

        $allowedPeriods = [
            'all',
            'month',
            'previous_month',
            'year',
            'custom',
        ];

        if (!in_array($period, $allowedPeriods, true)) {
            $period = 'all';
        }

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $filterStart = null;
        $filterEnd = null;

        switch ($period) {

            case 'month':

                $filterStart = Carbon::now()->startOfMonth();
                $filterEnd = Carbon::now()->endOfMonth();

                break;

            case 'previous_month':

                $filterStart = Carbon::now()
                    ->subMonthNoOverflow()
                    ->startOfMonth();

                $filterEnd = Carbon::now()
                    ->subMonthNoOverflow()
                    ->endOfMonth();

                break;

            case 'year':

                $filterStart = Carbon::now()->startOfYear();
                $filterEnd = Carbon::now()->endOfYear();

                break;

            case 'custom':

                try {

                    $filterStart = Carbon::createFromFormat(
                        'Y-m-d',
                        $fromDate
                    )->startOfDay();

                    $filterEnd = Carbon::createFromFormat(
                        'Y-m-d',
                        $toDate
                    )->endOfDay();

                    if ($filterStart->gt($filterEnd)) {
                        [$filterStart, $filterEnd] = [
                            $filterEnd,
                            $filterStart
                        ];

                        $fromDate = $filterStart->format('Y-m-d');
                        $toDate = $filterEnd->format('Y-m-d');
                    }

                } catch (\Throwable $e) {

                    $period = 'all';
                    $filterStart = null;
                    $filterEnd = null;
                    $fromDate = null;
                    $toDate = null;
                }

                break;
        }

        if ($period !== 'custom') {

            $fromDate = $filterStart
                ? $filterStart->format('Y-m-d')
                : null;

            $toDate = $filterEnd
                ? $filterEnd->format('Y-m-d')
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Income
        |--------------------------------------------------------------------------
        */

        $incomeQuery = Income::with('driver');

        if ($filterStart && $filterEnd) {
            $incomeQuery->whereBetween(
                'income_date',
                [$filterStart, $filterEnd]
            );
        }

        $incomes = $incomeQuery
            ->orderBy('income_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Expenses
        |--------------------------------------------------------------------------
        */

        $expenseQuery = Expense::query();

        if ($filterStart && $filterEnd) {
            $expenseQuery->whereBetween(
                'expense_date',
                [$filterStart, $filterEnd]
            );
        }

        $expenses = $expenseQuery
            ->orderBy('expense_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Excel / CSV Export
        |--------------------------------------------------------------------------
        */

        if ($request->get('export') === 'excel') {

            $fileName = 'financial_report';

            if ($period === 'month') {
                $fileName .= '_' . Carbon::now()->format('F_Y');
            } elseif ($period === 'previous_month') {
                $fileName .= '_' . Carbon::now()
                    ->subMonthNoOverflow()
                    ->format('F_Y');
            } elseif ($period === 'year') {
                $fileName .= '_' . Carbon::now()->format('Y');
            } elseif (
                $period === 'custom'
                && $fromDate
                && $toDate
            ) {
                $fileName .= '_'
                    . str_replace('-', '', $fromDate)
                    . '_to_'
                    . str_replace('-', '', $toDate);
            } else {
                $fileName .= '_all_time';
            }

            $fileName .= '.csv';

            return response()->streamDownload(
                function () use ($incomes, $expenses) {

                    $handle = fopen('php://output', 'w');

                    /*
                    | UTF-8 BOM
                    | Helps Excel properly display ₹ and other characters.
                    */
                    fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                    /*
                    |----------------------------------------------------------
                    | Report Header
                    |----------------------------------------------------------
                    */

                    fputcsv($handle, [
                        'FINANCIAL REPORT'
                    ]);

                    fputcsv($handle, []);

                    /*
                    |----------------------------------------------------------
                    | Income Records
                    |----------------------------------------------------------
                    */

                    fputcsv($handle, [
                        'INCOME RECORDS'
                    ]);

                    fputcsv($handle, [
                        'Date',
                        'Driver',
                        'Vehicle',
                        'Description',
                        'Amount'
                    ]);

                    foreach ($incomes as $income) {

                        $date = !empty($income->income_date)
                            ? Carbon::parse(
                                $income->income_date
                            )->format('d-m-Y')
                            : '';

                        fputcsv($handle, [
                            $date,
                            $income->driver->name ?? '-',
                            $income->driver->vehicle_number ?? '-',
                            $income->description ?? '-',
                            number_format(
                                (float) $income->amount,
                                2,
                                '.',
                                ''
                            )
                        ]);
                    }

                    fputcsv($handle, []);

                    /*
                    |----------------------------------------------------------
                    | Expense Records
                    |----------------------------------------------------------
                    */

                    fputcsv($handle, [
                        'EXPENSE RECORDS'
                    ]);

                    fputcsv($handle, [
                        'Date',
                        'Category',
                        'Description',
                        'Amount'
                    ]);

                    foreach ($expenses as $expense) {

                        $date = !empty($expense->expense_date)
                            ? Carbon::parse(
                                $expense->expense_date
                            )->format('d-m-Y')
                            : '';

                        fputcsv($handle, [
                            $date,
                            $expense->category ?? 'Uncategorized',
                            $expense->description ?? '-',
                            number_format(
                                (float) $expense->amount,
                                2,
                                '.',
                                ''
                            )
                        ]);
                    }

                    fclose($handle);
                },
                $fileName,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalIncome = (float) $incomes->sum('amount');
        $totalExpense = (float) $expenses->sum('amount');
        $balance = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Expense Category Summary
        |--------------------------------------------------------------------------
        */

        $expenseCategories = $expenses
            ->groupBy(function ($expense) {
                return $expense->category ?: 'Uncategorized';
            })
            ->map(function ($items) {
                return (float) $items->sum('amount');
            })
            ->sortDesc();

        /*
        |--------------------------------------------------------------------------
        | Report Heading
        |--------------------------------------------------------------------------
        */

        $reportTitle = match ($period) {

            'month' => Carbon::now()->format('F Y'),

            'previous_month' => Carbon::now()
                ->subMonthNoOverflow()
                ->format('F Y'),

            'year' => Carbon::now()->format('Y'),

            'custom' => (
                $fromDate && $toDate
                    ? Carbon::parse($fromDate)->format('d-m-Y')
                        . ' to '
                        . Carbon::parse($toDate)->format('d-m-Y')
                    : 'Custom Range'
            ),

            default => 'All Time',
        };

        /*
        |--------------------------------------------------------------------------
        | Reports View
        |--------------------------------------------------------------------------
        */

        return view(
            'reports.index',
            compact(
                'incomes',
                'expenses',
                'totalIncome',
                'totalExpense',
                'balance',
                'fromDate',
                'toDate',
                'period',
                'reportTitle',
                'expenseCategories'
            )
        );
    }
}