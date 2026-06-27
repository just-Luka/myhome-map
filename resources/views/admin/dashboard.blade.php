@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

<div class="stats">
    <div class="stat">
        <div class="val">{{ $stats['users'] }}</div>
        <div class="lbl">Total Users</div>
    </div>
    <div class="stat">
        <div class="val">{{ $stats['orgs'] }}</div>
        <div class="lbl">Organizations</div>
    </div>
    <div class="stat">
        <div class="val">{{ $stats['listings'] }}</div>
        <div class="lbl">Active Listings</div>
    </div>
    <div class="stat">
        <div class="val">{{ $stats['saves'] }}</div>
        <div class="lbl">Total Saves</div>
    </div>
    <div class="stat">
        <div class="val">{{ $stats['pro_users'] }}</div>
        <div class="lbl">Pro Users</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <div class="card">
        <div class="card-title">Recent Signups</div>
        @if($recentUsers->isEmpty())
            <div class="empty">No users yet.</div>
        @else
        <table>
            <thead><tr><th>Name</th><th>Role</th><th>Joined</th></tr></thead>
            <tbody>
            @foreach($recentUsers as $u)
            <tr>
                <td>
                    <div style="font-weight:600">{{ $u->name }}</div>
                    <div style="font-size:11px;color:#4d5780">{{ $u->email }}</div>
                </td>
                <td><span class="badge badge-{{ $u->role }}">{{ $u->role }}</span></td>
                <td style="color:#4d5780;font-size:12px">{{ $u->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="card">
        <div class="card-title">Recent Activity</div>
        @if($recentSaves->isEmpty())
            <div class="empty">No saves yet.</div>
        @else
        <table>
            <thead><tr><th>Employee</th><th>Listing</th><th>When</th></tr></thead>
            <tbody>
            @foreach($recentSaves as $s)
            @php $l = $s->listing_snapshot; @endphp
            <tr>
                <td style="font-weight:600">{{ $s->user->name }}</td>
                <td>
                    <div style="font-size:12px">{{ Str::limit($l['title'] ?? '—', 30) }}</div>
                    @if($s->my_price)
                        <div style="font-size:11px;color:#e94560">${{ number_format($s->my_price, 0) }}</div>
                    @endif
                </td>
                <td style="color:#4d5780;font-size:12px">{{ $s->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

@endsection
