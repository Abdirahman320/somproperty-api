@extends('layouts.owner')
@section('page-title','Tenants')
@section('content')
<div class="card-header">
  <span class="card-title">Tenants ({{ $tenants->total() }})</span>
  <a href="{{ route('owner.tenants.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> Add Tenant</a>
</div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table id="tenantsTable">
    <thead><tr><th data-sort>Name</th><th>Email</th><th>Unit</th><th>Contract End</th><th>Monthly Rent</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($tenants as $t)
      <tr>
        <td data-label="Name"><b>{{ $t->full_name }}</b></td>
        <td data-label="Email" class="text-sm">{{ $t->email }}</td>
        <td data-label="Unit" class="text-sm">{{ $t->activeContract?->unit?->unit_number ?? '—' }}</td>
        <td data-label="Contract End" class="text-sm">{{ $t->activeContract?->end_date?->format('M j, Y') ?? '—' }}</td>
        <td data-label="Monthly Rent">${{ number_format($t->activeContract?->monthly_rent ?? 0, 2) }}</td>
        <td data-label="Status"><span class="badge badge-{{ $t->status==='active'?'success':'danger' }}"><x-icon name="{{ $t->status==='active'?'check-circle':'alert' }}" />{{ ucfirst($t->status) }}</span></td>
        <td data-label="Actions">
          <div class="cell-actions">
            <a href="{{ route('owner.tenants.show',$t) }}" class="btn btn-outline btn-xs">View</a>
            <form method="POST" action="{{ route('owner.billing.notify-all') }}" class="d-inline">@csrf
              <button class="btn btn-gold btn-xs"><x-icon name="mail" /> Bill</button></form>
          </div>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="7">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="users" /></div>
          <div class="empty-title">No tenants yet</div>
          <div class="empty-text"><a href="{{ route('owner.tenants.create') }}">Add your first tenant</a></div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
 </div>
</div>
{{ $tenants->links() }}
@endsection
@push('scripts')<script>initSortableTable('tenantsTable');filterTable('globalSearch','tenantsTable');</script>@endpush
