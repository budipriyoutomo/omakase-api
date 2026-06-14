<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CampaignContext extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'campaign_contexts';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'name',
        'aliases',
        'mood_keywords',
        'urgency_phrases',
        'color_emotion',
        'composition_hint',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'aliases' => 'array',
            'mood_keywords' => 'array',
            'urgency_phrases' => 'array',
            'color_emotion' => 'array',
            'is_active' => 'boolean',
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