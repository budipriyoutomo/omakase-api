<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\FoodEnrichmentDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class OmakaseVisionDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        /** @var FoodEnrichmentDTO|null $foodEnrichment */
        $foodEnrichment = $visual->metadata['food_enrichment'] ?? null;

        $overrides = [
            'heroFocus' => 'precise-omakase-food-first',
            'cameraAngle' => 'low intimate counter angle or disciplined top-down sushi arrangement',
            'lighting' => 'soft directional omakase counter light with gentle specular highlights',
            'foodTexture' => 'fresh fish sheen, rice grain detail, nori texture, precise garnish realism',
            'photographyStyle' => 'premium japanese omakase commercial photography',
            'layoutStrategy' => 'minimal japanese negative space with hero sushi rhythm',
            'visualHierarchy' => '75_percent_sushi_precision_15_percent_counter_mood_10_percent_brand',
            'commercialDirectives' => array_merge($visual->commercialDirectives, [
                'respect japanese plating discipline and clean ingredient geometry',
                'make fish texture fresh, glossy, and believable with no artificial colors',
                'use dark stone, wood counter, ceramic, or chef-counter cues only when they support the hero dish',
            ]),
            'metadata' => [
                'director_role' => 'omakase_visual_director',
            ],
        ];

        if ($foodEnrichment instanceof FoodEnrichmentDTO && $foodEnrichment->enriched) {
            $foodContext  = $foodEnrichment->toVisualContext();
            $avoidContext = $foodEnrichment->toNegativeContext();
            $foodDirectives = [];

            if ($foodContext !== '') {
                $foodDirectives[] = "Food photography intelligence (KB): {$foodContext}";
            }
            if ($avoidContext !== '') {
                $foodDirectives[] = "Avoid elements: {$avoidContext}";
            }
            if ($foodEnrichment->cameraAngle !== '') {
                $overrides['cameraAngle'] = $foodEnrichment->cameraAngle
                    . ' | ' . ($visual->cameraAngle !== '' ? $visual->cameraAngle : 'omakase counter angle');
            }
            if (! empty($foodEnrichment->colorPalette)) {
                $foodDirectives[] = 'Suggested palette: ' . implode(', ', $foodEnrichment->colorPalette);
            }
            if ($foodEnrichment->lightingPreset !== '') {
                $overrides['lighting'] = $foodEnrichment->lightingPreset
                    . ($overrides['lighting'] !== '' ? '. ' . $overrides['lighting'] : '');
            }
            if ($foodEnrichment->platingStyle !== '') {
                $foodDirectives[] = "Plating style: {$foodEnrichment->platingStyle}";
            }
            // Enhance foodTexture with KB texture descriptors
            if (! empty($foodEnrichment->textureDescriptors)) {
                $overrides['foodTexture'] = implode(', ', $foodEnrichment->textureDescriptors)
                    . ' | ' . $overrides['foodTexture'];
            }

            $overrides['commercialDirectives'] = array_merge(
                $foodDirectives,
                $overrides['commercialDirectives'],
            );
        }

        return $visual->withOverrides($overrides);
    }
}
