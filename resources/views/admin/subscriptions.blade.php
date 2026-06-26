@extends('layouts.admin')
@section('page-title','Subscriptions')
@section('content')
<div class="stats-grid">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Monthly MRR</span><span class="stat-icon is-teal"><x-icon name="wallet" /></span></div><div class="stat-value">${{ number_format($mrr,0) }}</div><div class="stat-delta">across {{ $owners->total() }} owners</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Annual ARR</span><span class="stat-icon is-gold"><x-icon name="trending-up" /></span></div><div class="stat-value">${{ number_format($mrr*12,0) }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Active Owners</span><span class="stat-icon"><x-icon name="briefcase" /></span></div><div class="stat-value">{{ $owners->total() }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Plans</span><span class="stat-icon"><x-icon name="package" /></span></div><div class="stat-value">{{ $plans->count() }}</div></div>
</div>
<div class="grid-5 mb-5">
  @foreach($plans as $p)
  <div class="card text-center">
    <div class="text-md fw-600 text-muted mb-1">{{ $p->name }}</div>
    <div class="text-xl fw-600">${{ number_format($p->price_monthly,0) }}<span class="text-sm fw-400 text-muted">/mo</span></div>
    <div class="text-xs text-muted mb-2">{{ $p->max_apartments }} max apts</div>
    <div class="text-lg fw-600">{{ $p->active }}</div>
    <div class="text-xs text-muted">active owners</div>
    <div class="text-md fw-600 text-gold mt-1">${{ number_format($p->active * $p->price_monthly,0) }}/mo</div>
  </div>
  @endforeach
</div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table>
    <thead><tr><th>Owner</th><th>Plan</th><th>Subscription Value</th><th>Joined</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($owners as $o)
      <tr>
        <td data-label="Owner"><b>{{ $o->full_name }}</b><div class="text-xs text-muted">{{ $o->email }}</div></td>
        <td data-label="Plan"><span class="badge badge-info"><x-icon name="package" />{{ $o->plan?->name ?? '—' }}</span></td>
        <td data-label="Subscription Value"><b>${{ number_format($o->plan?->price_monthly ?? 0,0) }}/mo</b></td>
        <td data-label="Joined" class="text-sm text-muted">{{ $o->created_at->format('M j, Y') }}</td>
        <td data-label="Status">@php $os = $o->status==='active'?'success':($o->status==='trial'?'warning':'danger'); $osi = $o->status==='active'?'check-circle':($o->status==='trial'?'clock':'alert'); @endphp
          <span class="badge badge-{{ $os }}"><x-icon name="{{ $osi }}" />{{ ucfirst($o->status) }}</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
 </div>
</div>
{{ $owners->links() }}
@endsection
