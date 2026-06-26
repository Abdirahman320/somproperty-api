<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;background:#f7f8fc;font-family:Arial,sans-serif">
<div style="max-width:580px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e2e7ef">
  <div style="background:#0f1f3d;padding:28px 30px;text-align:center">
    <div style="color:#f0a500;font-size:22px;font-weight:700">{{ $owner->company_name ?? 'Property Management' }}</div>
    <div style="color:rgba(255,255,255,.65);font-size:13px;margin-top:4px">Welcome to your Tenant Portal</div>
  </div>
  <div style="padding:26px 30px">
    <p>Dear <b>{{ $tenant->full_name }}</b>,</p>
    <p style="margin-top:10px">Your tenant account has been created. You can now log in to view your bills, submit complaints, and track your rental history.</p>
    <div style="background:#f7f8fc;border-radius:12px;padding:18px;margin:20px 0">
      <div style="margin-bottom:8px"><b>📧 Login Email:</b> {{ $tenant->email }}</div>
      <div><b>🔑 Temporary Password:</b> <code style="background:#e2e7ef;padding:2px 8px;border-radius:4px;font-size:14px">{{ $password }}</code></div>
    </div>
    <div style="background:#fff8e6;border-left:4px solid #f0a500;padding:12px;border-radius:4px;margin-bottom:20px">
      ⚠️ Please change your password after your first login.
    </div>
    <div style="text-align:center">
      <a href="{{ url('/tenant/login') }}" style="background:#0f1f3d;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700">Access Your Tenant Portal →</a>
    </div>
  </div>
  <div style="background:#f8f9fa;padding:16px;text-align:center;font-size:11px;color:#8895a7">
    {{ $owner->company_name }} · Powered by SOM Property Management
  </div>
</div></body></html>
