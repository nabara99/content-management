<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    protected $fillable = ['name', 'image', 'status', 'title_x_percent', 'title_y_percent', 'caption_x_percent', 'caption_y_percent'];

    public function slots(): HasMany
    {
        return $this->hasMany(TemplateSlot::class)->orderBy('slot_number');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function getSlotCountAttribute(): int
    {
        return $this->relationLoaded('slots') ? $this->slots->count() : $this->slots()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image);
    }
}
