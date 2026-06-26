@extends('layouts.admin')
@section('page-title','Edit Owner')
@section('content')
<div class="card-header">
  <span class="card-title">Edit Owner — {{ $owner->full_name }}</span>
  <a href="{{ route('admin.owners.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Owners</a>
</div>

<div class="card max-w-md">
  <form method="POST" action="{{ route('admin.owners.update', $owner) }}">
    @csrf @method('PUT')
    <div class="form-row">
      <div class="form-group"><label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" required value="{{ old('full_name', $owner->full_name) }}"></div>
      <div class="form-group"><label class="form-label">Company Name</label>
        <input name="company_name" class="form-control" value="{{ old('company_name', $owner->company_name) }}"></div>
    </div>
    <div class="form-group"><label class="form-label">Phone</label>
      <input name="phone" class="form-control" value="{{ old('phone', $owner->phone) }}" placeholder="+252 61 xxx xxxx"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">City</label>
        <input name="city" class="form-control" value="{{ old('city', $owner->city) }}" placeholder="Mogadishu"></div>
      <div class="form-group"><label class="form-label">Country</label>
        <input name="country" class="form-control" value="{{ old('country', $owner->country) }}" placeholder="Somalia"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Plan *</label>
        <select name="plan_id" class="form-control" required>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ old('plan_id', $owner->plan_id) == $plan->id ? 'selected' : '' }}>
              {{ $plan->name }} (${{ number_format($plan->price_monthly,2) }}/mo)
            </option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Max Apartments *</label>
        <input name="max_apartments" type="number" min="1" class="form-control" required value="{{ old('max_apartments', $owner->max_apartments) }}"></div>
    </div>
    <div class="form-actions">
      <a href="{{ route('admin.owners.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>
@endsection
