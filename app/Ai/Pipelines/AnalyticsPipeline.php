<?php

declare(strict_types=1);

namespace App\Ai\Pipelines;

use App\Models\Generation;

final class AnalyticsPipeline
{
    public function metadataForQueuedImage(
        Generation $generation,
        array $promptResult,
        array $imageResult
    ): array {
        return [
            'architecture_version' => 'visual-orchestration-v1',
            'generation_id' => $generation->id,
            'prompt_renderer' => data_get($promptResult, 'orchestration.prompt_render.renderer'),
            'prompt_renderer_version' => data_get($promptResult, 'orchestration.prompt_render.version'),
            'visual_director' => data_get($promptResult, 'agent'),
            'campaign_goal' => data_get($promptResult, 'orchestration.campaign_intelligence.campaign_goal'),
            'platform_behavior' => data_get($promptResult, 'orchestration.campaign_intelligence.platform_behavior'),
            'composition_type' => data_get($promptResult, 'orchestration.visual_intelligence.composition_type'),
            'visual_format' => data_get($promptResult, 'orchestration.visual_intelligence.campaign_visual_format'),
            'photography_style' => data_get($promptResult, 'orchestration.visual_intelligence.photography_style'),
            'provider_prediction_id' => $imageResult['id'] ?? null,
            'variant_index' => 0,
            'variant_group_id' => (string) $generation->id,
            'upscale_status' => 'not_requested',
            'optimization_loop_status' => 'pending_performance_data',
        ];
    }
}
