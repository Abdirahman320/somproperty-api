@extends('layouts.tenant')
@section('page-title','My Documents')
@section('content')
<div class="card-header">
  <span class="card-title">Documents &amp; Contract</span>
</div>

<div class="card max-w-md">
  <div class="card-title mb-4">Lease Contract</div>
  @if($contract)
    <div class="flex flex-col gap-2 text-md mb-4">
      <div><span class="text-muted">Unit:</span> {{ $contract->unit?->unit_number }}, {{ $contract->unit?->property?->name }}</div>
      <div><span class="text-muted">Term:</span> {{ $contract->start_date?->format('M j, Y') }} &ndash; {{ $contract->end_date?->format('M j, Y') }}</div>
      <div><span class="text-muted">Monthly Rent:</span> ${{ number_format($contract->monthly_rent,2) }}</div>
      <div><span class="text-muted">Security Deposit:</span> ${{ number_format($contract->security_deposit,2) }}</div>
      <div><span class="text-muted">Status:</span>
        <span class="badge badge-{{ $contract->status==='active'?'success':'gray' }}">
          <x-icon name="{{ $contract->status==='active'?'check-circle':'info' }}" />{{ ucfirst($contract->status) }}</span></div>
    </div>
    @if($contract->terms_pdf_path)
      <a href="{{ asset('storage/'.$contract->terms_pdf_path) }}" class="btn btn-primary" target="_blank"><x-icon name="download" /> Download Signed Contract (PDF)</a>
    @else
      <div class="bg-soft rounded p-3 text-md text-muted">
        Your signed contract PDF has not been uploaded yet. Please contact your property manager for a copy.
      </div>
    @endif
  @else
    <div class="empty-state">
      <div class="empty-icon"><x-icon name="file-text" /></div>
      <div class="empty-title">No active contract on file</div>
      <div class="empty-text">Please contact your property manager.</div>
    </div>
  @endif
</div>
@endsection
