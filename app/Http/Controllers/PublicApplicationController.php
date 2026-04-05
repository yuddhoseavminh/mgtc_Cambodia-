<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\DocumentRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicApplicationController extends Controller
{
    private const MAX_TOTAL_UPLOAD_BYTES = 41943040;

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $documentRequirements = DocumentRequirement::query()
            ->where('is_active', true)
            ->ordered()
            ->get();

        $rules = [
            'khmer_name' => ['required', 'string', 'max:255'],
            'latin_name' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:100'],
            'rank_id' => ['required', Rule::exists('ranks', 'id')],
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
            $rules["{$fileKey}.*"] = ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:20480'];
        }

        $validated = $request->validate($rules);
        $this->ensureAggregateUploadLimit(
            $request->file('document_files', []),
            'ទំហំឯកសារសរុបធំពេក។ សូមរក្សាទំហំឯកសារនីមួយៗក្រោម 20 MB និងទំហំសរុបក្រោម 40 MB។',
        );

        $folder = 'applications/'.Str::uuid();

        $application = DB::transaction(function () use ($validated, $request, $folder, $documentRequirements) {
            $application = Application::create([
                ...$validated,
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

        $this->sendTelegramRegistrationNotification($application);

        if ($request->expectsJson()) {
            return response()->json([
                'status_title' => 'áž€áž¶ážšáž…áž»áŸ‡ážˆáŸ’áž˜áŸ„áŸ‡áž‡áŸ„áž‚áž‡áŸáž™',
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

        $path = $file->storeAs(
            $folder,
            $documentRequirement->slug.'-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
            'local',
        );

        return [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    private function sendTelegramRegistrationNotification(Application $application): void
    {
        if (! config('services.telegram.enabled', false)) {
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

        try {
            $document = $this->protectedRegistrationDocument($application);

            $response = $document
                ? $this->sendTelegramRegistrationMedia($application, $document, $chatId, $botToken)
                : $this->telegramRequest()
                    ->asForm()
                    ->post(
                        "https://api.telegram.org/bot{$botToken}/sendMessage",
                        [
                            'chat_id' => $chatId,
                            'text' => $this->buildTelegramRegistrationMessage($application),
                        ]
                    );

            if ($response->failed()) {
                Log::warning('Telegram registration notification failed.', [
                    'application_id' => $application->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Telegram registration notification threw an exception.', [
                'application_id' => $application->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function buildTelegramRegistrationMessage(Application $application): string
    {
        $document = $this->protectedRegistrationDocument($application);

        return implode("\n", [
            'បានចុះឈ្មោះចូលរៀនវគ្គ '.($application->course?->name ?? '-'),
            $this->buildKhmerDateLine(),
            $this->buildKhmerTimeLine(),
            'គោត្តនាម-នាម: '.$application->khmer_name,
            'ឋានន្តរស័ក្តិ : '.($application->rank?->name_kh ?? '-'),
            'អត្តលេខ : '.$application->id_number,
            'អង្គភាព : '.$application->unit,
            'លេខទូរស័ព្ទ : '.$application->phone_number,
            'ឯកសារ ភ្ជាប់មកជាមួយ : '.$this->buildTelegramDocumentLabel($document),
        ]);
    }

    private function buildTelegramDocumentLabel(?ApplicationDocument $document): string
    {
        if (! $document || ! $document->documentRequirement) {
            return 'មិនមាន';
        }

        return $document->documentRequirement->name_kh;
    }

    private function protectedRegistrationDocument(Application $application): ?ApplicationDocument
    {
        return $application->applicationDocuments
            ->filter(fn (ApplicationDocument $document) => $document->status === ApplicationDocument::STATUS_HAVE
                && ! empty($document->file_path)
                && $document->documentRequirement?->isProtectedRequirement())
            ->first();
    }

    private function sendTelegramRegistrationMedia(Application $application, ApplicationDocument $document, string $chatId, string $botToken)
    {
        $path = Storage::disk('local')->path($document->file_path);

        if (! is_file($path)) {
            Log::warning('Telegram registration document missing on disk.', [
                'application_id' => $application->id,
                'document_id' => $document->id,
                'file_path' => $document->file_path,
            ]);

            return $this->telegramRequest()
                ->asForm()
                ->post(
                    "https://api.telegram.org/bot{$botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text' => $this->buildTelegramRegistrationMessage($application),
                    ]
                );
        }

        return $this->telegramRequest()
            ->attach('document', fopen($path, 'r'), $document->original_name ?? basename($path))
            ->post(
                "https://api.telegram.org/bot{$botToken}/sendDocument",
                [
                    'chat_id' => $chatId,
                    'caption' => $this->buildTelegramRegistrationMessage($application),
                ]
            );
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

        return 'ថ្ងៃ ទី'.$this->toKhmerDigits($now->format('d')).' ខែ'.$months[(int) $now->format('n')].' ឆ្នាំ'.$this->toKhmerDigits($now->format('Y'));
    }

    private function buildKhmerTimeLine(): string
    {
        return 'ម៉ោង '.$this->toKhmerDigits(now('Asia/Phnom_Penh')->format('H:i'));
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

    private function telegramRequest()
    {
        $request = Http::timeout(30);

        if (! config('services.telegram.verify_ssl', true)) {
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
}
