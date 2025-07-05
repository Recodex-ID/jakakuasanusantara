<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Head Office Jakarta',
                'address' => 'Jl. Sudirman Kav. 52-53, Lot 8, Jakarta Selatan 12190, Indonesia',
                'latitude' => -6.9211752933673845,
                'longitude' => 106.88885954448519,
                'radius_meters' => 150,
                'status' => 'active',
            ],
            [
                'name' => 'Branch Office Bandung',
                'address' => 'Jl. Asia Afrika No. 8, Bandung 40111, West Java, Indonesia',
                'latitude' => -6.9218,
                'longitude' => 107.6048,
                'radius_meters' => 120,
                'status' => 'active',
            ],
            [
                'name' => 'Old Office Building',
                'address' => 'Jl. Thamrin No. 1, Jakarta Pusat 10310, Indonesia',
                'latitude' => -6.1944,
                'longitude' => 106.8229,
                'radius_meters' => 100,
                'status' => 'inactive',
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
