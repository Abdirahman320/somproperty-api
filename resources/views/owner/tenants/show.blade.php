@extends('layouts.owner')
@section('page-title','Tenant Details')
@section('content')
<div class="card-header">
  <span class="card-title">{{ $tenant->full_name }}</span>
  <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Tenants</a>
</div>

<div class="grid-2 mb-4">
  <div class="card">
    <div class="card-title mb-4">Profile</div>
    <div class="flex flex-col gap-2 text-md">
      <div><span class="text-muted">Email:</span> {{ $tenant->email }}</div>
      <div><span class="text-muted">Phone:</span> {{ $tenant->phone ?? '—' }}</div>
      <div><span class="text-muted">National ID:</span> {{ $tenant->national_id ?? '—' }}</div>
      <div><span class="text-muted">Status:</span>
        <span class="badge badge-{{ $tenant->status==='active'?'success':'danger' }}"><x-icon name="{{ $tenant->status==='active'?'check-circle':'alert' }}" />{{ ucfirst($tenant->status) }}</span>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Active Contract</div>
    @if($tenant->activeContract)
      <div class="flex flex-col gap-2 text-md">
        <div><span class="text-muted">Unit:</span>
          {{ $tenant->activeContract->unit?->unit_number ?? '—' }},
          {{ $tenant->activeContract->unit?->property?->name ?? '—' }}</div>
        <div><span class="text-muted">Monthly Rent:</span> ${{ number_format($tenant->activeContract->monthly_rent, 2) }}</div>
        <div><span class="text-muted">Term:</span>
          {{ $tenant->activeContract->start_date?->format('M j, Y') }} &ndash;
          {{ $tenant->activeContract->end_date?->format('M j, Y') }}</div>
        <div class="mt-1">
          <form method="POST" action="{{ route('owner.contracts.terminate', $tenant->activeContract) }}"
                onsubmit="return confirm('Terminate this contract? The unit will be marked vacant.')">
            @csrf @method('PUT')
            <button class="btn btn-danger btn-xs">Terminate Contract</button>
          </form>
        </div>
      </div>
    @else
      <p class="text-muted text-md">No active contract.</p>
    @endif
  </div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Recent Bills</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Month</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
    <tbody>
      @forelse($tenant->bills as $b)
      <tr>
        <td data-label="Month">{{ $b->billing_month?->format('M Y') ?? '—' }}</td>
        <td data-label="Total">${{ number_format($b->total_amount, 2) }}</td>
        <td data-label="Paid">${{ number_format($b->amount_paid, 2) }}</td>
        <td data-label="Status">@php $bs = $b->status==='paid'?'success':($b->status==='overdue'?'danger':'warning'); $bsi = $b->status==='paid'?'check-circle':($b->status==='overdue'?'alert':'clock'); @endphp
          <span class="badge badge-{{ $bs }}"><x-icon name="{{ $bsi }}" />{{ ucfirst($b->status) }}</span></td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="4">
        <div class="empty-state"><div class="empty-icon"><x-icon name="receipt" /></div><div class="empty-title">No bills recorded</div></div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
@endsection
