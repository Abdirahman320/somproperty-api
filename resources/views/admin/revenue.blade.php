@extends('layouts.admin')
@section('page-title','Revenue')
@section('content')
<div class="stats-grid">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Current MRR</span><span class="stat-icon is-teal"><x-icon name="wallet" /></span></div><div class="stat-value">${{ number_format($mrr,0) }}</div><div class="stat-delta">Monthly Recurring Revenue</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Annual ARR</span><span class="stat-icon is-gold"><x-icon name="trending-up" /></span></div><div class="stat-value">${{ number_format($mrr*12,0) }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Active Plans</span><span class="stat-icon"><x-icon name="package" /></span></div><div class="stat-value">{{ $plans->count() }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">To Reach $20k/mo</span><span class="stat-icon {{ $mrr>=20000?'is-success':'' }}"><x-icon name="{{ $mrr>=20000?'check-circle':'rocket' }}" /></span></div>
    <div class="stat-value">{{ $mrr>=20000?'Done':'$'.number_format(20000-$mrr,0) }}</div>
    <div class="stat-delta {{ $mrr>=20000?'pos':'neg' }}">{{ $mrr>=20000?'Target reached!':'still needed' }}</div>
  </div>
</div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table>
    <thead><tr><th>Plan</th><th>Price</th><th>Active Owners</th><th>MRR Contribution</th><th>% of Total</th></tr></thead>
    <tbody>
      @foreach($plans as $p)
      @php $pct = $mrr>0 ? round($p->active*$p->price_monthly/$mrr*100,1) : 0; @endphp
      <tr>
        <td data-label="Plan"><b>{{ $p->name }}</b></td>
        <td data-label="Price">${{ number_format($p->price_monthly,0) }}/mo</td>
        <td data-label="Active Owners">{{ $p->active }}</td>
        <td data-label="MRR Contribution"><b>${{ number_format($p->active*$p->price_monthly,0) }}</b></td>
        <td data-label="% of Total">{{ $pct }}%
          <div class="progress mt-1"><div class="progress-bar is-gold" style="width:{{ $pct }}%"></div></div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
 </div>
</div>
@endsection
