<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('page-title', 'Agent Portal') — SOM Property</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  @stack('styles')
</head>
<body>

<a href="#main" class="skip-link">Skip to main content</a>

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
      <div class="sidebar-logo-icon"><x-icon name="briefcase" /></div>
      <div class="sidebar-logo-text">Agent Portal<span>Property Broker</span></div>
      <button type="button" class="sidebar-close" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav>
      <div class="nav-section">Main</div>
      <a href="{{ route('agent.dashboard') }}" class="nav-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
        <span class="nav-icon"><x-icon name="dashboard" /></span> <span class="nav-label">Dashboard</span>
      </a>
      <a href="{{ route('agent.advertisements.index') }}" class="nav-item {{ request()->routeIs('agent.advertisements.*') ? 'active' : '' }}">
        <span class="nav-icon"><x-icon name="megaphone" /></span> <span class="nav-label">My Listings</span>
      </a>

      <div class="nav-section">Account</div>
      <a href="{{ route('agent.profile') }}" class="nav-item {{ request()->routeIs('agent.profile') ? 'active' : '' }}">
        <span class="nav-icon"><x-icon name="settings" /></span> <span class="nav-label">Profile &amp; Password</span>
      </a>
      <div class="nav-item" style="cursor:default;opacity:.6;">
        <span class="nav-icon"><x-icon name="credit-card" /></span>
        <span class="nav-label">
          {{ ucfirst(auth('agent')->user()->subscription_plan) }} plan
          @if(auth('agent')->user()->subscription_ends_at)
            <span class="badge badge-{{ auth('agent')->user()->isSubscriptionActive() ? 'success' : 'danger' }} ml-1" style="font-size:10px">
              {{ auth('agent')->user()->isSubscriptionActive() ? 'Active' : 'Expired' }}
            </span>
          @endif
        </span>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="user-avatar">{{ strtoupper(substr(auth('agent')->user()->full_name, 0, 2)) }}</div>
        <div class="user-info">
          <b>{{ auth('agent')->user()->full_name }}</b>
          <span>{{ auth('agent')->user()->city ?? 'Agent' }}</span>
        </div>
      </div>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <button type="button" class="hamburger" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
        <x-icon name="menu" />
      </button>
      <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
      <div class="topbar-search-wrap">
        <x-icon name="search" />
        <input type="text" class="topbar-search" placeholder="Search listings…" id="globalSearch">
      </div>
      <form method="POST" action="{{ route('agent.logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="icon-btn" aria-label="Log out"><x-icon name="log-out" /></button>
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
        <div class="alert alert-danger" role="alert"><x-icon name="alert" /><div class="alert-body"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
      @endif
      @yield('content')
    </main>
  </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
