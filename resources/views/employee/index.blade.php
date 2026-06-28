@extends('dashboard.layout')

@section('title', 'Employees')

@section('styles')
.progress-bar { height: 4px; background: var(--card-border); border-radius: 2px; margin-top: 6px; width: 100px; }
.progress-fill { height: 4px; border-radius: 2px; transition: width .3s; }
.progress-fill.ok   { background: #4f6ef7; }
.progress-fill.warn { background: #f59e0b; }
.progress-fill.full { background: #e94560; }
@endsection

@section('topbar-actions')
<form method="POST" action="{{ route('owner.invite') }}">
    @csrf
    <button class="btn btn-primary btn-sm" type="submit">+ Invite Employee</button>
</form>
@endsection

@section('content')

@if(session('employee_link'))
    <div class="alert alert-success">
        ✓ Invite link (expires in 7 days):<br><br>
        <strong id="invite-url">{{ session('employee_link') }}</strong>
        <button class="copy-btn" style="margin-left:10px" onclick="navigator.clipboard.writeText(document.getElementById('invite-url').textContent)">Copy</button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="stats">
    <div class="stat">
        <div class="val">{{ $employees->count() }}</div>
        <div class="lbl">Employees</div>
    </div>
    <div class="stat">
        <div class="val">{{ $employees->sum('total') }}</div>
        <div class="lbl">Total Saves</div>
    </div>
    <div class="stat">
        <div class="val">{{ $employees->sum('this_week') }}</div>
        <div class="lbl">This Week</div>
    </div>
    <div class="stat">
        <div class="val">{{ $employees->sum('today') }}</div>
        <div class="lbl">Saved Today</div>
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:16px">Team</div>

    @if($employees->isEmpty())
        <div class="empty">No employees yet. Use the Invite button to add someone.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Total</th>
                <th>This Week</th>
                <th>Today / Limit</th>
                <th>Last Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $e)
        @php
            $u    = $e['user'];
            $pct  = $e['limit'] > 0 ? min(100, $e['today'] / $e['limit'] * 100) : 0;
            $cls  = $pct >= 100 ? 'full' : ($pct >= 75 ? 'warn' : 'ok');
        @endphp
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="employee-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                    <div>
                        <div class="employee-name">{{ $u->name }}</div>
                        <div class="employee-meta">{{ $u->email }}</div>
                    </div>
                </div>
            </td>
            <td style="font-weight:600;color:var(--brand)">{{ $e['total'] }}</td>
            <td style="color:var(--subtle)">{{ $e['this_week'] }}</td>
            <td>
                <div style="font-size:12px">{{ $e['today'] }} / {{ $e['limit'] }}</div>
                <div class="progress-bar"><div class="progress-fill {{ $cls }}" style="width:{{ $pct }}%"></div></div>
            </td>
            <td class="listing-when">{{ $e['last_save'] ? $e['last_save']->diffForHumans() : '—' }}</td>
            <td>
                <div style="display:flex;gap:6px;align-items:center">
                    <a href="{{ route('owner.employees.show', $u->id) }}" class="btn btn-ghost btn-sm">View →</a>
                    <form id="remove-form-{{ $u->id }}" method="POST" action="{{ route('owner.employees.destroy', $u->id) }}">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" class="btn btn-danger btn-sm"
                            onclick="confirmRemove({{ $u->id }}, '{{ addslashes($u->name) }}')">Remove</button>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@if($formerEmployees->isNotEmpty())
<div class="card" style="margin-top:24px">
    <div class="card-title" style="margin-bottom:16px">Former Employees</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Saved Listings</th>
                <th>Last Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($formerEmployees as $e)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="employee-avatar" style="opacity:.6">{{ strtoupper(substr($e['user']->name, 0, 1)) }}</div>
                    <div>
                        <div class="employee-name" style="color:var(--muted)">{{ $e['user']->name }}</div>
                        <div class="employee-meta">{{ $e['user']->email }}</div>
                    </div>
                </div>
            </td>
            <td style="font-weight:600;color:var(--subtle)">{{ $e['total'] }}</td>
            <td class="listing-when">{{ $e['last_save'] ? $e['last_save']->diffForHumans() : '—' }}</td>
            <td>
                <a href="{{ route('owner.employees.export-former', $e['user']->id) }}" class="btn btn-ghost btn-sm">⬇ Export CSV</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@section('scripts')
<script>
function confirmRemove(id, name) {
    confirmAction({
        heading:     'Remove employee?',
        body:        name + ' will be removed from the team. Their saved listings will be kept.',
        confirmText: 'Remove',
        onConfirm:   () => document.getElementById('remove-form-' + id).submit(),
    });
}
</script>
@endsection
