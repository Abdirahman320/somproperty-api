<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — SOM Property</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:20px;padding:40px;width:420px;box-shadow:0 20px 50px rgba(15,23,42,.35)}
.logo{display:flex;align-items:center;gap:12px;margin-bottom:32px}.logo-icon{width:48px;height:48px;background:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px}
.logo-text h1{font-size:17px;font-weight:600;color:#0f1f3d}.logo-text p{font-size:11px;color:#8895a7;letter-spacing:.5px}
h2{font-size:22px;font-weight:600;color:#0f1f3d;margin-bottom:6px}p.sub{font-size:13px;color:#8895a7;margin-bottom:28px}
.form-group{margin-bottom:16px}.form-group label{display:block;font-size:12px;font-weight:600;color:#6b7a8d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.form-group input{width:100%;padding:11px 14px;border:1px solid #e2e7ef;border-radius:10px;font-size:14px;font-family:inherit;outline:none;color:#0f1f3d;transition:border .15s}
.form-group input:focus{border-color:#2563eb}.alert{background:#fef2f2;color:#b91c1c;border-radius:10px;padding:12px;font-size:13px;margin-bottom:16px}
.btn{width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;transition:opacity .15s;margin-top:8px}
.btn:hover{background:#1d4ed8}.badge{text-align:center;margin-top:16px;font-size:11px;color:#8895a7;letter-spacing:.3px}.logo-icon svg{width:26px;height:26px}.btn{display:flex;align-items:center;justify-content:center;gap:8px}.btn-arrow{width:16px;height:16px}</style></head>
<body>
<div class="card">
  <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></div><div class="logo-text"><h1>SOM Admin Panel</h1><p>SYSTEM ADMINISTRATOR</p></div></div>
  <h2>Admin Login</h2>
  <p class="sub">Restricted access — authorised personnel only</p>
  @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('admin.login') }}">
    @csrf
    <div class="form-group"><label>Admin Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
    <button class="btn" type="submit">Access Admin Panel <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
  </form>
  <div class="badge">SOM Property Management v1.0</div>
</div></body></html>
