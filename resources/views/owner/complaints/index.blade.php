@extends('layouts.owner')
@section('page-title','Complaints')
@section('content')
<div class="card-header">
  <span class="card-title">Complaints & Issues</span>
  <select class="form-control select-filter" onchange="filterByStatus(this.value)" aria-label="Filter by status">
    <option value="">All Status</option>
    <option value="open">Open</option>
    <option value="in_progress">In Progress</option>
    <option value="resolved">Resolved</option>
  </select>
</div>
<div class="notice-list">
@forelse($complaints as $c)
<div class="card">
  <div class="flex gap-3 items-start">
    <div class="icon-tile round"><x-icon name="clipboard" /></div>
    <div class="flex-1">
      <div class="flex items-center gap-2 flex-wrap mb-1">
        <b class="text-base">{{ $c->title }}</b>
        <span class="badge badge-gray">{{ ucfirst($c->category) }}</span>
        @php $pr = $c->priority==='emergency'||$c->priority==='high'?'danger':($c->priority==='medium'?'warning':'gray'); @endphp
        <span class="badge badge-{{ $pr }}"><x-icon name="{{ $pr==='danger'?'alert':'info' }}" />{{ ucfirst($c->priority) }}</span>
      </div>
      <div class="text-sm text-muted mb-2">{{ Str::limit($c->description,120) }}</div>
      <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs text-muted flex items-center gap-1"><x-icon name="user" class="icon-sm" /> {{ $c->tenant?->full_name ?? 'Unknown' }} · Unit {{ $c->unit?->unit_number ?? '—' }} · {{ $c->created_at->diffForHumans() }}</span>
        @php $st = $c->status==='open'?'danger':($c->status==='resolved'?'success':'warning'); $sti = $c->status==='open'?'alert':($c->status==='resolved'?'check-circle':'clock'); @endphp
        <span class="badge badge-{{ $st }}"><x-icon name="{{ $sti }}" />{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
        <a href="{{ route('owner.complaints.show',$c) }}" class="btn btn-outline btn-xs">View & Reply</a>
        <form method="POST" action="{{ route('owner.complaints.status',$c) }}" class="d-inline">@csrf @method('PUT')
          <select name="status" class="form-control select-inline" onchange="this.form.submit()" aria-label="Change status">
            @foreach(['open','assigned','in_progress','resolved','closed'] as $s)
              <option value="{{ $s }}" {{ $c->status===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
  </div>
</div>
@empty
<div class="empty-state">
  <div class="empty-icon"><x-icon name="clipboard" /></div>
  <div class="empty-title">No complaints yet</div>
</div>
@endforelse
</div>
{{ $complaints->links() }}
@endsection
