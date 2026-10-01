@extends('layouts.app')

@section('content')

<style>

.advance-page {
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

.button-group {
    display: flex;
    gap: 10px;
}

.add-btn,
.back-btn {
    display: inline-block;
    padding: 12px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
}

.add-btn {
    background: #111827;
    color: #ffffff;
}

.back-btn {
    background: #e5e7eb;
    color: #111827;
}

.search-card {
    background: #ffffff;
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
}

.search-btn,
.clear-btn {
    height: 44px;
    padding: 0 20px;
    border: none;
    border-radius: 8px;
    background: #111827;
    color: #ffffff;
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

.records-card {
    background: #ffffff;
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

.advance-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

.advance-table th {
    background: #343a40;
    color: #ffffff;
    text-align: left;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

.advance-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    color: #334155;
    font-size: 14px;
    white-space: nowrap;
}

.advance-table tr:last-child td {
    border-bottom: none;
}

.amount {
    color: #dc2626;
    font-weight: 700;
}

.cash {
    color: #15803d;
    font-weight: 700;
}

.account {
    color: #2563eb;
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
    color: #ffffff;
    text-decoration: none;
}

.edit-btn {
    background: #0d6efd;
    color: #ffffff;
    text-decoration: none;
}

.delete-btn {
    background: #dc3545;
    color: #ffffff;
    border: none;
    cursor: pointer;
}

.success-message {
    background: #ecfdf3;
    border: 1px solid #b7e4c7;
    color: #166534;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.empty-message {
    text-align: center;
    padding: 35px;
    color: #64748b;
}

@media(max-width:700px) {

    .page-header {
        flex-direction: column;
        gap: 15px;
    }

    .button-group {
        width: 100%;
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


<div class="advance-page">

    <div class="page-header">

        <div>

            <h2>
                Employee Advances
            </h2>

            <p>
                Manage employee salary advance payments
            </p>

        </div>


        <div class="button-group">

            <a
                href="{{ route('expenses.index') }}"
                class="back-btn"
            >
                ← Expenses
            </a>


            <a
                href="{{ route('expenses.advances.create') }}"
                class="add-btn"
            >
                + Salary Advance
            </a>

        </div>

    </div>


    @if(session('success'))

        <div class="success-message">

            {{ session('success') }}

        </div>

    @endif


    {{-- SEARCH --}}

    <div class="search-card">

        <form
            method="GET"
            action="{{ route('expenses.advances.index') }}"
        >

            <label for="search">
                Search Salary Advances
            </label>


            <div class="search-row">

                <input
                    type="text"
                    name="search"
                    id="search"
                    class="search-input"
                    value="{{ request('search') }}"
                    placeholder="Search employee, category, amount, payment mode, UPI ID or date..."
                >


                @if(request('search'))

                    <a
                        href="{{ route('expenses.advances.index') }}"
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

        </form>

    </div>


    {{-- RECORDS --}}

    <div class="records-card">

        <h3 class="records-title">
            Employee Salary Advance Records
        </h3>


        @if($advances->count())

            <table class="advance-table">

                <thead>

                    <tr>

                        <th>Date</th>

                        <th>Employee Name</th>

                        <th>Category</th>

                        <th>Advance Amount</th>

                        <th>Payment Mode</th>

                        <th>UPI ID</th>

                        <th>Description</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($advances as $advance)

                        <tr>

                            <td>

                                {{ \Carbon\Carbon::parse(
                                    $advance->advance_date
                                )->format('d-m-Y') }}

                            </td>


                            <td>
                                {{ $advance->employee_name }}
                            </td>


                            <td>
                                Salary Advance
                            </td>


                            <td class="amount">

                                ₹{{ number_format(
                                    (float) $advance->amount,
                                    2
                                ) }}

                            </td>


                            <td>

                                @if(
                                    $advance->payment_mode === 'account'
                                )

                                    <span class="account">
                                        A/C
                                    </span>

                                @else

                                    <span class="cash">
                                        Cash
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if(
                                    $advance->payment_mode === 'account'
                                )

                                    {{ $advance->upi_id ?? '-' }}

                                @else

                                    -

                                @endif

                            </td>


                            <td>
                                {{ $advance->description ?? '-' }}
                            </td>


                            <td>

                                <div class="action-buttons">

                                    {{-- OWNER + MANAGER --}}

                                    <a
                                        href="{{ route(
                                            'expenses.advances.voucher',
                                            $advance->id
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
                                                'expenses.advances.edit',
                                                $advance->id
                                            ) }}"
                                            class="edit-btn"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            action="{{ route(
                                                'expenses.advances.destroy',
                                                $advance->id
                                            ) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm(
                                                'Are you sure you want to delete this salary advance?'
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

                No salary advance records found.

            </div>

        @endif

    </div>

</div>

@endsection