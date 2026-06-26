@extends('layouts.admin')
@section('page-title','Audit Logs')
@section('content')
<div class="card-header">
  <span class="card-title">Audit Logs</span>
</div>
<div class="table-wrap">
  <table>
    <thead><tr><th>Time</th><th>User Type</th><th>Action</th><th>Resource</th><th>IP</th></tr></thead>
    <tbody>
      @forelse($logs as $log)
      <tr>
        <td style="font-size:12px;color:var(--muted)">{{ $log->created_at->format('M j H:i') }}</td>
        <td><span class="badge badge-{{ $log->user_type==='admin'?'danger':($log->user_type==='owner'?'info':'success') }}">{{ ucfirst($log->user_type) }}</span></td>
        <td style="font-size:13px">{{ $log->action }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $log->resource_type }} #{{ $log->resource_id }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $log->ip_address }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="5" style="text-align:center;padding:48px;color:var(--muted)">
          <div style="font-size:32px;margin-bottom:10px">🔍</div>
          <div style="font-weight:600;margin-bottom:4px">No audit logs yet</div>
          <div style="font-size:12px">Actions performed in the system will appear here.</div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $logs->links() }}
@endsection
