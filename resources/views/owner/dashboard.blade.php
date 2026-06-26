@extends('layouts.owner')
@section('page-title', 'Dashboard')

@section('content')
@php
  // Derive a month-over-month revenue delta + sparkline series from the
  // existing chart data (no new controller variables required).
  $series   = array_values($chartData ?? []);
  $last     = count($series) ? (float) end($series) : 0;
  $prev     = count($series) > 1 ? (float) $series[count($series) - 2] : 0;
  $revDelta = $prev > 0 ? round(($last - $prev) / $prev * 100, 1) : null;
  $slotsLeft = max(0, $stats['plan_limit'] - $stats['total_units']);
  $planPct   = $stats['plan_limit'] > 0 ? round($stats['total_units'] / $stats['plan_limit'] * 100) : 0;
  $attention = ($stats['overdue_count'] ?? 0) + ($stats['expiring_count'] ?? 0);
@endphp

{{-- ── PRIMARY KPIs ── --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Total Revenue</span>
      <span class="stat-icon is-teal"><x-icon name="wallet" /></span>
    </div>
    <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
    @if($revDelta !== null)
      <div class="stat-delta {{ $revDelta >= 0 ? 'pos' : 'neg' }}">
        <x-icon name="trending-up" class="icon-sm" />{{ $revDelta >= 0 ? '+' : '' }}{{ $revDelta }}% vs last month
      </div>
    @else
      <div class="stat-delta">Collected to date</div>
    @endif
    <svg class="sparkline is-teal mt-3" data-sparkline="{{ json_encode($series) }}" role="img" aria-label="Revenue trend"></svg>
  </div>

  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Rent Collected</span>
      <span class="stat-icon is-gold"><x-icon name="receipt" /></span>
    </div>
    <div class="stat-value">${{ number_format($stats['rent_collected'], 0) }}</div>
    <div class="stat-delta">This month</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Occupancy Rate</span>
      <span class="stat-icon"><x-icon name="percent" /></span>
    </div>
    <div class="stat-value">{{ $stats['occupancy_rate'] }}%</div>
    <div class="stat-delta">{{ $stats['occupied'] }} of {{ $stats['total_units'] }} units occupied</div>
    <div class="progress mt-3"><div class="progress-bar is-teal" style="width:{{ $stats['occupancy_rate'] }}%"></div></div>
  </div>

  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Portfolio</span>
      <span class="stat-icon"><x-icon name="building" /></span>
    </div>
    <div class="stat-value">{{ $stats['total_units'] }}</div>
    <div class="stat-delta">{{ $slotsLeft }} of {{ $stats['plan_limit'] }} slots free</div>
  </div>
</div>

{{-- ── SECONDARY METRICS ── --}}
<div class="metric-grid">
  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon"><x-icon name="check-circle" /></span>
      <span class="metric-tile-label">Occupied</span>
    </div>
    <div class="metric-tile-value">{{ $stats['occupied'] }}</div>
    <div class="metric-tile-foot">rooms with active tenants</div>
  </div>

  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon"><x-icon name="home" /></span>
      <span class="metric-tile-label">Vacant</span>
    </div>
    <div class="metric-tile-value">{{ $stats['vacant'] }}</div>
    <div class="metric-tile-foot">rooms available to let</div>
  </div>

  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon {{ $stats['overdue_count'] > 0 ? 'is-danger' : '' }}"><x-icon name="alert" /></span>
      <span class="metric-tile-label">Overdue Bills</span>
    </div>
    <div class="metric-tile-value">{{ $stats['overdue_count'] }}</div>
    <div class="metric-tile-foot">
      @if($stats['overdue_count'] > 0)
        <span class="badge badge-danger"><x-icon name="alert" />Needs attention</span>
      @else
        <span class="badge badge-success"><x-icon name="check" />All clear</span>
      @endif
    </div>
  </div>

  <div class="metric-tile">
    <div class="metric-tile-top">
      <span class="metric-tile-icon"><x-icon name="calendar" /></span>
      <span class="metric-tile-label">Expiring Soon</span>
    </div>
    <div class="metric-tile-value">{{ $stats['expiring_count'] }}</div>
    <div class="metric-tile-foot">
      @if($stats['expiring_count'] > 0)
        <span class="badge badge-warning"><x-icon name="clock" />Within 30 days</span>
      @else
        <span class="badge badge-gray"><x-icon name="check" />None upcoming</span>
      @endif
    </div>
  </div>
</div>

{{-- ── PLAN USAGE ── --}}
<div class="card mb-5">
  <div class="flex justify-between items-center mb-3">
    <span class="card-title"><x-icon name="layers" /> Plan Usage — {{ auth('owner')->user()->plan->name }}</span>
    <span class="text-md text-muted">{{ $stats['total_units'] }} / {{ $stats['plan_limit'] }} apartments</span>
  </div>
  <div class="progress">
    <div class="progress-bar {{ $planPct > 90 ? 'is-danger' : 'is-teal' }}" style="width:{{ $planPct }}%"></div>
  </div>
  @if($stats['total_units'] >= $stats['plan_limit'])
    <div class="alert alert-danger mt-3 mb-0" role="alert">
      <x-icon name="alert" />
      <div class="alert-body">You've reached your plan limit. <a href="/billing/upgrade" class="fw-600">Upgrade your plan</a> to add more units.</div>
    </div>
  @endif
</div>

{{-- ── REVENUE CHART + QUICK ACTIONS ── --}}
<div class="grid-2-1 mb-5">
  <div class="card">
    <div class="card-header"><span class="card-title"><x-icon name="trending-up" /> Monthly Revenue</span></div>
    <div class="chart-box"><canvas id="revenueChart"></canvas></div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Quick Actions</div>
    <div class="action-list">
      <a href="{{ route('owner.billing.index') }}" class="btn btn-gold"><x-icon name="megaphone" /> Send Billing Notifications</a>
      <a href="{{ route('owner.units.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add New Unit</a>
      <a href="{{ route('owner.tenants.create') }}" class="btn btn-outline"><x-icon name="users" /> Add Tenant</a>
      <a href="{{ route('owner.assets.issues.create') }}" class="btn btn-outline"><x-icon name="wrench" /> Log Technical Issue</a>
      <a href="{{ route('owner.complaints.index') }}" class="btn btn-outline"><x-icon name="clipboard" /> View Complaints</a>
    </div>
  </div>
</div>

{{-- ── EXPIRING CONTRACTS ── --}}
<div class="section-head"><span class="section-title"><x-icon name="calendar" /> Rent Contracts Expiring (next 30 days)</span></div>
<div class="table-wrap table-stack">
  <div class="table-scroll">
    <table>
      <thead><tr><th>Tenant</th><th>Unit</th><th>End Date</th><th>Days Left</th><th>Monthly Rent</th></tr></thead>
      <tbody>
        @forelse($expiringContracts as $c)
        <tr>
          <td data-label="Tenant"><b>{{ $c->tenant?->full_name ?? '—' }}</b></td>
          <td data-label="Unit" class="text-sm">{{ $c->unit?->unit_number ?? '—' }}</td>
          <td data-label="End Date" class="text-sm">{{ $c->end_date->format('M j, Y') }}</td>
          <td data-label="Days Left">
            @php $days = (int) ceil(now()->diffInDays($c->end_date, false)); @endphp
            <span class="badge badge-{{ $days <= 7 ? 'danger' : 'warning' }}">
              <x-icon name="clock" />{{ max(0,$days) }} day{{ $days==1?'':'s' }}
            </span>
          </td>
          <td data-label="Monthly Rent">${{ number_format($c->monthly_rent, 2) }}</td>
        </tr>
        @empty
        <tr class="table-empty">
          <td colspan="5">
            <div class="empty-state">
              <span class="empty-icon"><x-icon name="check-circle" /></span>
              <span class="empty-title">No contracts expiring</span>
              <span class="empty-text">Nothing is up for renewal in the next 30 days.</span>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ── RECENT ACTIVITY ── --}}
