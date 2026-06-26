@extends('layouts.admin')
@section('page-title','Analytics')
@section('content')
<div class="stats-grid">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Active Owners</span><span class="stat-icon"><x-icon name="briefcase" /></span></div><div class="stat-value">{{ $stats['total_owners'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Total Tenants</span><span class="stat-icon"><x-icon name="users" /></span></div><div class="stat-value">{{ $stats['total_tenants'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Occupancy Rate</span><span class="stat-icon"><x-icon name="percent" /></span></div><div class="stat-value">{{ $stats['total_units']>0 ? round($stats['occupied_units']/$stats['total_units']*100,1) : 0 }}%</div><div class="stat-delta">{{ $stats['occupied_units'] }}/{{ $stats['total_units'] }} units</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Overdue Amount</span><span class="stat-icon is-danger"><x-icon name="alert" /></span></div><div class="stat-value">${{ number_format($stats['overdue_amount'],0) }}</div><div class="stat-delta neg"><x-icon name="alert" class="icon-sm" />needs attention</div></div>
</div>
<div class="card">
  <div class="card-title mb-4">MRR Trend</div>
  <div class="chart-box"><canvas id="mrrChart"></canvas></div>
</div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('mrrChart').getContext('2d'),{
  type:'line',
  data:{
    labels:{!! json_encode($monthlyMrr->pluck('label')) !!},
    datasets:[{label:'MRR ($)',data:{!! json_encode($monthlyMrr->pluck('mrr')) !!},borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.08)',tension:.4,fill:true,pointBackgroundColor:'#2563eb'}]
  },
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#e2e7ef'}},x:{grid:{display:false}}}}
});
</script>
@endpush
