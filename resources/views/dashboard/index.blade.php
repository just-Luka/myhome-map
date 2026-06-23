<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — {{ $org->name }}</title>
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

        /* ── Copy button ── */
        .copy-btn { padding: 3px 8px; border-radius: 5px; background: #1e2235; border: none; color: #94a3b8; font-size: 11px; cursor: pointer; }
        .copy-btn:hover { background: #2d3149; color: #fff; }

        /* ── Lang switcher ── */
        #lang-switcher { display: flex; border: 1.5px solid #1e2235; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
        .lang-btn { padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: transparent; color: #64748b; transition: all 0.15s; }
        .lang-btn.active { background: #e94560; color: #fff; }
        .lang-btn:hover:not(.active) { color: #e2e8f0; }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-logo">
        MYHOME-<span>MAP</span>
        <div class="sidebar-org">{{ $org->name }}</div>
    </div>

    @php $seatPct = $members->count() / max($org->user_limit, 1) * 100; @endphp
    <div class="seats-bar">
        <div class="seats-label">
            <span data-i18n="seats">Seats</span>
            <span>{{ $members->count() }} / {{ $org->user_limit }}</span>
        </div>
        <div class="seats-track"><div class="seats-fill" style="width:{{ min($seatPct, 100) }}%"></div></div>
    </div>

    <div class="sidebar-label" data-i18n="overview">Overview</div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="active">📊 <span data-i18n="dashboard">Dashboard</span></a>
    </nav>

    <div class="sidebar-label" data-i18n="actions">Actions</div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard.export') }}">⬇ <span data-i18n="export_all">Export All</span></a>
        <a href="/" target="_blank">🗺 <span data-i18n="live_map">Live Map</span></a>
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
        <h1 data-i18n="team_dashboard">Team Dashboard</h1>
        <form method="POST" action="{{ route('dashboard.invite') }}">
            @csrf
            <button class="btn btn-primary btn-sm" type="submit">+ <span data-i18n="invite_employee">Invite Employee</span></button>
        </form>
        <button id="theme-toggle" onclick="toggleTheme()" title="Switch theme">🌙</button>
        <div id="lang-switcher">
            <button class="lang-btn active" onclick="setLang('en')">EN</button>
            <button class="lang-btn" onclick="setLang('ka')">KA</button>
        </div>
    </div>

    <div id="content">

        @if(session('employee_link'))
            <div class="alert alert-success">
                ✓ <span data-i18n="invite_ready">Employee invite link (expires in 7 days):</span><br><br>
                <strong id="invite-url">{{ session('employee_link') }}</strong>
                <button class="copy-btn" style="margin-left:10px" onclick="copyInviteLink(document.getElementById('invite-url').textContent)"><span data-i18n="copy">Copy</span></button>
            </div>
        @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif

        {{-- Stats --}}
        <div class="stats">
            <div class="stat">
                <div class="val">{{ $allSaves->count() }}</div>
                <div class="lbl" data-i18n="total_saves">Total Saves</div>
            </div>
            <div class="stat">
                <div class="val">{{ $weekSaves }}</div>
                <div class="lbl" data-i18n="this_week">This Week</div>
                <div class="sub">{{ $todaySaves }} <span data-i18n="today">today</span></div>
            </div>
            <div class="stat">
                <div class="val">{{ $members->count() }}</div>
                <div class="lbl" data-i18n="team_members">Team Members</div>
                <div class="sub">{{ $org->user_limit - $members->count() }} <span data-i18n="seats_left">seats left</span></div>
            </div>
            <div class="stat">
                <div class="val">{{ $avgDiscount !== null ? $avgDiscount . '%' : '—' }}</div>
                <div class="lbl" data-i18n="avg_discount">Avg Discount</div>
                <div class="sub" data-i18n="from_orig">from original price</div>
            </div>
        </div>

        <div class="grid-3-1">

            {{-- Left column --}}
            <div>
                {{-- Activity table --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title" data-i18n="saved_listings">Saved Listings</div>
                        <div class="filter-row">
                            <form method="GET" style="display:flex;gap:8px;align-items:center">
                                <select name="employee" onchange="this.form.submit()">
                                    <option value="" data-i18n="all_employees">All employees</option>
                                    @foreach($members as $m)
                                        <option value="{{ $m->id }}" {{ request('employee') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @if(request('employee'))
                                <a href="{{ route('dashboard.export', ['employee' => request('employee')]) }}" class="btn btn-green btn-sm">⬇ Export</a>
                            @else
                                <a href="{{ route('dashboard.export') }}" class="btn btn-green btn-sm">⬇ Export</a>
                            @endif
                        </div>
                    </div>

                    @if($saves->isEmpty())
                        <div class="empty" data-i18n="no_listings">No listings saved yet.</div>
                    @else
                    <table>
                        <thead>
                            <tr>
                                <th data-i18n="th_employee">Employee</th>
                                <th data-i18n="th_listing">Listing</th>
                                <th data-i18n="th_contact">Contact</th>
                                <th data-i18n="th_price">Price</th>
                                <th data-i18n="th_when">When</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($saves as $s)
                        @php
                            $l    = $s->listing_snapshot;
                            $orig = (float)($l['price'] ?? 0);
                            $mine = (float)($s->my_price ?? 0);
                            $disc = ($mine && $orig && $orig > $mine) ? round((($orig - $mine) / $orig) * 100) : null;
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-{{ $s->user->role }}">{{ $s->user->name }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:13px;margin-bottom:3px">{{ $l['title'] ?? '—' }}</div>
                                <div style="font-size:11px;color:#4d5780">{{ $l['address'] ?? '' }}</div>
                                <div style="display:flex;gap:6px;margin-top:4px;flex-wrap:wrap">
                                    @if(!empty($l['rooms'])) <span style="font-size:10px;background:#1e2235;padding:2px 6px;border-radius:4px;color:#94a3b8">{{ $l['rooms'] }} <span data-i18n="rooms">rooms</span></span> @endif
                                    @if(!empty($l['area']))  <span style="font-size:10px;background:#1e2235;padding:2px 6px;border-radius:4px;color:#94a3b8">{{ $l['area'] }}</span> @endif
                                    @if(!empty($l['district'])) <span style="font-size:10px;background:#1e2235;padding:2px 6px;border-radius:4px;color:#94a3b8">{{ $l['district'] }}</span> @endif
                                </div>
                                <a href="{{ $l['url'] ?? '#' }}" target="_blank" style="font-size:11px;color:#4f6ef7;text-decoration:none;display:block;margin-top:4px">myhome.ge →</a>
                            </td>
                            <td>
                                @if(!empty($l['owner_name'])) <div style="font-size:12px;font-weight:600">{{ $l['owner_name'] }}</div> @endif
                                @if(!empty($l['phone']))      <div style="font-size:12px;color:#94a3b8">📞 {{ $l['phone'] }}</div> @endif
                            </td>
                            <td style="white-space:nowrap">
                                @if($mine)
                                    <div class="price-my">${{ number_format($mine, 0) }}</div>
                                    <div class="price-orig">${{ number_format($orig, 0) }}</div>
                                    @if($disc) <div class="discount">-{{ $disc }}%</div> @endif
                                @else
                                    <div style="color:#94a3b8">${{ number_format($orig, 0) }}</div>
                                @endif
                            </td>
                            <td style="color:#4d5780;font-size:12px;white-space:nowrap">{{ $s->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            {{-- Right column --}}
            <div>

                {{-- Employee breakdown --}}
                <div class="card">
                    <div class="card-title" style="margin-bottom:14px" data-i18n="team_perf">Team Performance</div>
                    @if($employeeStats->isEmpty())
                        <div class="empty" data-i18n="no_members">No members yet.</div>
                    @else
                    @foreach($employeeStats as $w)
                    <div class="employee-row">
                        <div class="employee-avatar">{{ strtoupper(substr($w['name'], 0, 1)) }}</div>
                        <div class="employee-info">
                            <div class="employee-name">{{ $w['name'] }}</div>
                            <div class="employee-meta">
                                <span class="badge badge-{{ $w['role'] }}" style="font-size:10px;padding:1px 6px">{{ $w['role'] }}</span>
                                @if($w['last_save'])
                                    <span style="margin-left:6px"><span data-i18n="last_save">Last:</span> {{ $w['last_save']->diffForHumans() }}</span>
                                @else
                                    <span style="margin-left:6px" data-i18n="no_saves_yet">No saves yet</span>
                                @endif
                            </div>
                        </div>
                        <div class="employee-count">{{ $w['count'] }}</div>
                    </div>
                    @endforeach
                    @endif
                </div>

                {{-- Pending invites --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title" data-i18n="pending_invites">Pending Invites</div>
                        <form method="POST" action="{{ route('dashboard.invite') }}">
                            @csrf
                            <button class="btn btn-ghost btn-sm" type="submit">+ <span data-i18n="new">New</span></button>
                        </form>
                    </div>
                    @if($pendingInvites->isEmpty())
                        <div class="empty" style="padding:16px" data-i18n="no_invites">No pending invites.</div>
                    @else
                    @foreach($pendingInvites as $inv)
                    <div class="invite-row">
                        <div class="invite-token">{{ route('invite.show', $inv->token) }}</div>
                        <div class="invite-exp">{{ $inv->expires_at?->diffForHumans() ?? '∞' }}</div>
                        <button class="copy-btn" onclick="copyInviteLink('{{ route('invite.show', $inv->token) }}')"><span data-i18n="copy">Copy</span></button>
                    </div>
                    @endforeach
                    @endif
                </div>

                {{-- Logo --}}
                <div class="card">
                    <div class="card-title" style="margin-bottom:14px" data-i18n="org_logo">Organization Logo</div>
                    @if($org->logo)
                    <div style="margin-bottom:14px;text-align:center">
                        <img src="{{ Storage::url($org->logo) }}" alt="Logo"
                             style="max-height:60px;max-width:160px;object-fit:contain;border-radius:6px">
                    </div>
                    @endif
                    <form method="POST" action="{{ route('dashboard.logo') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="logo" accept="image/*" required
                               style="width:100%;background:#0b0d14;border:1px solid #1e2235;border-radius:8px;padding:8px;color:#e2e8f0;font-size:13px;margin-bottom:10px">
                        @error('logo') <p style="color:#fca5a5;font-size:12px;margin-bottom:8px">{{ $message }}</p> @enderror
                        <button class="btn btn-primary" style="width:100%" type="submit" data-i18n="upload_logo">Upload Logo</button>
                    </form>
                    @if($org->logo)
                    <form method="POST" action="{{ route('dashboard.logo.remove') }}" style="margin-top:8px">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost" style="width:100%;font-size:12px" type="submit" data-i18n="remove_logo">Remove Logo</button>
                    </form>
                    @endif
                </div>

                {{-- Settings --}}
                <div class="card">
                    <div class="card-title" style="margin-bottom:14px" data-i18n="settings">Settings</div>
                    <form method="POST" action="{{ route('dashboard.settings') }}">
                        @csrf @method('PATCH')
                        <div class="toggle-row">
                            <div>
                                <span data-i18n="show_team_saves">Show team saves to employees</span>
                                <small data-i18n="show_team_saves_hint">Employees see "Saved by [name]" on pins</small>
                            </div>
                            <input type="checkbox" name="show_team_saves" value="1"
                                {{ $org->show_team_saves ? 'checked' : '' }} onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
// ── Theme ─────────────────────────────────────────────────────────────────
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

// ── Language ───────────────────────────────────────────────────────────────
const translations = {
    en: {
        invite_ready: 'Employee invite link (expires in 7 days):',
        seats: 'Seats', overview: 'Overview', dashboard: 'Dashboard',
        actions: 'Actions', export_all: 'Export All', live_map: 'Live Map',
        sign_out: 'Sign out', team_dashboard: 'Team Dashboard',
        invite_employee: 'Invite Employee',
        total_saves: 'Total Saves', this_week: 'This Week', today: 'today',
        team_members: 'Team Members', seats_left: 'seats left',
        avg_discount: 'Avg Discount', from_orig: 'from original price',
        saved_listings: 'Saved Listings', all_employees: 'All employees',
        no_listings: 'No listings saved yet.',
        th_employee: 'Employee', th_listing: 'Listing', th_contact: 'Contact',
        th_price: 'Price', th_when: 'When', rooms: 'rooms',
        team_perf: 'Team Performance', no_members: 'No members yet.',
        last_save: 'Last:', no_saves_yet: 'No saves yet',
        pending_invites: 'Pending Invites', new: 'New', no_invites: 'No pending invites.',
        copy: 'Copy', org_logo: 'Organization Logo',
        upload_logo: 'Upload Logo', remove_logo: 'Remove Logo',
        settings: 'Settings',
        show_team_saves: 'Show team saves to employees',
        show_team_saves_hint: 'Employees see "Saved by [name]" on pins',
    },
    ka: {
        invite_ready: 'თანამშრომლის მოწვევის ლინკი (7 დღე მოქმედებს):',
        seats: 'ადგილები', overview: 'მიმოხილვა', dashboard: 'პანელი',
        actions: 'მოქმედებები', export_all: 'ექსპორტი', live_map: 'რუქა',
        sign_out: 'გასვლა', team_dashboard: 'გუნდის პანელი',
        invite_employee: 'თანამშრომლის მოწვევა',
        total_saves: 'სულ შენახული', this_week: 'ამ კვირაში', today: 'დღეს',
        team_members: 'გუნდის წევრები', seats_left: 'ადგილი დარჩა',
        avg_discount: 'საშ. ფასდაკლება', from_orig: 'საწყისი ფასიდან',
        saved_listings: 'შენახული განცხადებები', all_employees: 'ყველა თანამშრომელი',
        no_listings: 'განცხადებები ჯერ არ არის შენახული.',
        th_employee: 'თანამშრომელი', th_listing: 'განცხადება', th_contact: 'კონტაქტი',
        th_price: 'ფასი', th_when: 'როდის', rooms: 'ოთახი',
        team_perf: 'გუნდის შედეგები', no_members: 'წევრები ჯერ არ არიან.',
        last_save: 'ბოლო:', no_saves_yet: 'ჯერ არ შენახულა',
        pending_invites: 'მოლოდინის მოწვევები', new: 'ახალი', no_invites: 'მოლოდინის მოწვევები არ არის.',
        copy: 'კოპირება', org_logo: 'ორგანიზაციის ლოგო',
        upload_logo: 'ლოგოს ატვირთვა', remove_logo: 'ლოგოს წაშლა',
        settings: 'პარამეტრები',
        show_team_saves: 'გუნდის შენახვების ჩვენება',
        show_team_saves_hint: 'თანამშრომლები ხედავენ "შენახულია [სახელი]-ის მიერ" ნიშანზე',
    },
};

function setLang(lang) {
    localStorage.setItem('dashboard_lang', lang);
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.dataset.i18n;
        if (translations[lang][key] !== undefined) el.textContent = translations[lang][key];
    });
    document.querySelectorAll('.lang-btn').forEach(b => {
        b.classList.toggle('active', b.textContent.toLowerCase() === lang);
    });
}

function copyInviteLink(baseUrl) {
    const lang = localStorage.getItem('dashboard_lang') || 'en';
    const url  = lang !== 'en' ? baseUrl + '?lang=' + lang : baseUrl;
    navigator.clipboard.writeText(url);
}

(function () {
    const saved = localStorage.getItem('dashboard_lang') || 'en';
    if (saved !== 'en') setLang(saved);
    else document.querySelector('.lang-btn')?.classList.add('active');
})();
</script>
</body>
</html>
