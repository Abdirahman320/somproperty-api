@extends('layouts.owner')
@section('page-title','Log Technical Issue')
@section('content')
<div class="card-header">
  <span class="card-title">Log Technical Issue</span>
  <a href="{{ route('owner.assets.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Assets</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="{{ route('owner.assets.issues.store') }}">
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
      <div class="form-group"><label class="form-label">Unit (optional)</label>
        <select name="unit_id" class="form-control">
          <option value="">— Whole property / N/A —</option>
          @foreach($units as $u)
            <option value="{{ $u->id }}" {{ old('unit_id')==$u->id?'selected':'' }}>
              {{ $u->unit_number }} — {{ $u->property->name ?? '' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Title *</label>
      <input name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Water leak in unit 4B"></div>
    <div class="form-group"><label class="form-label">Description *</label>
      <textarea name="description" class="form-control" rows="4" required placeholder="Describe the issue in detail…">{{ old('description') }}</textarea></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Priority *</label>
        <select name="priority" class="form-control" required>
          @foreach(['low','medium','high','critical'] as $pr)
            <option value="{{ $pr }}" {{ old('priority','medium')===$pr?'selected':'' }}>{{ ucfirst($pr) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Assigned To</label>
        <input name="assigned_to" class="form-control" value="{{ old('assigned_to') }}" placeholder="Contractor / staff name"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Scheduled Date</label>
        <input name="scheduled_date" type="date" class="form-control" value="{{ old('scheduled_date') }}"></div>
      <div class="form-group"><label class="form-label">Estimated Cost</label>
        <input name="estimated_cost" type="number" step="0.01" min="0" class="form-control" value="{{ old('estimated_cost') }}" placeholder="0.00"></div>
    </div>
    <div class="form-actions">
      <a href="{{ route('owner.assets.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-danger">Log Issue</button>
    </div>
  </form>
</div>
@endsection
