<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    private const DAYS_CUTOFF = 5;

    public function export(Request $request)
    {
        $cutoff = Carbon::now()->subDays(self::DAYS_CUTOFF);

        $q = Listing::where('listed_at', '>=', $cutoff);

        if ($request->filled('rent_type') && $request->rent_type !== 'all') {
            $q->where('rent_type', $request->rent_type);
        }
        if ($request->filled('price_min')) {
            $q->where('price', '>=', (int) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $q->where('price', '<=', (int) $request->price_max);
        }
        if ($request->filled('rooms')) {
            $q->whereIn('rooms', array_filter(explode(',', $request->rooms)));
        }
        if ($request->filled('bedrooms')) {
            $q->whereIn('bedrooms', array_map('intval', array_filter(explode(',', $request->bedrooms))));
        }
        if ($request->boolean('newly_built')) {
            $q->where('newly_built', true);
        }
        if ($request->filled('poster_type') && $request->poster_type !== 'all') {
            $q->where('poster_type', $request->poster_type);
        }

        $listings = $q->orderByDesc('listed_at')->get();

        $filename = 'listings-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($listings) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Cyrillic/Georgian correctly
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['ID', 'Title', 'Address', 'Owner', 'Phone', 'Price (USD)', 'My Price', 'Rent Type', 'Rooms', 'Bedrooms', 'Area', 'District', 'Listed', 'URL']);

            foreach ($listings as $l) {
                fputcsv($out, [
                    $l->listing_id,
                    $l->title,
                    $l->address,
                    $l->owner_name,
                    $l->phone,
                    $l->price,
                    '', // My Price — filled in by the user
                    $l->rent_type,
                    $l->rooms,
                    $l->bedrooms,
                    $l->area,
                    $l->district_name,
                    $l->listed_at?->format('Y-m-d H:i'),
                    $l->url,
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
