<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandKit extends Model
{
    protected $fillable = [
        'user_id',
        'logo_url',
        'primary_color',
        'accent_color',
        'font_family',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
