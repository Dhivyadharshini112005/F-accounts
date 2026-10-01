@extends('layouts.app')

@section('content')

<style>

    .search-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .search-header {
        margin-bottom: 22px;
    }

    .search-header h2 {
        margin: 0 0 6px;
        font-size: 28px;
        color: #111827;
    }

    .search-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .search-box {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }

    .search-form {
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .search-input:focus {
        border-color: #374151;
        box-shadow: 0 0 0 3px rgba(55,65,81,.10);
    }

    .search-btn {
        height: 44px;
        padding: 0 20px;
        border: none;
        border-radius: 8px;
        background: #111827;
        color: #ffffff;
        font-weight: 600;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #1f2937;
    }

    .result-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }

    .result-title {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #f9fafb;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        text-align: left;
        padding: 13px 16px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #4b5563;
        font-size: 14px;
        white-space: nowrap;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .amount {
        font-weight: 600;
        color: #111827;
    }

    .empty {
        padding: 30px 20px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }

    @media(max-width:700px) {

        .search-form {
            flex-direction: column;
        }

        .search-btn {
            width: 100%;
        }

    }

</style>


<div class="search-page">

    {{-- HEADER --}}

    <div class="search-header">

        <h2>
            Search
        </h2>

        <p>
            Search income and expense records.
        </p>

    </div>


    {{-- SEARCH BOX --}}

    <div class="search-box">

        <form
            action="{{ route('search.index') }}"
            method="GET"
            class="search-form"
        >

            <input
                type="text"
                name="search"
                class="search-input"
                value="{{ $search }}"
                placeholder="Search by driver ID, description, amount or date..."
                autocomplete="off"
            >

            <button
                type="submit"
                class="search-btn"
            >
                Search
            </button>

        </form>

    </div>


    {{-- INCOME RESULTS --}}

    <div class="result-card">

        <div class="result-title">
            Income Results
        </div>


        @if($incomes->count())

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Driver ID
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Fees Amount
                            </th>

                            <th>
                                GST Amount
                            </th>

                            <th>
                                Total Amount
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($incomes as $income)

                            <tr>

                                {{-- DATE --}}

                                <td>

                                    @if($income->income_date)

                                        {{ \Carbon\Carbon::parse(
                                            $income->income_date
                                        )->format('d-m-Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- DRIVER ID --}}

                                <td>
                                    {{ $income->driver_id }}
                                </td>


                                {{-- DESCRIPTION --}}

                                <td>
                                    {{ $income->description ?? '-' }}
                                </td>


                                {{-- FEES AMOUNT --}}

                                <td class="amount">

                                    ₹{{ number_format(
                                        $income->fees_amount
                                        ?? $income->amount,
                                        2
                                    ) }}

                                </td>


                                {{-- GST AMOUNT --}}

                                <td class="amount">

                                    ₹{{ number_format(
                                        $income->gst_amount ?? 0,
                                        2
                                    ) }}

                                </td>


                                {{-- TOTAL AMOUNT --}}

                                <td class="amount">

                                    ₹{{ number_format(
                                        $income->total_amount
                                        ?? $income->amount,
                                        2
                                    ) }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">

                @if($search !== '')

                    No income records found for
                    <strong>
                        "{{ $search }}"
                    </strong>.

                @else

                    No income records found.

                @endif

            </div>

        @endif

    </div>


    {{-- EXPENSE RESULTS --}}

    <div class="result-card">

        <div class="result-title">
            Expense Results
        </div>


        @if($expenses->count())

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Amount
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($expenses as $expense)

                            <tr>

                                {{-- DATE --}}

                                <td>

                                    @if($expense->expense_date)

                                        {{ \Carbon\Carbon::parse(
                                            $expense->expense_date
                                        )->format('d-m-Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- DESCRIPTION --}}

                                <td>
                                    {{ $expense->description ?? '-' }}
                                </td>


                                {{-- AMOUNT --}}

                                <td class="amount">

                                    ₹{{ number_format(
                                        $expense->amount,
                                        2
                                    ) }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">

                @if($search !== '')

                    No expense records found for
                    <strong>
                        "{{ $search }}"
                    </strong>.

                @else

                    No expense records found.

                @endif

            </div>

        @endif

    </div>

</div>

@endsection