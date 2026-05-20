<?php

declare(strict_types=1);

namespace App\Modules\Subscription\Controllers;

use App\Modules\Subscription\Resources\PlanResource;
use App\Modules\Subscription\Resources\SubscriptionResource;
use App\Modules\Subscription\Services\SubscriptionService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly SubscriptionService $service) {}

    // GET /v1/subscription/plans  (public)
    public function plans(): JsonResponse
    {
        $plans = $this->service->getActivePlans();

        return $this->success(PlanResource::collection($plans));
    }

    // GET /v1/subscription
    public function show(): JsonResponse
    {
        $user         = JWTAuth::parseToken()->authenticate();
        $subscription = $this->service->getUserSubscription($user->id);

        return $this->success(new SubscriptionResource($subscription));
    }

    // POST /v1/subscription/checkout
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'planId'     => ['required', 'string'],
            'successUrl' => ['nullable', 'url'],
            'cancelUrl'  => ['nullable', 'url'],
        ]);

        // Dummy response — replace with Stripe integration
        return $this->success([
            'checkoutUrl' => null,
            'message'     => 'Payment integration coming soon.',
        ]);
    }

    // POST /v1/subscription/billing-portal
    public function billingPortal(): JsonResponse
    {
        // Dummy response — replace with Stripe Billing Portal
        return $this->success([
            'url' => null,
            'message' => 'Billing portal coming soon.',
        ]);
    }
}
