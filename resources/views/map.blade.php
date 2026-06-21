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
        #status-bar { color: #aaa; font-size: 13px; margin-left: auto; display: flex; align-items: center; gap: 8px; }
        #live-dot { width: 8px; height: 8px; border-radius: 50%; background: #e94560; display: none; animation: pulse 1s infinite; flex-shrink: 0; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

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
        .popup-link {
            display: block; background: #1a1a2e; color: #fff; text-align: center;
            padding: 10px; text-decoration: none; font-size: 13px; font-weight: 600;
            border-radius: 8px; transition: background 0.2s;
        }
        .popup-link:hover { background: #e94560; }
    </style>
</head>
<body>

<div id="header">
    <h1>MYHOME<span>-MAP</span></h1>
    <div id="status-bar">
        <div id="live-dot"></div>
        <span id="status-text"></span>
    </div>
    <div id="lang-switcher">
        <button class="lang-btn active" onclick="setLang('en')">EN</button>
        <button class="lang-btn" onclick="setLang('ru')">RU</button>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
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
let openDrop = null;

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
    if (activeStream) { activeStream.close(); activeStream = null; }
    markers.clearLayers();
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

    let count = 0;
    activeStream = new EventSource('/api/stream?' + params.toString());

    activeStream.onmessage = (e) => {
        const data = JSON.parse(e.data);
        if (data.done) {
            activeStream.close(); activeStream = null;
            setStatus(tr('n_found', data.total), false);
            return;
        }
        if (data.status) { setStatus(data.status, true); return; }
        if (data.listing) { count++; addMarker(data.listing); setStatus(tr('finding', count), true); }
    };

    activeStream.onerror = () => {
        activeStream?.close(); activeStream = null;
        setStatus(count ? tr('n_found', count) : tr('error'), false);
    };
}

function addMarker(l) {
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
            <a class="popup-link" href="${l.url}" target="_blank" rel="noopener">${tr('view')}</a>
        </div>
    `, { maxWidth: 310 });

    markers.addLayer(marker);
    if (markers.getLayers().length === 1) map.setView([l.lat, l.lng], 13);
}

// ── Init ──────────────────────────────────────────────────────────────────

window.addEventListener('load', () => {
    setLang(lang);
    startStream();
});
</script>
</body>
</html>
