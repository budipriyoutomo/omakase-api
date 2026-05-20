<?php

declare(strict_types=1);

namespace App\Ai\Contracts;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

interface VisualDirector
{
    public function refine(
        VisualIntelligenceDTO $visual,
        CampaignIntelligenceDTO $campaign,
        CampaignPayloadDTO $payload
    ): VisualIntelligenceDTO;
}
