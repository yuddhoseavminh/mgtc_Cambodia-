<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    public const STATUS_HAVE = 'have';

    public const STATUS_DONT_HAVE = 'dont_have';

    protected $fillable = [
        'application_id',
        'document_requirement_id',
        'status',
        'file_path',
        'original_name',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function documentRequirement(): BelongsTo
    {
        return $this->belongsTo(DocumentRequirement::class);
    }
}
