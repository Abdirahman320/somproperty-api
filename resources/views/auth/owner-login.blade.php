<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Owner Login — SOM Property</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:20px;padding:40px;width:420px;box-shadow:0 20px 50px rgba(15,23,42,.25)}
.logo{display:flex;align-items:center;gap:12px;margin-bottom:32px}.logo-icon{width:48px;height:48px;background:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px}
.logo-text h1{font-size:17px;font-weight:600;color:#0f1f3d}.logo-text p{font-size:11px;color:#8895a7;letter-spacing:.5px}
h2{font-size:22px;font-weight:600;color:#0f1f3d;margin-bottom:6px}p.sub{font-size:13px;color:#8895a7;margin-bottom:28px}
.form-group{margin-bottom:16px}.form-group label{display:block;font-size:12px;font-weight:600;color:#6b7a8d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.form-group input{width:100%;padding:11px 14px;border:1px solid #e2e7ef;border-radius:10px;font-size:14px;font-family:inherit;outline:none;color:#0f1f3d;transition:border .15s}
.form-group input:focus{border-color:#2563eb;background:#fff}
.error{color:#dc2626;font-size:12px;margin-top:4px}
.btn{width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;transition:opacity .15s;margin-top:8px}
.btn:hover{background:#1d4ed8}.alert{background:#fef2f2;color:#b91c1c;border-radius:10px;padding:12px;font-size:13px;margin-bottom:16px}
.footer{text-align:center;margin-top:20px;font-size:12px;color:#8895a7}
.footer a{color:#2563eb;text-decoration:none;font-weight:600}.logo-icon svg{width:26px;height:26px}.btn{display:flex;align-items:center;justify-content:center;gap:8px}.btn-arrow{width:16px;height:16px}</style></head>
<body>
<div class="card">
  <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/></svg></div><div class="logo-text"><h1>SOM Property</h1><p>MANAGEMENT</p></div></div>
  <h2>Property Owner Login</h2>
  <p class="sub">Sign in to manage your properties</p>
  @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('owner.login') }}">
    @csrf
    <div class="form-group"><label>Email Address</label><input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="owner@company.com"></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required placeholder="••••••••"></div>
    <button class="btn" type="submit">Sign In <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
  </form>
  <div class="footer"><a href="{{ route('tenant.login') }}">Tenant? Sign in here</a></div>
</div></body></html>
