@extends('admin.layout')
@section('title', 'Activity')

@section('content')

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <div class="card-title" style="margin:0">All Saved Listings</div>
        <a href="{{ route('admin.activity.export') }}" class="btn btn-green btn-sm">⬇ Export CSV</a>
    </div>

    @if($saves->isEmpty())
        <div class="empty">No activity yet.</div>
    @else
    <table>
        <thead>
            <tr><th>Employee</th><th>Organization</th><th>Listing</th><th>Owner / Phone</th><th>Price</th><th>Saved</th></tr>
        </thead>
        <tbody>
        @foreach($saves as $s)
        @php $l = $s->listing_snapshot; @endphp
        <tr>
            <td>
                <div style="font-weight:600">{{ $s->user->name }}</div>
                <div style="font-size:11px;color:#4d5780">{{ $s->user->email }}</div>
            </td>
            <td>
                @if($s->organization)
                    <span class="badge badge-org">{{ $s->organization->name }}</span>
                @else
                    <span style="color:#4d5780;font-size:12px">—</span>
                @endif
            </td>
            <td>
                <div style="font-weight:600;font-size:13px">{{ Str::limit($l['title'] ?? '—', 35) }}</div>
                <div style="font-size:11px;color:#4d5780">{{ $l['address'] ?? '' }}</div>
                <a href="{{ $l['url'] ?? '#' }}" target="_blank" style="font-size:11px;color:#4f6ef7;text-decoration:none">myhome.ge →</a>
            </td>
            <td>
                <div style="font-size:13px">{{ $l['owner_name'] ?? '—' }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ $l['phone'] ?? '' }}</div>
            </td>
            <td>
                @if($s->my_price)
                    <div style="color:#e94560;font-weight:600">${{ number_format($s->my_price, 0) }}</div>
                    <div style="font-size:11px;color:#4d5780">orig: ${{ number_format($l['price'] ?? 0, 0) }}</div>
                @else
                    <span style="color:#4d5780">${{ number_format($l['price'] ?? 0, 0) }}</span>
                @endif
            </td>
            <td style="color:#4d5780;font-size:12px;white-space:nowrap">{{ $s->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pagination">{{ $saves->links('pagination::simple-default') }}</div>
    @endif
</div>

@endsection
