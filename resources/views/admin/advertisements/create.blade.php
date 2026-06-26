@extends('layouts.admin')
@section('page-title','New Advertisement')
@section('content')
<div class="card-header">
  <span class="card-title">Create Advertisement</span>
  <a href="{{ route('admin.advertisements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('admin.advertisements.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="form-row">
      <div class="form-group"><label class="form-label">Owner</label>
        <select name="owner_id" class="form-control">
          <option value="">— None / external —</option>
          @foreach($owners as $o)
            <option value="{{ $o->id }}" {{ old('owner_id')==$o->id?'selected':'' }}>{{ $o->full_name }} ({{ $o->email }})</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Vacant Unit</label>
        <select name="unit_id" class="form-control">
          <option value="">— None (free-form) —</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>
              #{{ $u->unit_number }} — {{ $u->property?->name }} ({{ $u->owner?->full_name }})
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-group"><label class="form-label">Title *</label>
      <input name="title" class="form-control" required value="{{ old('title') }}"></div>
    <div class="form-group"><label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea></div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Monthly Rent *</label>
        <input name="monthly_rent" type="number" step="0.01" min="0" class="form-control" required value="{{ old('monthly_rent') }}"></div>
      <div class="form-group"><label class="form-label">Bedrooms</label>
        <select name="bedrooms" class="form-control">
          <option value="">—</option>
          @foreach(['studio','1br','2br','3br','4br+'] as $b)
            <option value="{{ $b }}" {{ old('bedrooms')===$b?'selected':'' }}>{{ strtoupper($b) }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city') }}"></div>
      <div class="form-group"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address') }}"></div>
    </div>

    <div class="divider"></div>
    <div class="card-subtitle mb-3"><x-icon name="user" /> Home Owner Contact (shown publicly)</div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Contact Name *</label>
        <input name="contact_name" class="form-control" required value="{{ old('contact_name') }}"></div>
      <div class="form-group"><label class="form-label">Contact Phone *</label>
        <input name="contact_phone" class="form-control" required value="{{ old('contact_phone') }}"></div>
    </div>
    <div class="form-group"><label class="form-label">Contact Email</label>
      <input name="contact_email" type="email" class="form-control" value="{{ old('contact_email') }}"></div>

    <div class="form-group"><label class="form-label">Photo</label>
      <input name="image" type="file" accept="image/*" class="form-control"></div>

    <div class="form-actions">
      <button class="btn btn-primary"><x-icon name="megaphone" /> Publish</button>
      <a href="{{ route('admin.advertisements.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
@endsection
