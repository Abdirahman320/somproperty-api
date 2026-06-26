@extends('layouts.owner')
@section('page-title','Register Asset')
@section('content')
<div class="card-header">
  <span class="card-title">Register New Asset</span>
  <a href="{{ route('owner.assets.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Assets</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('owner.assets.store') }}">
    @csrf
    <div class="form-row">
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
      <div class="form-group"><label class="form-label">Asset Name *</label>
        <input name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g. Rooftop AC Unit"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category *</label>
        <select name="category" class="form-control" required>
          @foreach(['mechanical','electrical','plumbing','electronic','furniture','vehicle','other'] as $cat)
            <option value="{{ $cat }}" {{ old('category')===$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Location</label>
        <input name="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Basement, Floor 3"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Brand</label>
        <input name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. Samsung"></div>
      <div class="form-group"><label class="form-label">Model</label>
        <input name="model" class="form-control" value="{{ old('model') }}" placeholder="Model number"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Serial Number</label>
        <input name="serial_number" class="form-control" value="{{ old('serial_number') }}"></div>
      <div class="form-group"><label class="form-label">Purchase Value</label>
        <input name="purchase_value" type="number" step="0.01" min="0" class="form-control" value="{{ old('purchase_value') }}" placeholder="0.00"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Purchase Date</label>
        <input name="purchase_date" type="date" class="form-control" value="{{ old('purchase_date') }}"></div>
      <div class="form-group"><label class="form-label">Warranty Expires</label>
        <input name="warranty_expires_at" type="date" class="form-control" value="{{ old('warranty_expires_at') }}"></div>
    </div>
    <div class="form-actions">
      <a href="{{ route('owner.assets.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Register Asset</button>
    </div>
  </form>
</div>
@endsection
