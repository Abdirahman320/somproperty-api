<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — SOM Property Agent</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  @stack('styles')
</head>
<body>

<a href="#main" class="skip-link">Skip to main content</a>

<div class="sidebar-overlay"></div>

<div class="layout">

  {{-- SIDEBAR --}}
  <aside class="sidebar" id="sidebar" aria-label="Main navigation" style="--sidebar-accent:#7c3aed">
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon" style="background:#7c3aed"><x-icon name="building" /></div>
      <div class="sidebar-logo-text">SOM Property<span>Agent Portal</span></div>
      <button type="button" class="sidebar-close" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav>
      <div class="nav-section">Main</div>
      <a href="{{ route('agent.dashboard') }}" class="nav-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}" @if(request()->routeIs('agent.dashboard')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="dashboard" /></span> <span class="nav-label">Dashboard</span>
      </a>

      <div class="nav-section">Account</div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="user-avatar" style="background:#7c3aed">
          {{ strtoupper(substr(auth('agent')->user()->name, 0, 2)) }}
        </div>
        <div class="user-info">
          <b>{{ auth('agent')->user()->name }}</b>
          <span>Agent</span>
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
      <div class="topbar-search-wrap" style="flex:1"></div>
      <form method="POST" action="{{ route('agent.logout') }}" class="d-inline">
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

      @yield('content')
    </main>
  </div>
</div>

<script src="{{ asset('assets/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
