<?php

declare(strict_types=1);

namespace App\Ai\Routers;

use App\Ai\Agents\CommercialFoodDirector;
use App\Ai\Agents\DeliveryBannerDirector;
use App\Ai\Agents\LuxuryHospitalityDirector;
use App\Ai\Agents\OmakaseVisionDirector;
use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class VisualDirectionRouter
{
    public function resolve(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): string {
        $routingText = strtolower(implode(' ', [
            $payload->cuisine,
            $payload->heroItem,
            $visual->compositionType,
            $visual->campaignVisualFormat,
            $visual->realismLevel,
            $visual->photographyStyle,
            $campaign->platformBehavior,
        ]));

        if (str_contains($routingText, 'omakase') || str_contains($routingText, 'sushi')) {
            return OmakaseVisionDirector::class;
        }

        if ($visual->campaignVisualFormat === 'horizontal-delivery-banner') {
            return DeliveryBannerDirector::class;
        }

        if ($campaign->brandPositioning === 'luxury' || str_contains($visual->photographyStyle, 'luxury')) {
            return LuxuryHospitalityDirector::class;
        }

        return CommercialFoodDirector::class;
    }
}
