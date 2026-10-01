<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Check Owner
    |--------------------------------------------------------------------------
    */

    private function isOwner()
    {
        return Auth::check()
            && strtoupper(Auth::user()->role ?? '') === 'OWNER';
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Advance::orderBy(
            'advance_date',
            'desc'
        )->orderBy(
            'id',
            'desc'
        );


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'employee_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'category',
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
                )

                ->orWhere(
                    'upi_id',
                    'like',
                    "%{$search}%"
                )

                ->orWhere(
                    'description',
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
                    'advance_date',
                    $search
                );

            });
        }


        $advances = $query->get();


        return view(
            'advances.index',
            compact('advances')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('advances.create');
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'employee_name' =>
                'required|string|max:255',

            'category' =>
                'required|string|max:100',

            'amount' =>
                'required|numeric|min:0.01',

            'payment_mode' =>
                'required|in:cash,account',

            'upi_id' =>
                'nullable|string|max:255',

            'advance_date' =>
                'required|date',

            'description' =>
                'nullable|string|max:255',

        ]);


        Advance::create([

            'employee_name' =>
                $validated['employee_name'],

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            'category' =>
                'Salary Advance',

            'amount' =>
                $validated['amount'],

            'payment_mode' =>
                $validated['payment_mode'],

            'upi_id' =>
                $validated['upi_id'] ?? null,

            'advance_date' =>
                $validated['advance_date'],

            'description' =>
                $validated['description'] ?? null,

        ]);


        return redirect()
            ->route('expenses.advances.index')
            ->with(
                'success',
                'Salary advance added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    |
    | OWNER ONLY
    |
    */

    public function edit(Advance $advance)
    {
        if (!$this->isOwner()) {

            abort(403);

        }


        return view(
            'advances.edit',
            compact('advance')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    |
    | OWNER ONLY
    |
    */

    public function update(
        Request $request,
        Advance $advance
    ) {

        if (!$this->isOwner()) {

            abort(403);

        }


        $validated = $request->validate([

            'employee_name' =>
                'required|string|max:255',

            'amount' =>
                'required|numeric|min:0.01',

            'payment_mode' =>
                'required|in:cash,account',

            'upi_id' =>
                'nullable|string|max:255',

            'advance_date' =>
                'required|date',

            'description' =>
                'nullable|string|max:255',

        ]);


        $advance->update([

            'employee_name' =>
                $validated['employee_name'],

            /*
            |--------------------------------------------------------------------------
            | Always Salary Advance
            |--------------------------------------------------------------------------
            */

            'category' =>
                'Salary Advance',

            'amount' =>
                $validated['amount'],

            'payment_mode' =>
                $validated['payment_mode'],

            'upi_id' =>
                $validated['upi_id'] ?? null,

            'advance_date' =>
                $validated['advance_date'],

            'description' =>
                $validated['description'] ?? null,

        ]);


        return redirect()
            ->route('expenses.advances.index')
            ->with(
                'success',
                'Salary advance updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    |
    | OWNER ONLY
    |
    */

    public function destroy(Advance $advance)
    {
        if (!$this->isOwner()) {

            abort(403);

        }


        $advance->delete();


        return redirect()
            ->route('expenses.advances.index')
            ->with(
                'success',
                'Salary advance deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VOUCHER
    |--------------------------------------------------------------------------
    |
    | OWNER + MANAGER
    |
    */

    public function voucher(Advance $advance)
    {
        /*
        |--------------------------------------------------------------------------
        | Display-only voucher number
        |--------------------------------------------------------------------------
        */

        $voucherNumber = Advance::where(
            'id',
            '<=',
            $advance->id
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

        $amount = (float) $advance->amount;

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
            'advances.voucher',
            compact(
                'advance',
                'displayVoucherNumber',
                'amountInWords'
            )
        );
    }
}