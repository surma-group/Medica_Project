<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Upazila;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UpazilaSeeder extends Seeder
{
    public function run(): void
    {
        $url = 'https://raw.githubusercontent.com/'
            . 'open-admin-data/bangladesh-administrative-divisions/'
            . 'main/data/all-upazila.json';

        $response = Http::timeout(120)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Upazila dataset download failed. HTTP status: '
                    . $response->status()
            );
        }

        $data = $response->json();

        if (! is_array($data) || count($data) === 0) {
            throw new RuntimeException(
                'Invalid or empty upazila dataset.'
            );
        }

        // Match dataset district names with existing database names.
        $aliases = [
            'Chattogram' => 'Chittagong',
            'Chattagram' => 'Chittagong',
            'Cumilla' => 'Comilla',
            'Barishal' => 'Barisal',
            "Cox's Bazar" => 'Cox’s Bazar',
            'Bogra' => 'Bogura',
            'Jessore' => 'Jashore',
            'Khagrachhari' => 'Khagrachari',
            'Chapainababganj' => 'Chapainawabganj',
            'Jhalokati' => 'Jhalokathi',
            'Netrakona' => 'Netrokona',
        ];

        $processed = 0;

        foreach ($data as $item) {
            $districtName = data_get(
                $item,
                'parent.name.en'
            );

            $nameEn = data_get($item, 'name.en');
            $nameBn = data_get($item, 'name.local');

            if (! $districtName || ! $nameEn || ! $nameBn) {
                continue;
            }

            $districtName = $aliases[$districtName]
                ?? $districtName;

            $district = District::where(
                'name_en',
                $districtName
            )->first();

            if (! $district) {
                throw new RuntimeException(
                    "District not found: {$districtName}. "
                        . 'Check the districts table and aliases.'
                );
            }

            Upazila::updateOrCreate(
                [
                    'district_id' => $district->id,
                    'name_en' => $nameEn,
                ],
                [
                    'name_bn' => $nameBn,
                ]
            );

            $processed++;
        }

        $this->command?->info(
            "Dataset records processed: {$processed}"
        );

        $this->command?->info(
            'Total upazila records: ' . Upazila::count()
        );
    }
}
