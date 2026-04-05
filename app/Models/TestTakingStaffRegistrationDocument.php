<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestTakingStaffRegistrationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_taking_staff_registration_id',
        'test_taking_staff_document_requirement_id',
        'file_path',
        'original_name',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(TestTakingStaffRegistration::class, 'test_taking_staff_registration_id');
    }

    public function documentRequirement(): BelongsTo
    {
        return $this->belongsTo(TestTakingStaffDocumentRequirement::class, 'test_taking_staff_document_requirement_id');
    }
}
