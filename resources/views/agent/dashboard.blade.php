@extends('layouts.agent')
@section('page-title','Agent Dashboard')
@section('content')

{{-- ── PRIMARY KPIs ── --}}
<div class="stats-grid cols-3">
  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Total Listings</span>
      <span class="stat-icon is-teal"><x-icon name="megaphone" /></span>
    </div>
    <div class="stat-value">{{ $stats['ads'] }}</div>
    <div class="stat-delta">
      {{ $stats['active'] }} active
      @if(($stats['ads'] - $stats['active']) > 0)
        · {{ $stats['ads'] - $stats['active'] }} inactive
      @endif
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Booking Requests</span>
      <span class="stat-icon is-gold"><x-icon name="inbox" /></span>
    </div>
    <div class="stat-value">{{ $stats['bookings'] }}</div>
    <div class="stat-delta">
      @if($stats['new_bookings'] > 0)
        <span class="badge badge-warning"><x-icon name="bell" />{{ $stats['new_bookings'] }} new</span>
      @else
        No new requests
      @endif
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-head">
      <span class="stat-label">Subscription Plan</span>
      <span class="stat-icon"><x-icon name="credit-card" /></span>
    </div>
    <div class="stat-value sm">{{ ucfirst($agent->subscription_plan) }}</div>
    <div class="stat-delta">
      ${{ number_format($agent->subscription_price, 2) }}/mo
      @if($agent->subscription_ends_at)
        · expires {{ $agent->subscription_ends_at->format('M j, Y') }}
      @endif
    </div>
  </div>
</div>

{{-- ── CARDS: Recent Bookings + Recent Listings ── --}}
<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <span class="card-title"><x-icon name="inbox" /> Recent Booking Requests</span>
    </div>
    @forelse($recentBookings as $b)
      <div class="list-row">
        <div class="list-row-body">
          <div class="list-row-title">{{ $b->name }}</div>
          <div class="list-row-sub">{{ $b->advertisement?->title ?? '—' }}</div>
        </div>
        <span class="badge badge-{{ $b->statusBadge() }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
      </div>
    @empty
      <div class="empty-state" style="padding:24px 0">
        <span class="empty-icon"><x-icon name="inbox" /></span>
        <span class="empty-title">No booking requests yet</span>
        <span class="empty-text">Inquiries from your listings will appear here.</span>
      </div>
    @endforelse
    @if($stats['bookings'] > 5)
      <a href="{{ route('agent.advertisements.index') }}#bookings" class="btn btn-outline btn-sm mt-3">View all requests</a>
    @endif
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title"><x-icon name="megaphone" /> My Recent Listings</span>
    </div>
    @forelse($recentAds as $ad)
      <div class="list-row">
        <div class="list-row-body">
          <div class="list-row-title">{{ $ad->title }}</div>
          <div class="list-row-sub">${{ number_format($ad->monthly_rent, 0) }}/mo · {{ $ad->bookings_count }} {{ $ad->bookings_count == 1 ? 'inquiry' : 'inquiries' }}</div>
        </div>
        <span class="badge badge-{{ $ad->statusBadge() }}">{{ ucfirst($ad->status) }}</span>
      </div>
    @empty
      <div class="empty-state" style="padding:24px 0">
        <span class="empty-icon"><x-icon name="megaphone" /></span>
        <span class="empty-title">No listings yet</span>
        <span class="empty-text">Create your first listing from the My Listings page.</span>
      </div>
    @endforelse
  </div>
</div>
@endsection
