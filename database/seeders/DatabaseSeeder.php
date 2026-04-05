<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CulturalLevel;
use App\Models\DocumentRequirement;
use App\Models\PortalContent;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminUsers = [];

        if (app()->environment(['local', 'testing'])) {
            $adminUsers = [
                [
                    'email' => 'seavminhcoding@gmail.com',
                    'name' => 'System Administrator',
                    'password' => 'Seavminh1233',
                ],
                [
                    'email' => 'geography.training.center@system.local',
                    'name' => 'Geography Training Center',
                    'password' => '99996666',
                ],
            ];
        }

        $productionAdminEmail = env('ADMIN_EMAIL');
        $productionAdminPassword = env('ADMIN_PASSWORD');

        if ($productionAdminEmail && $productionAdminPassword) {
            $adminUsers[] = [
                'email' => $productionAdminEmail,
                'name' => env('ADMIN_NAME', 'System Administrator'),
                'password' => $productionAdminPassword,
            ];
        }

        foreach ($adminUsers as $adminUser) {
            User::query()->updateOrCreate(
                ['email' => $adminUser['email']],
                [
                    'name' => $adminUser['name'],
                    'password' => $adminUser['password'],
                    'is_admin' => true,
                ],
            );
        }

        foreach ([
            ['name_kh' => 'ពលបាល', 'name_en' => 'Sergeant', 'sort_order' => 1],
            ['name_kh' => 'អនុសេនីយ៍ទោ', 'name_en' => 'Second Lieutenant', 'sort_order' => 2],
            ['name_kh' => 'អនុសេនីយ៍ឯក', 'name_en' => 'First Lieutenant', 'sort_order' => 3],
            ['name_kh' => 'វរសេនីយ៍ទោ', 'name_en' => 'Lieutenant Colonel', 'sort_order' => 4],
            ['name_kh' => 'វរសេនីយ៍ឯក', 'name_en' => 'Colonel', 'sort_order' => 5],
        ] as $rank) {
            Rank::query()->updateOrCreate(
                ['name_en' => $rank['name_en']],
                [...$rank, 'is_active' => true],
            );
        }

        foreach ([
            [
                'name' => 'Military Leadership Course',
                'description' => 'Leadership, planning, and command decision training for mid-level officers.',
                'duration' => '6 Months',
            ],
            [
                'name' => 'Staff Operations Course',
                'description' => 'Operational planning, coordination, and reporting for command staff roles.',
                'duration' => '4 Months',
            ],
            [
                'name' => 'Military Administration Course',
                'description' => 'Administrative procedures, personnel management, and documentation workflow.',
                'duration' => '3 Months',
            ],
        ] as $course) {
            Course::query()->updateOrCreate(
                ['name' => $course['name']],
                [...$course, 'is_active' => true],
            );
        }

        foreach ([
            ['name' => 'High School', 'sort_order' => 1],
            ['name' => 'Associate Degree', 'sort_order' => 2],
            ['name' => 'Bachelor Degree', 'sort_order' => 3],
            ['name' => 'Master Degree', 'sort_order' => 4],
        ] as $level) {
            CulturalLevel::query()->updateOrCreate(
                ['name' => $level['name']],
                [...$level, 'is_active' => true],
            );
        }

        foreach ([
            [
                'name_kh' => 'ប្រវត្តិរូបសង្ខេប',
                'name_en' => 'Brief biography',
                'slug' => 'brief-biography',
                'sort_order' => 1,
                'is_protected' => false,
            ],
            [
                'name_kh' => 'ប្រកាសឋានន្តរស័ក្តិ',
                'name_en' => 'Declaring a title of merit',
                'slug' => 'title-of-merit',
                'sort_order' => 2,
                'is_protected' => false,
            ],
            [
                'name_kh' => 'បញ្ជីត្រៀមចាត់បញ្ជូន',
                'name_en' => 'Transfer list',
                'slug' => 'transfer-list',
                'sort_order' => 3,
                'is_protected' => false,
            ],
            [
                'name_kh' => 'អត្តសញ្ញាណប័ណ្ណយោធា/ស៊ីវិល',
                'name_en' => 'Military/civilian ID card',
                'slug' => 'military-civilian-id-card',
                'sort_order' => 4,
                'is_protected' => false,
            ],
            [
                'name_kh' => 'រូបថតយោធា (គ្រប់ទំហំ)',
                'name_en' => 'Military photos (all sizes)',
                'slug' => 'military-photos',
                'sort_order' => 5,
                'is_protected' => false,
            ],
            [
                'name_kh' => 'បញ្ញីគល់ទទឹង',
                'name_en' => 'Broad-leaved tree',
                'slug' => DocumentRequirement::PROTECTED_TELEGRAM_REQUIREMENT_SLUG,
                'sort_order' => 6,
                'is_protected' => true,
            ],
        ] as $requirement) {
            DocumentRequirement::query()->updateOrCreate(
                ['slug' => $requirement['slug']],
                [...$requirement, 'is_active' => true],
            );
        }

        PortalContent::query()->updateOrCreate(
            ['id' => 1],
            [
                'badge' => 'Military Academy Intake Portal',
                'title' => 'Military Course Registration Form',
                'description' => 'Please fill in the following information to apply for the training course.',
                'feature_one_title' => 'Admin-managed courses',
                'feature_one_description' => 'Ranks, courses, and cultural levels stay synchronized with the dashboard.',
                'feature_two_title' => 'Secure document intake',
                'feature_two_description' => 'Required applicant documents are validated before submission.',
                'feature_three_title' => 'Professional review',
                'feature_three_description' => 'Applications move directly into the admin table for follow-up.',
                'course_page_title' => 'សាលាហ្វឹកហ្វឺនយោធា',
                'course_page_subtitle' => 'ប្រព័ន្ធចុះឈ្មោះសិក្សាវគ្គយោធា',
                'course_page_description' => 'សូមបំពេញព័ត៌មានឲ្យបានត្រឹមត្រូវ និងងាយស្រួលត្រួតពិនិត្យ។',
                'test_taking_staff_page_title' => 'សាលាហ្វឹកហ្វឺនយោធា',
                'test_taking_staff_page_subtitle' => 'ទម្រង់ចុះឈ្មោះបុគ្គលិកសាកល្បង',
                'test_taking_staff_page_description' => 'សូមបំពេញព័ត៌មានរបស់បុគ្គលិកសាកល្បងឲ្យបានត្រឹមត្រូវ ដើម្បីឲ្យក្រុមការងារត្រួតពិនិត្យបានងាយស្រួល។',
            ],
        );
    }
}
