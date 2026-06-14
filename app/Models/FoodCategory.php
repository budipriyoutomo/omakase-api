<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FoodCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'food_categories';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'name',
        'aliases',
        'visual_keywords',
        'texture_descriptors',
        'lighting_preset',
        'camera_angle',
        'plating_style',
        'color_palette',
        'avoid',
        'cuisine_affinity',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'visual_keywords' => 'array',
            'texture_descriptors' => 'array',
            'color_palette' => 'array',
            'avoid' => 'array',
            'cuisine_affinity' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function matchesInput(string $input): bool
    {
        $normalized = Str::lower(trim($input));

        if (Str::contains($normalized, $this->key)) {
            return true;
        }

        foreach ($this->aliases as $alias) {
            if (Str::contains($normalized, Str::lower($alias))) {
                return true;
            }
        }

        return false;
    }
}