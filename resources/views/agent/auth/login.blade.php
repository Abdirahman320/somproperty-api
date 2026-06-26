<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Agent Login — SOM Property</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:20px;padding:40px;width:420px;box-shadow:0 20px 50px rgba(15,23,42,.25)}
.logo{display:flex;align-items:center;gap:12px;margin-bottom:32px}.logo-icon{width:48px;height:48px;background:#2563eb;border-radius:12px;display:flex;align-items:center;justify-content:center}
.logo-text h1{font-size:17px;font-weight:600;color:#0f1f3d}.logo-text p{font-size:11px;color:#8895a7;letter-spacing:.5px;text-transform:uppercase}
h2{font-size:22px;font-weight:600;color:#0f1f3d;margin-bottom:6px}p.sub{font-size:13px;color:#8895a7;margin-bottom:28px}
.form-group{margin-bottom:16px}.form-group label{display:block;font-size:12px;font-weight:600;color:#6b7a8d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.form-group input[type=email],.form-group input[type=password],.form-group input[type=text]{width:100%;padding:11px 14px;border:1px solid #e2e7ef;border-radius:10px;font-size:14px;font-family:inherit;outline:none;color:#0f1f3d;transition:border .15s}
.form-group input:focus{border-color:#2563eb;background:#fff}
.pw-wrap{position:relative;display:flex;align-items:center}.pw-wrap input{padding-right:42px;flex:1}
.pw-eye{position:absolute;right:10px;background:none;border:none;cursor:pointer;color:#64748b;display:flex;align-items:center;padding:4px;line-height:0}.pw-eye:hover{color:#0f172a}.pw-eye svg{width:18px;height:18px}
.remember{display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7a8d;cursor:pointer}
.btn{width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover{background:#1d4ed8}.btn-arrow{width:16px;height:16px}
.alert{background:#fef2f2;color:#b91c1c;border-radius:10px;padding:12px;font-size:13px;margin-bottom:16px}
.footer{text-align:center;margin-top:20px;font-size:12px;color:#8895a7}.footer a{color:#2563eb;text-decoration:none;font-weight:600}
</style></head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
    </div>
    <div class="logo-text"><h1>Agent Portal</h1><p>Property Broker</p></div>
  </div>

  <h2>Agent Login</h2>
  <p class="sub">Sign in to manage your listings</p>

  @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif

  <form method="POST" action="{{ route('agent.login') }}">
    @csrf
    <div class="form-group">
      <label for="email">Email Address</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="agent@example.com">
    </div>
    <div class="form-group">
      <label for="agt_pwd">Password</label>
      <div class="pw-wrap">
        <input id="agt_pwd" type="password" name="password" required placeholder="••••••••">
        <button type="button" class="pw-eye" onclick="togglePwd('agt_pwd',this)" tabindex="-1" aria-label="Show password">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="form-group">
      <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
    </div>
    <button class="btn" type="submit">
      Sign In
      <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </button>
  </form>

  <div class="footer">
    <a href="{{ route('owner.login') }}">Property Owner? Sign in here</a>
  </div>
</div>
<script>
function togglePwd(id,btn){var inp=document.getElementById(id),show=inp.type==='password';inp.type=show?'text':'password';btn.setAttribute('aria-label',show?'Hide password':'Show password');btn.querySelector('svg').innerHTML=show?'<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>':'<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>';}
</script>
</body>
</html>
