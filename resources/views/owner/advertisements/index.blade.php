@extends('layouts.owner')
@section('page-title','Advertisements')
@section('content')
<div class="card-header">
  <span class="card-title">Advertise Vacant Units</span>
  <a href="{{ route('owner.advertisements.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> New Advertisement</a>
</div>

<div class="table-wrap table-stack mb-6">
  <div class="table-title">Your Listings ({{ $ads->count() }})</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Photo</th><th>Title</th><th>Unit</th><th>Rent</th><th>Inquiries</th><th>Status</th><th>Published</th><th></th></tr></thead>
    <tbody>
      @forelse($ads as $ad)
      <tr>
        <td data-label="Photo">
          @php $img = $ad->images->first(); @endphp
          @if($img)
            <img src="{{ Storage::disk('public')->url($img->image_path) }}" alt="listing" style="width:56px;height:40px;object-fit:cover;border-radius:6px;">
          @elseif($ad->image_path)
            <img src="{{ Storage::disk('public')->url($ad->image_path) }}" alt="listing" style="width:56px;height:40px;object-fit:cover;border-radius:6px;">
          @else
            <div style="width:56px;height:40px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#9ca3af;"><x-icon name="image" /></div>
          @endif
        </td>
        <td data-label="Title">
          <a href="{{ route('listings.show', $ad) }}" target="_blank" class="text-primary fw-600">{{ $ad->title }}</a>
          <div class="list-row-sub">{{ $ad->views_count }} view{{ $ad->views_count==1?'':'s' }}</div>
        </td>
        <td data-label="Unit">{{ $ad->unit?->unit_number ? '#'.$ad->unit->unit_number : '—' }}{{ $ad->unit?->property ? ', '.$ad->unit->property->name : '' }}</td>
        <td data-label="Rent">${{ number_format($ad->monthly_rent, 0) }}</td>
        <td data-label="Inquiries"><span class="badge badge-info"><x-icon name="inbox" />{{ $ad->bookings_count }}</span></td>
        <td data-label="Status"><span class="badge badge-{{ $ad->statusBadge() }}">{{ ucfirst($ad->status) }}</span></td>
        <td data-label="Published">
          <span class="badge badge-{{ $ad->is_published ? 'success' : 'gray' }}">
            <x-icon name="{{ $ad->is_published ? 'check-circle' : 'eye' }}" />{{ $ad->is_published ? 'Live' : 'Hidden' }}
          </span>
        </td>
        <td data-label="Actions" class="cell-actions">
          <button type="button" class="btn btn-outline btn-xs"
            onclick='Modal.open("Manage Advertisement", document.getElementById("ad-edit-{{ $ad->id }}").innerHTML)'>Manage</button>
          <div id="ad-edit-{{ $ad->id }}" class="d-none">
            <form method="POST" action="{{ route('owner.advertisements.update', $ad) }}">
              @csrf @method('PUT')
              <div class="form-group"><label class="form-label">Status</label>
                <select name="status" class="form-control">
                  @foreach(['available'=>'Available','reserved'=>'Reserved','rented'=>'Rented','closed'=>'Closed'] as $v=>$l)
                    <option value="{{ $v }}" {{ $ad->status===$v?'selected':'' }}>{{ $l }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label"><input type="checkbox" name="is_published" value="1" {{ $ad->is_published?'checked':'' }}> Published (visible on public home page)</label>
              </div>
              <div class="form-actions">
                <button class="btn btn-primary btn-sm">Save Changes</button>
              </div>
            </form>
            <form method="POST" action="{{ route('owner.advertisements.destroy', $ad) }}" class="mt-2"
                  onsubmit="return confirm('Delete this advertisement? This cannot be undone.')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-xs btn-block">Delete Advertisement</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="8">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="megaphone" /></div>
          <div class="empty-title">No advertisements yet</div>
          <div class="empty-text">Advertise a vacant unit so people can find and book it from the public home page.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Booking Requests &amp; Inquiries ({{ $bookings->count() }})</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Ref</th><th>Name</th><th>Contact</th><th>Listing</th><th>Move-in</th><th>Status</th><th></th></tr></thead>
    <tbody>
      @forelse($bookings as $b)
      <tr>
        <td data-label="Ref"><code>{{ $b->reference }}</code></td>
        <td data-label="Name">
          {{ $b->name }}
          @if($b->message)<div class="list-row-sub">{{ \Illuminate\Support\Str::limit($b->message, 60) }}</div>@endif
        </td>
        <td data-label="Contact">
          <div><a href="mailto:{{ $b->email }}" class="text-primary">{{ $b->email }}</a></div>
          @if($b->phone)<div class="list-row-sub">{{ $b->phone }}</div>@endif
        </td>
        <td data-label="Listing">{{ $b->advertisement?->title ?? '—' }}</td>
        <td data-label="Move-in">{{ $b->preferred_move_in?->format('M j, Y') ?? '—' }}</td>
        <td data-label="Status"><span class="badge badge-{{ $b->statusBadge() }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
        <td data-label="Update" class="cell-actions">
          <form method="POST" action="{{ route('owner.bookings.update', $b) }}" class="flex gap-2 items-center">
            @csrf @method('PUT')
            <select name="status" class="form-control select-inline" onchange="this.form.submit()">
              @foreach(['new'=>'New','contacted'=>'Contacted','viewing_scheduled'=>'Viewing scheduled','closed'=>'Closed','cancelled'=>'Cancelled'] as $v=>$l)
                <option value="{{ $v }}" {{ $b->status===$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          </form>
        </td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="7">
        <div class="empty-state">
          <div class="empty-icon"><x-icon name="inbox" /></div>
          <div class="empty-title">No booking requests yet</div>
          <div class="empty-text">Requests from the public home page will appear here.</div>
        </div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
</div>
@endsection
