<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            // Dhaka Division
            ['name' => 'Dhaka',          'name_bn' => 'ঢাকা',          'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.8103,  'longitude' => 90.4125],
            ['name' => 'Gazipur',        'name_bn' => 'গাজীপুর',       'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.9999,  'longitude' => 90.4203],
            ['name' => 'Narayanganj',    'name_bn' => 'নারায়ণগঞ্জ',   'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.6238,  'longitude' => 90.4996],
            ['name' => 'Manikganj',      'name_bn' => 'মানিকগঞ্জ',    'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.8623,  'longitude' => 90.0082],
            ['name' => 'Munshiganj',     'name_bn' => 'মুন্সীগঞ্জ',   'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.5422,  'longitude' => 90.5305],
            ['name' => 'Narsingdi',      'name_bn' => 'নরসিংদী',      'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.9324,  'longitude' => 90.7150],
            ['name' => 'Faridpur',       'name_bn' => 'ফরিদপুর',      'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.6070,  'longitude' => 89.8429],
            ['name' => 'Gopalganj',      'name_bn' => 'গোপালগঞ্জ',    'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.0050,  'longitude' => 89.8263],
            ['name' => 'Madaripur',      'name_bn' => 'মাদারীপুর',    'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.1641,  'longitude' => 90.2018],
            ['name' => 'Rajbari',        'name_bn' => 'রাজবাড়ী',     'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.7574,  'longitude' => 89.6444],
            ['name' => 'Shariatpur',     'name_bn' => 'শরীয়তপুর',    'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 23.2423,  'longitude' => 90.4352],
            ['name' => 'Kishoreganj',    'name_bn' => 'কিশোরগঞ্জ',    'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 24.4449,  'longitude' => 90.7766],
            ['name' => 'Tangail',        'name_bn' => 'টাঙ্গাইল',     'division' => 'Dhaka',     'division_bn' => 'ঢাকা',       'latitude' => 24.2513,  'longitude' => 89.9167],

            // Chittagong Division
            ['name' => 'Chittagong',     'name_bn' => 'চট্টগ্রাম',    'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 22.3569,  'longitude' => 91.7832],
            ['name' => 'Cox\'s Bazar',   'name_bn' => "কক্সবাজার",    'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 21.4272,  'longitude' => 92.0058],
            ['name' => 'Comilla',        'name_bn' => 'কুমিল্লা',     'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 23.4607,  'longitude' => 91.1809],
            ['name' => 'Feni',           'name_bn' => 'ফেনী',          'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 23.0233,  'longitude' => 91.3958],
            ['name' => 'Noakhali',       'name_bn' => 'নোয়াখালী',    'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 22.8696,  'longitude' => 91.0993],
            ['name' => 'Lakshmipur',     'name_bn' => 'লক্ষ্মীপুর',  'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 22.9423,  'longitude' => 90.8414],
            ['name' => 'Chandpur',       'name_bn' => 'চাঁদপুর',      'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 23.2332,  'longitude' => 90.6715],
            ['name' => 'Brahmanbaria',   'name_bn' => 'ব্রাহ্মণবাড়িয়া','division' => 'Chittagong','division_bn' => 'চট্টগ্রাম', 'latitude' => 23.9570,  'longitude' => 91.1115],
            ['name' => 'Khagrachhari',   'name_bn' => 'খাগড়াছড়ি',   'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 23.1193,  'longitude' => 91.9847],
            ['name' => 'Rangamati',      'name_bn' => 'রাঙ্গামাটি',   'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 22.7324,  'longitude' => 92.2985],
            ['name' => 'Bandarban',      'name_bn' => 'বান্দরবান',    'division' => 'Chittagong','division_bn' => 'চট্টগ্রাম',  'latitude' => 22.1953,  'longitude' => 92.2184],

            // Rajshahi Division
            ['name' => 'Rajshahi',       'name_bn' => 'রাজশাহী',      'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.3745,  'longitude' => 88.6042],
            ['name' => 'Bogura',         'name_bn' => 'বগুড়া',        'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.8465,  'longitude' => 89.3773],
            ['name' => 'Chapainawabganj','name_bn' => 'চাঁপাইনবাবগঞ্জ','division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.5965,  'longitude' => 88.2761],
            ['name' => 'Joypurhat',      'name_bn' => 'জয়পুরহাট',    'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 25.0973,  'longitude' => 89.0188],
            ['name' => 'Naogaon',        'name_bn' => 'নওগাঁ',         'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.7936,  'longitude' => 88.9312],
            ['name' => 'Natore',         'name_bn' => 'নাটোর',         'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.4203,  'longitude' => 88.9878],
            ['name' => 'Pabna',          'name_bn' => 'পাবনা',         'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.0064,  'longitude' => 89.2372],
            ['name' => 'Sirajganj',      'name_bn' => 'সিরাজগঞ্জ',    'division' => 'Rajshahi',  'division_bn' => 'রাজশাহী',    'latitude' => 24.4533,  'longitude' => 89.7006],

            // Khulna Division
            ['name' => 'Khulna',         'name_bn' => 'খুলনা',         'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 22.8456,  'longitude' => 89.5403],
            ['name' => 'Bagerhat',       'name_bn' => 'বাগেরহাট',      'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 22.6602,  'longitude' => 89.7854],
            ['name' => 'Chuadanga',      'name_bn' => 'চুয়াডাঙ্গা',   'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.6401,  'longitude' => 88.8415],
            ['name' => 'Jessore',        'name_bn' => 'যশোর',           'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.1667,  'longitude' => 89.2167],
            ['name' => 'Jhenaidah',      'name_bn' => 'ঝিনাইদহ',      'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.5447,  'longitude' => 89.1540],
            ['name' => 'Kushtia',        'name_bn' => 'কুষ্টিয়া',     'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.9010,  'longitude' => 89.1202],
            ['name' => 'Magura',         'name_bn' => 'মাগুরা',        'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.4876,  'longitude' => 89.4197],
            ['name' => 'Meherpur',       'name_bn' => 'মেহেরপুর',      'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.7622,  'longitude' => 88.6318],
            ['name' => 'Narail',         'name_bn' => 'নড়াইল',        'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 23.1724,  'longitude' => 89.5122],
            ['name' => 'Satkhira',       'name_bn' => 'সাতক্ষীরা',    'division' => 'Khulna',    'division_bn' => 'খুলনা',      'latitude' => 22.7185,  'longitude' => 89.0705],

            // Barishal Division
            ['name' => 'Barishal',       'name_bn' => 'বরিশাল',        'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.7010,  'longitude' => 90.3535],
            ['name' => 'Barguna',        'name_bn' => 'বরগুনা',        'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.1551,  'longitude' => 90.1120],
            ['name' => 'Bhola',          'name_bn' => 'ভোলা',           'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.6854,  'longitude' => 90.6482],
            ['name' => 'Jhalokati',      'name_bn' => 'ঝালকাঠি',      'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.6406,  'longitude' => 90.1987],
            ['name' => 'Patuakhali',     'name_bn' => 'পটুয়াখালী',   'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.3596,  'longitude' => 90.3298],
            ['name' => 'Pirojpur',       'name_bn' => 'পিরোজপুর',      'division' => 'Barishal',  'division_bn' => 'বরিশাল',     'latitude' => 22.5841,  'longitude' => 89.9758],

            // Sylhet Division
            ['name' => 'Sylhet',         'name_bn' => 'সিলেট',         'division' => 'Sylhet',    'division_bn' => 'সিলেট',      'latitude' => 24.8949,  'longitude' => 91.8687],
            ['name' => 'Habiganj',       'name_bn' => 'হবিগঞ্জ',      'division' => 'Sylhet',    'division_bn' => 'সিলেট',      'latitude' => 24.3745,  'longitude' => 91.4156],
            ['name' => 'Moulvibazar',    'name_bn' => 'মৌলভীবাজার',   'division' => 'Sylhet',    'division_bn' => 'সিলেট',      'latitude' => 24.4826,  'longitude' => 91.7774],
            ['name' => 'Sunamganj',      'name_bn' => 'সুনামগঞ্জ',    'division' => 'Sylhet',    'division_bn' => 'সিলেট',      'latitude' => 25.0658,  'longitude' => 91.3950],

            // Rangpur Division
            ['name' => 'Rangpur',        'name_bn' => 'রংপুর',         'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.7439,  'longitude' => 89.2752],
            ['name' => 'Dinajpur',       'name_bn' => 'দিনাজপুর',      'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.6279,  'longitude' => 88.6338],
            ['name' => 'Gaibandha',      'name_bn' => 'গাইবান্ধা',    'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.3288,  'longitude' => 89.5285],
            ['name' => 'Kurigram',       'name_bn' => 'কুড়িগ্রাম',   'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.8052,  'longitude' => 89.6369],
            ['name' => 'Lalmonirhat',    'name_bn' => 'লালমনিরহাট',   'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.9923,  'longitude' => 89.2847],
            ['name' => 'Nilphamari',     'name_bn' => 'নীলফামারী',    'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 25.9318,  'longitude' => 88.8563],
            ['name' => 'Panchagarh',     'name_bn' => 'পঞ্চগড়',      'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 26.3411,  'longitude' => 88.5541],
            ['name' => 'Thakurgaon',     'name_bn' => 'ঠাকুরগাঁও',   'division' => 'Rangpur',   'division_bn' => 'রংপুর',      'latitude' => 26.0336,  'longitude' => 88.4616],

            // Mymensingh Division
            ['name' => 'Mymensingh',     'name_bn' => 'ময়মনসিংহ',    'division' => 'Mymensingh','division_bn' => 'ময়মনসিংহ',   'latitude' => 24.7471,  'longitude' => 90.4203],
            ['name' => 'Jamalpur',       'name_bn' => 'জামালপুর',      'division' => 'Mymensingh','division_bn' => 'ময়মনসিংহ',   'latitude' => 24.9375,  'longitude' => 89.9370],
            ['name' => 'Netrokona',      'name_bn' => 'নেত্রকোণা',    'division' => 'Mymensingh','division_bn' => 'ময়মনসিংহ',   'latitude' => 24.8703,  'longitude' => 90.7279],
            ['name' => 'Sherpur',        'name_bn' => 'শেরপুর',        'division' => 'Mymensingh','division_bn' => 'ময়মনসিংহ',   'latitude' => 25.0204,  'longitude' => 90.0152],
        ];

        foreach ($districts as $district) {
            Zone::updateOrCreate(
                ['name' => $district['name']],
                array_merge($district, [
                    'status'        => 'green',
                    'score'         => 0,
                    'report_count'  => 0,
                    'verified_count'=> 0,
                    'ranger_count'  => 0,
                ])
            );
        }

        $this->command->info('✅ All 64 districts seeded as zones.');
    }
}