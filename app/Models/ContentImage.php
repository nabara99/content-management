<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentImage extends Model
{
    protected $fillable = ['content_id', 'image', 'slot_number', 'offset_x', 'offset_y', 'scale'];

    protected $casts = [
        'offset_x' => 'float',
        'offset_y' => 'float',
        'scale' => 'float',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
