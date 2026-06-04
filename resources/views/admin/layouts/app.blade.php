<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — pcfy Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<style>
/* ── Reset & base ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{font-size:14px;-webkit-font-smoothing:antialiased;}
body{font-family:'Inter',sans-serif;background:#f0f2f5;color:#111827;min-height:100vh;}
a{color:inherit;text-decoration:none;}
button{font-family:inherit;cursor:pointer;}
input,select,textarea{font-family:inherit;}

/* ── Design tokens ── */
:root{
  --navy:#0f4c8a; --navy-dark:#08305a; --navy-xdark:#051e3a;
  --accent:#f5a623; --accent-dark:#d48b1a; --accent-ink:#1a1000;
  --green:#16a34a; --red:#dc2626; --orange:#ea580c;
  --bg:#f0f2f5; --surface:#fff; --border:#e5e7eb; --border-dark:#d1d5db;
  --text:#111827; --muted:#6b7280; --muted-light:#9ca3af;
  --sidebar-w:220px; --header-h:50px;
  --r:6px; --r-sm:4px;
}

/* ── Layout ── */
.admin-wrap{display:flex;min-height:100vh;}

/* ── Sidebar ── */
.sidebar{width:var(--sidebar-w);background:var(--navy-xdark);display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;z-index:200;transition:transform .25s;}
.sidebar-logo{padding:14px 16px 12px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:10px;}
.sidebar-logo .mark{font-family:'DM Mono',monospace;font-size:18px;font-weight:500;color:#fff;letter-spacing:-.5px;}
.sidebar-logo .mark span{color:var(--accent);}
.sidebar-logo .badge{font-size:9px;font-weight:700;background:var(--accent);color:var(--accent-ink);padding:2px 6px;border-radius:3px;letter-spacing:.04em;text-transform:uppercase;}

.nav-section{padding:10px 0 4px;margin:0 8px;}
.nav-label{font-size:9.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3);padding:0 8px 5px;}
.nav-item{display:flex;align-items:center;gap:9px;padding:7px 10px;border-radius:var(--r-sm);font-size:12.5px;font-weight:500;color:rgba(255,255,255,.65);transition:all .12s;cursor:pointer;white-space:nowrap;}
.nav-item:hover{color:#fff;background:rgba(255,255,255,.07);}
.nav-item.active{color:#fff;background:var(--navy);font-weight:600;}
.nav-item.active svg{opacity:1;}
.nav-item svg{width:15px;height:15px;flex-shrink:0;opacity:.6;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
.nav-badge{margin-left:auto;font-size:10px;font-weight:700;background:var(--accent);color:var(--accent-ink);padding:1px 6px;border-radius:10px;line-height:1.5;}
.nav-badge.red{background:#ef4444;color:#fff;}

.sidebar-footer{margin-top:auto;padding:10px 8px;border-top:1px solid rgba(255,255,255,.07);}

/* ── Header ── */
.admin-header{position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--header-h);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 20px;gap:14px;z-index:100;}
.header-toggle{display:none;width:34px;height:34px;border:none;background:none;align-items:center;justify-content:center;border-radius:var(--r-sm);}
.header-toggle:hover{background:var(--bg);}
.header-toggle svg{width:18px;height:18px;stroke:var(--text);fill:none;stroke-width:2;}
.header-breadcrumb{font-size:12.5px;color:var(--muted);flex:1;}
.header-breadcrumb strong{color:var(--text);font-weight:600;}
.header-actions{display:flex;align-items:center;gap:8px;margin-left:auto;}
.hbtn{height:32px;padding:0 12px;border-radius:var(--r-sm);font-size:12px;font-weight:600;border:none;transition:all .15s;display:inline-flex;align-items:center;gap:6px;}
.hbtn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;}
.hbtn-ghost{background:transparent;color:var(--muted);}
.hbtn-ghost:hover{background:var(--bg);color:var(--text);}
.hbtn-accent{background:var(--accent);color:var(--accent-ink);}
.hbtn-accent:hover{background:var(--accent-dark);}
.user-chip{display:flex;align-items:center;gap:7px;padding:4px 10px 4px 4px;border-radius:20px;border:1px solid var(--border);cursor:pointer;transition:border-color .15s;}
.user-chip:hover{border-color:var(--border-dark);}
.user-av{width:26px;height:26px;border-radius:50%;background:var(--navy);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.user-name{font-size:12px;font-weight:600;color:var(--text);}

/* ── Main content ── */
.admin-main{margin-left:var(--sidebar-w);margin-top:var(--header-h);flex:1;min-width:0;}
.page-header{padding:16px 20px 0;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.page-title{font-size:18px;font-weight:700;color:var(--text);}
.page-sub{font-size:12px;color:var(--muted);margin-top:1px;}
.page-actions{display:flex;gap:8px;align-items:center;}
.page-body{padding:14px 20px 30px;}

/* ── Cards ── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;}
.card-header{padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;}
.card-title{font-size:13px;font-weight:700;color:var(--text);}
.card-body{padding:16px;}

/* ── Stat cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:14px 16px;display:flex;align-items:center;gap:12px;}
.stat-icon{width:38px;height:38px;border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-icon svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8;}
.stat-icon.blue{background:#eff6ff;color:#2563eb;}
.stat-icon.green{background:#f0fdf4;color:var(--green);}
.stat-icon.amber{background:#fffbeb;color:#d97706;}
.stat-icon.red{background:#fef2f2;color:var(--red);}
.stat-icon.navy{background:#eff3fb;color:var(--navy);}
.stat-val{font-size:22px;font-weight:700;color:var(--text);line-height:1;letter-spacing:-.5px;}
.stat-lbl{font-size:11px;color:var(--muted);margin-top:2px;}
.stat-delta{font-size:10.5px;font-weight:600;margin-top:3px;}
.stat-delta.up{color:var(--green);}
.stat-delta.down{color:var(--red);}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:var(--r-sm);font-size:12.5px;font-weight:600;border:none;transition:all .15s;cursor:pointer;}
.btn svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.btn-primary{background:var(--navy);color:#fff;}
.btn-primary:hover{background:var(--navy-dark);}
.btn-accent{background:var(--accent);color:var(--accent-ink);}
.btn-accent:hover{background:var(--accent-dark);}
.btn-danger{background:#fef2f2;color:var(--red);border:1px solid #fecaca;}
.btn-danger:hover{background:#fee2e2;}
.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--border);}
.btn-ghost:hover{background:var(--bg);color:var(--text);}
.btn-sm{height:28px;padding:0 10px;font-size:11.5px;}
.btn-icon{width:32px;height:32px;padding:0;justify-content:center;}
.btn-icon.sm{width:27px;height:27px;}

/* ── Tables ── */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:12.5px;}
thead th{background:#f8fafc;padding:8px 12px;text-align:left;font-weight:700;font-size:11px;letter-spacing:.04em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);white-space:nowrap;}
tbody td{padding:9px 12px;border-bottom:1px solid var(--border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#fafbfc;}

/* ── Forms ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-grid.cols-3{grid-template-columns:1fr 1fr 1fr;}
.form-grid.cols-1{grid-template-columns:1fr;}
.field{display:flex;flex-direction:column;gap:4px;}
.field.span-2{grid-column:span 2;}
.field.span-3{grid-column:span 3;}
label{font-size:11.5px;font-weight:600;color:var(--text);}
label .req{color:var(--red);margin-left:2px;}
.input,.select,.textarea{width:100%;height:34px;padding:0 10px;border:1px solid var(--border-dark);border-radius:var(--r-sm);font-size:12.5px;font-family:inherit;color:var(--text);background:#fff;transition:border-color .12s,box-shadow .12s;outline:none;}
.textarea{height:auto;padding:8px 10px;resize:vertical;}
.input:focus,.select:focus,.textarea:focus{border-color:var(--navy);box-shadow:0 0 0 2.5px rgba(15,76,138,.12);}
.input.error,.select.error{border-color:var(--red);}
.field-error{font-size:11px;color:var(--red);margin-top:2px;}
.field-hint{font-size:11px;color:var(--muted);margin-top:2px;}
.input-group{display:flex;}
.input-group .input{border-radius:var(--r-sm) 0 0 var(--r-sm);}
.input-group .btn{border-radius:0 var(--r-sm) var(--r-sm) 0;height:34px;}
.form-section{margin-bottom:20px;}
.form-section-title{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border);}

/* ── Badges / Pills ── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10.5px;font-weight:700;}
.badge-green{background:#f0fdf4;color:var(--green);border:1px solid #bbf7d0;}
.badge-red{background:#fef2f2;color:var(--red);border:1px solid #fecaca;}
.badge-amber{background:#fffbeb;color:#d97706;border:1px solid #fde68a;}
.badge-blue{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;}
.badge-gray{background:#f3f4f6;color:var(--muted);border:1px solid var(--border);}
.badge-navy{background:#eff3fb;color:var(--navy);border:1px solid #c7d7f5;}

/* ── Status badges for quotes ── */
.status-new{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;}
.status-reviewing{background:#fffbeb;color:#b45309;border:1px solid #fde68a;}
.status-quoted{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;}
.status-won{background:#ecfdf5;color:#047857;border:1px solid #6ee7b7;}
.status-lost{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;}

/* ── Alerts ── */
.alert{padding:10px 14px;border-radius:var(--r-sm);font-size:12.5px;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px;}
.alert svg{width:14px;height:14px;flex-shrink:0;margin-top:1px;fill:none;stroke:currentColor;stroke-width:2;}
.alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;}
.alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;}
.alert-info{background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;}
.alert-warning{background:#fffbeb;color:#92400e;border:1px solid #fde68a;}

/* ── Pagination ── */
.pagination{display:flex;align-items:center;gap:4px;padding:12px 0 0;}
.page-link{min-width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-sm);border:1px solid var(--border);font-size:12px;font-weight:500;color:var(--muted);transition:all .12s;}
.page-link:hover{border-color:var(--navy);color:var(--navy);}
.page-link.active{background:var(--navy);color:#fff;border-color:var(--navy);}
.page-link.disabled{opacity:.4;pointer-events:none;}

/* ── Dropdown menus ── */
.dropdown{position:relative;}
.dropdown-menu{position:absolute;right:0;top:calc(100% + 4px);background:#fff;border:1px solid var(--border);border-radius:var(--r);box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:160px;z-index:300;padding:4px;}
.dropdown-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:var(--r-sm);font-size:12.5px;color:var(--text);cursor:pointer;transition:background .1s;}
.dropdown-item:hover{background:var(--bg);}
.dropdown-item svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;opacity:.6;}
.dropdown-item.danger{color:var(--red);}
.dropdown-item.danger:hover{background:#fef2f2;}
.dropdown-sep{height:1px;background:var(--border);margin:4px 0;}

/* ── Toggle switch ── */
.toggle{position:relative;display:inline-flex;align-items:center;cursor:pointer;gap:8px;}
.toggle input{position:absolute;opacity:0;width:0;height:0;}
.toggle-track{width:36px;height:20px;background:var(--border-dark);border-radius:10px;transition:background .15s;flex-shrink:0;}
.toggle input:checked + .toggle-track{background:var(--navy);}
.toggle-thumb{position:absolute;width:14px;height:14px;background:#fff;border-radius:50%;top:3px;left:3px;transition:left .15s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.toggle input:checked ~ .toggle-thumb{left:19px;}
.toggle-label{font-size:12.5px;font-weight:500;color:var(--text);}

/* ── Empty state ── */
.empty-state{text-align:center;padding:48px 20px;color:var(--muted);}
.empty-state svg{width:40px;height:40px;stroke:var(--border-dark);fill:none;stroke-width:1.5;margin:0 auto 12px;}
.empty-state .t{font-size:14px;font-weight:600;color:var(--text);margin-bottom:5px;}
.empty-state .s{font-size:12.5px;}

/* ── Search / filter bar ── */
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;}
.filter-bar .input{width:220px;}
.filter-bar .select{width:auto;}

/* ── Tabs ── */
.tabs{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:16px;}
.tab{padding:8px 14px;font-size:12.5px;font-weight:500;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all .12s;white-space:nowrap;}
.tab:hover{color:var(--text);}
.tab.active{color:var(--navy);border-bottom-color:var(--navy);font-weight:600;}

/* ── Backdrop for mobile sidebar ── */
.sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:190;}

/* ── Responsive ── */
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:none;}
  .sidebar-backdrop.open{display:block;}
  .admin-main{margin-left:0;}
  .admin-header{left:0;}
  .header-toggle{display:flex;}
  .stats-grid{grid-template-columns:repeat(2,1fr);}
  .form-grid{grid-template-columns:1fr;}
  .form-grid.cols-3{grid-template-columns:1fr;}
  .field.span-2,.field.span-3{grid-column:span 1;}
}
@media(max-width:540px){
  .stats-grid{grid-template-columns:1fr 1fr;}
  .page-header{padding:12px 14px 0;}
  .page-body{padding:12px 14px 24px;}
  .admin-header{padding:0 14px;}
  .page-actions{gap:6px;}
}
</style>
@stack('styles')
</head>
<body x-data="adminApp()" @keydown.escape="closeSidebar()">

<div class="sidebar-backdrop" :class="{open:sidebarOpen}" @click="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" :class="{open:sidebarOpen}" id="sidebar">
  <div class="sidebar-logo">
    <div class="mark">pcfy<span>.in</span></div>
    <div class="badge">Admin</div>
  </div>

  <nav style="flex:1;overflow-y:auto;overflow-x:hidden;padding-bottom:8px;">

    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <a href="{{ route('adminhu.dashboard') }}" class="nav-item {{ request()->routeIs('adminhu.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Catalogue</div>
      <a href="{{ route('adminhu.products.index') }}" class="nav-item {{ request()->routeIs('adminhu.products.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
        Products
      </a>
      <a href="{{ route('adminhu.categories.index') }}" class="nav-item {{ request()->routeIs('adminhu.categories.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
        Categories
      </a>
      <a href="{{ route('adminhu.brands.index') }}" class="nav-item {{ request()->routeIs('adminhu.brands.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
        Brands
      </a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Sales</div>
      <a href="{{ route('adminhu.quotes.index') }}" class="nav-item {{ request()->routeIs('adminhu.quotes.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Quotes
        @php $newQuotes = 0; @endphp
        {{-- @if($newQuotes > 0)<span class="nav-badge red">{{ $newQuotes }}</span>@endif --}}
      </a>
      <a href="{{ route('adminhu.orders.index') }}" class="nav-item {{ request()->routeIs('adminhu.orders.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        Orders
      </a>
      <a href="{{ route('adminhu.contacts.index') }}" class="nav-item {{ request()->routeIs('adminhu.contacts.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        Contact
      </a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Content</div>
      <a href="{{ route('adminhu.blog.index') }}" class="nav-item {{ request()->routeIs('adminhu.blog.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Blog
      </a>
      <a href="{{ route('adminhu.pages.index') }}" class="nav-item {{ request()->routeIs('adminhu.pages.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
        Pages
      </a>
      <a href="{{ route('adminhu.menus.index') }}" class="nav-item {{ request()->routeIs('adminhu.menus.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
        Menus
      </a>
      <a href="{{ route('adminhu.sections.index') }}" class="nav-item {{ request()->routeIs('adminhu.sections.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="4" rx="1"/><rect x="3" y="10" width="18" height="4" rx="1"/><rect x="3" y="17" width="18" height="4" rx="1"/></svg>
        Sections
      </a>
      <a href="{{ route('adminhu.banners.index') }}" class="nav-item {{ request()->routeIs('adminhu.banners.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="10" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
        Banners
      </a>
      <a href="{{ route('adminhu.testimonials.index') }}" class="nav-item {{ request()->routeIs('adminhu.testimonials.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        Testimonials
      </a>
      <a href="{{ route('adminhu.trust.index') }}" class="nav-item {{ request()->routeIs('adminhu.trust.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Trust Bar
      </a>
      <a href="{{ route('adminhu.media.index') }}" class="nav-item {{ request()->routeIs('adminhu.media.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
        Media
      </a>
    </div>

    <div class="nav-section">
      <div class="nav-label">System</div>
      @if(auth()->user()->hasRole('super_admin') || auth()->user()->can('users.view'))
      <a href="{{ route('adminhu.users.index') }}" class="nav-item {{ request()->routeIs('adminhu.users.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Users
      </a>
      @endif
      <a href="{{ route('adminhu.settings.index') }}" class="nav-item {{ request()->routeIs('adminhu.settings.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        Settings
      </a>
      <a href="{{ route('sitemap') }}" target="_blank" class="nav-item">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><path d="M12 7v4M5 17l7-6M19 17l-7-6"/></svg>
        Sitemap
      </a>
    </div>

  </nav>

  <div class="sidebar-footer">
    <a href="{{ route('adminhu.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-item" style="color:#ef4444;">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Sign out
    </a>
    <form id="logout-form" action="{{ route('adminhu.logout') }}" method="POST" style="display:none;">@csrf</form>
  </div>
</aside>

<!-- HEADER -->
<header class="admin-header">
  <button class="header-toggle" @click="toggleSidebar()">
    <svg viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
  </button>
  <div class="header-breadcrumb">
    {!! $breadcrumb ?? '<strong>' . (View::hasSection('title') ? View::getSections()['title'] : 'Dashboard') . '</strong>' !!}
  </div>
  <div class="header-actions">
    <a href="{{ url('/') }}" target="_blank" class="hbtn hbtn-ghost">
      <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6M15 3h6v6M10 14L21 3"/></svg>
      View site
    </a>
    {{-- Notification bell --}}
    @php
      $_bellQuotes   = \App\Models\Quote::where('status','new')->count();
      $_bellContacts = \App\Models\ContactSubmission::where('status','unread')->count();
      $_bellTotal    = $_bellQuotes + $_bellContacts;
    @endphp
    <div class="dropdown" x-data="{open:false}" style="position:relative;">
      <button class="hbtn hbtn-ghost" @click="open=!open" style="position:relative;">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
        @if($_bellTotal > 0)
          <span style="position:absolute;top:2px;right:2px;width:16px;height:16px;background:#ef4444;color:#fff;border-radius:50%;font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1;">{{ $_bellTotal > 9 ? '9+' : $_bellTotal }}</span>
        @endif
      </button>
      <div class="dropdown-menu" x-show="open" @click.outside="open=false" x-cloak style="min-width:220px;right:0;">
        <div class="dropdown-item" style="pointer-events:none;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);">Notifications</div>
        <div class="dropdown-sep"></div>
        <a href="{{ route('adminhu.quotes.index', ['status'=>'new']) }}" class="dropdown-item">
          <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/></svg>
          New quotes
          @if($_bellQuotes > 0)<span class="badge badge-amber" style="margin-left:auto;">{{ $_bellQuotes }}</span>@endif
        </a>
        <a href="{{ route('adminhu.contacts.index', ['status'=>'unread']) }}" class="dropdown-item">
          <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
          Unread contacts
          @if($_bellContacts > 0)<span class="badge badge-amber" style="margin-left:auto;">{{ $_bellContacts }}</span>@endif
        </a>
      </div>
    </div>
    <div class="dropdown" x-data="{open:false}">
      <div class="user-chip" @click="open=!open">
        <div class="user-av">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        <div class="user-name">{{ auth()->user()->name }}</div>
      </div>
      <div class="dropdown-menu" x-show="open" @click.outside="open=false" x-cloak>
        <div class="dropdown-item" style="pointer-events:none;color:var(--muted);font-size:11px;">
          {{ auth()->user()->roles->first()?->name ?? 'User' }}
        </div>
        <div class="dropdown-sep"></div>
        <a href="{{ route('adminhu.profile') }}" class="dropdown-item">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
          Profile
        </a>
        <div class="dropdown-sep"></div>
        <a href="{{ route('adminhu.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="dropdown-item danger">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Sign out
        </a>
      </div>
    </div>
  </div>
</header>

<!-- MAIN -->
<main class="admin-main">
  @if(session('success'))
    <div style="padding:12px 20px 0;">
      <div class="alert alert-success">
        <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
        {{ session('success') }}
      </div>
    </div>
  @endif
  @if(session('error'))
    <div style="padding:12px 20px 0;">
      <div class="alert alert-error">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
        {{ session('error') }}
      </div>
    </div>
  @endif
  @yield('content')
</main>

<script>
function adminApp() {
  return {
    sidebarOpen: window.innerWidth > 900,
    toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
    closeSidebar() { if(window.innerWidth <= 900) this.sidebarOpen = false; },
  }
}
</script>
@stack('scripts')
</body>
</html>
