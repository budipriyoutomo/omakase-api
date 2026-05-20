<?php

declare(strict_types=1);

namespace App\Modules\Subscription\Services;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService
{
    public function getActivePlans(): Collection
    {
        return SubscriptionPlan::where('is_active', true)->orderBy('price_usd_monthly')->get();
    }

    public function getUserSubscription(int $userId): array
    {
        $subscription = UserSubscription::with('plan')->where('user_id', $userId)->first();

        if (!$subscription) {
            return [
                'planId' => null,
                'status' => 'none',
                'currentPeriodEndsAt' => null,
            ];
        }

        return [
            'planId' => $subscription->plan_id,
            'status' => $subscription->status,
            'currentPeriodEndsAt' => $subscription->current_period_ends_at?->toIso8601String(),
        ];
    }
}
