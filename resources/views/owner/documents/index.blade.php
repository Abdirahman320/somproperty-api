@extends('layouts.owner')
@section('page-title','Tenant Documents')
@section('content')
<div class="card-header">
  <span class="card-title">All Tenant Documents</span>
</div>

@if($stats['expired'] || $stats['expiring'])
<div class="alert {{ $stats['expired'] ? 'alert-danger' : 'alert-warning' }}" role="note">
  <x-icon name="alert" />
  <div class="alert-body">
    @if($stats['expired'])<b>{{ $stats['expired'] }}</b> document{{ $stats['expired']==1?'':'s' }} expired @endif
    @if($stats['expired'] && $stats['expiring']) · @endif
    @if($stats['expiring'])<b>{{ $stats['expiring'] }}</b> expiring within 30 days @endif.
    Review and request renewals from each tenant's page.
  </div>
</div>
@endif

<div class="grid-3 mb-4">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Total Documents</span><span class="stat-icon"><x-icon name="file-text" /></span></div><div class="stat-value">{{ $stats['total'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Expiring Soon</span><span class="stat-icon"><x-icon name="clock" /></span></div><div class="stat-value text-warning">{{ $stats['expiring'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Expired</span><span class="stat-icon"><x-icon name="alert" /></span></div><div class="stat-value text-danger">{{ $stats['expired'] }}</div></div>
</div>

<div class="filter-bar mb-3">
  <form method="GET" action="{{ route('owner.documents.index') }}" class="flex gap-2 items-end flex-wrap">
    <div class="form-group mb-0">
      <label class="form-label">Type</label>
      <select name="doc_type" class="form-control select-inline">
        <option value="">All types</option>
        @foreach(['passport'=>'Passport','police_certificate'=>'Police Certificate','national_id'=>'ID Card / National ID','visa'=>'Visa','residence_permit'=>'Residence Permit','employment_letter'=>'Employment Letter','bank_statement'=>'Bank Statement','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}" @selected(request('doc_type')===$v)>{{ $l }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group mb-0">
      <label class="form-label">Status</label>
      <select name="filter" class="form-control select-inline">
        <option value="">All</option>
        <option value="expiring" @selected(request('filter')==='expiring')>Expiring soon</option>
        <option value="expired" @selected(request('filter')==='expired')>Expired</option>
      </select>
    </div>
    <button class="btn btn-outline btn-sm"><x-icon name="search" /> Filter</button>
    @if(request('doc_type') || request('filter'))
      <a href="{{ route('owner.documents.index') }}" class="btn btn-ghost btn-sm">Clear</a>
    @endif
  </form>
</div>

<div class="table-wrap table-stack">
  <div class="table-scroll">
  <table>
    <thead><tr><th>Tenant</th><th>Type</th><th>Label</th><th>Uploaded</th><th>Expiry</th><th></th></tr></thead>
    <tbody>
      @forelse($documents as $doc)
      <tr>
        <td data-label="Tenant">
          @if($doc->tenant)
            <a href="{{ route('owner.tenants.show', $doc->tenant) }}" class="text-primary fw-600">{{ $doc->tenant->full_name }}</a>
          @else <span class="text-muted">—</span> @endif
        </td>
        <td data-label="Type"><span class="badge badge-gray"><x-icon name="file-text" />{{ $doc->typeLabel() }}</span></td>
        <td data-label="Label">{{ $doc->label ?? $doc->original_name ?? '—' }}</td>
        <td data-label="Uploaded">{{ $doc->created_at?->format('M j, Y') }}</td>
        <td data-label="Expiry">
          @php $eb = $doc->expiryBadge(); @endphp
          @if($eb)<span class="badge badge-{{ $eb['class'] }}"><x-icon name="{{ $eb['icon'] }}" />{{ $eb['text'] }}</span>
          @else <span class="text-muted">No expiry</span> @endif
        </td>
        <td data-label="Actions" class="cell-actions">
          <a href="{{ route('owner.documents.download', $doc) }}" class="btn btn-outline btn-xs"><x-icon name="download" /> Download</a>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="6">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="file-text" /></div>
          <div class="empty-title">No documents found</div>
          <div class="empty-text">Upload tenant documents (ID, passport, police clearance, etc.) from a tenant's page.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<div class="mt-4">{{ $documents->links() }}</div>
@endsection
