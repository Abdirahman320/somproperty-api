@extends('layouts.owner')
@section('page-title','Assets & Issues')
@section('content')
<div class="card-header">
  <span class="card-title">Asset Register & Technical Issues</span>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('owner.assets.create') }}" class="btn btn-outline btn-sm"><x-icon name="plus" /> Register Asset</a>
    <a href="{{ route('owner.assets.issues.create') }}" class="btn btn-danger btn-sm"><x-icon name="alert" /> Log Issue</a>
  </div>
</div>
<div class="grid-2">
  <div class="table-wrap">
    <div class="table-title">Fixed Assets ({{ $assets->count() }})</div>
    @forelse($assets as $a)
    @php $ci = ['mechanical'=>'settings','electrical'=>'zap','plumbing'=>'wrench','electronic'=>'radio','furniture'=>'sofa'][$a->category] ?? 'package'; @endphp
    <div class="list-row">
      <div class="icon-tile"><x-icon name="{{ $ci }}" /></div>
      <div class="list-row-body">
        <div class="list-row-title">{{ $a->name }}</div>
        <div class="list-row-sub">{{ $a->property->name }} · {{ $a->location }}</div>
      </div>
      @php $as = $a->status==='operational'?'success':($a->status==='under_repair'?'danger':'warning'); $asi = $a->status==='operational'?'check-circle':($a->status==='under_repair'?'wrench':'alert'); @endphp
      <span class="badge badge-{{ $as }}"><x-icon name="{{ $asi }}" />{{ ucfirst(str_replace('_',' ',$a->status)) }}</span>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-icon"><x-icon name="package" /></div>
      <div class="empty-title">No assets registered</div>
    </div>
    @endforelse
  </div>
  <div class="table-wrap">
    <div class="table-title">Open Technical Issues ({{ $issues->count() }})</div>
    @forelse($issues as $i)
    <div class="list-row top">
      <div class="icon-tile sm"><x-icon name="hammer" /></div>
      <div class="list-row-body">
        <div class="list-row-title">{{ $i->title }}</div>
        <div class="list-row-sub">Assigned: {{ $i->assigned_to ?? 'Unassigned' }}</div>
      </div>
      @php $ps = $i->priority==='critical'?'danger':($i->priority==='high'?'warning':'gray'); @endphp
      <span class="badge badge-{{ $ps }}"><x-icon name="{{ $ps==='gray'?'info':'alert' }}" />{{ ucfirst($i->priority) }}</span>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-icon"><x-icon name="check-circle" /></div>
      <div class="empty-title">No open issues</div>
    </div>
    @endforelse
  </div>
</div>
@endsection