<div class="section-head"><span class="section-title"><x-icon name="clock" /> Recent Activity</span></div>
<div class="table-wrap table-stack">
  <div class="table-scroll">
    <table>
      <thead><tr><th>Time</th><th>Action</th><th>Resource</th><th>IP</th></tr></thead>
      <tbody>
        @forelse($recentActivity as $activity)
        @php
          $actBadge = $activity->user_type === 'admin' ? 'danger' : ($activity->user_type === 'owner' ? 'info' : 'success');
          $actLabel = ucfirst(str_replace('_', ' ', $activity->action));
          $actRes   = $activity->resource_type ? $activity->resource_type . ' #' . $activity->resource_id : '—';
        @endphp
        <tr>
          <td data-label="Time" class="text-muted text-sm">{{ $activity->created_at->diffForHumans() }}</td>
          <td data-label="Action"><span class="badge badge-{{ $actBadge }}">{{ $actLabel }}</span></td>
          <td data-label="Resource">{{ $actRes }}</td>
          <td data-label="IP">{{ $activity->ip_address ?? '—' }}</td>
        </tr>
        @empty
        <tr class="table-empty">
          <td colspan="4">
            <div class="empty-state">
              <span class="empty-icon"><x-icon name="inbox" /></span>
              <span class="empty-title">No recent activity</span>
              <span class="empty-text">Activity from tenants and billing will appear here.</span>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
renderBarChart('revenueChart',
  {!! json_encode($chartLabels) !!},
  {!! json_encode($chartData)   !!},
  'Revenue ($)', '#2563eb'
);
</script>
@endpush
