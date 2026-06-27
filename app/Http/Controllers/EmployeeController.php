<?php

namespace App\Http\Controllers;

use App\Models\SavedListing;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $org       = auth()->user()->organization;
        $saveLimit = $org->save_limit ?? 20;
        $today     = now()->toDateString();
        $members   = $org->users()->get();

        $employees = $org->users()
            ->where('role', 'employee')
            ->orderBy('name')
            ->withCount('savedListings')
            ->get()
            ->map(function ($emp) use ($today, $saveLimit) {
                $todayCount = SavedListing::where('user_id', $emp->id)->where('saved_date', $today)->count();
                $weekCount  = SavedListing::where('user_id', $emp->id)
                    ->whereBetween('created_at', [now()->startOfWeek(), now()])
                    ->count();
                $lastSave   = SavedListing::where('user_id', $emp->id)->latest()->value('created_at');
                $limit      = $emp->save_limit ?? $saveLimit;

                return [
                    'user'      => $emp,
                    'total'     => $emp->saved_listings_count,
                    'today'     => $todayCount,
                    'this_week' => $weekCount,
                    'last_save' => $lastSave,
                    'limit'     => $limit,
                ];
            });

        return view('employee.index', compact('org', 'members', 'employees', 'saveLimit'));
    }

    public function show(User $user)
    {
        $org = auth()->user()->organization;
        abort_if($user->organization_id !== $org->id, 403);

        $saveLimit  = $org->save_limit ?? 20;
        $today      = now()->toDateString();
        $members    = $org->users()->get();
        $saves      = SavedListing::where('user_id', $user->id)->latest()->get();
        $todayCount = $saves->where('saved_date', $today)->count();
        $weekCount  = $saves->filter(fn($s) => $s->created_at->isCurrentWeek())->count();
        $userLimit  = $user->save_limit ?? $saveLimit;

        return view('employee.show', compact(
            'org', 'members', 'user', 'saves', 'todayCount', 'weekCount', 'saveLimit', 'userLimit'
        ));
    }

    public function destroy(User $user)
    {
        $org = auth()->user()->organization;
        abort_if($user->organization_id !== $org->id, 403);
        abort_if($user->isCeo(), 403);

        $user->update(['organization_id' => null]);

        return redirect()->route('owner.employees')->with('success', "{$user->name} has been removed from the team.");
    }

    public function updateLimit(Request $request, User $user)
    {
        $org = auth()->user()->organization;
        abort_if($user->organization_id !== $org->id, 403);

        $request->validate(['save_limit' => 'nullable|integer|min:1|max:500']);

        $user->update(['save_limit' => $request->filled('save_limit') ? (int) $request->save_limit : null]);

        return back()->with('success', 'Save limit updated.');
    }

    public function export(User $user)
    {
        $org = auth()->user()->organization;
        abort_if($user->organization_id !== $org->id, 403);

        $saves    = SavedListing::where('user_id', $user->id)->latest()->get();
        $filename = 'listings-' . str($user->name)->slug() . '-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($saves) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Saved At', 'ID', 'Title', 'Address', 'Owner', 'Phone', 'Original Price', 'My Price', 'Discount %', 'Rooms', 'Area', 'District', 'URL', 'Agent Post ID (myhome.ge)', 'Agent Post Link (myhome.ge)', 'Agent Post ID (ss.ge)', 'Agent Post Link (ss.ge)', 'Comment']);

            foreach ($saves as $s) {
                $l    = $s->listing_snapshot;
                $orig = (float) ($l['price'] ?? 0);
                $mine = (float) ($s->my_price ?? 0);
                $disc = ($mine && $orig) ? round((($orig - $mine) / $orig) * 100, 1) . '%' : '';

                fputcsv($out, [
                    $s->created_at->format('Y-m-d H:i'),
                    $s->listing_id,
                    $l['title'] ?? '',
                    $l['address'] ?? '',
                    $l['owner_name'] ?? '',
                    $l['phone'] ?? '',
                    $orig ?: '',
                    $mine ?: '',
                    $disc,
                    $l['rooms'] ?? '',
                    $l['area'] ?? '',
                    $l['district'] ?? '',
                    $l['url'] ?? '',
                    $s->myhomePostId() ?? '',
                    $s->link_myhome ?? '',
                    $s->ssPostId() ?? '',
                    $s->link_ss ?? '',
                    $s->note ?? '',
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    public function destroySave(SavedListing $save)
    {
        $org = auth()->user()->organization;
        abort_if($save->organization_id !== $org->id, 403);

        $save->delete();

        return back()->with('success', 'Listing removed.');
    }
}
