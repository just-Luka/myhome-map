@php
$lang = in_array(request('lang'), ['en', 'ka']) ? request('lang') : 'en';
$t = [
    'en' => [
        'title'    => 'Join',
        'invited'  => "You've been invited to join",
        'owner'    => 'Owner',
        'employee' => 'Employee',
        'name'     => 'Full Name',
        'email'    => 'Email',
        'password' => 'Password',
        'confirm'  => 'Confirm Password',
        'submit'   => 'Create Account & Join',
    ],
    'ka' => [
        'title'    => 'შეუერთდით',
        'invited'  => 'თქვენ მოწვეული ხართ, რომ შეუერთდეთ',
        'owner'    => 'მფლობელი',
        'employee' => 'თანამშრომელი',
        'name'     => 'სრული სახელი',
        'email'    => 'ელ. ფოსტა',
        'password' => 'პაროლი',
        'confirm'  => 'პაროლის დადასტურება',
        'submit'   => 'ანგარიშის შექმნა და შეერთება',
    ],
][$lang];
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $t['title'] }} {{ $invite->organization->name }} — MYHOME-MAP</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #1a1d27; border: 1px solid #2d3149; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; }
        .logo { font-size: 22px; font-weight: 700; letter-spacing: -.5px; margin-bottom: 6px; }
        .logo span { color: #e05c6e; }
        .org-name { font-size: 13px; color: #64748b; margin-bottom: 28px; }
        .role-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;
            background: {{ $invite->role === 'ceo' ? '#1e2a3a' : '#1a2535' }};
            color: {{ $invite->role === 'ceo' ? '#7dd3fc' : '#86efac' }};
            margin-bottom: 24px; }
        .error { background: #3b1a1a; border: 1px solid #7f1d1d; color: #fca5a5; border-radius: 8px; padding: 11px 14px; margin-bottom: 18px; font-size: 14px; }
        label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; }
        input[type=text], input[type=email], input[type=password] { width: 100%; background: #0f1117; border: 1px solid #2d3149; border-radius: 8px; padding: 10px 14px; color: #e2e8f0; font-size: 15px; outline: none; }
        input:focus { border-color: #4f6ef7; }
        .field { margin-bottom: 18px; }
        button { width: 100%; background: #4f6ef7; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 15px; font-weight: 600; cursor: pointer; }
        button:hover { background: #3b5be0; }
    </style>
</head>
<body>
<div class="card">
    @if($invite->organization->logo)
        <img src="{{ Storage::url($invite->organization->logo) }}" alt="{{ $invite->organization->name }}"
             style="max-height:48px;max-width:160px;object-fit:contain;margin-bottom:12px;display:block">
    @else
        <div class="logo">MYHOME-<span>MAP</span></div>
    @endif
    <div class="org-name">{{ $t['invited'] }} <strong>{{ $invite->organization->name }}</strong></div>
    <div class="role-badge">{{ $invite->role === 'ceo' ? $t['owner'] : $t['employee'] }}</div>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('invite.accept', $invite->token) }}">
        @csrf
        <input type="hidden" name="lang" value="{{ $lang }}">
        <div class="field">
            <label>{{ $t['name'] }}</label>
            <input type="text" name="name" value="{{ old('name') }}" autofocus required>
        </div>
        <div class="field">
            <label>{{ $t['email'] }}</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label>{{ $t['password'] }}</label>
            <input type="password" name="password" required>
        </div>
        <div class="field">
            <label>{{ $t['confirm'] }}</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">{{ $t['submit'] }}</button>
    </form>
</div>
</body>
</html>
