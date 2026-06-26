@extends('layouts.admin')
@section('page-title','Admin Dashboard')
@section('content')

{{-- ── PRIMARY KPIs ── --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Active Owners</span>
      <span class="stat-icon"><x-icon name="briefcase" /></span>
    </div>
    <div class="stat-value">{{ $stats['total_owners'] }}</div>
    <div class="stat-delta">{{ $stats['trial_owners'] }} on trial</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Total Properties</span>
      <span class="stat-icon"><x-icon name="building" /></span>
    </div>
    <div class="stat-value">{{ $stats['total_properties'] }}</div>
    <div class="stat-delta">Across all owners</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Monthly Revenue</span>
      <span class="stat-icon is-teal"><x-icon name="wallet" /></span>
    </div>
    <div class="stat-value">${{ number_format($stats['mrr'],0) }}</div>
    <div class="stat-delta">Recurring (MRR)</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Total Tenants</span>
      <span class="stat-icon"><x-icon name="users" /></span>
    </div>
    <div class="stat-value">{{ $stats['total_tenants'] }}</div>
    <div class="stat-delta">Platform-wide</div>
  </div>
</div>

{{-- ── SECONDARY METRICS (system-wide rooms, revenue & contracts) ── --}}
<div class="metric-grid">
  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon"><x-icon name="check-circle" /></span>
      <span class="metric-tile-label">Rooms Occupied</span>
    </div>
    <div class="metric-tile-value">{{ $stats['occupied'] }}</div>
    @php $tot = $stats['occupied'] + $stats['vacant']; @endphp
    <div class="metric-tile-foot">{{ $tot > 0 ? round($stats['occupied'] / $tot * 100, 1) : 0 }}% occupancy</div>
  </div>
  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon"><x-icon name="door" /></span>
      <span class="metric-tile-label">Rooms Vacant</span>
    </div>
    <div class="metric-tile-value">{{ $stats['vacant'] }}</div>
    <div class="metric-tile-foot">Across all owners</div>
  </div>
  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon is-success"><x-icon name="trending-up" /></span>
      <span class="metric-tile-label">Total Revenue</span>
    </div>
    <div class="metric-tile-value">${{ number_format($stats['total_revenue'],0) }}</div>
    <div class="metric-tile-foot">Collected to date</div>
  </div>
  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon {{ $stats['expiring_count']>0?'is-warning':'' }}"><x-icon name="calendar" /></span>
      <span class="metric-tile-label">Contracts Expiring</span>
    </div>
    <div class="metric-tile-value">{{ $stats['expiring_count'] }}</div>
    <div class="metric-tile-foot">
      @if($stats['expiring_count']>0)
        <span class="badge badge-warning"><x-icon name="clock" />Within 30 days</span>
      @else
        <span class="badge badge-success"><x-icon name="check" />None upcoming</span>
      @endif
    </div>
  </div>
</div>

<div class="card-header"><span class="card-title">Plan Distribution</span></div>
<div class="table-wrap table-stack mb-5">
 <div class="table-scroll">
  <table><thead><tr><th>Plan</th><th>Price</th><th>Max Apts</th><th>Active Owners</th><th>MRR</th></tr></thead>
  <tbody>
    @foreach($planStats as $p)
    <tr>
      <td data-label="Plan"><b>{{ $p->name }}</b></td>
      <td data-label="Price">${{ number_format($p->price_monthly,0) }}/mo</td>
      <td data-label="Max Apts">{{ $p->max_apartments }}</td>
      <td data-label="Active Owners">{{ $p->owners_count }}</td>
      <td data-label="MRR">${{ number_format($p->owners_count * $p->price_monthly,0) }}</td>
    </tr>
    @endforeach
  </tbody></table>
 </div>
</div>

{{-- Expiring Contracts (system-wide) --}}
<div class="card-header"><span class="card-title">Rent Contracts Expiring (next 30 days)</span></div>
<div class="table-wrap table-stack mb-5">
 <div class="table-scroll">
  <table><thead><tr><th>Owner</th><th>Tenant</th><th>Unit</th><th>End Date</th><th>Days Left</th></tr></thead>
  <tbody>
    @forelse($expiringContracts as $c)
    <tr>
      <td data-label="Owner" class="text-sm">{{ $c->owner?->full_name ?? '—' }}</td>
      <td data-label="Tenant"><b>{{ $c->tenant?->full_name ?? '—' }}</b></td>
      <td data-label="Unit" class="text-sm">{{ $c->unit?->unit_number ?? '—' }}</td>
      <td data-label="End Date" class="text-sm">{{ $c->end_date->format('M j, Y') }}</td>
      <td data-label="Days Left">
        @php $days = (int) ceil(now()->diffInDays($c->end_date, false)); @endphp
        <span class="badge badge-{{ $days <= 7 ? 'danger' : 'warning' }}"><x-icon name="clock" />{{ max(0,$days) }} day{{ $days==1?'':'s' }}</span>
      </td>
    </tr>
    @empty
    <tr class="table-empty"><td colspan="5">
      <div class="empty-state"><div class="empty-icon"><x-icon name="calendar" /></div><div class="empty-title">No contracts expiring in the next 30 days</div></div>
    </td></tr>
    @endforelse
  </tbody></table>
 </div>
</div>

<div class="card-header"><span class="card-title">Recent Owners</span><a href="{{ route('admin.owners.index') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> New Owner</a></div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table><thead><tr><th>Owner</th><th>Email</th><th>Plan</th><th>Max Apts</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
  <tbody>
    @foreach($recentOwners as $o)
    <tr>
      <td data-label="Owner"><b>{{ $o->full_name }}</b><div class="text-xs text-muted">{{ $o->company_name }}</div></td>
      <td data-label="Email" class="text-sm">{{ $o->email }}</td>
      <td data-label="Plan"><span class="badge badge-info"><x-icon name="package" />{{ $o->plan?->name ?? '—' }}</span></td>
      <td data-label="Max Apts">{{ $o->max_apartments }}</td>
      <td data-label="Status">@php $os = $o->status==='active'?'success':($o->status==='trial'?'warning':'danger'); $osi = $o->status==='active'?'check-circle':($o->status==='trial'?'clock':'alert'); @endphp
        <span class="badge badge-{{ $os }}"><x-icon name="{{ $osi }}" />{{ ucfirst($o->status) }}</span></td>
      <td data-label="Joined" class="text-sm text-muted">{{ $o->created_at->format('M j, Y') }}</td>
      <td data-label="Actions">
        @if($o->status==='active')
          <form method="POST" action="{{ route('admin.owners.suspend',$o) }}" class="d-inline">@csrf @method('PUT')<button class="btn btn-outline btn-xs">Suspend</button></form>
        @else
          <form method="POST" action="{{ route('admin.owners.activate',$o) }}" class="d-inline">@csrf @method('PUT')<button class="btn btn-gold btn-xs">Activate</button></form>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody></table>
 </div>
</div>
@endsection
