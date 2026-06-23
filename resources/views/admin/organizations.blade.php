@extends('admin.layout')
@section('title', 'Organizations')

@section('content')

@if(session('ceo_link'))
    <div class="alert alert-success">✓ CEO invite link (expires in 7 days):<br><br><strong>{{ session('ceo_link') }}</strong></div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <div class="card">
        <div class="card-title">All Organizations</div>
        @if($orgs->isEmpty())
            <div class="empty">No organizations yet.</div>
        @else
        <table class="tbl">
            <thead>
                <tr><th>Name</th><th>Members</th><th>Limit</th><th>Team Saves</th><th>Created</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($orgs as $org)
            <tr>
                <td><a href="{{ route('admin.orgs.show', $org) }}" style="color:#7dd3fc;text-decoration:none;font-weight:600">{{ $org->name }}</a></td>
                <td>{{ $org->users_count }} / {{ $org->user_limit }}</td>
                <td style="color:#4d5780">{{ $org->user_limit }}</td>
                <td>
                    <span class="badge {{ $org->show_team_saves ? 'badge-pro' : 'badge-free' }}">
                        {{ $org->show_team_saves ? 'On' : 'Off' }}
                    </span>
                </td>
                <td style="color:#4d5780;font-size:12px">{{ $org->created_at->format('M d, Y') }}</td>
                <td style="text-align:right">
                    <a href="{{ route('admin.orgs.show', $org) }}" class="btn btn-ghost btn-sm">Manage</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="card">
        <div class="card-title">New Organization</div>
        <form method="POST" action="{{ route('admin.org.create') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            <div class="form-grid cols-1" style="gap:14px">
                <div class="field">
                    <label>Company Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Acme Real Estate" required>
                </div>
                <div class="field">
                    <label>User Limit</label>
                    <input type="number" name="user_limit" value="{{ old('user_limit', 10) }}" min="1" max="500" required>
                </div>
                <div class="field">
                    <label>Company Logo <span style="color:#4d5780;font-weight:400">(optional)</span></label>
                    <input type="file" name="logo" accept="image/*"
                           style="background:#0b0d14;border:1px solid #1e2235;border-radius:8px;padding:8px 12px;color:#94a3b8;font-size:13px;width:100%;cursor:pointer">
                    <div style="font-size:11px;color:#4d5780;margin-top:5px">PNG, JPG or SVG — shown on map header and invite page</div>
                </div>
            </div>
            <button class="btn btn-primary" style="margin-top:18px;width:100%" type="submit">
                Create & Get CEO Invite Link
            </button>
        </form>
    </div>

</div>

@endsection
