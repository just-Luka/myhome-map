<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Theme variables ── */
        :root {
            --body-bg:       #0b0d14;
            --body-text:     #e2e8f0;
            --sidebar-bg:    #111320;
            --sidebar-border:#1e2235;
            --sidebar-label: #3d4466;
            --sidebar-link:  #94a3b8;
            --sidebar-hover-bg: rgba(255,255,255,0.04);
            --topbar-bg:     #111320;
            --card-bg:       #14172a;
            --card-border:   #1e2235;
            --stat-bg:       #14172a;
            --tbl-sep:       #1e2235;
            --tbl-row-sep:   #111320;
            --tbl-hover:     rgba(255,255,255,0.015);
            --th-color:      #4d5780;
            --field-bg:      #0b0d14;
            --field-border:  #1e2235;
            --empty-color:   #4d5780;
            --toggle-border: #1e2235;
            --toggle-small:  #4d5780;
            --pagination-bg: #14172a;
            --pagination-border: #1e2235;
            --pagination-text:   #94a3b8;
            --card-title:    #4d5780;
            --topbar-user:   #64748b;
            --sidebar-bottom-text: #64748b;
        }
        body.theme-light {
            --body-bg:       #f1f5f9;
            --body-text:     #1a1a2e;
            --sidebar-bg:    #ffffff;
            --sidebar-border:#e2e8f0;
            --sidebar-label: #94a3b8;
            --sidebar-link:  #64748b;
            --sidebar-hover-bg: rgba(0,0,0,0.04);
            --topbar-bg:     #ffffff;
            --card-bg:       #ffffff;
            --card-border:   #e2e8f0;
            --stat-bg:       #ffffff;
            --tbl-sep:       #e2e8f0;
            --tbl-row-sep:   #f8fafc;
            --tbl-hover:     rgba(0,0,0,0.02);
            --th-color:      #94a3b8;
            --field-bg:      #f8fafc;
            --field-border:  #e2e8f0;
            --empty-color:   #94a3b8;
            --toggle-border: #e2e8f0;
            --toggle-small:  #94a3b8;
            --pagination-bg: #ffffff;
            --pagination-border: #e2e8f0;
            --pagination-text:   #64748b;
            --card-title:    #94a3b8;
            --topbar-user:   #94a3b8;
            --sidebar-bottom-text: #94a3b8;
        }

        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--body-bg); color: var(--body-text); min-height: 100vh; display: flex; transition: background 0.25s, color 0.25s; }

        /* ── Sidebar ── */
        #sidebar {
            width: 220px; flex-shrink: 0; background: var(--sidebar-bg); border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;
            transition: background 0.25s, border-color 0.25s;
        }
        .sidebar-logo { padding: 24px 20px 20px; font-size: 17px; font-weight: 700; border-bottom: 1px solid var(--sidebar-border); color: var(--body-text); }
        .sidebar-logo span { color: #e94560; }
        .sidebar-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--sidebar-label); padding: 20px 20px 8px; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px; padding: 10px 20px;
            font-size: 13px; color: var(--sidebar-link); text-decoration: none; border-radius: 0;
            transition: all 0.15s; border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover { color: var(--body-text); background: var(--sidebar-hover-bg); }
        .sidebar-nav a.active { color: #4f6ef7; background: rgba(79,110,247,0.08); border-left-color: #4f6ef7; }
        .sidebar-nav a .icon { font-size: 15px; width: 20px; text-align: center; }
        .sidebar-bottom { margin-top: auto; padding: 16px; border-top: 1px solid var(--sidebar-border); }
        .sidebar-bottom a { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--sidebar-bottom-text); text-decoration: none; padding: 8px 4px; }
        .sidebar-bottom a:hover { color: var(--body-text); }

        /* ── Main ── */
        #main { margin-left: 220px; flex: 1; min-height: 100vh; }
        #topbar {
            height: 56px; background: var(--topbar-bg); border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; padding: 0 28px; gap: 12px; position: sticky; top: 0; z-index: 50;
            transition: background 0.25s, border-color 0.25s;
        }
        #topbar h1 { font-size: 16px; font-weight: 600; flex: 1; color: var(--body-text); }
        .topbar-user { font-size: 13px; color: var(--topbar-user); }
        #theme-toggle {
            display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px; cursor: pointer;
            background: var(--sidebar-hover-bg); border: 1px solid var(--sidebar-border);
            font-size: 15px; transition: all 0.2s; flex-shrink: 0;
        }
        #theme-toggle:hover { border-color: #4f6ef7; }
        #content { padding: 28px; }

        /* ── Cards ── */
        .card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 24px; margin-bottom: 20px; transition: background 0.25s, border-color 0.25s; }
        .card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--card-title); margin-bottom: 18px; }

        /* ── Stats grid ── */
        .stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .stat { background: var(--stat-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 20px; transition: background 0.25s; }
        .stat .val { font-size: 32px; font-weight: 700; color: #e94560; line-height: 1; }
        .stat .lbl { font-size: 12px; color: var(--card-title); margin-top: 6px; }

        /* ── Tables ── */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--th-color); padding: 0 12px 12px 0; border-bottom: 1px solid var(--tbl-sep); }
        .tbl td { padding: 12px 12px 12px 0; font-size: 13px; border-bottom: 1px solid var(--tbl-row-sep); vertical-align: middle; }
        .tbl tr:last-child td { border-bottom: none; }
        .tbl tr:hover td { background: var(--tbl-hover); }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-super  { background: #2d1a3a; color: #c084fc; }
        .badge-ceo    { background: #1e2a3a; color: #7dd3fc; }
        .badge-employee { background: #1a2535; color: #86efac; }
        .badge-free   { background: #1e2235; color: #64748b; }
        .badge-pro    { background: #1e3a1e; color: #86efac; }
        .badge-org    { background: #1e2a1a; color: #a3e635; }

        /* ── Forms ── */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid.cols-1 { grid-template-columns: 1fr; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 12px; color: var(--card-title); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .field input, .field select { background: var(--field-bg); border: 1px solid var(--field-border); border-radius: 8px; padding: 9px 12px; color: var(--body-text); font-size: 14px; outline: none; width: 100%; transition: background 0.25s, border-color 0.2s; }
        .field input:focus, .field select:focus { border-color: #4f6ef7; }
        .field select option { background: var(--card-bg); }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
        .btn-primary  { background: #4f6ef7; color: #fff; }
        .btn-primary:hover  { background: #3b5be0; }
        .btn-danger   { background: #7f1d1d; color: #fca5a5; }
        .btn-danger:hover   { background: #991b1b; }
        .btn-ghost    { background: transparent; border: 1px solid #1e2235; color: #94a3b8; }
        .btn-ghost:hover    { border-color: #4f6ef7; color: #fff; }
        .btn-green    { background: #166534; color: #86efac; }
        .btn-green:hover    { background: #15803d; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* ── Alerts ── */
        .alert { border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; word-break: break-all; }
        .alert-success { background: #1a2a1a; border: 1px solid #2e5c2e; color: #86efac; }
        .alert-error   { background: #2a1a1a; border: 1px solid #5c2e2e; color: #fca5a5; }
        .alert-info    { background: #1a2035; border: 1px solid #2d3a6e; color: #7dd3fc; }

        /* ── Toggle ── */
        .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--toggle-border); }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-label span { font-size: 14px; display: block; }
        .toggle-label small { font-size: 12px; color: var(--toggle-small); }
        input[type=checkbox] { width: 18px; height: 18px; accent-color: #4f6ef7; cursor: pointer; }

        /* ── Empty state ── */
        .empty { text-align: center; padding: 40px 20px; color: var(--empty-color); font-size: 14px; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 6px; margin-top: 20px; justify-content: flex-end; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; }
        .pagination a { background: var(--pagination-bg); border: 1px solid var(--pagination-border); color: var(--pagination-text); }
        .pagination a:hover { border-color: #4f6ef7; color: #4f6ef7; }
        .pagination span { background: #4f6ef7; color: #fff; }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-logo">MYHOME-<span>MAP</span></div>

    <div class="sidebar-label">Overview</div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <span class="icon">📊</span> Dashboard
        </a>
    </nav>

    <div class="sidebar-label">Manage</div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.orgs') }}" class="{{ request()->routeIs('admin.orgs*') ? 'active' : '' }}">
            <span class="icon">🏢</span> Organizations
        </a>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="icon">👥</span> Users
        </a>
        <a href="{{ route('admin.activity') }}" class="{{ request()->routeIs('admin.activity') ? 'active' : '' }}">
            <span class="icon">📋</span> Activity
        </a>
    </nav>

    <div class="sidebar-label">System</div>
    <nav class="sidebar-nav">
        <a href="/scrape" target="_blank">
            <span class="icon">🔄</span> Scraper
        </a>
        <a href="/" target="_blank">
            <span class="icon">🗺</span> Live Map
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
            ↩ Sign out
        </a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}">@csrf</form>
    </div>
</div>

<div id="main">
    <div id="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        <span class="topbar-user">{{ auth()->user()->name }}</span>
        <button id="theme-toggle" onclick="toggleTheme()" title="Switch theme">🌙</button>
    </div>
    <div id="content">
        @yield('content')
    </div>
</div>

<script>
(function () {
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('theme-light');
        const btn = document.getElementById('theme-toggle');
        if (btn) btn.textContent = '☀️';
    }
})();
function toggleTheme() {
    const isLight = document.body.classList.toggle('theme-light');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    document.getElementById('theme-toggle').textContent = isLight ? '☀️' : '🌙';
}
</script>
</body>
</html>
