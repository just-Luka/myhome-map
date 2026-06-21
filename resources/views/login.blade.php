<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 40px; width: 100%; max-width: 380px; }
        .logo { font-size: 22px; font-weight: 700; letter-spacing: -.5px; margin-bottom: 28px; }
        .logo span { color: #e05c6e; }
        .error { background: #3b1a1a; border: 1px solid #7f1d1d; color: #fca5a5; border-radius: 8px; padding: 11px 14px; margin-bottom: 18px; font-size: 14px; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=password] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 15px; outline: none; }
        input[type=password]:focus { border-color: #4f6ef7; }
        .field { margin-bottom: 20px; }
        button { width: 100%; background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 15px; font-weight: 600; cursor: pointer; }
        button:hover { background: #3b5be0; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">MYHOME-<span>MAP</span></div>

    @if (session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autofocus placeholder="Enter password">
        </div>
        <button type="submit">Enter</button>
    </form>
</div>
</body>
</html>
