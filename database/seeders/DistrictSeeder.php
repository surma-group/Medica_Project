<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Division;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::where('iso2', 'BD')->firstOrFail();

        $districts = [
            // Dhaka Division
            ['division' => 'Dhaka', 'name_en' => 'Dhaka', 'name_bn' => 'ঢাকা'],
            ['division' => 'Dhaka', 'name_en' => 'Gazipur', 'name_bn' => 'গাজীপুর'],
            ['division' => 'Dhaka', 'name_en' => 'Narayanganj', 'name_bn' => 'নারায়ণগঞ্জ'],
            ['division' => 'Dhaka', 'name_en' => 'Manikganj', 'name_bn' => 'মানিকগঞ্জ'],
            ['division' => 'Dhaka', 'name_en' => 'Munshiganj', 'name_bn' => 'মুন্সীগঞ্জ'],
            ['division' => 'Dhaka', 'name_en' => 'Narsingdi', 'name_bn' => 'নরসিংদী'],
            ['division' => 'Dhaka', 'name_en' => 'Tangail', 'name_bn' => 'টাঙ্গাইল'],
            ['division' => 'Dhaka', 'name_en' => 'Kishoreganj', 'name_bn' => 'কিশোরগঞ্জ'],
            ['division' => 'Dhaka', 'name_en' => 'Faridpur', 'name_bn' => 'ফরিদপুর'],
            ['division' => 'Dhaka', 'name_en' => 'Rajbari', 'name_bn' => 'রাজবাড়ি'],
            ['division' => 'Dhaka', 'name_en' => 'Shariatpur', 'name_bn' => 'শরীয়তপুর'],
            ['division' => 'Dhaka', 'name_en' => 'Madaripur', 'name_bn' => 'মাদারীপুর'],
            ['division' => 'Dhaka', 'name_en' => 'Gopalganj', 'name_bn' => 'গোপালগঞ্জ'],

            // Chattogram Division
            ['division' => 'Chattogram', 'name_en' => 'Cox’s Bazar', 'name_bn' => 'কক্সবাজার'],
            ['division' => 'Chattogram', 'name_en' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম'],
            ['division' => 'Chattogram', 'name_en' => 'Rangamati', 'name_bn' => 'রাঙ্গামাটি'],
            ['division' => 'Chattogram', 'name_en' => 'Bandarban', 'name_bn' => 'বান্দরবান'],
            ['division' => 'Chattogram', 'name_en' => 'Khagrachari', 'name_bn' => 'খাগড়াছড়ি'],
            ['division' => 'Chattogram', 'name_en' => 'Feni', 'name_bn' => 'ফেনী'],
            ['division' => 'Chattogram', 'name_en' => 'Lakshmipur', 'name_bn' => 'লক্ষ্মীপুর'],
            ['division' => 'Chattogram', 'name_en' => 'Noakhali', 'name_bn' => 'নোয়াখালী'],
            ['division' => 'Chattogram', 'name_en' => 'Brahmanbaria', 'name_bn' => 'ব্রাহ্মণবাড়ীয়া'],
            ['division' => 'Chattogram', 'name_en' => 'Comilla', 'name_bn' => 'কুমিল্লা'],
            ['division' => 'Chattogram', 'name_en' => 'Chandpur', 'name_bn' => 'চাঁদপুর'],

            // Rajshahi Division
            ['division' => 'Rajshahi', 'name_en' => 'Chapainawabganj', 'name_bn' => 'চাঁপাইনবাবগঞ্জ'],
            ['division' => 'Rajshahi', 'name_en' => 'Rajshahi', 'name_bn' => 'রাজশাহী'],
            ['division' => 'Rajshahi', 'name_en' => 'Natore', 'name_bn' => 'নাটোর'],
            ['division' => 'Rajshahi', 'name_en' => 'Naogaon', 'name_bn' => 'নওগাঁ'],
            ['division' => 'Rajshahi', 'name_en' => 'Pabna', 'name_bn' => 'পাবনা'],
            ['division' => 'Rajshahi', 'name_en' => 'Sirajganj', 'name_bn' => 'সিরাজগঞ্জ'],
            ['division' => 'Rajshahi', 'name_en' => 'Bogura', 'name_bn' => 'বগুড়া'],
            ['division' => 'Rajshahi', 'name_en' => 'Joypurhat', 'name_bn' => 'জয়পুরহাট'],

            // Khulna Division
            ['division' => 'Khulna', 'name_en' => 'Magura', 'name_bn' => 'মাগুরা'],
            ['division' => 'Khulna', 'name_en' => 'Jashore', 'name_bn' => 'যশোর'],
            ['division' => 'Khulna', 'name_en' => 'Jhenaidah', 'name_bn' => 'ঝিনাইদহ'],
            ['division' => 'Khulna', 'name_en' => 'Kushtia', 'name_bn' => 'কুষ্টিয়া'],
            ['division' => 'Khulna', 'name_en' => 'Meherpur', 'name_bn' => 'মেহেরপুর'],
            ['division' => 'Khulna', 'name_en' => 'Narail', 'name_bn' => 'নড়াইল'],
            ['division' => 'Khulna', 'name_en' => 'Bagerhat', 'name_bn' => 'বাগেরহাট'],
            ['division' => 'Khulna', 'name_en' => 'Khulna', 'name_bn' => 'খুলনা'],
            ['division' => 'Khulna', 'name_en' => 'Satkhira', 'name_bn' => 'সাতক্ষীরা'],
            ['division' => 'Khulna', 'name_en' => 'Chuadanga', 'name_bn' => 'চুয়াডাঙ্গা'],

            // Barishal Division
            ['division' => 'Barishal', 'name_en' => 'Pirojpur', 'name_bn' => 'পিরোজপুর'],
            ['division' => 'Barishal', 'name_en' => 'Patuakhali', 'name_bn' => 'পটুয়াখালী'],
            ['division' => 'Barishal', 'name_en' => 'Barguna', 'name_bn' => 'বরগুনা'],
            ['division' => 'Barishal', 'name_en' => 'Barisal', 'name_bn' => 'বরিশাল'],
            ['division' => 'Barishal', 'name_en' => 'Bhola', 'name_bn' => 'ভোলা'],
            ['division' => 'Barishal', 'name_en' => 'Jhalokathi', 'name_bn' => 'ঝালকাঠি'],

            // Sylhet Division
            ['division' => 'Sylhet', 'name_en' => 'Sylhet', 'name_bn' => 'সিলেট'],
            ['division' => 'Sylhet', 'name_en' => 'Sunamganj', 'name_bn' => 'সুনামগঞ্জ'],
            ['division' => 'Sylhet', 'name_en' => 'Habiganj', 'name_bn' => 'হবিগঞ্জ'],
            ['division' => 'Sylhet', 'name_en' => 'Moulvibazar', 'name_bn' => 'মৌলভীবাজার'],

            // Rangpur Division
            ['division' => 'Rangpur', 'name_en' => 'Rangpur', 'name_bn' => 'রংপুর'],
            ['division' => 'Rangpur', 'name_en' => 'Dinajpur', 'name_bn' => 'দিনাজপুর'],
            ['division' => 'Rangpur', 'name_en' => 'Kurigram', 'name_bn' => 'কুড়িগ্রাম'],
            ['division' => 'Rangpur', 'name_en' => 'Nilphamari', 'name_bn' => 'নীলফামারী'],
            ['division' => 'Rangpur', 'name_en' => 'Thakurgaon', 'name_bn' => 'ঠাকুরগাঁও'],
            ['division' => 'Rangpur', 'name_en' => 'Panchagarh', 'name_bn' => 'পঞ্চগড়'],
            ['division' => 'Rangpur', 'name_en' => 'Lalmonirhat', 'name_bn' => 'লালমনিরহাট'],
            ['division' => 'Rangpur', 'name_en' => 'Gaibandha', 'name_bn' => 'গাইবান্ধা'],

            // Mymensingh Division
            ['division' => 'Mymensingh', 'name_en' => 'Sherpur', 'name_bn' => 'শেরপুর'],
            ['division' => 'Mymensingh', 'name_en' => 'Netrokona', 'name_bn' => 'নেত্রকোনা'],
            ['division' => 'Mymensingh', 'name_en' => 'Jamalpur', 'name_bn' => 'জামালপুর'],
            ['division' => 'Mymensingh', 'name_en' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ'],
        ];

        foreach ($districts as $item) {
            $division = Division::where('country_id', $country->id)
                ->where('name_en', $item['division'])
                ->firstOrFail();

            District::updateOrCreate(
                ['name_en' => $item['name_en']],
                [
                    'division_id' => $division->id,
                    'name_bn' => $item['name_bn'],
                ]
            );
        }
    }
}