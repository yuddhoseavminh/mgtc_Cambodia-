<?php

namespace App\Http\Controllers;

use App\Jobs\SendTestTakingStaffRegistrationTelegramNotification;
use App\Models\TestTakingStaffDocumentRequirement;
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
    private const MOBILE_MAX_TOTAL_UPLOAD_BYTES = 10485760;
    private const SUCCESS_TITLE = 'ការចុះឈ្មោះជោគជ័យ';
    private const SUCCESS_MESSAGE = 'ការចុះឈ្មោះបុគ្គលិកសាកល្បងបានជោគជ័យ។';
    private const SUCCESS_DESCRIPTION = 'ព័ត៌មានរបស់អ្នកត្រូវបានបញ្ជូនរួចរាល់។ សូមរង់ចាំការត្រួតពិនិត្យពីក្រុមការងារ។';
    private const TOTAL_UPLOAD_ERROR = 'Total upload size is too large. Please reduce the total upload size and try again.';

    public function store(Request $request): JsonResponse|Response
    {
        $documentRequirements = TestTakingStaffDocumentRequirement::query()
            ->select(['id', 'slug'])
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
        $uploadedDocumentBatches = $request->file('document_files', []);

        if (! is_array($uploadedDocumentBatches)) {
            $uploadedDocumentBatches = [];
        }

        $aggregateUploadLimit = $this->resolveAggregateUploadLimit($request);

        $this->ensureAggregateUploadLimit(
            [
                $request->file('avatar_image'),
                $uploadedDocumentBatches,
            ],
            self::TOTAL_UPLOAD_ERROR,
            $aggregateUploadLimit,
        );

        $folder = 'test-taking-staff-registrations/'.Str::uuid();
        $avatar = $request->file('avatar_image');
        $storedPaths = [];

        try {
            $storedAvatarPath = $this->storeAvatar($avatar, $folder);
            $storedPaths[] = $storedAvatarPath;
            $documents = [];

            foreach ($documentRequirements as $documentRequirement) {
                $files = $uploadedDocumentBatches[$documentRequirement->id] ?? [];

                if (! is_array($files)) {
                    $files = $files instanceof UploadedFile ? [$files] : [];
                }

                foreach ($files as $file) {
                    if (! $file instanceof UploadedFile) {
                        continue;
                    }

                    $storedDocument = $this->storeDocument($file, $folder, $documentRequirement->slug);

                    if (filled($storedDocument['file_path'] ?? null)) {
                        $storedPaths[] = $storedDocument['file_path'];
                    }

                    $documents[] = [
                        'test_taking_staff_document_requirement_id' => $documentRequirement->id,
                        ...$storedDocument,
                    ];
                }
            }

            $registration = DB::transaction(function () use ($validated, $avatar, $storedAvatarPath, $documents) {
                $registration = TestTakingStaffRegistration::create([
                    'name_kh' => $validated['name_kh'],
                    'name_latin' => $validated['name_latin'],
                    'id_number' => $validated['id_number'] ?? null,
                    'test_taking_staff_rank_id' => $validated['test_taking_staff_rank_id'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'military_service_day' => $validated['military_service_day'],
                    'phone_number' => $validated['phone_number'],
                    'avatar_path' => $storedAvatarPath,
                    'avatar_original_name' => $avatar?->getClientOriginalName(),
                    'submitted_at' => now(),
                ]);

                if ($documents !== []) {
                    $timestamp = now();
                    $registration->documents()->getModel()->newQuery()->insert(
                        array_map(fn (array $document) => [
                            'test_taking_staff_registration_id' => $registration->id,
                            ...$document,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ], $documents)
                    );
                }

                return $registration;
            });
        } catch (\Throwable $exception) {
            UploadStorage::delete($storedPaths);

            throw $exception;
        }

        $this->queueTelegramRegistrationNotification($registration);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => self::SUCCESS_MESSAGE,
                'id' => $registration->id,
            ], 201);
        }

        $request->session()->flash('status_title', self::SUCCESS_TITLE);
        $request->session()->flash('status', self::SUCCESS_DESCRIPTION);

        return response()->view('public.test-taking-staff-register', $this->successPageData(), 201);
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

    private function queueTelegramRegistrationNotification(TestTakingStaffRegistration $registration): void
    {
        if (! $this->telegramNotificationsEnabled()) {
            return;
        }

        try {
            $pendingDispatch = SendTestTakingStaffRegistrationTelegramNotification::dispatch($registration)->afterResponse();
            unset($pendingDispatch);
        } catch (\Throwable $exception) {
            Log::warning('Unable to queue Telegram test-taking staff registration notification.', [
                'registration_id' => $registration->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sendTelegramRegistrationNotification(TestTakingStaffRegistration $registration): void
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
            $result = $this->dispatchTelegramRegistrationNotification($registration, $chatId, $botToken, $message);
            $response = $result['primary'];
            $fallbackResponse = $result['fallback'];

            if ($response?->successful() || $fallbackResponse?->successful()) {
                return;
            }

            Log::warning('Telegram test-taking staff registration notification failed.', [
                'registration_id' => $registration->id,
                'status' => $response?->status(),
                'response' => $response?->body(),
                'fallback_status' => $fallbackResponse?->status(),
                'fallback_response' => $fallbackResponse?->body(),
            ]);
        } catch (\Throwable $exception) {
            $sslRetryResponse = null;
            $sslRetryFallbackResponse = null;
            $exceptionTextFallback = null;

            if ($this->isTelegramSslException($exception)) {
                try {
                    $retryResult = $this->dispatchTelegramRegistrationNotification(
                        $registration,
                        $chatId,
                        $botToken,
                        $message,
                        true
                    );

                    $sslRetryResponse = $retryResult['primary'];
                    $sslRetryFallbackResponse = $retryResult['fallback'];

                    if ($sslRetryResponse?->successful() || $sslRetryFallbackResponse?->successful()) {
                        return;
                    }
                } catch (\Throwable $retryException) {
                    Log::warning('Telegram test-taking staff SSL retry threw an exception.', [
                        'registration_id' => $registration->id,
                        'message' => $retryException->getMessage(),
                    ]);
                }
            }

            try {
                $exceptionTextFallback = $this->sendTelegramTextMessage(
                    $chatId,
                    $botToken,
                    $message,
                    $this->isTelegramSslException($exception)
                );

                if ($exceptionTextFallback->successful()) {
                    Log::warning('Telegram test-taking staff media notification failed, but text fallback succeeded.', [
                        'registration_id' => $registration->id,
                        'message' => $exception->getMessage(),
                    ]);

                    return;
                }
            } catch (\Throwable $fallbackException) {
                Log::warning('Telegram test-taking staff exception text fallback threw an exception.', [
                    'registration_id' => $registration->id,
                    'message' => $fallbackException->getMessage(),
                ]);
            }

            Log::warning('Telegram test-taking staff registration notification threw an exception.', [
                'registration_id' => $registration->id,
                'message' => $exception->getMessage(),
                'ssl_retry_status' => $sslRetryResponse?->status(),
                'ssl_retry_response' => $sslRetryResponse?->body(),
                'ssl_retry_fallback_status' => $sslRetryFallbackResponse?->status(),
                'ssl_retry_fallback_response' => $sslRetryFallbackResponse?->body(),
                'exception_text_fallback_status' => $exceptionTextFallback?->status(),
                'exception_text_fallback_response' => $exceptionTextFallback?->body(),
            ]);
        }
    }

    /**
     * @return array{primary: HttpResponse|null, fallback: HttpResponse|null}
     */
    private function dispatchTelegramRegistrationNotification(
        TestTakingStaffRegistration $registration,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): array {
        if (! $this->telegramAttachmentsEnabled()) {
            return [
                'primary' => $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying),
                'fallback' => null,
            ];
        }

        $attachments = $this->preferredTelegramAttachments($registration);

        if ($attachments->isNotEmpty()) {
            return $this->sendTelegramRegistrationMediaDocuments(
                $registration,
                $attachments,
                $chatId,
                $botToken,
                $message,
                $forceWithoutVerifying
            );
        }

        $primaryResponse = $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying);

        if ($primaryResponse->successful()) {
            return ['primary' => $primaryResponse, 'fallback' => null];
        }

        $fallbackResponse = $this->sendTelegramFallbackDocument(
            $registration,
            $chatId,
            $botToken,
            $message,
            $forceWithoutVerifying
        );

        return ['primary' => $primaryResponse, 'fallback' => $fallbackResponse];
    }

    /**
     * @param  Collection<int, array{path: string, original_name: string}>  $attachments
     * @return array{primary: HttpResponse|null, fallback: HttpResponse|null}
     */
    private function sendTelegramRegistrationMediaDocuments(
        TestTakingStaffRegistration $registration,
        Collection $attachments,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): array {
        $primaryResponse = $this->sendTelegramRegistrationMediaGroup(
            $registration,
            $attachments,
            $chatId,
            $botToken,
            $message,
            $forceWithoutVerifying
        );

        if ($primaryResponse?->successful()) {
            return ['primary' => $primaryResponse, 'fallback' => null];
        }

        return [
            'primary' => $primaryResponse,
            'fallback' => $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying),
        ];
    }

    /**
     * @return Collection<int, array{path: string, original_name: string}>
     */
    private function preferredTelegramAttachments(TestTakingStaffRegistration $registration): Collection
    {
        $attachments = $registration->documents
            ->filter(fn ($document) => filled($document->file_path) && UploadStorage::exists($document->file_path))
            ->map(fn ($document) => [
                'path' => (string) $document->file_path,
                'original_name' => (string) ($document->original_name ?: basename((string) $document->file_path)),
            ])
            ->values();

        if (filled($registration->avatar_path) && UploadStorage::exists($registration->avatar_path)) {
            $attachments->push([
                'path' => (string) $registration->avatar_path,
                'original_name' => (string) ($registration->avatar_original_name ?: basename((string) $registration->avatar_path)),
            ]);
        }

        return $attachments
            ->unique('path')
            ->values();
    }

    /**
     * @param  array{path: string, original_name: string}  $attachment
     */
    private function sendTelegramRegistrationMedia(
        TestTakingStaffRegistration $registration,
        array $attachment,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): HttpResponse {
        $path = UploadStorage::path($attachment['path']);

        if (! is_file($path)) {
            Log::warning('Telegram test-taking staff attachment missing on disk.', [
                'registration_id' => $registration->id,
                'file_path' => $attachment['path'],
            ]);

            return $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying);
        }

        $resource = fopen($path, 'r');
        $filename = $attachment['original_name'] ?: basename($path);

        if ($resource === false) {
            return $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying);
        }

        try {
            return $this->telegramRequest($forceWithoutVerifying)
                ->attach('document', $resource, $filename)
                ->post("https://api.telegram.org/bot{$botToken}/sendDocument", $this->telegramPayload([
                    'chat_id' => $chatId,
                    'caption' => Str::limit($message, 900, '...'),
                ]));
        } finally {
            fclose($resource);
        }
    }

    private function sendTelegramTextMessage(
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): HttpResponse {
        return $this->telegramRequest($forceWithoutVerifying)
            ->asForm()
            ->post("https://api.telegram.org/bot{$botToken}/sendMessage", $this->telegramPayload([
                'chat_id' => $chatId,
                'text' => $message,
            ]));
    }

    private function sendTelegramFallbackDocument(
        TestTakingStaffRegistration $registration,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): ?HttpResponse {
        $attachment = $this->preferredTelegramAttachments($registration)->first();

        if (! is_array($attachment)) {
            return null;
        }

        return $this->sendTelegramRegistrationMedia(
            $registration,
            $attachment,
            $chatId,
            $botToken,
            $message,
            $forceWithoutVerifying
        );
    }

    /**
     * @param  Collection<int, array{path: string, original_name: string}>  $attachments
     */
    private function sendTelegramRegistrationMediaGroup(
        TestTakingStaffRegistration $registration,
        Collection $attachments,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): ?HttpResponse {
        $attachments = $attachments->values();

        if ($attachments->isEmpty()) {
            return null;
        }

        if ($attachments->count() === 1) {
            return $this->sendTelegramRegistrationMedia(
                $registration,
                $attachments->first(),
                $chatId,
                $botToken,
                $message,
                $forceWithoutVerifying
            );
        }

        $lastResponse = null;
        $captionPending = true;
        $caption = Str::limit($message, 900, '...');

        foreach ($attachments->chunk(10) as $chunk) {
            $request = $this->telegramRequest($forceWithoutVerifying);
            $media = [];
            $resources = [];
            $validIndex = 0;

            foreach ($chunk as $attachment) {
                $path = UploadStorage::path($attachment['path']);

                if (! is_file($path)) {
                    Log::warning('Telegram test-taking staff attachment missing on disk.', [
                        'registration_id' => $registration->id,
                        'file_path' => $attachment['path'],
                    ]);
                    continue;
                }

                $resource = fopen($path, 'r');

                if ($resource === false) {
                    continue;
                }

                $attachName = 'document_'.$validIndex;
                $request = $request->attach($attachName, $resource, $attachment['original_name'] ?: basename($path));

                $mediaItem = [
                    'type' => 'document',
                    'media' => 'attachment://'.$attachName,
                ];

                if ($captionPending && $validIndex === 0) {
                    $mediaItem['caption'] = $caption;
                    $captionPending = false;
                }

                $media[] = $mediaItem;
                $resources[] = $resource;
                $validIndex++;
            }

            if ($media === []) {
                continue;
            }

            try {
                $response = $request->post(
                    "https://api.telegram.org/bot{$botToken}/sendMediaGroup",
                    $this->telegramPayload([
                        'chat_id' => $chatId,
                        'media' => json_encode($media, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ])
                );
            } finally {
                foreach ($resources as $resource) {
                    fclose($resource);
                }
            }

            $lastResponse = $response;

            if (! $response->successful()) {
                return $response;
            }
        }

        return $lastResponse;
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

    private function telegramAttachmentsEnabled(): bool
    {
        return filter_var(config('services.telegram.send_attachments', true), FILTER_VALIDATE_BOOLEAN) === true;
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
        $timeout = max(5, (int) config('services.telegram.timeout', 30));
        $connectTimeout = max(2, (int) config('services.telegram.connect_timeout', 10));
        $retryTimes = max(1, (int) config('services.telegram.retry_times', 3));
        $retryDelay = max(0, (int) config('services.telegram.retry_delay_ms', 1200));

        $request = Http::timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->retry(
                $retryTimes,
                $retryDelay,
                function (\Throwable $exception): bool {
                    if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                        return true;
                    }

                    $message = strtolower($exception->getMessage());

                    return str_contains($message, 'timed out')
                        || str_contains($message, 'could not connect')
                        || str_contains($message, 'connection reset')
                        || str_contains($message, 'temporarily unavailable');
                },
                false
            );

        $proxy = $this->telegramProxy();

        if ($proxy !== null) {
            $request = $request->withOptions(['proxy' => $proxy]);
        }

        if ($forceWithoutVerifying || ! config('services.telegram.verify_ssl', true)) {
            $request = $request->withoutVerifying();
        }

        return $request->acceptJson();
    }

    /**
     * @return array{http?: string, https?: string}|string|null
     */
    private function telegramProxy()
    {
        $proxy = trim((string) config('services.telegram.proxy', ''));

        if ($proxy !== '') {
            return $proxy;
        }

        $httpProxy = trim((string) config('services.telegram.http_proxy', ''));
        $httpsProxy = trim((string) config('services.telegram.https_proxy', ''));

        $scopedProxy = array_filter(
            [
                'http' => $httpProxy,
                'https' => $httpsProxy,
            ],
            fn (string $value) => $value !== ''
        );

        return $scopedProxy === [] ? null : $scopedProxy;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function telegramPayload(array $payload): array
    {
        $messageThreadId = config('services.telegram.message_thread_id');

        if (filled($messageThreadId)) {
            $payload['message_thread_id'] = (int) $messageThreadId;
        }

        return $payload;
    }

    private function resolveAggregateUploadLimit(Request $request): int
    {
        return $this->isMobileRequest($request)
            ? self::MOBILE_MAX_TOTAL_UPLOAD_BYTES
            : self::MAX_TOTAL_UPLOAD_BYTES;
    }

    private function isMobileRequest(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());

        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match('/android|iphone|ipad|ipod|iemobile|opera mini|mobile/i', $userAgent);
    }

    private function ensureAggregateUploadLimit(mixed $files, string $message, int $maxBytes): void
    {
        if ($this->sumUploadedFileSizes($files) <= $maxBytes) {
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
     * @return array<string, mixed>
     */
    private function successPageData(): array
    {
        return [
            'portalContent' => null,
            'ranks' => collect(),
            'documentRequirements' => collect(),
        ];
    }
}
