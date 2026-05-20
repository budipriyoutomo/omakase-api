<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class LuxuryHospitalityDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        return $visual->withOverrides([
            'heroFocus' => 'premium-food-first-with-hospitality-atmosphere',
            'lighting' => 'soft cinematic side light, polished highlights, controlled deep shadows',
            'negativeSpace' => 'top-right-minimal-premium-space',
            'typographySafeLayout' => 'quiet refined text-safe space with premium restraint',
            'ctaSafeSpacing' => 'subtle luxury CTA room, no loud discount layout',
            'photographyStyle' => 'luxury hospitality editorial food photography',
            'visualHierarchy' => '70_percent_hero_dish_20_percent_atmosphere_10_percent_brand',
            'commercialDirectives' => array_merge($visual->commercialDirectives, [
                'use premium plating, restrained props, and expensive-feeling material surfaces',
                'make the restaurant feel aspirational without losing food dominance',
                'avoid crowded promotional energy and obvious discount language',
            ]),
            'metadata' => [
                'director_role' => 'luxury_hospitality_creative_director',
            ],
        ]);
    }
}
