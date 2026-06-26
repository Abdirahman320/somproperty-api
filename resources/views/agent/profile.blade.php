@extends('layouts.agent')
@section('page-title','My Profile')
@section('content')
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Profile Details</div>
    <form method="POST" action="{{ route('agent.profile.update') }}">
      @csrf @method('PUT')
      <div class="form-group"><label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" required value="{{ $agent->full_name }}"></div>
      <div class="form-group"><label class="form-label">Company / Agency Name</label>
        <input name="company_name" class="form-control" value="{{ $agent->company_name }}" placeholder="Hassan Real Estate"></div>
      <div class="form-group"><label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="{{ $agent->phone }}" placeholder="+252 61 xxx xxxx"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">City</label>
          <input name="city" class="form-control" value="{{ $agent->city }}" placeholder="Mogadishu"></div>
        <div class="form-group"><label class="form-label">Country</label>
          <input name="country" class="form-control" value="{{ $agent->country }}" placeholder="Somalia"></div>
      </div>
      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>

  <div class="card">
    <div class="card-title mb-4"><x-icon name="lock" /> Change Password</div>
    <form method="POST" action="{{ route('agent.profile.password') }}" autocomplete="off">
      @csrf
      <div class="form-group">
        <label class="form-label">Current Password *</label>
        <div class="pw-wrap">
          <input id="a_cur" name="current_password" type="password" class="form-control"
                 required autocomplete="off" placeholder="Enter your current password">
          <button type="button" class="pw-eye" onclick="togglePwd('a_cur',this)" tabindex="-1" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
          </button>
        </div>
        @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">New Password *</label>
        <div class="pw-wrap">
          <input id="a_new" name="new_password" type="password" class="form-control"
                 required autocomplete="new-password" placeholder="Min 8 characters">
          <button type="button" class="pw-eye" onclick="togglePwd('a_new',this)" tabindex="-1" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
          </button>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm New Password *</label>
        <div class="pw-wrap">
          <input id="a_con" name="new_password_confirmation" type="password" class="form-control"
                 required autocomplete="new-password" placeholder="Repeat new password">
          <button type="button" class="pw-eye" onclick="togglePwd('a_con',this)" tabindex="-1" aria-label="Show password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Change Password</button>
    </form>

    <div class="divider mt-4"></div>
    <div class="card-subtitle mt-3">Subscription</div>
    <div class="flex flex-col gap-2 mt-3 text-sm">
      <div><span class="text-muted">Plan:</span>
        <span class="badge badge-{{ $agent->subscriptionBadge() }} ml-1">{{ ucfirst($agent->subscription_plan) }}</span>
      </div>
      <div><span class="text-muted">Price:</span> ${{ number_format($agent->subscription_price, 2) }}/month</div>
      <div><span class="text-muted">Expires:</span>
        @if($agent->subscription_ends_at)
          <span class="badge badge-{{ $agent->isSubscriptionActive() ? 'success' : 'danger' }}">
            {{ $agent->subscription_ends_at->format('M j, Y') }}
          </span>
        @else —
        @endif
      </div>
    </div>
  </div>
</div>
@push('styles')
<style>
.pw-wrap{position:relative;display:flex;align-items:center}
.pw-wrap .form-control{padding-right:42px;flex:1}
.pw-eye{position:absolute;right:10px;background:none;border:none;cursor:pointer;color:var(--slate);display:flex;align-items:center;padding:4px;border-radius:4px;line-height:0}
.pw-eye:hover{color:var(--text)}
.pw-eye svg{width:18px;height:18px}
</style>
@endpush

@push('scripts')
<script>
function togglePwd(id,btn){
  var inp=document.getElementById(id),show=inp.type==='password';
  inp.type=show?'text':'password';
  btn.setAttribute('aria-label',show?'Hide password':'Show password');
  btn.querySelector('svg').innerHTML=show
    ?'<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>'
    :'<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>';
}
</script>
@endpush
@endsection
