<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuisineStyle extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'cuisine_styles';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'name',
        'plating_keywords',
        'props',
        'color_mood',
        'lighting',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'plating_keywords' => 'array',
            'props' => 'array',
            'color_mood' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}