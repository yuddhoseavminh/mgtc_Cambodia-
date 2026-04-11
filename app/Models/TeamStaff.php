<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamStaff extends Authenticatable
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
        'username',
        'password',
        'is_active',
        'must_change_password',
        'password_changed_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'sequence_no' => 'integer',
            'documents' => 'array',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
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

    public function displayName(): string
    {
        return trim((string) ($this->name_kh ?: $this->name_latin ?: $this->username ?: 'Staff'));
    }

    public static function buildGeneratedIdNumber(int $seed, ?int $ignoreId = null): string
    {
        $candidate = max(1, $seed);

        while (true) {
            $idNumber = str_pad((string) $candidate, 6, '0', STR_PAD_LEFT);

            $exists = static::query()
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->where('id_number', $idNumber)
                ->exists();

            if (! $exists) {
                return $idNumber;
            }

            $candidate++;
        }
    }

    public static function usernameBase(string $nameLatin): string
    {
        $base = Str::of($nameLatin)
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->limit(120, '')
            ->value();

        return $base !== '' ? $base : 'Staff';
    }

    public static function makeUniqueUsername(string $nameLatin, ?int $ignoreId = null): string
    {
        $base = static::usernameBase($nameLatin);
        $candidate = $base;
        $suffix = 2;

        while (
            static::query()
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->where('username', $candidate)
                ->exists()
        ) {
            $suffixLabel = ' '.$suffix;
            $candidate = Str::of($base)
                ->limit(120 - strlen($suffixLabel), '')
                ->rtrim()
                ->append($suffixLabel)
                ->value();
            $suffix++;
        }

        return $candidate;
    }
}
