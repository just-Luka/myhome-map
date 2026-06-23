<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; padding: 40px 20px; }
        .wrap { max-width: 860px; margin: 0 auto; }
        h1 { font-size: 20px; font-weight: 700; margin-bottom: 32px; }
        h1 span { color: #e05c6e; }
        h2 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 16px; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 28px; margin-bottom: 24px; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=text], input[type=number] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 14px; outline: none; margin-bottom: 14px; }
        input:focus { border-color: #4f6ef7; }
        .btn { background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #3b5be0; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .alert { background: #1a2a1a; border: 1px solid #2e5c2e; color: #86efac; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: 13px; word-break: break-all; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #64748b; padding: 0 0 10px; border-bottom: 1px solid #2d3149; }
        td { padding: 12px 0; font-size: 13px; border-bottom: 1px solid #1e2235; vertical-align: middle; }
        td:last-child { text-align: right; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #1e2a3a; color: #7dd3fc; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>MYHOME-<span>MAP</span> · Super Admin</h1>

    @if (session('ceo_link'))
        <div class="alert">
            ✓ CEO invite link (expires in 7 days):<br><br>
            <strong>{{ session('ceo_link') }}</strong>
        </div>
    @endif

    <div class="card">
        <h2>Add Organization</h2>
        <form method="POST" action="{{ route('admin.org.create') }}">
            @csrf
            @error('name') <p style="color:#fca5a5;font-size:13px;margin-bottom:8px">{{ $message }}</p> @enderror
            <label>Company Name</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Acme Real Estate" required>
            <label>User Limit</label>
            <input type="number" name="user_limit" value="{{ old('user_limit', 10) }}" min="1" max="500" required>
            <button class="btn" type="submit">Create & Get CEO Invite Link</button>
        </form>
    </div>

    <div class="card">
        <h2>Organizations</h2>
        @if ($orgs->isEmpty())
            <p style="color:#64748b;font-size:13px">No organizations yet.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Members</th>
                    <th>Limit</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($orgs as $org)
                <tr>
                    <td>{{ $org->name }}</td>
                    <td><span class="badge">{{ $org->users_count }}</span></td>
                    <td style="color:#64748b">{{ $org->user_limit }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.org.invite', $org) }}">
                            @csrf
                            <button class="btn btn-sm" type="submit">New CEO Link</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
</body>
</html>
