<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    public const DOCUMENT_FIELDS = [
        'id_card' => [
            'label' => 'ID Card',
            'path' => 'id_card_path',
            'name' => 'id_card_original_name',
        ],
        'family_book' => [
            'label' => 'Family Book',
            'path' => 'family_book_path',
            'name' => 'family_book_original_name',
        ],
        'certificate' => [
            'label' => 'Certificate',
            'path' => 'certificate_path',
            'name' => 'certificate_original_name',
        ],
        'other_document' => [
            'label' => 'Other Reference Document',
            'path' => 'other_document_path',
            'name' => 'other_document_original_name',
        ],
    ];

    protected $fillable = [
        'khmer_name',
        'latin_name',
        'id_number',
        'rank_id',
        'gender',
        'date_of_birth',
        'date_of_enlistment',
        'position',
        'unit',
        'course_id',
        'cultural_level_id',
        'place_of_birth',
        'current_address',
        'family_situation',
        'phone_number',
        'status',
        'admin_notes',
        'submitted_at',
        'id_card_path',
        'id_card_original_name',
        'family_book_path',
        'family_book_original_name',
        'certificate_path',
        'certificate_original_name',
        'other_document_path',
        'other_document_original_name',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_enlistment' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function culturalLevel(): BelongsTo
    {
        return $this->belongsTo(CulturalLevel::class);
    }

    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function document(string $type): ?array
    {
        $definition = self::DOCUMENT_FIELDS[$type] ?? null;

        if ($definition === null) {
            return null;
        }

        $path = $this->{$definition['path']};

        if (! $path) {
            return null;
        }

        return [
            'type' => $type,
            'label' => $definition['label'],
            'path' => $path,
            'name' => $this->{$definition['name']},
        ];
    }

    public function documents(): array
    {
        $legacyDocuments = collect(self::DOCUMENT_FIELDS)
            ->keys()
            ->map(fn (string $type) => $this->document($type))
            ->filter()
            ->values();

        $managedDocuments = $this->relationLoaded('applicationDocuments')
            ? $this->applicationDocuments
            : $this->applicationDocuments()->with('documentRequirement')->get();

        return $managedDocuments
            ->map(function (ApplicationDocument $document) {
                if (! $document->documentRequirement) {
                    return null;
                }

                return [
                    'id' => $document->id,
                    'type' => $document->documentRequirement->slug,
                    'label' => $document->documentRequirement->name_kh
                        ?: $document->documentRequirement->name_en,
                    'path' => $document->file_path,
                    'name' => $document->original_name,
                    'status' => $document->status,
                    'source' => 'managed',
                ];
            })
            ->filter(fn (?array $document) => $document !== null && $document['status'] === ApplicationDocument::STATUS_HAVE && ! empty($document['path']))
            ->values()
            ->merge(
                $legacyDocuments->map(fn (array $document) => [...$document, 'source' => 'legacy'])
            )
            ->all();
    }
}
