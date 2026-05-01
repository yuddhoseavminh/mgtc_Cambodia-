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
use App\Models\TestTakingStaffRegistration;
use App\Models\TeamStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
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

    private function createStaffAccount(array $attributes = []): TeamStaff
    {
        Storage::fake('local');

        $avatarPath = 'team-staff/avatar-test.jpg';
        Storage::disk('local')->put($avatarPath, 'avatar');

        return TeamStaff::query()->create(array_merge([
            'sequence_no' => 1,
            'military_rank' => 'Captain',
            'name_kh' => 'Sok Dara KH',
            'name_latin' => 'Sok Dara',
            'id_number' => '058256',
            'username' => 'Sok Dara',
            'password' => '058256',
            'avatar_path' => $avatarPath,
            'avatar_original_name' => 'avatar-test.jpg',
            'gender' => 'Male',
            'position' => 'Operations Officer',
            'role' => 'Staff',
            'phone_number' => '012345678',
            'documents' => [],
            'is_active' => true,
            'must_change_password' => true,
        ], $attributes));
    }

    private function createTestTakingStaffRegistration(array $attributes = []): TestTakingStaffRegistration
    {
        $rankId = $attributes['test_taking_staff_rank_id']
            ?? TestTakingStaffRank::query()->value('id')
            ?? TestTakingStaffRank::query()->create([
                'name_kh' => 'Seed Rank KH',
                'name_en' => 'Seed Rank',
                'sort_order' => 1,
                'is_active' => true,
            ])->id;

        return TestTakingStaffRegistration::query()->create(array_merge([
            'test_taking_staff_rank_id' => $rankId,
            'name_kh' => 'Delete Target KH',
            'name_latin' => 'Delete Target',
            'id_number' => 'DEL-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'date_of_birth' => '1990-01-01',
            'military_service_day' => '2010-01-01',
            'phone_number' => '012345678',
            'avatar_path' => 'test-taking-staff/avatars/missing-avatar.jpg',
            'avatar_original_name' => 'missing-avatar.jpg',
            'submitted_at' => now(),
        ], $attributes));
    }

    private function createLocalFileWithSize(string $relativePath, int $sizeBytes): void
    {
        $absolutePath = Storage::disk('local')->path($relativePath);
        $directory = dirname($absolutePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $handle = fopen($absolutePath, 'wb');

        if ($handle === false) {
            $this->fail('Unable to create file: '.$relativePath);
        }

        if ($sizeBytes > 0) {
            fseek($handle, $sizeBytes - 1);
            fwrite($handle, "\0");
        }

        fclose($handle);
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
                'khmer_name' => 'Applicant KH',
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

        $response->assertCreated();

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

    public function test_applicant_registration_rejects_document_file_larger_than_15_mb(): void
    {
        Storage::fake('local');

        $rankId = Rank::query()->value('id');
        $courseId = Course::query()->value('id');
        $culturalLevelId = CulturalLevel::query()->value('id');
        $documentRequirements = DocumentRequirement::query()->ordered()->get();
        $firstRequirement = $documentRequirements->firstOrFail();

        $documentStatuses = [];
        foreach ($documentRequirements as $documentRequirement) {
            $documentStatuses[$documentRequirement->id] = $documentRequirement->id === $firstRequirement->id ? 'have' : 'dont_have';
        }

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/applications', [
                '_token' => $this->csrfToken(),
                'khmer_name' => 'Oversize KH',
                'latin_name' => 'Oversize Latin',
                'id_number' => 'A-15001',
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
                'document_files' => [
                    $firstRequirement->id => [
                        UploadedFile::fake()->create('oversize-file.pdf', 15361, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertUnprocessable();

        $errorKeys = array_keys((array) $response->json('errors', []));
        $hasOversizeError = false;

        foreach ($errorKeys as $errorKey) {
            if (str_starts_with((string) $errorKey, "document_files.{$firstRequirement->id}.")) {
                $hasOversizeError = true;
                break;
            }
        }

        $this->assertTrue($hasOversizeError, 'Expected a per-file validation error for oversized uploads.');
    }

    public function test_applicant_registration_rejects_total_document_uploads_over_50_mb(): void
    {
        Storage::fake('local');

        $rankId = Rank::query()->value('id');
        $courseId = Course::query()->value('id');
        $culturalLevelId = CulturalLevel::query()->value('id');
        $documentRequirements = DocumentRequirement::query()->ordered()->get();
        $firstRequirement = $documentRequirements->firstOrFail();

        $documentStatuses = [];
        foreach ($documentRequirements as $documentRequirement) {
            $documentStatuses[$documentRequirement->id] = $documentRequirement->id === $firstRequirement->id ? 'have' : 'dont_have';
        }

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/applications', [
                '_token' => $this->csrfToken(),
                'khmer_name' => 'Total Limit KH',
                'latin_name' => 'Total Limit Latin',
                'id_number' => 'A-50001',
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
                'document_files' => [
                    $firstRequirement->id => [
                        UploadedFile::fake()->create('doc-1.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-2.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-3.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-4.pdf', 15360, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('upload_total');
    }

    public function test_public_registration_sends_all_uploaded_documents_from_marked_types_to_telegram(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        Config::set('services.telegram.enabled', true);
        Config::set('services.telegram.bot_token', 'telegram-test-token');
        Config::set('services.telegram.chat_id', 'telegram-test-chat');
        Config::set('services.telegram.message_thread_id', 12345);

        DocumentRequirement::query()->update(['is_protected' => false]);

        $rankId = Rank::query()->create([
            'name_kh' => 'Auto Rank KH',
            'name_en' => 'Auto Rank',
            'sort_order' => 1,
            'is_active' => true,
        ])->id;

        $courseId = Course::query()->create([
            'name' => 'Auto Course',
            'description' => 'Auto course for Telegram test.',
            'duration' => '3 months',
            'is_active' => true,
        ])->id;

        $culturalLevelId = CulturalLevel::query()->create([
            'name' => 'Auto Cultural Level',
            'sort_order' => 1,
            'is_active' => true,
        ])->id;

        $documentRequirements = DocumentRequirement::query()->ordered()->get();

        if ($documentRequirements->isEmpty()) {
            $documentRequirements = collect([
                DocumentRequirement::query()->create([
                    'name_kh' => 'Auto Telegram Document KH',
                    'name_en' => 'Auto Telegram Document',
                    'slug' => 'auto-telegram-document',
                    'sort_order' => 1,
                    'is_active' => true,
                    'is_protected' => true,
                ]),
            ]);
        }

        $documentRequirements->each(fn (DocumentRequirement $requirement) => $requirement->forceFill(['is_protected' => true])->save());
        $documentRequirements = DocumentRequirement::query()->ordered()->get();

        $documentStatuses = [];
        $documentFiles = [];
        $expectedTelegramDocumentCount = 0;

        foreach ($documentRequirements as $index => $documentRequirement) {
            $documentStatuses[$documentRequirement->id] = 'have';

            if ($index === 0) {
                $documentFiles[$documentRequirement->id] = [
                    UploadedFile::fake()->create(
                        $documentRequirement->slug.'-auto.pdf',
                        100,
                        'application/pdf',
                    ),
                    UploadedFile::fake()->create(
                        $documentRequirement->slug.'-extra.pdf',
                        120,
                        'application/pdf',
                    ),
                ];

                $expectedTelegramDocumentCount += 2;
                continue;
            }

            $documentFiles[$documentRequirement->id] = [
                UploadedFile::fake()->create(
                    $documentRequirement->slug.'-telegram.pdf',
                    130,
                    'application/pdf',
                ),
            ];
            $expectedTelegramDocumentCount++;
        }

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/applications', [
                '_token' => $this->csrfToken(),
                'khmer_name' => 'Auto Telegram KH',
                'latin_name' => 'Auto Telegram Latin',
                'id_number' => 'AUTO-1001',
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

        $response->assertCreated();

        Http::assertSentCount(1);
        Http::assertSent(function ($request) use ($expectedTelegramDocumentCount) {
            $data = $request->data();
            $mediaJson = isset($data['media']) && is_string($data['media'])
                ? $data['media']
                : null;

            if (! $mediaJson) {
                $mediaPart = collect($data)->first(fn ($part) => ($part['name'] ?? null) === 'media');
                $mediaJson = is_array($mediaPart) ? ($mediaPart['contents'] ?? null) : null;
            }

            $mediaPayload = is_string($mediaJson) ? json_decode($mediaJson, true) : null;

            return str_contains($request->url(), '/sendMediaGroup')
                && is_array($mediaPayload)
                && count($mediaPayload) === $expectedTelegramDocumentCount
                && (($mediaPayload[0]['type'] ?? null) === 'document')
                && (
                    (isset($data['message_thread_id']) && (int) $data['message_thread_id'] === 12345)
                    || collect($data)->contains(fn ($part) => ($part['name'] ?? null) === 'message_thread_id'
                        && (int) ($part['contents'] ?? 0) === 12345)
                );
        });
    }

    public function test_test_taking_staff_registration_sends_uploaded_files_to_telegram_with_caption(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        Config::set('services.telegram.enabled', true);
        Config::set('services.telegram.bot_token', 'telegram-test-token');
        Config::set('services.telegram.chat_id', 'telegram-test-chat');
        Config::set('services.telegram.message_thread_id', 12345);

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'Test Staff Rank KH',
            'name_en' => 'Test Staff Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $documentRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'Test Document KH',
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
                'name_kh' => 'No Telegram Tester KH',
                'name_latin' => 'No Telegram Tester',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'document_files' => [
                    $documentRequirement->id => [
                        UploadedFile::fake()->create('test-document.pdf', 120, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertCreated();
        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            $data = $request->data();
            $mediaJson = isset($data['media']) && is_string($data['media'])
                ? $data['media']
                : null;

            if (! $mediaJson) {
                $mediaPart = collect($data)->first(fn ($part) => ($part['name'] ?? null) === 'media');
                $mediaJson = is_array($mediaPart) ? ($mediaPart['contents'] ?? null) : null;
            }

            $mediaPayload = is_string($mediaJson) ? json_decode($mediaJson, true) : null;

            return str_contains($request->url(), '/sendMediaGroup')
                && is_array($mediaPayload)
                && count($mediaPayload) === 2
                && (($mediaPayload[0]['caption'] ?? null) !== null)
                && (
                    (isset($data['message_thread_id']) && (int) $data['message_thread_id'] === 12345)
                    || collect($data)->contains(fn ($part) => ($part['name'] ?? null) === 'message_thread_id'
                        && (int) ($part['contents'] ?? 0) === 12345)
                );
        });
    }

    public function test_test_taking_staff_registration_sends_only_admin_selected_document_types_to_telegram(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        Config::set('services.telegram.enabled', true);
        Config::set('services.telegram.bot_token', 'telegram-test-token');
        Config::set('services.telegram.chat_id', 'telegram-test-chat');
        Config::set('services.telegram.message_thread_id', 12345);

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'Selected Docs Rank KH',
            'name_en' => 'Selected Docs Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $selectedRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'Selected Requirement KH',
            'name_en' => 'Selected Requirement EN',
            'slug' => 'selected-requirement',
            'sort_order' => 1,
            'is_active' => true,
            'send_to_telegram' => true,
        ]);

        $unselectedRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'Unselected Requirement KH',
            'name_en' => 'Unselected Requirement EN',
            'slug' => 'unselected-requirement',
            'sort_order' => 2,
            'is_active' => true,
            'send_to_telegram' => false,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'Selected Docs Tester KH',
                'name_latin' => 'Selected Docs Tester',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'document_files' => [
                    $selectedRequirement->id => [
                        UploadedFile::fake()->create('selected-document.pdf', 120, 'application/pdf'),
                    ],
                    $unselectedRequirement->id => [
                        UploadedFile::fake()->create('unselected-document.pdf', 120, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertCreated();
        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            $data = $request->data();
            $mediaJson = isset($data['media']) && is_string($data['media'])
                ? $data['media']
                : null;

            if (! $mediaJson) {
                $mediaPart = collect($data)->first(fn ($part) => ($part['name'] ?? null) === 'media');
                $mediaJson = is_array($mediaPart) ? ($mediaPart['contents'] ?? null) : null;
            }

            $mediaPayload = is_string($mediaJson) ? json_decode($mediaJson, true) : null;
            $caption = is_array($mediaPayload) ? (string) ($mediaPayload[0]['caption'] ?? '') : '';

            return str_contains($request->url(), '/sendMediaGroup')
                && is_array($mediaPayload)
                && count($mediaPayload) === 2
                && str_contains($caption, 'Selected Requirement KH')
                && ! str_contains($caption, 'Unselected Requirement KH');
        });
    }

    public function test_test_taking_staff_registration_returns_success_page_without_redirect(): void
    {
        Storage::fake('local');

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'Tester KH',
            'name_en' => 'Test Staff Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'Tester KH',
                'name_latin' => 'Tester Latin',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
            ]);

        $response->assertCreated()
            ->assertSee('public-success-page', false);

    }

    public function test_test_taking_staff_registration_rejects_document_file_larger_than_15_mb(): void
    {
        Storage::fake('local');

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'Limit Rank KH',
            'name_en' => 'Limit Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $documentRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'Limit Document KH',
            'name_en' => 'Limit Document',
            'slug' => 'limit-document',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'Limit Tester KH',
                'name_latin' => 'Limit Tester',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'document_files' => [
                    $documentRequirement->id => [
                        UploadedFile::fake()->create('oversize-document.pdf', 15361, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertUnprocessable();

        $errorKeys = array_keys((array) $response->json('errors', []));
        $hasOversizeError = false;

        foreach ($errorKeys as $errorKey) {
            if (str_starts_with((string) $errorKey, "document_files.{$documentRequirement->id}.")) {
                $hasOversizeError = true;
                break;
            }
        }

        $this->assertTrue($hasOversizeError, 'Expected a per-file validation error for oversized uploads.');
    }

    public function test_test_taking_staff_registration_rejects_total_upload_over_50_mb(): void
    {
        Storage::fake('local');

        $rank = TestTakingStaffRank::query()->create([
            'name_kh' => 'Total Limit Rank KH',
            'name_en' => 'Total Limit Rank',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $documentRequirement = TestTakingStaffDocumentRequirement::query()->create([
            'name_kh' => 'Total Limit Document KH',
            'name_en' => 'Total Limit Document',
            'slug' => 'total-limit-document',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->post('/test-taking-staff-registrations', [
                '_token' => $this->csrfToken(),
                'name_kh' => 'Total Limit Tester KH',
                'name_latin' => 'Total Limit Tester',
                'test_taking_staff_rank_id' => $rank->id,
                'date_of_birth' => '1995-01-10',
                'military_service_day' => '2018-02-01',
                'phone_number' => '012345678',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'document_files' => [
                    $documentRequirement->id => [
                        UploadedFile::fake()->create('doc-1.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-2.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-3.pdf', 15360, 'application/pdf'),
                        UploadedFile::fake()->create('doc-4.pdf', 15360, 'application/pdf'),
                    ],
                ],
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('upload_total');
    }

    public function test_admin_rejects_adding_test_taking_staff_document_when_total_upload_exceeds_50_mb(): void
    {
        Storage::fake('local');
        $this->loginAsAdmin();

        $registration = $this->createTestTakingStaffRegistration();
        $requirement = TestTakingStaffDocumentRequirement::query()->first()
            ?? TestTakingStaffDocumentRequirement::query()->create([
                'name_kh' => 'Admin Total Limit Document KH',
                'name_en' => 'Admin Total Limit Document',
                'slug' => 'admin-total-limit-document',
                'sort_order' => 1,
                'is_active' => true,
            ]);

        $existingPath = 'test-taking-staff/documents/existing-large.pdf';
        $this->createLocalFileWithSize($existingPath, 50000 * 1024);

        $registration->documents()->create([
            'test_taking_staff_document_requirement_id' => $requirement->id,
            'file_path' => $existingPath,
            'original_name' => 'existing-large.pdf',
        ]);

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->post('/admin/test-taking-staff-registrations/'.$registration->id.'/documents', [
                'test_taking_staff_document_requirement_id' => $requirement->id,
                'document_file' => UploadedFile::fake()->create('new-document.pdf', 1500, 'application/pdf'),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('upload_total');

        $this->assertSame(1, $registration->documents()->count());
    }

    public function test_admin_rejects_replacing_test_taking_staff_document_when_total_upload_exceeds_50_mb(): void
    {
        Storage::fake('local');
        $this->loginAsAdmin();

        $registration = $this->createTestTakingStaffRegistration();
        $requirement = TestTakingStaffDocumentRequirement::query()->first()
            ?? TestTakingStaffDocumentRequirement::query()->create([
                'name_kh' => 'Admin Replace Total Limit KH',
                'name_en' => 'Admin Replace Total Limit',
                'slug' => 'admin-replace-total-limit',
                'sort_order' => 2,
                'is_active' => true,
            ]);

        $replacePath = 'test-taking-staff/documents/replace-me.pdf';
        $otherPath = 'test-taking-staff/documents/other-existing.pdf';
        $this->createLocalFileWithSize($replacePath, 20000 * 1024);
        $this->createLocalFileWithSize($otherPath, 30000 * 1024);

        $documentToReplace = $registration->documents()->create([
            'test_taking_staff_document_requirement_id' => $requirement->id,
            'file_path' => $replacePath,
            'original_name' => 'replace-me.pdf',
        ]);

        $registration->documents()->create([
            'test_taking_staff_document_requirement_id' => $requirement->id,
            'file_path' => $otherPath,
            'original_name' => 'other-existing.pdf',
        ]);

        $oldPath = $documentToReplace->file_path;

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->put('/admin/test-taking-staff-registrations/'.$registration->id.'/documents/'.$documentToReplace->id, [
                'document_file' => UploadedFile::fake()->create('replacement.pdf', 22000, 'application/pdf'),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('upload_total');

        $documentToReplace->refresh();
        $this->assertSame($oldPath, $documentToReplace->file_path);
    }

    public function test_admin_can_delete_test_taking_staff_registration_via_json_request(): void
    {
        Storage::fake('local');
        $this->loginAsAdmin();

        $registration = $this->createTestTakingStaffRegistration();

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->delete('/admin/test-taking-staff-registrations/'.$registration->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('test_taking_staff_registrations', [
            'id' => $registration->id,
        ]);
    }

    public function test_register_staff_delete_permission_allows_deleting_registrations(): void
    {
        Storage::fake('local');

        $user = User::query()->create([
            'name' => 'Register Staff Delete Operator',
            'email' => 'register.staff.delete@example.com',
            'password' => 'Password@12345',
            'is_admin' => false,
            'role' => 'Management',
            'permissions' => ['register-staff.delete'],
        ]);

        $registration = $this->createTestTakingStaffRegistration();

        $response = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->delete('/admin/test-taking-staff-registrations/'.$registration->id);

        $response->assertNoContent();

        $this->assertDatabaseMissing('test_taking_staff_registrations', [
            'id' => $registration->id,
        ]);
    }

    public function test_register_staff_read_only_permission_cannot_delete_registrations(): void
    {
        Storage::fake('local');

        $user = User::query()->create([
            'name' => 'Register Staff Viewer',
            'email' => 'register.staff.viewer@example.com',
            'password' => 'Password@12345',
            'is_admin' => false,
            'role' => 'Management',
            'permissions' => ['register-staff.read'],
        ]);

        $registration = $this->createTestTakingStaffRegistration();

        $response = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->delete('/admin/test-taking-staff-registrations/'.$registration->id);

        $response->assertForbidden();

        $this->assertDatabaseHas('test_taking_staff_registrations', [
            'id' => $registration->id,
        ]);
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

    public function test_admin_can_login_with_username(): void
    {
        $loginResponse = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->postJson('/admin/login', [
                '_token' => $this->csrfToken(),
                'email' => '  Geography Training Center  ',
                'password' => '99996666',
            ]);

        $loginResponse->assertOk()
            ->assertJsonPath('user.name', 'Geography Training Center')
            ->assertJsonPath('user.email', 'geography.training.center@system.local');
    }

    public function test_team_staff_username_matches_latin_name_when_created(): void
    {
        Storage::fake('local');
        $this->loginAsAdmin();

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->post('/admin/team-staff', [
                '_token' => $this->csrfToken(),
                'military_rank' => 'Captain',
                'name_kh' => 'Yuddho KH',
                'name_latin' => 'Yuddho Seavminh',
                'id_number' => '058256',
                'avatar_image' => UploadedFile::fake()->image('avatar.png', 300, 300),
                'gender' => 'Male',
                'position' => 'Operations Officer',
                'role' => 'Staff',
                'phone_number' => '012345678',
            ]);

        $response->assertCreated()
            ->assertJsonPath('username', 'Yuddho Seavminh');

        $this->assertDatabaseHas('team_staff', [
            'name_latin' => 'Yuddho Seavminh',
            'username' => 'Yuddho Seavminh',
        ]);
    }

    public function test_team_staff_username_updates_when_latin_name_changes(): void
    {
        $staff = $this->createStaffAccount();
        $this->loginAsAdmin();

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('Accept', 'application/json')
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->put("/admin/team-staff/{$staff->id}", [
                '_token' => $this->csrfToken(),
                'military_rank' => $staff->military_rank,
                'name_kh' => $staff->name_kh,
                'name_latin' => 'Yuddho Seavminh',
                'id_number' => $staff->id_number,
                'gender' => $staff->gender,
                'position' => $staff->position,
                'role' => $staff->role,
                'phone_number' => $staff->phone_number,
            ]);

        $response->assertOk()
            ->assertJsonPath('username', 'Yuddho Seavminh');

        $this->assertDatabaseHas('team_staff', [
            'id' => $staff->id,
            'name_latin' => 'Yuddho Seavminh',
            'username' => 'Yuddho Seavminh',
        ]);
    }

    public function test_staff_first_login_redirects_to_password_change(): void
    {
        $staff = $this->createStaffAccount();

        $response = $this->post('/staff/login', [
            'username' => $staff->username,
            'password' => '058256',
        ]);

        $response->assertRedirect(route('staff.password.edit'));
        $this->assertAuthenticatedAs($staff, 'staff');
    }

    public function test_staff_can_change_password_and_manage_private_documents(): void
    {
        Storage::fake('local');
        $staff = $this->createStaffAccount([
            'avatar_path' => 'team-staff/avatar-test-2.jpg',
        ]);
        Storage::disk('local')->put($staff->avatar_path, 'avatar');

        $this->post('/staff/login', [
            'username' => $staff->username,
            'password' => '058256',
        ])->assertRedirect(route('staff.password.edit'));

        $this->put('/staff/password', [
            'password' => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ])->assertRedirect(route('staff.profile.show'));

        $this->get('/staff/profile')
            ->assertOk()
            ->assertSee($staff->name_latin);

        $this->post('/staff/profile/documents', [
            'document_title' => 'Service Letter',
            'document_file' => UploadedFile::fake()->create('service-letter.pdf', 120, 'application/pdf'),
        ])->assertRedirect(route('staff.profile.show'));

        $staff->refresh();
        $this->assertCount(1, $staff->documents);
        $this->assertSame('staff', $staff->documents[0]['uploaded_by']);

        $this->get('/staff/profile/documents/0/download')
            ->assertOk();

        $this->delete('/staff/profile/documents/0')
            ->assertRedirect(route('staff.profile.show'));

        $staff->refresh();
        $this->assertSame([], $staff->documents);
    }

    public function test_staff_profile_document_routes_support_legacy_file_path_key(): void
    {
        Storage::fake('local');
        $legacyPath = 'team-staff/legacy-profile-document.pdf';

        $staff = $this->createStaffAccount([
            'avatar_path' => 'team-staff/avatar-test-legacy-staff.jpg',
            'documents' => [[
                'label' => 'Legacy Profile Document',
                'file_path' => $legacyPath,
                'original_name' => 'legacy-profile-document.pdf',
                'uploaded_by' => 'staff',
                'status' => 'Pending',
            ]],
        ]);

        Storage::disk('local')->put($legacyPath, 'legacy-profile-document-content');

        $this->post('/staff/login', [
            'username' => $staff->username,
            'password' => '058256',
        ])->assertRedirect(route('staff.password.edit'));

        $this->put('/staff/password', [
            'password' => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ])->assertRedirect(route('staff.profile.show'));

        $this->get('/staff/profile/documents/0/show')
            ->assertOk();

        $this->get('/staff/profile/documents/0/download')
            ->assertOk();
    }

    public function test_admin_team_staff_document_routes_support_legacy_file_path_key(): void
    {
        Storage::fake('local');
        $legacyPath = 'team-staff/legacy-admin-document.pdf';

        $staff = $this->createStaffAccount([
            'avatar_path' => 'team-staff/avatar-test-legacy-admin.jpg',
            'documents' => [[
                'label' => 'Legacy Admin Document',
                'file_path' => $legacyPath,
                'original_name' => 'legacy-admin-document.pdf',
                'uploaded_by' => 'admin',
                'status' => 'Approved',
            ]],
        ]);

        Storage::disk('local')->put($legacyPath, 'legacy-admin-document-content');

        $this->loginAsAdmin();

        $this->get('/admin/team-staff/'.$staff->id.'/documents/0')
            ->assertOk();

        $this->get('/admin/team-staff/'.$staff->id.'/documents/0/download')
            ->assertOk();
    }

    public function test_admin_can_create_update_and_delete_system_users(): void
    {
        $this->loginAsAdmin();

        $this->post('/admin/users', [
            'name' => 'Global Admin',
            'email' => 'global.admin@gmail.com',
            'password' => 'GlobalAdmin@123',
            'password_confirmation' => 'GlobalAdmin@123',
            'role' => 'Management',
            'is_admin' => '1',
        ])->assertRedirect(route('admin.home', ['section' => 'users']));

        $user = User::query()->where('email', 'global.admin@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->is_admin);

        $this->put('/admin/users/'.$user->id, [
            'name' => 'Operations Manager',
            'email' => 'operations.manager@gmail.com',
            'password' => 'Operations@1234',
            'password_confirmation' => 'Operations@1234',
            'is_admin' => '0',
        ])->assertRedirect(route('admin.home', ['section' => 'users']));

        $user->refresh();

        $this->assertSame('Operations Manager', $user->name);
        $this->assertSame('operations.manager@gmail.com', $user->email);
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('Operations@1234', $user->password));

        $this->delete('/admin/users/'.$user->id)
            ->assertRedirect(route('admin.home', ['section' => 'users']));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_the_currently_authenticated_account(): void
    {
        $this->loginAsAdmin();

        $admin = User::query()->where('email', 'seavminhcoding@gmail.com')->firstOrFail();

        $this->delete('/admin/users/'.$admin->id)
            ->assertRedirect(route('admin.home', ['section' => 'users']))
            ->assertSessionHasErrors('users');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email' => 'seavminhcoding@gmail.com',
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

    public function test_admin_can_open_design_template_page(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin?section=design-template');

        $response->assertOk()
            ->assertSee('Portal Content');

    }
    public function test_course_template_page_shows_single_design_navigation_group(): void
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin?section=course-template');

        $response->assertOk()
            ->assertSee('section=design-template', false)
            ->assertSee('section=course-template', false)
            ->assertSee('section=test-taking-staff-template', false)
            ->assertDontSee('template_tab=portal', false)
            ->assertDontSee('template_tab=staff', false);
    }

    public function test_admin_portal_content_redirect_preserves_active_template_tab(): void
    {
        $this->loginAsAdmin();

        $portalContent = PortalContent::query()->firstOrFail();

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->put('/admin/portal-content', [
                '_token' => $this->csrfToken(),
                'template_tab' => 'staff',
                'badge' => $portalContent->badge,
                'title' => $portalContent->title,
                'description' => $portalContent->description,
                'feature_one_title' => $portalContent->feature_one_title,
                'feature_one_description' => $portalContent->feature_one_description,
                'feature_two_title' => $portalContent->feature_two_title,
                'feature_two_description' => $portalContent->feature_two_description,
                'feature_three_title' => $portalContent->feature_three_title,
                'feature_three_description' => $portalContent->feature_three_description,
                'staff_title' => 'Updated Staff Title',
                'staff_subtitle' => 'Updated Staff Subtitle',
            ]);

        $response->assertRedirect(route('admin.home', [
            'section' => 'design-template',
            'template_tab' => 'staff',
        ]));

        $this->assertDatabaseHas('portal_contents', [
            'id' => $portalContent->id,
            'staff_title' => 'Updated Staff Title',
            'staff_subtitle' => 'Updated Staff Subtitle',
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
            ->assertSee('Back to Rank List')
            ->assertSee('sort_order');
    }

    public function test_admin_can_create_rank_with_sort_order(): void
    {
        $this->loginAsAdmin();

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->postJson('/admin/ranks', [
                'name_kh' => 'Rank New KH',
                'sort_order' => 9,
                'is_active' => true,
            ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name_kh' => 'Rank New KH',
                'sort_order' => 9,
            ]);

        $this->assertDatabaseHas('ranks', [
            'name_kh' => 'Rank New KH',
            'name_en' => 'Rank New KH',
            'sort_order' => 9,
            'is_active' => true,
        ]);
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

    public function test_admin_can_choose_multiple_document_requirements_for_telegram_sending(): void
    {
        $this->loginAsAdmin();
        DocumentRequirement::query()->update(['is_protected' => false]);

        $currentTelegramRequirement = DocumentRequirement::query()->create([
            'name_kh' => 'Telegram Current KH',
            'name_en' => 'Telegram Current',
            'slug' => 'telegram-current',
            'sort_order' => 50,
            'is_active' => true,
            'is_protected' => true,
        ]);

        $nextTelegramRequirement = DocumentRequirement::query()->create([
            'name_kh' => 'Telegram Next KH',
            'name_en' => 'Telegram Next',
            'slug' => 'telegram-next',
            'sort_order' => 51,
            'is_active' => true,
            'is_protected' => false,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->putJson('/admin/document-requirements/'.$nextTelegramRequirement->id, [
                'name_kh' => $nextTelegramRequirement->name_kh,
                'sort_order' => $nextTelegramRequirement->sort_order,
                'is_active' => true,
                'is_protected' => true,
                'slug' => $nextTelegramRequirement->slug,
            ]);

        $response->assertOk()
            ->assertJsonPath('id', $nextTelegramRequirement->id)
            ->assertJsonPath('is_protected', true);

        $this->assertDatabaseHas('document_requirements', [
            'id' => $nextTelegramRequirement->id,
            'is_protected' => true,
        ]);

        $this->assertDatabaseHas('document_requirements', [
            'id' => $currentTelegramRequirement->id,
            'is_protected' => true,
        ]);
    }

    public function test_admin_can_mark_more_than_two_document_requirements_for_telegram_sending(): void
    {
        $this->loginAsAdmin();
        DocumentRequirement::query()->update(['is_protected' => false]);

        DocumentRequirement::query()->create([
            'name_kh' => 'Telegram First KH',
            'name_en' => 'Telegram First',
            'slug' => 'telegram-first',
            'sort_order' => 50,
            'is_active' => true,
            'is_protected' => true,
        ]);

        DocumentRequirement::query()->create([
            'name_kh' => 'Telegram Second KH',
            'name_en' => 'Telegram Second',
            'slug' => 'telegram-second',
            'sort_order' => 51,
            'is_active' => true,
            'is_protected' => true,
        ]);

        $thirdRequirement = DocumentRequirement::query()->create([
            'name_kh' => 'Telegram Third KH',
            'name_en' => 'Telegram Third',
            'slug' => 'telegram-third',
            'sort_order' => 52,
            'is_active' => true,
            'is_protected' => false,
        ]);

        $response = $this
            ->withSession(['_token' => $this->csrfToken()])
            ->withHeader('X-CSRF-TOKEN', $this->csrfToken())
            ->putJson('/admin/document-requirements/'.$thirdRequirement->id, [
                'name_kh' => $thirdRequirement->name_kh,
                'sort_order' => $thirdRequirement->sort_order,
                'is_active' => true,
                'is_protected' => true,
                'slug' => $thirdRequirement->slug,
            ]);

        $response->assertOk()
            ->assertJsonPath('id', $thirdRequirement->id)
            ->assertJsonPath('is_protected', true);

        $this->assertDatabaseHas('document_requirements', [
            'id' => $thirdRequirement->id,
            'is_protected' => true,
        ]);
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

