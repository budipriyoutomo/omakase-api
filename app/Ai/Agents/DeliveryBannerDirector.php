<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class DeliveryBannerDirector implements VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO {
        return $visual->withOverrides([
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
        ]);
    }
}
