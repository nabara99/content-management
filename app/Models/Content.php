<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'title', 'caption', 'final_image', 'status',
        'title_font_family', 'title_font_size', 'title_font_bold',
        'title_font_italic', 'title_font_underline', 'title_font_color',
        'caption_font_family', 'caption_font_size', 'caption_font_bold',
        'caption_font_italic', 'caption_font_underline', 'caption_font_color',
        'title_x_percent', 'title_y_percent', 'caption_x_percent', 'caption_y_percent',
    ];

    protected function casts(): array
    {
        return [
            'title_font_size' => 'integer',
            'title_font_bold' => 'boolean',
            'title_font_italic' => 'boolean',
            'title_font_underline' => 'boolean',
            'caption_font_size' => 'integer',
            'caption_font_bold' => 'boolean',
            'caption_font_italic' => 'boolean',
            'caption_font_underline' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ContentImage::class)->orderBy('slot_number');
    }
}
