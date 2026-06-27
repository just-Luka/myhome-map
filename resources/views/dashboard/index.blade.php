@extends('dashboard.layout')

@section('title', 'Team Dashboard')

@section('topbar-actions')
<form method="POST" action="{{ route('owner.invite') }}">
    @csrf
    <button class="btn btn-primary btn-sm" type="submit">+ <span data-i18n="invite_employee">Invite Employee</span></button>
</form>
@endsection

@section('content')

@if(session('employee_link'))
    <div class="alert alert-success">
        ✓ <span data-i18n="invite_ready">Employee invite link (expires in 7 days):</span><br><br>
        <strong id="invite-url">{{ session('employee_link') }}</strong>
        <button class="copy-btn" style="margin-left:10px" onclick="copyInviteLink(document.getElementById('invite-url').textContent)"><span data-i18n="copy">Copy</span></button>
    </div>
@endif
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif

{{-- Stats --}}
<div class="stats">
    <div class="stat">
        <div class="val">{{ $allSaves->count() }}</div>
        <div class="lbl" data-i18n="total_saves">Total Saves</div>
    </div>
    <div class="stat">
        <div class="val">{{ $weekSaves }}</div>
        <div class="lbl" data-i18n="this_week">This Week</div>
        <div class="sub">{{ $todaySaves }} <span data-i18n="today">today</span></div>
    </div>
    <div class="stat">
        <div class="val">{{ $members->count() }}</div>
        <div class="lbl" data-i18n="team_members">Team Members</div>
        <div class="sub">{{ $org->user_limit - $members->count() }} <span data-i18n="seats_left">seats left</span></div>
    </div>
    <div class="stat">
        <div class="val">{{ $avgDiscount !== null ? $avgDiscount . '%' : '—' }}</div>
        <div class="lbl" data-i18n="avg_discount">Avg Discount</div>
        <div class="sub" data-i18n="from_orig">from original price</div>
    </div>
</div>

