@extends('layouts.owner')
@section('page-title','New Advertisement')
@section('content')
<div class="card-header">
  <span class="card-title">Advertise a Vacant Unit</span>
  <a href="{{ route('owner.advertisements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('owner.advertisements.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group"><label class="form-label">Vacant Unit</label>
      <select name="unit_id" class="form-control" id="unitSelect">
        <option value="">— Free-form listing (no specific unit) —</option>
        @foreach($units as $u)
          <option value="{{ $u->id }}"
            data-rent="{{ $u->monthly_rent }}" data-beds="{{ $u->bedrooms }}" data-baths="{{ $u->bathrooms }}"
            data-area="{{ $u->area_sqft }}" data-city="{{ $u->property?->city }}" data-address="{{ $u->property?->address }}"
            {{ old('unit_id')==$u->id?'selected':'' }}>
            #{{ $u->unit_number }} — {{ $u->property?->name }} (${{ number_format($u->monthly_rent,0) }})
          </option>
        @endforeach
      </select>
      <div class="form-hint">Pick a vacant unit to auto-fill its details, or leave blank for a custom listing.</div>
    </div>

    <div class="form-group"><label class="form-label">Title *</label>
      <input name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Spacious 2BR apartment with parking"></div>

    <div class="form-group"><label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4" placeholder="Highlight features, amenities, nearby landmarks…">{{ old('description') }}</textarea></div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Monthly Rent *</label>
        <input name="monthly_rent" id="f_rent" type="number" step="0.01" min="0" class="form-control" required value="{{ old('monthly_rent') }}" placeholder="0.00"></div>
      <div class="form-group"><label class="form-label">Bedrooms</label>
        <select name="bedrooms" id="f_beds" class="form-control">
          <option value="">—</option>
          @foreach(['studio','1br','2br','3br','4br+'] as $b)
            <option value="{{ $b }}" {{ old('bedrooms')===$b?'selected':'' }}>{{ strtoupper($b) }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Bathrooms</label>
        <input name="bathrooms" id="f_baths" type="number" min="0" max="20" class="form-control" value="{{ old('bathrooms') }}"></div>
      <div class="form-group"><label class="form-label">Area (sqft)</label>
        <input name="area_sqft" id="f_area" type="number" step="0.01" min="0" class="form-control" value="{{ old('area_sqft') }}"></div>
    </div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">City</label>
        <input name="city" id="f_city" class="form-control" value="{{ old('city') }}"></div>
      <div class="form-group"><label class="form-label">Address</label>
        <input name="address" id="f_address" class="form-control" value="{{ old('address') }}"></div>
    </div>

    <div class="divider"></div>
    <div class="card-subtitle mb-3"><x-icon name="user" /> Home Owner Contact (shown publicly)</div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Contact Name *</label>
        <input name="contact_name" class="form-control" required value="{{ old('contact_name', $owner->full_name) }}"></div>
      <div class="form-group"><label class="form-label">Contact Phone *</label>
        <input name="contact_phone" class="form-control" required value="{{ old('contact_phone', $owner->phone) }}" placeholder="+1 555 123 4567"></div>
    </div>
    <div class="form-group"><label class="form-label">Contact Email</label>
      <input name="contact_email" type="email" class="form-control" value="{{ old('contact_email', $owner->email) }}"></div>

    <div class="form-group"><label class="form-label">Photos (up to 10)</label>
      <input name="images[]" type="file" accept="image/*" class="form-control" multiple>
      <div class="form-hint">Select one or multiple photos. JPG/PNG/WebP, max 5 MB each. First photo becomes the main image.</div>
    </div>

    <div class="form-actions">
      <button class="btn btn-primary"><x-icon name="megaphone" /> Publish Advertisement</button>
      <a href="{{ route('owner.advertisements.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
document.getElementById('unitSelect').addEventListener('change', function(){
  var o = this.options[this.selectedIndex];
  if (!o.value) return;
  var set = function(id, v){ if(v && v!=='null' && v!=='') document.getElementById(id).value = v; };
  set('f_rent', o.dataset.rent);
  set('f_baths', o.dataset.baths);
  set('f_area', o.dataset.area);
  set('f_city', o.dataset.city);
  set('f_address', o.dataset.address);
  if (o.dataset.beds) document.getElementById('f_beds').value = o.dataset.beds;
});
</script>
@endpush
@endsection
