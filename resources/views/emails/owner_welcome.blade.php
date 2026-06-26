<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;background:#f7f8fc;font-family:Arial,sans-serif">
<div style="max-width:580px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e2e7ef">
  <div style="background:#0f1f3d;padding:28px 30px;text-align:center">
    <div style="color:#f0a500;font-size:28px;font-weight:700">🏢</div>
    <div style="color:#f0a500;font-size:20px;font-weight:700;margin-top:8px">SOM Property Management</div>
    <div style="color:rgba(255,255,255,.65);font-size:13px;margin-top:4px">Your account is ready</div>
  </div>
  <div style="padding:26px 30px">
    <p>Dear <b>{{ $owner->full_name }}</b>,</p>
    <p style="margin-top:10px">Welcome to SOM Property Management! Your account has been created and you can start managing your properties right away.</p>
    <div style="background:#f7f8fc;border-radius:12px;padding:18px;margin:20px 0">
      <div style="margin-bottom:8px"><b>📧 Login Email:</b> {{ $owner->email }}</div>
      <div style="margin-bottom:8px"><b>🔑 Temporary Password:</b> <code style="background:#e2e7ef;padding:2px 8px;border-radius:4px;font-size:14px">{{ $password }}</code></div>
      <div><b>📦 Plan:</b> {{ $owner->plan->name }} ({{ $owner->max_apartments }} max apartments)</div>
    </div>
    <div style="background:#e6faf4;border-left:4px solid #22c993;padding:12px;border-radius:4px;margin-bottom:20px">
      ✓ You have a <b>14-day free trial</b>. No credit card required.
    </div>
    <div style="text-align:center">
      <a href="{{ url('/owner/login') }}" style="background:#f0a500;color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:700">Access Your Dashboard →</a>
    </div>
  </div>
  <div style="background:#f8f9fa;padding:16px;text-align:center;font-size:11px;color:#8895a7">
    SOM Property Management · support@somproperty.com
  </div>
</div></body></html>
