@extends('layouts.app')

@section('content')

<style>
    .income-page {
        max-width: 980px;
        margin: 0 auto;
    }

    .page-header {
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

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 34px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 26px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 9px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .form-control {
        width: 100%;
        height: 48px;
        padding: 0 15px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #64748b;
        box-shadow: 0 0 0 3px rgba(100,116,139,.12);
    }

    textarea.form-control {
        height: 112px;
        padding-top: 13px;
        resize: vertical;
    }

    /* Remove number input spinner arrows */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .inline-payment {
        margin-top: 14px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #dbe3ed;
        border-radius: 10px;
    }

    .payment-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 12px;
    }

    .payment-options {
        display: flex;
        gap: 25px;
        align-items: center;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        font-size: 14px;
        cursor: pointer;
    }

    .payment-option input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .upi-box {
        display: none;
        margin-top: 16px;
    }

    .upi-box.show {
        display: block;
    }

    .upi-box label {
        margin-bottom: 7px;
    }

    .total-box {
        grid-column: 1 / -1;
        background: #111827;
        color: #fff;
        border-radius: 10px;
        padding: 18px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-label {
        font-size: 15px;
        font-weight: 700;
    }

    .total-value {
        font-size: 25px;
        font-weight: 800;
    }

    .form-actions {
        grid-column: 1 / -1;
        border-top: 1px solid #e5e7eb;
        padding-top: 25px;
        display: flex;
        gap: 12px;
    }

    .save-btn {
        background: #111827;
        color: #fff;
        border: none;
        padding: 13px 25px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
    }

    .save-btn:hover {
        background: #1f2937;
    }

    .cancel-btn {
        background: #f1f5f9;
        color: #334155;
        text-decoration: none;
        padding: 13px 25px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 800;
    }

    .old-errors {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 9px;
        padding: 14px 18px;
        margin-bottom: 22px;
    }

    .old-errors ul {
        margin: 0;
        padding-left: 20px;
    }

    @media (max-width: 700px) {

        .form-grid,
        .form-group.full,
        .total-box,
        .form-actions {
            grid-column: auto;
        }

        .form-card {
            padding: 22px;
        }
    }
</style>


<div class="income-page">

    {{-- PAGE HEADER --}}

    <div class="page-header">

        <h2>Add Income</h2>

        <p>
            Enter the income details below.
        </p>

    </div>


    {{-- VALIDATION ERRORS --}}

    @if($errors->any())

        <div class="old-errors">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- INCOME FORM --}}

    <form
        method="POST"
        action="{{ route('income.store') }}"
    >

        @csrf


        <div class="form-card">

            <div class="form-grid">


                {{-- DRIVER ID --}}

                <div class="form-group">

                    <label for="driver_id">
                        Driver ID
                    </label>

                    <input
                        type="text"
                        name="driver_id"
                        id="driver_id"
                        class="form-control"
                        value="{{ old('driver_id') }}"
                        placeholder="Enter Driver ID"
                        required
                    >

                </div>


                {{-- DATE --}}

                <div class="form-group">

                    <label for="income_date">
                        Date
                    </label>

                    <input
                        type="date"
                        name="income_date"
                        id="income_date"
                        class="form-control"
                        value="{{ old('income_date', now()->format('Y-m-d')) }}"
                        required
                    >

                </div>


                {{-- FEES AMOUNT --}}

                <div class="form-group">
                    <label for="fees_amount">Fees Amount</label>
                    <input
                        type="number"
                        name="fees_amount"
                        id="fees_amount"
                        class="form-control"
                        value="{{ old('fees_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="Enter Fees Amount"
                        required
                    >
                </div>

                {{-- ATTACHMENT AMOUNT --}}

                <div class="form-group">
                    <label for="attachment_amount">Attachment Amount</label>
                    <input
                        type="number"
                        name="attachment_amount"
                        id="attachment_amount"
                        class="form-control"
                        value="{{ old('attachment_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="Enter Attachment Amount"
                        required
                    >
                </div>

                {{-- GST AMOUNT --}}

                <div class="form-group">
                    <label for="gst_amount">GST Amount</label>
                    <input
                        type="number"
                        name="gst_amount"
                        id="gst_amount"
                        class="form-control"
                        value="{{ old('gst_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="Enter GST Amount"
                        required
                    >
                </div>

                {{-- COMMON PAYMENT MODE --}}

                <div class="form-group">
                    <div class="inline-payment">
                        <div class="payment-section-title">Payment Mode</div>

                        <div class="payment-options">
                            <label class="payment-option">
                                <input
                                    type="radio"
                                    name="payment_mode"
                                    value="cash"
                                    {{ old('payment_mode', 'cash') === 'cash' ? 'checked' : '' }}
                                    required
                                >
                                Cash
                            </label>

                            <label class="payment-option">
                                <input
                                    type="radio"
                                    name="payment_mode"
                                    value="account"
                                    {{ old('payment_mode') === 'account' ? 'checked' : '' }}
                                >
                                A/C
                            </label>
                        </div>

                        <div id="commonUpiBox" class="upi-box {{ old('payment_mode') === 'account' ? 'show' : '' }}">
                            <label for="common_upi_id">UPI ID</label>
                            <input
                                type="text"
                                name="common_upi_id"
                                id="common_upi_id"
                                class="form-control"
                                value="{{ old('common_upi_id') }}"
                                placeholder="Enter UPI ID"
                            >
                        </div>
                    </div>
                </div>

                {{-- TOTAL AMOUNT --}}

                <div class="total-box">

                    <span class="total-label">
                        Total Amount
                    </span>

                    <span
                        class="total-value"
                        id="totalAmount"
                    >
                        ₹0.00
                    </span>

                </div>


                {{-- ACTIONS --}}

                <div class="form-actions">

                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Save Income
                    </button>


                    <a
                        href="{{ route('income.index') }}"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>

                </div>

            </div>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {


    const feesAmount =
        document.getElementById('fees_amount');

    const attachmentAmount =
        document.getElementById('attachment_amount');

    const gstAmount =
        document.getElementById('gst_amount');

    const totalAmount =
        document.getElementById('totalAmount');


    const commonUpiBox =
        document.getElementById('commonUpiBox');

    const commonUpiInput =
        document.getElementById('common_upi_id');


    /*
    |--------------------------------------------------------------------------
    | TOTAL CALCULATION
    |--------------------------------------------------------------------------
    */

    function updateTotal() {

        const fees =
            parseFloat(feesAmount.value) || 0;

        const attachment =
            parseFloat(attachmentAmount.value) || 0;

        const gst =
            parseFloat(gstAmount.value) || 0;

        const total =
            fees + attachment + gst;

        totalAmount.textContent =
            '₹' + total.toFixed(2);
    }


    /*
    |--------------------------------------------------------------------------
    | COMMON PAYMENT MODE
    |--------------------------------------------------------------------------
    */

    function updateCommonPayment() {

        const selected =
            document.querySelector(
                'input[name="payment_mode"]:checked'
            );

        const isAccount =
            selected && selected.value === 'account';

        if (isAccount) {
            commonUpiBox.classList.add('show');
            commonUpiInput.required = true;
        } else {
            commonUpiBox.classList.remove('show');
            commonUpiInput.required = false;
            commonUpiInput.value = '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    feesAmount.addEventListener(
        'input',
        updateTotal
    );


    attachmentAmount.addEventListener('input', updateTotal);

    gstAmount.addEventListener(
        'input',
        updateTotal
    );


    document
        .querySelectorAll('input[name="payment_mode"]')
        .forEach(function (input) {
            input.addEventListener(
                'change',
                updateCommonPayment
            );
        });


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateTotal();

    updateCommonPayment();

});

</script>

@endsection