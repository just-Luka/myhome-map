<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --body-bg: #0b0d14; --body-text: #e2e8f0;
            --sidebar-bg: #111320; --sidebar-border: #1e2235;
            --sidebar-label: #3d4466; --sidebar-org: #4d5780;
            --sidebar-link: #94a3b8; --sidebar-hover: rgba(255,255,255,0.04);
            --topbar-bg: #111320;
            --card-bg: #14172a; --card-border: #1e2235;
            --th-color: #4d5780; --td-border: #0f1117;
            --employee-border: #1e2235; --invite-border: #1e2235;
            --toggle-border: #1e2235; --toggle-small: #4d5780;
            --seats-track: #1e2235; --seats-label: #4d5780;
            --sidebar-bottom: #64748b;
        }
        body.theme-light {
            --body-bg: #f1f5f9; --body-text: #1a1a2e;
            --sidebar-bg: #ffffff; --sidebar-border: #e2e8f0;
            --sidebar-label: #94a3b8; --sidebar-org: #94a3b8;
            --sidebar-link: #64748b; --sidebar-hover: rgba(0,0,0,0.04);
            --topbar-bg: #ffffff;
            --card-bg: #ffffff; --card-border: #e2e8f0;
            --th-color: #94a3b8; --td-border: #f1f5f9;
            --employee-border: #e2e8f0; --invite-border: #e2e8f0;
            --toggle-border: #e2e8f0; --toggle-small: #94a3b8;
            --seats-track: #e2e8f0; --seats-label: #94a3b8;
            --sidebar-bottom: #94a3b8;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--body-bg); color: var(--body-text); min-height: 100vh; display: flex; transition: background 0.25s, color 0.25s; }

        /* ── Sidebar ── */
        #sidebar {
            width: 220px; flex-shrink: 0; background: var(--sidebar-bg); border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column; position: fixed; top: 0; bottom: 0; left: 0; transition: background 0.25s, border-color 0.25s;
        }
        .sidebar-logo { padding: 22px 20px 18px; font-size: 16px; font-weight: 700; border-bottom: 1px solid var(--sidebar-border); color: var(--body-text); }
        .sidebar-logo span { color: #e94560; }
        .sidebar-org { font-size: 11px; color: var(--sidebar-org); margin-top: 4px; font-weight: 400; }
        .sidebar-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--sidebar-label); padding: 18px 20px 8px; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 10px; padding: 9px 20px;
            font-size: 13px; color: var(--sidebar-link); text-decoration: none;
            transition: all 0.15s; border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover { color: var(--body-text); background: var(--sidebar-hover); }
        .sidebar-nav a.active { color: #4f6ef7; background: rgba(79,110,247,0.08); border-left-color: #4f6ef7; }
        .sidebar-bottom { margin-top: auto; padding: 16px; border-top: 1px solid var(--sidebar-border); }
        .sidebar-bottom a, .sidebar-bottom button {
            display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--sidebar-bottom);
            text-decoration: none; padding: 7px 4px; background: none; border: none; cursor: pointer; width: 100%;
        }
        .sidebar-bottom a:hover, .sidebar-bottom button:hover { color: var(--body-text); }
        .seats-bar { margin: 14px 20px; }
        .seats-label { font-size: 11px; color: var(--seats-label); margin-bottom: 6px; display: flex; justify-content: space-between; }
        .seats-track { height: 4px; background: var(--seats-track); border-radius: 2px; }
        .seats-fill { height: 4px; background: #4f6ef7; border-radius: 2px; transition: width .3s; }

        /* ── Main ── */
        #main { margin-left: 220px; flex: 1; }
        #topbar {
            height: 54px; background: var(--topbar-bg); border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; padding: 0 24px; gap: 10px;
            position: sticky; top: 0; z-index: 50; transition: background 0.25s, border-color 0.25s;
        }
        #topbar h1 { font-size: 15px; font-weight: 600; flex: 1; color: var(--body-text); }
        #theme-toggle {
            display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px; cursor: pointer; flex-shrink: 0;
            background: rgba(255,255,255,0.06); border: 1px solid #1e2235;
            font-size: 15px; transition: all 0.2s;
        }
        #theme-toggle:hover { border-color: #4f6ef7; }
        #content { padding: 24px; }

        /* ── Stats ── */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px; }
        .stat { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 18px 20px; transition: background 0.25s; }
        .stat .val { font-size: 28px; font-weight: 700; color: #e94560; line-height: 1; }
        .stat .lbl { font-size: 11px; color: var(--th-color); margin-top: 5px; }
        .stat .sub { font-size: 11px; color: #64748b; margin-top: 3px; }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .grid-3-1 { display: grid; grid-template-columns: 1fr 300px; gap: 16px; align-items: start; }

        /* ── Cards ── */
        .card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 20px; margin-bottom: 16px; transition: background 0.25s, border-color 0.25s; }
        .card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .card-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--th-color); }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: var(--th-color); padding: 0 10px 10px 0; border-bottom: 1px solid var(--card-border); }
        td { padding: 11px 10px 11px 0; font-size: 13px; border-bottom: 1px solid var(--td-border); vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(0,0,0,0.02); }

        /* ── Employee list ── */
        .employee-row { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--employee-border); }
        .employee-row:last-child { border-bottom: none; }
        .employee-avatar { width: 32px; height: 32px; border-radius: 50%; background: #e94560; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; color: #fff; }
        .employee-info { flex: 1; min-width: 0; }
        .employee-name { font-size: 13px; font-weight: 600; }
        .employee-meta { font-size: 11px; color: var(--th-color); margin-top: 2px; }
        .employee-count { font-size: 18px; font-weight: 700; color: #e94560; flex-shrink: 0; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-employee { background: #1e2a3a; color: #7dd3fc; }
        .badge-ceo    { background: #2a1e3a; color: #c084fc; }

        /* ── Invite ── */
        .invite-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid var(--invite-border); font-size: 12px; }
        .invite-row:last-child { border-bottom: none; }
        .invite-token { font-family: monospace; color: #4f6ef7; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .invite-exp { color: var(--th-color); white-space: nowrap; }

        /* ── Toggle ── */
        .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 0; border-bottom: 1px solid var(--toggle-border); }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-row span { font-size: 13px; }
        .toggle-row small { font-size: 11px; color: var(--toggle-small); display: block; }
        input[type=checkbox] { width: 18px; height: 18px; accent-color: #4f6ef7; cursor: pointer; }

        /* ── Buttons ── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .15s; }
        .btn-primary { background: #4f6ef7; color: #fff; }
        .btn-primary:hover { background: #3b5be0; }
        .btn-ghost { background: transparent; border: 1px solid #1e2235; color: #94a3b8; }
        .btn-ghost:hover { border-color: #4f6ef7; color: #fff; }
        .btn-green { background: #166534; color: #86efac; }
        .btn-green:hover { background: #15803d; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* ── Alerts ── */
        .alert { border-radius: 8px; padding: 11px 15px; margin-bottom: 16px; font-size: 13px; word-break: break-all; }
        .alert-success { background: #1a2a1a; border: 1px solid #2e5c2e; color: #86efac; }
        .alert-error   { background: #2a1a1a; border: 1px solid #5c2e2e; color: #fca5a5; }

        /* ── Price ── */
        .price-my   { color: #e94560; font-weight: 700; }
        .price-orig { color: #4d5780; font-size: 11px; }
        .discount   { color: #86efac; font-size: 11px; font-weight: 600; }

        /* ── Filter bar ── */
        .filter-row { display: flex; gap: 8px; align-items: center; }
        .filter-row select { background: #0b0d14; border: 1px solid #1e2235; border-radius: 8px; padding: 6px 10px; color: #e2e8f0; font-size: 12px; outline: none; }
        .filter-row select:focus { border-color: #4f6ef7; }

        /* ── Empty ── */
        .empty { text-align: center; padding: 32px; color: #4d5780; font-size: 13px; }

        /* ── Listing table cells ── */
        .listing-title   { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
        .listing-address { font-size: 11px; color: #4d5780; }
        .listing-chips   { display: flex; gap: 6px; margin-top: 4px; flex-wrap: wrap; }
        .listing-chip    { font-size: 10px; background: #1e2235; padding: 2px 6px; border-radius: 4px; color: #94a3b8; }
        .listing-url     { font-size: 11px; color: #4f6ef7; text-decoration: none; display: block; margin-top: 4px; }
        .listing-owner   { font-size: 12px; font-weight: 600; }
        .listing-phone   { font-size: 12px; color: #94a3b8; }
        .listing-when    { color: #4d5780; font-size: 12px; white-space: nowrap; }
        .listing-price-original { color: #94a3b8; }

        /* ── File input ── */
        .file-input { width: 100%; background: #0b0d14; border: 1px solid #1e2235; border-radius: 8px; padding: 8px; color: #e2e8f0; font-size: 13px; margin-bottom: 10px; }
        .field-error { color: #fca5a5; font-size: 12px; margin-bottom: 8px; }

        /* ── Number input ── */
        .number-input { width: 70px; padding: 6px 10px; border-radius: 8px; border: 1px solid var(--card-border); background: var(--body-bg); color: var(--body-text); font-size: 14px; text-align: center; outline: none; }
        .number-input:focus { border-color: #4f6ef7; }

        /* ── Copy button ── */
        .copy-btn { padding: 3px 8px; border-radius: 5px; background: #1e2235; border: none; color: #94a3b8; font-size: 11px; cursor: pointer; }
        .copy-btn:hover { background: #2d3149; color: #fff; }

        /* ── Lang switcher ── */
        #lang-switcher { display: flex; border: 1.5px solid #1e2235; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
        .lang-btn { padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: transparent; color: #64748b; transition: all 0.15s; }
        .lang-btn.active { background: #e94560; color: #fff; }
        .lang-btn:hover:not(.active) { color: #e2e8f0; }

        @yield('styles')
    </style>
</head>
<body>

@php
    $seatPct = $members->count() / max($org->user_limit, 1) * 100;

    // ── Nav items — add new pages here ──────────────────────────────────────
    $navItems = [
        ['icon' => '📊', 'label' => 'Dashboard',  'i18n' => 'dashboard',   'route' => 'dashboard'],
        ['icon' => '⚙',  'label' => 'Settings',   'i18n' => 'settings',    'route' => 'dashboard.settings'],
    ];
    $actionItems = [
        ['icon' => '⬇', 'label' => 'Export All', 'i18n' => 'export_all', 'route' => 'dashboard.export', 'external' => false],
        ['icon' => '🗺', 'label' => 'Live Map',   'i18n' => 'live_map',   'url'   => '/',               'external' => true],
    ];
@endphp

<div id="sidebar">
    <div class="sidebar-logo">
        MYHOME-<span>MAP</span>
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
            <a href="{{ route($item['route']) }}"
               class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                {{ $item['icon'] }} <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="sidebar-label" data-i18n="actions">Actions</div>
    <nav class="sidebar-nav">
        @foreach($actionItems as $item)
            @if(isset($item['url']))
                <a href="{{ $item['url'] }}" @if($item['external']) target="_blank" @endif>
                    {{ $item['icon'] }} <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
                </a>
            @else
                <a href="{{ route($item['route']) }}" @if($item['external'] ?? false) target="_blank" @endif>
                    {{ $item['icon'] }} <span data-i18n="{{ $item['i18n'] }}">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="sidebar-bottom">
        <div style="font-size:12px;color:#4d5780;margin-bottom:8px">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">↩ <span data-i18n="sign_out">Sign out</span></button>
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

// Base translations (sidebar labels — shared across all pages)
const translations = {
    en: {
        seats: 'Seats', overview: 'Overview', actions: 'Actions', sign_out: 'Sign out',
        dashboard: 'Dashboard', settings: 'Settings', export_all: 'Export All', live_map: 'Live Map',
    },
    ka: {
        seats: 'ადგილები', overview: 'მიმოხილვა', actions: 'მოქმედებები', sign_out: 'გასვლა',
        dashboard: 'პანელი', settings: 'პარამეტრები', export_all: 'ექსპორტი', live_map: 'რუქა',
    },
};

function setLang(lang) {
    localStorage.setItem('dashboard_lang', lang);
    document.querySelectorAll('[data-i18n]').forEach(el => {
        if (translations[lang]?.[el.dataset.i18n] !== undefined)
            el.textContent = translations[lang][el.dataset.i18n];
    });
    document.querySelectorAll('.lang-btn').forEach(b =>
        b.classList.toggle('active', b.textContent.toLowerCase() === lang)
    );
}
</script>

{{-- Page scripts run here — they can extend `translations` before init --}}
@yield('scripts')

<script>
(function () {
    const saved = localStorage.getItem('dashboard_lang') || 'en';
    if (saved !== 'en') setLang(saved);
    else document.querySelector('.lang-btn')?.classList.add('active');
})();
</script>
</body>
</html>
