<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['name_en'=>'Dhaka', 'name_bn'=>'ঢাকা'],
            ['name_en'=>'Gazipur', 'name_bn'=>'গাজীপুর'],
            ['name_en'=>'Narayanganj', 'name_bn'=>'নারায়ণগঞ্জ'],
            ['name_en'=>'Manikganj', 'name_bn'=>'মানিকগঞ্জ'],
            ['name_en'=>'Munshiganj', 'name_bn'=>'মুন্সীগঞ্জ'],
            ['name_en'=>'Narsingdi', 'name_bn'=>'নরসিংদী'],
            ['name_en'=>'Tangail', 'name_bn'=>'টাঙ্গাইল'],
            ['name_en'=>'Kishoreganj', 'name_bn'=>'কিশোরগঞ্জ'],
            ['name_en'=>'Faridpur', 'name_bn'=>'ফরিদপুর'],
            ['name_en'=>'Rajbari', 'name_bn'=>'রাজবাড়ি'],
            ['name_en'=>'Shariatpur', 'name_bn'=>'শরীয়তপুর'],
            ['name_en'=>'Madaripur', 'name_bn'=>'মাদারীপুর'],
            ['name_en'=>'Gopalganj', 'name_bn'=>'গোপালগঞ্জ'],
            ['name_en'=>'Magura', 'name_bn'=>'মাগুরা'],
            ['name_en'=>'Jashore', 'name_bn'=>'যশোর'],
            ['name_en'=>'Jhenaidah', 'name_bn'=>'ঝিনাইদহ'],
            ['name_en'=>'Kushtia', 'name_bn'=>'কুষ্টিয়া'],
            ['name_en'=>'Meherpur', 'name_bn'=>'মেহেরপুর'],
            ['name_en'=>'Narail', 'name_bn'=>'নড়াইল'],
            ['name_en'=>'Bagerhat', 'name_bn'=>'বাগেরহাট'],
            ['name_en'=>'Khulna', 'name_bn'=>'খুলনা'],
            ['name_en'=>'Satkhira', 'name_bn'=>'সাতক্ষীরা'],
            ['name_en'=>'Chuadanga', 'name_bn'=>'চুয়াডাঙ্গা'],
            ['name_en'=>'Pirojpur', 'name_bn'=>'পিরোজপুর'],
            ['name_en'=>'Patuakhali', 'name_bn'=>'পটুয়াখালী'],
            ['name_en'=>'Barguna', 'name_bn'=>'বরগুনা'],
            ['name_en'=>'Barisal', 'name_bn'=>'বরিশাল'],
            ['name_en'=>'Bhola', 'name_bn'=>'ভোলা'],
            ['name_en'=>'Jhalokathi', 'name_bn'=>'ঝালকাঠি'],
            ['name_en'=>'Sylhet', 'name_bn'=>'সিলেট'],
            ['name_en'=>'Sunamganj', 'name_bn'=>'সুনামগঞ্জ'],
            ['name_en'=>'Habiganj', 'name_bn'=>'হবিগঞ্জ'],
            ['name_en'=>'Moulvibazar', 'name_bn'=>'মৌলভীবাজার'],
            ['name_en'=>'Cox’s Bazar', 'name_bn'=>'কক্সবাজার'],
            ['name_en'=>'Chittagong', 'name_bn'=>'চট্টগ্রাম'],
            ['name_en'=>'Rangamati', 'name_bn'=>'রাঙ্গামাটি'],
            ['name_en'=>'Bandarban', 'name_bn'=>'বান্দরবান'],
            ['name_en'=>'Khagrachari', 'name_bn'=>'খাগড়াছড়ি'],
            ['name_en'=>'Feni', 'name_bn'=>'ফেনী'],
            ['name_en'=>'Lakshmipur', 'name_bn'=>'লক্ষ্মীপুর'],
            ['name_en'=>'Noakhali', 'name_bn'=>'নোয়াখালী'],
            ['name_en'=>'Brahmanbaria', 'name_bn'=>'ব্রাহ্মণবাড়ীয়া'],
            ['name_en'=>'Comilla', 'name_bn'=>'কুমিল্লা'],
            ['name_en'=>'Chandpur', 'name_bn'=>'চাঁদপুর'],
            ['name_en'=>'Chapainawabganj', 'name_bn'=>'চাঁপাইনবাবগঞ্জ'],
            ['name_en'=>'Rajshahi', 'name_bn'=>'রাজশাহী'],
            ['name_en'=>'Natore', 'name_bn'=>'নাটোর'],
            ['name_en'=>'Naogaon', 'name_bn'=>'নওগাঁ'],
            ['name_en'=>'Pabna', 'name_bn'=>'পাবনা'],
            ['name_en'=>'Sirajganj', 'name_bn'=>'সিরাজগঞ্জ'],
            ['name_en'=>'Bogura', 'name_bn'=>'বগুড়া'],
            ['name_en'=>'Joypurhat', 'name_bn'=>'জয়পুরহাট'],
            ['name_en'=>'Rangpur', 'name_bn'=>'রংপুর'],
            ['name_en'=>'Dinajpur', 'name_bn'=>'দিনাজপুর'],
            ['name_en'=>'Kurigram', 'name_bn'=>'কুড়িগ্রাম'],
            ['name_en'=>'Nilphamari', 'name_bn'=>'নীলফামারী'],
            ['name_en'=>'Thakurgaon', 'name_bn'=>'ঠাকুরগাঁও'],
            ['name_en'=>'Panchagarh', 'name_bn'=>'পঞ্চগড়'],
            ['name_en'=>'Lalmonirhat', 'name_bn'=>'লালমনিরহাট'],
            ['name_en'=>'Sherpur', 'name_bn'=>'শেরপুর'],
            ['name_en'=>'Netrokona', 'name_bn'=>'নেত্রকোনা'],
        ];

        DB::table('districts')->insert($districts);
    }
}
