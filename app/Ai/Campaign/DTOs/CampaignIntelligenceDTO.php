<?php

declare(strict_types=1);

namespace App\Ai\Campaign\DTOs;

use App\Ai\Contracts\ArrayableData;

final class CampaignIntelligenceDTO implements ArrayableData
{
    public function __construct(
        public readonly string $campaignGoal,
        public readonly string $conversionPriority,
        public readonly string $brandPositioning,
        public readonly string $audienceEnergy,
        public readonly string $ctaStrength,
        public readonly string $platformBehavior,
        public readonly string $marketingEnergy,
        public readonly string $campaignTone,
        public readonly array $psychologySignals = [],
        public readonly array $metadata = [],
    ) {}

    public function toArray(): array
    {
        return [
            'campaign_goal' => $this->campaignGoal,
            'conversion_priority' => $this->conversionPriority,
            'brand_positioning' => $this->brandPositioning,
            'audience_energy' => $this->audienceEnergy,
            'cta_strength' => $this->ctaStrength,
            'platform_behavior' => $this->platformBehavior,
            'marketing_energy' => $this->marketingEnergy,
            'campaign_tone' => $this->campaignTone,
            'psychology_signals' => $this->psychologySignals,
            'metadata' => $this->metadata,
        ];
    }
}
