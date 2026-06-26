@extends('layouts.agent')
@section('title', 'Agent Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
  <div>
    <h2 class="page-heading">Welcome, {{ $agent->name }}</h2>
    <p class="page-sub">Viewing properties for <strong>{{ $owner->company_name ?? $owner->full_name }}</strong></p>
  </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:24px">
  <div class="stat-card">
    <div class="stat-label">Total Properties</div>
    <div class="stat-value">{{ $properties->count() }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Units</div>
    <div class="stat-value">{{ $totalUnits }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Occupied</div>
    <div class="stat-value" style="color:#16a34a">{{ $occupied }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Vacant</div>
    <div class="stat-value" style="color:#f59e0b">{{ $vacant }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Active Tenants</div>
    <div class="stat-value">{{ $tenants }}</div>
  </div>
</div>

{{-- Properties --}}
<div class="card">
  <div class="card-head">
    <h3 class="card-title">Properties &amp; Units</h3>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($properties as $property)
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <div>
            <div style="font-weight:600;color:#0f1f3d;font-size:15px">{{ $property->name }}</div>
            <div style="font-size:12px;color:#8895a7">{{ $property->address }}, {{ $property->city }}</div>
          </div>
          <span class="badge badge-green">{{ $property->property_type }}</span>
        </div>
        @if($property->units->count())
          <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
              <thead>
                <tr style="color:#8895a7;text-align:left">
                  <th style="padding:6px 8px;font-weight:600">Unit</th>
                  <th style="padding:6px 8px;font-weight:600">Floor</th>
                  <th style="padding:6px 8px;font-weight:600">Bedrooms</th>
                  <th style="padding:6px 8px;font-weight:600">Rent</th>
                  <th style="padding:6px 8px;font-weight:600">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($property->units->whereNotIn('status', ['disposed']) as $unit)
                  <tr style="border-top:1px solid #f8fafc">
                    <td style="padding:6px 8px;font-weight:600">{{ $unit->unit_number }}</td>
                    <td style="padding:6px 8px;color:#6b7a8d">{{ $unit->floor_number }}</td>
                    <td style="padding:6px 8px;color:#6b7a8d">{{ $unit->bedrooms }}</td>
                    <td style="padding:6px 8px;color:#0f1f3d">${{ number_format($unit->monthly_rent, 0) }}/mo</td>
                    <td style="padding:6px 8px">
                      @if($unit->status === 'vacant')
                        <span class="badge badge-yellow">Vacant</span>
                      @elseif($unit->status === 'occupied')
                        <span class="badge badge-green">Occupied</span>
                      @else
                        <span class="badge">{{ ucfirst($unit->status) }}</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p style="font-size:13px;color:#8895a7">No units yet.</p>
        @endif
      </div>
    @empty
      <div style="padding:32px;text-align:center;color:#8895a7">No active properties.</div>
    @endforelse
  </div>
</div>
@endsection
