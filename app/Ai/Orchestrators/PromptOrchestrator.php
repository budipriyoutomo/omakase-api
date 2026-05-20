<?php

declare(strict_types=1);

namespace App\Ai\Orchestrators;

use App\Ai\DTOs\AiGenerationLifecycleDTO;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Renderers\CommercialPromptRenderer;

class PromptOrchestrator
{
    public function __construct(
        private readonly CampaignIntelligenceOrchestrator $campaignIntelligence,
        private readonly VisualCompositionOrchestrator $visualComposition,
        private readonly CommercialPromptRenderer $renderer,
    ) {}

    public function build(CampaignPayloadDTO $payload): array
    {
        return $this->orchestrate($payload)->toArray();
    }

    public function orchestrate(CampaignPayloadDTO $payload): AiGenerationLifecycleDTO
    {
        $campaign = $this->campaignIntelligence->build($payload);
        $visual = $this->visualComposition->build($payload, $campaign);
        $promptRender = $this->renderer->render($payload, $campaign, $visual);

        return new AiGenerationLifecycleDTO(
            input: $payload,
            campaignIntelligence: $campaign,
            visualIntelligence: $visual,
            promptRender: $promptRender,
            analytics: [
                'ready_for_visual_performance_tracking' => true,
                'dimensions' => [
                    'campaign_goal' => $campaign->campaignGoal,
                    'brand_positioning' => $campaign->brandPositioning,
                    'platform_behavior' => $campaign->platformBehavior,
                    'composition_type' => $visual->compositionType,
                    'visual_format' => $visual->campaignVisualFormat,
                    'photography_style' => $visual->photographyStyle,
                    'director' => $visual->director,
                    'renderer_version' => $promptRender->version,
                ],
                'future_optimization_hooks' => [
                    'variant_group_id' => null,
                    'upscale_job_id' => null,
                    'prompt_version_id' => $promptRender->version,
                    'performance_score' => null,
                    'learning_feedback_status' => 'pending',
                ],
            ],
        );
    }
}
