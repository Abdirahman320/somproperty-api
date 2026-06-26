@extends('layouts.owner')
@section('page-title','Add Utility Readings')
@section('content')
<div class="card-header">
  <span class="card-title">Record Utility Readings</span>
  <a href="{{ route('owner.billing.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Billing</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('owner.billing.utility.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group"><label class="form-label">Unit *</label>
        <select name="unit_id" class="form-control" required>
          <option value="">— Select unit —</option>
          @forelse($units as $u)
            <option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>
              {{ $u->unit_number }} — {{ $u->property->name ?? '' }}
            </option>
          @empty
            <option value="" disabled>No occupied units available</option>
          @endforelse
        </select>
      </div>
      <div class="form-group"><label class="form-label">Billing Month *</label>
        <input name="billing_month" type="date" class="form-control" required
               value="{{ old('billing_month', now()->startOfMonth()->toDateString()) }}"></div>
    </div>

    {{-- Water --}}
    <div class="subhead flex items-center gap-2"><x-icon name="droplet" /> Water</div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Previous Reading</label>
        <input id="water_prev" name="water_prev" type="number" step="0.001" min="0" class="form-control" value="{{ old('water_prev') }}" oninput="calcUtility('water')"></div>
      <div class="form-group"><label class="form-label">Current Reading</label>
        <input id="water_curr" name="water_curr" type="number" step="0.001" min="0" class="form-control" value="{{ old('water_curr') }}" oninput="calcUtility('water')"></div>
      <div class="form-group"><label class="form-label">Rate / Unit</label>
        <input id="water_rate" name="water_rate" type="number" step="0.0001" min="0" class="form-control" value="{{ old('water_rate') }}" oninput="calcUtility('water')"></div>
    </div>
    <div class="text-sm text-muted">Consumption: <b id="water_consumption">0.000</b> · Amount: <b id="water_amount_display">$0.00</b></div>
    <input type="hidden" id="water_calculated">

    {{-- Electric --}}
    <div class="subhead flex items-center gap-2"><x-icon name="zap" /> Electricity</div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Previous Reading</label>
        <input id="electric_prev" name="electric_prev" type="number" step="0.001" min="0" class="form-control" value="{{ old('electric_prev') }}" oninput="calcUtility('electric')"></div>
      <div class="form-group"><label class="form-label">Current Reading</label>
        <input id="electric_curr" name="electric_curr" type="number" step="0.001" min="0" class="form-control" value="{{ old('electric_curr') }}" oninput="calcUtility('electric')"></div>
      <div class="form-group"><label class="form-label">Rate / Unit</label>
        <input id="electric_rate" name="electric_rate" type="number" step="0.0001" min="0" class="form-control" value="{{ old('electric_rate') }}" oninput="calcUtility('electric')"></div>
    </div>
    <div class="text-sm text-muted">Consumption: <b id="electric_consumption">0.000</b> · Amount: <b id="electric_amount_display">$0.00</b></div>
    <input type="hidden" id="electric_calculated">

    <div class="form-actions">
      <a href="{{ route('owner.billing.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Readings &amp; Update Bill</button>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>calcUtility('water');calcUtility('electric');</script>
@endpush
