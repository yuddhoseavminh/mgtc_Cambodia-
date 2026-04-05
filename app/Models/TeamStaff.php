<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamStaff extends Model
{
    use HasFactory;

    protected $table = 'team_staff';

    protected $fillable = [
        'sequence_no',
        'military_rank',
        'name_kh',
        'name_latin',
        'id_number',
        'avatar_path',
        'avatar_original_name',
        'gender',
        'position',
        'role',
        'phone_number',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'sequence_no' => 'integer',
            'documents' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sequence_no')->orderBy('name_latin');
    }

    public function hasStoredAvatar(): bool
    {
        return filled($this->avatar_path) && Storage::disk('local')->exists($this->avatar_path);
    }
}
