<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'My Portal') — SOM Property</title>
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
      <div class="sidebar-logo-icon"><x-icon name="home" /></div>
      <div class="sidebar-logo-text">My Portal<span>Tenant Dashboard</span></div>
      <button type="button" class="sidebar-close" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav>
      <div class="nav-section">Home</div>
      <a href="{{ route('tenant.home') }}" class="nav-item {{ request()->routeIs('tenant.home') ? 'active' : '' }}" @if(request()->routeIs('tenant.home')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="home" /></span> <span class="nav-label">My Home</span>
      </a>
      <a href="{{ route('tenant.billing.index') }}" class="nav-item {{ request()->routeIs('tenant.billing.*') ? 'active' : '' }}" @if(request()->routeIs('tenant.billing.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="wallet" /></span> <span class="nav-label">My Billings</span>
      </a>
      <a href="{{ route('tenant.complaints.index') }}" class="nav-item {{ request()->routeIs('tenant.complaints.*') ? 'active' : '' }}" @if(request()->routeIs('tenant.complaints.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="clipboard" /></span> <span class="nav-label">My Complaints</span>
      </a>
      <a href="{{ route('tenant.notifications.index') }}" class="nav-item {{ request()->routeIs('tenant.notifications.*') ? 'active' : '' }}" @if(request()->routeIs('tenant.notifications.*')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="bell" /></span> <span class="nav-label">Notifications</span>
        @if(($unreadCount ?? 0) > 0)
          <span class="nav-badge" aria-label="{{ $unreadCount }} unread">{{ $unreadCount }}</span>
        @endif
      </a>
      <a href="{{ route('tenant.documents') }}" class="nav-item {{ request()->routeIs('tenant.documents') ? 'active' : '' }}" @if(request()->routeIs('tenant.documents')) aria-current="page" @endif>
        <span class="nav-icon"><x-icon name="file-text" /></span> <span class="nav-label">Documents</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-pill">
        <div class="user-avatar is-primary">
          {{ strtoupper(substr(auth('tenant')->user()->full_name, 0, 2)) }}
        </div>
        <div class="user-info">
          <b>{{ auth('tenant')->user()->full_name }}</b>
          <span>Unit {{ auth('tenant')->user()->activeContract?->unit?->unit_number }}</span>
        </div>
      </div>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <button type="button" class="hamburger" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
        <x-icon name="menu" />
      </button>
      <h1 class="topbar-title">@yield('page-title', 'My Portal')</h1>
      <a href="{{ route('tenant.notifications.index') }}" class="icon-btn" aria-label="Notifications">
        <x-icon name="bell" />
      </a>
      <form method="POST" action="{{ route('tenant.logout') }}" class="d-inline">
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
