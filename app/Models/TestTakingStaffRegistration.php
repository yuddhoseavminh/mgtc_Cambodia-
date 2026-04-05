<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class TestTakingStaffRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_taking_staff_rank_id',
        'name_kh',
        'name_latin',
        'date_of_birth',
        'military_service_day',
        'phone_number',
        'avatar_path',
        'avatar_original_name',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'military_service_day' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(TestTakingStaffRank::class, 'test_taking_staff_rank_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TestTakingStaffRegistrationDocument::class);
    }

    public function hasStoredAvatar(): bool
    {
        return filled($this->avatar_path) && Storage::disk('local')->exists($this->avatar_path);
    }
}
