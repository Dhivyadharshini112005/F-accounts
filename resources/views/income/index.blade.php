@extends('layouts.app')

@section('content')

<style>
    .income-page {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding: 25px 20px 40px;
    }

    .income-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .income-header h2 {
        margin: 0;
        font-size: 30px;
        color: #111827;
    }

    .income-header p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .add-income-btn {
        background: #111827;
        color: #fff;
        text-decoration: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
    }

    .success-box {
        background: #ecfdf3;
        border: 1px solid #a7f3d0;
        color: #166534;
        padding: 13px 16px;
        border-radius: 9px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .search-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
    }

    .search-box label {
        display: block;
        margin-bottom: 8px;
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

    .search-button,
    .clear-button {
        height: 44px;
        padding: 0 20px;
        border: 0;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .clear-button {
        background: #6b7280;
    }

    .search-help {
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
    }

    .table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
        overflow-x: auto;
    }

    .table-title {
        padding: 18px 20px;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        border-bottom: 1px solid #e5e7eb;
    }

    .income-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .income-table th {
        background: #111827;
        color: #fff;
        text-align: left;
        padding: 13px 15px;
        font-size: 13px;
        white-space: nowrap;
    }

    .income-table td {
        padding: 13px 15px;
        border-bottom: 1px solid #e5e7eb;
        color: #334155;
        font-size: 14px;
        white-space: nowrap;
    }

    .income-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .amount {
        font-weight: 700;
        color: #111827;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .edit-button {
        background: #0d6efd;
        color: #fff;
        text-decoration: none;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
    }

    .delete-button {
        background: #dc3545;
        color: #fff;
        border: 0;
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .empty-box {
        padding: 35px;
        text-align: center;
        color: #64748b;
        font-size: 15px;
    }

    @media (max-width: 700px) {
        .income-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .search-row {
            flex-direction: column;
        }

        .search-button,
        .clear-button {
            width: 100%;
        }
    }
</style>

<div class="income-page">

    <div class="income-header">
        <div>
            <h2>Income</h2>
            <p>Manage all income records.</p>
        </div>

        <a href="{{ route('income.create') }}" class="add-income-btn">
            + Add Income
        </a>
    </div>

    @if(session('success'))
        <div class="success-box">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="search-box">
        <form method="GET" action="{{ route('income.index') }}">

            <label for="income-search">Search Income</label>

            <div class="search-row">

                <input
                    type="text"
                    id="income-search"
                    name="search"
                    class="search-input"
                    value="{{ request('search') }}"
                    placeholder="Search by driver ID, description, amount or date..."
                >

                @if(request('search'))
                    <a href="{{ route('income.index') }}" class="clear-button">
                        Clear
                    </a>
                @else
                    <button type="submit" class="search-button">
                        Search
                    </button>
                @endif

            </div>

            <div class="search-help">
                Search by Driver ID, description, fees amount, GST amount,
                total amount or date.
            </div>

        </form>
    </div>

    <div class="table-card">

        <div class="table-title">
            Income Records
        </div>

        @if($incomes->count() > 0)

            <table class="income-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver ID</th>
                        <th>Description</th>
                        <th>Fees Amount</th>
                        <th>GST Amount</th>
                        <th>Total Amount</th>

                        @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @foreach($incomes as $income)

                        <tr>

                            <td>
                                @if($income->income_date)
                                    {{ \Carbon\Carbon::parse($income->income_date)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $income->driver_id ?? '-' }}
                            </td>

                            <td>
                                {{ $income->description ?? '-' }}
                            </td>

                            <td class="amount">
                                ₹{{ number_format((float)($income->fees_amount ?? $income->amount ?? 0), 2) }}
                            </td>

                            <td class="amount">
                                ₹{{ number_format((float)($income->gst_amount ?? 0), 2) }}
                            </td>

                            <td class="amount">
                                ₹{{ number_format((float)($income->total_amount ?? $income->amount ?? 0), 2) }}
                            </td>

                            @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')

                                <td>
                                    <div class="action-buttons">

                                        <a
                                            href="{{ route('income.edit', $income->id) }}"
                                            class="edit-button"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('income.destroy', $income->id) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this income record?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="delete-button"
                                            >
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            @endif

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="empty-box">
                No income records found.
            </div>

        @endif

    </div>

</div>

@endsection
