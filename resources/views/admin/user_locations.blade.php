@extends('layouts.admin')
@section('page-title','User Locations')
@section('content')
<div class="card-header">
  <span class="card-title">User Locations</span>
  <span class="badge badge-info">{{ $users->count() }} user{{ $users->count()==1?'':'s' }}</span>
</div>

{{-- Filters --}}
<div class="card mb-4">
  <form method="GET" action="{{ route('admin.user-locations') }}" class="flex flex-wrap gap-3 items-end">
    <div class="form-group mb-0" style="min-width:140px">
      <label class="form-label">User Type</label>
      <select name="type" class="form-control" onchange="this.form.submit()">
        <option value="all"    {{ $type==='all'   ?'selected':'' }}>All types</option>
        <option value="owner"  {{ $type==='owner' ?'selected':'' }}>Owners</option>
        <option value="tenant" {{ $type==='tenant'?'selected':'' }}>Tenants</option>
        <option value="agent"  {{ $type==='agent' ?'selected':'' }}>Agents</option>
      </select>
    </div>
    <div class="form-group mb-0" style="min-width:160px">
      <label class="form-label">City</label>
      <select name="city" class="form-control">
        <option value="">All cities</option>
        @foreach($allCities as $c)
          <option value="{{ $c }}" {{ $city===$c?'selected':'' }}>{{ $c }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group mb-0" style="min-width:160px">
      <label class="form-label">Country</label>
      <select name="country" class="form-control">
        <option value="">All countries</option>
        @foreach($allCountries as $c)
          <option value="{{ $c }}" {{ $country===$c?'selected':'' }}>{{ $c }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('admin.user-locations') }}" class="btn btn-outline btn-sm">Clear</a>
  </form>
</div>

<div class="table-wrap table-stack">
  <div class="table-scroll">
  <table id="locTable">
    <thead>
      <tr>
        <th data-sort>Name</th>
        <th>Type</th>
        <th>Email</th>
        <th>Phone</th>
        <th data-sort>City</th>
        <th data-sort>Country</th>
        <th>Status</th>
        <th>Joined</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $u)
      @php
        $uType    = $u['_type'] ?? '';
        $uBadge   = $uType === 'Owner' ? 'info' : ($uType === 'Agent' ? 'warning' : 'success');
        $uStatus  = $u['status'] ?? '';
        $uSBadge  = $uStatus === 'active' ? 'success' : ($uStatus === 'suspended' ? 'danger' : 'warning');
      @endphp
      <tr>
        <td data-label="Name"><b>{{ $u['full_name'] ?? '—' }}</b></td>
        <td data-label="Type"><span class="badge badge-{{ $uBadge }}">{{ $uType }}</span></td>
        <td data-label="Email" class="text-sm">{{ $u['email'] ?? '—' }}</td>
        <td data-label="Phone" class="text-sm">{{ $u['phone'] ?? '—' }}</td>
        <td data-label="City">
          @if(!empty($u['city']))
            <span class="badge badge-gray"><x-icon name="map-pin" />{{ $u['city'] }}</span>
          @else —
          @endif
        </td>
        <td data-label="Country">{{ $u['country'] ?? '—' }}</td>
        <td data-label="Status">
          <span class="badge badge-{{ $uSBadge }}">{{ ucfirst($uStatus) ?: '—' }}</span>
        </td>
        <td data-label="Joined" class="text-sm text-muted">{{ $u['created_at'] ?? '—' }}</td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="8">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="map-pin" /></div>
          <div class="empty-title">No users found</div>
          <div class="empty-text">No users match the selected filters. Try adding city/country when creating users.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
@endsection
@push('scripts')<script>initSortableTable('locTable');filterTable('globalSearch','locTable');</script>@endpush
