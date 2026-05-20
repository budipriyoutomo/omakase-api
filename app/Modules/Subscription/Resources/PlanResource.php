<?php

declare(strict_types=1);

namespace App\Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SubscriptionPlan
 */
class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'priceUsdMonthly'  => $this->price_usd_monthly,
            'creditsMonthly'   => $this->credits_monthly,
            'description'      => $this->description,
        ];
    }
}
