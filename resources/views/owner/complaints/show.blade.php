@extends('layouts.owner')
@section('page-title','Complaint Detail')
@section('content')
<div class="card-header">
  <span class="card-title">{{ $complaint->title }}</span>
  <a href="{{ route('owner.complaints.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Complaints</a>
</div>

<div class="grid-2-1">
  <div class="card">
    <div class="flex gap-2 items-center flex-wrap mb-2">
      <span class="badge badge-gray">{{ ucfirst($complaint->category) }}</span>
      @php $pr = $complaint->priority==='emergency'||$complaint->priority==='high'?'danger':($complaint->priority==='medium'?'warning':'gray'); @endphp
      <span class="badge badge-{{ $pr }}"><x-icon name="{{ $pr==='danger'?'alert':'info' }}" />{{ ucfirst($complaint->priority) }}</span>
      @php $st = $complaint->status==='open'?'danger':($complaint->status==='resolved'?'success':'warning'); $sti = $complaint->status==='open'?'alert':($complaint->status==='resolved'?'check-circle':'clock'); @endphp
      <span class="badge badge-{{ $st }}"><x-icon name="{{ $sti }}" />{{ ucfirst(str_replace('_',' ',$complaint->status)) }}</span>
    </div>
    <div class="text-xs text-muted mb-2 flex items-center gap-1">
      <x-icon name="user" class="icon-sm" /> {{ $complaint->tenant?->full_name ?? 'Unknown' }} · Unit {{ $complaint->unit?->unit_number ?? '—' }} · {{ $complaint->created_at->format('M j, Y g:i A') }}
    </div>
    <p class="text-md leading-relaxed">{{ $complaint->description }}</p>

    <div class="subhead">Conversation</div>
    <div class="thread">
    @forelse($complaint->replies as $r)
      <div class="bubble {{ $r->sender_type==='owner' ? 'is-staff' : '' }}">
        <div class="bubble-meta">{{ ucfirst($r->sender_type) }} · {{ $r->created_at->diffForHumans() }}</div>
        <div class="text-md">{{ $r->message }}</div>
      </div>
    @empty
      <p class="text-muted text-md">No replies yet.</p>
    @endforelse
    </div>

    <form method="POST" action="{{ route('owner.complaints.reply', $complaint) }}" class="mt-3">
      @csrf
      <div class="form-group"><label class="form-label">Reply to Tenant</label>
        <textarea name="message" class="form-control" rows="3" required placeholder="Type your reply…">{{ old('message') }}</textarea></div>
      <button type="submit" class="btn btn-primary"><x-icon name="arrow-right" /> Send Reply</button>
    </form>
  </div>

  <div class="card">
    <div class="card-title mb-4">Update Status</div>
    <form method="POST" action="{{ route('owner.complaints.status', $complaint) }}">
      @csrf @method('PUT')
      <div class="form-group"><label class="form-label">Status</label>
        <select name="status" class="form-control">
          @foreach(['open','assigned','in_progress','resolved','closed','rejected'] as $s)
            <option value="{{ $s }}" {{ $complaint->status===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Assigned To</label>
        <input name="assigned_to" class="form-control" value="{{ old('assigned_to', $complaint->assigned_to) }}" placeholder="Staff / contractor"></div>
      <div class="form-group"><label class="form-label">Resolution Notes</label>
        <textarea name="resolution_notes" class="form-control" rows="3">{{ old('resolution_notes', $complaint->resolution_notes) }}</textarea></div>
      <button type="submit" class="btn btn-gold btn-block">Update</button>
    </form>
  </div>
</div>
@endsection
