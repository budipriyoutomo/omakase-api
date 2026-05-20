<?php

declare(strict_types=1);

namespace App\Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this->resource is the raw array from SubscriptionService::getUserSubscription
        return is_array($this->resource) ? $this->resource : parent::toArray($request);
    }
}
