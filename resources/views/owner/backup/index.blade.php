@extends('layouts.owner')
@section('page-title','Backup & Restore')
@section('content')
<div class="card-header">
  <span class="card-title">Backup &amp; Restore My Data</span>
</div>

<div class="grid-2 mb-4">
  {{-- EXPORT --}}
  <div class="card">
    <div class="card-title mb-2"><x-icon name="download" /> Download a Backup</div>
    <p class="text-md text-muted mb-4">Exports all of your data (properties, units, tenants, contracts, billing, complaints, assets, advertisements and more) tied to your account.</p>
    <div class="flex flex-col gap-2">
      <a href="{{ route('owner.backup.export', ['format'=>'excel']) }}" class="btn btn-primary"><x-icon name="file-text" /> Download as Excel (.xls)</a>
      <a href="{{ route('owner.backup.export', ['format'=>'csv']) }}" class="btn btn-outline"><x-icon name="file-text" /> Download as CSV</a>
      <a href="{{ route('owner.backup.export', ['format'=>'sql']) }}" class="btn btn-outline"><x-icon name="layers" /> Download as SQL</a>
    </div>
  </div>

  {{-- IMPORT --}}
  <div class="card">
    <div class="card-title mb-2"><x-icon name="refresh" /> Restore from a File</div>
    <p class="text-md text-muted mb-4">Upload a backup file (Excel <code>.xls</code>, <code>.csv</code> or <code>.sql</code>) that you previously downloaded. Existing rows with the same ID are updated; new rows are added. All imported data is attached to your account.</p>
    <form method="POST" action="{{ route('owner.backup.import') }}" enctype="multipart/form-data"
          onsubmit="return confirm('Restore data from this file? Matching records will be overwritten.')">
      @csrf
      <div class="form-group">
        <label class="form-label">Backup File</label>
        <input type="file" name="file" class="form-control" required accept=".xls,.xml,.csv,.sql,.txt">
        <div class="form-hint">Max 50 MB. SQL restores only accept files exported from your own account.</div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary"><x-icon name="refresh" /> Upload &amp; Restore</button>
      </div>
    </form>
  </div>
</div>

<div class="alert alert-info" role="note">
  <x-icon name="info" />
  <div class="alert-body">
    A backup includes these tables: <b>{{ implode(', ', $tables) }}</b>.
    Keep your backup files private — they contain personal tenant information.
  </div>
</div>
@endsection
