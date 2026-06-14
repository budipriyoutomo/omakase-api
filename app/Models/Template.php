<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'category',
        'style',
        'description',
        'payload',
        'is_trending',
    ];

    protected $casts = [
        'is_trending' => 'boolean',
        'payload' => 'array',
    ];

    public function scopeTrending(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_trending', true);
    }
}