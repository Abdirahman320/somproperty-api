@extends('layouts.tenant')
@section('page-title','Complaint Detail')
@section('content')
<div class="card-header">
  <span class="card-title">{{ $complaint->title }}</span>
  <a href="{{ route('tenant.complaints.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back</a>
</div>

<div class="card max-w-md">
  <div class="flex gap-2 items-center flex-wrap mb-2">
    <span class="badge badge-gray">{{ ucfirst($complaint->category) }}</span>
    @php $pr = $complaint->priority==='emergency'||$complaint->priority==='high'?'danger':($complaint->priority==='medium'?'warning':'gray'); @endphp
    <span class="badge badge-{{ $pr }}"><x-icon name="{{ $pr==='danger'?'alert':'info' }}" />{{ ucfirst($complaint->priority) }}</span>
    @php $st = $complaint->status==='open'?'danger':($complaint->status==='resolved'?'success':'warning'); $sti = $complaint->status==='open'?'alert':($complaint->status==='resolved'?'check-circle':'clock'); @endphp
    <span class="badge badge-{{ $st }}"><x-icon name="{{ $sti }}" />{{ ucfirst(str_replace('_',' ',$complaint->status)) }}</span>
  </div>
  <div class="text-xs text-muted mb-2">
    Unit {{ $complaint->unit?->unit_number ?? '—' }} · Submitted {{ $complaint->created_at->format('M j, Y g:i A') }}
  </div>
  <p class="text-md leading-relaxed">{{ $complaint->description }}</p>

  <div class="subhead">Conversation</div>
  <div class="thread">
  @forelse($complaint->replies as $r)
    <div class="bubble {{ $r->sender_type==='tenant' ? '' : 'is-staff' }}">
      <div class="bubble-meta">{{ $r->sender_type==='tenant' ? 'You' : 'Property Manager' }} · {{ $r->created_at->diffForHumans() }}</div>
      <div class="text-md">{{ $r->message }}</div>
    </div>
  @empty
    <p class="text-muted text-md">No replies yet. The property manager will respond soon.</p>
  @endforelse
  </div>

  <form method="POST" action="{{ route('tenant.complaints.reply', $complaint) }}" class="mt-3">
    @csrf
    <div class="form-group"><label class="form-label">Add a message</label>
      <textarea name="message" class="form-control" rows="3" required placeholder="Type your message…">{{ old('message') }}</textarea></div>
    <button type="submit" class="btn btn-primary"><x-icon name="arrow-right" /> Send</button>
  </form>
</div>
@endsection
