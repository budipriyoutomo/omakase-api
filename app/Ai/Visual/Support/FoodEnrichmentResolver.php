<?php

declare(strict_types=1);

namespace App\Ai\Visual\Support;

use App\Ai\Agents\PromptEnrichmentAgent;
use App\Ai\DTOs\FoodEnrichmentDTO;
use App\Services\AI\FoodKnowledgeService;
use Illuminate\Support\Facades\Log;

class FoodEnrichmentResolver
{
    public function __construct(
        private readonly FoodKnowledgeService $knowledgeService,
    ) {}

    public function resolve(
        string $heroItem,
        string $cuisineType   = '',
        string $campaignType  = '',
        string $visualStyle   = '',
        string $mood          = '',
    ): FoodEnrichmentDTO {

        // Guard: if no hero item, return empty enrichment
        if (empty(trim($heroItem))) {
            return FoodEnrichmentDTO::empty();
        }

        // Step 1: Try Knowledge Base first (instant, zero latency)
        $kbData = $this->knowledgeService->buildEnrichmentData(
            heroItem:     $heroItem,
            cuisineType:  $cuisineType,
            campaignType: $campaignType,
            mood:         $mood,
        );

        if ($kbData['found_in_kb']) {
            Log::info('FoodEnrichment: KB hit', [
                'food_category' => $kbData['food_category'],
                'hero_item'     => $heroItem,
            ]);

            return FoodEnrichmentDTO::fromKnowledgeBase($kbData);
        }

        // Step 2: Fallback to Gemini
        Log::info('FoodEnrichment: KB miss → Gemini fallback', [
            'hero_item' => $heroItem,
        ]);

        return $this->resolveWithGemini(
            heroItem:     $heroItem,
            cuisineType:  $cuisineType,
            campaignType: $campaignType,
            visualStyle:  $visualStyle,
            mood:         $mood,
        );
    }

    private function resolveWithGemini(
        string $heroItem,
        string $cuisineType,
        string $campaignType,
        string $visualStyle,
        string $mood,
    ): FoodEnrichmentDTO {

        try {
            $agent = new PromptEnrichmentAgent(
                heroItem:     $heroItem,
                cuisineType:  $cuisineType,
                campaignType: $campaignType,
                visualStyle:  $visualStyle,
                mood:         $mood,
            );

            $response = $agent->prompt('Provide food photography enrichment data.');

            // Strip markdown fences if present
            $text = trim($response);
            $text = preg_replace('/^```json\s*/i', '', $text);
            $text = preg_replace('/\s*```$/m', '', $text);

            $data = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE
                || empty($data['visual_keywords'])) {
                throw new \RuntimeException(
                    'Invalid JSON from Gemini enrichment: ' . $text
                );
            }

            Log::info('FoodEnrichment: Gemini success', [
                'hero_item'     => $heroItem,
                'food_category' => $data['food_category'] ?? null,
            ]);

            return new FoodEnrichmentDTO(
                enriched:           true,
                source:             'gemini',
                foodCategory:       $data['food_category']     ?? null,
                visualKeywords:     $data['visual_keywords']   ?? [],
                textureDescriptors: $data['texture_descriptors'] ?? [],
                lightingPreset:     $data['lighting_preset']   ?? '',
                cameraAngle:        $data['camera_angle']      ?? '',
                platingStyle:       $data['plating_style']     ?? '',
                colorPalette:       $data['color_palette']     ?? [],
                avoidElements:      $data['avoid_elements']    ?? [],
                cuisineProps:       $data['cuisine_props']     ?? [],
                campaignMood:       $data['campaign_mood']     ?? [],
            );

        } catch (\Throwable $e) {
            Log::warning('FoodEnrichment: Gemini failed, passthrough', [
                'hero_item' => $heroItem,
                'error'     => $e->getMessage(),
            ]);

            return FoodEnrichmentDTO::empty();
        }
    }
}