<?php

declare(strict_types=1);

namespace App\Modules\KnowledgeBase\Controllers;

use App\Models\CuisineStyle;
use App\Services\AI\FoodKnowledgeService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CuisineStyleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly FoodKnowledgeService $kbService
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(CuisineStyle::all());
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $style = CuisineStyle::findOrFail($id);

        $validated = $request->validate([
            'key' => 'sometimes|string|unique:cuisine_styles,key,' . $id,
            'name' => 'sometimes|string',
            'plating_keywords' => 'sometimes|array',
            'props' => 'sometimes|array',
            'color_mood' => 'sometimes|array',
            'lighting' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $style->update($validated);
        $this->kbService->clearCache();

        return $this->success($style);
    }
}