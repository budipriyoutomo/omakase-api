<?php

declare(strict_types=1);

namespace App\Modules\Generation\Resources;

use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Generation
 */
class GenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'campaignType' => $this->campaign_type,
            'cuisine' => $this->cuisine,
            'platform' => $this->platform,
            'audience' => $this->audience,
            'goal' => $this->goal,
            'mood' => $this->mood,
            'style' => $this->style,
            'heroItem' => $this->hero_item,
            'visualStrategy' => $this->visual_strategy,
            'ctaStrategy' => $this->cta_strategy,
            'aspectRatio' => $this->aspect_ratio,
            'prompt' => $this->prompt,
            'enhancedPrompt' => $this->enhanced_prompt,
            'negativePrompt' => $this->negative_prompt,
            'orchestration' => $this->orchestration ?? [],
            'agent' => $this->agent,
            'provider' => $this->provider,
            'model' => $this->model,
            'result' => $this->result,
            'imageUrl' => $this->image_url,
            'imageUrls' => $this->image_urls ?? [],
            'previewUrls' => $this->preview_urls ?? [],
            'metadata' => $this->metadata ?? [],
            'aiMetadata' => $this->ai_metadata ?? [],

            // ── Phase 3 & 4 ─────────────────────────────────────────
            'typographyBlueprint' => data_get($this->ai_metadata, 'typography_blueprint'),
            'creativeBlueprint'   => data_get($this->ai_metadata, 'creative_blueprint'),
            'hasCreativeHtml'     => ! empty(data_get($this->ai_metadata, 'creative_html')),

            // ── Marketing Intelligence (diproses terpisah dari image generation) ──
            'marketingIntelligence' => data_get($this->ai_metadata, 'marketing_intelligence'),

            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
