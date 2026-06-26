<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SOM Property — Find Your Next Home</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f4f6fb;color:#0f1f3d;line-height:1.5}
a{text-decoration:none;color:inherit}
.wrap{max-width:1140px;margin:0 auto;padding:0 20px}
/* header */
.site-header{background:#fff;border-bottom:1px solid #e7ebf3;position:sticky;top:0;z-index:50}
.site-header .wrap{display:flex;align-items:center;gap:20px;height:68px}
.brand{display:flex;align-items:center;gap:11px}
.brand-icon{width:42px;height:42px;background:#2563eb;border-radius:11px;display:flex;align-items:center;justify-content:center}
.brand-icon svg{width:23px;height:23px}
.brand-text h1{font-size:16px;font-weight:700}
.brand-text p{font-size:10px;color:#8895a7;letter-spacing:1px}
.nav{margin-left:auto;display:flex;align-items:center;gap:8px}
.nav a.navlink{padding:9px 14px;border-radius:9px;font-size:14px;font-weight:500;color:#475569}
.nav a.navlink:hover{background:#f1f5fb;color:#2563eb}
.dropdown{position:relative}
.dropdown-btn{padding:9px 16px;border-radius:9px;font-size:14px;font-weight:600;background:#2563eb;color:#fff;border:none;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px}
.dropdown-btn:hover{background:#1d4ed8}
.dropdown-menu{position:absolute;right:0;top:calc(100% + 8px);background:#fff;border:1px solid #e7ebf3;border-radius:12px;box-shadow:0 12px 32px rgba(15,23,42,.14);min-width:200px;padding:8px;display:none}
.dropdown.open .dropdown-menu{display:block}
.dropdown-menu a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:14px;color:#334155}
.dropdown-menu a:hover{background:#f1f5fb;color:#2563eb}
.dropdown-menu svg{width:17px;height:17px;color:#94a3b8}
/* hero */
.hero{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);color:#fff;padding:54px 0 90px}
.hero h2{font-size:34px;font-weight:700;margin-bottom:12px;letter-spacing:-.5px}
.hero p{font-size:16px;color:#cbd5e1;max-width:560px}
.hero .pill{display:inline-flex;align-items:center;gap:7px;background:rgba(37,99,235,.18);border:1px solid rgba(96,165,250,.35);color:#bfdbfe;padding:6px 13px;border-radius:999px;font-size:12px;font-weight:600;margin-bottom:18px}
.hero .pill svg{width:14px;height:14px}
/* search bar */
.search-card{background:#fff;border-radius:16px;box-shadow:0 16px 40px rgba(15,23,42,.16);padding:18px;margin-top:-52px;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end}
.field label{display:block;font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.field input,.field select{width:100%;padding:11px 13px;border:1px solid #e2e7ef;border-radius:10px;font-size:14px;font-family:inherit;color:#0f1f3d;outline:none;background:#fff}
.field input:focus,.field select:focus{border-color:#2563eb}
.search-btn{padding:12px 22px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;height:43px;display:flex;align-items:center;gap:7px}
.search-btn:hover{background:#1d4ed8}
.search-btn svg{width:16px;height:16px}
/* listings */
.section{padding:38px 0 60px}
.section-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:8px}
.section-head h3{font-size:21px;font-weight:700}
.section-head span{font-size:14px;color:#8895a7}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.listing{background:#fff;border-radius:15px;overflow:hidden;border:1px solid #eef1f6;transition:transform .15s,box-shadow .15s;display:flex;flex-direction:column}
.listing:hover{transform:translateY(-3px);box-shadow:0 14px 30px rgba(15,23,42,.12)}
.listing-img{height:178px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);position:relative;display:flex;align-items:center;justify-content:center}
.listing-img img{width:100%;height:100%;object-fit:cover}
.listing-img .ph{color:#60a5fa}
.listing-img .ph svg{width:54px;height:54px}
.listing-badge{position:absolute;top:12px;left:12px;background:rgba(255,255,255,.95);color:#15803d;font-size:11px;font-weight:700;padding:5px 11px;border-radius:999px;text-transform:uppercase;letter-spacing:.4px}
.listing-badge.reserved{color:#b45309}
.listing-body{padding:17px 17px 19px;display:flex;flex-direction:column;flex:1}
.listing-rent{font-size:20px;font-weight:700;color:#2563eb}
.listing-rent span{font-size:12px;color:#8895a7;font-weight:500}
.listing-title{font-size:15px;font-weight:600;margin:6px 0 4px}
.listing-loc{font-size:13px;color:#8895a7;display:flex;align-items:center;gap:5px}
.listing-loc svg{width:14px;height:14px}
.listing-meta{display:flex;gap:14px;margin:13px 0 16px;font-size:13px;color:#475569}
.listing-meta span{display:flex;align-items:center;gap:5px}
.listing-meta svg{width:15px;height:15px;color:#94a3b8}
.listing-cta{margin-top:auto;display:block;text-align:center;padding:11px;background:#eff4ff;color:#2563eb;border-radius:10px;font-size:14px;font-weight:600}
.listing-cta:hover{background:#2563eb;color:#fff}
/* empty */
.empty{background:#fff;border-radius:15px;border:1px dashed #d4dbe6;padding:60px 20px;text-align:center;color:#8895a7}
.empty svg{width:46px;height:46px;color:#cbd5e1;margin-bottom:12px}
.empty h4{font-size:16px;color:#475569;margin-bottom:4px}
/* pagination */
.pagination{display:flex;gap:6px;justify-content:center;margin-top:30px;list-style:none;flex-wrap:wrap}
.pagination a,.pagination span{padding:8px 13px;border-radius:8px;font-size:13px;background:#fff;border:1px solid #e7ebf3;color:#475569}
.pagination .active span{background:#2563eb;color:#fff;border-color:#2563eb}
/* alert */
.alert-ok{background:#f0fdf4;border:1px solid #16a34a;color:#15803d;border-radius:12px;padding:14px 18px;font-size:14px;font-weight:500;margin:20px 0}
/* footer */
.site-footer{background:#0f172a;color:#94a3b8;padding:30px 0;font-size:13px;text-align:center}
.site-footer a{color:#93c5fd;font-weight:600}
@media(max-width:860px){.grid{grid-template-columns:1fr 1fr}.search-card{grid-template-columns:1fr 1fr}.hero h2{font-size:26px}}
@media(max-width:560px){.grid{grid-template-columns:1fr}.search-card{grid-template-columns:1fr}.topbar-search{display:none}.nav a.navlink{display:none}}
</style>
</head>
<body>

<header class="site-header">
  <div class="wrap">
    <a href="{{ route('home') }}" class="brand">
      <span class="brand-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/></svg></span>
      <span class="brand-text"><h1>SOM Property</h1><p>MANAGEMENT</p></span>
    </a>
    <nav class="nav">
      <a class="navlink" href="#listings">Advertisements</a>
      <div class="dropdown" id="loginDropdown">
        <button class="dropdown-btn" type="button" onclick="document.getElementById('loginDropdown').classList.toggle('open')">
          Login
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="dropdown-menu">
          <a href="{{ route('owner.login') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/></svg> Property Owner</a>
          <a href="{{ route('tenant.login') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Tenant</a>
          <a href="{{ route('admin.login') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> System Admin</a>
        </div>
      </div>
    </nav>
  </div>
</header>

<section class="hero">
  <div class="wrap">
    <span class="pill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg> No account &amp; no payment needed to enquire</span>
    <h2>Find your next home</h2>
    <p>Browse vacant rooms, units and apartments. Book a viewing and contact the owner directly — completely free.</p>
  </div>
</section>

<div class="wrap">
  <form method="GET" action="{{ route('home') }}" class="search-card">
    <div class="field">
      <label>City</label>
      <input type="text" name="city" value="{{ request('city') }}" placeholder="Any city" list="cityList">
      <datalist id="cityList">@foreach($cities as $c)<option value="{{ $c }}">@endforeach</datalist>
    </div>
    <div class="field">
      <label>Bedrooms</label>
      <select name="bedrooms">
        <option value="">Any</option>
        @foreach(['studio'=>'Studio','1br'=>'1 Bedroom','2br'=>'2 Bedrooms','3br'=>'3 Bedrooms','4br+'=>'4+ Bedrooms'] as $v=>$lbl)
          <option value="{{ $v }}" @selected(request('bedrooms')===$v)>{{ $lbl }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label>Max Rent</label>
      <input type="number" name="max_rent" value="{{ request('max_rent') }}" placeholder="No limit" min="0" step="50">
    </div>
    <button class="search-btn" type="submit">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Search
    </button>
  </form>
</div>

<section class="section" id="listings">
  <div class="wrap">
    @if(session('booked'))
      <div class="alert-ok">{{ session('success') }}</div>
    @endif

    <div class="section-head">
      <h3>Available Listings</h3>
      <span>{{ $ads->total() }} {{ \Illuminate\Support\Str::plural('property', $ads->total()) }} available</span>
    </div>

    @if($ads->count())
      <div class="grid">
        @foreach($ads as $ad)
          <a href="{{ route('listings.show', $ad) }}" class="listing">
            <div class="listing-img">
              @if($ad->image_path)
                <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}">
              @else
                <span class="ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/></svg></span>
              @endif
              <span class="listing-badge {{ $ad->status==='reserved'?'reserved':'' }}">{{ ucfirst($ad->status) }}</span>
            </div>
            <div class="listing-body">
              <div class="listing-rent">${{ number_format($ad->monthly_rent, 0) }} <span>/ month</span></div>
              <div class="listing-title">{{ $ad->title }}</div>
              <div class="listing-loc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $ad->city ?? $ad->address ?? 'Location on request' }}
              </div>
              <div class="listing-meta">
                @if($ad->bedrooms)<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8V6a2 2 0 0 1 2-2h8"/></svg> {{ strtoupper($ad->bedrooms) }}</span>@endif
                @if($ad->bathrooms)<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M7 12V5a2 2 0 0 1 2-2h1"/></svg> {{ $ad->bathrooms }} bath</span>@endif
                @if($ad->area_sqft)<span>{{ number_format($ad->area_sqft) }} sqft</span>@endif
              </div>
              <span class="listing-cta">View &amp; Book</span>
            </div>
          </a>
        @endforeach
      </div>
      <div style="margin-top:30px">{{ $ads->links() }}</div>
    @else
      <div class="empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/></svg>
        <h4>No listings match your search</h4>
        <p>Try adjusting your filters or check back soon.</p>
      </div>
    @endif
  </div>
</section>

<footer class="site-footer">
  <div class="wrap">
    &copy; {{ date('Y') }} SOM Property Management. &nbsp;·&nbsp;
    <a href="{{ route('owner.login') }}">Owner</a> &nbsp;·&nbsp;
    <a href="{{ route('tenant.login') }}">Tenant</a> &nbsp;·&nbsp;
    <a href="{{ route('admin.login') }}">Admin</a>
  </div>
</footer>

<script>
document.addEventListener('click', function(e){
  var dd = document.getElementById('loginDropdown');
  if (dd && !dd.contains(e.target)) dd.classList.remove('open');
});
</script>
</body>
</html>
