<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateSlot extends Model
{
    protected $fillable = [
        'template_id',
        'slot_number',
        'x_percent',
        'y_percent',
        'width_percent',
        'height_percent',
    ];

    protected $casts = [
        'x_percent' => 'float',
        'y_percent' => 'float',
        'width_percent' => 'float',
        'height_percent' => 'float',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
