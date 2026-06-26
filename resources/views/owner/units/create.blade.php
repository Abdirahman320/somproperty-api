@extends('layouts.owner')
@section('page-title','Add Unit')
@section('content')
<div class="card-header">
  <span class="card-title">Add New Unit</span>
  <a href="{{ route('owner.properties.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Properties</a>
</div>

<div class="card max-w-md">
  <form method="POST" action="{{ route('owner.units.store') }}">
    @csrf
    <div class="form-group"><label class="form-label">Property *</label>
      <select name="property_id" class="form-control" required>
        <option value="">— Select property —</option>
        @forelse($properties as $p)
          <option value="{{ $p->id }}" {{ old('property_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
        @empty
          <option value="" disabled>No properties — add one first</option>
        @endforelse
      </select>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Unit Number *</label>
        <input name="unit_number" class="form-control" required value="{{ old('unit_number') }}" placeholder="e.g. 4B"></div>
      <div class="form-group"><label class="form-label">Floor</label>
        <input name="floor_number" type="number" class="form-control" value="{{ old('floor_number') }}" placeholder="3"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Bedrooms</label>
        <select name="bedrooms" class="form-control">
          @foreach(['studio','1br','2br','3br','4br+'] as $b)
            <option value="{{ $b }}" {{ old('bedrooms')===$b?'selected':'' }}>{{ strtoupper($b) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Area (sqft)</label>
        <input name="area_sqft" type="number" step="0.01" min="0" class="form-control" value="{{ old('area_sqft') }}" placeholder="850"></div>
    </div>
    <div class="form-group"><label class="form-label">Monthly Rent *</label>
      <input name="monthly_rent" type="number" step="0.01" min="0" class="form-control" required value="{{ old('monthly_rent') }}" placeholder="0.00"></div>
    <div class="form-actions">
      <a href="{{ route('owner.properties.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Add Unit</button>
    </div>
  </form>
</div>
@endsection
