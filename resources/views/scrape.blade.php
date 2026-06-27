<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraper — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 36px; width: 100%; max-width: 560px; }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; color: #f1f5f9; }
        .sub { font-size: 13px; color: #4d5780; margin-bottom: 24px; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=number] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 15px; outline: none; }
        input[type=number]:focus { border-color: #4f6ef7; }
        .field { margin-bottom: 18px; }
        button { background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 11px 24px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background .15s; }
        button:hover { background: #3b5be0; }
        button:disabled { background: #2d3149; color: #64748b; cursor: not-allowed; }
        .controls { display: flex; gap: 10px; align-items: flex-end; }
        .controls .field { flex: 1; margin-bottom: 0; }
        #terminal-wrap { display: none; margin-top: 24px; }
        #terminal { background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 14px 16px; height: 340px; overflow-y: auto; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px; line-height: 1.6; color: #a3e4d7; }
        #terminal .line { white-space: pre-wrap; word-break: break-all; }
        #terminal .line.done { color: #6ee7b7; font-weight: 600; }
        #terminal .line.fail { color: #f87171; font-weight: 600; }
        .back { display: inline-block; margin-bottom: 20px; font-size: 13px; color: #64748b; text-decoration: none; }
        .back:hover { color: #e2e8f0; }
    </style>
</head>
<body>
<div class="card">
    <a href="{{ route('admin.index') }}" class="back">← Admin Panel</a>
    <h1>myhome-map scraper</h1>
    <div class="sub">Logged in as {{ auth()->user()->name }}</div>

    <div class="controls">
        <div class="field">
            <label for="pages">Pages (1–30)</label>
            <input type="number" id="pages" value="30" min="1" max="30">
        </div>
        <div class="field">
            <label for="delay">Page delay (ms)</label>
            <input type="number" id="delay" value="400" min="200" max="5000" step="100">
        </div>
        <div class="field">
            <label for="detail-delay">Detail delay (ms)</label>
            <input type="number" id="detail-delay" value="700" min="300" max="5000" step="100">
        </div>
        <button id="run-btn" onclick="startScrape()">Run scrape</button>
    </div>

    <div id="terminal-wrap">
        <div id="terminal"></div>
    </div>
</div>

<script>
let source = null;

function startScrape() {
    const btn      = document.getElementById('run-btn');
    const pages       = Math.max(1, Math.min(30, parseInt(document.getElementById('pages').value) || 30));
    const delay       = Math.max(200, parseInt(document.getElementById('delay').value) || 400);
    const detailDelay = Math.max(300, parseInt(document.getElementById('detail-delay').value) || 700);
    const terminal = document.getElementById('terminal');
    const wrap     = document.getElementById('terminal-wrap');

    if (source) { source.close(); source = null; }

    terminal.innerHTML = '';
    wrap.style.display = 'block';
    btn.disabled = true;
    btn.textContent = 'Running…';

    source = new EventSource(`{{ route('scrape.run') }}?pages=${pages}&delay=${delay}&detail_delay=${detailDelay}`);

    source.onmessage = function (e) {
        const msg = JSON.parse(e.data);
        if (msg.done) {
            source.close(); source = null;
            btn.disabled = false;
            btn.textContent = 'Run scrape';
            return;
        }
        if (msg.line !== undefined) {
            const div = document.createElement('div');
            div.className = 'line' + (msg.line.startsWith('✓') ? ' done' : msg.line.startsWith('✗') ? ' fail' : '');
            div.textContent = msg.line;
            terminal.appendChild(div);
            terminal.scrollTop = terminal.scrollHeight;
        }
    };

    source.onerror = function () {
        source.close(); source = null;
        btn.disabled = false;
        btn.textContent = 'Run scrape';
        const div = document.createElement('div');
        div.className = 'line fail';
        div.textContent = '✗ Connection lost.';
        terminal.appendChild(div);
        terminal.scrollTop = terminal.scrollHeight;
    };
}
</script>
</body>
</html>
