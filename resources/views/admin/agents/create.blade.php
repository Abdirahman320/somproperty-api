@extends('layouts.admin')
@section('page-title','Create Agent')
@section('content')
<div class="card-header">
  <span class="card-title">Create New Agent / Broker</span>
  <a href="{{ route('admin.agents.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" /> Back to Agents</a>
</div>

<div class="card max-w-md">
  <div class="alert alert-info mb-4"><x-icon name="info" /><div class="alert-body">
    Property Agents (Dulaal) can advertise units and receive booking inquiries. They pay a monthly subscription of $15 (Basic) or $20 (Pro).
  </div></div>

  <form method="POST" action="{{ route('admin.agents.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group"><label class="form-label">Full Name *</label>
        <input name="full_name" class="form-control" required value="{{ old('full_name') }}" placeholder="Ahmed Hassan"></div>
      <div class="form-group"><label class="form-label">Company / Agency Name *</label>
        <input name="company_name" class="form-control" required value="{{ old('company_name') }}" placeholder="Hassan Real Estate"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Email *</label>
        <input name="email" type="email" class="form-control" required value="{{ old('email') }}" placeholder="agent@example.com"></div>
      <div class="form-group"><label class="form-label">Phone *</label>
        <input name="phone" class="form-control" required value="{{ old('phone') }}" placeholder="+252 61 xxx xxxx"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">City *</label>
        <input name="city" class="form-control" required value="{{ old('city') }}" placeholder="Mogadishu"></div>
      <div class="form-group"><label class="form-label">Country *</label>
        <input name="country" class="form-control" required value="{{ old('country') }}" placeholder="Somalia"></div>
    </div>

    <div class="divider"></div>
    <div class="card-subtitle mb-3"><x-icon name="credit-card" /> Subscription</div>

    <div class="form-row">
      <div class="form-group"><label class="form-label">Plan *</label>
        <select name="subscription_plan" class="form-control" required id="planSelect">
          <option value="basic" {{ old('subscription_plan','basic')==='basic'?'selected':'' }}>Basic — $15/month</option>
          <option value="pro"   {{ old('subscription_plan')==='pro'?'selected':'' }}>Pro — $20/month</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Monthly Price ($) *</label>
        <input name="subscription_price" id="subPrice" type="number" step="0.01" min="0" class="form-control" required value="{{ old('subscription_price', 15) }}"></div>
    </div>
    <div class="form-group"><label class="form-label">Subscription Expires *</label>
      <input name="subscription_ends_at" type="date" class="form-control" required
             value="{{ old('subscription_ends_at', now()->addMonth()->toDateString()) }}">
      <div class="form-hint">Set when this agent's subscription expires. Renew by editing and updating this date.</div>
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.agents.index') }}" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Create Agent</button>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('planSelect').addEventListener('change', function(){
  document.getElementById('subPrice').value = this.value === 'pro' ? 20 : 15;
});
</script>
@endpush
