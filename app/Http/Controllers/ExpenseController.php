<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Check whether logged-in user is Owner.
     */
    private function isOwner()
    {
        return Auth::check()
            && strtoupper(Auth::user()->role ?? '') === 'OWNER';
    }


    /**
     * Display expense records.
     */
    public function index(Request $request)
    {
        $query = Expense::orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc');


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'expense_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'expense_type',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'category',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'amount',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'payment_mode',
                    'like',
                    "%{$search}%"
                );


                if (is_numeric($search)) {

                    $q->orWhere(
                        'id',
                        (int) $search
                    );

                }


                $q->orWhereDate(
                    'expense_date',
                    $search
                );

            });

        }


        $expenses = $query->get();


        return view(
            'expenses.index',
            compact('expenses')
        );
    }


    /**
     * Show Add Expense page.
     */
    public function create()
    {
        return view('expenses.create');
    }


    /**
     * Store Expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'expense_name' =>
                'required|string|max:255',

            'amount' =>
                'required|numeric|min:0.01',

            'payment_mode' =>
                'required|in:cash,account',

            'expense_date' =>
                'required|date',

            'category' =>
                'nullable|string|max:255',

            'description' =>
                'nullable|string|max:255',

        ]);


        $expenseType =
            $validated['category']
            ?? $validated['expense_name'];


        Expense::create([

            'expense_name' =>
                $validated['expense_name'],

            'expense_type' =>
                $expenseType,

            'amount' =>
                $validated['amount'],

            'payment_mode' =>
                $validated['payment_mode'],

            'date' =>
                $validated['expense_date'],

            'expense_date' =>
                $validated['expense_date'],

            'category' =>
                $validated['category'] ?? null,

            'description' =>
                $validated['description'] ?? null,

        ]);


        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense added successfully.'
            );
    }


    /**
     * Edit Expense.
     * Owner only.
     */
    public function edit(Expense $expense)
    {
        if (!$this->isOwner()) {

            abort(403);

        }


        return view(
            'expenses.edit',
            compact('expense')
        );
    }


    /**
     * Update Expense.
     * Owner only.
     */
    public function update(
        Request $request,
        Expense $expense
    ) {

        if (!$this->isOwner()) {

            abort(403);

        }


        $validated = $request->validate([

            'expense_name' =>
                'required|string|max:255',

            'amount' =>
                'required|numeric|min:0.01',

            'payment_mode' =>
                'required|in:cash,account',

            'expense_date' =>
                'required|date',

            'category' =>
                'nullable|string|max:255',

            'description' =>
                'nullable|string|max:255',

        ]);


        $expenseType =
            $validated['category']
            ?? $validated['expense_name'];


        $expense->update([

            'expense_name' =>
                $validated['expense_name'],

            'expense_type' =>
                $expenseType,

            'amount' =>
                $validated['amount'],

            'payment_mode' =>
                $validated['payment_mode'],

            'date' =>
                $validated['expense_date'],

            'expense_date' =>
                $validated['expense_date'],

            'category' =>
                $validated['category'] ?? null,

            'description' =>
                $validated['description'] ?? null,

        ]);


        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense updated successfully.'
            );
    }


    /**
     * Delete Expense.
     * Owner only.
     */
    public function destroy(Expense $expense)
    {
        if (!$this->isOwner()) {

            abort(403);

        }


        $expense->delete();


        return redirect()
            ->route('expenses.index')
            ->with(
                'success',
                'Expense deleted successfully.'
            );
    }


    /**
     * Payment Voucher.
     */
    public function voucher(Expense $expense)
    {
        /*
        |--------------------------------------------------------------------------
        | Display-only voucher number
        |--------------------------------------------------------------------------
        */

        $voucherNumber = Expense::where(
            'id',
            '<=',
            $expense->id
        )->count();


        $displayVoucherNumber = str_pad(
            $voucherNumber,
            3,
            '0',
            STR_PAD_LEFT
        );


        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */

        $amount = (float) $expense->amount;

        $wholeAmount = (int) floor($amount);


        /*
        |--------------------------------------------------------------------------
        | Number To Words
        |--------------------------------------------------------------------------
        */

        $ones = [

            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',

        ];


        $tens = [

            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety',

        ];


        $convertNumber = function ($number) use (
            &$convertNumber,
            $ones,
            $tens
        ) {

            if ($number < 20) {

                return $ones[$number];

            }


            if ($number < 100) {

                return $tens[
                    intdiv($number, 10)
                ]

                .

                (
                    ($number % 10)

                    ?

                    ' ' . $ones[$number % 10]

                    :

                    ''
                );

            }


            if ($number < 1000) {

                return $ones[
                    intdiv($number, 100)
                ]

                . ' Hundred'

                .

                (
                    ($number % 100)

                    ?

                    ' ' .
                    $convertNumber(
                        $number % 100
                    )

                    :

                    ''
                );

            }


            if ($number < 100000) {

                return $convertNumber(
                    intdiv($number, 1000)
                )

                . ' Thousand'

                .

                (
                    ($number % 1000)

                    ?

                    ' ' .
                    $convertNumber(
                        $number % 1000
                    )

                    :

                    ''
                );

            }


            if ($number < 10000000) {

                return $convertNumber(
                    intdiv($number, 100000)
                )

                . ' Lakh'

                .

                (
                    ($number % 100000)

                    ?

                    ' ' .
                    $convertNumber(
                        $number % 100000
                    )

                    :

                    ''
                );

            }


            return $convertNumber(
                intdiv($number, 10000000)
            )

            . ' Crore'

            .

            (
                ($number % 10000000)

                ?

                ' ' .
                $convertNumber(
                    $number % 10000000
                )

                :

                ''
            );

        };


        $amountInWords = $convertNumber(
            $wholeAmount
        );


        return view(
            'expenses.voucher',
            compact(
                'expense',
                'displayVoucherNumber',
                'amountInWords'
            )
        );
    }
}