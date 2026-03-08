<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeZoneSeeder extends Seeder
{
    public function run(): void
    {
        $timezones = [
            ['name' => 'UTC', 'label' => '(UTC+00:00) Coordinated Universal Time', 'utc_offset' => '+00:00'],
            ['name' => 'Europe/London', 'label' => '(UTC+00:00) London', 'utc_offset' => '+00:00'],
            ['name' => 'Europe/Paris', 'label' => '(UTC+01:00) Paris', 'utc_offset' => '+01:00'],
            ['name' => 'Europe/Berlin', 'label' => '(UTC+01:00) Berlin', 'utc_offset' => '+01:00'],
            ['name' => 'Africa/Cairo', 'label' => '(UTC+02:00) Cairo', 'utc_offset' => '+02:00'],
            ['name' => 'Africa/Nairobi', 'label' => '(UTC+03:00) Nairobi', 'utc_offset' => '+03:00'],
            ['name' => 'Asia/Dubai', 'label' => '(UTC+04:00) Dubai', 'utc_offset' => '+04:00'],
            ['name' => 'Asia/Karachi', 'label' => '(UTC+05:00) Karachi', 'utc_offset' => '+05:00'],
            ['name' => 'Asia/Dhaka', 'label' => '(UTC+06:00) Dhaka', 'utc_offset' => '+06:00'],
            ['name' => 'Asia/Jakarta', 'label' => '(UTC+07:00) Jakarta', 'utc_offset' => '+07:00'],
            ['name' => 'Asia/Shanghai', 'label' => '(UTC+08:00) Beijing', 'utc_offset' => '+08:00'],
            ['name' => 'Asia/Tokyo', 'label' => '(UTC+09:00) Tokyo', 'utc_offset' => '+09:00'],
            ['name' => 'Australia/Sydney', 'label' => '(UTC+10:00) Sydney', 'utc_offset' => '+10:00'],
            ['name' => 'America/New_York', 'label' => '(UTC-05:00) New York', 'utc_offset' => '-05:00'],
            ['name' => 'America/Chicago', 'label' => '(UTC-06:00) Chicago', 'utc_offset' => '-06:00'],
            ['name' => 'America/Denver', 'label' => '(UTC-07:00) Denver', 'utc_offset' => '-07:00'],
            ['name' => 'America/Los_Angeles', 'label' => '(UTC-08:00) Los Angeles', 'utc_offset' => '-08:00'],
            ['name' => 'America/Sao_Paulo', 'label' => '(UTC-03:00) São Paulo', 'utc_offset' => '-03:00'],
        ];

        DB::table('time_zones')->insert($timezones);
    }
}
