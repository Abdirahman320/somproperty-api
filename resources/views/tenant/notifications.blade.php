@extends('layouts.tenant')
@section('page-title','Notifications')
@section('content')
<div class="card-header">
  <span class="card-title">Notifications</span>
  @if($unreadCount > 0)<span class="badge badge-warning"><x-icon name="bell" /> {{ $unreadCount }} unread</span>@endif
</div>
<div class="notice-list">
@forelse($notifications as $n)
@php $icon = ['billing'=>'wallet','overdue'=>'alert','maintenance'=>'wrench','announcement'=>'megaphone','payment'=>'check-circle'][$n->notification?->type ?? ''] ?? 'bell'; @endphp
<div class="card notice-card {{ $n->is_read ? '' : 'is-unread' }}">
  <div class="flex items-start gap-3">
    <div class="icon-tile sm"><x-icon name="{{ $icon }}" /></div>
    <div class="flex-1">
      <div class="text-md mb-1 {{ $n->is_read ? 'fw-400' : 'fw-600' }}">{{ $n->notification?->subject ?? 'Notification' }}</div>
      <div class="text-sm text-muted mb-2">{{ Str::limit($n->notification?->message ?? '',150) }}</div>
      <div class="text-xs text-muted">{{ $n->delivered_at?->diffForHumans() }}</div>
    </div>
    @if(!$n->is_read)
    <form method="POST" action="{{ route('tenant.notifications.read',$n) }}">@csrf @method('PUT')
      <button class="btn btn-outline btn-xs"><x-icon name="check" /> Mark read</button></form>
    @endif
  </div>
</div>
@empty
<div class="empty-state">
  <div class="empty-icon"><x-icon name="bell" /></div>
  <div class="empty-title">No notifications yet</div>
  <div class="empty-text">You're all caught up.</div>
</div>
@endforelse
</div>
{{ $notifications->links() }}
@endsection
