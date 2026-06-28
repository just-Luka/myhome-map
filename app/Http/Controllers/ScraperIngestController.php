<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ScraperIngestController extends Controller
{
    public function checkpoint()
    {
        $latest = Listing::max('listed_at');
        return response()->json(['latest_listed_at' => $latest]);
    }

    public function ingest(Request $request)
    {
        $request->validate([
            'listings'                => 'required|array|min:1|max:500',
            'listings.*.listing_id'   => 'required|string',
            'listings.*.lat'          => 'required|numeric',
            'listings.*.lng'          => 'required|numeric',
            'listings.*.listed_at'    => 'required|date',
        ]);

        $saved   = 0;
        $updated = 0;

        foreach ($request->listings as $data) {
            $existing = Listing::where('listing_id', $data['listing_id'])->first();

            $fields = [
                'title'         => $data['title']         ?? null,
                'price'         => isset($data['price']) ? (float) $data['price'] : null,
                'currency'      => $data['currency']      ?? 'USD',
                'lat'           => (float) $data['lat'],
                'lng'           => (float) $data['lng'],
                'geocoded'      => (bool) ($data['geocoded'] ?? false),
                'address'       => $data['address']       ?? null,
                'area'          => $data['area']          ?? null,
                'rooms'         => $data['rooms']         ?? null,
                'bedrooms'      => isset($data['bedrooms']) ? (int) $data['bedrooms'] : null,
                'rent_type'     => $data['rent_type']     ?? 'monthly',
                'district_id'   => $data['district_id']   ?? null,
                'district_name' => $data['district_name'] ?? null,
                'newly_built'   => (bool) ($data['newly_built'] ?? false),
                'poster_type'   => $data['poster_type']   ?? 'owner',
                'owner_name'    => $data['owner_name']    ?? null,
                'phone'         => $data['phone']         ?? null,
                'listed_at'     => $data['listed_at'],
                'url'           => $data['url']           ?? null,
            ];

            if ($existing) {
                $existing->update($fields);
                $updated++;
            } else {
                Listing::create(array_merge($fields, ['listing_id' => $data['listing_id']]));
                $saved++;
            }
        }

        return response()->json(['saved' => $saved, 'updated' => $updated]);
    }
}
