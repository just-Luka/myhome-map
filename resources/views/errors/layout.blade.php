<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — MYHOME-MAP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0b0d14; color: #e2e8f0;
            min-height: 100vh; display: flex; flex-direction: column;
            align-items: center; justify-content: center; padding: 24px;
        }
        .logo { font-size: 18px; font-weight: 800; letter-spacing: -.5px; color: #fff; margin-bottom: 48px; text-decoration: none; }
        .logo span { color: #e94560; }
        .code {
            font-size: 120px; font-weight: 900; line-height: 1;
            background: linear-gradient(135deg, #e94560 0%, #f97316 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: 16px; letter-spacing: -4px;
        }
        .title { font-size: 22px; font-weight: 700; color: #e2e8f0; margin-bottom: 10px; }
        .message { font-size: 14px; color: #64748b; max-width: 360px; text-align: center; line-height: 1.7; margin-bottom: 40px; }
        .btn {
            display: inline-block; background: #e94560; color: #fff; text-decoration: none;
            border-radius: 10px; padding: 12px 32px; font-size: 14px; font-weight: 700;
            transition: background 0.15s, transform 0.1s; border: none; cursor: pointer;
        }
        .btn:hover { background: #c73652; transform: translateY(-1px); }
        .btn-ghost {
            background: transparent; border: 1px solid #2d3149; color: #94a3b8; margin-left: 10px;
        }
        .btn-ghost:hover { background: #1a1d27; transform: translateY(-1px); }
        .divider { width: 40px; height: 3px; background: linear-gradient(90deg,#e94560,#f97316); border-radius: 2px; margin: 0 auto 28px; }
    </style>
</head>
<body>
    <a href="/" class="logo">MYHOME<span>-MAP</span></a>
    <div class="code">@yield('code')</div>
    <div class="divider"></div>
    <div class="title">@yield('title')</div>
    <div class="message">@yield('message')</div>
    <div>
        <a href="/" class="btn">Back to Map</a>
        @yield('extra_action')
    </div>
</body>
</html>