<div class="grid-3-1">

    {{-- Left column --}}
    <div>
        <div class="card">
            <div class="card-head">
                <div class="card-title" data-i18n="saved_listings">Saved Listings</div>
                <div class="filter-row">
                    <form method="GET" style="display:flex;gap:8px;align-items:center">
                        <select name="employee" onchange="this.form.submit()">
                            <option value="" data-i18n="all_employees">All employees</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" {{ request('employee') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ request('employee') ? route('owner.export', ['employee' => request('employee')]) : route('owner.export') }}" class="btn btn-green btn-sm">⬇ Export</a>
                </div>
            </div>

            @if($saves->isEmpty())
                <div class="empty" data-i18n="no_listings">No listings saved yet.</div>
            @else
            <table>
                <thead>
                    <tr>
                        <th data-i18n="th_employee">Employee</th>
                        <th data-i18n="th_listing">Listing</th>
                        <th data-i18n="th_contact">Contact</th>
                        <th data-i18n="th_price">Price</th>
                        <th data-i18n="th_when">When</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($saves as $s)
                @php
                    $l    = $s->listing_snapshot;
                    $orig = (float) ($l['price'] ?? 0);
                    $mine = (float) ($s->my_price ?? 0);
                    $disc = $s->discountPercent();
                @endphp
                <tr>
                    <td><span class="badge badge-{{ $s->user->role }}">{{ $s->user->name }}</span></td>
                    <td>
                        <div class="listing-title">{{ $l['title'] ?? '—' }}</div>
                        <div class="listing-address">{{ $l['address'] ?? '' }}</div>
                        <div class="listing-chips">
                            @if(!empty($l['rooms']))    <span class="listing-chip">{{ $l['rooms'] }} <span data-i18n="rooms">rooms</span></span> @endif
                            @if(!empty($l['area']))     <span class="listing-chip">{{ $l['area'] }}</span> @endif
                            @if(!empty($l['district'])) <span class="listing-chip">{{ $l['district'] }}</span> @endif
                        </div>
                        <a href="{{ $l['url'] ?? '#' }}" target="_blank" class="listing-url">myhome.ge →</a>
                    </td>
                    <td>
                        @if(!empty($l['owner_name'])) <div class="listing-owner">{{ $l['owner_name'] }}</div> @endif
                        @if(!empty($l['phone']))      <div class="listing-phone">📞 {{ $l['phone'] }}</div> @endif
                    </td>
                    <td style="white-space:nowrap">
                        @if($mine)
                            <div class="price-my">${{ number_format($mine, 0) }}</div>
                            <div class="price-orig">${{ number_format($orig, 0) }}</div>
                            @if($disc) <div class="discount">-{{ $disc }}%</div> @endif
                        @else
                            <div class="listing-price-original">${{ number_format($orig, 0) }}</div>
                        @endif
                    </td>
                    <td class="listing-when">{{ $s->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- Right column --}}
    <div>

        <div class="card">
            <div class="card-title" style="margin-bottom:14px" data-i18n="team_perf">Team Performance</div>
            @if($employeeStats->isEmpty())
                <div class="empty" data-i18n="no_members">No members yet.</div>
            @else
            @foreach($employeeStats as $w)
            <div class="employee-row">
                <div class="employee-avatar">{{ strtoupper(substr($w['name'], 0, 1)) }}</div>
                <div class="employee-info">
                    <div class="employee-name">{{ $w['name'] }}</div>
                    <div class="employee-meta">
                        <span class="badge badge-{{ $w['role'] }}" style="font-size:10px;padding:1px 6px">{{ $w['role'] }}</span>
                        @if($w['last_save'])
                            <span style="margin-left:6px"><span data-i18n="last_save">Last:</span> {{ $w['last_save']->diffForHumans() }}</span>
                        @else
                            <span style="margin-left:6px" data-i18n="no_saves_yet">No saves yet</span>
                        @endif
                    </div>
                </div>
                <div class="employee-count">{{ $w['count'] }}</div>
            </div>
            @endforeach
            @endif
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-title" data-i18n="pending_invites">Pending Invites</div>
                <form method="POST" action="{{ route('owner.invite') }}">
                    @csrf
                    <button class="btn btn-ghost btn-sm" type="submit">+ <span data-i18n="new">New</span></button>
                </form>
            </div>
            @if($pendingInvites->isEmpty())
                <div class="empty" style="padding:16px" data-i18n="no_invites">No pending invites.</div>
            @else
            @foreach($pendingInvites as $inv)
            @php $inviteUrl = route('invite.show', $inv->token); @endphp
            <div class="invite-row">
                <div class="invite-token">{{ $inviteUrl }}</div>
                <div class="invite-exp">{{ $inv->expires_at?->diffForHumans() ?? '∞' }}</div>
                <button class="copy-btn" onclick="copyInviteLink('{{ $inviteUrl }}')"><span data-i18n="copy">Copy</span></button>
            </div>
            @endforeach
            @endif
        </div>

        <div class="card">
            <div class="card-title" style="margin-bottom:14px" data-i18n="org_logo">Organization Logo</div>
            @if($org->logo)
            <div style="margin-bottom:14px;text-align:center">
                <img src="{{ Storage::url($org->logo) }}" alt="Logo"
                     style="max-height:60px;max-width:160px;object-fit:contain;border-radius:6px">
            </div>
            @endif
            <form method="POST" action="{{ route('owner.logo') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="logo" accept="image/*" required class="file-input">
                @error('logo') <p class="field-error">{{ $message }}</p> @enderror
                <button class="btn btn-primary" style="width:100%" type="submit" data-i18n="upload_logo">Upload Logo</button>
            </form>
            @if($org->logo)
            <form method="POST" action="{{ route('owner.logo.remove') }}" style="margin-top:8px">
                @csrf @method('DELETE')
                <button class="btn btn-ghost" style="width:100%;font-size:12px" type="submit" data-i18n="remove_logo">Remove Logo</button>
            </form>
            @endif
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
Object.assign(translations.en, {
    invite_ready: 'Employee invite link (expires in 7 days):',
    dashboard: 'Dashboard', team_dashboard: 'Team Dashboard',
    invite_employee: 'Invite Employee',
    total_saves: 'Total Saves', this_week: 'This Week', today: 'today',
    team_members: 'Team Members', seats_left: 'seats left',
    avg_discount: 'Avg Discount', from_orig: 'from original price',
    saved_listings: 'Saved Listings', all_employees: 'All employees',
    no_listings: 'No listings saved yet.',
    th_employee: 'Employee', th_listing: 'Listing', th_contact: 'Contact',
    th_price: 'Price', th_when: 'When', rooms: 'rooms',
    team_perf: 'Team Performance', no_members: 'No members yet.',
    last_save: 'Last:', no_saves_yet: 'No saves yet',
    pending_invites: 'Pending Invites', new: 'New', no_invites: 'No pending invites.',
    copy: 'Copy', org_logo: 'Organization Logo',
    upload_logo: 'Upload Logo', remove_logo: 'Remove Logo',
});
Object.assign(translations.ka, {
    invite_ready: 'თანამშრომლის მოწვევის ლინკი (7 დღე მოქმედებს):',
    dashboard: 'პანელი', team_dashboard: 'გუნდის პანელი',
    invite_employee: 'თანამშრომლის მოწვევა',
    total_saves: 'სულ შენახული', this_week: 'ამ კვირაში', today: 'დღეს',
    team_members: 'გუნდის წევრები', seats_left: 'ადგილი დარჩა',
    avg_discount: 'საშ. ფასდაკლება', from_orig: 'საწყისი ფასიდან',
    saved_listings: 'შენახული განცხადებები', all_employees: 'ყველა თანამშრომელი',
    no_listings: 'განცხადებები ჯერ არ არის შენახული.',
    th_employee: 'თანამშრომელი', th_listing: 'განცხადება', th_contact: 'კონტაქტი',
    th_price: 'ფასი', th_when: 'როდის', rooms: 'ოთახი',
    team_perf: 'გუნდის შედეგები', no_members: 'წევრები ჯერ არ არიან.',
    last_save: 'ბოლო:', no_saves_yet: 'ჯერ არ შენახულა',
    pending_invites: 'მოლოდინის მოწვევები', new: 'ახალი', no_invites: 'მოლოდინის მოწვევები არ არის.',
    copy: 'კოპირება', org_logo: 'ორგანიზაციის ლოგო',
    upload_logo: 'ლოგოს ატვირთვა', remove_logo: 'ლოგოს წაშლა',
});

function copyInviteLink(baseUrl) {
    const lang = localStorage.getItem('dashboard_lang') || 'en';
    navigator.clipboard.writeText(lang !== 'en' ? baseUrl + '?lang=' + lang : baseUrl);
}
</script>
@endsection
