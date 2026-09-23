<?php

namespace Database\Seeders;

use App\Models\FeedbackAirport;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Models\FeedbackLocation;
use App\Models\FeedbackService;
use App\Models\FeedbackSubcategory;
use App\Models\FeedbackTerminal;
use App\Models\FeedbackZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeedbackMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $airport = FeedbackAirport::firstOrCreate(
            ['code' => 'NSCB'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Netaji Subhas Chandra Bose International Airport',
                'city' => 'Kolkata',
                'country' => 'India',
                'timezone' => 'Asia/Kolkata',
                'is_active' => true,
            ]
        );

        $t2 = FeedbackTerminal::firstOrCreate(
            ['airport_id' => $airport->id, 'code' => 'T2'],
            ['uuid' => (string) Str::uuid(), 'name' => 'Terminal 2', 'sort_order' => 2]
        );

        $departure = FeedbackZone::firstOrCreate(
            ['terminal_id' => $t2->id, 'code' => 'DEP'],
            ['uuid' => (string) Str::uuid(), 'name' => 'Departure', 'sort_order' => 1]
        );

        $arrival = FeedbackZone::firstOrCreate(
            ['terminal_id' => $t2->id, 'code' => 'ARR'],
            ['uuid' => (string) Str::uuid(), 'name' => 'Arrival', 'sort_order' => 2]
        );

        $services = [
            ['code' => 'SEC', 'name' => 'Security Screening'],
            ['code' => 'CHK', 'name' => 'Check-in'],
            ['code' => 'IMM', 'name' => 'Immigration'],
            ['code' => 'BAG', 'name' => 'Baggage'],
            ['code' => 'GATE', 'name' => 'Boarding Gate'],
            ['code' => 'WASH', 'name' => 'Washroom'],
            ['code' => 'FOOD', 'name' => 'Food & Beverage'],
            ['code' => 'PARK', 'name' => 'Parking'],
            ['code' => 'RETAIL', 'name' => 'Retail'],
            ['code' => 'STAFF', 'name' => 'Airport Staff'],
        ];

        foreach ($services as $s) {
            FeedbackService::firstOrCreate(
                ['code' => $s['code']],
                ['uuid' => (string) Str::uuid(), 'name' => $s['name'], 'is_active' => true]
            );
        }

        $securityService = FeedbackService::where('code', 'SEC')->first();

        FeedbackLocation::firstOrCreate(
            ['code' => 'T2-DEP-SEC-CP03'],
            [
                'uuid' => (string) Str::uuid(),
                'airport_id' => $airport->id,
                'terminal_id' => $t2->id,
                'zone_id' => $departure->id,
                'service_id' => $securityService->id,
                'name' => 'Security Screening',
                'checkpoint_label' => 'Checkpoint 03',
                'is_active' => true,
            ]
        );

        $departments = [
            ['code' => 'SEC', 'name' => 'Security Department'],
            ['code' => 'OPS', 'name' => 'Airport Operations'],
            ['code' => 'HSK', 'name' => 'Housekeeping'],
            ['code' => 'FNB', 'name' => 'Food & Beverage'],
            ['code' => 'MNT', 'name' => 'Maintenance'],
            ['code' => 'CS', 'name' => 'Customer Service'],
        ];

        foreach ($departments as $d) {
            FeedbackDepartment::firstOrCreate(
                ['code' => $d['code']],
                ['uuid' => (string) Str::uuid(), 'name' => $d['name'], 'is_active' => true]
            );
        }

        $categories = [
            ['code' => 'SEC', 'en' => 'Security Screening', 'hi' => 'सुरक्षा जांच', 'bn' => 'নিরাপত্তা স্ক্রিনিং', 'icon' => '🛡️', 'dept' => 'SEC'],
            ['code' => 'CHK', 'en' => 'Check-in', 'hi' => 'चेक-इन', 'bn' => 'চেক-ইন', 'icon' => '🧾', 'dept' => 'CS'],
            ['code' => 'IMM', 'en' => 'Immigration', 'hi' => 'आव्रजन', 'bn' => 'ইমিগ্রেশন', 'icon' => '🛂', 'dept' => 'OPS'],
            ['code' => 'GATE', 'en' => 'Boarding Gate', 'hi' => 'बोर्डिंग गेट', 'bn' => 'বোর্ডিং গেট', 'icon' => '🚪', 'dept' => 'OPS'],
            ['code' => 'BAG', 'en' => 'Baggage', 'hi' => 'सामान', 'bn' => 'ব্যাগেজ', 'icon' => '🧳', 'dept' => 'OPS'],
            ['code' => 'WASH', 'en' => 'Washroom', 'hi' => 'शौचालय', 'bn' => 'ওয়াশরুম', 'icon' => '🚻', 'dept' => 'HSK'],
            ['code' => 'PARK', 'en' => 'Parking', 'hi' => 'पार्किंग', 'bn' => 'পার্কিং', 'icon' => '🅿️', 'dept' => 'OPS'],
            ['code' => 'FOOD', 'en' => 'Food & Beverage', 'hi' => 'खाना और पेय', 'bn' => 'খাবার ও পানীয়', 'icon' => '🍽️', 'dept' => 'FNB'],
            ['code' => 'RETAIL', 'en' => 'Retail', 'hi' => 'खुदरा', 'bn' => 'খুচরা', 'icon' => '🛍️', 'dept' => 'CS'],
            ['code' => 'STAFF', 'en' => 'Airport Staff', 'hi' => 'हवाई अड्डा कर्मचारी', 'bn' => 'বিমানবন্দর কর্মী', 'icon' => '👥', 'dept' => 'CS'],
            ['code' => 'FAC', 'en' => 'Facilities', 'hi' => 'सुविधाएं', 'bn' => 'সুবিধা', 'icon' => '🏢', 'dept' => 'MNT'],
            ['code' => 'CLN', 'en' => 'Cleanliness', 'hi' => 'स्वच्छता', 'bn' => 'পরিচ্ছন্নতা', 'icon' => '🧹', 'dept' => 'HSK'],
            ['code' => 'OTHER', 'en' => 'Other', 'hi' => 'अन्य', 'bn' => 'অন্যান্য', 'icon' => '❓', 'dept' => 'CS'],
        ];

        foreach ($categories as $i => $c) {
            $dept = FeedbackDepartment::where('code', $c['dept'])->first();

            FeedbackCategory::firstOrCreate(
                ['code' => $c['code']],
                [
                    'uuid' => (string) Str::uuid(),
                    'name_en' => $c['en'],
                    'name_hi' => $c['hi'],
                    'name_bn' => $c['bn'],
                    'icon' => $c['icon'],
                    'default_department_id' => $dept?->id,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
        }

        $subcategories = [
            'SEC' => [
                ['code' => 'WAIT', 'en' => 'Waiting Time', 'hi' => 'प्रतीक्षा समय', 'bn' => 'অপেক্ষার সময়'],
                ['code' => 'QUEUE', 'en' => 'Queue Management', 'hi' => 'कतार प्रबंधन', 'bn' => 'সারি ব্যবস্থাপনা'],
                ['code' => 'STAFF', 'en' => 'Staff Behaviour', 'hi' => 'कर्मचारी व्यवहार', 'bn' => 'কর্মীর আচরণ'],
                ['code' => 'PROC', 'en' => 'Screening Process', 'hi' => 'जांच प्रक्रिया', 'bn' => 'স্ক্রিনিং প্রক্রিয়া'],
                ['code' => 'INFO', 'en' => 'Information', 'hi' => 'जानकारी', 'bn' => 'তথ্য'],
                ['code' => 'INFRA', 'en' => 'Infrastructure', 'hi' => 'बुनियादी ढांचा', 'bn' => 'অবকাঠামো'],
                ['code' => 'OTHER', 'en' => 'Other', 'hi' => 'अन्य', 'bn' => 'অন্যান্য'],
            ],
            'WASH' => [
                ['code' => 'CLN', 'en' => 'Cleanliness', 'hi' => 'स्वच्छता', 'bn' => 'পরিচ্ছন্নতা'],
                ['code' => 'MNT', 'en' => 'Maintenance', 'hi' => 'रखरखाव', 'bn' => 'রক্ষণাবেক্ষণ'],
                ['code' => 'WATER', 'en' => 'Water Supply', 'hi' => 'पानी की आपूर्ति', 'bn' => 'জল সরবরাহ'],
                ['code' => 'AVAIL', 'en' => 'Availability', 'hi' => 'उपलब्धता', 'bn' => 'উপলব্ধতা'],
                ['code' => 'HYG', 'en' => 'Hygiene', 'hi' => 'स्वच्छता', 'bn' => 'স্বাস্থ্যবিধি'],
                ['code' => 'OTHER', 'en' => 'Other', 'hi' => 'अन्य', 'bn' => 'অন্যান্য'],
            ],
            'BAG' => [
                ['code' => 'DELAY', 'en' => 'Delay', 'hi' => 'देरी', 'bn' => 'বিলম্ব'],
                ['code' => 'MISSING', 'en' => 'Missing Baggage', 'hi' => 'खोया सामान', 'bn' => 'হারানো ব্যাগেজ'],
                ['code' => 'DAMAGE', 'en' => 'Damaged Baggage', 'hi' => 'क्षतिग्रस्त सामान', 'bn' => 'ক্ষতিগ্রস্ত ব্যাগেজ'],
                ['code' => 'CONV', 'en' => 'Conveyor', 'hi' => 'कन्वेयर', 'bn' => 'কনভেয়র'],
                ['code' => 'STAFF', 'en' => 'Staff Behaviour', 'hi' => 'कर्मचारी व्यवहार', 'bn' => 'কর্মীর আচরণ'],
                ['code' => 'OTHER', 'en' => 'Other', 'hi' => 'अन्य', 'bn' => 'অন্যান্য'],
            ],
        ];

        foreach ($subcategories as $catCode => $items) {
            $cat = FeedbackCategory::where('code', $catCode)->first();
            if (! $cat) {
                continue;
            }
            foreach ($items as $i => $sub) {
                FeedbackSubcategory::firstOrCreate(
                    ['category_id' => $cat->id, 'code' => $sub['code']],
                    [
                        'uuid' => (string) Str::uuid(),
                        'name_en' => $sub['en'],
                        'name_hi' => $sub['hi'],
                        'name_bn' => $sub['bn'],
                        'default_department_id' => $cat->default_department_id,
                        'sort_order' => $i,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
