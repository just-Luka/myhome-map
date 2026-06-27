<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — MYHOME-MAP</title>
    @include('partials.app-css')
    <style>
        /* ── Sidebar ── */
        #sidebar {
            width: 220px; flex-shrink: 0; background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0;
            transition: background 0.25s, border-color 0.25s;
        }
        .sidebar-logo { padding: 22px 20px 18px; font-size: 16px; font-weight: 700; border-bottom: 1px solid var(--sidebar-border); color: var(--body-text); }
        .sidebar-logo span { color: var(--brand); }
        .sidebar-logo img { max-height: 36px; max-width: 160px; object-fit: contain; display: block; }
        .sidebar-org  { font-size: 11px; color: var(--muted); margin-top: 4px; font-weight: 400; }
        .sidebar-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--sidebar-label); padding: 18px 20px 8px; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px; padding: 9px 20px;
            font-size: 13px; color: var(--sidebar-link); text-decoration: none;
            transition: all 0.15s; border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover  { color: var(--body-text); background: var(--sidebar-hover); }
        .sidebar-nav a.active { color: var(--primary); background: var(--primary-muted); border-left-color: var(--primary); }
        .sidebar-nav a .ti { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { margin-top: auto; padding: 16px; border-top: 1px solid var(--sidebar-border); }
        .sidebar-bottom a, .sidebar-bottom button {
            display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--dim);
            text-decoration: none; padding: 7px 4px; background: none; border: none; cursor: pointer; width: 100%;
        }
        .sidebar-bottom a:hover, .sidebar-bottom button:hover { color: var(--body-text); }
        .sidebar-user { font-size: 12px; color: var(--muted); margin-bottom: 8px; }

        /* ── Seats bar ── */
        .seats-bar   { margin: 14px 20px; }
        .seats-label { font-size: 11px; color: var(--muted); margin-bottom: 6px; display: flex; justify-content: space-between; }
        .seats-track { height: 4px; background: var(--card-border); border-radius: 2px; }
        .seats-fill  { height: 4px; background: var(--primary); border-radius: 2px; transition: width .3s; }

        /* ── Layout ── */
        #main    { margin-left: 220px; flex: 1; }
        #topbar  {
            height: 54px; background: var(--topbar-bg); border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; padding: 0 24px; gap: 10px;
            position: sticky; top: 0; z-index: 50; transition: background 0.25s, border-color 0.25s;
        }
        #topbar h1 { font-size: 15px; font-weight: 600; flex: 1; color: var(--body-text); }
        #content   { padding: 24px; }

        /* ── Dashboard-specific components ── */
        .invite-row   { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--card-border); font-size: 12px; }
        .invite-row:last-child { border-bottom: none; }
        .invite-token { font-family: monospace; color: var(--primary); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .invite-exp   { color: var(--th-color); white-space: nowrap; }

        .number-input { width: 70px; padding: 6px 10px; border-radius: 8px; border: 1px solid var(--card-border); background: var(--body-bg); color: var(--body-text); font-size: 14px; text-align: center; outline: none; }
        .number-input:focus { border-color: var(--primary); }

        .file-input  { width: 100%; background: var(--field-bg); border: 1px solid var(--field-border); border-radius: 8px; padding: 8px; color: var(--body-text); font-size: 13px; margin-bottom: 10px; }
        .field-error { color: var(--danger-text); font-size: 12px; margin-bottom: 8px; }

        .delete-save-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 14px; padding: 2px 6px; border-radius: 4px; transition: all .15s; }
        .delete-save-btn:hover { color: var(--brand); background: rgba(233,69,96,0.1); }

        .limit-input { width: 70px; padding: 5px 8px; border-radius: 6px; border: 1px solid var(--card-border); background: var(--body-bg); color: var(--body-text); font-size: 13px; text-align: center; outline: none; }
        .limit-input:focus { border-color: var(--primary); }

        @yield('styles')
    </style>
</head>
<body>

@php
    $seatPct = $members->count() / max($org->user_limit, 1) * 100;

    $navItems = [
        ['icon' => 'ti-layout-dashboard', 'label' => 'Dashboard', 'i18n' => 'dashboard', 'route' => 'owner.dashboard'],
        ['icon' => 'ti-users',            'label' => 'Employees', 'i18n' => 'employees', 'route' => 'owner.employees'],
        ['icon' => 'ti-settings',         'label' => 'Settings',  'i18n' => 'settings',  'route' => 'owner.settings'],
    ];
    $actionItems = [
        ['icon' => 'ti-map', 'label' => 'Live Map', 'i18n' => 'live_map', 'url' => '/', 'external' => true],
    ];
@endphp

<div id="sidebar">
    <div class="sidebar-logo">
        @if($org->logo)
            <img src="{{ Storage::url($org->logo) }}" alt="{{ $org->name }}">
        @else
            MYHOME-<span>MAP</span>
        @endif
        <div class="sidebar-org">{{ $org->name }}</div>
    </div>

    <div class="seats-bar">
        <div class="seats-label">
            <span data-i18n="seats">Seats</span>
            <span>{{ $members->count() }} / {{ $org->user_limit }}</span>
        </div>
        <div class="seats-track"><div class="seats-fill" style="width:{{ min($seatPct,100) }}%"></div></div>
    </div>

    <div class="sidebar-label" data-i18n="overview">Overview</div>
    <nav class="sidebar-nav">
        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                <i class="ti {{ $item['icon'] }}"></i>
                <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-label" data-i18n="actions">Actions</div>
    <nav class="sidebar-nav">
        @foreach($actionItems as $item)
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" @if($item['external']) target="_blank" @endif>
                    <i class="ti {{ $item['icon'] }}"></i>
                    <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
                </a>
            @else
                <a href="{{ route($item['route']) }}" @if($item['external'] ?? false) target="_blank" @endif>
                    <i class="ti {{ $item['icon'] }}"></i>
                    <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-user">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><i class="ti ti-logout"></i> <span data-i18n="sign_out">Sign out</span></button>
        </form>
    </div>
</div>

<div id="main">
    <div id="topbar">
        <h1>@yield('title', 'Dashboard')</h1>
        @yield('topbar-actions')
        <div id="lang-switcher">
            <button class="lang-btn active" onclick="setLang('en')">EN</button>
            <button class="lang-btn" onclick="setLang('ka')">KA</button>
        </div>
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
