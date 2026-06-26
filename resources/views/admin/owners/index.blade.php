@extends('layouts.admin')
@section('page-title','Property Owners')
@section('content')
<div class="card-header">
  <span class="card-title">All Owners ({{ $owners->total() }})</span>
  <a href="{{ route('admin.owners.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> Create Owner</a>
</div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table id="ownersTable">
    <thead><tr><th data-sort>Owner</th><th>Email</th><th>Plan</th><th>Max Apts</th><th>Used</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($owners as $o)
      <tr>
        <td data-label="Owner"><b>{{ $o->full_name }}</b><div class="text-xs text-muted">{{ $o->company_name }}</div></td>
        <td data-label="Email" class="text-sm">{{ $o->email }}</td>
        <td data-label="Plan"><span class="badge badge-info"><x-icon name="package" />{{ $o->plan?->name ?? '—' }}</span></td>
        <td data-label="Max Apts" class="text-center">{{ $o->max_apartments }}</td>
        <td data-label="Used" class="text-center">{{ $o->usedApartments() }}</td>
        <td data-label="Status">@php $os = $o->status==='active'?'success':($o->status==='trial'?'warning':'danger'); $osi = $o->status==='active'?'check-circle':($o->status==='trial'?'clock':'alert'); @endphp
          <span class="badge badge-{{ $os }}"><x-icon name="{{ $osi }}" />{{ ucfirst($o->status) }}</span></td>
        <td data-label="Joined" class="text-sm text-muted">{{ $o->created_at->format('M j, Y') }}</td>
        <td data-label="Actions">
          @if($o->status==='active')
            <form method="POST" action="{{ route('admin.owners.suspend',$o) }}" class="d-inline">@csrf @method('PUT')
              <button class="btn btn-outline btn-xs">Suspend</button></form>
          @else
            <form method="POST" action="{{ route('admin.owners.activate',$o) }}" class="d-inline">@csrf @method('PUT')
              <button class="btn btn-gold btn-xs">Activate</button></form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
 </div>
</div>
{{ $owners->links() }}
@endsection
@push('scripts')<script>initSortableTable('ownersTable');filterTable('globalSearch','ownersTable');</script>@endpush
