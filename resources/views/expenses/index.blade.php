@extends('layouts.app')

@section('content')

<style>
    .expense-page {
        max-width: 1180px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .page-header h2 {
        margin: 0 0 6px;
        font-size: 30px;
        color: #111827;
    }

    .page-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .add-btn {
        background: #111827;
        color: #fff;
        text-decoration: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        display: inline-block;
    }

    .advance-btn {
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        display: inline-block;
        margin-left: 8px;
    }

    .search-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
    }

    .search-card label {
        display: block;
        margin-bottom: 9px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .search-row {
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .search-btn,
    .clear-btn {
        height: 44px;
        padding: 0 20px;
        border: none;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .clear-btn {
        background: #6b7280;
    }

    .search-help {
        margin-top: 9px;
        color: #64748b;
        font-size: 13px;
    }

    .records-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
        overflow-x: auto;
    }

    .records-title {
        margin: 0 0 18px;
        font-size: 22px;
        color: #111827;
    }

    .expense-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .expense-table th {
        background: #343a40;
        color: #fff;
        text-align: left;
        padding: 14px 16px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .expense-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        color: #334155;
        font-size: 14px;
        white-space: nowrap;
    }

    .expense-table tr:last-child td {
        border-bottom: none;
    }

    .amount {
        color: #dc2626;
        font-weight: 700;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .voucher-btn,
    .edit-btn,
    .delete-btn {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .voucher-btn {
        background: #198754;
        color: #fff;
        text-decoration: none;
    }

    .edit-btn {
        background: #0d6efd;
        color: #fff;
        text-decoration: none;
    }

    .delete-btn {
        background: #dc3545;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .empty-message {
        text-align: center;
        padding: 35px;
        color: #64748b;
    }

    .success-message {
        background: #ecfdf3;
        border: 1px solid #b7e4c7;
        color: #166534;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    @media(max-width:700px) {

        .page-header {
            flex-direction: column;
            gap: 15px;
        }

        .search-row {
            flex-direction: column;
        }

        .search-btn,
        .clear-btn {
            width: 100%;
        }

    }
</style>


<div class="expense-page">

    {{-- PAGE HEADER --}}

    <div class="page-header">

        <div>

            <h2>
                Expense Records
            </h2>

            <p>
                Manage daily business expenses
            </p>

        </div>


        <div>

            <a
                href="{{ route('expenses.create') }}"
                class="add-btn"
            >
                + Add Expense
            </a>


            <a
                href="{{ route('expenses.advances.index') }}"
                class="advance-btn"
            >
                Employee Advances
            </a>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    {{-- SEARCH --}}

    <div class="search-card">

        <form
            method="GET"
            action="{{ route('expenses.index') }}"
        >

            <label for="search">
                Search Expenses
            </label>


            <div class="search-row">

                <input
                    type="text"
                    name="search"
                    id="search"
                    class="search-input"
                    value="{{ request('search') }}"
                    placeholder="Search expense name, category, description, amount or date..."
                >


                @if(request('search'))

                    <a
                        href="{{ route('expenses.index') }}"
                        class="clear-btn"
                    >
                        Clear
                    </a>

                @else

                    <button
                        type="submit"
                        class="search-btn"
                    >
                        Search
                    </button>

                @endif

            </div>


            <div class="search-help">
                Search by expense name, category, description,
                amount, payment mode or date.
            </div>

        </form>

    </div>


    {{-- EXPENSE RECORDS --}}

    <div class="records-card">

        <h3 class="records-title">
            Expense Records
        </h3>


        @if($expenses->count())

            <table class="expense-table">

                <thead>

                    <tr>

                        <th>
                            Date
                        </th>

                        <th>
                            Expense Name
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Payment Mode
                        </th>

                        <th>
                            Description
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($expenses as $expense)

                        <tr>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $expense->expense_date
                                )->format('d-m-Y') }}
                            </td>


                            <td>
                                {{ $expense->expense_name }}
                            </td>


                            <td>
                                {{ $expense->category ?? '-' }}
                            </td>


                            <td>

                                @if($expense->payment_mode === 'account')
                                    A/C
                                @else
                                    Cash
                                @endif

                            </td>


                            <td>
                                {{ $expense->description ?? '-' }}
                            </td>


                            <td class="amount">

                                ₹{{ number_format(
                                    (float) $expense->amount,
                                    2
                                ) }}

                            </td>


                            <td>

                                <div class="action-buttons">

                                    {{-- VOUCHER --}}

                                    <a
                                        href="{{ route(
                                            'expenses.voucher',
                                            $expense->id
                                        ) }}"
                                        class="voucher-btn"
                                    >
                                        Voucher
                                    </a>


                                    {{-- OWNER ONLY --}}

                                    @if(
                                        strtoupper(
                                            auth()->user()->role ?? ''
                                        ) === 'OWNER'
                                    )

                                        <a
                                            href="{{ route(
                                                'expenses.edit',
                                                $expense->id
                                            ) }}"
                                            class="edit-btn"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            action="{{ route(
                                                'expenses.destroy',
                                                $expense->id
                                            ) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm(
                                                'Are you sure you want to delete this expense?'
                                            );"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="delete-btn"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="empty-message">

                No expense records found.

            </div>

        @endif

    </div>

</div>

@endsection