<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — MYHOME-MAP</title>
    @include('partials.app-css')
    <style>
        /* ── Sidebar ── */
        #sidebar {
            width: 220px; flex-shrink: 0; background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;
            transition: background 0.25s, border-color 0.25s;
        }
        .sidebar-logo { padding: 24px 20px 20px; font-size: 17px; font-weight: 700; border-bottom: 1px solid var(--sidebar-border); color: var(--body-text); }
        .sidebar-logo span { color: var(--brand); }
        .sidebar-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--sidebar-label); padding: 20px 20px 8px; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px; padding: 10px 20px;
            font-size: 13px; color: var(--sidebar-link); text-decoration: none;
            transition: all 0.15s; border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover  { color: var(--body-text); background: var(--sidebar-hover); }
        .sidebar-nav a.active { color: var(--primary); background: var(--primary-muted); border-left-color: var(--primary); }
        .sidebar-nav a .ti { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px; border-top: 1px solid var(--sidebar-border); }
        .sidebar-bottom a { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--dim); text-decoration: none; padding: 8px 4px; }
        .sidebar-bottom a:hover { color: var(--body-text); }

        /* ── Layout ── */
        #main   { margin-left: 220px; flex: 1; min-height: 100vh; }
        #topbar {
            height: 56px; background: var(--topbar-bg); border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; padding: 0 28px; gap: 12px;
            position: sticky; top: 0; z-index: 50; transition: background 0.25s, border-color 0.25s;
        }
        #topbar h1   { font-size: 16px; font-weight: 600; flex: 1; color: var(--body-text); }
        .topbar-user { font-size: 13px; color: var(--dim); }
        #content { padding: 28px; }

        @yield('styles')
    </style>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-logo">MYHOME-<span>MAP</span></div>

    <div class="sidebar-label">Overview</div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
    </nav>

    <div class="sidebar-label">Manage</div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.orgs') }}"     class="{{ request()->routeIs('admin.orgs*')    ? 'active' : '' }}"><i class="ti ti-building"></i> Organizations</a>
        <a href="{{ route('admin.users') }}"    class="{{ request()->routeIs('admin.users*')   ? 'active' : '' }}"><i class="ti ti-users"></i> Users</a>
        <a href="{{ route('admin.activity') }}" class="{{ request()->routeIs('admin.activity') ? 'active' : '' }}"><i class="ti ti-activity"></i> Activity</a>
    </nav>

    <div class="sidebar-label">System</div>
    <nav class="sidebar-nav">
        <a href="/scrape" target="_blank"><i class="ti ti-refresh"></i> Scraper</a>
        <a href="/"       target="_blank"><i class="ti ti-map"></i> Live Map</a>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
            <i class="ti ti-logout"></i> Sign out
        </a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}">@csrf</form>
    </div>
</div>

<div id="main">
    <div id="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        @yield('topbar-actions')
        <span class="topbar-user">{{ auth()->user()->name }}</span>
        <button id="theme-toggle" onclick="toggleTheme()" title="Switch theme">🌙</button>
    </div>
    <div id="content">
        @yield('content')
    </div>
</div>

@include('partials.confirm-modal')
@include('partials.app-js')
</body>
</html>
