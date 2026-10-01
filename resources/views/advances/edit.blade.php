@extends('layouts.app')

@section('content')
<style>
*{box-sizing:border-box}.page-container{max-width:1100px;margin:0 auto;padding:55px 30px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-title{margin:0;font-size:32px;color:#12345b}.page-subtitle{margin:8px 0 0;color:#60738f;font-size:16px}.back-btn{padding:11px 18px;background:#e8eef7;color:#12345b;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600}.form-card{background:#fff;border-radius:16px;padding:34px;box-shadow:0 8px 25px rgba(18,52,91,.08)}.form-group{margin-bottom:23px}label{display:block;margin-bottom:9px;font-size:15px;font-weight:700;color:#172b4d}input,textarea{width:100%;padding:14px 15px;border:1px solid #ccd7e5;border-radius:8px;font-size:15px;font-family:inherit;outline:none;background:#fff;color:#172b4d}.payment-options{display:flex;gap:10px;margin-top:8px}.payment-option{display:inline-flex;align-items:center;gap:7px;width:auto;padding:9px 14px;border:1px solid #ccd7e5;border-radius:7px;cursor:pointer;font-size:13px;font-weight:500;margin:0}.payment-option input{width:auto;padding:0;margin:0}.payment-option:has(input:checked){border-color:#12345b;background:#f1f5f9}textarea{min-height:120px;resize:vertical}.error-box{background:#fff0f0;border:1px solid #f5b5b5;color:#c62828;border-radius:8px;padding:14px 16px;margin-bottom:25px}.form-actions{display:flex;gap:12px;margin-top:28px}.save-btn{border:none;background:#22a447;color:white;padding:13px 25px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer}.cancel-btn{padding:13px 22px;border-radius:8px;background:#6c757d;color:white;text-decoration:none;font-weight:600}
</style>
<div class="page-container">
    <div class="page-header"><div><h1 class="page-title">Edit Employee Advance</h1><p class="page-subtitle">Update the employee advance record</p></div><a href="{{ route('advances.index') }}" class="back-btn">← Back to Advances</a></div>
    @if($errors->any())<div class="error-box"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="form-card">
        <form action="{{ route('advances.update', $advance->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group"><label for="employee_name">Employee Name</label><input type="text" id="employee_name" name="employee_name" value="{{ old('employee_name', $advance->employee_name) }}" required></div>
            <div class="form-group"><label for="amount">Advance Amount</label><input type="number" id="amount" name="amount" value="{{ old('amount', $advance->amount) }}" min="0.01" step="0.01" required></div>
            <div class="form-group"><label>Payment Mode</label><div class="payment-options">
                <label class="payment-option"><input type="radio" name="payment_mode" value="cash" {{ old('payment_mode', $advance->payment_mode) === 'cash' ? 'checked' : '' }} required> Cash</label>
                <label class="payment-option"><input type="radio" name="payment_mode" value="account" {{ old('payment_mode', $advance->payment_mode) === 'account' ? 'checked' : '' }}> A/C</label>
            </div></div>
            <div class="form-group" id="upi_id_group" style="display:none;"><label for="upi_id">UPI ID</label><input type="text" id="upi_id" name="upi_id" value="{{ old('upi_id', $advance->upi_id) }}" placeholder="Enter UPI ID manually"></div>
            <div class="form-group"><label for="advance_date">Date</label><input type="date" id="advance_date" name="advance_date" value="{{ old('advance_date', optional($advance->advance_date)->format('Y-m-d')) }}" required></div>
            <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" placeholder="Enter description">{{ old('description', $advance->description) }}</textarea></div>
            <div class="form-actions"><button type="submit" class="save-btn">Update Advance</button><a href="{{ route('advances.index') }}" class="cancel-btn">Cancel</a></div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){const radios=document.querySelectorAll('input[name="payment_mode"]'),group=document.getElementById('upi_id_group'),upi=document.getElementById('upi_id');function update(){const selected=document.querySelector('input[name="payment_mode"]:checked');if(selected&&selected.value==='account'){group.style.display='block';upi.required=true}else{group.style.display='none';upi.required=false}}radios.forEach(r=>r.addEventListener('change',update));update();});
</script>
@endsection
