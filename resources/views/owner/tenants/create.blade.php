@extends('layouts.owner')
@section('page-title','Add Tenant')
@section('content')
<div class="card-header">
  <span class="card-title">Add New Tenant</span>
  <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Tenants</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('owner.tenants.store') }}">
    @csrf

    <div class="subhead is-first">Tenant Details</div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" required value="{{ old('full_name') }}" placeholder="John Doe"></div>
      <div class="form-group"><label class="form-label">Email *</label>
        <input name="email" type="email" class="form-control" required value="{{ old('email') }}" placeholder="john@example.com"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555 123 4567"></div>
      <div class="form-group"><label class="form-label">National ID</label>
        <input name="national_id" class="form-control" value="{{ old('national_id') }}" placeholder="ID / Passport"></div>
    </div>

    <div class="subhead">Lease / Contract</div>
    <div class="form-group"><label class="form-label">Unit *</label>
      <select name="unit_id" class="form-control" required>
        <option value="">— Select a vacant unit —</option>
        @forelse($units as $u)
          <option value="{{ $u->id }}" data-rent="{{ $u->monthly_rent }}" {{ old('unit_id')==$u->id?'selected':'' }}>
            {{ $u->unit_number }} — {{ $u->property->name ?? 'Unassigned' }} (${{ number_format($u->monthly_rent,2) }}/mo)
          </option>
        @empty
          <option value="" disabled>No vacant units available — add or free up a unit first</option>
        @endforelse
      </select>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Start Date *</label>
        <input name="start_date" type="date" class="form-control" required value="{{ old('start_date', now()->toDateString()) }}"></div>
      <div class="form-group"><label class="form-label">End Date *</label>
        <input name="end_date" type="date" class="form-control" required value="{{ old('end_date', now()->addYear()->toDateString()) }}"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Monthly Rent *</label>
        <input id="monthly_rent" name="monthly_rent" type="number" step="0.01" min="0" class="form-control" required value="{{ old('monthly_rent') }}" placeholder="0.00"></div>
      <div class="form-group"><label class="form-label">Security Deposit</label>
        <input name="security_deposit" type="number" step="0.01" min="0" class="form-control" value="{{ old('security_deposit') }}" placeholder="0.00"></div>
    </div>

    <div class="form-actions">
      <a href="{{ route('owner.tenants.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Add Tenant &amp; Send Welcome Email</button>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
  // Auto-fill monthly rent from the selected unit
  document.querySelector('select[name="unit_id"]')?.addEventListener('change', function () {
    var rent = this.selectedOptions[0]?.dataset.rent;
    var input = document.getElementById('monthly_rent');
    if (rent && input && !input.value) input.value = parseFloat(rent).toFixed(2);
  });
</script>
@endpush
