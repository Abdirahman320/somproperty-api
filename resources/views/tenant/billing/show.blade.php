@extends('layouts.tenant')
@section('page-title','Bill Detail')
@section('content')
<div class="card-header">
  <span class="card-title">Bill — {{ $bill->billing_month->format('F Y') }}</span>
  <div class="flex gap-2">
    <a href="{{ route('tenant.billing.pdf', $bill) }}" class="btn btn-primary btn-sm"><x-icon name="file-text" /> PDF</a>
    <a href="{{ route('tenant.billing.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
  </div>
</div>

<div class="card max-w-sm">
  <div class="flex justify-between items-center mb-3">
    <div class="stat-label">Total Due</div>
    <span class="badge badge-{{ $bill->status==='paid'?'success':($bill->status==='overdue'?'danger':'warning') }}">{{ ucfirst(str_replace('_',' ',$bill->status)) }}</span>
  </div>
  <div class="stat-value sm mb-4">${{ number_format($bill->total_amount,2) }}</div>

  <div class="text-md">
    <div class="kv-row"><span class="text-muted">Base Rent</span><span>${{ number_format($bill->rent_amount,2) }}</span></div>
    @if($bill->water_amount>0)<div class="kv-row"><span class="text-muted">Water</span><span>${{ number_format($bill->water_amount,2) }}</span></div>@endif
    @if($bill->electric_amount>0)<div class="kv-row"><span class="text-muted">Electricity</span><span>${{ number_format($bill->electric_amount,2) }}</span></div>@endif
    @if($bill->parking_amount>0)<div class="kv-row"><span class="text-muted">Parking</span><span>${{ number_format($bill->parking_amount,2) }}</span></div>@endif
    @if($bill->late_fee>0)<div class="kv-row"><span class="text-danger">Late Fee</span><span>${{ number_format($bill->late_fee,2) }}</span></div>@endif
    <div class="kv-row kv-total"><span>Total</span><span>${{ number_format($bill->total_amount,2) }}</span></div>
    <div class="kv-row"><span class="text-muted">Paid</span><span>${{ number_format($bill->amount_paid,2) }}</span></div>
    <div class="kv-row fw-600"><span>Balance</span><span>${{ number_format(max(0,$bill->total_amount-$bill->amount_paid),2) }}</span></div>
  </div>

  <div class="bg-soft rounded p-3 text-sm mt-4 flex items-center gap-2">
    <x-icon name="calendar" /> Due: <b>{{ $bill->due_date->format('F j, Y') }}</b>
    @if($bill->unit)<span class="text-muted">· Unit {{ $bill->unit->unit_number }}, {{ $bill->unit->property?->name }}</span>@endif
  </div>
</div>
@endsection
