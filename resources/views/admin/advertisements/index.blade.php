@extends('layouts.admin')
@section('page-title','Advertisements')
@section('content')

<div class="card-header">
  <span class="card-title">Advertisement Management</span>
  <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> New Advertisement</a>
</div>

<div class="stats-grid mb-5">
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Total Ads</span><span class="stat-icon"><x-icon name="megaphone" /></span></div><div class="stat-value">{{ $stats['total'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Published</span><span class="stat-icon"><x-icon name="check-circle" /></span></div><div class="stat-value">{{ $stats['published'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Available</span><span class="stat-icon"><x-icon name="home" /></span></div><div class="stat-value">{{ $stats['available'] }}</div></div>
  <div class="stat-card"><div class="stat-card-head"><span class="stat-label">Total Inquiries</span><span class="stat-icon"><x-icon name="inbox" /></span></div><div class="stat-value">{{ $stats['bookings'] }}</div></div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">All Advertisements</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Title</th><th>Owner</th><th>Rent</th><th>By</th><th>Inquiries</th><th>Published</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($ads as $ad)
      <tr>
        <td data-label="Title">
          <a href="{{ route('listings.show', $ad) }}" target="_blank" class="text-primary fw-600">{{ $ad->title }}</a>
          <div class="list-row-sub">{{ $ad->unit?->unit_number ? '#'.$ad->unit->unit_number.' · ' : '' }}{{ $ad->city ?? '—' }} · {{ $ad->views_count }} views</div>
        </td>
        <td data-label="Owner">{{ $ad->owner?->full_name ?? '—' }}</td>
        <td data-label="Rent">${{ number_format($ad->monthly_rent, 0) }}</td>
        <td data-label="By"><span class="badge badge-{{ $ad->created_by_type==='admin'?'info':'gray' }}">{{ ucfirst($ad->created_by_type) }}</span></td>
        <td data-label="Inquiries">{{ $ad->bookings_count }}</td>
        <td data-label="Published">
          <span class="badge badge-{{ $ad->is_published ? 'success' : 'gray' }}">{{ $ad->is_published ? 'Live' : 'Hidden' }}</span>
        </td>
        <td data-label="Actions" class="cell-actions">
          <div>
            <form method="POST" action="{{ route('admin.advertisements.update', $ad) }}" style="display:contents">
              @csrf @method('PUT')
              <select name="status" class="form-control select-inline" onchange="this.form.submit()" aria-label="Change status">
                @foreach(['available'=>'Available','reserved'=>'Reserved','rented'=>'Rented','closed'=>'Closed'] as $v=>$l)
                  <option value="{{ $v }}" {{ $ad->status===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </form>
            <form method="POST" action="{{ route('admin.advertisements.destroy', $ad) }}" style="display:contents"
                  onsubmit="return confirm('Delete this advertisement?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-xs">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="7">
        <div class="empty-state"><div class="empty-icon"><x-icon name="megaphone" /></div>
          <div class="empty-title">No advertisements</div></div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<div class="mt-4">{{ $ads->links() }}</div>
@endsection
