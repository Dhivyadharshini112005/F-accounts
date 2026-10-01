@extends('layouts.app')

@section('content')

<style>
    .driver-account-wrapper {
        max-width: 1450px;
        margin: 0 auto;
        padding: 25px;
    }

    .account-header {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: #fff;
        border-radius: 18px;
        padding: 24px 28px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }

    .account-header h1 {
        margin: 0;
        font-size: 27px;
        font-weight: 700;
    }

    .account-header p {
        margin: 6px 0 0;
        color: #cbd5e1;
        font-size: 14px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 16px;
        border-radius: 9px;
        border: 1px solid #374151;
        background: #fff;
        color: #111827;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: 0.2s ease;
    }

    .back-btn:hover {
        background: #f3f4f6;
        color: #111827;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .account-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 17px;
        margin-bottom: 22px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .account-card-header {
        padding: 18px 22px;
        background: #111827;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .account-card-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }

    .account-card-header span {
        color: #cbd5e1;
        font-size: 12px;
    }

    .driver-info {
        padding: 22px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .info-item {
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .info-label {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .info-value {
        margin-top: 6px;
        color: #111827;
        font-size: 15px;
        font-weight: 700;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        right: -25px;
        top: -25px;
        background: rgba(37,99,235,0.07);
    }

    .stat-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
    }

    .stat-value {
        margin-top: 8px;
        color: #111827;
        font-size: 28px;
        font-weight: 750;
    }

    .stat-sub {
        margin-top: 4px;
        color: #9ca3af;
        font-size: 12px;
    }

    .income-stat .stat-value {
        color: #15803d;
    }

    .pending-stat .stat-value {
        color: #dc2626;
    }

    .records-stat .stat-value {
        color: #2563eb;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 650px;
    }

    .history-table thead th {
        background: #f8fafc;
        color: #4b5563;
        padding: 14px 18px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.35px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .history-table tbody td {
        padding: 14px 18px;
        color: #374151;
        font-size: 13px;
        border-bottom: 1px solid #eef0f3;
        white-space: nowrap;
    }

    .history-table tbody tr:hover {
        background: #f9fafb;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    .history-amount {
        color: #15803d;
        font-weight: 700;
        text-align: right;
    }

    .empty-state {
        padding: 45px 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 14px;
    }

    .empty-icon {
        font-size: 34px;
        margin-bottom: 8px;
        opacity: 0.5;
    }



    .alert-box {
        margin-bottom: 18px;
        padding: 12px 15px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .alert-success {
        background: #ecfdf3;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .payment-form {
        padding: 20px 22px;
        display: grid;
        grid-template-columns: 1fr 1fr 1.6fr auto;
        gap: 12px;
        align-items: end;
    }

    .payment-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .payment-field label {
        color: #374151;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .payment-field input {
        height: 42px;
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 0 12px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        outline: none;
    }

    .payment-field input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
    }

    .payment-btn {
        height: 42px;
        border: 0;
        border-radius: 9px;
        padding: 0 17px;
        background: #15803d;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    .payment-btn:hover {
        background: #166534;
    }

    .payment-closed {
        padding: 20px 22px;
        color: #166534;
        font-size: 13px;
        font-weight: 700;
        background: #f0fdf4;
    }

    .payment-note {
        margin-top: 5px;
        color: #9ca3af;
        font-size: 11px;
    }

    @media (max-width: 1000px) {
        .payment-form {
            grid-template-columns: repeat(2, 1fr);
        }

        .payment-btn {
            width: 100%;
        }
        .driver-info {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .driver-account-wrapper {
            padding: 15px;
        }

        .account-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .back-btn {
            width: 100%;
        }

        .driver-info {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="driver-account-wrapper">

    <div class="account-header">

        <div>
            <h1>Driver Account</h1>
            <p>Payment details and account history</p>
        </div>

        <a
            href="{{ route('drivers.index') }}"
            class="back-btn"
        >
            ← Back to Drivers
        </a>

    </div>

    @if(session('success'))
        <div class="alert-box alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-box alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="account-card">

        <div class="account-card-header">

            <h2>
                {{ $driver->name ?? 'Driver' }}
            </h2>

            <span>
                Driver Account
            </span>

        </div>

        <div class="driver-info">

            <div class="info-item">
                <div class="info-label">Driver ID</div>
                <div class="info-value">{{ $driver->id }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $driver->phone ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Vehicle Number</div>
                <div class="info-value">{{ $driver->vehicle_number ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    {{ ucfirst($driver->status ?? 'Active') }}
                </div>
            </div>

        </div>

    </div>

    @php
        $pendingAmount = (float) ($driver->pending_amount ?? 0);
        $totalPayments = (float) ($totalPaid ?? 0);
        $paymentRecords = $incomes->count();
    @endphp

    <div class="stats-grid">

        <div class="stat-card income-stat">

            <div class="stat-label">
                Total Payments
            </div>

            <div class="stat-value">
                ₹{{ number_format($totalPayments, 2) }}
            </div>

            <div class="stat-sub">
                Total recorded income
            </div>

        </div>

        <div class="stat-card pending-stat">

            <div class="stat-label">
                Pending Amount
            </div>

            <div class="stat-value">
                ₹{{ number_format($pendingAmount, 2) }}
            </div>

            <div class="stat-sub">
                Current pending driver amount
            </div>

        </div>

        <div class="stat-card records-stat">

            <div class="stat-label">
                Payment Records
            </div>

            <div class="stat-value">
                {{ number_format($paymentRecords) }}
            </div>

            <div class="stat-sub">
                Recorded payment entries
            </div>

        </div>

    </div>


    <div class="account-card">

        <div class="account-card-header">

            <h2>
                Record Payment
            </h2>

            <span>
                Pending ₹{{ number_format($pendingAmount, 2) }}
            </span>

        </div>

        @if($pendingAmount > 0)

            <form
                method="POST"
                action="{{ route('drivers.payment', $driver->id) }}"
                class="payment-form"
            >
                @csrf

                <div class="payment-field">
                    <label for="amount">Payment Amount</label>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        min="0.01"
                        max="{{ number_format($pendingAmount, 2, '.', '') }}"
                        step="0.01"
                        placeholder="0.00"
                        value="{{ old('amount') }}"
                        required
                    >
                    <div class="payment-note">
                        Maximum: ₹{{ number_format($pendingAmount, 2) }}
                    </div>
                </div>

                <div class="payment-field">
                    <label for="payment_date">Payment Date</label>
                    <input
                        type="date"
                        name="payment_date"
                        id="payment_date"
                        value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                    >
                </div>

                <div class="payment-field">
                    <label for="description">Description</label>
                    <input
                        type="text"
                        name="description"
                        id="description"
                        maxlength="255"
                        placeholder="Driver payment"
                        value="{{ old('description') }}"
                    >
                </div>

                <button
                    type="submit"
                    class="payment-btn"
                >
                    + Record Payment
                </button>
            </form>

        @else

            <div class="payment-closed">
                ✓ No pending amount. This driver's account is fully settled.
            </div>

        @endif

    </div>

    <div class="account-card">

        <div class="account-card-header">

            <h2>
                Payment History
            </h2>

            <span>
                {{ $paymentRecords }} records
            </span>

        </div>

        <div class="table-wrapper">

            @if($paymentRecords > 0)

                <table class="history-table">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th style="text-align:right;">Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($incomes as $income)

                            <tr>

                                <td>

                                    @if(!empty($income->income_date))

                                        {{ \Carbon\Carbon::parse($income->income_date)->format('d-m-Y') }}

                                    @elseif(!empty($income->date))

                                        {{ \Carbon\Carbon::parse($income->date)->format('d-m-Y') }}

                                    @elseif($income->created_at)

                                        {{ $income->created_at->format('d-m-Y') }}

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>
                                    {{ $income->description ?? '-' }}
                                </td>

                                <td class="history-amount">
                                    ₹{{ number_format((float)$income->amount, 2) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        💳
                    </div>

                    No payment records found.

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
