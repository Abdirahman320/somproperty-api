@extends('layouts.owner')
@section('page-title','Notifications')
@section('content')
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Send Notification</div>
    <form method="POST" action="{{ route('owner.notifications.send') }}">
      @csrf
      <div class="form-group"><label class="form-label">Recipients</label>
        <select name="recipients" class="form-control">
          <option value="all">All Tenants</option>
          <option value="overdue">Overdue Tenants Only</option>
          <option value="expiring">Expiring Contracts (30 days)</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Type</label>
        <select name="type" class="form-control">
          <option value="billing">Billing Reminder</option>
          <option value="overdue">Overdue Notice</option>
          <option value="maintenance">Maintenance Alert</option>
          <option value="announcement">General Announcement</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Channels</label>
        <div class="flex flex-col gap-2 mt-1">
          <label class="flex items-center gap-2 text-md"><input type="checkbox" name="channel_app" checked> <x-icon name="smartphone" class="icon-sm" /> In-App</label>
          <label class="flex items-center gap-2 text-md"><input type="checkbox" name="channel_email" checked> <x-icon name="mail" class="icon-sm" /> Gmail Email</label>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Subject</label><input name="subject" class="form-control" required placeholder="e.g. June 2026 Billing Statement Ready"></div>
      <div class="form-group"><label class="form-label">Message</label>
        <textarea name="message" class="form-control" rows="6" required placeholder="Dear [Tenant Name], your billing for..."></textarea>
      </div>
      <button type="submit" class="btn btn-gold btn-block"><x-icon name="rocket" /> Send Notifications</button>
    </form>
  </div>
  <div class="card">
    <div class="card-title mb-4">Sent History</div>
    @forelse($history ?? [] as $n)
    <div class="border-bottom-row">
      <div class="text-md fw-600">{{ $n->subject }}</div>
      <div class="text-xs text-muted">{{ ucfirst($n->channel) }} · {{ $n->sent_to_count }} recipients · {{ $n->sent_at?->diffForHumans() }}</div>
      <span class="badge badge-success mt-1"><x-icon name="check-circle" />{{ $n->opened_count }} opened</span>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-icon"><x-icon name="megaphone" /></div>
      <div class="empty-title">No notifications sent yet</div>
    </div>
    @endforelse
  </div>
</div>
@endsection
