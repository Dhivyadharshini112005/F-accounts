@extends('layouts.app')

@section('content')
<style>
    .edit-income { max-width: 900px; margin: 0 auto; }
    .edit-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:30px; box-shadow:0 2px 10px rgba(0,0,0,.05); }
    .field { margin-bottom:20px; }
    .field label { display:block; margin-bottom:8px; font-weight:600; color:#111827; font-size:14px; }
    .field input, .field textarea { width:100%; box-sizing:border-box; padding:12px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; }
    .field textarea { min-height:100px; resize:vertical; }
    .payment-options { display:flex; gap:10px; margin-top:8px; }
    .payment-option { display:inline-flex; align-items:center; gap:7px; padding:9px 14px; border:1px solid #d1d5db; border-radius:7px; cursor:pointer; font-size:13px; }
    .payment-option input { width:auto !important; }
    .payment-option:has(input:checked) { border-color:#111827; background:#f3f4f6; }
    .readonly { background:#f3f4f6; font-weight:600; }
    .actions { display:flex; gap:10px; margin-top:25px; }
    .btn { padding:12px 20px; border-radius:8px; border:0; text-decoration:none; font-weight:600; cursor:pointer; }
    .btn-primary { background:#111827; color:#fff; } .btn-secondary { background:#e5e7eb; color:#111827; }
    .error { color:#dc2626; font-size:13px; margin-top:5px; }
</style>

<div class="edit-income">
    <h2>Edit Income</h2>
    <p>Update the income details below.</p>

    <div class="edit-card">
        <form action="{{ route('income.update', $income->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="driver_id">Driver ID</label>
                <input type="text" name="driver_id" id="driver_id" value="{{ old('driver_id', $income->driver_id) }}" required>
                @error('driver_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="fees_amount">Fees Amount</label>
                <input type="number" name="fees_amount" id="fees_amount" step="0.01" min="0" value="{{ old('fees_amount', $income->fees_amount) }}" required>
                @error('fees_amount')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="attachment_amount">Attachment Amount</label>
                <input type="number" name="attachment_amount" id="attachment_amount" step="0.01" min="0" value="{{ old('attachment_amount', $income->attachment_amount ?? 0) }}" required>
                @error('attachment_amount')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="gst_amount">GST Amount</label>
                <input type="number" name="gst_amount" id="gst_amount" step="0.01" min="0" value="{{ old('gst_amount', $income->gst_amount) }}" required>
                @error('gst_amount')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label>Payment Mode</label>
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_mode" value="cash" {{ old('payment_mode', $income->fees_payment_mode ?: 'cash') === 'cash' ? 'checked' : '' }} required> Cash
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_mode" value="account" {{ old('payment_mode', $income->fees_payment_mode) === 'account' ? 'checked' : '' }}> A/C
                    </label>
                </div>
                @error('payment_mode')<div class="error">{{ $message }}</div>@enderror

                <input type="text" name="common_upi_id" value="{{ old('common_upi_id', $income->fees_upi_id ?? $income->attachment_upi_id ?? $income->gst_upi_id ?? '') }}" placeholder="UPI ID" style="margin-top:8px; width:100%; box-sizing:border-box; padding:12px 14px; border:1px solid #d1d5db; border-radius:8px;">
            </div>

            <div class="field">
                <label for="total_amount">Total Amount</label>
                <input type="text" id="total_amount" class="readonly" value="0.00" readonly>
            </div>

            <div class="field">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', optional($income->income_date)->format('Y-m-d') ?? $income->income_date) }}" required>
                @error('date')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea name="description" id="description">{{ old('description', $income->description) }}</textarea>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Update Income</button>
                <a href="{{ route('income.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fees = document.getElementById('fees_amount');
    const attachment = document.getElementById('attachment_amount');
    const gst = document.getElementById('gst_amount');
    const total = document.getElementById('total_amount');
    function calculate() {
        total.value = ((parseFloat(fees.value) || 0) + (parseFloat(attachment.value) || 0) + (parseFloat(gst.value) || 0)).toFixed(2);
    }
    fees.addEventListener('input', calculate);
    attachment.addEventListener('input', calculate);
    gst.addEventListener('input', calculate);
    calculate();
});
</script>
@endsection
