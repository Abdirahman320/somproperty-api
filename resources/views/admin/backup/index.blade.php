@extends('layouts.admin')
@section('page-title','Backup & Restore')
@section('content')
<div class="card-header">
  <span class="card-title">System Backup &amp; Restore</span>
</div>

<div class="grid-2 mb-4">
  {{-- EXPORT --}}
  <div class="card">
    <div class="card-title mb-2"><x-icon name="download" /> Download Full Backup</div>
    <p class="text-md text-muted mb-4">Exports <b>every table</b> in the system across all owners, tenants, billing, complaints, assets, advertisements and audit logs.</p>
    <div class="flex flex-col gap-2">
      <a href="{{ route('admin.backup.export', ['format'=>'excel']) }}" class="btn btn-primary"><x-icon name="file-text" /> Download as Excel (.xls)</a>
      <a href="{{ route('admin.backup.export', ['format'=>'csv']) }}" class="btn btn-outline"><x-icon name="file-text" /> Download as CSV</a>
      <a href="{{ route('admin.backup.export', ['format'=>'sql']) }}" class="btn btn-outline"><x-icon name="layers" /> Download as SQL</a>
    </div>
  </div>

  {{-- IMPORT --}}
  <div class="card">
    <div class="card-title mb-2"><x-icon name="refresh" /> Restore from a File</div>
    <p class="text-md text-muted mb-4">Upload a backup file (Excel <code>.xls</code>, <code>.csv</code> or <code>.sql</code>). Rows with a matching ID are updated; new rows are inserted. SQL files are executed as-is.</p>
    <form method="POST" action="{{ route('admin.backup.import') }}" enctype="multipart/form-data"
          onsubmit="return confirm('Restore data from this file? This can overwrite existing records system-wide.')">
      @csrf
      <div class="form-group">
        <label class="form-label">Backup File</label>
        <input type="file" name="file" class="form-control" required accept=".xls,.xml,.csv,.sql,.txt">
        <div class="form-hint">Max 50 MB. Foreign-key checks are disabled during the restore and re-enabled afterwards.</div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary"><x-icon name="refresh" /> Upload &amp; Restore</button>
      </div>
    </form>
  </div>
</div>

<div class="alert alert-warning" role="note">
  <x-icon name="alert" />
  <div class="alert-body">
    Restoring affects live data. Download a fresh backup before importing.
    Tables included: <b>{{ implode(', ', $tables) }}</b>.
  </div>
</div>
@endsection
