<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        /*
        |--------------------------------------------------------------------------
        | INCOME
        |--------------------------------------------------------------------------
        */

        $incomeQuery = Income::query();

        if ($search !== '') {

            $incomeQuery->where(function ($q) use ($search) {

                // Driver ID
                $q->where('driver_id', 'like', '%' . $search . '%')

                    // Description
                    ->orWhere('description', 'like', '%' . $search . '%')

                    // Fees Amount
                    ->orWhere('fees_amount', 'like', '%' . $search . '%')

                    // GST Amount
                    ->orWhere('gst_amount', 'like', '%' . $search . '%')

                    // Total Amount
                    ->orWhere('total_amount', 'like', '%' . $search . '%')

                    // Old/main amount column
                    ->orWhere('amount', 'like', '%' . $search . '%')

                    // Database date
                    ->orWhere('income_date', 'like', '%' . $search . '%');


                /*
                |--------------------------------------------------------------------------
                | Driver ID numeric search
                |--------------------------------------------------------------------------
                | Allows searching 012 and finding database value 12.
                |--------------------------------------------------------------------------
                */

                if (is_numeric($search)) {

                    $q->orWhere(
                        'driver_id',
                        (int) $search
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Date search
                |--------------------------------------------------------------------------
                | Example:
                | 16-09-2026
                |--------------------------------------------------------------------------
                */

                if (
                    preg_match(
                        '/^(\d{2})-(\d{2})-(\d{4})$/',
                        $search,
                        $matches
                    )
                ) {

                    $date = $matches[3] . '-' .
                            $matches[2] . '-' .
                            $matches[1];

                    $q->orWhereDate(
                        'income_date',
                        $date
                    );
                }
            });
        }

        $incomes = $incomeQuery
            ->orderBy('income_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | EXPENSE
        |--------------------------------------------------------------------------
        */

        $expenseQuery = Expense::query();

        if ($search !== '') {

            $expenseQuery->where(function ($q) use ($search) {

                // Description
                $q->where(
                    'description',
                    'like',
                    '%' . $search . '%'
                )

                // Amount
                ->orWhere(
                    'amount',
                    'like',
                    '%' . $search . '%'
                )

                // Date
                ->orWhere(
                    'expense_date',
                    'like',
                    '%' . $search . '%'
                );

                /*
                |--------------------------------------------------------------------------
                | Date search
                |--------------------------------------------------------------------------
                | Example:
                | 14-09-2026
                |--------------------------------------------------------------------------
                */

                if (
                    preg_match(
                        '/^(\d{2})-(\d{2})-(\d{4})$/',
                        $search,
                        $matches
                    )
                ) {

                    $date = $matches[3] . '-' .
                            $matches[2] . '-' .
                            $matches[1];

                    $q->orWhereDate(
                        'expense_date',
                        $date
                    );
                }
            });
        }

        $expenses = $expenseQuery
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SEARCH VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'search.search',
            compact(
                'search',
                'incomes',
                'expenses'
            )
        );
    }
}