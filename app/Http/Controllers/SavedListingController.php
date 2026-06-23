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

        $user    = auth()->user();
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
        ]);

        return response()->json(['saved' => true]);
    }

    public function mySaves()
    {
        $saves = SavedListing::where('user_id', auth()->id())
            ->pluck('my_price', 'listing_id');

        return response()->json($saves);
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

        $saves = SavedListing::with('user:id,name')
            ->where('organization_id', $org->id)
            ->where('user_id', '!=', $user->id)
            ->get(['listing_id', 'user_id'])
            ->keyBy('listing_id')
            ->map(fn ($s) => $s->user->name);

        return response()->json($saves);
    }
}
