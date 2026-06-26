@extends('layouts.owner')
@section('page-title','Settings')
@section('content')
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Account Settings</div>
    <form method="POST" action="{{ route('owner.settings.update') }}">
      @csrf @method('PUT')
      <div class="form-group"><label class="form-label">Company Name</label><input name="company_name" class="form-control" value="{{ $owner->company_name }}"></div>
      <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ $owner->phone }}"></div>
      <div class="form-group"><label class="form-label">Timezone</label>
        <select name="timezone" class="form-control">
          @foreach(timezone_identifiers_list() as $tz)<option value="{{ $tz }}" {{ $owner->timezone===$tz?'selected':'' }}>{{ $tz }}</option>@endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
  </div>
  <div class="card">
    <div class="card-title mb-4">Gmail SMTP Configuration</div>
    <div class="alert alert-info mb-4"><x-icon name="info" /><div class="alert-body">
      Use a Gmail App Password (not your account password).<br>
      Google Account &rsaquo; Security &rsaquo; 2-Step Verification &rsaquo; App Passwords
    </div></div>
    <form method="POST" action="{{ route('owner.settings.update') }}">
      @csrf @method('PUT')
      <div class="form-group"><label class="form-label">Gmail Address</label><input name="smtp_user" type="email" class="form-control" value="{{ $owner->smtp_user }}" placeholder="you@gmail.com"></div>
      <div class="form-group"><label class="form-label">App Password</label><input name="smtp_pass" type="password" class="form-control" placeholder="Leave blank to keep current"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">SMTP Host</label><input name="smtp_host" class="form-control" value="{{ $owner->smtp_host ?? 'smtp.gmail.com' }}"></div>
        <div class="form-group"><label class="form-label">Port</label><input name="smtp_port" type="number" class="form-control" value="{{ $owner->smtp_port ?? 587 }}"></div>
      </div>
      <button type="submit" class="btn btn-primary">Save Gmail Config</button>
      @if($owner->gmail_configured)<span class="badge badge-success ml-2"><x-icon name="check-circle" /> Gmail configured</span>@endif
    </form>
  </div>
</div>
@endsection
