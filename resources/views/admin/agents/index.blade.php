@extends('layouts.admin')
@section('page-title','Property Agents')
@section('content')
<div class="card-header">
  <span class="card-title">Property Agents / Brokers ({{ $agents->total() }})</span>
  <a href="{{ route('admin.agents.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> Create Agent</a>
</div>

<div class="table-wrap table-stack">
  <div class="table-scroll">
  <table id="agentsTable">
    <thead>
      <tr>
        <th data-sort>Name</th>
        <th>Email</th>
        <th>City / Country</th>
        <th>Plan</th>
        <th>Price</th>
        <th>Subscription Ends</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($agents as $a)
      <tr>
        <td data-label="Name">
          <b>{{ $a->full_name }}</b>
          @if($a->company_name)<div class="text-xs text-muted">{{ $a->company_name }}</div>@endif
        </td>
        <td data-label="Email" class="text-sm">{{ $a->email }}</td>
        <td data-label="Location" class="text-sm">
          {{ $a->city ?? '—' }}@if($a->city && $a->country), @endif{{ $a->country ?? '' }}
        </td>
        <td data-label="Plan">
          <span class="badge badge-{{ $a->subscriptionBadge() }}">{{ ucfirst($a->subscription_plan) }}</span>
        </td>
        <td data-label="Price" class="text-sm">${{ number_format($a->subscription_price, 2) }}/mo</td>
        <td data-label="Expires" class="text-sm">
          @if($a->subscription_ends_at)
            @php $exp = $a->isSubscriptionActive(); @endphp
            <span class="badge badge-{{ $exp ? 'success' : 'danger' }}">
              {{ $a->subscription_ends_at->format('M j, Y') }}
            </span>
          @else —
          @endif
        </td>
        <td data-label="Status">
          <span class="badge badge-{{ $a->statusBadge() }}">
            <x-icon name="{{ $a->status==='active'?'check-circle':($a->status==='pending'?'clock':'alert') }}" />
            {{ ucfirst($a->status) }}
          </span>
        </td>
        <td data-label="Actions" class="cell-actions">
          <div>
            @if($a->status === 'active')
              <form method="POST" action="{{ route('admin.agents.suspend', $a) }}" style="display:contents">@csrf @method('PUT')
                <button class="btn btn-outline btn-xs">Suspend</button></form>
            @else
              <form method="POST" action="{{ route('admin.agents.activate', $a) }}" style="display:contents">@csrf @method('PUT')
                <button class="btn btn-gold btn-xs">Activate</button></form>
            @endif
            <form method="POST" action="{{ route('admin.agents.destroy', $a) }}" style="display:contents"
                  onsubmit="return confirm('Delete agent {{ addslashes($a->full_name) }}?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-xs">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="8">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="briefcase" /></div>
          <div class="empty-title">No agents yet</div>
          <div class="empty-text">Create property agents (brokers / Dulaal) who can advertise units and receive bookings.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
{{ $agents->links() }}
@endsection
@push('scripts')<script>initSortableTable('agentsTable');filterTable('globalSearch','agentsTable');</script>@endpush
