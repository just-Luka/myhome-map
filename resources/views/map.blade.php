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

        /* ── Theme variables ── */
        :root {
            --bg:        #1a1a2e;
            --bg-card:   #111320;
            --bg-input:  #0f1117;
            --bg-hover:  #1e2235;
            --border:    #2d3149;
            --border2:   #3d4466;
            --text:      #e2e8f0;
            --muted:     #94a3b8;
            --dim:       #64748b;
        }
        [data-theme="light"] {
            --bg:        #f0f4f8;
            --bg-card:   #ffffff;
            --bg-input:  #f8fafc;
            --bg-hover:  #e2e8f0;
            --border:    #dde3eb;
            --border2:   #c8d0db;
            --text:      #1a202c;
            --muted:     #4a5568;
            --dim:       #94a3b8;
        }

        /* ── Light theme overrides ── */
        [data-theme="light"] #header { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        [data-theme="light"] #header h1 { color: #1a202c; }
        [data-theme="light"] #status-bar { color: #718096; }

        [data-theme="light"] #saved-btn { background: #e8edf3; color: #1a202c; }
        [data-theme="light"] #saved-btn:hover { background: var(--bg-hover); }
        [data-theme="light"] #progress-ring-bg { stroke: #dde3eb; }
        [data-theme="light"] #progress-ring-label { color: #1a202c; }
        [data-theme="light"] #saved-count { color: #4a5568; }

        [data-theme="light"] #filter-bar { background: #fff; border-bottom: 1px solid #e8edf3; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
        [data-theme="light"] .fpill { background: #f0f4f8; color: #1a202c; border: 1.5px solid #dde3eb; }
        [data-theme="light"] .fpill:hover, [data-theme="light"] .fpill.active { background: #1a1a2e; color: #fff; border-color: #1a1a2e; }
        [data-theme="light"] .fdrop { background: #fff; border: 1px solid #dde3eb; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        [data-theme="light"] .fdrop-label { color: #4a5568; }
        [data-theme="light"] .chip { background: #f0f4f8; color: #1a202c; border-color: #dde3eb; }
        [data-theme="light"] .chip.active { background: #1a1a2e; color: #fff; border-color: #1a1a2e; }
        [data-theme="light"] .range-row input[type=range]::-webkit-slider-runnable-track { background: #dde3eb; }
        [data-theme="light"] .apply-btn { background: #e94560; }

        [data-theme="light"] #view-switcher { background: #e8edf3; }
        [data-theme="light"] .vsw-btn { color: #4a5568; }
        [data-theme="light"] .vsw-btn.active { background: #1a1a2e; color: #fff; }

        [data-theme="light"] #panel-tab { background: #fff; border-color: #dde3eb; color: #4a5568; box-shadow: -4px 0 12px rgba(0,0,0,.1); }
        [data-theme="light"] #panel-tab:hover { background: #f0f4f8; color: #1a202c; }

        [data-theme="light"] #saved-panel { background: #f8fafc; border-left: 1px solid #dde3eb; box-shadow: -4px 0 24px rgba(0,0,0,.08); }
        [data-theme="light"] #saved-panel > div:first-child { background: #fff !important; border-bottom-color: #dde3eb !important; }
        [data-theme="light"] #saved-panel > div:first-child span { color: #1a202c !important; }
        [data-theme="light"] .sp-section-header { border-bottom-color: #dde3eb; }
        [data-theme="light"] .sp-section-title { color: #4a5568; }
        [data-theme="light"] .sp-section-meta { color: #4f6ef7; }
        [data-theme="light"] .sp-section-divider { border-top-color: #dde3eb; }
        [data-theme="light"] .saved-card { background: #fff; border-color: #dde3eb; }
        [data-theme="light"] .saved-card:hover { border-color: #4f6ef7; }
        [data-theme="light"] .saved-card-title { color: #1a202c; }
        [data-theme="light"] .saved-card-price { color: #c0392b; }
        [data-theme="light"] .saved-card-myprice { color: #166534; }
        [data-theme="light"] .saved-card-addr, [data-theme="light"] .saved-card-contact { color: #4a5568; }
        [data-theme="light"] .link-pill { background: #f0f4f8; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .link-pill-clear { color: #94a3b8; }
        [data-theme="light"] .link-pill-id { color: #4a5568; }
        [data-theme="light"] .saved-card-link-input { background: #f8fafc; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .saved-card-link-input::placeholder { color: #94a3b8; }
        [data-theme="light"] .saved-card-remove { background: #fee2e2; color: #dc2626; }
        [data-theme="light"] .saved-card-remove:hover { background: #fecaca; }
        [data-theme="light"] .saved-card-link { background: #e8edf3; color: #4f6ef7; }
        [data-theme="light"] .saved-card-link:hover { background: #dde3eb; }
        [data-theme="light"] .note-trigger { color: #718096; }
        [data-theme="light"] .saved-card-note-trigger.has-note { color: #1a202c; }
        [data-theme="light"] .saved-card-note-area { background: #f8fafc; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .archive-row-price { color: #c0392b; }
        [data-theme="light"] .panel-btn-exclusive { background: #eef0ff; color: #4f46e5; }
        [data-theme="light"] .panel-btn-exclusive:hover { background: #e0e3ff; }
        [data-theme="light"] .panel-btn-export { background: #dcfce7; color: #166534; }
        [data-theme="light"] .panel-btn-export:hover { background: #bbf7d0; }
        [data-theme="light"] .panel-btn-clear { background: #fee2e2; color: #991b1b; }
        [data-theme="light"] .panel-btn-clear:hover { background: #fecaca; }
        [data-theme="light"] .saved-empty { color: #94a3b8; }

        [data-theme="light"] #archive-modal { background: rgba(100,116,139,.4); }
        [data-theme="light"] #archive-box { background: #fff; border-color: #dde3eb; }
        [data-theme="light"] #archive-header { border-bottom-color: #dde3eb; }
        [data-theme="light"] #archive-header h2 { color: #1a202c; }
        [data-theme="light"] #archive-search { background: #f8fafc; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] #archive-search::placeholder { color: #94a3b8; }
        [data-theme="light"] #archive-close { color: #4a5568; }
        [data-theme="light"] .archive-date-label { color: #1a202c; border-bottom-color: #dde3eb; }
        [data-theme="light"] .archive-date-group { background: #fff; }
        [data-theme="light"] .archive-row { border-bottom-color: #f0f4f8; }
        [data-theme="light"] .archive-row:hover { background: #f8fafc; }
        [data-theme="light"] a.archive-row-title, [data-theme="light"] a.archive-row-title:visited { color: #1a202c !important; }
        [data-theme="light"] a.archive-row-title:hover { color: #4f6ef7 !important; }
        [data-theme="light"] .archive-row-addr, [data-theme="light"] .archive-row-owner { color: #718096; }
        [data-theme="light"] .archive-link-pill { background: #f0f4f8; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .archive-link-pill:hover { border-color: #4f6ef7; }
        [data-theme="light"] .archive-link-id { color: #94a3b8; }
        [data-theme="light"] .archive-edit-form { background: #f8fafc; border-top-color: #dde3eb; }
        [data-theme="light"] .archive-edit-input { background: #fff; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .arc-btn-edit { background: #e8edf3; color: #4a5568; }
        [data-theme="light"] .arc-btn-edit:hover { background: #dde3eb; color: #1a202c; }
        [data-theme="light"] .arc-btn-remove { background: #fee2e2; color: #dc2626; }
        [data-theme="light"] .arc-btn-remove:hover { background: #fecaca; }

        [data-theme="light"] #custom-modal { background: rgba(100,116,139,.4); }
        [data-theme="light"] #custom-modal-box { background: #fff; border-color: #dde3eb; }
        [data-theme="light"] #custom-modal-box::-webkit-scrollbar-thumb { background: #cbd5e0; }
        [data-theme="light"] .cm-header { border-bottom-color: #dde3eb; }
        [data-theme="light"] .cm-header h2 { color: #1a202c; }
        [data-theme="light"] .cm-close { color: #718096; }
        [data-theme="light"] .cm-close:hover { color: #1a202c; background: #f0f4f8; }
        [data-theme="light"] .cm-field label { color: #718096; }
        [data-theme="light"] .cm-field input, [data-theme="light"] .cm-field textarea { background: #f8fafc; border-color: #dde3eb; color: #1a202c; }
        [data-theme="light"] .cm-field input:focus, [data-theme="light"] .cm-field textarea:focus { border-color: #4f6ef7; }
        [data-theme="light"] .cm-field input::placeholder, [data-theme="light"] .cm-field textarea::placeholder { color: #a0aec0; }
        [data-theme="light"] .cm-cancel { border-color: #dde3eb; color: #4a5568; }
        [data-theme="light"] .cm-cancel:hover { border-color: #4f6ef7; color: #1a202c; }
        [data-theme="light"] .cm-submit:disabled { background: #e2e8f0; color: #a0aec0; }

        [data-theme="light"] #user-trigger { color: #1a202c; }
        [data-theme="light"] #user-avatar { background: #e94560; }
        [data-theme="light"] #user-dropdown { background: #fff; border-color: #dde3eb; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
        [data-theme="light"] #user-dropdown a, [data-theme="light"] #user-dropdown button { color: #1a202c; }
        [data-theme="light"] #user-dropdown a:hover, [data-theme="light"] #user-dropdown button:hover { background: #f0f4f8; }
        [data-theme="light"] #user-info strong { color: #1a202c; }
        [data-theme="light"] #user-info span { color: #4a5568; }
        [data-theme="light"] #lang-switcher { border-color: rgba(0,0,0,.15); }
        [data-theme="light"] .lang-btn { color: #4a5568; }
        [data-theme="light"] .lang-btn.active { background: #1a1a2e; color: #fff; }
        [data-theme="light"] .lang-btn:not(.active):hover { background: #e8edf3; }
        [data-theme="light"] #theme-toggle { border-color: rgba(0,0,0,.15) !important; color: #1a202c; }
        [data-theme="light"] .popup-link,
        [data-theme="light"] .leaflet-container a.popup-link { background: #1a1a2e; color: #fff !important; }
        [data-theme="light"] .popup-link:hover,
        [data-theme="light"] .leaflet-container a.popup-link:hover { background: #e94560; color: #fff !important; }
        [data-theme="light"] .plan-pro { background: #dcfce7; color: #166534; }

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

        /* Progress button */
        #saved-btn {
            display: none; align-items: center; gap: 7px; padding: 5px 10px 5px 5px;
            font-size: 12px; font-weight: 600; border-radius: 20px;
            background: #1a1d2e; color: #e2e8f0; border: none;
            white-space: nowrap; flex-shrink: 0; cursor: pointer;
            transition: background 0.15s;
        }
        #saved-btn:hover { background: #1e2235; }
        #progress-ring-wrap {
            position: relative; width: 28px; height: 28px; flex-shrink: 0;
        }
        #progress-ring-wrap svg { position: absolute; inset: 0; transform: rotate(-90deg); }
        #progress-ring-bg  { fill: none; stroke: #2d3149; stroke-width: 3; }
        #progress-ring-arc { fill: none; stroke: #4f6ef7; stroke-width: 3; stroke-linecap: round; transition: stroke-dashoffset 0.4s ease; }
        #progress-ring-arc.full { stroke: #f87171; }
        #progress-ring-label {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            font-size: 8px; font-weight: 800; color: #e2e8f0; line-height: 1;
        }

        /* ── Saved panel ── */
        #saved-panel {
            position: fixed; top: 56px; right: 0; bottom: 0; width: 360px;
            background: #1a1a2e; border-left: 1px solid #2d3149;
            z-index: 900; display: flex; flex-direction: column;
            transform: translateX(100%); transition: transform 0.3s cubic-bezier(.4,0,.2,1);
            box-shadow: -4px 0 24px rgba(0,0,0,0.4); overflow: hidden;
        }
        #saved-panel.open { transform: translateX(0); }
        #saved-list { flex: 1; }

        /* ── Archive modal ── */
        #archive-modal {
            display: none; position: fixed; inset: 0; z-index: 2500;
            background: rgba(5,6,12,0.85); backdrop-filter: blur(6px);
            align-items: flex-start; justify-content: center; padding: 40px 20px;
        }
        #archive-modal.open { display: flex; }
        #archive-box {
            background: #111320; border: 1px solid #2d3149; border-radius: 16px;
            width: 100%; max-width: 860px; max-height: calc(100vh - 80px);
            display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,0.6);
        }
        #archive-header {
            display: flex; align-items: center; gap: 12px;
            padding: 18px 24px; border-bottom: 1px solid #2d3149; flex-shrink: 0;
        }
        #archive-header h2 { font-size: 16px; font-weight: 700; color: #e2e8f0; flex: 1; }
        #archive-search {
            padding: 7px 14px; background: #0f1117; border: 1px solid #3d4466;
            border-radius: 8px; color: #e2e8f0; font-size: 13px; outline: none; width: 220px;
        }
        #archive-search::placeholder { color: #64748b; }
        #archive-search:focus { border-color: #4f6ef7; }
        #archive-close {
            background: none; border: none; color: #64748b; font-size: 20px;
            cursor: pointer; padding: 0 4px; transition: color 0.15s; line-height: 1;
        }
        #archive-close:hover { color: #e2e8f0; }
        #archive-body { overflow-y: auto; flex: 1; }
        #archive-body::-webkit-scrollbar { width: 4px; }
        #archive-body::-webkit-scrollbar-thumb { background: #2d3149; border-radius: 2px; }
        .archive-date-group { padding: 0 24px; }
        .archive-date-label {
            font-size: 11px; font-weight: 700; color: #e2e8f0; text-transform: uppercase;
            letter-spacing: .07em; padding: 16px 0 8px; border-bottom: 1px solid #3d4466;
        }
        .archive-row {
            display: grid; grid-template-columns: auto 1fr auto auto; gap: 12px;
            align-items: center; padding: 10px 0; border-bottom: 1px solid #111320;
        }
        .archive-row-actions { display: flex; flex-direction: column; gap: 4px; }
        .arc-btn {
            width: 30px; height: 30px; border-radius: 7px; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: background 0.15s;
        }
        .arc-btn-edit   { background: #1e2235; color: #94a3b8; }
        .arc-btn-edit:hover   { background: #2d3149; color: #e2e8f0; }
        .arc-btn-edit.active  { background: #4f6ef7; color: #fff; }
        .arc-btn-remove { background: #2a1a1a; color: #f87171; }
        .arc-btn-remove:hover { background: #3b1a1a; }
        .archive-edit-form {
            display: none; grid-column: 1 / -1;
            background: #0f1117; border: 1px solid #2d3149; border-radius: 10px;
            padding: 14px; margin: 4px 0 8px; display: none;
            gap: 10px; flex-direction: column;
        }
        .archive-edit-form.open { display: flex; }
        .archive-edit-row { display: flex; gap: 8px; }
        .archive-edit-input {
            flex: 1; padding: 7px 10px; background: #1a1a2e; border: 1px solid #3d4466;
            border-radius: 7px; color: #e2e8f0; font-size: 12px; outline: none;
            font-family: inherit; transition: border-color 0.15s;
        }
        .archive-edit-input:focus { border-color: #4f6ef7; }
        .archive-edit-input::placeholder { color: #64748b; }
        .archive-edit-save {
            padding: 7px 18px; background: #4f6ef7; color: #fff; border: none;
            border-radius: 7px; font-size: 12px; font-weight: 600; cursor: pointer;
            transition: background 0.15s; white-space: nowrap;
        }
        .archive-edit-save:hover { background: #3b5be0; }
        .archive-row-title, a.archive-row-title, a.archive-row-title:visited {
            font-size: 13px; font-weight: 600; color: #fff !important;
            text-decoration: none !important;
        }
        a.archive-row-title:hover {
            color: #7dd3fc !important;
            text-decoration: underline !important;
            cursor: pointer;
        }
        .archive-row-addr  { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .archive-row-price { font-size: 14px; font-weight: 800; color: #e94560; white-space: nowrap; }
        .archive-row-owner { font-size: 11px; color: #94a3b8; margin-top: 2px; }
        .archive-row-links { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; width: 155px; flex-shrink: 0; }
        .archive-row-links .archive-link-pill { width: 100%; box-sizing: border-box; justify-content: flex-start; }
        .archive-link-pill {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px;
            background: #1e2235; border: 1px solid #3d4466; border-radius: 6px;
            text-decoration: none; color: #e2e8f0; font-size: 11px; font-weight: 500;
            white-space: nowrap; transition: border-color 0.15s;
        }
        .archive-link-pill:hover { border-color: #4f6ef7; }
        .archive-link-pill img { width: 14px; height: 14px; border-radius: 2px; }
        .archive-link-empty { opacity: 0.35; cursor: default; pointer-events: none; }
        .archive-link-id { color: #64748b; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 10px; }
        .sp-section-header {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-bottom: 1px solid #2d3149; flex-shrink: 0;
        }
        .sp-section-divider { border-top: 2px solid #2d3149; }
        .sp-section-title { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .07em; }
        .sp-section-meta { font-size: 12px; color: #4f6ef7; font-weight: 700; }
        .sp-section-actions { display: flex; gap: 6px; margin-left: auto; }
        .sp-section-actions .panel-btn { padding: 4px 10px; font-size: 11px; }
        .sp-scroll {
            flex: 1; overflow-y: auto; padding: 10px; display: flex; flex-direction: column; gap: 8px;
            min-height: 0;
        }
        .sp-scroll::-webkit-scrollbar { width: 4px; }
        .sp-scroll::-webkit-scrollbar-track { background: transparent; }
        .sp-scroll::-webkit-scrollbar-thumb { background: #2d3149; border-radius: 2px; }
        #panel-tab {
            position: fixed; right: 0; top: 50%; transform: translateY(-50%);
            width: 28px; height: 56px; background: #1a1a2e; border: 1px solid #2d3149;
            border-right: none; border-radius: 8px 0 0 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #94a3b8; font-size: 18px;
            transition: color 0.15s, background 0.15s, right 0.3s cubic-bezier(.4,0,.2,1);
            box-shadow: -4px 0 12px rgba(0,0,0,0.4); z-index: 901;
        }
        #panel-tab:hover { color: #e2e8f0; background: #1e2235; }
        #panel-tab.open { right: 360px; }
        .saved-card {
            background: #111320; border: 1px solid #2d3149; border-radius: 10px;
            padding: 12px 14px; position: relative;
        }
        .saved-card-title { font-size: 13px; font-weight: 600; color: #e2e8f0; margin-bottom: 4px; line-height: 1.3; }
        .saved-card-address { font-size: 11px; color: #94a3b8; margin-bottom: 8px; }
        .saved-card-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .saved-card-price { font-size: 18px; font-weight: 800; color: #e94560; }
        .saved-card-orig { font-size: 11px; color: #64748b; }
        .saved-card-contact { font-size: 12px; color: #cbd5e1; margin-bottom: 8px; display: flex; flex-direction: column; gap: 3px; }
        .saved-card-myprice { font-size: 12px; color: #86efac; font-weight: 600; }
        .saved-card-footer { display: flex; gap: 6px; margin-top: 8px; }
        .saved-card-link {
            flex: 1; text-align: center; font-size: 11px; font-weight: 600; padding: 6px;
            border-radius: 6px; background: #1e2235; color: #4f6ef7; text-decoration: none; transition: background 0.15s;
        }
        .saved-card-link:hover { background: #2d3149; }
        .saved-card-remove {
            font-size: 11px; font-weight: 600; padding: 6px 10px; border-radius: 6px;
            background: #2a1a1a; color: #f87171; border: none; cursor: pointer; transition: background 0.15s;
        }
        .saved-card-remove:hover { background: #3b1a1a; }
        .saved-card-note-trigger {
            font-size: 11px; color: #94a3b8; cursor: pointer; margin-top: 6px;
            display: block; transition: color 0.15s;
        }
        .saved-card-note-trigger:hover { color: #cbd5e1; }
        .saved-card-note-trigger.has-note { color: #e2e8f0; font-style: normal; }
        .saved-card-note-area {
            display: none; width: 100%; margin-top: 6px; padding: 7px 9px; font-size: 12px; line-height: 1.5;
            background: #0f1117; border: 1px solid #3d4466; border-radius: 6px; color: #e2e8f0;
            resize: none; outline: none; font-family: inherit; transition: border-color 0.15s;
        }
        .saved-card-note-area.visible { display: block; }
        .saved-card-note-area:focus { border-color: #4f6ef7; }
        .saved-card-note-area::placeholder { color: #64748b; }
        .saved-card-links { display: flex; flex-direction: column; gap: 5px; margin-top: 8px; }
        .saved-card-link-input {
            width: 100%; padding: 6px 9px; font-size: 11px;
            background: #0f1117; border: 1px solid #3d4466; border-radius: 6px;
            color: #e2e8f0; outline: none; font-family: inherit; transition: border-color 0.15s;
        }
        .saved-card-link-input:focus { border-color: #4f6ef7; }
        .saved-card-link-input::placeholder { color: #64748b; }
        .link-pill {
            display: none; align-items: center; gap: 7px; padding: 5px 9px;
            background: #1e2235; border: 1px solid #3d4466; border-radius: 6px;
            text-decoration: none; color: #e2e8f0; font-size: 12px; font-weight: 500;
            transition: border-color 0.15s;
        }
        .link-pill:hover { border-color: #4f6ef7; }
        .link-pill img { width: 16px; height: 16px; border-radius: 3px; flex-shrink: 0; }
        .link-pill span { flex: 1; }
        .link-pill-clear {
            background: none; border: none; color: #475569; font-size: 15px; line-height: 1;
            cursor: pointer; padding: 0 2px; flex-shrink: 0; transition: color 0.15s;
        }
        .link-pill-clear:hover { color: #f87171; }
        .link-pill-id {
            margin-left: auto; font-size: 11px; color: #e2e8f0; font-weight: 600;
            font-family: 'SFMono-Regular', Consolas, monospace; letter-spacing: .3px;
        }
        .saved-empty { text-align: center; padding: 48px 20px; color: #4d5780; font-size: 13px; }
        .panel-btn {
            flex: 1; padding: 7px; font-size: 12px; font-weight: 600; border-radius: 7px;
            border: none; cursor: pointer; transition: all 0.15s;
        }
        .panel-btn-exclusive { background: #1a1f3a; color: #818cf8; }
        .panel-btn-exclusive:hover { background: #252c52; }
        .panel-btn-export { background: #1e3a1e; color: #86efac; }
        .panel-btn-export:hover { background: #166534; }
        .panel-btn-clear { background: #2a1a1a; color: #f87171; }
        .panel-btn-clear:hover { background: #3b1a1a; }

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

        /* ── View switcher ── */
        #view-switcher {
            display: flex; border: 1.5px solid #ddd; border-radius: 22px; overflow: hidden;
            flex-shrink: 0; margin-left: auto;
        }
        .vsw-btn {
            padding: 5px 14px; font-size: 13px; font-weight: 500; border: none;
            background: #fff; color: #888; cursor: pointer; transition: all 0.15s; white-space: nowrap;
        }
        .vsw-btn:hover:not(.active) { color: #1a1a2e; }
        .vsw-btn.active { background: #1a1a2e; color: #fff; font-weight: 600; }

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

        /* ── Pin legend ── */
        #pin-legend {
            position: absolute; bottom: 28px; left: 12px; z-index: 999;
            background: rgba(17,19,32,0.85); backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 10px;
            padding: 8px 12px; display: flex; flex-direction: column; gap: 5px;
        }
        .pin-legend-item { display: flex; align-items: center; gap: 7px; font-size: 11px; color: #94a3b8; }
        [data-theme="light"] #pin-legend { background: rgba(255,255,255,0.92); border-color: #dde3eb; }
        [data-theme="light"] .pin-legend-item { color: #4a5568; }

        /* ── Map pins ── */
        .map-pin { width: 12px; height: 12px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.8); box-shadow: 0 1px 4px rgba(0,0,0,0.35); }
        .map-pin.age-0 { background: #22c55e; }   /* today */
        .map-pin.age-1 { background: #3b82f6; }   /* 1 day ago */
        .map-pin.age-2 { background: #f59e0b; }   /* 2 days ago */
        .map-pin.age-3 { background: #f97316; }   /* 3 days ago */
        .map-pin.age-old { background: #94a3b8; } /* 4+ days ago */

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
            display: block; background: #1a1a2e; color: #fff !important; text-align: center;
            padding: 10px; text-decoration: none; font-size: 13px; font-weight: 600;
            border-radius: 8px; transition: background 0.2s;
        }
        .popup-link:hover { background: #e94560; }
        .team-avatars { display: flex; gap: 4px; align-items: center; margin-bottom: 8px; }
        .team-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 10px; font-weight: 700;
            cursor: default; position: relative; flex-shrink: 0;
            border: 2px solid #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .team-avatar .avatar-tooltip {
            display: none; position: absolute; bottom: calc(100% + 6px); left: 50%;
            transform: translateX(-50%); background: #1a1a2e; color: #fff;
            font-size: 11px; white-space: nowrap; text-align: center;
            padding: 6px 10px; border-radius: 6px; pointer-events: none; z-index: 9999;
        }
        .team-avatar .avatar-tooltip .tt-name  { font-weight: 700; }
        .team-avatar .avatar-tooltip .tt-price { color: #86efac; font-weight: 600; margin-top: 2px; }
        .team-avatar .avatar-tooltip::after {
            content: ''; position: absolute; top: 100%; left: 50%;
            transform: translateX(-50%); border: 5px solid transparent;
            border-top-color: #1a1a2e;
        }
        .team-avatar:hover .avatar-tooltip { display: block; }

        /* ── Custom listing modal ── */
        .badge-exclusive {
            display: inline-block; font-size: 9px; font-weight: 700; letter-spacing: .05em;
            text-transform: uppercase; padding: 2px 6px; border-radius: 4px;
            background: rgba(79,110,247,0.15); color: #7dd3fc; margin-bottom: 4px;
        }
        #custom-modal {
            display: none; position: fixed; inset: 0; z-index: 9998;
            background: rgba(0,0,0,.6); backdrop-filter: blur(2px);
            align-items: center; justify-content: center; padding: 20px;
        }
        #custom-modal.open { display: flex; }
        #custom-modal-box {
            background: #1a1d2e; border: 1px solid #2d3149; border-radius: 14px;
            width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.6);
        }
        #custom-modal-box::-webkit-scrollbar { width: 4px; }
        #custom-modal-box::-webkit-scrollbar-thumb { background: #2d3149; border-radius: 2px; }
        .cm-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px; border-bottom: 1px solid #2d3149;
        }
        .cm-header h2 { font-size: 15px; font-weight: 700; color: #e2e8f0; }
        .cm-close { background: none; border: none; color: #64748b; font-size: 20px; cursor: pointer; line-height: 1; padding: 2px 4px; border-radius: 6px; }
        .cm-close:hover { color: #e2e8f0; background: #1e2235; }
        .cm-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 14px; }
        .cm-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .cm-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .cm-field { display: flex; flex-direction: column; gap: 5px; }
        .cm-field label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #64748b; }
        .cm-field input, .cm-field textarea {
            background: #0f1117; border: 1px solid #2d3149; border-radius: 8px;
            padding: 8px 12px; color: #e2e8f0; font-size: 13px; outline: none;
            transition: border-color .15s; width: 100%;
        }
        .cm-field input:focus, .cm-field textarea:focus { border-color: #4f6ef7; }
        .cm-field textarea { resize: vertical; min-height: 64px; font-family: inherit; }
        .cm-field input::placeholder, .cm-field textarea::placeholder { color: #3d4466; }
        .cm-footer { padding: 0 24px 20px; display: flex; gap: 10px; }
        .cm-submit { flex: 1; padding: 10px; border-radius: 8px; border: none; background: #4f6ef7; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
        .cm-submit:hover { background: #3b5be0; }
        .cm-submit:disabled { background: #2d3149; color: #64748b; cursor: not-allowed; }
        .cm-cancel { padding: 10px 16px; border-radius: 8px; border: 1px solid #3d4466; background: transparent; color: #94a3b8; font-size: 13px; cursor: pointer; }
        .cm-cancel:hover { border-color: #4f6ef7; color: #e2e8f0; }
        .cm-error { font-size: 12px; color: #f87171; padding: 0 24px 12px; display: none; }
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
    <button id="saved-btn" onclick="togglePanel()">
        <div id="progress-ring-wrap">
            <svg viewBox="0 0 28 28" width="28" height="28">
                <circle id="progress-ring-bg"  cx="14" cy="14" r="11"/>
                <circle id="progress-ring-arc" cx="14" cy="14" r="11" stroke-dasharray="69.12" stroke-dashoffset="69.12"/>
            </svg>
            <div id="progress-ring-label">0</div>
        </div>
        <span id="saved-count">0/20</span>
    </button>

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
                <a href="{{ route('owner.dashboard') }}">📊 Dashboard</a>
                <a href="{{ route('owner.settings') }}">⚙ Settings</a>
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

    <button id="theme-toggle" onclick="toggleTheme()" title="Toggle theme"
        style="background:none;border:1.5px solid rgba(255,255,255,.2);border-radius:8px;width:34px;height:34px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .15s,background .15s;color:inherit;"
        onmouseover="this.style.borderColor='rgba(255,255,255,.5)'" onmouseout="this.style.borderColor='rgba(255,255,255,.2)'">🌙</button>
</div>

<!-- Filter bar -->
<div id="filter-bar">
    <button class="fpill" id="pill-price"    onclick="toggleDrop('price')"><span data-i18n="pill_price">Price</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-period"   onclick="toggleDrop('period')"><span data-i18n="pill_period">Period</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-rooms"    onclick="toggleDrop('rooms')"><span data-i18n="pill_rooms">Rooms</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-bedrooms" onclick="toggleDrop('bedrooms')"><span data-i18n="pill_bedrooms">Bedrooms</span> <span class="arrow">▼</span></button>
    <button class="fpill" id="pill-building" onclick="toggleDrop('building')" disabled style="opacity:.35;cursor:not-allowed;"><span data-i18n="pill_building">Building</span> <span class="arrow">▼</span></button>
    <div id="view-switcher">
        <button class="vsw-btn active" data-view="all"   onclick="setView('all')">All</button>
        <button class="vsw-btn"        data-view="saved" onclick="setView('saved')">Saved</button>
    </div>
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
<div id="pin-legend">
    <div class="pin-legend-item"><span class="map-pin age-0"></span> Today</div>
    <div class="pin-legend-item"><span class="map-pin age-1"></span> 1d ago</div>
    <div class="pin-legend-item"><span class="map-pin age-2"></span> 2d ago</div>
    <div class="pin-legend-item"><span class="map-pin age-3"></span> 3d ago</div>
    <div class="pin-legend-item"><span class="map-pin age-old"></span> Older</div>
</div>

<!-- Saved panel -->
<button id="panel-tab" onclick="togglePanel()" title="Toggle panel">›</button>

<div id="saved-panel">
    <!-- Panel header -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #2d3149;flex-shrink:0;background:#111320;">
        <span style="font-size:14px;font-weight:700;color:#e2e8f0;">Saved Listings</span>
        <button onclick="togglePanel()" style="background:none;border:none;color:#64748b;font-size:20px;cursor:pointer;line-height:1;padding:2px 4px;border-radius:6px;" onmouseover="this.style.color='#e2e8f0';this.style.background='#1e2235'" onmouseout="this.style.color='#64748b';this.style.background='none'">×</button>
    </div>

    <!-- Today section -->
    <div class="sp-section-header">
        <span class="sp-section-title">Today</span>
        <span id="saved-panel-count" class="sp-section-meta">0/20</span>
        <div class="sp-section-actions">
            @auth
            <button class="panel-btn panel-btn-exclusive" onclick="openCustomModal()">+ Exclusive</button>
            @endauth
            <button class="panel-btn panel-btn-export" onclick="exportSaved()">Excel</button>
            <button class="panel-btn panel-btn-clear"  onclick="clearSaved()">Clear</button>
        </div>
    </div>
    <div id="saved-list" class="sp-scroll">
        <div class="saved-empty">No saved listings yet.<br>Click "+ Save" on any pin.</div>
    </div>

    <div class="sp-section-header sp-section-divider" style="flex-shrink:0">
        <button class="panel-btn panel-btn-export" onclick="openArchive()" style="flex:1;padding:8px">
            View All Listings <span id="archive-count" style="opacity:.7"></span>
        </button>
    </div>
</div>

<!-- Custom / Exclusive listing modal -->
@auth
<div id="custom-modal">
    <div id="custom-modal-box">
        <div class="cm-header">
            <h2>Add Exclusive Listing</h2>
            <button class="cm-close" onclick="closeCustomModal()">×</button>
        </div>
        <form id="custom-form" onsubmit="submitCustomListing(event)">
            <div class="cm-body">
                <div class="cm-field">
                    <label>Title *</label>
                    <input type="text" name="title" placeholder="e.g. 3-room apartment in Vake" required>
                </div>
                <div class="cm-field">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="Street, building…">
                </div>
                <div class="cm-row">
                    <div class="cm-field">
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" placeholder="Full name">
                    </div>
                    <div class="cm-field">
                        <label>Phone</label>
                        <input type="text" name="phone" placeholder="+995 5xx xxx xxx">
                    </div>
                </div>
                <div class="cm-row">
                    <div class="cm-field">
                        <label>Asking Price ($)</label>
                        <input type="number" name="price" placeholder="0" min="0">
                    </div>
                    <div class="cm-field">
                        <label>My Price ($)</label>
                        <input type="number" name="my_price" placeholder="0" min="0">
                    </div>
                </div>
                <div class="cm-row cols-3">
                    <div class="cm-field">
                        <label>Rooms</label>
                        <input type="text" name="rooms" placeholder="3">
                    </div>
                    <div class="cm-field">
                        <label>Area (m²)</label>
                        <input type="text" name="area" placeholder="75 m²">
                    </div>
                    <div class="cm-field">
                        <label>District</label>
                        <input type="text" name="district" placeholder="Vake">
                    </div>
                </div>
                <div class="cm-row">
                    <div class="cm-field">
                        <label>myhome.ge post link</label>
                        <input type="url" name="link_myhome" placeholder="https://myhome.ge/…">
                    </div>
                    <div class="cm-field">
                        <label>ss.ge post link</label>
                        <input type="url" name="link_ss" placeholder="https://ss.ge/…">
                    </div>
                </div>
                <div class="cm-field">
                    <label>Note</label>
                    <textarea name="note" placeholder="Any additional details…"></textarea>
                </div>
            </div>
            <div id="custom-form-error" class="cm-error"></div>
            <div class="cm-footer">
                <button type="button" class="cm-cancel" onclick="closeCustomModal()">Cancel</button>
                <button type="submit" class="cm-submit" id="cm-submit-btn">Save Listing</button>
            </div>
        </form>
    </div>
</div>
@endauth

@guest
<div id="auth-overlay">
    <div id="auth-box">
        <div class="auth-logo">MYHOME<span>-MAP</span></div>
        <p>Sign in to view listings on the map.</p>
        <a href="/login">Sign in</a>
    </div>
</div>
@endguest

<!-- Archive modal -->
<div id="confirm-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.6);backdrop-filter:blur(2px);align-items:center;justify-content:center;">
    <div style="background:#1a1d2e;border:1px solid #2d3149;border-radius:14px;padding:28px 32px;min-width:300px;max-width:360px;box-shadow:0 20px 60px rgba(0,0,0,.6);text-align:center;">
        <div style="width:44px;height:44px;margin:0 auto 16px;background:#2a1a1a;border-radius:50%;display:flex;align-items:center;justify-content:center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </div>
        <div id="confirm-title" style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:6px;">Remove listing?</div>
        <div id="confirm-desc"  style="font-size:13px;color:#94a3b8;margin-bottom:24px;line-height:1.5;"></div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <button id="confirm-cancel" style="flex:1;padding:9px 0;border-radius:8px;border:1px solid #3d4466;background:transparent;color:#94a3b8;font-size:13px;font-weight:500;cursor:pointer;" onmouseover="this.style.borderColor='#4f6ef7';this.style.color='#e2e8f0'" onmouseout="this.style.borderColor='#3d4466';this.style.color='#94a3b8'">Cancel</button>
            <button id="confirm-ok"     style="flex:1;padding:9px 0;border-radius:8px;border:none;background:#dc2626;color:#fff;font-size:13px;font-weight:600;cursor:pointer;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">Remove</button>
        </div>
    </div>
</div>

<div id="iframe-modal" style="display:none;position:fixed;inset:0;z-index:3000;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);flex-direction:column;align-items:center;justify-content:center;padding:20px;">
    <div style="width:100%;max-width:1100px;height:90vh;background:#fff;border-radius:12px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 24px 80px rgba(0,0,0,.6);">
        <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;background:#1a1a2e;flex-shrink:0;">
            <span id="iframe-url-label" style="flex:1;font-size:12px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
            <a id="iframe-open-tab" href="#" target="_blank" rel="noopener" style="font-size:12px;color:#7dd3fc;white-space:nowrap;text-decoration:none;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#7dd3fc'">↗ Open in tab</a>
            <button onclick="closeIframeModal()" style="background:none;border:none;color:#94a3b8;font-size:22px;cursor:pointer;line-height:1;padding:0 4px;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">×</button>
        </div>
        <iframe id="iframe-content" src="" style="flex:1;border:none;width:100%;" sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox"></iframe>
    </div>
</div>

<div id="archive-modal">
    <div id="archive-box">
        <div id="archive-header">
            <h2>All Listings</h2>
            <input id="archive-search" type="search" placeholder="Search title, address, owner…" oninput="filterArchive(this.value)">
            <button id="archive-close" onclick="closeArchive()">×</button>
        </div>
        <div id="archive-body"></div>
    </div>
</div>

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
            if (mySaves.size) renderSavedPanel();
        })
        .catch(err => {
            if (err.name !== 'AbortError') setStatus(tr('error'), false);
            activeStream = null;
        });
}

function addMarker(l) {
    allListings.set(String(l.id), l);
    // Back-fill listing data for saves that were loaded from DB without a snapshot
    const save = mySaves.get(String(l.id));
    if (save && !save.listing) { save.listing = l; }

    const days    = l.days_ago ?? 99;
    const ageClass = days === 0 ? 'age-0' : days === 1 ? 'age-1' : days === 2 ? 'age-2' : days === 3 ? 'age-3' : 'age-old';
    const pinIcon = L.divIcon({ className: '', html: `<div class="map-pin ${ageClass}"></div>`, iconSize: [12, 12], iconAnchor: [6, 6] });

    const marker = L.marker([l.lat, l.lng], {
        icon:    pinIcon,
        opacity: (currentView === 'all' && viewedListings.has(String(l.id))) ? 0.35 : 1,
    });
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

    const teamBadge = savedByTeam?.length
        ? `<div class="team-avatars">${savedByTeam.map(entry => {
               const name = entry.name ?? entry;
               const price = entry.price;
               const initials = name.trim().split(/\s+/).map(w => w[0]).join('').toUpperCase().slice(0, 2);
               const hue = name.split('').reduce((a, c) => a + c.charCodeAt(0), 0) % 360;
               const tooltip = price
                   ? `<div class="tt-name">${name}</div><div class="tt-price">$${price}</div>`
                   : `<div class="tt-name">${name}</div>`;
               return `<div class="team-avatar" style="background:hsl(${hue},55%,48%);">
                   ${initials}<span class="avatar-tooltip">${tooltip}</span>
               </div>`;
           }).join('')}</div>`
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
                    onclick="toggleSave('${lid}')">
                    ${alreadySaved ? '✓ Saved' : '+ Save'}
                </button>
            </div>
            <a class="popup-link" href="${l.url}" target="_blank" rel="noopener" onclick="markViewed('${lid}')">${tr('view')}</a>
        </div>
    `, { maxWidth: 310 });

    markerMap.set(String(l.id), marker);
    markers.addLayer(marker);
    if (markers.getLayers().length === 1) map.setView([l.lat, l.lng], 13);
}

// ── Saved list ────────────────────────────────────────────────────────────────

const isAuthed   = !!document.querySelector('meta[name="user-authed"]');
const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Theme ─────────────────────────────────────────────────────────────────────
(function () {
    const saved = localStorage.getItem('theme') || 'light';
    applyTheme(saved);
})();
function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    const btn = document.getElementById('theme-toggle');
    if (btn) {
        btn.textContent = theme === 'light' ? '🌙' : '☀️';
        btn.style.borderColor = theme === 'light' ? 'rgba(0,0,0,.15)' : 'rgba(255,255,255,.2)';
        btn.onmouseover = () => btn.style.borderColor = theme === 'light' ? 'rgba(0,0,0,.35)' : 'rgba(255,255,255,.5)';
        btn.onmouseout  = () => btn.style.borderColor = theme === 'light' ? 'rgba(0,0,0,.15)' : 'rgba(255,255,255,.2)';
    }
}
function toggleTheme() {
    const next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', next);
    applyTheme(next);
}

// listing_id → {myPrice} for current user; populated on load
const mySaves   = new Map();
// listing_id → employee name; populated on load (team saves)
const teamSaves = new Map();
// listing_id → Leaflet marker, for opacity updates
const markerMap = new Map();
let currentView = 'all';
// viewed listing IDs, persisted in localStorage
const viewedListings = new Set(JSON.parse(localStorage.getItem('viewedListings') || '[]'));
function markViewed(lid) {
    if (viewedListings.has(lid)) return;
    viewedListings.add(lid);
    localStorage.setItem('viewedListings', JSON.stringify([...viewedListings]));
    const m = markerMap.get(lid);
    if (m) m.setOpacity(0.35);
}

let panelOpen      = false;
let saveLimit      = 20;
const archiveSaves = [];

function togglePanel() {
    panelOpen = !panelOpen;
    document.getElementById('saved-panel').classList.toggle('open', panelOpen);
    const tab = document.getElementById('panel-tab');
    tab.classList.toggle('open', panelOpen);
    tab.textContent = panelOpen ? '›' : '‹';
}


function updateSavedBtn() {
    const count      = mySaves.size;
    const circumference = 2 * Math.PI * 11; // r=11 → ~69.12
    const pct        = saveLimit > 0 ? Math.min(count / saveLimit, 1) : 0;
    const offset     = circumference * (1 - (count > 0 ? pct : 0));

    document.getElementById('saved-count').textContent = count + '/' + saveLimit;
    document.getElementById('saved-btn').style.display = 'flex';
    document.getElementById('progress-ring-label').textContent = count;
    const arc = document.getElementById('progress-ring-arc');
    arc.style.strokeDashoffset = offset;
    arc.classList.toggle('full', count >= saveLimit);

    renderSavedPanel();
}

function renderSavedPanel() {
    const list  = document.getElementById('saved-list');
    const label = document.getElementById('saved-panel-count');
    if (!list) return;

    const count = mySaves.size;
    if (label) label.textContent = count + '/' + saveLimit;
    const archiveCount = document.getElementById('archive-count');
    if (archiveCount) archiveCount.textContent = archiveSaves.length ? '(' + archiveSaves.length + ')' : '';

    if (!count) {
        list.innerHTML = '<div class="saved-empty">No saved listings yet.<br>Click "+ Save" on any pin.</div>';
        return;
    }

    list.innerHTML = '';
    [...mySaves.entries()].reverse().forEach(([id, entry]) => {
        const l = entry.listing || allListings.get(id);
        const isCustom = id.startsWith('custom-');
        const card = document.createElement('div');
        card.className = 'saved-card';
        card.id = 'card-' + id;

        const priceFmt = formatPrice(l?.price, entry.myPrice);
        const contact = l?.owner_name ? `
            <div class="saved-card-contact"><span>👤 ${l.owner_name}</span>${l?.phone ? ` <span>📞 ${l.phone}</span>` : ''}</div>` : '';

        const noteText = entry.note || '';
        card.innerHTML = `
            <div class="saved-card-title">
                ${isCustom ? '<span class="badge-exclusive">Exclusive</span> ' : ''}${l?.title || 'Listing #' + id}
            </div>
            ${l?.address ? `<div class="saved-card-address">📍 ${l.address}</div>` : ''}
            <div class="saved-card-row">
                <div class="saved-card-price">${priceFmt}</div>
                <div class="saved-card-orig">${l?.rent_type || ''}</div>
            </div>
            ${contact}
            <span class="saved-card-note-trigger ${noteText ? 'has-note' : ''}" id="note-trigger-${id}">${noteText || '+ Add note…'}</span>
            <textarea class="saved-card-note-area" id="note-area-${id}" rows="2"></textarea>
            <div class="saved-card-links">
                <div id="link-wrap-myhome-${id}">
                    <input class="saved-card-link-input" type="url" placeholder="myhome.ge…" value="${entry.linkMyhome || ''}">
                    <a class="link-pill" href="#" target="_blank" rel="noopener">
                        <img src="https://www.google.com/s2/favicons?domain=myhome.ge&sz=32" alt="">
                        <span>myhome.ge</span>
                        <span class="link-pill-id"></span>
                        <button class="link-pill-clear" type="button">×</button>
                    </a>
                </div>
                <div id="link-wrap-ss-${id}">
                    <input class="saved-card-link-input" type="url" placeholder="ss.ge…" value="${entry.linkSs || ''}">
                    <a class="link-pill" href="#" target="_blank" rel="noopener">
                        <img src="https://www.google.com/s2/favicons?domain=ss.ge&sz=32" alt="">
                        <span>ss.ge</span>
                        <span class="link-pill-id"></span>
                        <button class="link-pill-clear" type="button">×</button>
                    </a>
                </div>
            </div>
            <div class="saved-card-footer">
                ${!isCustom && l?.url ? `<a class="saved-card-link" href="${l.url}" target="_blank" rel="noopener">View →</a>` : ''}
                <button class="saved-card-remove" onclick="removeSave('${id}')">Remove</button>
            </div>
        `;
        list.appendChild(card);

        const trigger = card.querySelector('.saved-card-note-trigger');
        const area    = card.querySelector('.saved-card-note-area');
        area.value = noteText;

        trigger.addEventListener('click', () => {
            trigger.style.display = 'none';
            area.classList.add('visible');
            area.focus();
        });

        area.addEventListener('blur', async () => {
            area.classList.remove('visible');
            trigger.style.display = '';
            const val = area.value.trim();
            trigger.textContent = val || '+ Add note…';
            trigger.classList.toggle('has-note', !!val);
            await saveNote(id, val);
        });

        initLinkField(id, 'myhome', entry.linkMyhome || '');
        initLinkField(id, 'ss',     entry.linkSs     || '');
    });
}

function initLinkField(id, type, initialUrl) {
    const wrap    = document.getElementById('link-wrap-' + type + '-' + id);
    const input   = wrap.querySelector('input');
    const pill    = wrap.querySelector('.link-pill');
    const clearBtn = wrap.querySelector('.link-pill-clear');

    function showPill(url) {
        pill.href = url;
        input.style.display = 'none';
        pill.style.display  = 'flex';
        const idEl = pill.querySelector('.link-pill-id');
        if (idEl) {
            const match = type === 'myhome'
                ? url.match(/\/pr\/(\d+)/)
                : url.match(/-(\d+)\/?$/);
            idEl.textContent = match ? '#' + match[1] : '';
        }
    }
    function showInput() {
        pill.style.display  = 'none';
        input.style.display = '';
    }

    if (initialUrl) showPill(initialUrl);

    input.addEventListener('blur', async () => {
        const url = input.value.trim();
        if (url) showPill(url);
        const entry = mySaves.get(id);
        const myhome = type === 'myhome' ? url : (entry?.linkMyhome || '');
        const ss     = type === 'ss'     ? url : (entry?.linkSs     || '');
        await saveLinks(id, myhome, ss);
    });

    clearBtn.addEventListener('click', e => {
        e.preventDefault();
        e.stopPropagation();
        input.value = '';
        showInput();
        const entry = mySaves.get(id);
        const myhome = type === 'myhome' ? '' : (entry?.linkMyhome || '');
        const ss     = type === 'ss'     ? '' : (entry?.linkSs     || '');
        saveLinks(id, myhome, ss);
    });
}

// ── Archive lazy-loading state ────────────────────────────────────────────────
let archiveAllItems   = [];
let archiveOffset     = 0;
let archiveLastDate   = null;
let archiveGroup      = null;
let archiveObserver   = null;
let archiveSearchMode = false;
const ARCHIVE_PAGE    = 30;

function formatPrice(origRaw, myRaw) {
    const light    = document.documentElement.dataset.theme === 'light';
    const orig     = Number(origRaw);
    const my       = Number(myRaw);
    const origFmt  = origRaw ? `$${orig.toLocaleString()}` : 'N/A';
    if (!myRaw) return origFmt;
    const myColor  = light ? '#166534' : '#86efac';
    const upColor  = light ? '#b91c1c' : '#f87171';
    const downColor= light ? '#166534' : '#86efac';
    const pct      = orig ? Math.round((my - orig) / orig * 100) : null;
    const pctHtml  = pct !== null
        ? ` <span style="font-size:10px;font-weight:600;color:${pct <= 0 ? downColor : upColor}">(${pct > 0 ? '+' : ''}${pct}%)</span>`
        : '';
    return `${origFmt} <span style="color:#64748b;font-size:12px;font-weight:400">→</span> <span style="color:${myColor}">$${my.toLocaleString()}</span>${pctHtml}`;
}

function buildArchiveBody(items) {
    archiveAllItems   = items;
    archiveOffset     = 0;
    archiveLastDate   = null;
    archiveGroup      = null;
    archiveSearchMode = false;

    const body = document.getElementById('archive-body');
    body.innerHTML = '';

    if (!items.length) {
        body.innerHTML = '<div class="saved-empty" style="padding:48px 24px">No archived listings yet.</div>';
        return;
    }

    renderMoreArchive();
}

function renderMoreArchive() {
    if (archiveSearchMode) return;

    const body = document.getElementById('archive-body');
    const sentinel = document.getElementById('archive-sentinel');
    if (sentinel) sentinel.remove();
    if (archiveObserver) { archiveObserver.disconnect(); archiveObserver = null; }

    const chunk = archiveAllItems.slice(archiveOffset, archiveOffset + ARCHIVE_PAGE);
    archiveOffset += chunk.length;
    chunk.forEach(entry => appendArchiveRow(entry, body));

    if (archiveOffset < archiveAllItems.length) {
        const s = document.createElement('div');
        s.id = 'archive-sentinel';
        s.style.height = '1px';
        body.appendChild(s);
        archiveObserver = new IntersectionObserver(([e]) => {
            if (e.isIntersecting) renderMoreArchive();
        }, { root: body });
        archiveObserver.observe(s);
    }
}

function appendArchiveRow(entry, body) {
    if (entry.saved_date !== archiveLastDate) {
        archiveLastDate = entry.saved_date;
        archiveGroup = document.createElement('div');
        archiveGroup.className = 'archive-date-group';
        const label = document.createElement('div');
        label.className = 'archive-date-label';
        label.textContent = entry.saved_date;
        archiveGroup.appendChild(label);
        body.appendChild(archiveGroup);
    }

    const l = entry.snapshot;
    const price = formatPrice(l?.price, entry.my_price);

    const myhomeUrl = entry.link_myhome || '';
    const ssUrl     = entry.link_ss || '';
    const myhomeId  = (myhomeUrl.match(/\/pr\/(\d+)/) || [])[1] || '';
    const ssId      = (ssUrl.match(/-(\d+)\/?$/) || [])[1] || '';

    const myhomePill = myhomeUrl
        ? `<a class="archive-link-pill" href="${myhomeUrl}" target="_blank" rel="noopener">
            <img src="https://www.google.com/s2/favicons?domain=myhome.ge&sz=32" alt="">
            <span>myhome.ge</span>
            ${myhomeId ? `<span class="archive-link-id">#${myhomeId}</span>` : ''}
           </a>`
        : `<span class="archive-link-pill archive-link-empty">
            <img src="https://www.google.com/s2/favicons?domain=myhome.ge&sz=32" alt="" style="opacity:.3">
            <span>myhome.ge</span>
           </span>`;
    const ssPill = ssUrl
        ? `<a class="archive-link-pill" href="${ssUrl}" target="_blank" rel="noopener">
            <img src="https://www.google.com/s2/favicons?domain=ss.ge&sz=32" alt="">
            <span>ss.ge</span>
            ${ssId ? `<span class="archive-link-id">#${ssId}</span>` : ''}
           </a>`
        : `<span class="archive-link-pill archive-link-empty">
            <img src="https://www.google.com/s2/favicons?domain=ss.ge&sz=32" alt="" style="opacity:.3">
            <span>ss.ge</span>
           </span>`;

    const row = document.createElement('div');
    row.className = 'archive-row';
    const viewUrl = l?.url || myhomeUrl || ssUrl;
    row.dataset.id = entry.listing_id;
    const eid = entry.listing_id;
    row.innerHTML = `
        <div class="archive-row-actions">
            <button class="arc-btn arc-btn-edit" id="arc-edit-btn-${eid}" onclick="toggleArchiveEdit('${eid}')" title="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button class="arc-btn arc-btn-remove" onclick="removeArchiveItem('${eid}')" title="Remove">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>
        </div>
        <div>
            ${viewUrl
                ? `<a class="archive-row-title" href="${viewUrl}" target="_blank" rel="noopener">${l?.title || 'Listing #' + eid}</a>`
                : `<div class="archive-row-title">${l?.title || 'Listing #' + eid}</div>`}
            ${l?.address ? `<div class="archive-row-addr">📍 ${l.address}</div>` : ''}
            ${l?.owner_name ? `<div class="archive-row-owner">👤 ${l.owner_name}</div>` : ''}
            <div id="arc-note-display-${eid}" style="font-size:11px;color:#94a3b8;margin-top:4px;font-style:italic">${entry.note ? '💬 ' + entry.note : ''}</div>
        </div>
        <div id="arc-price-display-${eid}" class="archive-row-price">${price}</div>
        <div class="archive-row-links">${myhomePill}${ssPill}</div>
        <div class="archive-edit-form" id="arc-edit-${eid}">
            <div class="archive-edit-row">
                <input class="archive-edit-input" id="arc-price-${eid}"  type="number" placeholder="My price $"  value="${entry.my_price || ''}" min="0" step="50">
                <input class="archive-edit-input" id="arc-note-${eid}"   type="text"   placeholder="Comment…"    value="${(entry.note || '').replace(/"/g, '&quot;')}">
            </div>
            <div class="archive-edit-row">
                <input class="archive-edit-input" id="arc-myhome-${eid}" type="url"    placeholder="myhome.ge…"  value="${myhomeUrl || ''}">
                <input class="archive-edit-input" id="arc-ss-${eid}"     type="url"    placeholder="ss.ge…"      value="${ssUrl || ''}">
            </div>
            <div class="archive-edit-row">
                <button class="archive-edit-save" onclick="saveArchiveEdit('${eid}')">Save</button>
            </div>
        </div>
    `;
    archiveGroup.appendChild(row);
}

function toggleArchiveEdit(id) {
    const form = document.getElementById('arc-edit-' + id);
    const btn  = document.getElementById('arc-edit-btn-' + id);
    const open = form.classList.toggle('open');
    btn.classList.toggle('active', open);
    if (open) form.querySelector('input').focus();
}

async function saveArchiveEdit(id) {
    const myPrice   = document.getElementById('arc-price-' + id)?.value  || null;
    const note      = document.getElementById('arc-note-' + id)?.value   || null;
    const linkMyhome = document.getElementById('arc-myhome-' + id)?.value || null;
    const linkSs    = document.getElementById('arc-ss-' + id)?.value     || null;

    await fetch('/api/save/update', {
        method:  'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: id, my_price: myPrice, note, link_myhome: linkMyhome, link_ss: linkSs }),
    });

    // Update displayed note
    const noteDisplay = document.getElementById('arc-note-display-' + id);
    if (noteDisplay) noteDisplay.textContent = note ? '💬 ' + note : '';

    // Update displayed price
    const priceDisplay = document.getElementById('arc-price-display-' + id);
    if (priceDisplay) {
        const origEntry = archiveSaves.find(e => e.listing_id === id);
        const origEntry2 = archiveAllItems.find(e => e.listing_id === id);
        priceDisplay.innerHTML = formatPrice(origEntry2?.snapshot?.price, myPrice);
    }

    // Close form
    toggleArchiveEdit(id);
}

function filterArchive(q) {
    const term = q.toLowerCase().trim();
    const body = document.getElementById('archive-body');

    if (!term) {
        // Back to paginated view
        archiveOffset     = 0;
        archiveLastDate   = null;
        archiveGroup      = null;
        archiveSearchMode = false;
        body.innerHTML    = '';
        renderMoreArchive();
        return;
    }

    archiveSearchMode = true;
    if (archiveObserver) { archiveObserver.disconnect(); archiveObserver = null; }

    const matches = archiveAllItems.filter(e => {
        const l = e.snapshot;
        const myhomeId = (e.link_myhome?.match(/\/pr\/(\d+)/) || [])[1] || '';
        const ssId     = (e.link_ss?.match(/-(\d+)\/?$/) || [])[1] || '';
        return [l?.title, l?.address, l?.owner_name, myhomeId, ssId]
            .filter(Boolean).join(' ').toLowerCase().includes(term);
    });

    body.innerHTML  = '';
    archiveLastDate = null;
    archiveGroup    = null;

    if (!matches.length) {
        body.innerHTML = '<div class="saved-empty" style="padding:48px 24px">No results.</div>';
        return;
    }
    matches.forEach(entry => appendArchiveRow(entry, body));
}

function showConfirm(title, desc) {
    return new Promise(resolve => {
        const modal  = document.getElementById('confirm-modal');
        const okBtn  = document.getElementById('confirm-ok');
        const cancel = document.getElementById('confirm-cancel');
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-desc').textContent  = desc;
        modal.style.display = 'flex';
        const finish = result => {
            modal.style.display = 'none';
            okBtn.removeEventListener('click', onOk);
            cancel.removeEventListener('click', onCancel);
            resolve(result);
        };
        const onOk     = () => finish(true);
        const onCancel = () => finish(false);
        okBtn.addEventListener('click', onOk);
        cancel.addEventListener('click', onCancel);
        modal.addEventListener('click', e => { if (e.target === modal) finish(false); }, { once: true });
    });
}

async function removeArchiveItem(id) {
    const ok = await showConfirm('Remove listing?', 'This will permanently remove it from your saved list.');
    if (!ok) return;
    const entry   = mySaves.get(id);
    const listing = entry?.listing || allListings.get(id) || { id };
    await fetch('/api/save', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: id, listing_snapshot: listing, my_price: null }),
    });
    // Remove from today's map or archive array
    mySaves.delete(id);
    const ai = archiveSaves.findIndex(e => e.listing_id === id);
    if (ai !== -1) archiveSaves.splice(ai, 1);
    const ai2 = archiveAllItems.findIndex(e => e.listing_id === id);
    if (ai2 !== -1) archiveAllItems.splice(ai2, 1);
    // Remove the row from DOM
    const row = document.querySelector(`#archive-body .archive-row[data-id="${id}"]`);
    if (row) {
        const group = row.closest('.archive-date-group');
        row.remove();
        if (group && !group.querySelector('.archive-row')) group.remove();
    }
    updateSavedBtn();
    // Also update popup save button if visible
    const btn = document.getElementById('save-btn-' + id);
    if (btn) { btn.textContent = '+ Save'; btn.classList.remove('saved'); }
}

function openIframeModal(url) {
    document.getElementById('iframe-content').src = url;
    document.getElementById('iframe-url-label').textContent = url;
    document.getElementById('iframe-open-tab').href = url;
    document.getElementById('iframe-modal').style.display = 'flex';
}
function closeIframeModal() {
    document.getElementById('iframe-modal').style.display = 'none';
    document.getElementById('iframe-content').src = '';
}
document.getElementById('iframe-modal').addEventListener('click', e => {
    if (e.target === document.getElementById('iframe-modal')) closeIframeModal();
});

function openArchive() {
    const today = new Date().toISOString().slice(0, 10);
    const todayEntries = [...mySaves.entries()].reverse().map(([id, entry]) => ({
        listing_id:  id,
        snapshot:    entry.listing || allListings.get(id) || null,
        saved_date:  today,
        my_price:    entry.myPrice    || null,
        note:        entry.note       || '',
        link_myhome: entry.linkMyhome || '',
        link_ss:     entry.linkSs     || '',
    }));
    buildArchiveBody([...todayEntries, ...archiveSaves]);
    document.getElementById('archive-search').value = '';
    document.getElementById('archive-modal').classList.add('open');
}

function closeArchive() {
    document.getElementById('archive-modal').classList.remove('open');
}

document.getElementById('archive-modal').addEventListener('click', e => {
    if (e.target === document.getElementById('archive-modal')) closeArchive();
});

async function saveLinks(id, myhome, ss) {
    const entry = mySaves.get(id);
    if (entry) { entry.linkMyhome = myhome; entry.linkSs = ss; }
    await fetch('/api/save/links', {
        method:  'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: id, link_myhome: myhome || null, link_ss: ss || null }),
    });
}

async function saveNote(id, text) {
    const entry = mySaves.get(id);
    if (entry) entry.note = text;
    await fetch('/api/save/note', {
        method:  'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: id, note: text }),
    });
}

async function removeSave(id) {
    const ok = await showConfirm('Remove listing?', 'This will remove it from your saved list for today.');
    if (!ok) return;
    const entry   = mySaves.get(id);
    const listing = entry?.listing || allListings.get(id) || { id };
    const res  = await fetch('/api/save', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: id, listing_snapshot: listing, my_price: null }),
    });
    const data = await res.json();
    if (!data.saved) {
        mySaves.delete(id);
        const btn = document.getElementById('save-btn-' + id);
        if (btn) { btn.textContent = '+ Save'; btn.classList.remove('saved'); }
        updateSavedBtn();
    }
}

async function clearSaved() {
    if (!mySaves.size) return;
    const ids = [...mySaves.keys()];
    await Promise.all(ids.map(id => removeSave(id)));
}

async function loadSaves() {
    if (!isAuthed) return;
    try {
        const [mineRes, allRes, team] = await Promise.all([
            fetch('/api/my-saves').then(r => r.json()),
            fetch('/api/all-saves').then(r => r.json()),
            fetch('/api/team-saves').then(r => r.json()),
        ]);
        saveLimit = mineRes.limit ?? 20;
        const mine = mineRes.saves ?? mineRes;
        Object.entries(mine).forEach(([id, entry]) => mySaves.set(id, {
            myPrice:    entry.my_price,
            listing:    entry.snapshot || null,
            note:       entry.note       || '',
            linkMyhome: entry.link_myhome || '',
            linkSs:     entry.link_ss     || '',
        }));
        archiveSaves.length = 0;
        allRes.forEach(e => archiveSaves.push(e));
        Object.entries(team).forEach(([id, entries]) => teamSaves.set(id, Array.isArray(entries) ? entries : [entries]));
        updateSavedBtn();
    } catch (e) {}
}

async function toggleSave(lid) {
    if (!isAuthed) { window.location = '/login'; return; }

    lid = String(lid);
    const l       = allListings.get(lid);
    const myPrice = document.getElementById('my-price-' + lid)?.value || '';
    const btn     = document.getElementById('save-btn-' + lid);

    const res  = await fetch('/api/save', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body:    JSON.stringify({ listing_id: lid, listing_snapshot: l || { id: lid }, my_price: myPrice || null }),
    });
    const data = await res.json();

    if (data.saved) {
        mySaves.set(lid, { myPrice, listing: l });
        if (btn) { btn.textContent = '✓ Saved'; btn.classList.add('saved'); }
        if (!panelOpen) togglePanel();
    } else {
        mySaves.delete(lid);
        if (btn) { btn.textContent = '+ Save'; btn.classList.remove('saved'); }
    }
    updateSavedBtn();
}

function exportSaved() {
    if (!mySaves.size) return;

    const headers = ['ID', 'Title', 'Address', 'Owner', 'Phone', 'Original Price', 'My Price', 'Diff %', 'Rent Type', 'Rooms', 'Bedrooms', 'Area', 'District', 'Note', 'myhome.ge', 'ss.ge', 'URL'];
    const dataRows = [];

    mySaves.forEach((entry, id) => {
        const l = allListings.get(id);
        if (!l) return;
        const orig = Number(l.price) || 0;
        const my   = Number(entry.myPrice) || 0;
        const pct  = orig && my ? Math.round((my - orig) / orig * 100) : '';
        dataRows.push([
            l.id, l.title, l.address, l.owner_name ?? '', l.phone ?? '',
            l.price ?? '', entry.myPrice ?? '', pct,
            l.rent_type ?? '', l.rooms ?? '', l.bedrooms ?? '', l.area ?? '', l.district ?? '',
            entry.note ?? '', entry.linkMyhome ?? '', entry.linkSs ?? '', l.url ?? '',
        ]);
    });

    const esc = v => String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const th  = headers.map(h => `<th>${esc(h)}</th>`).join('');
    const trs = dataRows.map(r => `<tr>${r.map(v => `<td>${esc(v)}</td>`).join('')}</tr>`).join('');
    const xml = `<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
 <Worksheet ss:Name="Listings">
  <Table><Row>${th}</Row>${trs}</Table>
 </Worksheet>
</Workbook>`;

    const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'my-listings-' + new Date().toISOString().slice(0, 10) + '.xls';
    a.click();
    URL.revokeObjectURL(a.href);
}

// ── View switcher ─────────────────────────────────────────────────────────────

function setView(val) {
    currentView = val;
    document.querySelectorAll('.vsw-btn').forEach(b => b.classList.toggle('active', b.dataset.view === val));
    if (val === 'all') {
        startStream();
    } else {
        markers.clearLayers();
        allListings.forEach(l => {
            if (mySaves.has(String(l.id))) addMarker(l);
        });
        setStatus(mySaves.size + ' saved', false);
    }
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

// ── Custom / Exclusive listing modal ──────────────────────────────────────

function openCustomModal() {
    document.getElementById('custom-form').reset();
    document.getElementById('custom-form-error').style.display = 'none';
    document.getElementById('custom-modal').classList.add('open');
}

function closeCustomModal() {
    document.getElementById('custom-modal').classList.remove('open');
}

document.getElementById('custom-modal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('custom-modal')) closeCustomModal();
});

async function submitCustomListing(e) {
    e.preventDefault();
    const btn   = document.getElementById('cm-submit-btn');
    const errEl = document.getElementById('custom-form-error');
    btn.disabled    = true;
    btn.textContent = 'Saving…';
    errEl.style.display = 'none';

    const data = Object.fromEntries(new FormData(document.getElementById('custom-form')));

    try {
        const res  = await fetch('/api/save/custom', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body:    JSON.stringify(data),
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'Failed to save');

        mySaves.set(json.listing_id, {
            myPrice:    json.my_price    || null,
            listing:    json.snapshot,
            note:       json.note        || '',
            linkMyhome: json.link_myhome || '',
            linkSs:     json.link_ss     || '',
        });
        updateSavedBtn();
        closeCustomModal();
    } catch (err) {
        errEl.textContent    = err.message;
        errEl.style.display  = 'block';
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Save Listing';
    }
}

// ── Init ──────────────────────────────────────────────────────────────────

window.addEventListener('load', () => {
    setLang(lang);
    loadSaves().then(() => startStream());
});
</script>
</body>
</html>
