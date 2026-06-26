@extends('layouts.agent')
@section('page-title','New Listing')
@section('content')
<div class="card-header">
  <span class="card-title">Create New Listing</span>
  <a href="{{ route('agent.advertisements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('agent.advertisements.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group"><label class="form-label">Title *</label>
      <input name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Spacious 2BR apartment with parking"></div>

    <div class="form-group"><label class="form-label">Description *</label>
      <textarea name="description" class="form-control" rows="4" required placeholder="Highlight features, amenities, nearby landmarks…">{{ old('description') }}</textarea></div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Monthly Rent *</label>
        <input name="monthly_rent" type="number" step="0.01" min="0" class="form-control" required value="{{ old('monthly_rent') }}" placeholder="0.00"></div>
      <div class="form-group"><label class="form-label">Bedrooms *</label>
        <select name="bedrooms" class="form-control" required>
          <option value="">— Select —</option>
          @foreach(['studio'=>'Studio','1br'=>'1 Bedroom','2br'=>'2 Bedrooms','3br'=>'3 Bedrooms','4br+'=>'4+ Bedrooms'] as $v=>$l)
            <option value="{{ $v }}" {{ old('bedrooms')===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Bathrooms *</label>
        <input name="bathrooms" type="number" min="1" max="20" class="form-control" required value="{{ old('bathrooms') }}" placeholder="1"></div>
      <div class="form-group"><label class="form-label">Area (sqft) *</label>
        <input name="area_sqft" type="number" step="0.01" min="0" class="form-control" required value="{{ old('area_sqft') }}" placeholder="650"></div>
    </div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">City *</label>
        <input name="city" class="form-control" required value="{{ old('city') }}" placeholder="e.g. Mogadishu"></div>
      <div class="form-group"><label class="form-label">Address *</label>
        <input name="address" class="form-control" required value="{{ old('address') }}" placeholder="Street / neighbourhood"></div>
    </div>

    <div class="divider"></div>
    <div class="card-subtitle mb-3"><x-icon name="user" /> Your Contact Details (shown publicly)</div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Contact Name *</label>
        <input name="contact_name" class="form-control" required value="{{ old('contact_name', $agent->full_name) }}"></div>
      <div class="form-group"><label class="form-label">Contact Phone *</label>
        <input name="contact_phone" class="form-control" required value="{{ old('contact_phone', $agent->phone) }}" placeholder="+252 61 xxx xxxx"></div>
    </div>
    <div class="form-group"><label class="form-label">Contact Email *</label>
      <input name="contact_email" type="email" class="form-control" required value="{{ old('contact_email', $agent->email) }}"></div>

    <div class="divider"></div>
    <div class="card-subtitle mb-3"><x-icon name="image" /> Apartment Photos</div>

    <div class="form-group">
      <label class="form-label">Photos (up to 10)</label>
      <input name="images[]" type="file" accept="image/*" class="form-control" multiple>
      <div class="form-hint">Select one or multiple photos. JPG/PNG/WebP, max 5 MB each. First photo becomes the main image.</div>
    </div>

    <div class="form-actions">
      <button class="btn btn-primary"><x-icon name="megaphone" /> Publish Listing</button>
      <a href="{{ route('agent.advertisements.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
@endsection
