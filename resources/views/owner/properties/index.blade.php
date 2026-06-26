@extends('layouts.owner')
@section('page-title','Properties & Units')
@section('content')
<div class="card-header">
  <span class="card-title">Properties ({{ $properties->count() }})</span>
  <button class="btn btn-primary btn-sm" onclick="Modal.open('Add Property',`
    <form method='POST' action='{{ route('owner.properties.store') }}'><input type='hidden' name='_token' value='{{ csrf_token() }}'>
      <div class='form-group'><label class='form-label'>Name</label><input name='name' class='form-control' required placeholder='Green Valley Apartments'></div>
      <div class='form-group'><label class='form-label'>Address</label><input name='address' class='form-control' required placeholder='123 Main St, City'></div>
      <div class='form-row'>
        <div class='form-group'><label class='form-label'>City</label><input name='city' class='form-control' placeholder='Miami'></div>
        <div class='form-group'><label class='form-label'>Country</label><input name='country' class='form-control' placeholder='USA'></div>
      </div>
      <div class='form-row'>
        <div class='form-group'><label class='form-label'>Type</label><select name='property_type' class='form-control'><option value='residential'>Residential</option><option value='commercial'>Commercial</option><option value='mixed'>Mixed</option></select></div>
        <div class='form-group'><label class='form-label'>Floors</label><input name='total_floors' type='number' class='form-control' value='1' min='1'></div>
      </div>
      <div class='form-actions'><button type='button' class='btn btn-outline' onclick='Modal.close()'>Cancel</button>
      <button type='submit' class='btn btn-primary'>Add Property</button></div>
    </form>`)"><x-icon name="plus" /> Add Property</button>
</div>
<div class="grid-3">
  @forelse($properties as $p)
  <div class="card cursor-pointer">
    <div class="flex justify-between items-start mb-2">
      <div>
        <div class="text-base fw-600">{{ $p->name }}</div>
        <div class="text-sm text-muted flex items-center gap-1"><x-icon name="map-pin" class="icon-sm" /> {{ $p->address }}</div>
      </div>
      <span class="badge badge-{{ $p->status==='active'?'success':'gray' }}">{{ ucfirst($p->status) }}</span>
    </div>
    <div class="flex gap-2 flex-wrap mb-3">
      <span class="badge badge-info"><x-icon name="home" />{{ $p->units_count }} units</span>
      <span class="badge badge-success"><x-icon name="check-circle" />{{ $p->occupied_count }} occupied</span>
      @if($p->units_count - $p->occupied_count > 0)<span class="badge badge-warning"><x-icon name="door" />{{ $p->units_count - $p->occupied_count }} vacant</span>@endif
    </div>
    <div class="progress mb-1">
      <div class="progress-bar is-teal" style="width:{{ $p->units_count > 0 ? round($p->occupied_count/$p->units_count*100) : 0 }}%"></div>
    </div>
    <div class="text-xs text-muted">{{ $p->units_count > 0 ? round($p->occupied_count/$p->units_count*100) : 0 }}% occupied</div>
    <div class="mt-3 flex gap-2">
      <button class="btn btn-outline btn-xs" onclick="Modal.open('Add Unit to {{ addslashes($p->name) }}',`
        <form method='POST' action='{{ route('owner.units.store') }}'><input type='hidden' name='_token' value='{{ csrf_token() }}'>
          <input type='hidden' name='property_id' value='{{ $p->id }}'>
          <div class='form-row'>
            <div class='form-group'><label class='form-label'>Unit Number</label><input name='unit_number' class='form-control' required placeholder='3B'></div>
            <div class='form-group'><label class='form-label'>Floor</label><input name='floor_number' type='number' class='form-control' value='1'></div>
          </div>
          <div class='form-row'>
            <div class='form-group'><label class='form-label'>Bedrooms</label><select name='bedrooms' class='form-control'><option value='studio'>Studio</option><option value='1br'>1 BR</option><option value='2br'>2 BR</option><option value='3br'>3 BR</option></select></div>
            <div class='form-group'><label class='form-label'>Monthly Rent ($)</label><input name='monthly_rent' type='number' class='form-control' required placeholder='1200'></div>
          </div>
          <div class='form-group'><label class='form-label'>Area (sq ft)</label><input name='area_sqft' type='number' class='form-control' placeholder='650'></div>
          <div class='form-actions'><button type='button' class='btn btn-outline' onclick='Modal.close()'>Cancel</button>
          <button type='submit' class='btn btn-primary'>Add Unit</button></div>
        </form>`)"><x-icon name="plus" /> Add Unit</button>
    </div>
  </div>
  @empty
  <div class="col-span-full empty-state"><div class="empty-icon"><x-icon name="building" /></div><div class="empty-title">No properties yet</div><div class="empty-text">Add your first property above.</div></div>
  @endforelse
</div>
@endsection
