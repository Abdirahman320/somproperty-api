@extends('layouts.owner')
@section('page-title','Reports')
@section('content')
<div class="stats-grid mb-5">
  <div class="stat-card"><div class="stat-label">Annual Revenue {{ $year }}</div><div class="stat-value">${{ number_format($totals['annual_revenue'],0) }}</div></div>
  <div class="stat-card"><div class="stat-label">Total Units</div><div class="stat-value">{{ $totals['total_units'] }}</div><div class="stat-delta">{{ $totals['occupancy_rate'] }}% occupied</div></div>
  <div class="stat-card"><div class="stat-label">Occupied Units</div><div class="stat-value">{{ $totals['occupied_units'] }}</div></div>
  <div class="stat-card"><div class="stat-label">Overdue Amount</div><div class="stat-value">${{ number_format($totals['overdue_amount'],0) }}</div><div class="stat-delta neg">outstanding</div></div>
</div>
<div class="card">
  <div class="card-title mb-4">Monthly Revenue Breakdown — {{ $year }}</div>
  <div class="table-wrap table-stack"><div class="table-scroll">
    <table>
      <thead><tr><th>Month</th><th>Rent Collected</th><th>Water</th><th>Electric</th><th>Total</th></tr></thead>
      <tbody>
        @foreach($monthlyRevenue as $m)
        <tr>
          <td data-label="Month" class="fw-500">{{ $m['month'] }}</td>
          <td data-label="Rent Collected">${{ number_format($m['rent'],2) }}</td>
          <td data-label="Water" class="text-info">${{ number_format($m['water'],2) }}</td>
          <td data-label="Electric" class="text-gold">${{ number_format($m['electric'],2) }}</td>
          <td data-label="Total"><b>${{ number_format($m['rent']+$m['water']+$m['electric'],2) }}</b></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div></div>
</div>
@endsection
