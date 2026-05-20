<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'current_period_ends_at',
        'checkout_session_id',
    ];

    protected $casts = [
        'current_period_ends_at' => 'datetime',
    ];

    const STATUS_ACTIVE    = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAST_DUE  = 'past_due';
    const STATUS_TRIALING  = 'trialing';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
