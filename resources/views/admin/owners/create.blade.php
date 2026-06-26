@extends('layouts.admin')
@section('page-title','Create Owner')
@section('content')
<div class="card-header">
  <span class="card-title">Create New Owner</span>
  <a href="{{ route('admin.owners.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Owners</a>
</div>

<div class="card max-w-md">
  <form method="POST" action="{{ route('admin.owners.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group"><label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" required value="{{ old('full_name') }}" placeholder="Jane Smith"></div>
      <div class="form-group"><label class="form-label">Company Name</label>
        <input name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="Acme Properties LLC"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Email *</label>
        <input name="email" type="email" class="form-control" required value="{{ old('email') }}" placeholder="owner@example.com"></div>
      <div class="form-group"><label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555 123 4567"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Plan *</label>
        <select name="plan_id" class="form-control" required>
          <option value="">— Select plan —</option>
          @forelse($plans as $plan)
            <option value="{{ $plan->id }}" data-max="{{ $plan->max_apartments }}" {{ old('plan_id')==$plan->id?'selected':'' }}>
              {{ $plan->name }} (${{ number_format($plan->price_monthly,2) }}/mo · {{ $plan->max_apartments }} units)
            </option>
          @empty
            <option value="" disabled>No active plans — create a plan first</option>
          @endforelse
        </select>
      </div>
      <div class="form-group"><label class="form-label">Max Apartments *</label>
        <input id="max_apartments" name="max_apartments" type="number" min="1" class="form-control" required value="{{ old('max_apartments') }}" placeholder="e.g. 50"></div>
    </div>
    <div class="form-actions">
      <a href="{{ route('admin.owners.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Owner &amp; Send Invite</button>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
  document.querySelector('select[name="plan_id"]')?.addEventListener('change', function () {
    var max = this.selectedOptions[0]?.dataset.max;
    var input = document.getElementById('max_apartments');
    if (max && input && !input.value) input.value = max;
  });
</script>
@endpush
