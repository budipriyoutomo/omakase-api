<?php

declare(strict_types=1);

namespace App\Ai\Orchestrators;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Campaign\Intelligence\CampaignSignalClassifier;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\MarketingIntelligencePayloadDTO;

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

    /**
     * Build campaign intelligence dari MarketingIntelligencePayloadDTO.
     * Hanya memproses marketing fields — digunakan terpisah dari image generation.
     */
    public function buildFromMarketing(MarketingIntelligencePayloadDTO $marketing): CampaignIntelligenceDTO
    {
        // Mapping ke CampaignPayloadDTO agar CampaignSignalClassifier tetap bisa digunakan
        $payload = new CampaignPayloadDTO(
            campaignType: $marketing->campaignType,
            platform: $marketing->platform,
            audience: $marketing->audience,
            goal: $marketing->goal,
            visualStrategy: $marketing->visualStrategy,
            ctaStrategy: $marketing->ctaStrategy,
            style: '',
            mood: '',
            heroItem: '',
            cuisine: '',
            aspectRatio: '',
        );

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
                'layer' => 'marketing_intelligence_only',
                'separated_from_image_generation' => true,
                'source_fields' => [
                    'campaign_type',
                    'goal',
                    'audience',
                    'platform',
                    'visual_strategy',
                    'cta_strategy',
                ],
            ],
        );
    }
}
