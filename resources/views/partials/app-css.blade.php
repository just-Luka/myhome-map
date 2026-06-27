<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">
<style>
/* ════════════════════════════════════════════════════
   DESIGN TOKENS
   ════════════════════════════════════════════════════ */
:root {
    /* Surface */
    --body-bg:        #0b0d14;
    --body-text:      #e2e8f0;
    --sidebar-bg:     #111320;
    --sidebar-border: #1e2235;
    --sidebar-label:  #3d4466;
    --sidebar-link:   #94a3b8;
    --sidebar-hover:  rgba(255,255,255,0.04);
    --topbar-bg:      #111320;
    --card-bg:        #14172a;
    --card-border:    #1e2235;
    --th-color:       #4d5780;
    --td-border:      #0f1117;
    --field-bg:       #0b0d14;
    --field-border:   #1e2235;
    --toggle-border:  #1e2235;
    --toggle-small:   #4d5780;

    /* Palette */
    --primary:         #4f6ef7;
    --primary-hover:   #3b5be0;
    --primary-muted:   rgba(79,110,247,0.08);
    --brand:           #e94560;
    --muted:           #4d5780;
    --subtle:          #94a3b8;
    --dim:             #64748b;
    --success:         #86efac;
    --success-bg:      #1a2a1a;
    --success-border:  #2e5c2e;
    --danger-bg:       #7f1d1d;
    --danger-bg-hover: #991b1b;
    --danger-text:     #fca5a5;
    --warning:         #f59e0b;
    --info:            #7dd3fc;
    --info-bg:         #1a2035;
    --info-border:     #2d3a6e;
}

body.theme-light {
    --body-bg:        #f1f5f9;
    --body-text:      #1a1a2e;
    --sidebar-bg:     #ffffff;
    --sidebar-border: #e2e8f0;
    --sidebar-label:  #94a3b8;
    --sidebar-link:   #64748b;
    --sidebar-hover:  rgba(0,0,0,0.04);
    --topbar-bg:      #ffffff;
    --card-bg:        #ffffff;
    --card-border:    #e2e8f0;
    --th-color:       #94a3b8;
    --td-border:      #f1f5f9;
    --field-bg:       #f8fafc;
    --field-border:   #e2e8f0;
    --toggle-border:  #e2e8f0;
    --toggle-small:   #94a3b8;
}

/* ════════════════════════════════════════════════════
   BASE
   ════════════════════════════════════════════════════ */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--body-bg); color: var(--body-text);
    min-height: 100vh; display: flex;
    transition: background 0.25s, color 0.25s;
}

/* ════════════════════════════════════════════════════
   CARDS
   ════════════════════════════════════════════════════ */
.card {
    background: var(--card-bg); border: 1px solid var(--card-border);
    border-radius: 12px; padding: 20px; margin-bottom: 16px;
    transition: background 0.25s, border-color 0.25s;
}
.card-head  { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--th-color); }

/* ════════════════════════════════════════════════════
   STATS
   ════════════════════════════════════════════════════ */
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px; }
.stat {
    background: var(--card-bg); border: 1px solid var(--card-border);
    border-radius: 12px; padding: 18px 20px; transition: background 0.25s;
}
.stat .val { font-size: 28px; font-weight: 700; color: var(--brand); line-height: 1; }
.stat .lbl { font-size: 11px; color: var(--th-color); margin-top: 5px; }
.stat .sub { font-size: 11px; color: var(--dim); margin-top: 3px; }

/* ════════════════════════════════════════════════════
   TABLES
   ════════════════════════════════════════════════════ */
table { width: 100%; border-collapse: collapse; }
th {
    text-align: left; font-size: 10px; text-transform: uppercase;
    letter-spacing: .06em; color: var(--th-color);
    padding: 0 12px 12px 0; border-bottom: 1px solid var(--card-border);
}
td {
    padding: 11px 12px 11px 0; font-size: 13px;
    border-bottom: 1px solid var(--td-border); vertical-align: top;
}
tr:last-child td { border-bottom: none; }
tr:hover td { background: rgba(0,0,0,0.02); }

