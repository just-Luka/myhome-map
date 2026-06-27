<?php

namespace App\Http\Controllers;

use App\Models\SavedListing;
use Illuminate\Http\Request;

class SavedListingController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'listing_id'       => 'required|string',
            'listing_snapshot' => 'required|array',
            'my_price'         => 'nullable|numeric|min:0',
        ]);

        $user     = auth()->user();
        $existing = SavedListing::where('user_id', $user->id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        SavedListing::create([
            'user_id'          => $user->id,
            'organization_id'  => $user->organization_id,
            'listing_id'       => $request->listing_id,
            'listing_snapshot' => $request->listing_snapshot,
            'my_price'         => $request->my_price ?: null,
            'saved_date'       => now()->toDateString(),
        ]);

        return response()->json(['saved' => true]);
    }

    // Today's saves only — drives the daily progress counter
    public function mySaves()
    {
        $user  = auth()->user();
        $today = now()->toDateString();
        $limit = $user->organization?->save_limit ?? 20;

        $saves = SavedListing::where('user_id', $user->id)
            ->where('saved_date', $today)
            ->get(['listing_id', 'my_price', 'listing_snapshot', 'note', 'link_myhome', 'link_ss'])
            ->keyBy('listing_id')
            ->map(fn ($s) => [
                'my_price'    => $s->my_price,
                'snapshot'    => $s->listing_snapshot,
                'note'        => $s->note,
                'link_myhome' => $s->link_myhome,
                'link_ss'     => $s->link_ss,
            ]);

        return response()->json(['saves' => $saves, 'limit' => $limit]);
    }

    // All-time saves — the general archive list
    public function allSaves()
    {
        $today = now()->toDateString();

        $saves = SavedListing::where('user_id', auth()->id())
            ->where('saved_date', '!=', $today)
            ->orderByDesc('saved_date')
            ->get(['listing_id', 'my_price', 'listing_snapshot', 'note', 'link_myhome', 'link_ss', 'saved_date'])
            ->map(fn ($s) => [
                'listing_id'  => $s->listing_id,
                'my_price'    => $s->my_price,
                'snapshot'    => $s->listing_snapshot,
                'note'        => $s->note,
                'link_myhome' => $s->link_myhome,
                'link_ss'     => $s->link_ss,
                'saved_date'  => $s->saved_date,
            ]);

        return response()->json($saves);
    }

    public function updateEntry(Request $request)
    {
        $request->validate([
            'listing_id'  => 'required|string',
            'my_price'    => 'nullable|numeric|min:0',
            'note'        => 'nullable|string|max:1000',
            'link_myhome' => 'nullable|url|max:500',
            'link_ss'     => 'nullable|url|max:500',
        ]);

        SavedListing::where('user_id', auth()->id())
            ->where('listing_id', $request->listing_id)
            ->update([
                'my_price'    => $request->my_price    ?: null,
                'note'        => $request->note         ?: null,
                'link_myhome' => $request->link_myhome ?: null,
                'link_ss'     => $request->link_ss     ?: null,
            ]);

        return response()->json(['ok' => true]);
    }

    public function updateLinks(Request $request)
    {
        $request->validate([
            'listing_id'  => 'required|string',
            'link_myhome' => 'nullable|url|max:500',
            'link_ss'     => 'nullable|url|max:500',
        ]);

        SavedListing::where('user_id', auth()->id())
            ->where('listing_id', $request->listing_id)
            ->update([
                'link_myhome' => $request->link_myhome ?: null,
                'link_ss'     => $request->link_ss     ?: null,
            ]);

        return response()->json(['ok' => true]);
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|string',
            'note'       => 'nullable|string|max:1000',
        ]);

        SavedListing::where('user_id', auth()->id())
            ->where('listing_id', $request->listing_id)
            ->update(['note' => $request->note ?: null]);

        return response()->json(['ok' => true]);
    }

    public function teamSaves()
    {
        $user = auth()->user();

        if (! $user->inOrg()) {
            return response()->json([]);
        }

        $org = $user->organization;

        if (! $org->show_team_saves) {
            return response()->json([]);
        }

        $showPrices = $org->show_team_prices;

        $saves = SavedListing::with('user:id,name')
            ->where('organization_id', $org->id)
            ->where('user_id', '!=', $user->id)
            ->get(['listing_id', 'user_id', 'my_price'])
            ->groupBy('listing_id')
            ->map(fn ($rows) => $rows->map(fn ($r) => [
                'name'  => $r->user?->name,
                'price' => $showPrices ? $r->my_price : null,
            ])->filter(fn ($r) => $r['name'])->values());

        return response()->json($saves);
    }
}
