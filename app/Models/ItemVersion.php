<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ItemVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'title',
        'description',
        'price',
        'image_path',
        'image_filename',
        'version_no',
        'is_current',
        'updated_by',
        'change_note',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'version_no' => 'integer',
        'is_current' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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
