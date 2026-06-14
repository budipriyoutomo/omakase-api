<?php

declare(strict_types=1);

namespace App\Modules\KnowledgeBase\Controllers;

use App\Models\CampaignContext;
use App\Services\AI\FoodKnowledgeService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CampaignContextController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly FoodKnowledgeService $kbService
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(CampaignContext::all());
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $context = CampaignContext::findOrFail($id);

        $validated = $request->validate([
            'key' => 'sometimes|string|unique:campaign_contexts,key,' . $id,
            'name' => 'sometimes|string',
            'aliases' => 'sometimes|array',
            'mood_keywords' => 'sometimes|array',
            'urgency_phrases' => 'sometimes|array',
            'color_emotion' => 'sometimes|array',
            'composition_hint' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $context->update($validated);
        $this->kbService->clearCache();

        return $this->success($context);
    }
}