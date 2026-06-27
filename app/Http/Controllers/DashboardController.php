<?php

namespace App\Http\Controllers;

use App\Models\OrganizationInvite;
use App\Models\SavedListing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $org  = $user->organization;

        $savesQuery = SavedListing::with('user')
            ->where('organization_id', $org->id);

        if ($request->filled('employee')) {
            $savesQuery->where('user_id', $request->employee);
        }

        $saves   = $savesQuery->latest()->get();
        $members = $org->users()->withCount('savedListings')->orderBy('name')->get();

        // Stats
        $allSaves    = SavedListing::where('organization_id', $org->id)->get();
        $todaySaves  = $allSaves->filter(fn($s) => $s->created_at->isToday())->count();
        $weekSaves   = $allSaves->filter(fn($s) => $s->created_at->isCurrentWeek())->count();

        $savesWithPrice = $allSaves->filter(fn($s) => $s->my_price && ($s->listing_snapshot['price'] ?? 0) > 0);
        $avgDiscount = $savesWithPrice->count()
            ? round($savesWithPrice->avg(fn($s) => (($s->listing_snapshot['price'] - $s->my_price) / $s->listing_snapshot['price']) * 100), 1)
            : null;

        // Employee breakdown
        $employeeStats = $members->map(function ($m) use ($allSaves) {
            $mSaves    = $allSaves->where('user_id', $m->id);
            $lastSave  = $mSaves->sortByDesc('created_at')->first();
            return [
                'id'        => $m->id,
                'name'      => $m->name,
                'role'      => $m->role,
                'count'     => $mSaves->count(),
                'last_save' => $lastSave?->created_at,
            ];
        })->sortByDesc('count');

        // Pending invites
        $pendingInvites = OrganizationInvite::where('organization_id', $org->id)
            ->whereNull('used_at')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest()
            ->get();

        return view('dashboard.index', compact(
            'org', 'saves', 'members', 'employeeStats', 'pendingInvites',
            'todaySaves', 'weekSaves', 'allSaves', 'avgDiscount'
        ));
    }

    public function settings()
    {
        $org     = auth()->user()->organization;
        $members = $org->users()->get();
        return view('dashboard.settings', compact('org', 'members'));
    }

    public function updateSettings(Request $request)
    {
        $data = [
            'show_team_saves'  => $request->boolean('show_team_saves'),
            'show_team_prices' => $request->boolean('show_team_prices'),
        ];
        if ($request->filled('save_limit')) {
            $data['save_limit'] = max(1, min(200, (int) $request->input('save_limit')));
        }
        auth()->user()->organization->update($data);

        return back()->with('success', 'Settings saved.');
    }

    public function generateInvite()
    {
        $org = auth()->user()->organization;

        if (! $org->canAddMember()) {
            return back()->with('error', "User limit of {$org->user_limit} reached.");
        }

        $invite = OrganizationInvite::create([
            'organization_id' => $org->id,
            'token'           => Str::random(48),
            'role'            => 'employee',
            'expires_at'      => now()->addDays(7),
        ]);

        return back()->with('employee_link', route('invite.show', $invite->token));
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $org = auth()->user()->organization;

        // Delete old logo
        if ($org->logo && \Storage::disk('public')->exists($org->logo)) {
            \Storage::disk('public')->delete($org->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $org->update(['logo' => $path]);

        return back()->with('success', 'Logo updated.');
    }

    public function removeLogo()
    {
        $org = auth()->user()->organization;

        if ($org->logo && \Storage::disk('public')->exists($org->logo)) {
            \Storage::disk('public')->delete($org->logo);
        }

        $org->update(['logo' => null]);

        return back()->with('success', 'Logo removed.');
    }

    public function export(Request $request)
    {
        $org   = auth()->user()->organization;
        $query = SavedListing::with('user')->where('organization_id', $org->id);

        if ($request->filled('employee')) {
            $query->where('user_id', $request->employee);
        }

        $saves    = $query->latest()->get();
        $suffix   = $request->filled('employee') ? '-employee' : '-team';
        $filename = 'listings' . $suffix . '-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($saves) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Saved By', 'Saved At', 'ID', 'Title', 'Address', 'Owner', 'Phone', 'Original Price', 'My Price', 'Discount %', 'Rent Type', 'Rooms', 'Area', 'District', 'URL']);

            foreach ($saves as $s) {
                $l        = $s->listing_snapshot;
                $orig     = (float) ($l['price'] ?? 0);
                $mine     = (float) ($s->my_price ?? 0);
                $discount = ($mine && $orig) ? round((($orig - $mine) / $orig) * 100, 1) . '%' : '';

                fputcsv($out, [
                    $s->user->name,
                    $s->created_at->format('Y-m-d H:i'),
                    $s->listing_id,
                    $l['title'] ?? '',
                    $l['address'] ?? '',
                    $l['owner_name'] ?? '',
                    $l['phone'] ?? '',
                    $orig ?: '',
                    $mine ?: '',
                    $discount,
                    $l['rent_type'] ?? '',
                    $l['rooms'] ?? '',
                    $l['area'] ?? '',
                    $l['district'] ?? '',
                    $l['url'] ?? '',
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store',
        ]);
    }
}
