<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRequirement extends Model
{
    use HasFactory;

    public const PROTECTED_TELEGRAM_REQUIREMENT_SLUG = 'broad-leaved-tree';

    protected $fillable = [
        'name_kh',
        'name_en',
        'slug',
        'sort_order',
        'is_active',
        'is_protected',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_protected' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name_en');
    }

    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function isProtectedRequirement(): bool
    {
        return (bool) $this->is_protected;
    }
}
