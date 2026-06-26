@extends('layouts.admin')
@section('page-title','System Settings')
@section('content')
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Platform Settings</div>
    <form method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
      <div class="form-group"><label class="form-label">Platform Name</label><input class="form-control" name="app_name" value="SOM Property Management"></div>
      <div class="form-group"><label class="form-label">Support Email</label><input class="form-control" name="support_email" value="support@somproperty.com"></div>
      <div class="form-group"><label class="form-label">Default Currency</label>
        <select class="form-control" name="currency"><option>USD ($)</option><option>EUR (€)</option><option>GBP (£)</option></select>
      </div>
      <div class="form-group"><label class="form-label">Trial Days</label><input class="form-control" type="number" name="trial_days" value="14"></div>
      <button class="btn btn-primary" type="submit">Save Settings</button>
    </form>
  </div>
  <div class="card">
    <div class="card-title mb-4">System Info</div>
    <div class="flex flex-col text-md">
      <div class="kv-row"><span class="text-muted">PHP Version</span><b>{{ PHP_VERSION }}</b></div>
      <div class="kv-row"><span class="text-muted">Laravel Version</span><b>{{ app()->version() }}</b></div>
      <div class="kv-row"><span class="text-muted">Database</span><b>MySQL 8.0</b></div>
      <div class="kv-row"><span class="text-muted">Environment</span><b>{{ config('app.env') }}</b></div>
      <div class="kv-row"><span class="text-muted">Server Time</span><b>{{ now()->format('M j, Y H:i T') }}</b></div>
    </div>
  </div>
</div>
@endsection
