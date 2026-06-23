@extends('admin.layout')
@section('title', $org->name)

@section('content')

@if(session('ceo_link'))
    <div class="alert alert-success">✓ New CEO invite link:<br><br><strong>{{ session('ceo_link') }}</strong></div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="margin-bottom:16px">
    <a href="{{ route('admin.orgs') }}" class="btn btn-ghost btn-sm">← Organizations</a>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

    <div>
        <!-- Members -->
        <div class="card">
            <div class="card-title">Members ({{ $org->users->count() }} / {{ $org->user_limit }})</div>
            @if($org->users->isEmpty())
                <div class="empty">No members yet. Share the CEO invite link.</div>
            @else
            <table class="tbl">
                <thead><tr><th>Name</th><th>Role</th><th>Plan</th><th>Saves</th><th>Joined</th><th></th></tr></thead>
                <tbody>
                @foreach($org->users as $u)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $u->name }}</div>
                        <div style="font-size:11px;color:#4d5780">{{ $u->email }}</div>
                    </td>
                    <td><span class="badge badge-{{ $u->role }}">{{ $u->role }}</span></td>
                    <td><span class="badge badge-{{ $u->plan }}">{{ $u->plan }}</span></td>
                    <td style="color:#4d5780">{{ $u->savedListings->count() }}</td>
                    <td style="color:#4d5780;font-size:12px">{{ $u->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('admin.users.edit', $u) }}" class="btn btn-ghost btn-sm">Edit</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <!-- Recent saves -->
        <div class="card">
            <div class="card-title">Team Saves</div>
            @if($saves->isEmpty())
                <div class="empty">No saves yet.</div>
            @else
            <table class="tbl">
                <thead><tr><th>Employee</th><th>Listing</th><th>My Price</th><th>Saved</th></tr></thead>
                <tbody>
                @foreach($saves as $s)
                @php $l = $s->listing_snapshot; @endphp
                <tr>
                    <td><span class="badge badge-employee">{{ $s->user->name }}</span></td>
                    <td>
                        <div style="font-size:13px;font-weight:600">{{ Str::limit($l['title'] ?? '—', 40) }}</div>
                        <div style="font-size:11px;color:#4d5780">{{ $l['address'] ?? '' }}</div>
                    </td>
                    <td>
                        @if($s->my_price)
                            <span style="color:#e94560;font-weight:600">${{ number_format($s->my_price,0) }}</span>
                            <span style="color:#4d5780;font-size:11px"> / ${{ number_format($l['price'] ?? 0, 0) }}</span>
                        @else
                            <span style="color:#4d5780">${{ number_format($l['price'] ?? 0, 0) }}</span>
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

    <div>
        <!-- Settings -->
        <div class="card">
            <div class="card-title">Settings</div>
            <form method="POST" action="{{ route('admin.orgs.update', $org) }}">
                @csrf @method('PATCH')
                <div class="form-grid cols-1" style="gap:14px">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ $org->name }}" required>
                    </div>
                    <div class="field">
                        <label>User Limit</label>
                        <input type="number" name="user_limit" value="{{ $org->user_limit }}" min="1" max="500" required>
                    </div>
                </div>
                <div class="toggle-row" style="margin-top:16px">
                    <div class="toggle-label">
                        <span>Show Team Saves</span>
                        <small>Employees see teammates' pins</small>
                    </div>
                    <input type="checkbox" name="show_team_saves" value="1" {{ $org->show_team_saves ? 'checked' : '' }}>
                </div>
                <button class="btn btn-primary" style="margin-top:18px;width:100%" type="submit">Save Changes</button>
            </form>
        </div>

        <!-- Invite -->
        <div class="card">
            <div class="card-title">CEO Invite Link</div>
            <p style="font-size:13px;color:#4d5780;margin-bottom:14px">Generate a new link for a CEO to register.</p>
            <form method="POST" action="{{ route('admin.org.invite', $org) }}">
                @csrf
                <button class="btn btn-ghost" style="width:100%" type="submit">Generate CEO Link</button>
            </form>
        </div>

        <!-- Delete -->
        <div class="card">
            <div class="card-title">Danger Zone</div>
            <p style="font-size:13px;color:#4d5780;margin-bottom:14px">Deletes the org and removes all members from it.</p>
            <form method="POST" action="{{ route('admin.orgs.delete', $org) }}"
                  onsubmit="return confirm('Delete {{ $org->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger" style="width:100%" type="submit">Delete Organization</button>
            </form>
        </div>
    </div>

</div>

@endsection
