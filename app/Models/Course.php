<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    public const PROTECTED_DEFAULT_COURSE_NAME = 'បញ្ញីគល់ទទឹង Broad-leaved tree';

    protected $fillable = [
        'name',
        'description',
        'duration',
        'is_active',
        'is_protected',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_protected' => 'boolean',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isProtectedCourse(): bool
    {
        return (bool) $this->is_protected;
    }
}