/* ════════════════════════════════════════════════════
   BUTTONS
   ════════════════════════════════════════════════════ */
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 8px; font-size: 13px;
    font-weight: 600; cursor: pointer; border: none;
    text-decoration: none; transition: all .15s;
}
.btn-primary  { background: var(--primary); color: #fff; }
.btn-primary:hover  { background: var(--primary-hover); }
.btn-ghost    { background: transparent; border: 1px solid var(--card-border); color: var(--subtle); }
.btn-ghost:hover    { border-color: var(--primary); color: var(--body-text); }
.btn-green    { background: #166534; color: var(--success); }
.btn-green:hover    { background: #15803d; }
.btn-danger   { background: var(--danger-bg); color: var(--danger-text); }
.btn-danger:hover   { background: var(--danger-bg-hover); }
.btn-sm { padding: 5px 10px; font-size: 12px; }

/* ════════════════════════════════════════════════════
   BADGES
   ════════════════════════════════════════════════════ */
.badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-super_admin, .badge-super { background: #2d1a3a; color: #c084fc; }
.badge-ceo                       { background: #2a1e3a; color: #c084fc; }
.badge-employee                  { background: #1e2a3a; color: var(--info); }
.badge-free                      { background: var(--card-border); color: var(--dim); }
.badge-pro                       { background: var(--primary-muted); color: var(--primary); }
.badge-org                       { background: rgba(233,69,96,0.12); color: var(--brand); }

/* ════════════════════════════════════════════════════
   ALERTS
   ════════════════════════════════════════════════════ */
.alert { border-radius: 8px; padding: 11px 15px; margin-bottom: 16px; font-size: 13px; word-break: break-all; }
.alert-success { background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success); }
.alert-error   { background: #2a1a1a; border: 1px solid #5c2e2e; color: var(--danger-text); }
.alert-info    { background: var(--info-bg); border: 1px solid var(--info-border); color: var(--info); }

/* ════════════════════════════════════════════════════
   FORMS
   ════════════════════════════════════════════════════ */
.field { display: flex; flex-direction: column; gap: 6px; }
.field label {
    font-size: 12px; color: var(--th-color);
    font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
}
input[type=text], input[type=email], input[type=number],
input[type=password], select, textarea {
    background: var(--field-bg); border: 1px solid var(--field-border);
    border-radius: 8px; padding: 8px 12px; color: var(--body-text);
    font-size: 13px; outline: none; width: 100%;
    transition: border-color .15s;
}
input[type=text]:focus, input[type=email]:focus, input[type=number]:focus,
input[type=password]:focus, select:focus, textarea:focus { border-color: var(--primary); }
select option { background: var(--card-bg); }
input[type=checkbox] { width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer; }

.form-grid       { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-grid.cols-1 { grid-template-columns: 1fr; }

/* ════════════════════════════════════════════════════
   TOGGLE ROWS
   ════════════════════════════════════════════════════ */
.toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 0; border-bottom: 1px solid var(--toggle-border); }
.toggle-row:last-child { border-bottom: none; }
.toggle-row span  { font-size: 13px; }
.toggle-row small { font-size: 11px; color: var(--toggle-small); display: block; }

/* ════════════════════════════════════════════════════
   AVATARS & EMPLOYEE ROWS
   ════════════════════════════════════════════════════ */
.employee-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--brand); display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700;
    flex-shrink: 0; color: #fff;
}
.employee-row  { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--card-border); }
.employee-row:last-child { border-bottom: none; }
.employee-info  { flex: 1; min-width: 0; }
.employee-name  { font-size: 13px; font-weight: 600; }
.employee-meta  { font-size: 11px; color: var(--th-color); margin-top: 2px; }
.employee-count { font-size: 18px; font-weight: 700; color: var(--brand); flex-shrink: 0; }

/* ════════════════════════════════════════════════════
   LISTING CELLS
   ════════════════════════════════════════════════════ */
.listing-title   { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
.listing-address { font-size: 11px; color: var(--muted); }
.listing-chips   { display: flex; gap: 6px; margin-top: 4px; flex-wrap: wrap; }
.listing-chip    { font-size: 10px; background: var(--card-border); padding: 2px 6px; border-radius: 4px; color: var(--subtle); }
.listing-url     { font-size: 11px; color: var(--primary); text-decoration: none; display: block; margin-top: 4px; }
.listing-owner   { font-size: 12px; font-weight: 600; }
.listing-phone   { font-size: 12px; color: var(--subtle); }
.listing-when    { color: var(--muted); font-size: 12px; white-space: nowrap; }

/* ════════════════════════════════════════════════════
   PRICE
   ════════════════════════════════════════════════════ */
.price-my   { color: var(--brand); font-weight: 700; }
.price-orig { color: var(--muted); font-size: 11px; }
.discount   { color: var(--success); font-size: 11px; font-weight: 600; }

/* ════════════════════════════════════════════════════
   PROGRESS
   ════════════════════════════════════════════════════ */
.progress-bar  { height: 4px; background: var(--card-border); border-radius: 2px; margin-top: 6px; }
.progress-fill { height: 4px; border-radius: 2px; transition: width .3s; }
.progress-fill.ok   { background: var(--primary); }
.progress-fill.warn { background: var(--warning); }
.progress-fill.full { background: var(--brand); }

/* ════════════════════════════════════════════════════
   DATE BADGE
   ════════════════════════════════════════════════════ */
.date-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 20px; background: var(--card-border); color: var(--subtle); white-space: nowrap; }
.date-badge.today { background: var(--primary-muted); color: var(--info); }

/* ════════════════════════════════════════════════════
   MISC
   ════════════════════════════════════════════════════ */
.empty    { text-align: center; padding: 32px; color: var(--muted); font-size: 13px; }
.copy-btn { padding: 3px 8px; border-radius: 5px; background: var(--card-border); border: none; color: var(--subtle); font-size: 11px; cursor: pointer; }
.copy-btn:hover { color: var(--body-text); }

.filter-row        { display: flex; gap: 8px; align-items: center; }
.filter-row select { width: auto; }

.grid-2   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.grid-3-1 { display: grid; grid-template-columns: 1fr 300px; gap: 16px; align-items: start; }

.pagination { display: flex; gap: 6px; margin-top: 20px; justify-content: flex-end; }
.pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; }
.pagination a { background: var(--card-bg); border: 1px solid var(--card-border); color: var(--subtle); }
.pagination a:hover { border-color: var(--primary); color: var(--primary); }
.pagination span { background: var(--primary); color: #fff; }

/* ════════════════════════════════════════════════════
   LANG SWITCHER
   ════════════════════════════════════════════════════ */
#lang-switcher { display: flex; border: 1.5px solid var(--card-border); border-radius: 8px; overflow: hidden; flex-shrink: 0; }
.lang-btn { padding: 5px 10px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: transparent; color: var(--dim); transition: all 0.15s; }
.lang-btn.active { background: var(--brand); color: #fff; }
.lang-btn:hover:not(.active) { color: var(--body-text); }

/* ════════════════════════════════════════════════════
   THEME TOGGLE
   ════════════════════════════════════════════════════ */
#theme-toggle {
    display: flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px; cursor: pointer; flex-shrink: 0;
    background: rgba(255,255,255,0.06); border: 1px solid var(--card-border);
    font-size: 15px; transition: all 0.2s;
}
#theme-toggle:hover { border-color: var(--primary); }
</style>
