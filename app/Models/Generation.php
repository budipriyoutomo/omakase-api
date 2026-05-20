<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Generation extends Model
{
    use HasUuids;

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'generations';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        |--------------------------------------------------------------------------
        | RELATION
        |--------------------------------------------------------------------------
        */

        'user_id',

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        'status',

        /*
        |--------------------------------------------------------------------------
        | CAMPAIGN
        |--------------------------------------------------------------------------
        */

        'campaign_type',

        'cuisine',

        'platform',

        'audience',

        'goal',

        'mood',

        'style',

        'hero_item',

        'visual_strategy',

        'cta_strategy',

        'aspect_ratio',

        /*
        |--------------------------------------------------------------------------
        | PROMPTS
        |--------------------------------------------------------------------------
        */

        'prompt',

        'enhanced_prompt',

        'negative_prompt',

        /*
        |--------------------------------------------------------------------------
        | AI ORCHESTRATION
        |--------------------------------------------------------------------------
        */

        'orchestration',

        /*
        |--------------------------------------------------------------------------
        | AI ENGINE
        |--------------------------------------------------------------------------
        */

        'agent',

        'provider',

        'model',

        /*
        |--------------------------------------------------------------------------
        | RESULTS
        |--------------------------------------------------------------------------
        */

        'result',

        'image_url',

        'image_urls',

        'preview_urls',

        /*
        |--------------------------------------------------------------------------
        | ANALYTICS
        |--------------------------------------------------------------------------
        */

        'tokens_used',

        'cost',

        'generation_time_ms',

        /*
        |--------------------------------------------------------------------------
        | METADATA
        |--------------------------------------------------------------------------
        */

        'metadata',

        'ai_metadata',

        'raw_response',
    ];

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        'metadata' => 'array',

        'ai_metadata' => 'array',

        'preview_urls' => 'array',

        'image_urls' => 'array',

        'orchestration' => 'array',

        /*
        |--------------------------------------------------------------------------
        | NUMBERS
        |--------------------------------------------------------------------------
        */

        'cost' => 'decimal:4',

        'tokens_used' => 'integer',

        'generation_time_ms' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | DEFAULT ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $attributes = [

        'status' => self::STATUS_PENDING,

        'metadata' => '[]',

        'ai_metadata' => '[]',

        'orchestration' => '[]',
    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS CONSTANTS
    |--------------------------------------------------------------------------
    */

    public const STATUS_PENDING =
        'pending';

    public const STATUS_PROCESSING =
        'processing';

    public const STATUS_COMPLETED =
        'completed';

    public const STATUS_FAILED =
        'failed';

    public const STATUS_GENERATED_IMAGE =
        'generated_image';
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForUser(
        Builder $query,
        int $userId
    ): Builder {

        return $query->where(
            'user_id',
            $userId
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_COMPLETED
        );
    }

    public function scopePending(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopeFailed(
        Builder $query
    ): Builder {

        return $query->where(
            'status',
            self::STATUS_FAILED
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status ===
            self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->status ===
            self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status ===
            self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status ===
            self::STATUS_FAILED;
    }
}
