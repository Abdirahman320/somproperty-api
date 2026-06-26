@extends('layouts.owner')
@section('page-title','Bill Detail')
@section('content')
<div class="card-header">
  <span class="card-title">Bill — {{ $bill->tenant?->full_name ?? '—' }} ({{ $bill->billing_month->format('F Y') }})</span>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('owner.billing.bills.pdf', $bill) }}" class="btn btn-outline btn-sm"><x-icon name="file-text" /> PDF</a>
    @if($bill->status !== 'paid')
      <a href="{{ route('owner.billing.bills.pay', $bill) }}" class="btn btn-gold btn-sm">Record Payment</a>
    @endif
    <a href="{{ route('owner.billing.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
  </div>
</div>

<div class="grid-2 mb-4">
  <div class="card">
    <div class="card-title mb-4">Charges</div>
    <div class="kv-row"><span class="text-muted">Rent</span><span>${{ number_format($bill->rent_amount,2) }}</span></div>
    <div class="kv-row"><span class="text-muted">Water ({{ rtrim(rtrim(number_format($bill->water_consumption,3),'0'),'.') }} units)</span><span>${{ number_format($bill->water_amount,2) }}</span></div>
    <div class="kv-row"><span class="text-muted">Electricity ({{ rtrim(rtrim(number_format($bill->electric_consumption,3),'0'),'.') }} units)</span><span>${{ number_format($bill->electric_amount,2) }}</span></div>
    <div class="kv-row"><span class="text-muted">Parking</span><span>${{ number_format($bill->parking_amount,2) }}</span></div>
    @if($bill->late_fee > 0)<div class="kv-row"><span class="text-danger">Late Fee</span><span>${{ number_format($bill->late_fee,2) }}</span></div>@endif
    @if($bill->discount_amount > 0)<div class="kv-row"><span class="text-success">Discount</span><span>−${{ number_format($bill->discount_amount,2) }}</span></div>@endif
    <div class="kv-row kv-total"><span>Total</span><span>${{ number_format($bill->total_amount,2) }}</span></div>
    <div class="kv-row"><span class="text-muted">Paid</span><span>${{ number_format($bill->amount_paid,2) }}</span></div>
    <div class="kv-row fw-600"><span>Balance</span><span>${{ number_format(max(0,$bill->total_amount-$bill->amount_paid),2) }}</span></div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Details</div>
    <div class="flex flex-col gap-2 text-md">
      <div><span class="text-muted">Tenant:</span> {{ $bill->tenant?->full_name ?? '—' }} ({{ $bill->tenant?->email ?? '—' }})</div>
      <div><span class="text-muted">Unit:</span> {{ $bill->unit?->unit_number ?? '—' }}, {{ $bill->unit?->property?->name ?? '—' }}</div>
      <div><span class="text-muted">Due Date:</span> {{ $bill->due_date->format('M j, Y') }}</div>
      <div><span class="text-muted">Status:</span>
        @php $bs = $bill->status==='paid'?'success':($bill->status==='overdue'?'danger':'warning'); $bsi = $bill->status==='paid'?'check-circle':($bill->status==='overdue'?'alert':'clock'); @endphp
        <span class="badge badge-{{ $bs }}"><x-icon name="{{ $bsi }}" />{{ ucfirst(str_replace('_',' ',$bill->status)) }}</span>
      </div>
    </div>
  </div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Payment History</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
    <tbody>
      @forelse($bill->payments as $p)
      <tr>
        <td data-label="Date">{{ \Illuminate\Support\Carbon::parse($p->payment_date)->format('M j, Y') }}</td>
        <td data-label="Amount">${{ number_format($p->amount,2) }}</td>
        <td data-label="Method">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
        <td data-label="Reference" class="text-muted">{{ $p->reference_number ?? '—' }}</td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="4">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="receipt" /></div>
          <div class="empty-title">No payments recorded yet</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
@endsection
