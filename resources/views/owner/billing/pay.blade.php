@extends('layouts.owner')
@section('page-title','Record Payment')
@section('content')
<div class="card-header">
  <span class="card-title">Record Payment — {{ $bill->tenant->full_name }}</span>
  <a href="{{ route('owner.billing.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Billing</a>
</div>

@php $balance = max(0, $bill->total_amount - $bill->amount_paid); @endphp

<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Bill Summary</div>
    <div class="flex flex-col gap-2 text-md">
      <div><span class="text-muted">Unit:</span> {{ $bill->unit->unit_number }}, {{ $bill->unit->property->name }}</div>
      <div><span class="text-muted">Month:</span> {{ $bill->billing_month->format('F Y') }}</div>
      <div><span class="text-muted">Total Due:</span> ${{ number_format($bill->total_amount,2) }}</div>
      <div><span class="text-muted">Already Paid:</span> ${{ number_format($bill->amount_paid,2) }}</div>
      <div class="fw-600 border-top">Outstanding Balance: ${{ number_format($balance,2) }}</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title mb-4">New Payment</div>
    <form method="POST" action="{{ route('owner.billing.bills.pay', $bill) }}">
      @csrf
      <div class="form-group"><label class="form-label">Amount *</label>
        <input name="amount" type="number" step="0.01" min="0.01" class="form-control" required
               value="{{ old('amount', number_format($balance,2,'.','')) }}"></div>
      <div class="form-group"><label class="form-label">Payment Method *</label>
        <select name="payment_method" class="form-control" required>
          @foreach(['cash'=>'Cash','bank_transfer'=>'Bank Transfer','check'=>'Check','online'=>'Online','other'=>'Other'] as $v=>$lbl)
            <option value="{{ $v }}" {{ old('payment_method')===$v?'selected':'' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Payment Date *</label>
        <input name="payment_date" type="date" class="form-control" required value="{{ old('payment_date', now()->toDateString()) }}"></div>
      <div class="form-group"><label class="form-label">Reference Number</label>
        <input name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="Transaction / check #"></div>
      <div class="form-group"><label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
      <div class="form-actions">
        <a href="{{ route('owner.billing.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-gold">Record Payment</button>
      </div>
    </form>
  </div>
</div>
@endsection
