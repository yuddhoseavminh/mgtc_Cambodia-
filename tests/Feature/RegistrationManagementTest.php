<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CulturalLevel;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\DocumentRequirement;
use App\Models\PortalContent;
use App\Models\Rank;
use App\Models\TestTakingStaffDocumentRequirement;
use App\Models\TestTakingStaffRank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function csrfToken(): string
    {
        return 'test-csrf-token';
    }

    private function loginAsAdmin(): void
    {
        $this->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->postJson('/admin/login', [
                '_token' => $this->csrfToken(),
                'email' => 'seavminhcoding@gmail.com',
                'password' => 'Seavminh1233',
            ])
            ->assertOk();
    }

    public function test_public_registration_page_is_available(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Personal Information');
    }

    public function test_public_form_options_are_available(): void
    {
        $response = $this->getJson('/form-options');

        $response->assertOk()
            ->assertJsonStructure([
                'ranks',
                'courses',
                'cultural_levels',
                'document_requirements',
                'portal_content',
                'provinces',
                'family_situations',
            ]);
    }

    public function test_applicant_can_submit_registration(): void
    {
        Storage::fake('local');

        $rankId = Rank::query()->value('id');
        $courseId = Course::query()->value('id');
        $culturalLevelId = CulturalLevel::query()->value('id');
        $documentRequirements = DocumentRequirement::query()->ordered()->get();

        $documentStatuses = [];
        $documentFiles = [];

        foreach ($documentRequirements as $index => $documentRequirement) {
            $documentStatuses[$documentRequirement->id] = $index < 2 ? 'have' : 'dont_have';

            if ($index < 2) {
                $documentFiles[$documentRequirement->id] = [
                    UploadedFile::fake()->create(
                        $documentRequirement->slug.'-1.pdf',
                        100,
                        'application/pdf',
                    ),
                ];
            }

            if ($index === 0) {
                $documentFiles[$documentRequirement->id][] = UploadedFile::fake()->create(
                    $documentRequirement->slug.'-2.pdf',
                    120,
                    'application/pdf',
                );
            }
        }

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/applications', [
                '_token' => $this->csrfToken(),
                'khmer_name' => 'សា វ៉ាមិញ',
                'latin_name' => 'Sea Vaminh',
                'id_number' => 'A-1001',
                'rank_id' => $rankId,
                'date_of_birth' => '1994-08-10',
                'date_of_enlistment' => '2015-03-01',
                'position' => 'Operations Officer',
                'unit' => 'Royal Army Training Command',
                'course_id' => $courseId,
                'cultural_level_id' => $culturalLevelId,
                'place_of_birth' => 'Phnom Penh',
                'current_address' => '123 Military Campus Road, Phnom Penh',
                'family_situation' => 'Single',
                'phone_number' => '012345678',
                'document_statuses' => $documentStatuses,
                'document_files' => $documentFiles,
            ]);

        $response->assertCreated()
            ->assertJson([
                'message' => 'អ្នកបានចុះឈ្មោះដោយជោគជ័យ សំណាងល្អ ជួបគ្នាឆាប់ៗ។',
            ]);

        $this->assertDatabaseHas('applications', [
            'latin_name' => 'Sea Vaminh',
            'phone_number' => '012345678',
            'status' => 'Pending',
        ]);

        $application = Application::query()->where('latin_name', 'Sea Vaminh')->first();

        $this->assertNotNull($application);
        $this->assertSame(6, $application->applicationDocuments()->count());
        $this->assertSame(3, $application->applicationDocuments()->whereNotNull('file_path')->count());
    }

    public function test_test_taking_staff_registration_sends_telegram_notification_when_enabled(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        Config::set('services.telegram.enabled', true);
        Config::set('services.telegram.bot_token', 'telegram-test-token');
        Config::set('services.telegram.chat_id', 'telegram-test-chat');

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'នាយទាហានសាកល្បង',
            'name_en' => 'Test Staff Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $documentRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'ឯកសារសាកល្បង',
            'name_en' => 'Test Document',
            'slug' => 'test-document',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'អ្នកសាកល្បង',
                'name_latin' => 'Tester Latin',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'document_files' => [
                    $documentRequirement->id => UploadedFile::fake()->create('test-document.pdf', 120, 'application/pdf'),
                ],
            ]);

        $response->assertCreated();

        Http::assertSent(function (ClientRequest $request) {
            return $request->url() === 'https://api.telegram.org/bottelegram-test-token/sendMessage'
                && $request['chat_id'] === 'telegram-test-chat'
                && str_contains($request['text'], 'Tester Latin')
                && str_contains($request['text'], 'អ្នកសាកល្បង');
        });
    }

    public function test_test_taking_staff_registration_returns_success_page_without_redirect(): void
    {
        Storage::fake('local');

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'អ្នកសាកល្បង',
            'name_en' => 'Test Staff Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'បុគ្គលិកសាកល្បង',
                'name_latin' => 'Tester Latin',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
            ]);

        $response->assertCreated()
            ->assertSee('ព័ត៌មានរបស់អ្នកត្រូវបានបញ្ជូនរួចរាល់។ សូមរង់ចាំការត្រួតពិនិត្យពីក្រុមការងារ។');
    }

    public function test_admin_login_page_is_available(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk()
            ->assertSee('Admin Login');
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $loginResponse = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->postJson('/admin/login', [
                '_token' => $this->csrfToken(),
                'email' => 'seavminhcoding@gmail.com',
                'password' => 'Seavminh1233',
            ]);

        $loginResponse->assertOk()
            ->assertJsonPath('user.email', 'seavminhcoding@gmail.com');

        $dashboardResponse = $this->getJson('/admin/dashboard');

        $dashboardResponse->assertOk()
            ->assertJsonStructure([
                'stats' => [
                    'total_applicants',
                    'total_courses',
                    'total_ranks',
                    'pending_applications',
                ],
                'applications_per_month',
                'recent_applications',
            ]);
    }

    public function test_admin_can_update_portal_cover_content(): void
    {
        $this->loginAsAdmin();

        $response = $this
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->putJson('/admin/portal-content', [
                '_token' => $this->csrfToken(),
                'badge' => 'Updated Badge',
                'title' => 'Updated Cover Title',
                'description' => 'Updated cover description.',
                'feature_one_title' => 'Card One',
                'feature_one_description' => 'Card one description.',
                'feature_two_title' => 'Card Two',
                'feature_two_description' => 'Card two description.',
                'feature_three_title' => 'Card Three',
                'feature_three_description' => 'Card three description.',
            ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Updated Cover Title');

        $this->assertDatabaseHas('portal_contents', [
            'title' => 'Updated Cover Title',
            'badge' => 'Updated Badge',
        ]);
    }

    public function test_admin_can_upload_portal_banner_image(): void
    {
        Storage::fake('local');

        $this->loginAsAdmin();

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->post('/admin/portal-content', [
                '_token' => $this->csrfToken(),
                '_method' => 'PUT',
                'badge' => 'Updated Badge',
                'title' => 'Updated Cover Title',
                'description' => 'Updated cover description.',
                'feature_one_title' => 'Card One',
                'feature_one_description' => 'Card one description.',
                'feature_two_title' => 'Card Two',
                'feature_two_description' => 'Card two description.',
                'feature_three_title' => 'Card Three',
                'feature_three_description' => 'Card three description.',
                'banner_image' => UploadedFile::fake()->image('portal-banner.png', 1200, 300),
            ]);

        $response->assertOk();

        $content = PortalContent::query()->first();

        $this->assertNotNull($content?->banner_image_path);
        $this->assertTrue(Storage::disk('local')->exists($content->banner_image_path));
    }

    public function test_admin_can_open_course_create_form(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/courses/create');

        $response->assertOk()
            ->assertSee('Create Course')
            ->assertSee('Back to Course List');
    }

    public function test_admin_can_open_rank_create_form(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/ranks/create');

        $response->assertOk()
            ->assertSee('Create Rank')
            ->assertSee('Back to Rank List');
    }

    public function test_admin_can_open_cultural_level_create_form(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/cultural-levels/create');

        $response->assertOk()
            ->assertSee('Create Cultural Level')
            ->assertSee('Back to Cultural Level List');
    }

    public function test_admin_can_open_document_requirement_create_form(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/document-requirements/create');

        $response->assertOk()
            ->assertSee('Create Document Requirement')
            ->assertSee('Back to Document List');
    }

    public function test_admin_can_open_reports_page(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin?section=reports');

        $response->assertOk()
            ->assertSee('Reports')
            ->assertSee('Registrations Per Month');
    }

    public function test_admin_application_details_json_includes_matching_document_ids_and_urls(): void
    {
        Storage::fake('local');

        $this->loginAsAdmin();

        $application = Application::query()->create([
            'khmer_name' => 'Applicant Khmer',
            'latin_name' => 'Applicant Latin',
            'id_number' => 'APP-2001',
            'rank_id' => Rank::query()->value('id'),
            'date_of_birth' => '1990-01-01',
            'date_of_enlistment' => '2010-01-01',
            'position' => 'Officer',
            'unit' => 'Unit A',
            'course_id' => Course::query()->value('id'),
            'cultural_level_id' => CulturalLevel::query()->value('id'),
            'place_of_birth' => 'Phnom Penh',
            'current_address' => 'Phnom Penh',
            'family_situation' => 'Single',
            'phone_number' => '012345678',
            'status' => 'Pending',
            'submitted_at' => now(),
        ]);

        $documentRequirement = DocumentRequirement::query()->where('slug', 'military-photos')->firstOrFail();
        $documentPath = 'applications/test/photo-one.jpg';

        Storage::disk('local')->put($documentPath, 'file-content');

        $document = ApplicationDocument::query()->create([
            'application_id' => $application->id,
            'document_requirement_id' => $documentRequirement->id,
            'status' => ApplicationDocument::STATUS_HAVE,
            'file_path' => $documentPath,
            'original_name' => 'photo-one.jpg',
        ]);

        $response = $this->getJson("/admin/applications/{$application->id}");

        $response->assertOk()
            ->assertJsonPath('documents.0.id', $document->id)
            ->assertJsonPath('documents.0.view_url', url("/admin/applications/{$application->id}/documents/{$document->id}"))
            ->assertJsonPath('documents.0.download_url', url("/admin/applications/{$application->id}/documents/{$document->id}/download"));
    }
}
