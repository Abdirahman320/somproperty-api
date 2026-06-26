@extends('layouts.admin')
@section('page-title','Plans & Pricing')
@section('content')

<div class="card-header">
  <span class="card-title">Subscription Plans</span>
  <button class="btn btn-primary btn-sm" onclick="Modal.open('Add New Plan', document.getElementById('add-plan-tpl').innerHTML)"><x-icon name="plus" /> Add Plan</button>
</div>

{{-- Plan cards overview --}}
<div class="grid-5 mb-5">
  @foreach($plans as $p)
  <div class="stat-card">
    <div class="stat-label">{{ $p->name }}</div>
    <div class="stat-value sm">${{ number_format($p->price_monthly,0) }}<span class="text-md text-muted fw-500">/mo</span></div>
    <div class="text-sm text-muted mt-1">up to {{ $p->max_apartments }} units</div>
    <div class="flex gap-2 flex-wrap mt-2">
      <span class="badge badge-{{ $p->is_active?'success':'gray' }}"><x-icon name="{{ $p->is_active?'check-circle':'x' }}" />{{ $p->is_active?'Active':'Inactive' }}</span>
      <span class="badge badge-gray"><x-icon name="briefcase" />{{ $p->owners_count }} owner{{ $p->owners_count==1?'':'s' }}</span>
    </div>
  </div>
  @endforeach
</div>

{{-- Editable table --}}
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table>
    <thead>
      <tr><th>Plan</th><th>Capacity</th><th>Price / mo</th><th>Active Owners</th><th>MRR</th><th>Edit (admin only)</th></tr>
    </thead>
    <tbody>
      @foreach($plans as $p)
      <tr>
        <td data-label="Plan"><b>{{ $p->name }}</b><div class="text-xs text-muted">{{ $p->slug }}</div></td>
        <td data-label="Capacity">up to {{ $p->max_apartments }} units</td>
        <td data-label="Price / mo"><b>${{ number_format($p->price_monthly,2) }}</b></td>
        <td data-label="Active Owners">{{ $p->owners_count }}</td>
        <td data-label="MRR"><b>${{ number_format($p->owners_count * $p->price_monthly,0) }}</b></td>
        <td data-label="Edit">
          <form method="POST" action="{{ route('admin.plans.update',$p) }}" class="flex gap-2 items-center flex-wrap">
            @csrf @method('PUT')
            <input name="name" value="{{ $p->name }}" class="form-control input-narrow" title="Plan name" aria-label="Plan name">
            <input name="price_monthly" type="number" step="0.01" min="0" value="{{ $p->price_monthly }}" class="form-control input-narrow" title="Price / month" aria-label="Price per month">
            <input name="max_apartments" type="number" min="1" value="{{ $p->max_apartments }}" class="form-control input-narrow" title="Max apartments (cap)" aria-label="Max apartments">
            <label class="flex items-center gap-1 text-xs text-muted">
              <input type="checkbox" name="is_active" value="1" {{ $p->is_active?'checked':'' }}> Active
            </label>
            <button class="btn btn-primary btn-xs" type="submit">Save</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
 </div>
</div>
<p class="text-sm text-muted mt-3">Capacity is the enforced limit: an owner on a plan can hold up to and including the stated number of apartments. Only administrators can create or change plans.</p>

{{-- Hidden template for the Add Plan modal --}}
<template id="add-plan-tpl">
  <form method="POST" action="{{ route('admin.plans.store') }}">
    @csrf
    <div class="form-group"><label class="form-label">Plan Name</label>
      <input name="name" class="form-control" required placeholder="e.g. Enterprise"></div>
    <div class="form-group"><label class="form-label">Slug (optional)</label>
      <input name="slug" class="form-control" placeholder="auto-generated from name"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Price / month ($)</label>
        <input name="price_monthly" type="number" step="0.01" min="0" class="form-control" required placeholder="200"></div>
      <div class="form-group"><label class="form-label">Max Apartments (cap)</label>
        <input name="max_apartments" type="number" min="1" class="form-control" required placeholder="e.g. 300 for up to 300 units"></div>
    </div>
    <div class="form-actions">
      <button type="button" class="btn btn-outline" onclick="Modal.close()">Cancel</button>
      <button type="submit" class="btn btn-primary">Create Plan</button>
    </div>
  </form>
</template>
@endsection
