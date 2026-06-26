@extends('layouts.tenant')
@section('page-title','My Complaints')
@section('content')
<div class="card-header">
  <span class="card-title">My Complaints</span>
  <button class="btn btn-primary btn-sm" data-toggle="#newComplaintForm"><x-icon name="plus" /> New Complaint</button>
</div>
<div id="newComplaintForm" class="card mb-4 d-none">
  <div class="card-title mb-4">Submit New Complaint</div>
  <form method="POST" action="{{ route('tenant.complaints.store') }}">
    @csrf
    <div class="form-group"><label class="form-label">Title</label><input name="title" class="form-control" required placeholder="Brief description"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category</label>
        <select name="category" class="form-control"><option value="plumbing">Plumbing</option><option value="electrical">Electrical</option><option value="structural">Structural</option><option value="noise">Noise</option><option value="cleaning">Cleaning</option><option value="furniture">Furniture</option><option value="other">Other</option></select>
      </div>
      <div class="form-group"><label class="form-label">Priority</label>
        <select name="priority" class="form-control"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="emergency">Emergency</option></select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" required placeholder="Describe the issue in detail..."></textarea></div>
    <div class="form-actions">
      <button type="button" class="btn btn-outline" data-hide="#newComplaintForm">Cancel</button>
      <button type="submit" class="btn btn-primary">Submit Complaint</button>
    </div>
  </form>
</div>
<div class="notice-list">
@forelse($complaints as $c)
@php $st = $c->status==='open'?'danger':($c->status==='resolved'?'success':'warning'); $sti = $c->status==='open'?'alert':($c->status==='resolved'?'check-circle':'clock'); @endphp
<div class="card">
  <div class="flex items-center gap-2 flex-wrap mb-2">
    <b>{{ $c->title }}</b>
    <span class="badge badge-gray">{{ ucfirst($c->category) }}</span>
    <span class="badge badge-{{ $st }}"><x-icon name="{{ $sti }}" />{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
    <span class="text-xs text-muted nowrap ml-auto">{{ $c->ticket_number }}</span>
  </div>
  @if($c->replies->count())
  <div class="thread mt-2">
    @foreach($c->replies as $r)
    <div class="bubble {{ $r->sender_type==='owner'?'is-staff':'' }}">
      <div class="bubble-meta"><b class="text-default">{{ $r->sender_type==='owner'?'Management':'You' }}</b></div>
      {{ $r->message }}
    </div>
    @endforeach
  </div>
  @endif
  <div class="text-xs text-muted mt-2">{{ $c->created_at->format('M j, Y') }}</div>
</div>
@empty
<div class="empty-state">
  <div class="empty-icon"><x-icon name="clipboard" /></div>
  <div class="empty-title">No complaints submitted yet</div>
  <div class="empty-text">Use the button above to report an issue.</div>
</div>
@endforelse
</div>
@endsection
