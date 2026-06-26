@extends('layouts.admin')
@section('page-title','Advertisement & Report Billing')
@section('content')

<div class="card-header">
  <span class="card-title">Billing Register — Advertisements &amp; Reports</span>
  <button class="btn btn-primary btn-sm" onclick="Modal.open('Register Billing', document.getElementById('add-billing-tpl').innerHTML)">
    <x-icon name="plus" /> Register Billing
  </button>
</div>

<div class="stats-grid mb-5">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Total Billed</span><span class="stat-icon"><x-icon name="receipt" /></span></div><div class="stat-value">${{ number_format($stats['total_billed'], 2) }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Paid</span><span class="stat-icon"><x-icon name="check-circle" /></span></div><div class="stat-value text-success">${{ number_format($stats['paid'], 2) }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Outstanding</span><span class="stat-icon"><x-icon name="clock" /></span></div><div class="stat-value text-warning">${{ number_format($stats['unpaid'], 2) }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Records</span><span class="stat-icon"><x-icon name="layers" /></span></div><div class="stat-value">{{ $stats['count'] }}</div></div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Billing Records</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Owner / Ad</th><th>Amount</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($billings as $bill)
      <tr>
        <td data-label="Date">{{ $bill->billed_on?->format('M j, Y') ?? $bill->created_at?->format('M j, Y') }}</td>
        <td data-label="Category"><span class="badge badge-gray">{{ ucfirst($bill->category) }}</span></td>
        <td data-label="Description">
          {{ $bill->description }}
          @if($bill->reference_number)<div class="list-row-sub">Ref: {{ $bill->reference_number }}</div>@endif
        </td>
        <td data-label="Owner / Ad">
          {{ $bill->owner?->full_name ?? '—' }}
          @if($bill->advertisement)<div class="list-row-sub">{{ \Illuminate\Support\Str::limit($bill->advertisement->title, 36) }}</div>@endif
        </td>
        <td data-label="Amount">{{ $bill->currency }} {{ number_format($bill->amount, 2) }}</td>
        <td data-label="Status"><span class="badge badge-{{ $bill->statusBadge() }}">{{ ucfirst($bill->status) }}</span></td>
        <td data-label="Actions" class="cell-actions">
          @if($bill->status!=='paid')
          <form method="POST" action="{{ route('admin.ad-billing.update', $bill) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="paid">
            <button class="btn btn-outline btn-xs">Mark Paid</button>
          </form>
          @else
          <form method="POST" action="{{ route('admin.ad-billing.update', $bill) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="unpaid">
            <button class="btn btn-ghost btn-xs">Mark Unpaid</button>
          </form>
          @endif
          <form method="POST" action="{{ route('admin.ad-billing.destroy', $bill) }}" class="d-inline"
                onsubmit="return confirm('Delete this billing record?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-xs">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="7">
        <div class="empty-state"><div class="empty-icon"><x-icon name="receipt" /></div>
          <div class="empty-title">No billing records</div>
          <div class="empty-text">Register a charge for an advertisement or report to get started.</div></div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<div class="mt-4">{{ $billings->links() }}</div>

{{-- Register billing modal template --}}
<div id="add-billing-tpl" class="d-none">
  <form method="POST" action="{{ route('admin.ad-billing.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category *</label>
        <select name="category" class="form-control" required>
          <option value="advertisement">Advertisement</option>
          <option value="report">Report</option>
          <option value="feature">Featured listing</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Status *</label>
        <select name="status" class="form-control" required>
          <option value="unpaid">Unpaid</option>
          <option value="paid">Paid</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Description *</label>
      <input name="description" class="form-control" required placeholder="e.g. Featured listing — 30 days"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Owner</label>
        <select name="owner_id" class="form-control">
          <option value="">— None —</option>
          @foreach($owners as $o)<option value="{{ $o->id }}">{{ $o->full_name }}</option>@endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Advertisement</label>
        <select name="advertisement_id" class="form-control">
          <option value="">— None —</option>
          @foreach($ads as $a)<option value="{{ $a->id }}">{{ \Illuminate\Support\Str::limit($a->title, 40) }}</option>@endforeach
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Amount *</label>
        <input name="amount" type="number" step="0.01" min="0" class="form-control" required placeholder="0.00"></div>
      <div class="form-group"><label class="form-label">Currency</label>
        <input name="currency" class="form-control" value="USD" maxlength="8"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Billed On</label>
        <input name="billed_on" type="date" class="form-control" value="{{ date('Y-m-d') }}"></div>
      <div class="form-group"><label class="form-label">Reference No.</label>
        <input name="reference_number" class="form-control"></div>
    </div>
    <div class="form-group"><label class="form-label">Notes</label>
      <textarea name="notes" class="form-control" rows="2"></textarea></div>
    <div class="form-actions">
      <button class="btn btn-primary"><x-icon name="receipt" /> Register Billing</button>
    </div>
  </form>
</div>
@endsection
