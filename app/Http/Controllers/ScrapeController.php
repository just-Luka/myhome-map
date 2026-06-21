<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ScrapeController extends Controller
{
    public function run(Request $request)
    {
        set_time_limit(300);

        Artisan::call('scrape:myhome', [
            '--pages' => 25,
            '--delay' => 300,
        ]);

        // Return filtered listings after scraping
        $query = Listing::whereNotNull('lat')->whereNotNull('lng');

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }
        if ($request->filled('districts')) {
            $districts = array_filter(explode(',', $request->districts));
            if (count($districts)) {
                $query->whereIn('district_id', $districts);
            }
        }
        if ($request->filled('poster_type') && $request->poster_type !== 'all') {
            $query->where('poster_type', $request->poster_type);
        }
        if ($request->filled('rent_type') && $request->rent_type !== 'all') {
            $query->where('rent_type', $request->rent_type);
        }

        $listings = $query
            ->orderByDesc('listed_at')
            ->get(['listing_id', 'title', 'price', 'currency', 'rent_type', 'lat', 'lng',
                   'address', 'area', 'rooms', 'district_name', 'poster_type', 'listed_at', 'url']);

        return response()->json([
            'scraped' => Artisan::output(),
            'listings' => $listings,
        ]);
    }
}
