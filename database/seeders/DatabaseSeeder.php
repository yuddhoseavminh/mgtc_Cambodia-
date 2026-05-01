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

        if (app()->environment(['local', 'testing' , 'production'])) {
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
                [
                    'email' => 'system.owner@gmail.com',
                    'name' => 'System Owner',
                    'password' => 'SystemOwner@123!',
                ],
            ];
        }

        $systemOwnerEmail = env('SYSTEM_OWNER_EMAIL');
        $systemOwnerPassword = env('SYSTEM_OWNER_PASSWORD');

        if ($systemOwnerEmail && $systemOwnerPassword) {
            $adminUsers[] = [
                'email' => $systemOwnerEmail,
                'name' => env('SYSTEM_OWNER_NAME', 'System Owner'),
                'password' => $systemOwnerPassword,
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

        foreach (collect($adminUsers)->unique('email')->values() as $adminUser) {
            User::query()->updateOrCreate(
                ['email' => $adminUser['email']],
                [
                    'name' => $adminUser['name'],
                    'password' => $adminUser['password'],
                    'is_admin' => true,
                    'role' => 'Management',
                ],
            );
        }

        Rank::query()->updateOrCreate(
            ['name_en' => 'General Staff'],
            [
                'name_kh' => 'ឧត្តមសេនីយ៍',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Course::query()->updateOrCreate(
            ['name' => 'វគ្គសិក្សា'],
            [
                'description' => 'Default course used by the public registration form.',
                'duration' => '3 months',
                'is_active' => true,
                'is_protected' => false,
            ],
        );

        CulturalLevel::query()->updateOrCreate(
            ['name' => 'Bachelor'],
            [
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $documentRequirements = [
            [
                'slug' => DocumentRequirement::PROTECTED_TELEGRAM_REQUIREMENT_SLUG,
                'name_kh' => 'បញ្ជីគល់ទទឹង',
                'name_en' => 'Broad-leaved tree',
                'sort_order' => 1,
                'is_protected' => true,
            ],
            [
                'slug' => 'military-photos',
                'name_kh' => 'រូបថតយោធា',
                'name_en' => 'Military Photos',
                'sort_order' => 2,
                'is_protected' => false,
            ],
            [
                'slug' => 'identity-card',
                'name_kh' => 'អត្តសញ្ញាណប័ណ្ណ',
                'name_en' => 'Identity Card',
                'sort_order' => 3,
                'is_protected' => false,
            ],
            [
                'slug' => 'family-book',
                'name_kh' => 'សៀវភៅគ្រួសារ',
                'name_en' => 'Family Book',
                'sort_order' => 4,
                'is_protected' => false,
            ],
            [
                'slug' => 'certificate',
                'name_kh' => 'សញ្ញាបត្រ',
                'name_en' => 'Certificate',
                'sort_order' => 5,
                'is_protected' => false,
            ],
        ];

        foreach ($documentRequirements as $documentRequirement) {
            DocumentRequirement::query()->updateOrCreate(
                ['slug' => $documentRequirement['slug']],
                [
                    'name_kh' => $documentRequirement['name_kh'],
                    'name_en' => $documentRequirement['name_en'],
                    'sort_order' => $documentRequirement['sort_order'],
                    'is_active' => true,
                    'is_protected' => $documentRequirement['is_protected'],
                ],
            );
        }

        $portalContent = PortalContent::query()->first();
        $portalContentPayload = [
            'badge' => 'Military Training Registration',
            'title' => 'Military Course Registration',
            'description' => 'Submit and manage military course registration information.',
            'feature_one_title' => 'Applicant Information',
            'feature_one_description' => 'Collect identity and service details.',
            'feature_two_title' => 'Document Uploads',
            'feature_two_description' => 'Attach required registration documents.',
            'feature_three_title' => 'Review Workflow',
            'feature_three_description' => 'Track approval status from the admin dashboard.',
            'course_page_title' => 'Course Registration',
            'course_page_subtitle' => 'Training Course',
            'course_page_description' => 'Register for the selected training course.',
            'test_taking_staff_page_title' => 'Test Taking Staff Registration',
            'test_taking_staff_page_subtitle' => 'Staff Registration',
            'test_taking_staff_page_description' => 'Register test taking staff information.',
            'staff_title' => 'Staff Portal',
            'staff_subtitle' => 'Manage private staff documents.',
        ];

        if ($portalContent) {
            $portalContent->update($portalContentPayload);
        } else {
            PortalContent::query()->create($portalContentPayload);
        }

    }
}
