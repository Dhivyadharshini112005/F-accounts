@extends('layouts.app')

@section('content')

<style>

.advance-form-page {
    max-width: 800px;
    margin: 0 auto;
}

.form-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}

.form-card h2 {
    margin: 0 0 6px;
    color: #111827;
}

.form-card p {
    margin: 0 0 25px;
    color: #64748b;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    font-weight: 700;
    color: #111827;
    font-size: 14px;
}

.form-control {
    width: 100%;
    height: 44px;
    padding: 0 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
}

.form-control[readonly] {
    background: #f8fafc;
}

textarea.form-control {
    height: 90px;
    padding-top: 10px;
    resize: vertical;
}

.payment-options {
    display: flex;
    gap: 25px;
}

.payment-options label {
    font-weight: 500;
}

.buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.save-btn,
.cancel-btn {
    padding: 11px 20px;
    border-radius: 8px;
    border: none;
    text-decoration: none;
    font-weight: 700;
    cursor: pointer;
}

.save-btn {
    background: #111827;
    color: #ffffff;
}

.cancel-btn {
    background: #e5e7eb;
    color: #111827;
}

.error-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

</style>


<div class="advance-form-page">

    <div class="form-card">

        <h2>
            Add Salary Advance
        </h2>

        <p>
            Enter employee salary advance payment details.
        </p>


        @if($errors->any())

            <div class="error-box">

                <ul style="margin:0;padding-left:20px;">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('expenses.advances.store') }}"
        >

            @csrf


            {{-- EMPLOYEE --}}

            <div class="form-group">

                <label>
                    Employee Name
                </label>

                <input
                    type="text"
                    name="employee_name"
                    class="form-control"
                    value="{{ old('employee_name') }}"
                    required
                >

            </div>


            {{-- CATEGORY --}}

            <div class="form-group">

                <label>
                    Category
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="Salary Advance"
                    readonly
                >

                <input
                    type="hidden"
                    name="category"
                    value="Salary Advance"
                >

            </div>


            {{-- AMOUNT --}}

            <div class="form-group">

                <label>
                    Advance Amount
                </label>

                <input
                    type="number"
                    name="amount"
                    class="form-control"
                    value="{{ old('amount') }}"
                    min="0.01"
                    step="0.01"
                    required
                >

            </div>


            {{-- PAYMENT MODE --}}

            <div class="form-group">

                <label>
                    Payment Mode
                </label>


                <div class="payment-options">

                    <label>

                        <input
                            type="radio"
                            name="payment_mode"
                            value="cash"
                            {{ old('payment_mode') === 'cash' ? 'checked' : '' }}
                            required
                        >

                        Cash

                    </label>


                    <label>

                        <input
                            type="radio"
                            name="payment_mode"
                            value="account"
                            {{ old('payment_mode') === 'account' ? 'checked' : '' }}
                        >

                        A/C

                    </label>

                </div>

            </div>


            {{-- UPI --}}

            <div class="form-group">

                <label>
                    UPI ID
                </label>

                <input
                    type="text"
                    name="upi_id"
                    class="form-control"
                    value="{{ old('upi_id') }}"
                    placeholder="Enter UPI ID if applicable"
                >

            </div>


            {{-- DATE --}}

            <div class="form-group">

                <label>
                    Advance Date
                </label>

                <input
                    type="date"
                    name="advance_date"
                    class="form-control"
                    value="{{ old(
                        'advance_date',
                        date('Y-m-d')
                    ) }}"
                    required
                >

            </div>


            {{-- DESCRIPTION --}}

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    placeholder="Enter description"
                >{{ old('description') }}</textarea>

            </div>


            <div class="buttons">

                <button
                    type="submit"
                    class="save-btn"
                >
                    Save Salary Advance
                </button>


                <a
                    href="{{ route('expenses.advances.index') }}"
                    class="cancel-btn"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection