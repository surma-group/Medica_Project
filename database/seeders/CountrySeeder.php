<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::updateOrCreate(
            ['iso2' => 'BD'],
            [
                'name_en' => 'Bangladesh',
                'name_bn' => 'বাংলাদেশ',
            ]
        );
    }
}