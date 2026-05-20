<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Controllers;

use App\Modules\Analytics\Services\AnalyticsService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AnalyticsController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly AnalyticsService $service) {}

    // GET /v1/analytics/summary?range=30d
    public function summary(Request $request): JsonResponse
    {
        $range = $request->input('range', '30d');
        $user  = JWTAuth::parseToken()->authenticate();

        return $this->success($this->service->getSummary($user->id, $range));
    }

    // GET /v1/analytics/usage?range=12m
    public function usage(Request $request): JsonResponse
    {
        $range = $request->input('range', '12m');
        $user  = JWTAuth::parseToken()->authenticate();

        return $this->success($this->service->getUsageSeries($user->id, $range));
    }
}
