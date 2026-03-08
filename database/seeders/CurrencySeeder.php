<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'symbol_native' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'symbol_native' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'symbol_native' => '£'],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'symbol_native' => '৳'],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'symbol_native' => '₹'],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'symbol_native' => '₨'],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'symbol_native' => 'د.إ'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼', 'symbol_native' => '﷼'],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'symbol_native' => '¥'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'symbol_native' => '¥'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => '$', 'symbol_native' => '$'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => '$', 'symbol_native' => '$'],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
