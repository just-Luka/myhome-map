@extends('dashboard.layout')

@section('title', $user->name)

@section('styles')
.limit-form { display:flex; align-items:center; gap:6px; }
.employee-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.employee-header-info { display:flex; align-items:center; gap:12px; }
.employee-header-avatar { width:40px; height:40px; font-size:16px; }
.employee-header-name { font-weight:700; font-size:15px; }
.employee-header-email { font-size:12px; color:var(--muted); margin-top:2px; }
.limit-label { font-size:12px; color:var(--subtle); }
.btn-reset { color:var(--danger-text); border-color:var(--danger-bg); }
.custom-limit-badge { color:var(--warning); }
@endsection

@section('topbar-actions')
<a href="{{ route('owner.employees') }}" class="btn btn-ghost btn-sm">← Employees</a>
<a href="{{ route('owner.employees.export', $user->id) }}" class="btn btn-green btn-sm">⬇ Export CSV</a>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="stats">
    <div class="stat">
        <div class="val">{{ $saves->count() }}</div>
        <div class="lbl">Total Saves</div>
    </div>
    <div class="stat">
        <div class="val">{{ $weekCount }}</div>
        <div class="lbl">This Week</div>
    </div>
    <div class="stat">
        @php $pct = $userLimit > 0 ? min(100, round($todayCount / $userLimit * 100)) : 0; @endphp
        <div class="val">{{ $todayCount }}</div>
        <div class="lbl">Today</div>
        <div class="sub">{{ $pct }}% of limit</div>
    </div>
    <div class="stat">
        <div class="val">{{ $userLimit }}</div>
        <div class="lbl">Daily Limit</div>
        <div class="sub {{ $userLimit !== $saveLimit ? 'custom-limit-badge' : '' }}">
            {{ $userLimit !== $saveLimit ? 'custom' : 'org default' }}
        </div>
    </div>
</div>

<div class="card">
    <div class="employee-header">
        <div class="employee-header-info">
            <div class="employee-avatar employee-header-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <div class="employee-header-name">{{ $user->name }}</div>
                <div class="employee-header-email">{{ $user->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('owner.employees.limit', $user->id) }}" class="limit-form">
            @csrf @method('PATCH')
            <span class="limit-label">Daily limit</span>
            <input type="number" name="save_limit" class="limit-input"
                   value="{{ $user->save_limit ?? '' }}"
                   placeholder="{{ $saveLimit }}"
                   min="1" max="500">
            <button type="submit" class="btn btn-ghost btn-sm">Save</button>
            @if($user->save_limit)
            <button type="submit" name="save_limit" value="" class="btn btn-ghost btn-sm btn-reset">Reset</button>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:16px">Saved Listings</div>

    @if($saves->isEmpty())
        <div class="empty">No listings saved yet.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>Listing</th>
                <th>Contact</th>
                <th>Price</th>
                <th>Saved</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($saves as $s)
        @php
            $l       = $s->listing_snapshot;
            $orig    = (float) ($l['price'] ?? 0);
            $mine    = (float) ($s->my_price ?? 0);
            $disc    = $s->discountPercent();
            $isToday = $s->saved_date === now()->toDateString();
        @endphp
        <tr>
            <td>
                <div class="listing-title">{{ $l['title'] ?? '—' }}</div>
                <div class="listing-address">{{ $l['address'] ?? '' }}</div>
                <div class="listing-chips">
                    @if(!empty($l['rooms']))    <span class="listing-chip">{{ $l['rooms'] }} rooms</span> @endif
                    @if(!empty($l['area']))     <span class="listing-chip">{{ $l['area'] }}</span> @endif
                    @if(!empty($l['district'])) <span class="listing-chip">{{ $l['district'] }}</span> @endif
                </div>
                <a href="{{ $l['url'] ?? '#' }}" target="_blank" class="listing-url">myhome.ge →</a>
            </td>
            <td>
                @if(!empty($l['owner_name'])) <div class="listing-owner">{{ $l['owner_name'] }}</div> @endif
                @if(!empty($l['phone']))      <div class="listing-phone">📞 {{ $l['phone'] }}</div> @endif
            </td>
            <td class="listing-when">
                @if($mine)
                    <div class="price-my">${{ number_format($mine, 0) }}</div>
                    <div class="price-orig">${{ number_format($orig, 0) }}</div>
                    @if($disc) <div class="discount">-{{ $disc }}%</div> @endif
                @elseif($orig)
                    <div class="price-orig">${{ number_format($orig, 0) }}</div>
                @else
                    <span style="color:var(--muted)">—</span>
                @endif
            </td>
            <td class="listing-when">
                <span class="date-badge {{ $isToday ? 'today' : '' }}">
                    {{ $isToday ? 'Today' : $s->saved_date }}
                </span>
                <div style="margin-top:4px">{{ $s->created_at->diffForHumans() }}</div>
            </td>
            <td>
                <form id="del-save-{{ $s->id }}" method="POST" action="{{ route('owner.saves.destroy', $s->id) }}">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="delete-save-btn" title="Delete"
                        onclick="confirmAction({ heading: 'Delete listing?', body: '{{ addslashes($l['title'] ?? 'This listing') }} will be permanently removed.', confirmText: 'Delete', onConfirm: () => document.getElementById('del-save-{{ $s->id }}').submit() })">✕</button>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
