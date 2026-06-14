<?php

declare(strict_types=1);

namespace App\Modules\KnowledgeBase\Controllers;

use App\Models\FoodCategory;
use App\Services\AI\FoodKnowledgeService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FoodCategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly FoodKnowledgeService $kbService
    ) {}

    public function index(): JsonResponse
    {
        $categories = FoodCategory::orderBy('sort_order')->get();

        return $this->success($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:food_categories,key',
            'name' => 'required|string',
            'aliases' => 'required|array',
            'visual_keywords' => 'required|array',
            'texture_descriptors' => 'required|array',
            'lighting_preset' => 'nullable|string',
            'camera_angle' => 'nullable|string',
            'plating_style' => 'nullable|string',
            'color_palette' => 'required|array',
            'avoid' => 'required|array',
            'cuisine_affinity' => 'required|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = FoodCategory::create($validated);
        $this->kbService->clearCache();

        return $this->created($category);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);

        $validated = $request->validate([
            'key' => 'sometimes|string|unique:food_categories,key,' . $id,
            'name' => 'sometimes|string',
            'aliases' => 'sometimes|array',
            'visual_keywords' => 'sometimes|array',
            'texture_descriptors' => 'sometimes|array',
            'lighting_preset' => 'nullable|string',
            'camera_angle' => 'nullable|string',
            'plating_style' => 'nullable|string',
            'color_palette' => 'sometimes|array',
            'avoid' => 'sometimes|array',
            'cuisine_affinity' => 'sometimes|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);
        $this->kbService->clearCache();

        return $this->success($category);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);
        $category->delete();
        $this->kbService->clearCache();

        return $this->success(null, 'Food category deleted.');
    }
}