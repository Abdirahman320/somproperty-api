<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — SOM Property</title>
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

  {{-- SIDEBAR --}}
  <aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon"><x-icon name="building" /></div>
      <div class="sidebar-logo-text">SOM Property<span>Management</span></div>
      <button type="button" class="sidebar-close" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav>
      <div class="nav-section">Main</div>
      <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" @if(request()->routeIs('owner.dashboard')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="dashboard" /></span> <span class="nav-label">Dashboard</span>
      </a>
      <a href="{{ route('owner.properties.index') }}" class="nav-item {{ request()->routeIs('owner.properties.*') ? 'active' : '' }}" @if(request()->routeIs('owner.properties.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="building" /></span> <span class="nav-label">Properties &amp; Units</span>
      </a>
      <a href="{{ route('owner.tenants.index') }}" class="nav-item {{ request()->routeIs('owner.tenants.*') ? 'active' : '' }}" @if(request()->routeIs('owner.tenants.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="users" /></span> <span class="nav-label">Tenants</span>
      </a>
      <a href="{{ route('owner.advertisements.index') }}" class="nav-item {{ request()->routeIs('owner.advertisements.*') ? 'active' : '' }}" @if(request()->routeIs('owner.advertisements.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="megaphone" /></span> <span class="nav-label">Advertisements</span>
      </a>
      <a href="{{ route('owner.documents.index') }}" class="nav-item {{ request()->routeIs('owner.documents.*') ? 'active' : '' }}" @if(request()->routeIs('owner.documents.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="file-text" /></span> <span class="nav-label">Documents</span>
      </a>

      <div class="nav-section">Billing</div>
      <a href="{{ route('owner.billing.index') }}" class="nav-item {{ request()->routeIs('owner.billing.*') ? 'active' : '' }}" @if(request()->routeIs('owner.billing.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="wallet" /></span> <span class="nav-label">Billing Manager</span>
      </a>
      <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}" @if(request()->routeIs('owner.notifications.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="bell" /></span> <span class="nav-label">Notifications</span>
      </a>

      <div class="nav-section">Management</div>
      <a href="{{ route('owner.assets.index') }}" class="nav-item {{ request()->routeIs('owner.assets.*') ? 'active' : '' }}" @if(request()->routeIs('owner.assets.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="wrench" /></span> <span class="nav-label">Assets &amp; Issues</span>
      </a>
      <a href="{{ route('owner.complaints.index') }}" class="nav-item {{ request()->routeIs('owner.complaints.*') ? 'active' : '' }}" @if(request()->routeIs('owner.complaints.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="clipboard" /></span> <span class="nav-label">Complaints</span>
        @if(($openComplaints ?? 0) > 0)
          <span class="nav-badge green" aria-label="{{ $openComplaints }} open">{{ $openComplaints }}</span>
        @endif
      </a>
      <a href="{{ route('owner.reports.index') }}" class="nav-item {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}" @if(request()->routeIs('owner.reports.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="trending-up" /></span> <span class="nav-label">Reports</span>
      </a>

      <div class="nav-section">Account</div>
      <a href="{{ route('owner.backup.index') }}" class="nav-item {{ request()->routeIs('owner.backup.*') ? 'active' : '' }}" @if(request()->routeIs('owner.backup.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="download" /></span> <span class="nav-label">Backup &amp; Restore</span>
      </a>
      <a href="{{ route('owner.settings') }}" class="nav-item {{ request()->routeIs('owner.settings') ? 'active' : '' }}" @if(request()->routeIs('owner.settings')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="settings" /></span> <span class="nav-label">Settings</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="user-avatar is-teal">
          {{ strtoupper(substr(auth('owner')->user()->full_name, 0, 2)) }}
        </div>
        <div class="user-info">
          <b>{{ auth('owner')->user()->full_name }}</b>
          <span>{{ auth('owner')->user()->plan?->name ?? 'Free' }} plan</span>
        </div>
      </div>
    </div>
  </aside>

  {{-- MAIN --}}
  <div class="main-content">
    <header class="topbar">
      <button type="button" class="hamburger" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
        <x-icon name="menu" />
      </button>
      <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
      <div class="topbar-search-wrap">
        <x-icon name="search" />
        <label for="globalSearch" class="sr-only">Search</label>
        <input type="text" class="topbar-search" placeholder="Search…" id="globalSearch">
      </div>
      <a href="{{ route('owner.notifications.index') }}" class="icon-btn" aria-label="Notifications">
        <x-icon name="bell" /> <span class="notif-dot" aria-hidden="true"></span>
      </a>
      <form method="POST" action="{{ route('owner.logout') }}" class="d-inline">
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
      @if(session('new_creds'))
        @php $nc = session('new_creds'); @endphp
        <div style="background:#f0fdf4;border:1.5px solid #16a34a;border-radius:12px;padding:18px 20px;margin-bottom:16px">
          <div style="font-weight:700;color:#15803d;margin-bottom:10px;font-size:14px">&#10003; Login credentials for {{ $nc['name'] }}</div>
          <div style="font-size:13px;color:#374151;margin-bottom:6px">Email: <code style="background:#e5e7eb;padding:2px 6px;border-radius:4px">{{ $nc['email'] }}</code></div>
          <div style="font-size:13px;color:#374151;margin-bottom:12px">Password: <code id="genPwd" style="background:#e5e7eb;padding:2px 6px;border-radius:4px;user-select:all">{{ $nc['password'] }}</code></div>
          <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('genPwd').textContent.trim()).then(function(){this.textContent='Copied!';}.bind(this))" style="background:#16a34a;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:13px;cursor:pointer">Copy Password</button>
        </div>
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

      @yield('content')
    </main>
  </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
