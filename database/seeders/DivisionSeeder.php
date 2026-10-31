<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('iso2', 'BD')->firstOrFail();

        $divisions = [
            [
                'name_en' => 'Dhaka',
                'name_bn' => 'ঢাকা',
                'code' => 'DHA',
            ],
            [
                'name_en' => 'Chattogram',
                'name_bn' => 'চট্টগ্রাম',
                'code' => 'CHA',
            ],
            [
                'name_en' => 'Rajshahi',
                'name_bn' => 'রাজশাহী',
                'code' => 'RAJ',
            ],
            [
                'name_en' => 'Khulna',
                'name_bn' => 'খুলনা',
                'code' => 'KHU',
            ],
            [
                'name_en' => 'Barishal',
                'name_bn' => 'বরিশাল',
                'code' => 'BAR',
            ],
            [
                'name_en' => 'Sylhet',
                'name_bn' => 'সিলেট',
                'code' => 'SYL',
            ],
            [
                'name_en' => 'Rangpur',
                'name_bn' => 'রংপুর',
                'code' => 'RAN',
            ],
            [
                'name_en' => 'Mymensingh',
                'name_bn' => 'ময়মনসিংহ',
                'code' => 'MYM',
            ],
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'name_en' => $division['name_en'],
                ],
                [
                    'name_bn' => $division['name_bn'],
                    'code' => $division['code'],
                ]
            );
        }
    }
}