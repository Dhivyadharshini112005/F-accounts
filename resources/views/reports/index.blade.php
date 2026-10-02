@extends('layouts.app')

@section('content')

<style>

    /* =========================================================
       MAIN WRAPPER
    ========================================================= */

    .reports-wrapper {
        max-width: 1500px;
        margin: 0 auto;
        padding: 25px;
    }


    /* =========================================================
       HEADER
    ========================================================= */

    .reports-header {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: #fff;

        border-radius: 18px;

        padding: 25px 28px;
        margin-bottom: 22px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }

    .reports-title h2 {
        margin: 0;

        font-size: 28px;
        font-weight: 700;
    }

    .reports-title p {
        margin: 7px 0 0;

        color: #cbd5e1;

        font-size: 14px;
    }


    /* =========================================================
       BUTTONS
    ========================================================= */

    .print-btn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        gap: 8px;

        height: 42px;

        padding: 0 18px;

        border: 1px solid #374151;
        border-radius: 9px;

        background: #fff;
        color: #111827;

        font-size: 13px;
        font-weight: 700;

        cursor: pointer;

        text-decoration: none;

        transition: 0.2s ease;
    }

    .print-btn:hover {
        background: #f3f4f6;

        color: #111827;

        text-decoration: none;

        transform: translateY(-1px);
    }


    /* =========================================================
       FILTER
    ========================================================= */

    .filter-card {
        background: #fff;

        border: 1px solid #e5e7eb;

        border-radius: 17px;

        box-shadow: 0 5px 18px rgba(0,0,0,0.05);

        margin-bottom: 22px;

        overflow: hidden;
    }

    .filter-card-body {
        padding: 20px;
    }

    .filter-form {
        display: grid;

        grid-template-columns:
            1.2fr
            1fr
            1fr
            auto
            auto;

        gap: 12px;

        align-items: end;
    }

    .filter-group {
        display: flex;

        flex-direction: column;

        gap: 6px;
    }

    .filter-group label {
        color: #374151;

        font-size: 12px;

        font-weight: 700;
    }

    .filter-group select,
    .filter-group input {
        height: 42px;

        border: 1px solid #d1d5db;

        border-radius: 9px;

        padding: 0 12px;

        background: #fff;

        color: #111827;

        font-size: 13px;

        outline: none;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        border-color: #2563eb;

        box-shadow:
            0 0 0 3px rgba(37,99,235,0.08);
    }

    .filter-btn,
    .reset-btn {
        height: 42px;

        display: inline-flex;

        align-items: center;
        justify-content: center;

        border-radius: 9px;

        padding: 0 17px;

        font-size: 13px;

        font-weight: 700;

        text-decoration: none;

        cursor: pointer;
    }

    .filter-btn {
        border: 0;

        background: #111827;

        color: #fff;
    }

    .filter-btn:hover {
        background: #1f2937;
    }

    .reset-btn {
        border: 1px solid #d1d5db;

        background: #fff;

        color: #374151;
    }

    .reset-btn:hover {
        background: #f3f4f6;

        color: #111827;

        text-decoration: none;
    }

    .report-period {
        margin-top: 12px;

        color: #6b7280;

        font-size: 12px;
    }


    /* =========================================================
       SUMMARY CARDS
    ========================================================= */

    .summary-grid {
        display: grid;

        grid-template-columns:
            repeat(3, 1fr);

        gap: 18px;

        margin-bottom: 22px;
    }

    .summary-card {
        background: #fff;

        border: 1px solid #e5e7eb;

        border-radius: 16px;

        padding: 21px;

        box-shadow:
            0 5px 18px rgba(0,0,0,0.05);

        position: relative;

        overflow: hidden;
    }

    .summary-card::after {
        content: "";

        position: absolute;

        width: 95px;

        height: 95px;

        border-radius: 50%;

        right: -28px;

        top: -28px;

        background: rgba(37,99,235,0.06);
    }

    .summary-label {
        color: #6b7280;

        font-size: 12px;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: 0.35px;
    }

    .summary-value {
        margin-top: 8px;

        color: #111827;

        font-size: 28px;

        font-weight: 750;
    }

    .income-value {
        color: #15803d;
    }

    .expense-value {
        color: #dc2626;
    }

    .balance-positive {
        color: #2563eb;
    }

    .balance-negative {
        color: #dc2626;
    }

    .card-sub {
        margin-top: 5px;

        color: #9ca3af;

        font-size: 12px;
    }


    /* =========================================================
       TWO COLUMN GRID
    ========================================================= */

    .split-grid {
        display: grid;

        grid-template-columns:
            1fr 1fr;

        gap: 22px;

        margin-bottom: 22px;

        align-items: start;
    }


    /* =========================================================
       REPORT CARD
    ========================================================= */

    .report-card {
        background: #fff;

        border: 1px solid #e5e7eb;

        border-radius: 17px;

        box-shadow:
            0 5px 18px rgba(0,0,0,0.05);

        margin-bottom: 22px;

        overflow: hidden;
    }

    .report-card-header {
        padding: 17px 20px;

        background: #111827;

        color: #fff;

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 12px;
    }

    .report-card-header h3 {
        margin: 0;

        font-size: 17px;

        font-weight: 700;
    }

    .report-card-header span {
        color: #cbd5e1;

        font-size: 12px;
    }


    /* =========================================================
       CATEGORY
    ========================================================= */

    .category-list {
        padding: 8px 0;
    }

    .category-row {
        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 15px;

        padding: 13px 20px;

        border-bottom: 1px solid #eef0f3;
    }

    .category-row:last-child {
        border-bottom: none;
    }

    .category-name {
        color: #374151;

        font-size: 13px;

        font-weight: 600;
    }

    .category-amount {
        color: #dc2626;

        font-size: 13px;

        font-weight: 700;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .table-wrapper {
        overflow-x: auto;
    }

    .report-table {
        width: 100%;

        border-collapse: collapse;

        min-width: 0;
    }

    .report-table thead th {
        background: #f8fafc;

        color: #4b5563;

        padding: 13px 16px;

        font-size: 11px;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: 0.3px;

        border-bottom: 1px solid #e5e7eb;

        text-align: left;

        white-space: nowrap;
    }

    .report-table tbody td {
        padding: 13px 16px;

        color: #374151;

        font-size: 13px;

        border-bottom: 1px solid #eef0f3;

        white-space: nowrap;
    }

    .report-table tbody tr:hover {
        background: #f9fafb;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .income-amount {
        color: #15803d;

        font-weight: 700;

        text-align: right;
    }

    .expense-amount {
        color: #dc2626;

        font-weight: 700;

        text-align: right;
    }


    /* =========================================================
       EMPTY STATE
    ========================================================= */

    .empty-state {
        padding: 40px 20px;

        text-align: center;

        color: #9ca3af;

        font-size: 14px;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1100px) {

        .filter-form {
            grid-template-columns:
                repeat(2, 1fr);
        }

        .split-grid {
            grid-template-columns:
                1fr;
        }
    }


    @media (max-width: 750px) {

        .reports-wrapper {
            padding: 15px;
        }

        .reports-header {
            flex-direction: column;

            align-items: flex-start;
        }

        .print-btn {
            width: 100%;
        }

        .summary-grid {
            grid-template-columns:
                1fr;
        }

        .filter-form {
            grid-template-columns:
                1fr;
        }
    }


    /* =========================================================
       PRINT REPORT
    ========================================================= */

    @media print {

        @page {
            size: A4;

            margin: 12mm;
        }


        body * {
            visibility: hidden !important;
        }


        .print-area,
        .print-area * {
            visibility: visible !important;
        }


        .print-area {
            position: absolute !important;

            left: 0 !important;

            top: 0 !important;

            width: 100% !important;

            max-width: none !important;

            margin: 0 !important;

            padding: 0 !important;

            background: #fff !important;
        }


        /* Hide filter */

        .print-area .filter-card {
            display: none !important;
        }


        /* Hide print button */

        .print-area .print-btn {
            display: none !important;
        }


        /* Header */

        .print-area .reports-header {
            background: #fff !important;

            color: #111827 !important;

            border: none !important;

            box-shadow: none !important;

            border-radius: 0 !important;

            padding: 0 0 12px 0 !important;

            margin-bottom: 15px !important;
        }

        .print-area .reports-title h2 {
            color: #111827 !important;

            font-size: 22px !important;
        }

        .print-area .reports-title p {
            color: #6b7280 !important;
        }


        /* Summary */

        .print-area .summary-grid {
            display: grid !important;

            grid-template-columns:
                repeat(3, 1fr) !important;

            gap: 10px !important;

            margin-bottom: 15px !important;
        }

        .print-area .summary-card {
            box-shadow: none !important;

            border: 1px solid #d1d5db !important;

            border-radius: 8px !important;

            padding: 12px !important;

            break-inside: avoid;
        }

        .print-area .summary-card::after {
            display: none !important;
        }

        .print-area .summary-value {
            font-size: 20px !important;
        }


        /* Two-column sections */

        .print-area .split-grid {
            display: grid !important;

            grid-template-columns:
                1fr 1fr !important;

            gap: 15px !important;

            margin-bottom: 15px !important;

            align-items: start !important;
        }


        /* Cards */

        .print-area .report-card {
            box-shadow: none !important;

            border: 1px solid #d1d5db !important;

            border-radius: 8px !important;

            margin-bottom: 15px !important;

            break-inside: auto;
        }


        .print-area .report-card-header {
            background: #111827 !important;

            color: #fff !important;

            padding: 10px 12px !important;

            -webkit-print-color-adjust: exact !important;

            print-color-adjust: exact !important;
        }

        .print-area .report-card-header h3 {
            font-size: 13px !important;
        }

        .print-area .report-card-header span {
            font-size: 10px !important;
        }


        /* Category */

        .print-area .category-row {
            padding: 8px 12px !important;
        }

        .print-area .category-name,
        .print-area .category-amount {
            font-size: 11px !important;
        }


        /* Table */

        .print-area .table-wrapper {
            overflow: visible !important;
        }

        .print-area .report-table {
            width: 100% !important;

            min-width: 0 !important;

            border-collapse: collapse !important;
        }


        .print-area .report-table thead {
            display: table-header-group;
        }


        .print-area .report-table tr {
            break-inside: avoid;
        }


        .print-area .report-table thead th {
            background: #f3f4f6 !important;

            color: #111827 !important;

            padding: 7px 8px !important;

            font-size: 9px !important;

            -webkit-print-color-adjust: exact !important;

            print-color-adjust: exact !important;
        }


        .print-area .report-table tbody td {
            padding: 7px 8px !important;

            font-size: 9px !important;

            border-bottom:
                1px solid #e5e7eb !important;
        }


        .print-area .income-amount {
            color: #15803d !important;
        }

        .print-area .expense-amount {
            color: #dc2626 !important;
        }


        .print-area .empty-state {
            padding: 15px !important;
        }
    }

</style>


<div class="reports-wrapper print-area">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="reports-header">

        <div class="reports-title">

            <h2>
                Reports
            </h2>

            <p>
                Monthly and financial account summary
            </p>

        </div>


        <div style="
            display:flex;
            gap:10px;
            align-items:center;
        ">

            {{-- PRINT --}}

            <button
                type="button"
                class="print-btn"
                onclick="window.print()"
            >
                🖨 Print Report
            </button>


            {{-- EXCEL --}}

            <a
                href="{{ route('reports.index', [
                    'period' => $period,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'export' => 'excel'
                ]) }}"
                class="print-btn"
            >
                📊 Excel Export
            </a>

        </div>

    </div>



    {{-- =========================================================
         FILTER
    ========================================================== --}}

    <div class="filter-card">

        <div class="filter-card-body">

            <form
                method="GET"
                action="{{ route('reports.index') }}"
                class="filter-form"
            >

                {{-- PERIOD --}}

                <div class="filter-group">

                    <label for="period">
                        Report Period
                    </label>

                    <select
                        name="period"
                        id="period"
                    >

                        <option
                            value="all"
                            {{ $period === 'all' ? 'selected' : '' }}
                        >
                            All Time
                        </option>

                        <option
                            value="month"
                            {{ $period === 'month' ? 'selected' : '' }}
                        >
                            This Month
                        </option>

                        <option
                            value="previous_month"
                            {{ $period === 'previous_month' ? 'selected' : '' }}
                        >
                            Previous Month
                        </option>

                        <option
                            value="year"
                            {{ $period === 'year' ? 'selected' : '' }}
                        >
                            This Year
                        </option>

                        <option
                            value="custom"
                            {{ $period === 'custom' ? 'selected' : '' }}
                        >
                            Custom Range
                        </option>

                    </select>

                </div>


                {{-- FROM DATE --}}

                <div class="filter-group">

                    <label for="from_date">
                        From Date
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        id="from_date"
                        value="{{ $fromDate }}"
                    >

                </div>


                {{-- TO DATE --}}

                <div class="filter-group">

                    <label for="to_date">
                        To Date
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        id="to_date"
                        value="{{ $toDate }}"
                    >

                </div>


                {{-- APPLY --}}

                <button
                    type="submit"
                    class="filter-btn"
                >
                    Apply
                </button>


                {{-- RESET --}}

                <a
                    href="{{ route('reports.index') }}"
                    class="reset-btn"
                >
                    Reset
                </a>

            </form>


            <div class="report-period">

                Showing report for:

                <strong>
                    {{ $reportTitle }}
                </strong>

            </div>

        </div>

    </div>



    {{-- =========================================================
         SUMMARY
    ========================================================== --}}

    <div class="summary-grid">


        {{-- TOTAL INCOME --}}

        <div class="summary-card">

            <div class="summary-label">
                Total Income
            </div>

            <div class="summary-value income-value">

                ₹{{ number_format(
                    $totalIncome,
                    2
                ) }}

            </div>

            <div class="card-sub">
                Recorded income for selected period
            </div>

        </div>


        {{-- TOTAL EXPENSE --}}

        <div class="summary-card">

            <div class="summary-label">
                Total Expense
            </div>

            <div class="summary-value expense-value">

                ₹{{ number_format(
                    $totalExpense,
                    2
                ) }}

            </div>

            <div class="card-sub">
                Recorded expenses for selected period
            </div>

        </div>


        {{-- PROFIT / LOSS --}}

        <div class="summary-card">

            <div class="summary-label">
                Net Profit / Loss
            </div>

            <div
                class="summary-value
                {{ $balance >= 0
                    ? 'balance-positive'
                    : 'balance-negative'
                }}"
            >

                ₹{{ number_format(
                    abs($balance),
                    2
                ) }}

            </div>

            <div class="card-sub">

                {{
                    $balance >= 0
                        ? 'Net Profit'
                        : 'Net Loss'
                }}

            </div>

        </div>

    </div>



    {{-- =========================================================
         CATEGORY + INCOME SUMMARY
    ========================================================== --}}

    <div class="split-grid">


        {{-- EXPENSE CATEGORY --}}

        <div class="report-card">

            <div class="report-card-header">

                <h3>
                    Expense by Category
                </h3>

                <span>
                    {{ $expenses->count() }} records
                </span>

            </div>


            @if(count($expenseCategories) > 0)

                <div class="category-list">

                    @foreach(
                        $expenseCategories
                        as $category => $amount
                    )

                        <div class="category-row">

                            <div class="category-name">

                                {{ $category }}

                            </div>

                            <div class="category-amount">

                                ₹{{ number_format(
                                    $amount,
                                    2
                                ) }}

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-state">

                    No expense categories found.

                </div>

            @endif

        </div>



        {{-- INCOME SUMMARY --}}

        <div class="report-card">

            <div class="report-card-header">

                <h3>
                    Income Summary
                </h3>

                <span>
                    {{ $incomes->count() }} records
                </span>

            </div>


            @if($incomes->count() > 0)

                <div class="category-list">


                    {{-- TOTAL RECORDS --}}

                    <div class="category-row">

                        <div class="category-name">
                            Total Income Records
                        </div>

                        <div
                            class="category-amount"
                            style="color:#15803d;"
                        >
                            {{ $incomes->count() }}
                        </div>

                    </div>


                    {{-- AVERAGE --}}

                    <div class="category-row">

                        <div class="category-name">
                            Average Income
                        </div>

                        <div
                            class="category-amount"
                            style="color:#15803d;"
                        >

                            ₹{{ number_format(
                                $totalIncome /
                                $incomes->count(),
                                2
                            ) }}

                        </div>

                    </div>


                    {{-- HIGHEST --}}

                    <div class="category-row">

                        <div class="category-name">
                            Highest Income
                        </div>

                        <div
                            class="category-amount"
                            style="color:#15803d;"
                        >

                            ₹{{ number_format(
                                (float)
                                $incomes->max('amount'),
                                2
                            ) }}

                        </div>

                    </div>

                </div>

            @else

                <div class="empty-state">

                    No income records found.

                </div>

            @endif

        </div>

    </div>



    {{-- =========================================================
         INCOME + EXPENSE RECORDS
         SIDE BY SIDE
    ========================================================== --}}

    <div class="split-grid">


        {{-- =====================================================
             INCOME RECORDS
        ====================================================== --}}

        <div class="report-card">

            <div class="report-card-header">

                <h3>
                    Income Records
                </h3>

                <span>
                    {{ $incomes->count() }} records
                </span>

            </div>


            <div class="table-wrapper">

                @if($incomes->count() > 0)

                    <table class="report-table">

                        <thead>

                            <tr>

                                {{-- DATE --}}

                                <th>
                                    Date
                                </th>


                                {{-- DRIVER ID ONLY --}}

                                <th>
                                    Driver ID
                                </th>


                                {{-- DESCRIPTION --}}

                                <th>
                                    Description
                                </th>


                                {{-- AMOUNT --}}

                                <th
                                    style="text-align:right;"
                                >
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($incomes as $income)

                                <tr>


                                    {{-- DATE --}}

                                    <td>

                                        @if(
                                            !empty(
                                                $income->income_date
                                            )
                                        )

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $income->income_date
                                                )->format('d-m-Y')
                                            }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    {{-- DRIVER ID ONLY --}}
                                    {{-- NO DRIVER NAME --}}
                                    {{-- NO VEHICLE NUMBER --}}

                                    <td>

                                        {{
                                            $income->driver->ftaxi_driver_id
                                            ?? $income->driver->driver_id
                                            ?? $income->driver_id
                                            ?? $income->driver->id
                                            ?? '-'
                                        }}

                                    </td>


                                    {{-- DESCRIPTION --}}

                                    <td>

                                        {{
                                            $income->description
                                            ?? '-'
                                        }}

                                    </td>


                                    {{-- AMOUNT --}}

                                    <td class="income-amount">

                                        ₹{{ number_format(
                                            (float)
                                            $income->amount,
                                            2
                                        ) }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <div class="empty-state">

                        No income records found for this period.

                    </div>

                @endif

            </div>

        </div>



        {{-- =====================================================
             EXPENSE RECORDS
        ====================================================== --}}

        <div class="report-card">

            <div class="report-card-header">

                <h3>
                    Expense Records
                </h3>

                <span>
                    {{ $expenses->count() }} records
                </span>

            </div>


            <div class="table-wrapper">

                @if($expenses->count() > 0)

                    <table class="report-table">

                        <thead>

                            <tr>

                                {{-- DATE --}}

                                <th>
                                    Date
                                </th>


                                {{-- CATEGORY --}}

                                <th>
                                    Category
                                </th>


                                {{-- DESCRIPTION --}}

                                <th>
                                    Description
                                </th>


                                {{-- AMOUNT --}}

                                <th
                                    style="text-align:right;"
                                >
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($expenses as $expense)

                                <tr>


                                    {{-- DATE --}}

                                    <td>

                                        @if(
                                            !empty(
                                                $expense->expense_date
                                            )
                                        )

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $expense->expense_date
                                                )->format('d-m-Y')
                                            }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    {{-- CATEGORY --}}

                                    <td>

                                        {{
                                            $expense->category
                                            ?? 'Uncategorized'
                                        }}

                                    </td>


                                    {{-- DESCRIPTION --}}

                                    <td>

                                        {{
                                            $expense->description
                                            ?? '-'
                                        }}

                                    </td>


                                    {{-- AMOUNT --}}

                                    <td class="expense-amount">

                                        ₹{{ number_format(
                                            (float)
                                            $expense->amount,
                                            2
                                        ) }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <div class="empty-state">

                        No expense records found for this period.

                    </div>

                @endif

            </div>

        </div>

    </div>


</div>

@endsection