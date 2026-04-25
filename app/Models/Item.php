<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'image_path',
        'image_filename',
        'version_no',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'version_no' => 'integer',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(ItemVersion::class)->latest('version_no');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(ItemVersion::class)->where('is_current', true);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}
