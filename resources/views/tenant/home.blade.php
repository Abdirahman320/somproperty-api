@extends('layouts.tenant')
@section('page-title','My Home')
@section('content')
<div class="grid-2-1 mb-4">
  <div class="card">
    <div class="profile-head">
      <div class="avatar-lg"><x-icon name="user" /></div>
      <div>
        <div class="text-lg fw-600">{{ $tenant->full_name }}</div>
        <div class="text-md text-muted">Unit {{ $contract?->unit?->unit_number }}, {{ $contract?->unit?->property?->name }}</div>
      </div>
    </div>
    <div class="info-grid">
      @foreach([['Monthly Rent','$'.number_format($contract?->monthly_rent??0,2)],['Contract Start',$contract?->start_date?->format('M j, Y')??'—'],['Contract End',$contract?->end_date?->format('M j, Y')??'—'],['Security Deposit','$'.number_format($contract?->security_deposit??0,2)]] as [$label,$val])
      <div class="info-tile">
        <div class="info-tile-label">{{ $label }}</div>
        <div class="info-tile-value">{{ $val }}</div>
      </div>
      @endforeach
    </div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Quick Actions</div>
    <div class="action-list">
      <a href="{{ route('tenant.billing.index') }}" class="btn btn-primary"><x-icon name="wallet" /> View My Bills</a>
      <a href="{{ route('tenant.complaints.index') }}" class="btn btn-outline"><x-icon name="clipboard" /> My Complaints</a>
      <a href="{{ route('tenant.complaints.store') }}" class="btn btn-outline"><x-icon name="plus" /> Submit Complaint</a>
      <a href="{{ route('tenant.documents') }}" class="btn btn-outline"><x-icon name="file-text" /> Download Contract</a>
    </div>
  </div>
</div>
<div class="card-title mb-3">Recent Notifications</div>
<div class="notice-list">
@forelse($tenant->notifications()->with('notification')->orderByDesc('id')->take(5)->get() as $n)
<div class="card notice-card is-unread">
  <div class="fw-600 text-md mb-1">{{ $n->notification?->subject }}</div>
  <div class="text-sm text-muted">{{ $n->delivered_at?->diffForHumans() }}</div>
</div>
@empty
<div class="empty-state">
  <div class="empty-icon"><x-icon name="bell" /></div>
  <div class="empty-title">No notifications yet</div>
  <div class="empty-text">Updates from your property manager will appear here.</div>
</div>
@endforelse
</div>
@endsection
