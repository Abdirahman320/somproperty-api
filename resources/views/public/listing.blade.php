<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $advertisement->title }} — SOM Property</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f4f6fb;color:#0f1f3d;line-height:1.55}
a{text-decoration:none;color:inherit}
.wrap{max-width:1080px;margin:0 auto;padding:0 20px}
.site-header{background:#fff;border-bottom:1px solid #e7ebf3;position:sticky;top:0;z-index:50}
.site-header .wrap{display:flex;align-items:center;gap:20px;height:64px}
.brand{display:flex;align-items:center;gap:11px}
.brand-icon{width:40px;height:40px;background:#2563eb;border-radius:11px;display:flex;align-items:center;justify-content:center}
.brand-icon svg{width:22px;height:22px}
.brand-text h1{font-size:15px;font-weight:700}
.brand-text p{font-size:10px;color:#8895a7;letter-spacing:1px}
.back{margin-left:auto;font-size:14px;color:#2563eb;font-weight:600;display:flex;align-items:center;gap:6px}
.back svg{width:16px;height:16px}
.main{padding:30px 0 60px;display:grid;grid-template-columns:1.6fr 1fr;gap:28px;align-items:start}
.cover{height:340px;border-radius:16px;overflow:hidden;background:linear-gradient(135deg,#dbeafe,#bfdbfe);display:flex;align-items:center;justify-content:center;margin-bottom:22px}
.cover img{width:100%;height:100%;object-fit:cover}
.cover svg{width:80px;height:80px;color:#60a5fa}
.title-row{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap}
h2{font-size:25px;font-weight:700;margin-bottom:6px}
.loc{font-size:14px;color:#8895a7;display:flex;align-items:center;gap:6px}
.loc svg{width:16px;height:16px}
.rent{font-size:26px;font-weight:700;color:#2563eb;white-space:nowrap}
.rent span{font-size:13px;color:#8895a7;font-weight:500}
.badge{display:inline-block;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;text-transform:uppercase;letter-spacing:.4px;background:#dcfce7;color:#15803d;margin-top:10px}
.badge.reserved{background:#fef3c7;color:#b45309}
.facts{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:24px 0}
.fact{background:#fff;border:1px solid #eef1f6;border-radius:12px;padding:15px}
.fact .k{font-size:11px;text-transform:uppercase;letter-spacing:.4px;color:#8895a7;font-weight:600;margin-bottom:5px}
.fact .v{font-size:16px;font-weight:600}
.card{background:#fff;border:1px solid #eef1f6;border-radius:16px;padding:24px}
.card h3{font-size:16px;font-weight:700;margin-bottom:12px}
.desc{font-size:15px;color:#374151;white-space:pre-line}
/* contact + booking */
.aside{position:sticky;top:84px;display:flex;flex-direction:column;gap:20px}
.contact .row{display:flex;align-items:center;gap:11px;font-size:14px;margin-bottom:11px}
.contact .row svg{width:18px;height:18px;color:#2563eb;flex-shrink:0}
.contact .row b{font-weight:600}
.contact .row a{color:#2563eb;font-weight:600}
.field{margin-bottom:13px}
.field label{display:block;font-size:12px;font-weight:600;color:#6b7a8d;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px}
.field input,.field textarea{width:100%;padding:11px 13px;border:1px solid #e2e7ef;border-radius:10px;font-size:14px;font-family:inherit;color:#0f1f3d;outline:none}
.field input:focus,.field textarea:focus{border-color:#2563eb}
.field textarea{resize:vertical;min-height:80px}
.btn{width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover{background:#1d4ed8}
.btn svg{width:17px;height:17px}
.note{font-size:12px;color:#8895a7;text-align:center;margin-top:10px}
.alert-ok{background:#f0fdf4;border:1px solid #16a34a;color:#15803d;border-radius:12px;padding:14px 16px;font-size:14px;font-weight:500;margin-bottom:16px}
.alert-err{background:#fef2f2;border:1px solid #ef4444;color:#b91c1c;border-radius:12px;padding:12px 16px;font-size:13px;margin-bottom:14px}
.alert-err ul{margin:4px 0 0 18px}
@media(max-width:840px){.main{grid-template-columns:1fr}.aside{position:static}.facts{grid-template-columns:1fr 1fr}.cover{height:240px}}
</style>
</head>
<body>

<header class="site-header">
  <div class="wrap">
    <a href="{{ route('home') }}" class="brand">
      <span class="brand-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/></svg></span>
      <span class="brand-text"><h1>SOM Property</h1><p>MANAGEMENT</p></span>
    </a>
    <a href="{{ route('home') }}" class="back"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Back to listings</a>
  </div>
</header>

<div class="wrap">
  <div class="main">
    {{-- LEFT --}}
    <div>
      <div class="cover">
        @if($advertisement->image_path)
          <img src="{{ asset('storage/'.$advertisement->image_path) }}" alt="{{ $advertisement->title }}">
        @else
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M16 6h.01M8 10h.01M16 10h.01M8 14h.01M16 14h.01"/></svg>
        @endif
      </div>

      <div class="title-row">
        <div>
          <h2>{{ $advertisement->title }}</h2>
          <div class="loc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ $advertisement->address ?? $advertisement->city ?? 'Location available on request' }}</div>
          <span class="badge {{ $advertisement->status==='reserved'?'reserved':'' }}">{{ ucfirst($advertisement->status) }}</span>
        </div>
        <div class="rent">${{ number_format($advertisement->monthly_rent, 0) }} <span>/ month</span></div>
      </div>

      <div class="facts">
        <div class="fact"><div class="k">Bedrooms</div><div class="v">{{ $advertisement->bedrooms ? strtoupper($advertisement->bedrooms) : '—' }}</div></div>
        <div class="fact"><div class="k">Bathrooms</div><div class="v">{{ $advertisement->bathrooms ?? '—' }}</div></div>
        <div class="fact"><div class="k">Area</div><div class="v">{{ $advertisement->area_sqft ? number_format($advertisement->area_sqft).' sqft' : '—' }}</div></div>
      </div>

      @if($advertisement->description)
        <div class="card" style="margin-bottom:20px">
          <h3>About this property</h3>
          <div class="desc">{{ $advertisement->description }}</div>
        </div>
      @endif
    </div>

    {{-- RIGHT --}}
    <div class="aside">
      <div class="card contact">
        <h3>Contact the owner</h3>
        @if($advertisement->contact_name)
          <div class="row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><b>{{ $advertisement->contact_name }}</b></div>
        @endif
        @if($advertisement->contact_phone)
          <div class="row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg><a href="tel:{{ $advertisement->contact_phone }}">{{ $advertisement->contact_phone }}</a></div>
        @endif
        @if($advertisement->contact_email)
          <div class="row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg><a href="mailto:{{ $advertisement->contact_email }}">{{ $advertisement->contact_email }}</a></div>
        @endif
      </div>

      <div class="card">
        <h3>Book a viewing</h3>
        @if(session('booked'))
          <div class="alert-ok">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert-err"><b>Please check:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('listings.book', $advertisement) }}">
          @csrf
          <div class="field"><label>Your Name</label><input type="text" name="name" value="{{ old('name') }}" required></div>
          <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="field"><label>Phone</label><input type="text" name="phone" value="{{ old('phone') }}" placeholder="Optional"></div>
          <div class="field"><label>Preferred Move-in</label><input type="date" name="preferred_move_in" value="{{ old('preferred_move_in') }}"></div>
          <div class="field"><label>Message</label><textarea name="message" placeholder="Tell the owner a little about your enquiry…">{{ old('message') }}</textarea></div>
          <button class="btn" type="submit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
            Send Booking Request
          </button>
          <div class="note">Free — no account or payment required.</div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
