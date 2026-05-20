<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Controllers;

use App\Modules\Analytics\Services\AnalyticsService;
use App\Modules\Generation\Services\GeminiAiService;
use App\Modules\Template\Resources\TemplateResource;
use App\Shared\Traits\ApiResponseTrait;
use App\Models\Generation;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly GeminiAiService $aiService,
    ) {}

    // GET /v1/dashboard/stats
    public function stats(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $totalGenerations = Generation::where('user_id', $user->id)->count();
        $completed        = Generation::where('user_id', $user->id)->where('status', 'completed')->count();
        $thisMonth        = Generation::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $pending = Generation::where('user_id', $user->id)->where('status', 'pending')->count();

        return $this->success([
            ['label' => 'Total Generations', 'value' => (string) $totalGenerations],
            ['label' => 'Completed',          'value' => (string) $completed],
            ['label' => 'This Month',         'value' => (string) $thisMonth],
            ['label' => 'In Progress',        'value' => (string) $pending],
        ]);
    }

    // GET /v1/dashboard/usage
    public function usage(): JsonResponse
    {
        $user   = JWTAuth::parseToken()->authenticate();
        $series = $this->analyticsService->getUsageSeries($user->id, '12m');

        return $this->success($series);
    }

    // GET /v1/dashboard/suggestions
    public function suggestions(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            $suggestions = $this->aiService->generateSuggestions(
                context: "restaurant marketing campaigns for social media",
                count: 5,
            );

            return $this->success(['suggestions' => $suggestions]);
        } catch (\Throwable) {
            return $this->success(['suggestions' => [
                'Try a seasonal promotion campaign',
                'Feature your chef specials this week',
                'Share behind-the-scenes kitchen content',
                'Run a loyalty points announcement',
                'Promote a limited-time discount offer',
            ]]);
        }
    }

    // GET /v1/dashboard/templates/trending
    public function trendingTemplates(): JsonResponse
    {
        $templates = Template::trending()->limit(6)->get();

        return $this->success(TemplateResource::collection($templates));
    }
}
