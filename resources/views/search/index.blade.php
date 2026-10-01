@extends('layouts.app')

@section('content')

<style>
    .search-page {
        max-width: 1080px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        color: #0f172a;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .search-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 3px 14px rgba(15, 23, 42, 0.07);
        margin-bottom: 22px;
    }

    .search-row {
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
    }

    .search-btn,
    .clear-btn {
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .search-btn {
        background: #111827;
        color: #fff;
    }

    .clear-btn {
        background: #6b7280;
        color: #fff;
    }

    .search-help {
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
    }

    .results-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .result-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 3px 14px rgba(15, 23, 42, 0.07);
    }

    .result-header {
        background: #111827;
        color: #fff;
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .result-header h2 {
        margin: 0;
        font-size: 16px;
    }

    .result-count {
        font-size: 12px;
        color: #cbd5e1;
    }

    .result-body {
        padding: 0;
    }

    .result-item {
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .result-item:last-child {
        border-bottom: none;
    }

    .result-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 7px;
    }

    .result-label {
        color: #64748b;
        font-size: 12px;
    }

    .result-value {
        color: #0f172a;
        font-size: 13px;
        font-weight: 600;
        text-align: right;
    }

    .income-amount {
        color: #16a34a;
        font-weight: 700;
    }

    .expense-amount {
        color: #dc2626;
        font-weight: 700;
    }

    .empty-state {
        padding: 38px 20px;
        text-align: center;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 800px) {
        .results-grid {
            grid-template-columns: 1fr;
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

<div class="search-page">

    <div class="page-header">
        <h1>Search</h1>
        <p>Search across income and expense records</p>
    </div>

    <div class="search-card">

        <form method="GET" action="{{ route('search') }}">

            <div class="search-row">

                <input
                    type="text"
                    name="search"
                    class="search-input"
                    value="{{ $search }}"
                    placeholder="Search Driver ID, description, amount or date..."
                    autocomplete="off"
                >

                <button type="submit" class="search-btn">
                    Search
                </button>

                <a href="{{ route('search') }}" class="clear-btn">
                    Clear
                </a>

            </div>

        </form>

        <div class="search-help">
            Enter a search term to find income or expense records.
        </div>

    </div>


    <div class="results-grid">

        {{-- INCOME --}}
        <div class="result-card">

            <div class="result-header">
                <h2>💰 Income</h2>

                <span class="result-count">
                    {{ $incomes->count() }} results
                </span>
            </div>

            <div class="result-body">

                @forelse($incomes as $income)

                    <div class="result-item">

                        <div class="result-row">
                            <span class="result-label">
                                Date
                            </span>

                            <span class="result-value">
                                {{ \Carbon\Carbon::parse($income->income_date)->format('d-m-Y') }}
                            </span>
                        </div>

                        <div class="result-row">
                            <span class="result-label">
                                Driver ID
                            </span>

                            <span class="result-value">
                                {{ $income->driver_id }}
                            </span>
                        </div>

                        <div class="result-row">
                            <span class="result-label">
                                Description
                            </span>

                            <span class="result-value">
                                {{ $income->description ?: '-' }}
                            </span>
                        </div>

                        <div class="result-row">
                            <span class="result-label">
                                Amount
                            </span>

                            <span class="result-value income-amount">
                                ₹{{ number_format((float) $income->amount, 2) }}
                            </span>
                        </div>

                    </div>

                @empty

                    <div class="empty-state">
                        No income records found.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- EXPENSE --}}
        <div class="result-card">

            <div class="result-header">
                <h2>💸 Expenses</h2>

                <span class="result-count">
                    {{ $expenses->count() }} results
                </span>
            </div>

            <div class="result-body">

                @forelse($expenses as $expense)

                    <div class="result-item">

                        <div class="result-row">
                            <span class="result-label">
                                Date
                            </span>

                            <span class="result-value">
                                {{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}
                            </span>
                        </div>

                        <div class="result-row">
                            <span class="result-label">
                                Description
                            </span>

                            <span class="result-value">
                                {{ $expense->description ?: '-' }}
                            </span>
                        </div>

                        <div class="result-row">
                            <span class="result-label">
                                Amount
                            </span>

                            <span class="result-value expense-amount">
                                ₹{{ number_format((float) $expense->amount, 2) }}
                            </span>
                        </div>

                    </div>

                @empty

                    <div class="empty-state">
                        No expense records found.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection