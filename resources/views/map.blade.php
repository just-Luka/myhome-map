<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYHOME-MAP — Tbilisi Rent Map</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- SEO -->
    <meta name="description" content="Browse Tbilisi apartment rentals on an interactive map. Filter by price, rooms, and building type. Updated every 3 hours from myhome.ge.">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="https://myhome-map.ge/">
    <meta property="og:title"       content="MYHOME-MAP — Tbilisi Rent Map">
    <meta property="og:description" content="Browse Tbilisi apartment rentals on an interactive map. Filter by price, rooms, and building type. Updated every 3 hours from myhome.ge.">
    <meta property="og:image"       content="https://myhome-map.ge/og-image.png">
    <meta property="og:locale"      content="en_US">
    <meta property="og:locale:alternate" content="ru_RU">

    <!-- Twitter / X -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="MYHOME-MAP — Tbilisi Rent Map">
    <meta name="twitter:description" content="Browse Tbilisi apartment rentals on an interactive map. Filter by price, rooms, and building type.">
    <meta name="twitter:image"       content="https://myhome-map.ge/og-image.png">

    <!-- Theme -->
    <meta name="theme-color" content="#1a1a2e">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authed" content="1">
    @endauth
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        /* ── Header ── */
        #header {
            position: fixed; top: 0; left: 0; right: 0; height: 56px;
            background: #1a1a2e; display: flex; align-items: center;
            padding: 0 20px; z-index: 1000; gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }
        #header h1 { color: #fff; font-size: 18px; font-weight: 700; }
        #header h1 span { color: #e94560; }
        #org-logo { max-height: 32px; max-width: 140px; object-fit: contain; }
        #status-bar { color: #aaa; font-size: 13px; margin-left: auto; display: flex; align-items: center; gap: 8px; }
        #live-dot { width: 8px; height: 8px; border-radius: 50%; background: #e94560; display: none; animation: pulse 1s infinite; flex-shrink: 0; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* Saved list button */
        #saved-btn {
            display: none; padding: 6px 12px; font-size: 12px; font-weight: 600; border-radius: 8px;
            background: #e94560; color: #fff; text-decoration: none; border: none;
            white-space: nowrap; flex-shrink: 0; cursor: pointer; transition: background 0.15s;
        }
        #saved-btn:hover { background: #c73652; }

        /* User menu */
        #user-menu { position: relative; flex-shrink: 0; }
        #user-trigger {
            display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.15); border-radius: 8px;
            padding: 5px 10px; cursor: pointer; color: #fff; font-size: 13px;
            transition: background 0.15s;
        }
        #user-trigger:hover { background: rgba(255,255,255,0.14); }
        #user-avatar {
            width: 24px; height: 24px; border-radius: 50%; background: #e94560;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0; color: #fff;
        }
        #user-name { max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
        #user-dropdown {
            display: none; position: absolute; top: calc(100% + 8px); right: 0;
            background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px;
            padding: 8px; min-width: 200px; z-index: 2000;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
        }
        #user-dropdown.open { display: block; }
        #user-info { padding: 10px 10px 12px; border-bottom: 1px solid #2d3149; margin-bottom: 6px; }
        #user-info strong { display: block; font-size: 14px; color: #e2e8f0; margin-bottom: 2px; }
        #user-info span { display: block; font-size: 12px; color: #64748b; }
        .user-plan { display: inline-block; margin-top: 6px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .plan-pro { background: #1e3a1e; color: #86efac; }
        .plan-free { background: #1e2235; color: #7dd3fc; }
        #user-dropdown a, #user-dropdown button[type=submit] {
            display: block; width: 100%; text-align: left; padding: 9px 12px;
            font-size: 13px; color: #e2e8f0; text-decoration: none; border-radius: 8px;
            background: none; border: none; cursor: pointer; transition: background 0.15s;
        }
        #user-dropdown a:hover, #user-dropdown button[type=submit]:hover { background: rgba(255,255,255,0.07); }
        /* Language switcher */
        #lang-switcher { display: flex; border: 1.5px solid rgba(255,255,255,0.2); border-radius: 8px; overflow: hidden; flex-shrink: 0; }
        .lang-btn {
            padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer;
            border: none; background: transparent; color: rgba(255,255,255,0.5);
            transition: all 0.15s;
        }
        .lang-btn.active { background: #e94560; color: #fff; }
        .lang-btn:hover:not(.active) { color: #fff; }

        /* ── Filter bar ── */
        #filter-bar {
            position: fixed; top: 56px; left: 0; right: 0; height: 54px;
            background: #fff; border-bottom: 1px solid #ebebeb;
            display: flex; align-items: center; padding: 0 20px; gap: 8px;
            z-index: 999; overflow-x: auto;
        }
        #filter-bar::-webkit-scrollbar { display: none; }

        .fpill {
            display: flex; align-items: center; gap: 6px; white-space: nowrap;
            border: 1.5px solid #ddd; border-radius: 22px;
            padding: 7px 14px; font-size: 13px; font-weight: 500; color: #333;
            background: #fff; cursor: pointer; transition: all 0.15s; user-select: none;
            flex-shrink: 0;
        }
        .fpill:hover { border-color: #1a1a2e; color: #1a1a2e; }
        .fpill.active { border-color: #1a1a2e; background: #1a1a2e; color: #fff; }
        .fpill .arrow { font-size: 10px; opacity: 0.6; transition: transform 0.2s; }
        .fpill.open .arrow { transform: rotate(180deg); }

        /* ── Dropdowns ── */
        .fdrop {
            position: fixed; top: 110px;
            background: #fff; border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.16);
            padding: 20px; min-width: 280px; z-index: 1100;
            display: none; flex-direction: column; gap: 14px;
        }
        .fdrop.open { display: flex; }
        .fdrop-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #888; }

        .price-row { display: flex; gap: 10px; align-items: center; }
        .price-row input {
            flex: 1; border: 1.5px solid #e0e0e0; border-radius: 10px;
            padding: 9px 12px; font-size: 14px; color: #1a1a2e; outline: none;
            transition: border-color 0.2s;
        }
        .price-row input:focus { border-color: #e94560; }
        .price-row span { color: #bbb; }

        .chip-row { display: flex; gap: 7px; flex-wrap: wrap; }
        .chip {
            border: 1.5px solid #e0e0e0; border-radius: 8px;
            padding: 7px 14px; font-size: 13px; color: #555;
            cursor: pointer; transition: all 0.15s; user-select: none;
        }
        .chip:hover { border-color: #e94560; color: #e94560; }
        .chip.selected { border-color: #e94560; background: #fff0f3; color: #e94560; font-weight: 600; }

        .toggle-row { display: flex; gap: 7px; }
        .tbtn {
            flex: 1; padding: 9px; border: 1.5px solid #e0e0e0; border-radius: 10px;
            font-size: 13px; cursor: pointer; background: #fff; color: #555;
            font-weight: 500; transition: all 0.15s; text-align: center;
        }
        .tbtn:hover:not(:disabled) { border-color: #e94560; color: #e94560; }
        .tbtn.active { border-color: #1a1a2e; background: #1a1a2e; color: #fff; font-weight: 600; }
        .tbtn:disabled { opacity: 0.35; cursor: not-allowed; }

        .apply-btn {
            width: 100%; padding: 11px; background: #e94560; color: #fff;
            border: none; border-radius: 10px; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: background 0.2s;
        }
        .apply-btn:hover { background: #c73652; }

        /* ── Map ── */
        #map { position: fixed; top: 110px; bottom: 0; left: 0; right: 0; }

        /* ── Welcome splash ── */
        #welcome-splash {
            position: fixed; inset: 0; z-index: 3000;
            background: #0b0d18;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }
        #welcome-splash.fade-out { opacity: 0; visibility: hidden; pointer-events: none; }
        .splash-inner { text-align: center; max-width: 520px; padding: 48px 40px; }
        .splash-logo { font-size: 36px; font-weight: 800; letter-spacing: -1px; color: #fff; margin-bottom: 8px; }
        .splash-logo span { color: #e94560; }
        .splash-org-logo { max-height: 72px; max-width: 260px; object-fit: contain; margin-bottom: 28px; display: block; margin-left: auto; margin-right: auto; }
        .splash-greeting { font-size: 17px; color: #64748b; margin-bottom: 36px; }
        .splash-greeting strong { color: #e2e8f0; }
        .splash-title { font-size: 48px; font-weight: 800; color: #fff; line-height: 1.1; margin-bottom: 14px; }
        .splash-sub { font-size: 16px; color: #64748b; margin-bottom: 48px; line-height: 1.7; }
        .splash-btn {
            display: inline-block; background: #e94560; color: #fff; border: none;
            border-radius: 12px; padding: 16px 48px; font-size: 16px; font-weight: 700;
            cursor: pointer; letter-spacing: .3px; transition: background 0.15s, transform 0.1s;
        }
        .splash-btn:hover { background: #c73652; transform: translateY(-1px); }
        .splash-btn:disabled { background: #2d3149; color: #4d5780; cursor: not-allowed; transform: none; }
        .splash-divider { width: 56px; height: 3px; background: #e94560; border-radius: 2px; margin: 0 auto 32px; }
        .splash-progress-wrap {
            width: 100%; height: 4px; background: #1e2235; border-radius: 4px;
            margin-bottom: 14px; overflow: hidden;
        }
        .splash-progress-bar {
            height: 100%; width: 0%; background: linear-gradient(90deg, #e94560, #f97316);
            border-radius: 4px; transition: none;
        }
        .splash-progress-label { font-size: 12px; color: #4d5780; margin-bottom: 32px; letter-spacing: .3px; min-height: 18px; }
        @keyframes splashIn { from { opacity:0; transform: translateY(28px); } to { opacity:1; transform: translateY(0); } }
        .splash-inner { animation: splashIn 0.55s ease both; }

        /* ── Auth overlay ── */
        #auth-overlay {
            position: fixed; inset: 0; z-index: 2000;
            background: rgba(10, 11, 18, 0.82); backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
        }
        #auth-box {
            background: #1a1d27; border: 1px solid #2d3149; border-radius: 16px;
            padding: 40px 48px; text-align: center;
        }
        .auth-logo { font-size: 24px; font-weight: 700; margin-bottom: 12px; }
        .auth-logo span { color: #e94560; }
        #auth-box p { color: #94a3b8; font-size: 14px; margin-bottom: 24px; }
        #auth-box a {
            display: inline-block; background: #4f6ef7; color: #fff; text-decoration: none;
            padding: 10px 28px; border-radius: 8px; font-size: 14px; font-weight: 600;
            transition: background 0.15s;
        }
        #auth-box a:hover { background: #3b5be0; }

        /* ── Popup ── */
        .leaflet-popup-content-wrapper { border-radius: 14px; padding: 0; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.15); }
        .leaflet-popup-content { margin: 0; width: 290px !important; }
        .popup-body { padding: 16px; }
        .popup-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .popup-title { font-size: 13px; font-weight: 600; color: #1a1a2e; line-height: 1.4; flex: 1; }
        .popup-badge { font-size: 10px; font-weight: 700; padding: 3px 7px; border-radius: 6px; white-space: nowrap; margin-left: 8px; }
        .badge-owner { background: #e8f5e9; color: #2e7d32; }
        .badge-agent { background: #e3f2fd; color: #1565c0; }
        .popup-address { font-size: 11px; color: #888; margin-bottom: 6px; }
        .popup-meta { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
        .popup-chip { background: #f5f5f5; color: #555; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 500; }
        .popup-price { font-size: 20px; font-weight: 800; color: #e94560; margin-bottom: 14px; }
        .popup-price .period { font-size: 13px; font-weight: 400; color: #999; }
        .popup-contact { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; }
        .popup-contact-row { font-size: 12px; color: #555; display: flex; align-items: center; gap: 6px; }
        .popup-contact-row strong { color: #1a1a2e; }
        .popup-save-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
        .popup-save-row input {
            flex: 1; border: 1.5px solid #e0e0e0; border-radius: 8px;
            padding: 8px 10px; font-size: 13px; color: #1a1a2e; outline: none;
        }
        .popup-save-row input:focus { border-color: #e94560; }
        .popup-save-btn {
            padding: 8px 14px; background: #e94560; color: #fff; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
            white-space: nowrap; transition: background 0.2s; flex-shrink: 0;
        }
        .popup-save-btn:hover { background: #c73652; }
        .popup-save-btn.saved { background: #2e7d32; }
        .popup-link {
            display: block; background: #1a1a2e; color: #fff; text-align: center;
            padding: 10px; text-decoration: none; font-size: 13px; font-weight: 600;
            border-radius: 8px; transition: background 0.2s;
        }
        .popup-link:hover { background: #e94560; }
    </style>
</head>
<body>

@if(session('welcome_splash'))
@php
    $splash = session('welcome_splash');
    $sl = $splash['lang'] ?? 'en';
    $st = [
        'en' => [
            'welcome'  => 'Welcome,',
            'youre_in' => "You're in.",
            'joined'   => 'You\'ve joined',
            'explore'  => 'Start exploring listings on the map.',
            'init'     => 'Initializing map…',
            'loading'  => 'Loading listings…',
            'placing'  => 'Placing pins on map…',
            'fetching' => 'Fetching team data…',
            'district' => 'Filtering by district…',
            'almost'   => 'Almost ready…',
            'ready'    => 'Ready!',
            'open'     => 'Open the Map',
        ],
        'ka' => [
            'welcome'  => 'მოგესალმებით,',
            'youre_in' => 'მოგესალმებით!',
            'joined'   => 'თქვენ შეუერთდით',
            'explore'  => 'დაიწყეთ განცხადებების მოძებნა',
            'init'     => 'რუკის ინიციალიზაცია…',
            'loading'  => 'განცხადებები იტვირთება…',
            'placing'  => 'ნიშნები რუკაზე იდება…',
            'fetching' => 'გუნდის მონაცემები იტვირთება…',
            'district' => 'რაიონების ფილტრაცია…',
            'almost'   => 'თითქმის მზადაა…',
            'ready'    => 'მზადაა!',
            'open'     => 'დაწყება',
        ],
    ][$sl];
@endphp
<div id="welcome-splash">
    <div class="splash-inner">
        @if(!empty($splash['logo']))
            <img class="splash-org-logo" src="{{ Storage::url($splash['logo']) }}" alt="{{ $splash['org'] }}">
        @else
            <div class="splash-logo">MYHOME<span>-MAP</span></div>
        @endif
        <div class="splash-greeting">{{ $st['welcome'] }} <strong>{{ $splash['name'] }}</strong></div>
        <div class="splash-divider"></div>
        <div class="splash-title">{{ $st['youre_in'] }}</div>
        <div class="splash-sub">{{ $st['joined'] }} <strong style="color:#e2e8f0">{{ $splash['org'] }}</strong>.<br>{{ $st['explore'] }}</div>
        <div class="splash-progress-wrap">
            <div class="splash-progress-bar" id="splash-bar"></div>
        </div>
        <div class="splash-progress-label" id="splash-label">{{ $st['init'] }}</div>
        <button class="splash-btn" id="splash-btn" onclick="dismissSplash()" disabled>{{ $st['open'] }}</button>
    </div>
</div>
<script>
window.__splashSteps = [
    { pct: 15,  ms: 700,  text: '{{ $st['loading'] }}' },
    { pct: 35,  ms: 1000, text: '{{ $st['placing'] }}' },
    { pct: 58,  ms: 900,  text: '{{ $st['fetching'] }}' },
    { pct: 78,  ms: 800,  text: '{{ $st['district'] }}' },
    { pct: 92,  ms: 700,  text: '{{ $st['almost'] }}' },
    { pct: 100, ms: 500,  text: '{{ $st['ready'] }}' },
];
</script>
@endif

<div id="header">
    @auth
        @if(auth()->user()->inOrg() && auth()->user()->organization->logo)
            <img id="org-logo" src="{{ Storage::url(auth()->user()->organization->logo) }}" alt="{{ auth()->user()->organization->name }}">
        @else
            <h1>MYHOME<span>-MAP</span></h1>
        @endif
    @else
        <h1>MYHOME<span>-MAP</span></h1>
    @endauth
    <div id="status-bar">
        <div id="live-dot"></div>
        <span id="status-text"></span>
    </div>
    <button id="saved-btn" onclick="exportSaved()">⬇ Export (<span id="saved-count">0</span>)</button>

    @auth
    <div id="user-menu">
        <button id="user-trigger" onclick="toggleUserMenu()">
            <span id="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <span id="user-name">{{ auth()->user()->name }}</span>
            <span style="font-size:10px;opacity:.6">▼</span>
        </button>
        <div id="user-dropdown">
            <div id="user-info">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->email }}</span>
                <span class="user-plan {{ auth()->user()->isPro() ? 'plan-pro' : 'plan-free' }}">
                    {{ auth()->user()->isPro() ? 'Pro' : 'Free' }}
                </span>
            </div>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.index') }}">⚙ Admin Panel</a>
            @endif
            @if(auth()->user()->isCeo())
                <a href="/dashboard">📊 Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Sign out</button>
            </form>
        </div>
    </div>
    @endauth

    <div id="lang-switcher">
        <button class="lang-btn active" onclick="setLang('en')">EN</button>
        <button class="lang-btn" onclick="setLang('ru')">RU</button>
        <button class="lang-btn" onclick="setLang('ka')">KA</button>
    </div>
</div>

<!-- Filter bar -->
<div id="filter-bar">
    <button class="fpill" id="pill-price"    onclick="toggleDrop('price')"><span data-i18n="pill_price">Price</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-period"   onclick="toggleDrop('period')"><span data-i18n="pill_period">Period</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-rooms"    onclick="toggleDrop('rooms')"><span data-i18n="pill_rooms">Rooms</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-bedrooms" onclick="toggleDrop('bedrooms')"><span data-i18n="pill_bedrooms">Bedrooms</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-building" onclick="toggleDrop('building')" disabled style="opacity:.35;cursor:not-allowed;"><span data-i18n="pill_building">Building</span> <span class="arrow">▼</span></button>
</div>

<!-- Price dropdown -->
<div class="fdrop" id="drop-price">
    <span class="fdrop-label" data-i18n="label_price">Price (USD / month)</span>
    <div class="price-row">
        <input type="number" id="price-min" data-i18n-placeholder="min_price" placeholder="Min $" min="0" step="50">
        <span>—</span>
        <input type="number" id="price-max" data-i18n-placeholder="max_price" placeholder="Max $" min="0" step="50">
    </div>
    <button class="apply-btn" data-i18n="apply" onclick="applyAndClose('price')">Apply</button>
</div>

<!-- Period dropdown -->
<div class="fdrop" id="drop-period">
    <span class="fdrop-label" data-i18n="label_period">Rent Period</span>
    <div class="toggle-row" id="rent-type-group">
        <button class="tbtn active" data-value="all"     data-i18n="all">All</button>
        <button class="tbtn"        data-value="monthly" data-i18n="monthly">Monthly</button>
        <button class="tbtn"        data-value="daily"   data-i18n="daily">Daily</button>
    </div>
    <button class="apply-btn" data-i18n="apply" onclick="applyAndClose('period')">Apply</button>
</div>

<!-- Rooms dropdown -->
<div class="fdrop" id="drop-rooms">
    <span class="fdrop-label" data-i18n="label_rooms">Total Rooms</span>
    <div class="chip-row">
        <span class="chip" data-room="1">1</span>
        <span class="chip" data-room="2">2</span>
        <span class="chip" data-room="3">3</span>
        <span class="chip" data-room="4">4</span>
        <span class="chip" data-room="5">5+</span>
    </div>
    <button class="apply-btn" data-i18n="apply" onclick="applyAndClose('rooms')">Apply</button>
</div>

<!-- Bedrooms dropdown -->
<div class="fdrop" id="drop-bedrooms">
    <span class="fdrop-label" data-i18n="label_bedrooms">Bedrooms</span>
    <div class="chip-row">
        <span class="chip" data-bedroom="1">1</span>
        <span class="chip" data-bedroom="2">2</span>
        <span class="chip" data-bedroom="3">3</span>
        <span class="chip" data-bedroom="4">4</span>
        <span class="chip" data-bedroom="5">5+</span>
    </div>
    <button class="apply-btn" data-i18n="apply" onclick="applyAndClose('bedrooms')">Apply</button>
</div>

<!-- Building dropdown -->
<div class="fdrop" id="drop-building">
    <span class="fdrop-label" data-i18n="label_building">Building Type</span>
    <div class="toggle-row" id="building-group">
        <button class="tbtn active" data-value="all" data-i18n="all">All</button>
        <button class="tbtn"        data-value="new" data-i18n="newly_built">Newly Built</button>
    </div>
    <button class="apply-btn" data-i18n="apply" onclick="applyAndClose('building')">Apply</button>
</div>

<div id="map"></div>

@guest
<div id="auth-overlay">
    <div id="auth-box">
        <div class="auth-logo">MYHOME<span>-MAP</span></div>
        <p>Sign in to view listings on the map.</p>
        <a href="/login">Sign in</a>
    </div>
</div>
@endguest

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
// ── Splash ────────────────────────────────────────────────────────────────
(function () {
    const bar   = document.getElementById('splash-bar');
    const label = document.getElementById('splash-label');
    const btn   = document.getElementById('splash-btn');
    if (!bar) return;

    const steps = window.__splashSteps || [
        { pct: 15,  ms: 700,  text: 'Loading listings…' },
        { pct: 35,  ms: 1000, text: 'Placing pins on map…' },
        { pct: 58,  ms: 900,  text: 'Fetching team data…' },
        { pct: 78,  ms: 800,  text: 'Filtering by district…' },
        { pct: 92,  ms: 700,  text: 'Almost ready…' },
        { pct: 100, ms: 500,  text: 'Ready!' },
    ];

    let elapsed = 0;
    steps.forEach(({ pct, ms, text }) => {
        setTimeout(() => {
            bar.style.transition = `width ${ms}ms cubic-bezier(.4,0,.2,1)`;
            bar.style.width = pct + '%';
            label.textContent = text;
            if (pct === 100) {
                setTimeout(() => { btn.disabled = false; }, ms);
            }
        }, elapsed);
        elapsed += ms;
    });
})();

function dismissSplash() {
    const el = document.getElementById('welcome-splash');
    if (!el) return;
    el.classList.add('fade-out');
    setTimeout(() => el.remove(), 650);
}

// ── Translations ──────────────────────────────────────────────────────────

const translations = {
    en: {
        pill_price:    'Price',
        pill_period:   'Period',
        pill_rooms:    'Rooms',
        pill_bedrooms: 'Bedrooms',
        pill_building: 'Building',
        label_price:    'Price (USD / month)',
        label_period:   'Rent Period',
        label_rooms:    'Total Rooms',
        label_bedrooms: 'Bedrooms',
        label_building: 'Building Type',
        all:         'All',
        monthly:     'Monthly',
        daily:       'Daily',
        newly_built: 'Newly Built',
        apply:       'Apply',
        owner:       'Owner',
        agency:      'Agency',
        view:        'View on myhome.ge →',
        loading:     'Loading...',
        n_found:     (n) => `${n} listing${n !== 1 ? 's' : ''} found`,
        finding:     (n) => `${n} found...`,
        error:       'Error — try again',
        min_price:   'Min $',
        max_price:   'Max $',
        rooms_suffix:    'rm',
        bedrooms_suffix: 'bd',
    },
    ru: {
        pill_price:    'Цена',
        pill_period:   'Период',
        pill_rooms:    'Комнаты',
        pill_bedrooms: 'Спальни',
        pill_building: 'Тип дома',
        label_price:    'Цена (USD / месяц)',
        label_period:   'Период аренды',
        label_rooms:    'Всего комнат',
        label_bedrooms: 'Спальни',
        label_building: 'Тип здания',
        all:         'Все',
        monthly:     'Помесячно',
        daily:       'Посуточно',
        newly_built: 'Новостройка',
        apply:       'Применить',
        owner:       'Хозяин',
        agency:      'Агентство',
        view:        'Открыть на myhome.ge →',
        loading:     'Загрузка...',
        n_found:     (n) => `Найдено: ${n}`,
        finding:     (n) => `Найдено: ${n}...`,
        error:       'Ошибка — попробуйте снова',
        min_price:   'Мин $',
        max_price:   'Макс $',
        rooms_suffix:    'комн.',
        bedrooms_suffix: 'сп.',
    },
    ka: {
        pill_price:    'ფასი',
        pill_period:   'პერიოდი',
        pill_rooms:    'ოთახები',
        pill_bedrooms: 'საძინებლები',
        pill_building: 'შენობა',
        label_price:    'ფასი (USD / თვეში)',
        label_period:   'იჯარის პერიოდი',
        label_rooms:    'ოთახების რაოდენობა',
        label_bedrooms: 'საძინებლები',
        label_building: 'შენობის ტიპი',
        all:         'ყველა',
        monthly:     'თვიური',
        daily:       'დღიური',
        newly_built: 'ახალაშენებული',
        apply:       'გამოყენება',
        owner:       'მფლობელი',
        agency:      'სააგენტო',
        view:        'გახსნა myhome.ge-ზე →',
        loading:     'იტვირთება...',
        n_found:     (n) => `ნაპოვნია: ${n}`,
        finding:     (n) => `ნაპოვნია: ${n}...`,
        error:       'შეცდომა — სცადეთ თავიდან',
        min_price:   'მინ $',
        max_price:   'მაქს $',
        rooms_suffix:    'ოთ.',
        bedrooms_suffix: 'სძ.',
    },
};

let lang = localStorage.getItem('lang') || 'en';

function setLang(l) {
    lang = l;
    localStorage.setItem('lang', l);
    document.querySelectorAll('.lang-btn').forEach(b => b.classList.toggle('active', b.textContent === l.toUpperCase()));
    document.documentElement.lang = l;

    // Update all data-i18n elements
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.dataset.i18n;
        if (typeof translations[l][key] === 'string') el.textContent = translations[l][key];
    });

    // Update placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.dataset.i18nPlaceholder;
        el.placeholder = translations[l][key] || el.placeholder;
    });

    updatePillLabels();
}

const tr = (key, ...args) => {
    const val = translations[lang][key];
    return typeof val === 'function' ? val(...args) : (val ?? key);
};

// ── Map setup ─────────────────────────────────────────────────────────────

const map = L.map('map').setView([41.6941, 44.8337], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
}).addTo(map);

let markers = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 60 });
map.addLayer(markers);
let activeStream = null;
let openDrop     = null;
const allListings = new Map(); // listing_id → listing data, used for CSV export

// ── Dropdown logic ────────────────────────────────────────────────────────

function toggleDrop(name) {
    const drop = document.getElementById('drop-' + name);
    const pill = document.getElementById('pill-' + name);
    if (openDrop && openDrop !== name) closeDrop(openDrop);
    if (drop.classList.contains('open')) { closeDrop(name); return; }
    const rect = pill.getBoundingClientRect();
    drop.style.left = Math.min(rect.left, window.innerWidth - 300) + 'px';
    drop.classList.add('open');
    pill.classList.add('open');
    openDrop = name;
}

function closeDrop(name) {
    document.getElementById('drop-' + name)?.classList.remove('open');
    document.getElementById('pill-' + name)?.classList.remove('open');
    if (openDrop === name) openDrop = null;
}

function applyAndClose(name) {
    closeDrop(name);
    updatePillLabels();
    startStream();
}

document.addEventListener('click', e => {
    if (openDrop && !e.target.closest('.fdrop') && !e.target.closest('.fpill')) {
        closeDrop(openDrop);
    }
});

// ── Toggles & chips ───────────────────────────────────────────────────────

document.querySelectorAll('.toggle-row').forEach(group => {
    group.querySelectorAll('.tbtn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.disabled) return;
            group.querySelectorAll('.tbtn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
});

document.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', () => chip.classList.toggle('selected'));
});

// ── Pill labels ───────────────────────────────────────────────────────────

function updatePillLabels() {
    const priceMin = document.getElementById('price-min').value;
    const priceMax = document.getElementById('price-max').value;
    setPill('price', priceMin || priceMax
        ? (priceMin ? '$' + priceMin : '') + ' – ' + (priceMax ? '$' + priceMax : '')
        : null, 'pill_price');

    const period = document.querySelector('#rent-type-group .tbtn.active')?.dataset.value;
    setPill('period', period !== 'all' ? tr(period) : null, 'pill_period');

    const rooms = [...document.querySelectorAll('.chip[data-room].selected')].map(c => c.dataset.room);
    setPill('rooms', rooms.length ? rooms.join(', ') + ' ' + tr('rooms_suffix') : null, 'pill_rooms');

    const beds = [...document.querySelectorAll('.chip[data-bedroom].selected')].map(c => c.dataset.bedroom);
    setPill('bedrooms', beds.length ? beds.join(', ') + ' ' + tr('bedrooms_suffix') : null, 'pill_bedrooms');

    const building = document.querySelector('#building-group .tbtn.active')?.dataset.value;
    setPill('building', building === 'new' ? tr('newly_built') : null, 'pill_building');
}

function setPill(name, value, labelKey) {
    const pill = document.getElementById('pill-' + name);
    pill.innerHTML = (value ?? tr(labelKey)) + ' <span class="arrow">▼</span>';
    pill.classList.toggle('active', !!value);
    pill.onclick = () => toggleDrop(name);
}

// ── Stream ────────────────────────────────────────────────────────────────

function setStatus(text, live = false) {
    document.getElementById('status-text').textContent = text;
    document.getElementById('live-dot').style.display = live ? 'block' : 'none';
}

function startStream() {
    if (activeStream) { activeStream.abort(); activeStream = null; }
    markers.clearLayers();
    allListings.clear();
    setStatus(tr('loading'), true);

    const params = new URLSearchParams();
    const priceMin = document.getElementById('price-min').value;
    const priceMax = document.getElementById('price-max').value;
    if (priceMin) params.set('price_min', priceMin);
    if (priceMax) params.set('price_max', priceMax);

    const rooms = [...document.querySelectorAll('.chip[data-room].selected')].map(c => c.dataset.room);
    if (rooms.length) params.set('rooms', rooms.join(','));

    const bedrooms = [...document.querySelectorAll('.chip[data-bedroom].selected')].map(c => c.dataset.bedroom);
    if (bedrooms.length) params.set('bedrooms', bedrooms.join(','));

    const rentType = document.querySelector('#rent-type-group .tbtn.active')?.dataset.value;
    if (rentType && rentType !== 'all') params.set('rent_type', rentType);

    const building = document.querySelector('#building-group .tbtn.active')?.dataset.value;
    if (building === 'new') params.set('newly_built', '1');

    params.set('poster_type', 'owner');

    const controller = new AbortController();
    activeStream = controller;

    fetch('/api/stream?' + params.toString(), { signal: controller.signal })
        .then(r => r.json())
        .then(data => {
            activeStream = null;
            data.listings.forEach(l => addMarker(l));
            setStatus(tr('n_found', data.total), false);
        })
        .catch(err => {
            if (err.name !== 'AbortError') setStatus(tr('error'), false);
            activeStream = null;
        });
}

function addMarker(l) {
    allListings.set(String(l.id), l);

    const marker = L.marker([l.lat, l.lng]);
    const price  = l.price ? `$${Number(l.price).toLocaleString()}` : 'N/A';
    const period = l.period ?? (l.rent_type === 'daily' ? '/day' : '/month');
    const posterLabel = l.poster_type === 'owner' ? tr('owner') : tr('agency');
    const posterClass = l.poster_type === 'owner' ? 'badge-owner' : 'badge-agent';
    const chips = [
        l.rooms    ? `${l.rooms} ${tr('rooms_suffix')}`    : null,
        l.bedrooms ? `${l.bedrooms} ${tr('bedrooms_suffix')}` : null,
        l.area     ? l.area : null,
        l.district || null,
    ].filter(Boolean).map(t => `<span class="popup-chip">${t}</span>`).join('');

    const contactHtml = (l.owner_name || l.phone) ? `
        <div class="popup-contact">
            ${l.owner_name ? `<div class="popup-contact-row">👤 <strong>${l.owner_name}</strong></div>` : ''}
            ${l.phone ? `<div class="popup-contact-row">📞 <strong>${l.phone}</strong></div>` : ''}
        </div>` : '';

    const lid          = String(l.id);
    const alreadySaved = mySaves.has(lid);
    const savedByTeam  = teamSaves.get(lid);
    const savedEntry   = mySaves.get(lid);

    const teamBadge = savedByTeam
        ? `<div style="font-size:11px;color:#f59e0b;margin-bottom:8px">⭐ Saved by ${savedByTeam}</div>`
        : '';

    marker.bindPopup(`
        <div class="popup-body">
            <div class="popup-header">
                <div class="popup-title">${l.title || 'Apartment for Rent'}</div>
                <span class="popup-badge ${posterClass}">${posterLabel}</span>
            </div>
            ${l.address ? `<div class="popup-address">📍 ${l.address}</div>` : ''}
            ${l.updated_at ? `<div class="popup-address">🕐 ${l.updated_at}</div>` : ''}
            ${chips ? `<div class="popup-meta">${chips}</div>` : ''}
            <div class="popup-price">${price}<span class="period">${period}</span></div>
            ${contactHtml}
            ${teamBadge}
            <div class="popup-save-row">
                <input type="number" id="my-price-${lid}" placeholder="My price $"
                    value="${alreadySaved ? (savedEntry?.myPrice || '') : ''}" min="0" step="50">
                <button class="popup-save-btn ${alreadySaved ? 'saved' : ''}"
                    id="save-btn-${lid}"
                    onclick="toggleSave(${JSON.stringify(l)})">
                    ${alreadySaved ? '✓ Saved' : '+ Save'}
                </button>
            </div>
            <a class="popup-link" href="${l.url}" target="_blank" rel="noopener">${tr('view')}</a>
        </div>
    `, { maxWidth: 310 });

    markers.addLayer(marker);
    if (markers.getLayers().length === 1) map.setView([l.lat, l.lng], 13);
}

// ── Saved list ────────────────────────────────────────────────────────────────

const isAuthed   = !!document.querySelector('meta[name="user-authed"]');
const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';

// listing_id → {myPrice} for current user; populated on load
const mySaves   = new Map();
// listing_id → employee name; populated on load (team saves)
const teamSaves = new Map();

function updateSavedBtn() {
    const count = mySaves.size;
    document.getElementById('saved-count').textContent = count;
    document.getElementById('saved-btn').style.display = count ? 'block' : 'none';
}

async function loadSaves() {
    if (!isAuthed) return;
    try {
        const [mine, team] = await Promise.all([
            fetch('/api/my-saves').then(r => r.json()),
            fetch('/api/team-saves').then(r => r.json()),
        ]);
        Object.entries(mine).forEach(([id, price]) => mySaves.set(id, { myPrice: price }));
        Object.entries(team).forEach(([id, name]) => teamSaves.set(id, name));
        updateSavedBtn();
    } catch (e) {}
}

async function toggleSave(l) {
    if (!isAuthed) { window.location = '/login'; return; }

    const myPrice = document.getElementById('my-price-' + l.id)?.value || '';
    const btn     = document.getElementById('save-btn-' + l.id);

    const res  = await fetch('/api/save', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: l.id, listing_snapshot: l, my_price: myPrice || null }),
    });
    const data = await res.json();

    if (data.saved) {
        mySaves.set(l.id, { myPrice });
        if (btn) { btn.textContent = '✓ Saved'; btn.classList.add('saved'); }
    } else {
        mySaves.delete(l.id);
        if (btn) { btn.textContent = '+ Save'; btn.classList.remove('saved'); }
    }
    updateSavedBtn();
}

function exportSaved() {
    if (!mySaves.size) return;

    const rows = [['ID', 'Title', 'Address', 'Owner', 'Phone', 'Original Price', 'My Price', 'Rent Type', 'Rooms', 'Bedrooms', 'Area', 'District', 'URL']];

    mySaves.forEach((entry, id) => {
        // Find the listing data from markers (stored on the marker object)
        const l = allListings.get(id);
        if (!l) return;
        rows.push([
            l.id, l.title, l.address, l.owner_name ?? '', l.phone ?? '',
            l.price, entry.myPrice ?? '', l.rent_type, l.rooms ?? '',
            l.bedrooms ?? '', l.area ?? '', l.district ?? '', l.url,
        ]);
    });

    const csv = '﻿' + rows.map(r =>
        r.map(v => '"' + String(v ?? '').replace(/"/g, '""') + '"').join(',')
    ).join('\r\n');

    const a    = document.createElement('a');
    a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = 'my-listings-' + new Date().toISOString().slice(0, 10) + '.csv';
    a.click();
}

// ── User menu ─────────────────────────────────────────────────────────────────

function toggleUserMenu() {
    document.getElementById('user-dropdown')?.classList.toggle('open');
}

document.addEventListener('click', e => {
    if (!e.target.closest('#user-menu')) {
        document.getElementById('user-dropdown')?.classList.remove('open');
    }
});

// ── Init ──────────────────────────────────────────────────────────────────

window.addEventListener('load', () => {
    setLang(lang);
    loadSaves().then(() => startStream());
});
</script>
</body>
</html>
