<?php

namespace App\Http\Controllers;

use App\Jobs\SendApplicationTelegramNotification;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\DocumentRequirement;
use App\Models\Rank;
use App\Support\UploadStorage;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicApplicationController extends Controller
{
    private const MAX_TOTAL_UPLOAD_BYTES = 52428800;
    private const MOBILE_MAX_TOTAL_UPLOAD_BYTES = 52428800;
    private const MAX_SINGLE_FILE_UPLOAD_KILOBYTES = 15360;
    private const TOTAL_UPLOAD_ERROR = 'Total upload size is too large. Keep each file at or below 15 MB and total uploads at or below 50 MB.';

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $documentRequirements = DocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->get();

        $payload = $request->all();

        if (($payload['rank_id'] ?? null) === '__custom__') {
            $payload['rank_id'] = null;
        }

        $rules = [
            'khmer_name' => ['required', 'string', 'max:255'],
            'latin_name' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:100'],
            'rank_id' => ['nullable', Rule::exists('ranks', 'id')],
            'rank_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(config('military-registration.genders'))],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'date_of_enlistment' => ['required', 'date', 'before_or_equal:today'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'course_id' => ['required', Rule::exists('courses', 'id')],
            'cultural_level_id' => ['required', Rule::exists('cultural_levels', 'id')],
            'place_of_birth' => ['required', Rule::in(config('military-registration.provinces'))],
            'current_address' => ['required', 'string', 'max:1000'],
            'family_situation' => ['required', Rule::in(config('military-registration.family_situations'))],
            'phone_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
        ];

        foreach ($documentRequirements as $documentRequirement) {
            $statusKey = "document_statuses.{$documentRequirement->id}";
            $fileKey = "document_files.{$documentRequirement->id}";

            $rules[$statusKey] = ['required', Rule::in([ApplicationDocument::STATUS_HAVE, ApplicationDocument::STATUS_DONT_HAVE])];
            $rules[$fileKey] = [
                Rule::requiredIf(fn () => $request->input("document_statuses.{$documentRequirement->id}") === ApplicationDocument::STATUS_HAVE),
                'nullable',
                'array',
                'min:1',
            ];
            $rules["{$fileKey}.*"] = ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,webp', 'max:'.self::MAX_SINGLE_FILE_UPLOAD_KILOBYTES];
        }

        $validator = validator($payload, $rules);

        $validator->after(function ($validator) use ($payload) {
            $rankId = $payload['rank_id'] ?? null;
            $rankName = trim((string) ($payload['rank_name'] ?? ''));

            if ($rankId || $rankName !== '') {
                return;
            }

            $validator->errors()->add('rank_id', 'សូមជ្រើសរើស ឬ បញ្ចូលឋានន្តរសក្តិ។');
        });

        $validated = $validator->validate();
        $validated['rank_name'] = trim((string) ($validated['rank_name'] ?? ''));
        $validated['rank_id'] = $this->resolveRankId($validated);
        $aggregateUploadLimit = $this->resolveAggregateUploadLimit($request);
        $this->ensureAggregateUploadLimit(
            $request->file('document_files', []),
            self::TOTAL_UPLOAD_ERROR,
            $aggregateUploadLimit,
        );

        $folder = 'applications/'.Str::uuid();

        $application = DB::transaction(function () use ($validated, $request, $folder, $documentRequirements) {
            $applicationPayload = $validated;
            unset($applicationPayload['rank_name']);

            $application = Application::create([
                ...$applicationPayload,
                'status' => 'Pending',
                'submitted_at' => now(),
            ]);

                $application->applicationDocuments()->createMany(
                    $documentRequirements->flatMap(function (DocumentRequirement $documentRequirement) use ($request, $folder) {
                        $status = $request->input("document_statuses.{$documentRequirement->id}");
                        $files = $request->file("document_files.{$documentRequirement->id}", []);

                        if ($status !== ApplicationDocument::STATUS_HAVE && $documentRequirement->isProtectedRequirement()) {
                            return [];
                        }

                        if ($status !== ApplicationDocument::STATUS_HAVE || empty($files)) {
                            return [[
                                'document_requirement_id' => $documentRequirement->id,
                            'status' => $status,
                            'file_path' => null,
                            'original_name' => null,
                        ]];
                    }

                    return collect($files)->map(function (UploadedFile $file) use ($folder, $documentRequirement) {
                        return [
                            'document_requirement_id' => $documentRequirement->id,
                            'status' => ApplicationDocument::STATUS_HAVE,
                            ...$this->storeApplicationDocument($file, $folder, $documentRequirement),
                        ];
                    })->all();
                })->all()
            );

            return $application;
        });

        $this->queueTelegramRegistrationNotification($application);

        if ($request->expectsJson()) {
            return response()->json([
                'status_title' => 'ការចុះឈ្មោះជោគជ័យ',
                'message' => 'អ្នកបានចុះឈ្មោះដោយជោគជ័យ សំណាងល្អ ជួបគ្នាឆាប់ៗ។',
            ], 201);
        }

        return redirect()
            ->route('registration.form')
            ->with('status_title', 'ការចុះឈ្មោះជោគជ័យ')
            ->with('status', 'អ្នកបានចុះឈ្មោះដោយជោគជ័យ សំណាងល្អ ជួបគ្នាឆាប់ៗ។');
    }

    /**
     * @return array<string, string|null>
     */
    private function storeApplicationDocument(?UploadedFile $file, string $folder, DocumentRequirement $documentRequirement): array
    {
        if (! $file) {
            return [
                'file_path' => null,
                'original_name' => null,
            ];
        }

        $path = UploadStorage::storeAs(
            $file,
            $folder,
            $documentRequirement->slug.'-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
        );

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    private function queueTelegramRegistrationNotification(Application $application): void
    {
        if (! $this->telegramNotificationsEnabled()) {
            return;
        }

        try {
            $pendingDispatch = SendApplicationTelegramNotification::dispatch($application)->afterResponse();
            unset($pendingDispatch);
        } catch (\Throwable $exception) {
            Log::warning('Unable to queue Telegram registration notification.', [
                'application_id' => $application->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function sendTelegramRegistrationNotification(Application $application): void
    {
        if (! $this->telegramNotificationsEnabled()) {
            return;
        }

        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $botToken || ! $chatId) {
            return;
        }

        $application->loadMissing([
            'course:id,name',
            'rank:id,name_kh,name_en',
            'applicationDocuments.documentRequirement:id,name_kh,name_en,slug,is_protected',
        ]);

        $message = $this->buildTelegramRegistrationMessage($application);

        try {
            $result = $this->dispatchTelegramRegistrationNotification($application, $chatId, $botToken, $message);
            $response = $result['primary'];
            $fallbackResponse = $result['fallback'];

            if ($response?->successful() || $fallbackResponse?->successful()) {
                return;
            }

            Log::warning('Telegram registration notification failed.', [
                'application_id' => $application->id,
                'status' => $response?->status(),
                'response' => $response?->body(),
                'fallback_status' => $fallbackResponse?->status(),
                'fallback_response' => $fallbackResponse?->body(),
            ]);
        } catch (\Throwable $exception) {
            $sslRetryPrimary = null;
            $sslRetryFallback = null;
            $exceptionTextFallback = null;

            if ($this->isTelegramSslException($exception)) {
                try {
                    $retryResult = $this->dispatchTelegramRegistrationNotification(
                        $application,
                        $chatId,
                        $botToken,
                        $message,
                        true
                    );

                    $sslRetryPrimary = $retryResult['primary'];
                    $sslRetryFallback = $retryResult['fallback'];

                    if ($sslRetryPrimary?->successful() || $sslRetryFallback?->successful()) {
                        return;
                    }
                } catch (\Throwable $retryException) {
                    Log::warning('Telegram registration SSL retry threw an exception.', [
                        'application_id' => $application->id,
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
                    Log::warning('Telegram registration media notification failed, but text fallback succeeded.', [
                        'application_id' => $application->id,
                        'message' => $exception->getMessage(),
                    ]);

                    return;
                }
            } catch (\Throwable $fallbackException) {
                Log::warning('Telegram registration exception text fallback threw an exception.', [
                    'application_id' => $application->id,
                    'message' => $fallbackException->getMessage(),
                ]);
            }

            Log::warning('Telegram registration notification threw an exception.', [
                'application_id' => $application->id,
                'message' => $exception->getMessage(),
                'ssl_retry_status' => $sslRetryPrimary?->status(),
                'ssl_retry_response' => $sslRetryPrimary?->body(),
                'ssl_retry_fallback_status' => $sslRetryFallback?->status(),
                'ssl_retry_fallback_response' => $sslRetryFallback?->body(),
                'exception_text_fallback_status' => $exceptionTextFallback?->status(),
                'exception_text_fallback_response' => $exceptionTextFallback?->body(),
            ]);
        }
    }

    /**
     * @return array{primary: HttpResponse|null, fallback: HttpResponse|null}
     */
    private function dispatchTelegramRegistrationNotification(
        Application $application,
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

        $preferredDocuments = $this->preferredRegistrationDocuments($application);

        if ($preferredDocuments->isNotEmpty()) {
            return $this->sendTelegramRegistrationMediaDocuments(
                $application,
                $preferredDocuments,
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

        $fallbackResponse = $this->sendTelegramFallbackDocument($application, $chatId, $botToken, $message, $forceWithoutVerifying);

        return ['primary' => $primaryResponse, 'fallback' => $fallbackResponse];
    }

    /**
     * @param  Collection<int, ApplicationDocument>  $documents
     * @return array{primary: HttpResponse|null, fallback: HttpResponse|null}
     */
    private function sendTelegramRegistrationMediaDocuments(
        Application $application,
        Collection $documents,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): array {
        $primaryResponse = $this->sendTelegramRegistrationMediaGroup(
            $application,
            $documents,
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

    private function buildTelegramRegistrationMessage(Application $application): string
    {
        $document = $this->preferredRegistrationDocument($application);

        return implode("\n", [
            '* សិក្ខាកាមបានចុះឈ្មោះចូលរៀនវគ្គ៖'."\n".'» '.($application->course?->name ?? '-'),
            $this->buildKhmerDateLine(),
            $this->buildKhmerTimeLine(),
            '- គោត្តនាម-នាម: '.$application->khmer_name,
            '- ឋានន្តរស័ក្តិ : '.($application->rank?->name_kh ?? '-'),
            '- អត្តលេខ : '.$application->id_number,
            '- អង្គភាព : '.$application->unit,
            '- លេខទូរស័ព្ទ : '.$application->phone_number,
            '- ឯកសារ ភ្ជាប់មកជាមួយ : '.$this->buildTelegramDocumentLabel($document),
        ]);
    }

    private function buildTelegramDocumentLabel(?ApplicationDocument $document): string
    {
        if (! $document || ! $document->documentRequirement) {
            return 'មិនមាន';
        }

        return $document->documentRequirement->name_kh;
    }

    private function preferredRegistrationDocument(Application $application): ?ApplicationDocument
    {
        return $this->preferredRegistrationDocuments($application)->first();
    }

    /**
     * @return Collection<int, ApplicationDocument>
     */
    private function preferredRegistrationDocuments(Application $application): Collection
    {
        $documents = $application->applicationDocuments
            ->filter(fn (ApplicationDocument $document) => $document->status === ApplicationDocument::STATUS_HAVE
                && filled($document->file_path)
                && $document->documentRequirement
                && UploadStorage::exists($document->file_path));

        $telegramDocuments = $documents
            ->filter(fn (ApplicationDocument $document) => $document->documentRequirement?->isProtectedRequirement());

        if ($telegramDocuments->isNotEmpty()) {
            return $this->sortRegistrationDocuments($telegramDocuments);
        }

        return $this->sortRegistrationDocuments($documents)->take(1)->values();
    }

    /**
     * @param  Collection<int, ApplicationDocument>  $documents
     * @return Collection<int, ApplicationDocument>
     */
    private function sortRegistrationDocuments(Collection $documents): Collection
    {
        return $documents
            ->sort(function (ApplicationDocument $left, ApplicationDocument $right) {
                $leftProtected = $left->documentRequirement?->isProtectedRequirement() ? 0 : 1;
                $rightProtected = $right->documentRequirement?->isProtectedRequirement() ? 0 : 1;

                if ($leftProtected !== $rightProtected) {
                    return $leftProtected <=> $rightProtected;
                }

                $leftSortOrder = (int) ($left->documentRequirement?->sort_order ?? PHP_INT_MAX);
                $rightSortOrder = (int) ($right->documentRequirement?->sort_order ?? PHP_INT_MAX);

                if ($leftSortOrder !== $rightSortOrder) {
                    return $leftSortOrder <=> $rightSortOrder;
                }

                return (int) $left->id <=> (int) $right->id;
            })
            ->values();
    }

    private function sendTelegramRegistrationMedia(
        Application $application,
        ApplicationDocument $document,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): HttpResponse {
        $storedPath = (string) $document->file_path;
        $resource = $this->openUploadReadStream($storedPath);

        if (! is_resource($resource)) {
            Log::warning('Telegram registration document missing on disk.', [
                'application_id' => $application->id,
                'document_id' => $document->id,
                'file_path' => $document->file_path,
            ]);

            return $this->sendTelegramTextMessage($chatId, $botToken, $message, $forceWithoutVerifying);
        }

        $filename = $document->original_name ?? basename($storedPath);

        try {
            return $this->telegramRequest($forceWithoutVerifying)
                ->attach('document', $resource, $filename)
                ->post(
                    "https://api.telegram.org/bot{$botToken}/sendDocument",
                    $this->telegramPayload([
                        'chat_id' => $chatId,
                        'caption' => Str::limit($message, 900, '...'),
                    ])
                );
        } finally {
            $this->closeUploadReadStream($resource);
        }
    }

    /**
     * @param  Collection<int, ApplicationDocument>  $documents
     */
    private function sendTelegramRegistrationMediaGroup(
        Application $application,
        Collection $documents,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): ?HttpResponse {
        $documents = $documents->values();

        if ($documents->isEmpty()) {
            return null;
        }

        if ($documents->count() === 1) {
            return $this->sendTelegramRegistrationMedia(
                $application,
                $documents->first(),
                $chatId,
                $botToken,
                $message,
                $forceWithoutVerifying
            );
        }

        $lastResponse = null;
        $captionPending = true;
        $caption = Str::limit($message, 900, '...');

        foreach ($documents->chunk(10) as $chunk) {
            $request = $this->telegramRequest($forceWithoutVerifying);
            $media = [];
            $resources = [];
            $validIndex = 0;

            foreach ($chunk as $document) {
                $storedPath = (string) $document->file_path;
                $resource = $this->openUploadReadStream($storedPath);

                if (! is_resource($resource)) {
                    Log::warning('Telegram registration document missing on disk.', [
                        'application_id' => $application->id,
                        'document_id' => $document->id,
                        'file_path' => $document->file_path,
                    ]);
                    continue;
                }

                $attachName = 'document_'.$validIndex;
                $request = $request->attach($attachName, $resource, $document->original_name ?? basename($storedPath));

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
                    $this->closeUploadReadStream($resource);
                }
            }

            $lastResponse = $response;

            if (! $response->successful()) {
                return $response;
            }
        }

        return $lastResponse;
    }

    private function sendTelegramTextMessage(
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): HttpResponse {
        return $this->telegramRequest($forceWithoutVerifying)
            ->asForm()
            ->post(
                "https://api.telegram.org/bot{$botToken}/sendMessage",
                $this->telegramPayload([
                    'chat_id' => $chatId,
                    'text' => $message,
                ])
            );
    }

    private function sendTelegramFallbackDocument(
        Application $application,
        string $chatId,
        string $botToken,
        string $message,
        bool $forceWithoutVerifying = false
    ): ?HttpResponse {
        $fallbackDocument = $this->preferredRegistrationDocument($application);

        if (! $fallbackDocument) {
            return null;
        }

        return $this->sendTelegramRegistrationMedia(
            $application,
            $fallbackDocument,
            $chatId,
            $botToken,
            $message,
            $forceWithoutVerifying
        );
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

    private function buildKhmerDateLine(): string
    {
        $now = now('Asia/Phnom_Penh');
        $months = [
            1 => 'មករា',
            2 => 'កុម្ភៈ',
            3 => 'មីនា',
            4 => 'មេសា',
            5 => 'ឧសភា',
            6 => 'មិថុនា',
            7 => 'កក្កដា',
            8 => 'សីហា',
            9 => 'កញ្ញា',
            10 => 'តុលា',
            11 => 'វិច្ឆិកា',
            12 => 'ធ្នូ',
        ];

        return 'ថ្ងៃទី'.$this->toKhmerDigits($now->format('d')).' ខែ'.$months[(int) $now->format('n')].' ឆ្នាំ'.$this->toKhmerDigits($now->format('Y'));
    }

    private function buildKhmerTimeLine(): string
    {
        return 'ម៉ោង '.$this->toKhmerDigits(now('Asia/Phnom_Penh')->format('h:i')).' '.now('Asia/Phnom_Penh')->format('A');
    }

    private function toKhmerDigits(string $value): string
    {
        return strtr($value, [
            '0' => '០',
            '1' => '១',
            '2' => '២',
            '3' => '៣',
            '4' => '៤',
            '5' => '៥',
            '6' => '៦',
            '7' => '៧',
            '8' => '៨',
            '9' => '៩',
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

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveRankId(array $validated): int
    {
        if (! empty($validated['rank_id'])) {
            return (int) $validated['rank_id'];
        }

        $rankName = trim((string) ($validated['rank_name'] ?? ''));

        $existingRank = Rank::query()
            ->where('name_kh', $rankName)
            ->orWhereRaw('LOWER(name_en) = ?', [Str::lower($rankName)])
            ->first();

        if ($existingRank) {
            if (! $existingRank->is_active) {
                $existingRank->forceFill(['is_active' => true])->save();
            }

            return $existingRank->id;
        }

        $rank = Rank::create([
            'name_kh' => $rankName,
            'name_en' => $rankName,
            'sort_order' => $this->nextRankSortOrder(),
            'is_active' => true,
        ]);

        return $rank->id;
    }

    private function nextRankSortOrder(): int
    {
        return ((int) Rank::query()->max('sort_order')) + 1;
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
     * @return resource|null
     */
    private function openUploadReadStream(?string $path)
    {
        if (! filled($path) || ! UploadStorage::exists($path)) {
            return null;
        }

        $stream = UploadStorage::readDisk($path)->readStream($path);

        return is_resource($stream) ? $stream : null;
    }

    private function closeUploadReadStream(mixed $stream): void
    {
        if (! is_resource($stream)) {
            return;
        }

        try {
            fclose($stream);
        } catch (\Throwable) {
            // The HTTP client may already close the stream. Ignore close failures.
        }
    }
}

