@extends('layouts.owner')
@section('page-title', 'Billing Manager')

@section('content')

{{-- Summary cards --}}
<div class="grid-2 mb-5">
  <div class="card">
    <div class="stat-label">Rent Billing — {{ now()->format('F Y') }}</div>
    <div class="stat-value sm mt-1">${{ number_format($summary['total_rent'], 2) }}</div>
    <div class="progress mt-2 mb-1">
      <div class="progress-bar is-success" style="width:{{ $summary['rent_paid_pct'] }}%"></div>
    </div>
    <div class="text-sm text-muted">
      {{ $summary['rent_paid_count'] }}/{{ $summary['total_tenants'] }} paid ·
      ${{ number_format($summary['rent_outstanding'], 2) }} outstanding
    </div>
  </div>
  <div class="card">
    <div class="stat-label">Utilities — {{ now()->format('F Y') }}</div>
    <div class="stat-value sm mt-1">${{ number_format($summary['total_utilities'], 2) }}</div>
    <div class="progress mt-2 mb-1">
      <div class="progress-bar is-info" style="width:{{ $summary['util_paid_pct'] }}%"></div>
    </div>
    <div class="text-sm text-muted">
      Water: ${{ number_format($summary['total_water'], 2) }} ·
      Electric: ${{ number_format($summary['total_electric'], 2) }}
    </div>
  </div>
</div>

{{-- Actions --}}
<div class="card-header">
  <span class="card-title">Bills — {{ now()->format('F Y') }}</span>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('owner.billing.utility.create') }}" class="btn btn-outline btn-sm"><x-icon name="zap" /> Add Utility Readings</a>
    <form method="POST" action="{{ route('owner.billing.generate') }}" class="d-inline"
          onsubmit="return confirm('Generate bills for all active tenants for {{ now()->format('F Y') }}?')">
      @csrf
      <button type="submit" class="btn btn-outline btn-sm"><x-icon name="refresh" /> Generate Bills</button>
    </form>
    <form method="POST" action="{{ route('owner.billing.notify-all') }}">
      @csrf
      <button class="btn btn-gold btn-sm"><x-icon name="megaphone" /> Send All Notifications</button>
    </form>
  </div>
</div>

{{-- Bills table --}}
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table id="billsTable">
    <thead>
      <tr>
        <th data-sort>Tenant</th>
        <th>Unit</th>
        <th data-sort>Rent</th>
        <th>Water</th>
        <th>Electric</th>
        <th data-sort>Total</th>
        <th>Due Date</th>
        <th data-sort>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($bills as $bill)
      <tr>
        <td data-label="Tenant"><b>{{ $bill->tenant?->full_name ?? '—' }}</b></td>
        <td data-label="Unit" class="text-sm">{{ $bill->unit?->unit_number ?? '—' }}, {{ $bill->unit?->property?->name ?? '—' }}</td>
        <td data-label="Rent">${{ number_format($bill->rent_amount, 2) }}</td>
        <td data-label="Water" class="text-info">${{ number_format($bill->water_amount, 2) }}</td>
        <td data-label="Electric" class="text-gold">${{ number_format($bill->electric_amount, 2) }}</td>
        <td data-label="Total"><b>${{ number_format($bill->total_amount, 2) }}</b></td>
        <td data-label="Due Date" class="text-sm text-muted">{{ $bill->due_date->format('M j, Y') }}</td>
        <td data-label="Status">
          @php $bs = $bill->status==='paid'?'success':($bill->status==='overdue'?'danger':'warning'); $bsi = $bill->status==='paid'?'check-circle':($bill->status==='overdue'?'alert':'clock'); @endphp
          <span class="badge badge-{{ $bs }}"><x-icon name="{{ $bsi }}" />{{ ucfirst($bill->status) }}</span>
        </td>
        <td data-label="Actions" class="nowrap">
          <div class="cell-actions">
            <form method="POST" action="{{ route('owner.billing.notify', $bill) }}" class="d-inline">
              @csrf
              <button class="btn btn-outline btn-icon btn-xs" aria-label="Send notification to {{ $bill->tenant?->full_name ?? 'tenant' }}"><x-icon name="mail" /></button>
            </form>
            <a href="{{ route('owner.billing.bills.show', $bill) }}" class="btn btn-outline btn-icon btn-xs" aria-label="View bill"><x-icon name="eye" /></a>
            <a href="{{ route('owner.billing.bills.pdf', $bill) }}" class="btn btn-outline btn-icon btn-xs" aria-label="Download PDF"><x-icon name="file-text" /></a>
            @if($bill->status !== 'paid')
              <a href="{{ route('owner.billing.bills.pay', $bill) }}" class="btn btn-gold btn-xs">Pay</a>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="9">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="receipt" /></div>
          <div class="empty-title">No bills for this period</div>
          <div class="empty-text">Click "Generate Bills" to create them.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
 </div>
</div>

{{ $bills->links() }}
@endsection

@push('scripts')
<script>
initSortableTable('billsTable');
filterTable('globalSearch', 'billsTable');
</script>
@endpush
