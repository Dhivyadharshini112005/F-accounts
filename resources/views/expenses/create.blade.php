@extends('layouts.app')
 
@section('content') 
<style> 
*{box-sizing:border-box} 
.page-container{max-width:1100px;margin:0 auto;padding:55px 30px} 
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px} 
.page-title{margin:0;font-size:32px;color:#12345b} 
.page-subtitle{margin:8px 0 0;color:#60738f;font-size:16px} 
.back-btn{padding:11px 18px;background:#e8eef7;color:#12345b;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600} 
.form-card{background:#fff;border-radius:16px;padding:34px;box-shadow:0 8px 25px rgba(18,52,91,.08)} 
.form-group{margin-bottom:23px} 
label{display:block;margin-bottom:9px;font-size:15px;font-weight:700;color:#172b4d} 
input,textarea,select{width:100%;padding:14px 15px;border:1px solid #ccd7e5;border-radius:8px;font-size:15px;font-family:inherit;outline:none;background:#fff;color:#172b4d} 
input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0} 
input[type=number]{-moz-appearance:textfield} 
.payment-options{display:flex;gap:10px;margin-top:8px} 
.payment-option{display:inline-flex;align-items:center;gap:7px;width:auto;padding:9px 14px;border:1px solid #ccd7e5;border-radius:7px;cursor:pointer;font-size:13px;font-weight:500;margin:0} 
.payment-option input{width:auto;padding:0;margin:0} 
.payment-option:has(input:checked){border-color:#12345b;background:#f1f5f9} 
textarea{min-height:120px;resize:vertical} 
.error-box{background:#fff0f0;border:1px solid #f5b5b5;color:#c62828;border-radius:8px;padding:14px 16px;margin-bottom:25px} 
.form-actions{display:flex;gap:12px;margin-top:28px} 
.save-btn{border:none;background:#22a447;color:white;padding:13px 25px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer} 
.cancel-btn{padding:13px 22px;border-radius:8px;background:#6c757d;color:white;text-decoration:none;font-weight:600} 
@media(max-width:700px){.page-container{padding:30px 15px}.page-header{flex-direction:column;align-items:flex-start;gap:18px}.form-card{padding:22px}} 
</style> 
 
<div class="page-container"> 
    <div class="page-header"> 
        <div> 
            <h1 class="page-title">Add Expense</h1> 
            <p class="page-subtitle">Record an expense for the account</p> 
        </div> 
        <a href="{{ route('expenses.index') }}" class="back-btn">← Back to Expenses</a> 
    </div> 
 
    @if($errors->any()) 
        <div class="error-box"> 
            <ul> 
                @foreach($errors->all() as $error) 
                    <li>{{ $error }}</li> 
                @endforeach 
            </ul> 
        </div> 
    @endif 
 
    <div class="form-card"> 
        <form action="{{ route('expenses.store') }}" method="POST"> 
            @csrf 
 
            <div class="form-group"> 
                <label for="expense_name">Expense Name</label> 
                <input type="text" id="expense_name" name="expense_name" 
                       value="{{ old('expense_name') }}" 
                       placeholder="Enter expense name" required> 
            </div> 
 
            <div class="form-group"> 
                <label for="category">Category</label> 
                <select id="category" name="category" required> 
                    <option value="">Select Category</option> 
                    @foreach(['Provision','Advertisement','Stationary','Transport','Maintenance','Electricity','Rent','Other'] as $category) 
                        <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}> 
                            {{ $category }} 
                        </option> 
                    @endforeach 
                </select> 
            </div> 
 
            <div class="form-group"> 
                <label for="amount">Amount</label> 
                <input type="number" id="amount" name="amount" 
                       value="{{ old('amount') }}" 
                       placeholder="Enter amount" min="0" step="0.01" required> 
 
                <label style="margin-top:12px;margin-bottom:0;font-size:13px;">Payment Mode</label> 
                <div class="payment-options"> 
                    <label class="payment-option"> 
                        <input type="radio" name="payment_mode" value="cash" 
                            {{ old('payment_mode','cash') === 'cash' ? 'checked' : '' }} required> 
                        Cash 
                    </label> 
                    <label class="payment-option"> 
                        <input type="radio" name="payment_mode" value="account" 
                            {{ old('payment_mode') === 'account' ? 'checked' : '' }}> 
                        A/C 
                    </label> 
                </div> 

                <div id="upi_id_group" style="display:none;margin-top:12px;">
                    <label for="upi_id">UPI ID</label>
                    <input type="text" id="upi_id" name="upi_id"
                           value="{{ old('upi_id') }}"
                           placeholder="Enter UPI ID manually"
                           autocomplete="off">
                </div>
            </div> 
 
            <div class="form-group"> 
                <label for="expense_date">Date</label> 
                <input type="date" id="expense_date" name="expense_date" 
                       value="{{ old('expense_date', date('Y-m-d')) }}" required> 
            </div> 
 
            <div class="form-group"> 
                <label for="description">Description</label> 
                <textarea id="description" name="description" 
                          placeholder="Enter description">{{ old('description') }}</textarea> 
            </div> 
 
            <div class="form-actions"> 
                <button type="submit" class="save-btn">Save Expense</button> 
                <a href="{{ route('expenses.index') }}" class="cancel-btn">Cancel</a> 
            </div> 
        </form> 
    </div> 
</div>

<div id="balanceWarningModal" style="display:none;position:fixed;z-index:99999;inset:0;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="width:380px;max-width:90%;background:#fff;border-radius:14px;padding:28px;text-align:center;box-shadow:0 15px 45px rgba(0,0,0,.20);">
        <div style="font-size:42px;margin-bottom:10px;">⚠️</div>
        <h3 style="margin:0 0 10px;color:#c62828;font-size:21px;">No Money Available</h3>
        <p id="balanceWarningMessage" style="margin:0 0 22px;color:#60738f;font-size:15px;line-height:1.5;"></p>
        <button type="button" id="balanceWarningOk" style="border:none;background:#c62828;color:#fff;padding:10px 30px;border-radius:7px;font-size:14px;font-weight:600;cursor:pointer;">OK</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[name="payment_mode"]');
    const upiGroup = document.getElementById('upi_id_group');
    const upiId = document.getElementById('upi_id');
    const modal = document.getElementById('balanceWarningModal');
    const message = document.getElementById('balanceWarningMessage');
    const ok = document.getElementById('balanceWarningOk');
    const amount = document.getElementById('amount');
    const form = document.querySelector('form');

    const balances = {
        cash: Number({{ (float)($balances['cash'] ?? 0) }}),
        account: Number({{ (float)($balances['account'] ?? 0) }})
    };

    function updateUpi(showWarning) {
        const selected = document.querySelector('input[name="payment_mode"]:checked');
        const mode = selected ? selected.value : null;

        if (mode === 'account') {
            upiGroup.style.display = 'block';
            upiId.required = true;
        } else {
            upiGroup.style.display = 'none';
            upiId.required = false;
        }

        if (showWarning && mode && balances[mode] <= 0) {
            message.textContent = 'There is no available balance in ' + (mode === 'cash' ? 'Cash' : 'A/C') + '.';
            modal.style.display = 'flex';
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateUpi(true);
            if (this.value === 'account' && this.checked) {
                setTimeout(function () { upiId.focus(); }, 50);
            }
        });
    });

    amount.addEventListener('input', function () {
        const selected = document.querySelector('input[name="payment_mode"]:checked');
        if (!selected) return;
        const value = Number(this.value || 0);
        const available = balances[selected.value];
        if (value > 0 && available <= 0) {
            message.textContent = 'There is no available balance in ' + (selected.value === 'cash' ? 'Cash' : 'A/C') + '.';
        }
    });

    ok.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) modal.style.display = 'none';
    });

    updateUpi(false);
});
</script>
@endsection
