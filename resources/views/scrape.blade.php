<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrape — myhome-map</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 36px; width: 100%; max-width: 560px; }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 24px; color: #f1f5f9; }
        .error { background: #3b1a1a; border: 1px solid #7f1d1d; color: #fca5a5; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 14px; }
        .result { background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 14px 16px; margin-top: 20px; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px; line-height: 1.7; color: #a3e4d7; white-space: pre-wrap; max-height: 340px; overflow-y: auto; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=password], input[type=number] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 15px; outline: none; }
        input[type=password]:focus, input[type=number]:focus { border-color: #4f6ef7; }
        .field { margin-bottom: 18px; }
        button { width: 100%; background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background .15s; }
        button:hover { background: #3b5be0; }
        .controls { display: flex; gap: 10px; }
        .controls .field { flex: 1; margin-bottom: 0; }
        .controls button { flex: 0 0 auto; width: auto; padding: 11px 24px; }
        .note { font-size: 13px; color: #64748b; margin-top: 14px; }
    </style>
</head>
<body>
<div class="card">
    <h1>myhome-map scraper</h1>

    @if (session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if (! session('scrape_auth'))
        <form method="POST" action="{{ route('scrape.auth') }}">
            @csrf
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autofocus placeholder="Enter password">
            </div>
            <button type="submit">Unlock</button>
        </form>
    @else
        <form method="POST" action="{{ route('scrape.run') }}">
            @csrf
            <div class="controls">
                <div class="field">
                    <label for="pages">Pages (1–30)</label>
                    <input type="number" id="pages" name="pages" value="30" min="1" max="30">
                </div>
                <button type="submit" onclick="this.textContent='Running…';this.disabled=true;this.form.submit();">Run scrape</button>
            </div>
        </form>
        <p class="note">This may take several minutes. Keep the page open until it finishes.</p>

        @if (session('result'))
            <div class="result">{{ session('result') }}</div>
        @endif
    @endif
</div>
</body>
</html>
