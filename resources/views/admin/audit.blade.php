@extends('layouts.admin')
@section('page-title','Audit Logs')
@section('content')
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table>
    <thead><tr><th>Time</th><th>User Type</th><th>Action</th><th>Resource</th><th>IP</th></tr></thead>
    <tbody>
      @forelse($logs as $log)
      <tr>
        <td data-label="Time" class="text-sm text-muted">{{ $log->created_at->format('M j H:i') }}</td>
        <td data-label="User Type">@php $ut = $log->user_type==='admin'?'danger':($log->user_type==='owner'?'info':'success'); @endphp
          <span class="badge badge-{{ $ut }}"><x-icon name="user" />{{ ucfirst($log->user_type) }}</span></td>
        <td data-label="Action" class="text-md">{{ $log->action }}</td>
        <td data-label="Resource" class="text-sm text-muted">{{ $log->resource_type }} #{{ $log->resource_id }}</td>
        <td data-label="IP" class="text-sm text-muted">{{ $log->ip_address }}</td>
      </tr>
      @empty
      <tr class="table-empty"><td colspan="5">
        <div class="empty-state"><div class="empty-icon"><x-icon name="search" /></div><div class="empty-title">No audit entries</div></div>
      </td></tr>
      @endforelse
    </tbody>
  </table>
 </div>
</div>
{{ $logs->links() }}
@endsection
