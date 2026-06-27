<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ScrapeMyHome extends Command
{
    protected $signature   = 'scrape:myhome {--pages=30} {--delay=400} {--detail-delay=700} {--debug-listing=}';
    protected $description = 'Scrape recent Tbilisi rent listings from myhome.ge and save with exact coordinates';

    private const HEADERS = [
        'User-Agent'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
        'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.9',
        'Referer'         => 'https://www.myhome.ge/',
    ];

    private const DAYS_CUTOFF = 5;

    private const DISTRICT_COORDS = [
        'Gldani-Nadzaladevi'   => [41.7737, 44.8088],
        'Mtatsminda-Krtsanisi' => [41.6831, 44.8007],
        'Didube-Chugureti'     => [41.7259, 44.8014],
        'Vake-Saburtalo'       => [41.7121, 44.7597],
        'Isani-Samgori'        => [41.6808, 44.8836],
    ];

    // deal_types: 2 = monthly rent, 7 = daily rent
    private const DEAL_TYPES = ['2', '7'];

    private int $saved   = 0;
    private int $updated = 0;
    private int $skipped = 0;

    public function handle(): void
    {
        ini_set('memory_limit', '512M');

        $maxPages     = (int) $this->option('pages');
        $delayMs      = (int) $this->option('delay');
        $detailDelayMs = (int) $this->option('detail-delay');
        $cutoff    = Carbon::now()->subDays(self::DAYS_CUTOFF);
        $geoCache  = [];

        $this->info('Scraping myhome.ge — monthly + daily rent listings for all Tbilisi...');

        // Prune listings older than cutoff so the DB stays lean
        $pruned = Listing::where('listed_at', '<', $cutoff)->delete();
        if ($pruned) $this->line("Pruned {$pruned} expired listings.");

        foreach (self::DEAL_TYPES as $dealType) {
            $label = $dealType === '7' ? 'Daily' : 'Monthly';
            $this->info("\n── {$label} rentals ─────────────────────────────");

            for ($page = 1; $page <= $maxPages; $page++) {
                $url      = $this->listUrl($dealType, $page);
                $listings = $this->fetchListPage($url);

                if (empty($listings)) {
                    $this->line("  Page {$page}: empty — stopping.");
                    break;
                }

                $this->line("  Page {$page}: " . count($listings) . ' listings');

                $allOld    = true;
                $rentCount = 0;

                foreach ($listings as $l) {
                    if (! in_array($l['deal_type_id'] ?? 0, [2, 7])) continue;
                    $rentCount++;

                    $updatedAt = Carbon::parse($l['last_updated'], 'Asia/Tbilisi')->setTimezone('UTC');
                    if ($updatedAt->lt($cutoff)) continue;
                    $allOld = false;

                    $poster = ($l['user_type']['type'] ?? 'physical') === 'physical' ? 'owner' : 'agent';
                    if ($poster !== 'owner') continue;

                    $this->processListing($l, $updatedAt, $geoCache, $detailDelayMs);
                }

                if ($allOld && $rentCount > 0) {
                    $this->line("  Reached listings older than " . self::DAYS_CUTOFF . " days — stopping.");
                    break;
                }

                usleep($delayMs * 1000);
            }
        }

        $this->newLine();
        $this->info("Done. Saved: {$this->saved}  Updated: {$this->updated}  Skipped (no coords): {$this->skipped}");
    }

    private function processListing(array $l, Carbon $updatedAt, array &$geoCache, int $detailDelayMs): void
    {
        $id     = (string) $l['id'];
        $slug   = $l['href_lang']['en'] ?? ($l['dynamic_slug'] ?? '');
        $poster = ($l['user_type']['type'] ?? 'physical') === 'physical' ? 'owner' : 'agent';

        usleep($detailDelayMs * 1000);
        [$lat, $lng, $geocoded, $ownerName, $phone] = $this->resolveDetail($l, $slug, $geoCache);

        if (! $lat || ! $lng) {
            $this->skipped++;
            return;
        }

        $data = [
            'title'         => $l['dynamic_title'] ?? null,
            'price'         => (float) ($l['price'][2]['price_total'] ?? 0) ?: null,
            'currency'      => 'USD',
            'lat'           => $lat,
            'lng'           => $lng,
            'geocoded'      => $geocoded,
            'address'       => $l['address'] ?? null,
            'area'          => isset($l['area']) && $l['area'] ? $l['area'] . ' m²' : null,
            'rooms'         => isset($l['room']) ? (string) $l['room'] : null,
            'bedrooms'      => isset($l['bedroom']) ? (int) $l['bedroom'] : null,
            'rent_type'     => ($l['deal_type_id'] ?? 2) === 7 ? 'daily' : 'monthly',
            'district_id'   => $l['district_id'] ?? null,
            'district_name' => $l['district_name'] ?? null,
            'newly_built'   => false,
            'poster_type'   => $poster,
            'owner_name'    => $ownerName,
            'phone'         => $phone,
            'listed_at'     => $updatedAt,
            'url'           => "https://www.myhome.ge/en/pr/{$id}" . ($slug ? "/{$slug}" : ''),
        ];

        $existing = Listing::where('listing_id', $id)->first();

        if ($existing) {
            $existing->update($data);
            $this->updated++;
        } else {
            Listing::create(array_merge($data, ['listing_id' => $id]));
            $this->saved++;
        }
    }

    // ── Coordinates + contact ─────────────────────────────────────────────

    // Returns [lat, lng, geocoded, owner_name, phone]
    private function resolveDetail(array $l, string $slug, array &$geoCache): array
    {
        $detail = $this->fetchDetailData((string) $l['id'], $slug);

        // Coords: prefer detail page (exact), fall back to list page (swapped lat/lng)
        if ($detail['lat'] !== null) {
            return [$detail['lat'], $detail['lng'], false, $detail['owner_name'], $detail['phone']];
        }

        if (! empty($l['lat']) && ! empty($l['lng'])) {
            return [(float) $l['lng'], (float) $l['lat'], false, $detail['owner_name'], $detail['phone']];
        }

        // Nominatim — neighbourhood-level, cached per area
        $cacheKey = ($l['urban_name'] ?? '') . '|' . ($l['district_name'] ?? '');
        if (isset($geoCache[$cacheKey])) {
            [$lat, $lng, $geo] = $geoCache[$cacheKey];
            return [$lat, $lng, $geo, $detail['owner_name'], $detail['phone']];
        }

        $coords = $this->geocode($l['address'] ?? null, $l['urban_name'] ?? null, $l['district_name'] ?? null);
        if ($coords[0] !== null) {
            $geoCache[$cacheKey] = [$coords[0], $coords[1], true];
            return [$coords[0], $coords[1], true, $detail['owner_name'], $detail['phone']];
        }

        // District centre as last resort
        $fallback = $this->districtFallback($l['district_name'] ?? null);
        return [$fallback[0], $fallback[1] ?? null, false, $detail['owner_name'], $detail['phone']];
    }

    // Returns ['lat', 'lng', 'owner_name', 'phone'] from the detail page __NEXT_DATA__
    private function fetchDetailData(string $id, string $slug): array
    {
        $null  = ['lat' => null, 'lng' => null, 'owner_name' => null, 'phone' => null];
        $debug = $this->option('debug-listing') === $id;

        $url = "https://www.myhome.ge/en/pr/{$id}" . ($slug ? "/{$slug}" : '');
        try {
            $response = Http::withHeaders(self::HEADERS)->timeout(20)->get($url);
        } catch (\Exception $e) {
            return $null;
        }

        if (! $response->successful()) return $null;

        $body = $response->body();
        unset($response);

        if (! preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $body, $m)) {
            if ($debug) $this->warn("[$id] No __NEXT_DATA__ found.");
            return $null;
        }

        unset($body);
        $data = json_decode($m[1], true);
        unset($m);

        if ($debug) {
            $keys = array_map(fn($q) => json_encode($q['queryKey'] ?? []), $data['props']['pageProps']['dehydratedState']['queries'] ?? []);
            $this->line("[$id] Query keys: " . implode(' | ', $keys));
        }

        // Try both 'details' and any query key containing the listing id
        $stmt = null;
        foreach ($data['props']['pageProps']['dehydratedState']['queries'] ?? [] as $q) {
            $qk = $q['queryKey'] ?? [];
            if (in_array('details', $qk) || in_array((int)$id, $qk) || in_array($id, $qk)) {
                $stmt = $q['state']['data']['data']['statement']
                     ?? $q['state']['data']['data']
                     ?? $q['state']['data']['statement']
                     ?? null;
                if ($debug) $this->line("[$id] Matched query key: " . json_encode($qk));
                break;
            }
        }

        unset($data);

        if (! $stmt) {
            if ($debug) $this->warn("[$id] No matching query / statement found.");
            return $null;
        }

        if ($debug) $this->line("[$id] Statement keys: " . implode(', ', array_keys($stmt)));

        $lat = $lng = null;
        if (! empty($stmt['lat']) && ! empty($stmt['lng'])) {
            $lat = (float) $stmt['lng']; // myhome.ge swaps lat/lng
            $lng = (float) $stmt['lat'];
        }

        // Owner name — stored directly on statement
        $ownerName = $stmt['owner_name'] ?? null;

        // Phones — stored directly on statement
        $phones = array_filter([
            $stmt['user_phone_number'] ?? null,
            $stmt['additional_phone_number'] ?? null,
        ]);
        $phone = ! empty($phones) ? implode(', ', $phones) : null;

        if ($debug) $this->line("[$id] owner=$ownerName phone=$phone lat=$lat lng=$lng");

        return ['lat' => $lat, 'lng' => $lng, 'owner_name' => $ownerName, 'phone' => $phone];
    }

    private function geocode(?string $address, ?string $urban, ?string $district): array
    {
        $candidates = array_filter([
            $address ? implode(', ', array_filter([$address, 'Tbilisi', 'Georgia'])) : null,
            implode(', ', array_filter([$urban, $district, 'Tbilisi', 'Georgia'])),
            implode(', ', array_filter([$district, 'Tbilisi', 'Georgia'])),
        ]);

        foreach ($candidates as $query) {
            usleep(1_100_000); // Nominatim: max 1 req/sec

            $response = Http::withHeaders([
                'User-Agent' => 'MAPmyhomes.ge/1.0 (info.tsiklauri@gmail.com)',
                'Accept'     => 'application/json',
            ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $query,
                'format' => 'json',
                'limit'  => 1,
            ]);

            if ($response->successful() && ! empty($response->json()[0])) {
                $r = $response->json()[0];
                return [(float) $r['lat'], (float) $r['lon']];
            }
        }

        return [null, null];
    }

    private function districtFallback(?string $district): array
    {
        if (! $district) return [null, null];
        foreach (self::DISTRICT_COORDS as $name => $coords) {
            [$a, $b] = explode('-', $name);
            if (stripos($district, $a) !== false || stripos($district, $b) !== false) {
                return $coords;
            }
        }
        return [null, null];
    }

    // ── myhome.ge fetching ────────────────────────────────────────────────

    private function listUrl(string $dealType, int $page): string
    {
        return 'https://www.myhome.ge/en/s/Apartment-for-rent/?' . http_build_query([
            'deal_types'        => $dealType,
            'cities'            => '1',
            'currency_id'       => '2',
            'real_estate_types' => '1',
            'owner_type'        => 'physical', // owners only — eliminates agent duplicates
            'CardView'          => '1',
            'page'              => $page,
        ]);
    }

    private function fetchListPage(string $url): array
    {
        $response = Http::withHeaders(self::HEADERS)->timeout(20)->get($url);
        if (! $response->successful()) return [];

        $body = $response->body();
        unset($response);

        if (! preg_match('/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s', $body, $m)) {
            return [];
        }

        unset($body);
        $data = json_decode($m[1], true);
        unset($m);

        foreach ($data['props']['pageProps']['dehydratedState']['queries'] ?? [] as $q) {
            $qdata = $q['state']['data'] ?? null;
            if (is_array($qdata) && isset($qdata['data']['data'])) {
                $listings = $qdata['data']['data'];
                unset($data);
                return $listings;
            }
        }

        return [];
    }
}
