<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    private const DAYS_CUTOFF = 5;

    public function stream(Request $request)
    {
        $filters = [
            'rent_type'   => $request->input('rent_type', 'all'),
            'price_min'   => $request->filled('price_min') ? (int) $request->price_min : null,
            'price_max'   => $request->filled('price_max') ? (int) $request->price_max : null,
            'rooms'       => $request->filled('rooms')
                ? array_filter(explode(',', $request->rooms)) : [],
            'bedrooms'    => $request->filled('bedrooms')
                ? array_map('intval', array_filter(explode(',', $request->bedrooms))) : [],
            'newly_built' => $request->boolean('newly_built'),
            'poster_type' => $request->input('poster_type', 'all'),
        ];

        $cutoff   = Carbon::now()->subDays(self::DAYS_CUTOFF);
        $listings = $this->queryDb($filters, $cutoff);
        $formatted = $listings->map(fn ($l) => $this->formatListing($l))->values();

        return response()->json([
            'listings' => $formatted,
            'total'    => $formatted->count(),
        ]);
    }

    private function queryDb(array $filters, Carbon $cutoff): \Illuminate\Database\Eloquent\Collection
    {
        $q = Listing::where('listed_at', '>=', $cutoff);

        if ($filters['rent_type'] !== 'all') {
            $q->where('rent_type', $filters['rent_type']);
        }
        if ($filters['price_min']) {
            $q->where('price', '>=', $filters['price_min']);
        }
        if ($filters['price_max']) {
            $q->where('price', '<=', $filters['price_max']);
        }
        if (! empty($filters['rooms'])) {
            $q->whereIn('rooms', $filters['rooms']);
        }
        if (! empty($filters['bedrooms'])) {
            $q->whereIn('bedrooms', $filters['bedrooms']);
        }
        if ($filters['newly_built']) {
            $q->where('newly_built', true);
        }
        if ($filters['poster_type'] !== 'all') {
            $q->where('poster_type', $filters['poster_type']);
        }

        return $q->orderByDesc('listed_at')->get();
    }

    private function formatListing(Listing $listing): array
    {
        return [
            'id'          => $listing->listing_id,
            'title'       => $listing->title,
            'price'       => $listing->price,
            'rent_type'   => $listing->rent_type,
            'period'      => $listing->rent_type === 'daily' ? '/day' : '/month',
            'lat'         => $listing->lat,
            'lng'         => $listing->lng,
            'address'     => $listing->address,
            'area'        => $listing->area,
            'rooms'       => $listing->rooms,
            'district'    => $listing->district_name,
            'poster_type' => $listing->poster_type,
            'updated_at'  => $listing->listed_at?->diffForHumans(),
            'url'         => $listing->url,
        ];
    }
}
