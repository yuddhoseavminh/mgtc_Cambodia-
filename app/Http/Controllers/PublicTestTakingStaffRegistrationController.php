<?php

namespace App\Http\Controllers;

use App\Models\PortalContent;
use App\Models\TestTakingStaffDocumentRequirement;
use App\Models\TestTakingStaffRank;
use App\Models\TestTakingStaffRegistration;
use App\Support\UploadStorage;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicTestTakingStaffRegistrationController extends Controller
{
    private const MAX_TOTAL_UPLOAD_BYTES = 104857600;
    private const SUCCESS_TITLE = 'ការចុះឈ្មោះជោគជ័យ';
    private const SUCCESS_MESSAGE = 'ការចុះឈ្មោះបុគ្គលិកសាកល្បងបានជោគជ័យ។';
    private const SUCCESS_DESCRIPTION = 'ព័ត៌មានរបស់អ្នកត្រូវបានបញ្ជូនរួចរាល់។ សូមរង់ចាំការត្រួតពិនិត្យពីក្រុមការងារ។';
    private const TOTAL_UPLOAD_ERROR = 'ទំហំឯកសារសរុបធំពេក។ សូមរក្សាទំហំឯកសារនីមួយៗក្រោម 50 MB និងទំហំសរុបក្រោម 100 MB។';

    public function store(Request $request): JsonResponse|Response
    {
        $documentRequirements = TestTakingStaffDocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->get();

        $rules = [
            'name_kh' => ['required', 'string', 'max:255'],
            'name_latin' => ['required', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:100', Rule::unique('test_taking_staff_registrations', 'id_number')],
            'test_taking_staff_rank_id' => ['required', Rule::exists('test_taking_staff_ranks', 'id')],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'military_service_day' => ['required', 'date', 'before_or_equal:today'],
            'phone_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'avatar_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        foreach ($documentRequirements as $documentRequirement) {
            $rules["document_files.{$documentRequirement->id}"] = ['nullable', 'array'];
            $rules["document_files.{$documentRequirement->id}.*"] = ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp', 'max:51200'];
        }

        $validated = $request->validate($rules);

        $this->ensureAggregateUploadLimit(
            [
                $request->file('avatar_image'),
                $request->file('document_files', []),
            ],
            self::TOTAL_UPLOAD_ERROR,
        );

        $folder = 'test-taking-staff-registrations/'.Str::uuid();

        $registration = DB::transaction(function () use ($validated, $request, $folder, $documentRequirements) {
            $avatar = $request->file('avatar_image');

            $registration = TestTakingStaffRegistration::create([
                'name_kh' => $validated['name_kh'],
                'name_latin' => $validated['name_latin'],
                'id_number' => $validated['id_number'] ?? null,
                'test_taking_staff_rank_id' => $validated['test_taking_staff_rank_id'],
                'date_of_birth' => $validated['date_of_birth'],
                'military_service_day' => $validated['military_service_day'],
                'phone_number' => $validated['phone_number'],
                'avatar_path' => $this->storeAvatar($avatar, $folder),
                'avatar_original_name' => $avatar?->getClientOriginalName(),
                'submitted_at' => now(),
            ]);

            $documents = $documentRequirements
                ->flatMap(function (TestTakingStaffDocumentRequirement $documentRequirement) use ($request, $folder) {
                    $files = $request->file("document_files.{$documentRequirement->id}");

                    if (! is_array($files)) {
                        $files = $files instanceof UploadedFile ? [$files] : [];
                    }

                    return collect($files)->map(fn (UploadedFile $file) => [
                        'test_taking_staff_document_requirement_id' => $documentRequirement->id,
                        ...$this->storeDocument($file, $folder, $documentRequirement->slug),
                    ])->all();
                })
                ->filter()
                ->values()
                ->all();

            if ($documents !== []) {
                $registration->documents()->createMany($documents);
            }

            return $registration;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => self::SUCCESS_MESSAGE,
                'id' => $registration->id,
            ], 201);
        }

        $request->session()->now('status_title', self::SUCCESS_TITLE);
        $request->session()->now('status', self::SUCCESS_DESCRIPTION);

        return response()->view('public.test-taking-staff-register', $this->pageData($documentRequirements), 201);
    }

    private function storeAvatar(?UploadedFile $avatar, string $folder): string
    {
        if (! $avatar) {
            abort(422, 'Avatar image is required.');
        }

        return UploadStorage::storeAs(
            $avatar,
            $folder,
            'avatar-'.Str::uuid().'.'.$avatar->getClientOriginalExtension(),
        );
    }

    /**
     * @return array<string, string|null>
     */
    private function storeDocument(UploadedFile $file, string $folder, string $slug): array
    {
        $path = UploadStorage::storeAs(
            $file,
            $folder,
            $slug.'-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
        );

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    private function sendTelegramRegistrationNotification(TestTakingStaffRegistration $registration): void
    {
        if (! $this->telegramNotificationsEnabled()) {
            return;
        }

        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $botToken || ! $chatId) {
            return;
        }

        $registration->loadMissing([
            'rank:id,name_kh,name_en',
            'documents.documentRequirement:id,name_kh,name_en',
        ]);

        $message = $this->buildTelegramRegistrationMessage($registration);

        try {
            $response = $this->telegramRequest()
                ->asForm()
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                return;
            }

            $fallbackResponse = $this->sendTelegramRegistrationMediaFallback($registration, $chatId, $botToken, $message);

            if ($fallbackResponse?->successful()) {
                return;
            }

            Log::warning('Telegram test-taking staff registration notification failed.', [
                'registration_id' => $registration->id,
                'status' => $response->status(),
                'response' => $response->body(),
                'fallback_status' => $fallbackResponse?->status(),
                'fallback_response' => $fallbackResponse?->body(),
            ]);
        } catch (\Throwable $exception) {
            $sslRetryResponse = null;
            $sslRetryFallbackResponse = null;

            if ($this->isTelegramSslException($exception)) {
                try {
                    $sslRetryResponse = $this->telegramRequest(forceWithoutVerifying: true)
                        ->asForm()
                        ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                            'chat_id' => $chatId,
                            'text' => $message,
                        ]);

                    if ($sslRetryResponse->successful()) {
                        return;
                    }

                    $sslRetryFallbackResponse = $this->sendTelegramRegistrationMediaFallback(
                        $registration,
                        $chatId,
                        $botToken,
                        $message,
                        true
                    );

                    if ($sslRetryFallbackResponse?->successful()) {
                        return;
                    }
                } catch (\Throwable $retryException) {
                    Log::warning('Telegram test-taking staff SSL retry threw an exception.', [
                        'registration_id' => $registration->id,
                        'message' => $retryException->getMessage(),
                    ]);
                }
            }

            Log::warning('Telegram test-taking staff registration notification threw an exception.', [
                'registration_id' => $registration->id,
                'message' => $exception->getMessage(),
                'ssl_retry_status' => $sslRetryResponse?->status(),
                'ssl_retry_response' => $sslRetryResponse?->body(),
                'ssl_retry_fallback_status' => $sslRetryFallbackResponse?->status(),
                'ssl_retry_fallback_response' => $sslRetryFallbackResponse?->body(),
            ]);
        }
    }

    private function sendTelegramRegistrationMediaFallback(
        TestTakingStaffRegistration $registration,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): ?HttpResponse {
        $attachmentPath = $this->pickTelegramAttachmentPath($registration);

        if (! $attachmentPath) {
            return null;
        }

        $absolutePath = UploadStorage::path($attachmentPath);
        $resource = fopen($absolutePath, 'r');

        if ($resource === false) {
            return null;
        }

        try {
            return $this->telegramRequest($forceWithoutVerifying)
                ->attach('document', $resource, basename($absolutePath))
                ->post("https://api.telegram.org/bot{$botToken}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => Str::limit($message, 900, '...'),
                ]);
        } finally {
            fclose($resource);
        }
    }

    private function pickTelegramAttachmentPath(TestTakingStaffRegistration $registration): ?string
    {
        $candidatePaths = [
            $registration->avatar_path,
            ...$registration->documents
                ->pluck('file_path')
                ->filter()
                ->all(),
        ];

        foreach ($candidatePaths as $path) {
            if (is_string($path) && UploadStorage::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function isTelegramSslException(\Throwable $exception): bool
    {
        if (! config('services.telegram.verify_ssl', true)) {
            return false;
        }

        return str_contains(strtolower($exception->getMessage()), 'ssl certificate');
    }

    private function telegramNotificationsEnabled(): bool
    {
        return filter_var(config('services.telegram.enabled', false), FILTER_VALIDATE_BOOLEAN) === true;
    }

    private function buildTelegramRegistrationMessage(TestTakingStaffRegistration $registration): string
    {
        $documentLabels = $registration->documents
            ->map(fn ($document) => $document->documentRequirement?->name_kh ?: $document->original_name)
            ->filter()
            ->implode(', ');

        return implode("\n", [
            'ទទួលបានការចុះឈ្មោះបុគ្គលិកសាកល្បងថ្មី',
            'លេខសម្គាល់ចុះឈ្មោះ: #'.$registration->id,
            'ឈ្មោះ (ខ្មែរ): '.$registration->name_kh,
            'ឈ្មោះ (ឡាតាំង): '.$registration->name_latin,
            'អត្តលេខ: '.($registration->id_number ?: '-'),
            'ឋានន្តរស័ក្តិ: '.($registration->rank?->name_kh ?? $registration->rank?->name_en ?? '-'),
            'លេខទូរស័ព្ទ: '.$registration->phone_number,
            'ថ្ងៃខែឆ្នាំកំណើត: '.optional($registration->date_of_birth)->format('d/m/Y'),
            'ថ្ងៃចូលបម្រើការងារកងទ័ព: '.optional($registration->military_service_day)->format('d/m/Y'),
            'ឯកសារភ្ជាប់: '.($documentLabels !== '' ? $documentLabels : 'គ្មាន'),
            'បញ្ជូននៅម៉ោង: '.optional($registration->submitted_at)->timezone('Asia/Phnom_Penh')->format('d/m/Y H:i:s'),
        ]);
    }

    private function telegramRequest(bool $forceWithoutVerifying = false)
    {
        $request = Http::timeout(30);

        if ($forceWithoutVerifying || ! config('services.telegram.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function ensureAggregateUploadLimit(mixed $files, string $message): void
    {
        if ($this->sumUploadedFileSizes($files) <= self::MAX_TOTAL_UPLOAD_BYTES) {
            return;
        }

        throw ValidationException::withMessages([
            'upload_total' => $message,
        ]);
    }

    private function sumUploadedFileSizes(mixed $files): int
    {
        if ($files instanceof UploadedFile) {
            return $files->getSize() ?: 0;
        }

        if (is_array($files)) {
            return array_sum(array_map(fn (mixed $file) => $this->sumUploadedFileSizes($file), $files));
        }

        return 0;
    }

    /**
     * @param Collection<int, TestTakingStaffDocumentRequirement> $documentRequirements
     * @return array<string, mixed>
     */
    private function pageData(Collection $documentRequirements): array
    {
        return [
            'portalContent' => PortalContent::query()->first(),
            'ranks' => TestTakingStaffRank::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
            'documentRequirements' => $documentRequirements,
        ];
    }
}
