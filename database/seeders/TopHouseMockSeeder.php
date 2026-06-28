<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\SavedListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TopHouseMockSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::findOrFail(2); // TopHouse

        // Bump seat limit to fit mock employees
        $org->update(['user_limit' => 20]);

        $employeeNames = [
            ['name' => 'Giorgi Beridze',   'email' => 'giorgi@tophouse.ge'],
            ['name' => 'Nino Kvaratskhelia','email' => 'nino@tophouse.ge'],
            ['name' => 'Lasha Mchedlishvili','email' => 'lasha@tophouse.ge'],
            ['name' => 'Tamara Jgenti',    'email' => 'tamara@tophouse.ge'],
            ['name' => 'Davit Arabuli',    'email' => 'davit@tophouse.ge'],
        ];

        $employees = [];
        foreach ($employeeNames as $data) {
            $employees[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'            => $data['name'],
                    'password'        => Hash::make('password'),
                    'role'            => 'employee',
                    'organization_id' => $org->id,
                ]
            );
        }

        $titles = [
            'Spacious 3-room apartment in Vake',
            'New build studio in Saburtalo',
            'Cozy 2-room flat near Rustaveli Metro',
            'Renovated apartment in Old Tbilisi',
            'Modern 4-room penthouse in Mtatsminda',
            '2-room apartment with balcony in Isani',
            'Bright studio in Didi Dighomi',
            'Family home in Gldani',
            '1-room apartment near Didube Metro',
            'Luxury flat in Vera district',
            '3-room apartment in Ortachala',
            'Compact studio in Nadzaladevi',
            'New development unit in Varketili',
            'Spacious flat with garden view in Avlabari',
            '2-room apartment in Krtsanisi',
        ];

        $addresses = [
            'Kostava St 45, Tbilisi',
            'Chavchavadze Ave 12, Vake',
            'Rustaveli Ave 78, City Centre',
            'Agmashenebeli Ave 34, Didube',
            'David Aghmashenebeli 101, Tbilisi',
            'Vazha-Pshavela Ave 56, Saburtalo',
            'Mosashvili St 3, Vake',
            'Kazbegi Ave 22, Saburtalo',
            'Pekini Ave 44, Tbilisi',
            'Merab Kostava 17, Tbilisi',
        ];

        $districts  = ['Vake', 'Saburtalo', 'Mtatsminda', 'Isani', 'Gldani', 'Ortachala', 'Vera', 'Old Tbilisi', 'Didube', 'Avlabari'];
        $owners     = ['Giorgi M.', 'Nino T.', 'Davit K.', 'Tamara L.', 'Lasha B.', 'Ana S.', 'Irakli V.', null];
        $phones     = ['+995 599 123456', '+995 577 654321', '+995 555 111222', '+995 598 333444', null];
        $notes      = [
            'Owner is flexible on price',
            'Ready to move in immediately',
            'Needs minor renovation',
            'Great location, quiet street',
            'Motivated seller',
            'Price negotiable',
            null, null, null,
        ];

        $listingIds  = [];
        $myhomeLinks = [];
        $ssLinks     = [];

        // Pre-generate some fake listing IDs and links
        for ($i = 1; $i <= 100; $i++) {
            $listingIds[]  = (string) rand(100000, 999999);
            $myhomeLinks[] = rand(0, 1) ? 'https://www.myhome.ge/pr/' . rand(10000, 99999) . '/' : null;
            $ssLinks[]     = rand(0, 1) ? 'https://ss.ge/en/real-estate/l/Flat/For-Sale/' . rand(10000, 99999) : null;
        }

        $now = now();

        for ($i = 0; $i < 100; $i++) {
            $emp      = $employees[array_rand($employees)];
            $price    = rand(40, 300) * 1000;
            $myPrice  = rand(0, 1) ? $price - rand(5, 30) * 1000 : null;
            $daysAgo  = rand(0, 60);
            $savedDate = $now->copy()->subDays($daysAgo)->toDateString();

            SavedListing::create([
                'user_id'          => $emp->id,
                'organization_id'  => $org->id,
                'listing_id'       => $listingIds[$i],
                'listing_snapshot' => [
                    'title'      => $titles[array_rand($titles)],
                    'address'    => $addresses[array_rand($addresses)],
                    'price'      => $price,
                    'rooms'      => rand(1, 5),
                    'area'       => rand(35, 180),
                    'district'   => $districts[array_rand($districts)],
                    'owner_name' => $owners[array_rand($owners)],
                    'phone'      => $phones[array_rand($phones)],
                    'url'        => 'https://www.myhome.ge/pr/' . rand(10000, 99999) . '/',
                    'rent_type'  => 'For Sale',
                ],
                'my_price'    => $myPrice,
                'note'        => $notes[array_rand($notes)],
                'link_myhome' => $myhomeLinks[$i],
                'link_ss'     => $ssLinks[$i],
                'saved_date'  => $savedDate,
                'created_at'  => $now->copy()->subDays($daysAgo)->subHours(rand(0, 8)),
                'updated_at'  => $now->copy()->subDays($daysAgo)->subHours(rand(0, 8)),
            ]);
        }

        $this->command->info('✓ TopHouse seeded: 5 employees, 100 listings');
    }
}
