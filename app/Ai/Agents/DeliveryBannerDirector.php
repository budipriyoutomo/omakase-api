<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\FoodEnrichmentDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class DeliveryBannerDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        /** @var FoodEnrichmentDTO|null $foodEnrichment */
        $foodEnrichment = $visual->metadata['food_enrichment'] ?? null;

        $overrides = [
            'cameraAngle' => 'top-down or shallow 45-degree commerce angle',
            'negativeSpace' => 'right-third-offer-zone',
            'typographySafeLayout' => 'flat clean promo area with low texture and clear price/CTA room',
            'ctaSafeSpacing' => 'large marketplace CTA area separated from food edges',
            'layoutStrategy' => 'delivery app banner scan pattern',
            'visualHierarchy' => '60_percent_food_30_percent_offer_zone_10_percent_brand',
            'commercialDirectives' => array_merge($visual->commercialDirectives, [
                'optimize for small delivery-app thumbnails and fast price comparison',
                'keep the offer zone blank enough for later typography overlay',
                'show abundance without blocking product recognition',
            ]),
            'metadata' => [
                'director_role' => 'delivery_marketplace_art_director',
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
                    . ' | ' . ($visual->cameraAngle !== '' ? $visual->cameraAngle : 'delivery banner angle');
            }
            if (! empty($foodEnrichment->colorPalette)) {
                $foodDirectives[] = 'Suggested palette: ' . implode(', ', $foodEnrichment->colorPalette);
            }
            if ($foodEnrichment->lightingPreset !== '') {
                $overrides['lighting'] = $foodEnrichment->lightingPreset
                    . ($overrides['lighting'] ?? $visual->lighting !== '' ? '. ' . $visual->lighting : '');
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
