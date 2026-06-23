@extends('admin.layout')
@section('title', 'Edit User')

@section('content')

<div style="margin-bottom:16px">
    <a href="{{ route('admin.users') }}" class="btn btn-ghost btn-sm">← Users</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start">

    <div class="card">
        <div class="card-title">Edit {{ $user->name }}</div>
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PATCH')
            <div class="form-grid" style="gap:16px">
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="field">
                    <label>Role</label>
                    <select name="role">
                        @foreach(['free','employee','ceo','super_admin'] as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Plan</label>
                    <select name="plan">
                        <option value="free" {{ old('plan', $user->plan) === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="pro" {{ old('plan', $user->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>
                <div class="field">
                    <label>Organization</label>
                    <select name="organization_id">
                        <option value="">— None —</option>
                        @foreach($orgs as $org)
                            <option value="{{ $org->id }}" {{ old('organization_id', $user->organization_id) == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>New Password <span style="color:#4d5780;font-weight:400">(leave blank to keep)</span></label>
                    <input type="password" name="password" minlength="8">
                </div>
            </div>
            <button class="btn btn-primary" style="margin-top:20px" type="submit">Save Changes</button>
        </form>
    </div>

    <div>
        <div class="card">
            <div class="card-title">Account Info</div>
            <table class="tbl">
                <tr><td style="color:#4d5780">ID</td><td>#{{ $user->id }}</td></tr>
                <tr><td style="color:#4d5780">Saves</td><td>{{ $user->savedListings->count() }}</td></tr>
                <tr><td style="color:#4d5780">Joined</td><td style="font-size:12px">{{ $user->created_at->format('M d, Y') }}</td></tr>
            </table>
        </div>

        @if(!$user->isSuperAdmin() || $user->id !== auth()->id())
        <div class="card">
            <div class="card-title">Danger Zone</div>
            <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger" style="width:100%" type="submit">Delete User</button>
            </form>
        </div>
        @endif
    </div>

</div>

@endsection
