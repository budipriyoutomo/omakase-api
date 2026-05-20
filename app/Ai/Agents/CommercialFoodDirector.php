<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class CommercialFoodDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        return $visual->withOverrides([
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
        ]);
    }
}
