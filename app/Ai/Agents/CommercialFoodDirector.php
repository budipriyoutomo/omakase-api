<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\FoodEnrichmentDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class CommercialFoodDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        /** @var FoodEnrichmentDTO|null $foodEnrichment */
        $foodEnrichment = $visual->metadata['food_enrichment'] ?? null;

        $overrides = [
            'photographyStyle' => 'high-end commercial food advertising photography',
            'lighting' => $campaign->marketingEnergy === 'warm_inviting'
                ? 'warm directional softbox with appetizing table shadows'
                : $visual->lighting,
            'visualHierarchy' => 'dominant hero dish with clean brand and offer support',
            'commercialDirectives' => array_merge($visual->commercialDirectives, [
                'compose like a restaurant campaign key visual, not a generic menu photo',
                'make the dish texture tactile and appetite-led before any background detail',
                'use realistic plates, surfaces, garnish, and human-scale serving context',
            ]),
            'metadata' => [
                'director_role' => 'commercial_food_photographer',
            ],
        ];

        // ── Food Enrichment: apply KB/Gemini knowledge as foundation ──
        if ($foodEnrichment instanceof FoodEnrichmentDTO && $foodEnrichment->enriched) {
            $foodContext  = $foodEnrichment->toVisualContext();
            $avoidContext = $foodEnrichment->toNegativeContext();

            // Merge food photography intelligence into commercial directives
            $foodDirectives = [];
            if ($foodContext !== '') {
                $foodDirectives[] = "Food photography intelligence (KB): {$foodContext}";
            }
            if ($avoidContext !== '') {
                $foodDirectives[] = "Avoid elements: {$avoidContext}";
            }
            if ($foodEnrichment->cameraAngle !== '') {
                $overrides['cameraAngle'] = $foodEnrichment->cameraAngle
                    . ' | ' . ($visual->cameraAngle !== '' ? $visual->cameraAngle : 'commercial food angle');
            }

            // Merge KB color palette into visual direction
            if (! empty($foodEnrichment->colorPalette)) {
                $foodDirectives[] = 'Suggested palette: ' . implode(', ', $foodEnrichment->colorPalette);
            }

            // KB lighting preset takes priority as base, director can refine
            if ($foodEnrichment->lightingPreset !== '') {
                $overrides['lighting'] = $foodEnrichment->lightingPreset
                    . ($overrides['lighting'] !== '' ? '. ' . $overrides['lighting'] : '');
            }

            if ($foodEnrichment->platingStyle !== '') {
                $foodDirectives[] = "Plating style: {$foodEnrichment->platingStyle}";
            }

            $overrides['commercialDirectives'] = array_merge(
                $foodDirectives,
                $overrides['commercialDirectives'],
            );
        }

        return $visual->withOverrides($overrides);
    }
}
