<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 40px; width: 100%; max-width: 380px; }
        .logo { font-size: 22px; font-weight: 700; letter-spacing: -.5px; margin-bottom: 28px; }
        .logo span { color: #e05c6e; }
        .error { background: #3b1a1a; border: 1px solid #7f1d1d; color: #fca5a5; border-radius: 8px; padding: 11px 14px; margin-bottom: 18px; font-size: 14px; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=email], input[type=password] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 15px; outline: none; }
        input:focus { border-color: #4f6ef7; }
        .field { margin-bottom: 20px; }
        .remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #94a3b8; margin-bottom: 20px; }
        button { width: 100%; background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 15px; font-weight: 600; cursor: pointer; }
        button:hover { background: #3b5be0; }
        .footer { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b; }
        .footer a { color: #4f6ef7; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">MYHOME-<span>MAP</span></div>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="remember">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember" style="margin:0">Remember me</label>
        </div>
        <button type="submit">Sign In</button>
    </form>

    <div class="footer">
        Access is by invitation only.
    </div>
</div>
</body>
</html>
