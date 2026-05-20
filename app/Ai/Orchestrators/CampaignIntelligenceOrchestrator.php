<?php

declare(strict_types=1);

namespace App\Ai\Orchestrators;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Campaign\Intelligence\CampaignSignalClassifier;
use App\Ai\DTOs\CampaignPayloadDTO;

final class CampaignIntelligenceOrchestrator
{
    public function __construct(
        private readonly CampaignSignalClassifier $classifier,
    ) {}

    public function build(CampaignPayloadDTO $payload): CampaignIntelligenceDTO
    {
        return new CampaignIntelligenceDTO(
            campaignGoal: $this->classifier->campaignGoal($payload),
            conversionPriority: $this->classifier->conversionPriority($payload),
            brandPositioning: $this->classifier->brandPositioning($payload),
            audienceEnergy: $this->classifier->audienceEnergy($payload),
            ctaStrength: $this->classifier->ctaStrength($payload),
            platformBehavior: $this->classifier->platformBehavior($payload),
            marketingEnergy: $this->classifier->marketingEnergy($payload),
            campaignTone: $this->classifier->campaignTone($payload),
            psychologySignals: $this->classifier->psychologySignals($payload),
            metadata: [
                'layer' => 'campaign_intelligence',
                'prompt_generation_allowed' => false,
                'source_fields' => [
                    'campaign_type',
                    'goal',
                    'audience',
                    'platform',
                    'mood',
                    'cta_strategy',
                ],
            ],
        );
    }
}
