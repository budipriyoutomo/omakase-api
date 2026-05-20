<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\ArrayableData;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class AiGenerationLifecycleDTO implements ArrayableData
{
    public function __construct(
        public readonly CampaignPayloadDTO $input,
        public readonly CampaignIntelligenceDTO $campaignIntelligence,
        public readonly VisualIntelligenceDTO $visualIntelligence,
        public readonly PromptRenderResultDTO $promptRender,
        public readonly array $analytics = [],
    ) {}

    public function toArray(): array
    {
        return [
            'architecture_version' => 'visual-orchestration-v1',
            'lifecycle' => [
                'input_received',
                'campaign_intelligence_built',
                'visual_intelligence_built',
                'commercial_prompt_rendered',
                'image_generation_requested',
                'storage_pending',
                'analytics_pending',
            ],
            'input' => [
                'campaign_type' => $this->input->campaignType,
                'cuisine' => $this->input->cuisine,
                'platform' => $this->input->platform,
                'audience' => $this->input->audience,
                'goal' => $this->input->goal,
                'mood' => $this->input->mood,
                'style' => $this->input->style,
                'hero_item' => $this->input->heroItem,
                'visual_strategy' => $this->input->visualStrategy,
                'cta_strategy' => $this->input->ctaStrategy,
                'aspect_ratio' => $this->input->aspectRatio,
            ],
            'campaign_intelligence' => $this->campaignIntelligence->toArray(),
            'visual_intelligence' => $this->visualIntelligence->toArray(),
            'prompt_render' => $this->promptRender->toArray(),
            'analytics' => $this->analytics,
        ];
    }
}
