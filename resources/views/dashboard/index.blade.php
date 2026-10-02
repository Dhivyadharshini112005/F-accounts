@extends('layouts.app')

@section('content')

<style>
    * {
        box-sizing: border-box;
    }

    body {
        background: #f4f6f9;
    }

    .dashboard-wrapper {
        max-width: 1500px;
        margin: 0 auto;
        padding: 25px;
    }

    /* ================= HEADER ================= */

    .dashboard-header {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: #fff;
        border-radius: 18px;
        padding: 25px 28px;
        margin-bottom: 24px;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }

    .dashboard-title h2 {
        margin: 0;
        font-size: 27px;
        font-weight: 700;
    }

    .dashboard-title p {
        margin: 7px 0 0;
        color: #cbd5e1;
        font-size: 14px;
    }

    /* ================= ACTION BUTTONS ================= */

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-btn {
        text-decoration: none;
        color: #fff;

        border-radius: 10px;
        padding: 11px 18px;

        font-size: 14px;
        font-weight: 600;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        transition: 0.2s ease;
    }

    .action-btn:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .btn-income {
        background: #16a34a;
        box-shadow: 0 4px 12px rgba(22,163,74,0.25);
    }

    .btn-income:hover {
        background: #15803d;
    }

    .btn-expense {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(220,38,38,0.25);
    }

    .btn-expense:hover {
        background: #b91c1c;
    }

    /* ================= SUMMARY CARDS ================= */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 25px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;

        border-radius: 17px;
        padding: 23px;

        color: #fff;
        min-height: 145px;

        box-shadow: 0 8px 22px rgba(0,0,0,0.10);

        transition: 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.14);
    }

    .summary-card::after {
        content: "";

        position: absolute;

        width: 110px;
        height: 110px;

        border-radius: 50%;

        background: rgba(255,255,255,0.10);

        right: -30px;
        top: -30px;
    }

    .income-card {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }

    .expense-card {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .balance-card {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
    }

    /* Main Balance is display-only. It must not open the payment breakdown. */
    .non-clickable-balance {
        cursor: default !important;
    }

    .non-clickable-balance:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.14);
    }

    .driver-card {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    }

    .profit-card {
        background: linear-gradient(135deg, #0891b2, #06b6d4);
    }

    .card-label {
        font-size: 13px;
        font-weight: 600;

        opacity: 0.92;

        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .card-value {
        margin-top: 10px;

        font-size: 27px;
        font-weight: 750;
    }

    .card-description {
        margin-top: 7px;

        font-size: 12px;
        opacity: 0.85;
    }

    .card-split {
        margin-top: 9px;
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        font-size: 12px;
        font-weight: 600;
        opacity: 0.95;
    }

    .card-icon {
        position: absolute;

        right: 20px;
        bottom: 18px;

        font-size: 34px;
        opacity: 0.18;
    }


    /* ================= CASH / A-C BALANCE CARDS ================= */

    .balance-breakdown-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 25px;
    }

    .balance-breakdown-card {
        position: relative;
        overflow: hidden;
        border-radius: 17px;
        padding: 23px;
        color: #fff;
        min-height: 135px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.10);
        transition: 0.2s ease;
    }

    .balance-breakdown-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.14);
    }

    .balance-breakdown-card::after {
        content: "";
        position: absolute;
        width: 105px;
        height: 105px;
        border-radius: 50%;
        background: rgba(255,255,255,0.10);
        right: -28px;
        top: -28px;
    }

    .cash-balance-card {
        background: linear-gradient(135deg, #15803d, #22c55e);
    }

    .account-balance-card {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    }

    .negative-balance-card {
        background: linear-gradient(135deg, #991b1b, #ef4444) !important;
    }

    .balance-breakdown-description {
        margin-top: 7px;
        font-size: 12px;
        opacity: 0.88;
    }

    .balance-breakdown-card .card-icon {
        position: absolute;
        right: 20px;
        bottom: 18px;
        font-size: 34px;
        opacity: 0.18;
    }

    /* ================= SECTION CARD ================= */

    .section-card {
        background: #fff;

        border: 1px solid #e5e7eb;
        border-radius: 17px;

        overflow: hidden;

        margin-bottom: 25px;

        box-shadow: 0 5px 18px rgba(0,0,0,0.05);
    }

    .section-header {
        background: #111827;

        color: #fff;

        padding: 18px 22px;

        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h3 {
        margin: 0;

        font-size: 18px;
        font-weight: 700;
    }

    .section-header span {
        color: #cbd5e1;

        font-size: 13px;
    }

    /* ================= MONTHLY CARDS ================= */

    .monthly-grid {
        padding: 22px;

        display: grid;
        grid-template-columns: repeat(4, 1fr);

        gap: 16px;
    }

    .monthly-card {
        background: #f8fafc;

        border: 1px solid #e5e7eb;

        border-radius: 13px;

        padding: 18px;

        transition: 0.2s ease;
    }

    .monthly-card:hover {
        transform: translateY(-2px);

        box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    }

    .monthly-title {
        color: #6b7280;

        font-size: 12px;
        font-weight: 700;

        text-transform: uppercase;

        margin-bottom: 8px;
    }

    .monthly-value {
        color: #111827;

        font-size: 20px;
        font-weight: 700;
    }

    .monthly-green {
        border-left: 4px solid #16a34a;
    }

    .monthly-blue {
        border-left: 4px solid #2563eb;
    }

    .monthly-purple {
        border-left: 4px solid #7c3aed;
    }

    .monthly-orange {
        border-left: 4px solid #ea580c;
    }

    .monthly-red {
        border-left: 4px solid #dc2626;
    }

    .monthly-teal {
        border-left: 4px solid #0d9488;
    }

    /* ================= TWO COLUMNS ================= */

    .two-column {
        display: grid;

        grid-template-columns: 1fr 1fr;

        gap: 22px;
    }

    /* ================= TABLE ================= */

    .table-wrapper {
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;

        border-collapse: collapse;
    }

    .dashboard-table thead th {
        background: #f8fafc;

        color: #4b5563;

        padding: 14px 18px;

        font-size: 12px;
        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: 0.4px;

        text-align: left;

        border-bottom: 1px solid #e5e7eb;

        white-space: nowrap;
    }

    .dashboard-table tbody td {
        padding: 15px 18px;

        color: #374151;

        font-size: 14px;

        border-bottom: 1px solid #eef0f3;

        white-space: nowrap;
    }

    .dashboard-table tbody tr:hover {
        background: #f9fafb;
    }

    .dashboard-table tbody tr:last-child td {
        border-bottom: none;
    }

    .amount-income {
        color: #16a34a;

        font-weight: 700;
    }

    .amount-expense {
        color: #dc2626;

        font-weight: 700;
    }

    /* ================= VIEW ALL ================= */

    .view-all {
        padding: 14px 18px;

        border-top: 1px solid #eef0f3;

        text-align: right;
    }

    .view-all a {
        color: #2563eb;

        font-size: 13px;

        font-weight: 600;

        text-decoration: none;
    }

    .view-all a:hover {
        text-decoration: underline;
    }

    /* ================= EMPTY ================= */

    .empty-state {
        padding: 35px;

        text-align: center;

        color: #9ca3af;

        font-size: 14px;
    }

    /* ================= PAYMENT BREAKDOWN ================= */

    .payment-summary-trigger {
        border: 0;
        font: inherit;
        text-align: left;
        width: 100%;
        cursor: pointer;
    }

    .payment-breakdown-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
        margin-bottom: 25px;
    }

    .payment-breakdown-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 17px;
        overflow: hidden;
        box-shadow: 0 5px 18px rgba(0,0,0,.05);
    }

    .payment-breakdown-header {
        background: #111827;
        color: #fff;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .payment-breakdown-header h3 {
        margin: 0;
        font-size: 17px;
    }

    .payment-breakdown-header span {
        color: #cbd5e1;
        font-size: 12px;
    }

    .payment-breakdown-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        padding: 20px;
    }

    .payment-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 17px;
    }

    .payment-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .payment-value {
        margin-top: 8px;
        color: #111827;
        font-size: 22px;
        font-weight: 800;
    }

    .income-payment { border-left: 4px solid #16a34a; }
    .expense-payment { border-left: 4px solid #dc2626; }

    .payment-breakdown-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
    }

    .payment-breakdown-overlay.show { display: flex; }

    .payment-breakdown-modal {
        width: min(560px, 100%);
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        position: relative;
        box-shadow: 0 25px 60px rgba(0,0,0,.25);
    }

    .payment-breakdown-modal h3 {
        margin: 0 0 5px;
        font-size: 22px;
        color: #111827;
    }

    .payment-breakdown-subtitle {
        margin: 0 0 20px;
        color: #64748b;
        font-size: 13px;
    }

    .payment-breakdown-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .payment-breakdown-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
    }

    .payment-breakdown-item .label {
        color: #64748b;
        font-size: 13px;
    }

    .payment-breakdown-item .value {
        margin-top: 8px;
        color: #111827;
        font-size: 25px;
        font-weight: 800;
    }

    .payment-breakdown-total {
        margin-top: 15px;
        padding: 17px;
        background: #111827;
        color: #fff;
        border-radius: 11px;
        display: flex;
        justify-content: space-between;
    }

    .payment-breakdown-total strong { font-size: 22px; }

    .payment-breakdown-close {
        position: absolute;
        top: 9px;
        right: 14px;
        border: 0;
        background: transparent;
        color: #64748b;
        font-size: 28px;
        cursor: pointer;
    }


    /* ================= GRAPH ================= */

    .chart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 17px;
        padding: 22px;
        margin-bottom: 25px;
        box-shadow: 0 5px 18px rgba(0,0,0,.05);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .chart-header h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
    }

    .chart-header span {
        color: #64748b;
        font-size: 13px;
    }

    .chart-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-controls label {
        color: #374151;
        font-size: 13px;
        font-weight: 700;
    }

    .chart-period-select {
        min-width: 145px;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        outline: none;
    }

    .chart-period-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    .dashboard-chart {
        position: relative;
        height: 330px;
        padding: 15px 10px 0 45px;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .chart-y-axis {
        position: absolute;
        left: 0;
        top: 15px;
        bottom: 45px;
        width: 42px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-end;
        padding-right: 7px;
        color: #94a3b8;
        font-size: 10px;
    }

    .chart-grid-lines {
        position: absolute;
        left: 45px;
        right: 5px;
        top: 15px;
        bottom: 45px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        pointer-events: none;
    }

    .chart-grid-line {
        border-top: 1px dashed #e5e7eb;
        width: 100%;
    }

    .chart-bars-area {
        position: absolute;
        left: 45px;
        right: 5px;
        top: 15px;
        bottom: 45px;
        display: flex;
        align-items: stretch;
        gap: 12px;
        min-width: 100%;
        padding: 0 5px;
    }

    .chart-group {
        flex: 1 0 70px;
        min-width: 70px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
    }

    .chart-bar-area {
        width: 100%;
        height: calc(100% - 24px);
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 5px;
    }

    .chart-bar {
        width: 24px;
        min-height: 2px;
        border-radius: 6px 6px 0 0;
        transition: height .25s ease;
    }

    .chart-income-bar {
        background: #16a34a;
    }

    .chart-expense-bar {
        background: #dc2626;
    }

    .chart-label {
        height: 24px;
        margin-top: 4px;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
        text-align: center;
    }

    .chart-empty {
        position: absolute;
        inset: 15px 5px 45px 45px;
        display: none;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 14px;
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 22px;
        margin-top: 12px;
        color: #64748b;
        font-size: 12px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend-income {
        background: #16a34a;
    }

    .legend-expense {
        background: #dc2626;
    }

    /* ================= RESPONSIVE ================= */

    @media (max-width: 1150px) {

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .monthly-chart-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 800px) {

        .payment-breakdown-row {
            grid-template-columns: 1fr;
        }

        .dashboard-wrapper {
            padding: 15px;
        }

        .dashboard-header {
            flex-direction: column;

            align-items: flex-start;
        }

        .action-buttons {
            width: 100%;
        }

        .action-btn {
            flex: 1;
        }

        .month-toolbar {
            flex-direction: column;

            align-items: flex-start;
        }

        .month-form {
            width: 100%;
        }

        .month-form select {
            flex: 1;
        }

        .two-column {
            grid-template-columns: 1fr;
        }

        .chart-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .chart-controls {
            width: 100%;
        }

        .chart-period-select {
            flex: 1;
        }

        .dashboard-chart {
            height: 300px;
        }
    }

    @media (max-width: 550px) {

        .summary-grid {
            grid-template-columns: 1fr;
        }        .dashboard-title h2 {
            font-size: 22px;
        }
    }

    @media (max-width: 700px) {
        .balance-breakdown-row {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="dashboard-wrapper">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="dashboard-header">

        <div class="dashboard-title">

            <h2>
                Accounts Dashboard
            </h2>

            <p>
                Manage and monitor your taxi accounts
            </p>

        </div>


        <div class="action-buttons">


            {{-- ADD INCOME --}}

            <a
                href="{{ route('income.create') }}"
                class="action-btn btn-income"
            >

                <span>＋</span>

                Add Income

            </a>


            {{-- ADD EXPENSE --}}

            <a
                href="{{ url('/expenses/create') }}"
                class="action-btn btn-expense"
            >

                <span>＋</span>

                Add Expense

            </a>


        </div>

    </div>



    {{-- =========================================================
         CASH / A-C BALANCE CALCULATION
    ========================================================== --}}

    @php
        $cashIncomeValue = (float) ($cashIncome ?? 0);
        $accountIncomeValue = (float) ($accountIncome ?? 0);
        $cashExpenseValue = (float) ($cashExpense ?? 0);
        $accountExpenseValue = (float) ($accountExpense ?? 0);
        $cashAdvanceValue = (float) ($cashAdvance ?? 0);
        $accountAdvanceValue = (float) ($accountAdvance ?? 0);

        $cashBalance = (float) ($cashBalance ?? ($cashIncomeValue - $cashExpenseValue - $cashAdvanceValue));
        $accountBalance = (float) ($accountBalance ?? ($accountIncomeValue - $accountExpenseValue - $accountAdvanceValue));
        $balance = (float) ($balance ?? ($cashBalance + $accountBalance));
    @endphp

    {{-- =========================================================
         SUMMARY CARDS
    ========================================================== --}}

    <div class="summary-grid">


        {{-- TOTAL INCOME --}}

        <button type="button" class="summary-card income-card payment-summary-trigger" onclick="openPaymentBreakdown('income')" aria-label="View income Cash and A/C breakdown">

            <div class="card-label">
                Total Income
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$totalIncome, 2) }}
            </div>

            <div class="card-description">
                Total recorded income
            </div>

            <div class="card-icon">
                ₹
            </div>

        </button>


        {{-- TOTAL EXPENSE --}}

        <button type="button" class="summary-card expense-card payment-summary-trigger" onclick="openPaymentBreakdown('expense')" aria-label="View expense Cash and A/C breakdown">

            <div class="card-label">
                Total Expense
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$totalExpense, 2) }}
            </div>

            <div class="card-description">
                Total recorded expenses
            </div>

            <div class="card-icon">
                ₹
            </div>

        </button>


        {{-- BALANCE --}}

        @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
        <button type="button" class="summary-card balance-card payment-summary-trigger" id="mainBalanceCard" onclick="openPaymentBreakdown('balance')" aria-label="View Cash and A/C balance breakdown">

            <div class="card-label">
                Balance
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$balance, 2) }}
            </div>

            <div class="card-description">
                Current account balance
            </div>

            <div class="card-icon">
                ₹
            </div>

        </button>
        @else
        <div class="summary-card balance-card non-clickable-balance" id="mainBalanceCard">

            <div class="card-label">
                Balance
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$cashBalance, 2) }}
            </div>

            <div class="card-description">
                Current cash balance
            </div>

            <div class="card-icon">
                ₹
            </div>

        </div>
        @endif


        {{-- NET PROFIT / LOSS --}}

        <div class="summary-card profit-card">

            <div class="card-label">
                {{ (float)$balance >= 0 ? 'Net Profit' : 'Net Loss' }}
            </div>

            <div class="card-value">
                ₹{{ number_format(abs((float)$balance), 2) }}
            </div>

            <div class="card-description">
                Income minus expenses
            </div>

            <div class="card-icon">
                {{ (float)$balance >= 0 ? '↗' : '↘' }}
            </div>

        </div>


    </div>



    {{-- =========================================================
         CASH / A-C BALANCE CARDS
    ========================================================== --}}

    <div class="balance-breakdown-row">

        {{-- CASH BALANCE --}}
        <div class="balance-breakdown-card cash-balance-card {{ $cashBalance < 0 ? 'negative-balance-card' : '' }}">

            <div class="card-label">
                Cash Balance
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$cashBalance, 2) }}
            </div>

            <div class="balance-breakdown-description">
                Cash Income − Cash Expense − Cash Advance
            </div>

            <div class="card-icon">
                ₹
            </div>

        </div>


        @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
        {{-- A/C BALANCE --}}
        <div class="balance-breakdown-card account-balance-card {{ $accountBalance < 0 ? 'negative-balance-card' : '' }}">

            <div class="card-label">
                A/C Balance
            </div>

            <div class="card-value">
                ₹{{ number_format((float)$accountBalance, 2) }}
            </div>

            <div class="balance-breakdown-description">
                A/C Income − A/C Expense − A/C Advance
            </div>

            <div class="card-icon">
                ₹
            </div>

        </div>
        @endif

    </div>



    {{-- =========================================================
         SEPARATE PAYMENT BREAKDOWN CARDS
    ========================================================== --}}

    <div class="payment-breakdown-row">

        <div class="payment-breakdown-card">
            <div class="payment-breakdown-header">
                <h3>Income Payment Breakdown</h3>
                <span>Total Income</span>
            </div>
            <div class="payment-breakdown-body">
                <div class="payment-box income-payment">
                    <div class="payment-label">Cash</div>
                    <div class="payment-value">₹{{ number_format((float)($cashIncome ?? 0), 2) }}</div>
                </div>
                @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
                <div class="payment-box income-payment">
                    <div class="payment-label">A/C</div>
                    <div class="payment-value">₹{{ number_format((float)($accountIncome ?? 0), 2) }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="payment-breakdown-card">
            <div class="payment-breakdown-header">
                <h3>Expense Payment Breakdown</h3>
                <span>Total Expense</span>
            </div>
            <div class="payment-breakdown-body">
                <div class="payment-box expense-payment">
                    <div class="payment-label">Cash</div>
                    <div class="payment-value">₹{{ number_format((float)($cashExpense ?? 0), 2) }}</div>
                </div>
                @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
                <div class="payment-box expense-payment">
                    <div class="payment-label">A/C</div>
                    <div class="payment-value">₹{{ number_format((float)($accountExpense ?? 0), 2) }}</div>
                </div>
                @endif
            </div>
        </div>

    </div>


    {{-- =========================================================
         INCOME / EXPENSE GRAPH
    ========================================================== --}}

    @php
        $graphToday = \Carbon\Carbon::today();

        /*
         * Build graph data directly from the existing income/expense tables.
         * This keeps the existing DashboardController untouched.
         */

        // TODAY
        $todayIncome = (float) \App\Models\Income::whereDate('income_date', $graphToday)->sum('amount');
        $todayExpense = (float) \App\Models\Expense::whereDate('expense_date', $graphToday)->sum('amount');

        $todayGraph = [
            [
                'label' => 'Today',
                'income' => $todayIncome,
                'expense' => $todayExpense,
            ],
        ];

        // THIS WEEK - Monday to Sunday
        $weekStart = $graphToday->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekGraph = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);

            $weekGraph[] = [
                'label' => $day->format('D'),
                'income' => (float) \App\Models\Income::whereDate('income_date', $day)->sum('amount'),
                'expense' => (float) \App\Models\Expense::whereDate('expense_date', $day)->sum('amount'),
            ];
        }

        // THIS MONTH - one bar for every day of the current month
        $monthStart = $graphToday->copy()->startOfMonth();
        $monthEnd = $graphToday->copy()->endOfMonth();
        $monthGraph = [];

        for ($day = $monthStart->copy(); $day->lte($monthEnd); $day->addDay()) {
            $monthGraph[] = [
                'label' => $day->format('d'),
                'income' => (float) \App\Models\Income::whereDate('income_date', $day)->sum('amount'),
                'expense' => (float) \App\Models\Expense::whereDate('expense_date', $day)->sum('amount'),
            ];
        }
    @endphp

    <div class="chart-card">

        <div class="chart-header">

            <div>
                <h3 id="accountGraphTitle">Income &amp; Expense Graph</h3>
                <span id="accountGraphSubtitle">Today's income and expense</span>
            </div>

            <div class="chart-controls">
                <label for="graphPeriod">View</label>

                <select id="graphPeriod" class="chart-period-select">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>

        </div>

        <div class="dashboard-chart">

            <div class="chart-y-axis" id="chartYAxis"></div>

            <div class="chart-grid-lines">
                <div class="chart-grid-line"></div>
                <div class="chart-grid-line"></div>
                <div class="chart-grid-line"></div>
                <div class="chart-grid-line"></div>
                <div class="chart-grid-line"></div>
            </div>

            <div class="chart-bars-area" id="chartBarsArea"></div>

            <div class="chart-empty" id="chartEmpty">
                No income or expense recorded for this period.
            </div>

        </div>

        <div class="chart-legend">

            <span class="legend-item">
                <span class="legend-dot legend-income"></span>
                Income
            </span>

            <span class="legend-item">
                <span class="legend-dot legend-expense"></span>
                Expense
            </span>

        </div>

    </div>

    <script>
        (function () {

            const graphData = {
                today: @json($todayGraph),
                week: @json($weekGraph),
                month: @json($monthGraph)
            };

            const periodSelect = document.getElementById('graphPeriod');
            const barsArea = document.getElementById('chartBarsArea');
            const yAxis = document.getElementById('chartYAxis');
            const emptyState = document.getElementById('chartEmpty');
            const title = document.getElementById('accountGraphTitle');
            const subtitle = document.getElementById('accountGraphSubtitle');

            function money(value) {
                return '₹' + Number(value || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function renderGraph(period) {

                const rows = graphData[period] || [];

                barsArea.innerHTML = '';
                yAxis.innerHTML = '';

                let maxValue = 0;

                rows.forEach(function (row) {
                    maxValue = Math.max(
                        maxValue,
                        Number(row.income || 0),
                        Number(row.expense || 0)
                    );
                });

                if (maxValue <= 0) {
                    maxValue = 1;
                }

                // Y-axis values
                for (let i = 4; i >= 0; i--) {
                    const value = maxValue * (i / 4);

                    const item = document.createElement('div');
                    item.textContent = money(value);
                    yAxis.appendChild(item);
                }

                rows.forEach(function (row) {

                    const group = document.createElement('div');
                    group.className = 'chart-group';

                    const barArea = document.createElement('div');
                    barArea.className = 'chart-bar-area';

                    const incomeBar = document.createElement('div');
                    incomeBar.className = 'chart-bar chart-income-bar';

                    const expenseBar = document.createElement('div');
                    expenseBar.className = 'chart-bar chart-expense-bar';

                    const income = Number(row.income || 0);
                    const expense = Number(row.expense || 0);

                    incomeBar.style.height = Math.max((income / maxValue) * 100, income > 0 ? 2 : 0) + '%';
                    expenseBar.style.height = Math.max((expense / maxValue) * 100, expense > 0 ? 2 : 0) + '%';

                    incomeBar.title = 'Income: ' + money(income);
                    expenseBar.title = 'Expense: ' + money(expense);

                    barArea.appendChild(incomeBar);
                    barArea.appendChild(expenseBar);

                    const label = document.createElement('div');
                    label.className = 'chart-label';
                    label.textContent = row.label;

                    group.appendChild(barArea);
                    group.appendChild(label);

                    barsArea.appendChild(group);
                });

                const hasData = rows.some(function (row) {
                    return Number(row.income || 0) > 0 || Number(row.expense || 0) > 0;
                });

                emptyState.style.display = hasData ? 'none' : 'flex';

                if (period === 'today') {
                    title.textContent = 'Today Income & Expense';
                    subtitle.textContent = 'Today\'s account movement';
                } else if (period === 'week') {
                    title.textContent = 'This Week Income & Expense';
                    subtitle.textContent = 'Daily account movement for this week';
                } else {
                    title.textContent = 'This Month Income & Expense';
                    subtitle.textContent = 'Daily account movement for this month';
                }
            }

            periodSelect.addEventListener('change', function () {
                renderGraph(this.value);
            });

            renderGraph('today');

        })();
    </script>


    {{-- =========================================================
         RECENT INCOME + EXPENSE
    ========================================================== --}}

    <div class="two-column">


        {{-- =====================================================
             RECENT INCOME
        ====================================================== --}}

        <div class="section-card">


            <div class="section-header">

                <h3>
                    Recent Income
                </h3>

                <span>
                    Latest 5
                </span>

            </div>


            <div class="table-wrapper">


                @if($recentIncome->count() > 0)


                    <table class="dashboard-table">


                        <thead>

                            <tr>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Driver
                                </th>
<th>
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            @foreach($recentIncome as $income)


                                <tr>


                                    <td>

                                        @if(
                                            !empty($income->income_date)
                                            &&
                                            $income->income_date != '0000-00-00'
                                        )

                                            {{ \Carbon\Carbon::parse($income->income_date)->format('d-m-Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    <td>
                                        {{ $income->driver_id ?? '-' }}
                                    </td>



                                    <td class="amount-income">

                                        ₹{{ number_format((float)$income->amount, 2) }}

                                    </td>


                                </tr>


                            @endforeach


                        </tbody>


                    </table>


                @else


                    <div class="empty-state">

                        No income records found.

                    </div>


                @endif


            </div>


            <div class="view-all">

                <a href="{{ route('income.index') }}">
                    View All Income →
                </a>

            </div>


        </div>



        {{-- =====================================================
             RECENT EXPENSE
        ====================================================== --}}

        <div class="section-card">


            <div class="section-header">

                <h3>
                    Recent Expense
                </h3>

                <span>
                    Latest 5
                </span>

            </div>


            <div class="table-wrapper">


                @if($recentExpense->count() > 0)


                    <table class="dashboard-table">


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


                            @foreach($recentExpense as $expense)


                                <tr>


                                    <td>

                                        @if(
                                            !empty($expense->expense_date)
                                            &&
                                            $expense->expense_date != '0000-00-00'
                                        )

                                            {{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>


                                    <td>

                                        {{ $expense->description ?? '-' }}

                                    </td>


                                    <td class="amount-expense">

                                        ₹{{ number_format((float)$expense->amount, 2) }}

                                    </td>


                                </tr>


                            @endforeach


                        </tbody>


                    </table>


                @else


                    <div class="empty-state">

                        No expense records found.

                    </div>


                @endif


            </div>


            <div class="view-all">

                <a href="{{ url('/expenses') }}">
                    View All Expense →
                </a>

            </div>


        </div>


    </div>


</div>


{{-- =========================================================
     PAYMENT BREAKDOWN MODAL
========================================================= --}}

<div id="paymentBreakdownOverlay" class="payment-breakdown-overlay" onclick="closePaymentBreakdown(event)">
    <div class="payment-breakdown-modal" onclick="event.stopPropagation()">
        <button type="button" class="payment-breakdown-close" onclick="closePaymentBreakdown()">&times;</button>
        <h3 id="paymentBreakdownTitle">Payment Breakdown</h3>
        <p id="paymentBreakdownSubtitle" class="payment-breakdown-subtitle"></p>
        <div class="payment-breakdown-grid">
            <button type="button" class="payment-breakdown-item" id="paymentCashButton" onclick="openIncomeComponent('cash')">
                <div class="label">Cash</div>
                <div id="paymentCashValue" class="value">₹0.00</div>
            </button>
            @if(strtoupper(auth()->user()->role ?? '') === 'OWNER')
            <button type="button" class="payment-breakdown-item" id="paymentAccountButton" onclick="openIncomeComponent('account')">
                <div class="label">A/C</div>
                <div id="paymentAccountValue" class="value">₹0.00</div>
            </button>
            @endif
        </div>
        <div id="incomeComponentDetails" style="display:none;margin-top:16px;padding:16px;border-top:1px solid #e5e7eb;">
            <h4 id="incomeComponentTitle" style="margin:0 0 12px;">Income Breakdown</h4>
            <div style="display:flex;justify-content:space-between;padding:7px 0;"><span>Fees</span><strong id="incomeFeesDetail">₹0.00</strong></div>
            <div style="display:flex;justify-content:space-between;padding:7px 0;"><span>Attachment</span><strong id="incomeAttachmentDetail">₹0.00</strong></div>
            <div style="display:flex;justify-content:space-between;padding:7px 0;"><span>GST</span><strong id="incomeGstDetail">₹0.00</strong></div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:1px solid #e5e7eb;margin-top:5px;"><strong>Total</strong><strong id="incomeComponentTotal">₹0.00</strong></div>
        </div>
        <div class="payment-breakdown-total">
            <span>Total</span>
            <strong id="paymentTotalValue">₹0.00</strong>
        </div>
    </div>
</div>

<script>
const incomeComponents = {
    cash: { fees: Number({{ (float)($cashFees ?? 0) }}), attachment: Number({{ (float)($cashAttachment ?? 0) }}), gst: Number({{ (float)($cashGst ?? 0) }}) },
    account: { fees: Number({{ (float)($accountFees ?? 0) }}), attachment: Number({{ (float)($accountAttachment ?? 0) }}), gst: Number({{ (float)($accountGst ?? 0) }}) }
};

const paymentBreakdown = {
    income: {
        title: 'Total Income',
        subtitle: 'Income received through Cash and A/C',
        cash: Number({{ (float)($cashIncome ?? 0) }}),
        account: Number({{ (float)($accountIncome ?? 0) }}),
        total: Number({{ (float)($totalIncome ?? 0) }})
    },
    expense: {
        title: 'Total Expense',
        subtitle: 'Expenses paid through Cash and A/C',
        cash: Number({{ (float)($cashExpense ?? 0) }}),
        account: Number({{ (float)($accountExpense ?? 0) }}),
        total: Number({{ (float)($totalExpense ?? 0) }})
    },
    balance: {
        title: 'Balance Breakdown',
        subtitle: 'Current balance by Cash and A/C',
        cash: Number({{ (float)($cashBalance ?? 0) }}),
        account: Number({{ (float)($accountBalance ?? 0) }}),
        total: Number({{ (float)($balance ?? 0) }})
    }
};

function money(value) {
    return '₹' + Number(value || 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
}

function openPaymentBreakdown(type) {
    const data = paymentBreakdown[type];
    if (!data) return;
    document.getElementById('paymentBreakdownTitle').textContent = data.title;
    document.getElementById('paymentBreakdownSubtitle').textContent = data.subtitle;
    document.getElementById('paymentCashValue').textContent = money(data.cash);
    document.getElementById('paymentAccountValue').textContent = money(data.account);
    document.getElementById('paymentTotalValue').textContent = money(data.total);
    document.getElementById('paymentBreakdownOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}


function openIncomeComponent(type) {
    const data = incomeComponents[type];
    if (!data) return;
    document.getElementById('incomeComponentTitle').textContent = type === 'cash' ? 'Cash Income Breakdown' : 'A/C Income Breakdown';
    document.getElementById('incomeFeesDetail').textContent = money(data.fees);
    document.getElementById('incomeAttachmentDetail').textContent = money(data.attachment);
    document.getElementById('incomeGstDetail').textContent = money(data.gst);
    document.getElementById('incomeComponentTotal').textContent = money(data.fees + data.attachment + data.gst);
    document.getElementById('incomeComponentDetails').style.display = 'block';
}

function closePaymentBreakdown(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('paymentBreakdownOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closePaymentBreakdown();
});
</script>

@endsection