<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Docházej</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root{ --bg:#EDEBE3; --surface:#FAF9F5; --ink:#1B1A17; --ink-soft:#6E6C62; --line:#D9D6C9;
    --accent:#1F5C52; --accent-soft:#DCE8E4; --warm:#B8793A; --warm-soft:#F2E4D2; --danger:#A6403A; --danger-soft:#F3E0DD; --radius:4px; }
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:'IBM Plex Sans',sans-serif;font-size:14px;line-height:1.5;}
  .app{display:flex;min-height:100vh;}
  .sidebar{width:220px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--line);padding:28px 20px;display:flex;flex-direction:column;}
  .brand-row{display:flex;align-items:center;gap:10px;margin-bottom:2px;}
  .brand-row img{width:32px;height:32px;flex-shrink:0;}
  .brand{font-family:'Fraunces',serif;font-size:21px;font-weight:600;margin:0;}
  .brand-sub{color:var(--ink-soft);font-size:12px;margin:0 0 32px;padding-left:42px;}
  nav{display:flex;flex-direction:column;gap:2px;flex:1;}
  .nav-link{text-align:left;background:none;border:none;padding:10px 12px;border-radius:var(--radius);font-size:14px;color:var(--ink-soft);text-decoration:none;display:block;}
  .nav-link:hover{background:var(--accent-soft);color:var(--ink);}
  .nav-link.active{background:var(--accent);color:#fff;}
  .who-box{border-top:1px solid var(--line);padding-top:14px;margin-top:14px;}
  .who-name{font-weight:600;font-size:13px;}
  .who-role{color:var(--ink-soft);font-size:11px;margin-bottom:8px;}
  .main{flex:1;padding:36px 44px;max-width:1000px;}
  h1.section-title{font-family:'Fraunces',serif;font-weight:500;font-size:22px;margin:0 0 20px;}
  .card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:20px;}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;}
  .emp-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:16px;display:flex;flex-direction:column;gap:10px;}
  .emp-name{font-weight:600;font-size:15px;}
  .emp-pos{color:var(--ink-soft);font-size:12px;margin-top:-6px;}
  .status-badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-family:'IBM Plex Mono',monospace;padding:4px 8px;border-radius:2px;width:fit-content;}
  .status-in{background:var(--accent-soft);color:var(--accent);}
  .status-out{background:var(--line);color:var(--ink-soft);}
  .status-break{background:var(--warm-soft);color:var(--warm);}
  .dot{width:6px;height:6px;border-radius:50%;background:currentColor;}
  .card-actions{display:flex;gap:6px;flex-wrap:wrap;}
  button,.btn{cursor:pointer;font-family:inherit;}
  .btn{border:1px solid var(--ink);background:var(--ink);color:#fff;padding:9px 14px;border-radius:var(--radius);font-size:13px;font-weight:500;text-decoration:none;display:inline-block;}
  .btn:hover{opacity:0.85;}
  .btn.clock-in{background:var(--accent);border-color:var(--accent);}
  .btn.clock-out{background:var(--danger);border-color:var(--danger);}
  .btn.break-btn{background:var(--warm);border-color:var(--warm);}
  .btn.ghost{background:none;color:var(--ink-soft);border:1px solid var(--line);}
  .btn.small{padding:6px 10px;font-size:12px;}
  input,select{font-family:inherit;font-size:14px;padding:9px 10px;border:1px solid var(--line);border-radius:var(--radius);background:#fff;color:var(--ink);}
  .form-row{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;}
  .field label{display:block;font-size:12px;color:var(--ink-soft);margin-bottom:5px;}
  table{width:100%;border-collapse:collapse;font-size:13.5px;}
  th{text-align:left;color:var(--ink-soft);font-weight:500;font-size:12px;padding:8px 10px;border-bottom:1px solid var(--line);}
  td{padding:10px;border-bottom:1px solid var(--line);vertical-align:top;}
  tr:last-child td{border-bottom:none;}
  .mono{font-family:'IBM Plex Mono',monospace;}
  .empty{color:var(--ink-soft);font-style:italic;padding:24px;text-align:center;}
  .total-row{font-weight:600;background:var(--accent-soft);}
  .status-pill{display:inline-block;padding:3px 9px;border-radius:2px;font-size:11.5px;font-weight:500;}
  .pill-pending{background:var(--warm-soft);color:var(--warm);}
  .pill-approved{background:var(--accent-soft);color:var(--accent);}
  .pill-rejected{background:var(--danger-soft);color:var(--danger);}
  .flash{background:var(--accent-soft);color:var(--accent);padding:10px 14px;border-radius:var(--radius);margin-bottom:18px;font-size:13px;}
  @media(max-width:720px){ .app{flex-direction:column;} .sidebar{width:100%;flex-direction:row;align-items:center;gap:20px;overflow-x:auto;} .brand-sub,.who-box{display:none;} .main{padding:24px;} }
</style>
</head>
<body>
<div class="app">
  <div class="sidebar">
    <div class="brand-row">
      <img src="{{ asset('images/logo.svg') }}" alt="Docházej">
      <p class="brand">Docházej</p>
    </div>
    <p class="brand-sub">evidence pracovní doby</p>
    <nav>
      <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Přehled</a>
      @if(auth()->user()->isAdmin())
        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">Zaměstnanci</a>
      @endif
      <a href="{{ route('records.index') }}" class="nav-link {{ request()->routeIs('records.*') ? 'active' : '' }}">Záznamy</a>
      <a href="{{ route('vacation.index') }}" class="nav-link {{ request()->routeIs('vacation.*') ? 'active' : '' }}">Dovolená</a>
      @if(auth()->user()->isAdmin())
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">Nastavení</a>
      @endif
    </nav>
    <div class="who-box">
      <div class="who-name">{{ auth()->user()->name }}</div>
      <div class="who-role">{{ auth()->user()->isAdmin() ? 'plný přístup' : 'zaměstnanec' }}</div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn ghost small" style="width:100%;">Odhlásit se</button>
      </form>
    </div>
  </div>
  <div class="main">
    @if(session('status'))
      <div class="flash">{{ session('status') }}</div>
    @endif
    @yield('content')
  </div>
</div>
</body>
</html>
