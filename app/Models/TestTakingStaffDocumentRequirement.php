<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestTakingStaffDocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_kh',
        'name_en',
        'slug',
        'sort_order',
        'is_active',
        'send_to_telegram',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'send_to_telegram' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name_en');
    }

    public function registrationDocuments(): HasMany
    {
        return $this->hasMany(TestTakingStaffRegistrationDocument::class);
    }
}
