<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\CampaignContext;
use App\Models\CuisineStyle;
use App\Models\FoodCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FoodKnowledgeService
{
    private const CACHE_TTL_HOURS = 6;

    private function getCategories(): array
    {
        return Cache::remember(
            'kb:food_categories',
            now()->addHours(self::CACHE_TTL_HOURS),
            fn () => FoodCategory::active()
                ->orderBy('sort_order')
                ->get()
                ->toArray()
        );
    }

    private function getCuisineStyles(): array
    {
        return Cache::remember(
            'kb:cuisine_styles',
            now()->addHours(self::CACHE_TTL_HOURS),
            fn () => CuisineStyle::active()->get()->toArray()
        );
    }

    private function getCampaignContexts(): array
    {
        return Cache::remember(
            'kb:campaign_contexts',
            now()->addHours(self::CACHE_TTL_HOURS),
            fn () => CampaignContext::active()->get()->toArray()
        );
    }

    public function matchFoodCategory(string $heroItem): ?array
    {
        $normalized = Str::lower(trim($heroItem));

        foreach ($this->getCategories() as $category) {
            if (Str::contains($normalized, $category['key'])) {
                return $category;
            }
            foreach ($category['aliases'] as $alias) {
                if (Str::contains($normalized, Str::lower($alias))) {
                    return $category;
                }
            }
        }

        return null;
    }

    public function matchCuisineStyle(string $cuisineType): ?array
    {
        $normalized = Str::lower(trim($cuisineType));

        foreach ($this->getCuisineStyles() as $cuisine) {
            if (Str::contains($normalized, $cuisine['key']) ||
                Str::contains($cuisine['key'], $normalized)) {
                return $cuisine;
            }
        }

        return null;
    }

    public function matchCampaignContext(string $campaignType): ?array
    {
        $normalized = Str::lower(trim($campaignType));

        foreach ($this->getCampaignContexts() as $context) {
            if (Str::contains($normalized, $context['key'])) {
                return $context;
            }
            foreach ($context['aliases'] as $alias) {
                if (Str::contains($normalized, Str::lower($alias))) {
                    return $context;
                }
            }
        }

        return null;
    }

    public function buildEnrichmentData(
        string $heroItem,
        string $cuisineType = '',
        string $campaignType = '',
        string $mood = '',
    ): array {
        $enrichment = [
            'found_in_kb' => false,
            'food_category' => null,
            'visual_keywords' => [],
            'texture_descriptors' => [],
            'lighting_preset' => '',
            'camera_angle' => '',
            'plating_style' => '',
            'color_palette' => [],
            'avoid' => [],
            'cuisine_props' => [],
            'campaign_mood' => [],
            'urgency_phrases' => [],
        ];

        $food = $this->matchFoodCategory($heroItem);
        if ($food) {
            $enrichment['found_in_kb'] = true;
            $enrichment['food_category'] = $food['key'];
            $enrichment['visual_keywords'] = $food['visual_keywords'];
            $enrichment['texture_descriptors'] = $food['texture_descriptors'];
            $enrichment['lighting_preset'] = $food['lighting_preset'];
            $enrichment['camera_angle'] = $food['camera_angle'];
            $enrichment['plating_style'] = $food['plating_style'];
            $enrichment['color_palette'] = $food['color_palette'];
            $enrichment['avoid'] = $food['avoid'];
        }

        if ($cuisineType) {
            $cuisine = $this->matchCuisineStyle($cuisineType);
            if ($cuisine) {
                $enrichment['plating_style'] .=
                    ' ' . implode(', ', $cuisine['plating_keywords']);
                $enrichment['cuisine_props'] = $cuisine['props'];
                $enrichment['color_palette'] = array_unique(array_merge(
                    $enrichment['color_palette'],
                    $cuisine['color_mood']
                ));
            }
        }

        if ($campaignType) {
            $context = $this->matchCampaignContext($campaignType);
            if ($context) {
                $enrichment['campaign_mood'] = $context['mood_keywords'];
                $enrichment['urgency_phrases'] = $context['urgency_phrases'];
                $enrichment['color_palette'] = array_unique(array_merge(
                    $enrichment['color_palette'],
                    $context['color_emotion']
                ));
            }
        }

        return $enrichment;
    }

    public function clearCache(): void
    {
        Cache::forget('kb:food_categories');
        Cache::forget('kb:cuisine_styles');
        Cache::forget('kb:campaign_contexts');
    }
}