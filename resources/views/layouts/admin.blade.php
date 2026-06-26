<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin') — SOM Property Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  @stack('styles')
</head>
<body>

<a href="#main" class="skip-link">Skip to main content</a>

{{-- MODAL --}}
<div id="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div id="modal-box" tabindex="-1">
    <div class="modal-head">
      <span id="modal-title" class="modal-title"></span>
      <button type="button" class="modal-close" onclick="Modal.close()" aria-label="Close dialog">
        <x-icon name="x" />
      </button>
    </div>
    <div id="modal-body"></div>
  </div>
</div>

<div class="sidebar-overlay"></div>

<div class="layout">
  <aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon"><x-icon name="building" /></div>
      <div class="sidebar-logo-text">SOM Admin<span>System Administrator</span></div>
      <button type="button" class="sidebar-close" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav>
      <div class="nav-section">Overview</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="dashboard" /></span> <span class="nav-label">Dashboard</span>
      </a>
      <a href="{{ route('admin.owners.index') }}" class="nav-item {{ request()->routeIs('admin.owners.*') ? 'active' : '' }}" @if(request()->routeIs('admin.owners.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="briefcase" /></span> <span class="nav-label">Property Owners</span>
      </a>
      <a href="{{ route('admin.agents.index') }}" class="nav-item {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}" @if(request()->routeIs('admin.agents.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="users" /></span> <span class="nav-label">Property Agents</span>
      </a>
      <a href="{{ route('admin.subscriptions') }}" class="nav-item {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}" @if(request()->routeIs('admin.subscriptions')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="credit-card" /></span> <span class="nav-label">Subscriptions</span>
      </a>
      <a href="{{ route('admin.user-locations') }}" class="nav-item {{ request()->routeIs('admin.user-locations') ? 'active' : '' }}" @if(request()->routeIs('admin.user-locations')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="map-pin" /></span> <span class="nav-label">User Locations</span>
      </a>

      <div class="nav-section">Marketing</div>
      <a href="{{ route('admin.advertisements.index') }}" class="nav-item {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}" @if(request()->routeIs('admin.advertisements.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="megaphone" /></span> <span class="nav-label">Advertisements</span>
      </a>
      <a href="{{ route('admin.ad-billing.index') }}" class="nav-item {{ request()->routeIs('admin.ad-billing.*') ? 'active' : '' }}" @if(request()->routeIs('admin.ad-billing.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="receipt" /></span> <span class="nav-label">Ad &amp; Report Billing</span>
      </a>

      <div class="nav-section">Analytics</div>
      <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" @if(request()->routeIs('admin.analytics')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="trending-up" /></span> <span class="nav-label">Analytics</span>
      </a>
      <a href="{{ route('admin.revenue') }}" class="nav-item {{ request()->routeIs('admin.revenue') ? 'active' : '' }}" @if(request()->routeIs('admin.revenue')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="wallet" /></span> <span class="nav-label">Revenue</span>
      </a>

      <div class="nav-section">System</div>
      <a href="{{ route('admin.plans.index') }}" class="nav-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}" @if(request()->routeIs('admin.plans.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="package" /></span> <span class="nav-label">Plans &amp; Pricing</span>
      </a>
      <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}" @if(request()->routeIs('admin.settings')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="settings" /></span> <span class="nav-label">System Settings</span>
      </a>
      <a href="{{ route('admin.audit') }}" class="nav-item {{ request()->routeIs('admin.audit') ? 'active' : '' }}" @if(request()->routeIs('admin.audit')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="search" /></span> <span class="nav-label">Audit Logs</span>
      </a>
      <a href="{{ route('admin.backup.index') }}" class="nav-item {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" @if(request()->routeIs('admin.backup.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="download" /></span> <span class="nav-label">Backup &amp; Restore</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="user-avatar is-gold">SA</div>
        <div class="user-info">
          <b>{{ auth('admin')->user()->name }}</b>
          <span>System Admin</span>
        </div>
      </div>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <button type="button" class="hamburger" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
        <x-icon name="menu" />
      </button>
      <h1 class="topbar-title">@yield('page-title', 'Admin Dashboard')</h1>
      <div class="topbar-search-wrap">
        <x-icon name="search" />
        <label for="globalSearch" class="sr-only">Search owners and plans</label>
        <input type="text" class="topbar-search" placeholder="Search owners, plans…" id="globalSearch">
      </div>
      <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="icon-btn" aria-label="Log out">
          <x-icon name="log-out" />
        </button>
      </form>
    </header>
    <main class="page-content" id="main">
      @if(session('success'))
        <div class="alert alert-success" role="status"><x-icon name="check-circle" /><div class="alert-body">{{ session('success') }}</div></div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger" role="alert"><x-icon name="alert" /><div class="alert-body">{{ session('error') }}</div></div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger" role="alert">
          <x-icon name="alert" />
          <div class="alert-body">
            <b>Please fix the following:</b>
            <ul>
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif
      @if(session('new_creds'))
        @php $nc = session('new_creds'); @endphp
        <div style="background:#f0fdf4;border:1.5px solid #16a34a;border-radius:12px;padding:18px 20px;margin-bottom:16px">
          <div style="font-weight:700;color:#15803d;margin-bottom:10px;font-size:14px">&#10003; Login credentials for {{ $nc['name'] }}</div>
          <div style="font-size:13px;color:#374151;margin-bottom:6px">Email: <code style="background:#e5e7eb;padding:2px 6px;border-radius:4px">{{ $nc['email'] }}</code></div>
          <div style="font-size:13px;color:#374151;margin-bottom:12px">Password: <code id="genPwd" style="background:#e5e7eb;padding:2px 6px;border-radius:4px;user-select:all">{{ $nc['password'] }}</code></div>
          <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('genPwd').textContent.trim()).then(function(){this.textContent='Copied!';}.bind(this))" style="background:#16a34a;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:13px;cursor:pointer">Copy Password</button>
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
