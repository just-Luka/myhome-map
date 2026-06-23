@extends('admin.layout')
@section('title', 'Users')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div class="card-title" style="margin:0">All Users</div>
        <form method="GET" style="display:flex;gap:8px">
            <select name="role" onchange="this.form.submit()" style="background:#0b0d14;border:1px solid #1e2235;border-radius:8px;padding:6px 10px;color:#e2e8f0;font-size:13px">
                <option value="">All roles</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="ceo"         {{ request('role') === 'ceo'         ? 'selected' : '' }}>CEO</option>
                <option value="employee"      {{ request('role') === 'employee'      ? 'selected' : '' }}>Employee</option>
                <option value="free"        {{ request('role') === 'free'        ? 'selected' : '' }}>Free</option>
            </select>
            <select name="plan" onchange="this.form.submit()" style="background:#0b0d14;border:1px solid #1e2235;border-radius:8px;padding:6px 10px;color:#e2e8f0;font-size:13px">
                <option value="">All plans</option>
                <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
            </select>
        </form>
    </div>

    @if($users->isEmpty())
        <div class="empty">No users found.</div>
    @else
    <table class="tbl">
        <thead>
            <tr><th>Name</th><th>Role</th><th>Plan</th><th>Organization</th><th>Saves</th><th>Joined</th><th></th></tr>
        </thead>
        <tbody>
        @foreach($users as $u)
        <tr>
            <td>
                <div style="font-weight:600">{{ $u->name }}</div>
                <div style="font-size:11px;color:#4d5780">{{ $u->email }}</div>
            </td>
            <td><span class="badge badge-{{ $u->role }}">{{ $u->role }}</span></td>
            <td><span class="badge badge-{{ $u->plan }}">{{ $u->plan }}</span></td>
            <td>
                @if($u->organization)
                    <a href="{{ route('admin.orgs.show', $u->organization) }}" style="color:#a3e635;text-decoration:none;font-size:13px">{{ $u->organization->name }}</a>
                @else
                    <span style="color:#4d5780">—</span>
                @endif
            </td>
            <td style="color:#4d5780">{{ $u->saved_listings_count }}</td>
            <td style="color:#4d5780;font-size:12px">{{ $u->created_at->format('M d, Y') }}</td>
            <td style="text-align:right">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-ghost btn-sm">Edit</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $users->withQueryString()->links('pagination::simple-default') }}</div>
    @endif
</div>

@endsection
