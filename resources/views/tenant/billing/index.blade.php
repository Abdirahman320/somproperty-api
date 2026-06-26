@extends('layouts.tenant')
@section('page-title','My Billings')
@section('content')
@if($currentBill)
<div class="grid-2 mb-5">
  <div class="card">
    <div class="stat-label">Current Bill — {{ $currentBill->billing_month->format('F Y') }}</div>
    <div class="stat-value sm mt-2 mb-3">${{ number_format($currentBill->total_amount, 2) }}</div>
    <div class="flex flex-col gap-2 mb-4">
      <div class="kv-row"><span class="text-muted">Base Rent</span><b>${{ number_format($currentBill->rent_amount,2) }}</b></div>
      @if($currentBill->water_amount>0)<div class="kv-row"><span class="text-muted">Water</span><b class="text-info">${{ number_format($currentBill->water_amount,2) }}</b></div>@endif
      @if($currentBill->electric_amount>0)<div class="kv-row"><span class="text-muted">Electric</span><b class="text-gold">${{ number_format($currentBill->electric_amount,2) }}</b></div>@endif
    </div>
    <div class="bg-soft rounded p-3 text-sm mb-3 flex items-center gap-2">
      <x-icon name="calendar" /> Due: <b>{{ $currentBill->due_date->format('F j, Y') }}</b>
      <span class="badge badge-{{ $currentBill->status==='paid'?'success':($currentBill->status==='overdue'?'danger':'warning') }}">{{ ucfirst($currentBill->status) }}</span>
    </div>
    <a href="{{ route('tenant.billing.pdf',$currentBill) }}" class="btn btn-primary btn-block"><x-icon name="download" /> Download PDF Receipt</a>
  </div>
  <div class="card">
    <div class="stat-label mb-3">Payment History</div>
    @forelse($pastBills as $b)
    <div class="flex justify-between items-center kv-row">
      <span>{{ $b->billing_month->format('M Y') }}</span>
      <b>${{ number_format($b->total_amount,2) }}</b>
      <span class="badge badge-{{ $b->status==='paid'?'success':($b->status==='overdue'?'danger':'warning') }}">{{ ucfirst($b->status) }}</span>
      <a href="{{ route('tenant.billing.pdf',$b) }}" class="btn btn-outline btn-xs" aria-label="Download receipt"><x-icon name="receipt" /></a>
    </div>
    @empty
    <p class="text-muted text-md">No past bills yet.</p>
    @endforelse
  </div>
</div>
@else
<div class="card text-center p-6 text-muted">
  <div class="empty-state">
    <span class="empty-icon"><x-icon name="receipt" /></span>
    <span class="empty-title">No bills yet</span>
    <span class="empty-text">Your bills will appear here once your property manager generates them.</span>
  </div>
</div>
@endif
@endsection
